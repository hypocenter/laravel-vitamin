<?php

namespace Hypocenter\LaravelVitamin\Repository\Eloquent;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Query
 *
 * @mixin Builder
 *
 * @package Hypocenter\LaravelVitamin\EloquentRepository\Eloquent
 */
class Query
{
    /**
     * @var Builder
     */
    private $builder;

    private $context;

    public function __construct($builder, BuilderContext $context)
    {
        $this->builder = $builder;
        $this->context = $context;
        $this->apply();
    }

    public function reset()
    {
        $this->builder = $this->builder->getModel()->newQuery();
        $this->apply();
    }

    protected function apply()
    {
        if ($this->context->isSharedLock()) {
            $this->builder->sharedLock();
        }
        if ($this->context->isLockForUpdate()) {
            $this->builder->lockForUpdate();
        }
        if ($this->context->getWith()) {
            $this->builder->with(...(array)($this->context->getWith()));
        }
        if ($this->context->getCriteria()) {
            foreach ($this->context->getCriteria() as $criteria) {
                if (is_callable($criteria)) {
                    $criteria($this);
                } else {
                    $criteria->apply($this);
                }
            }
        }
    }

    /**
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Model[]|\Generator|mixed
     */
    public function all()
    {
        if (empty($this->builder->getQuery()->columns) && $this->hasJoin()) {
            $this->builder->select([$this->builder->qualifyColumn('*')]);
        }

        if ($this->context->getPaginate()) {
            return $this->builder->paginate(...$this->context->getPaginate());
        }

        if ($this->context->isUseCursor()) {
            return $this->builder->cursor();
        }

        return $this->builder->get();
    }

    public function __get($name)
    {
        return $this->builder->qualifyColumn($name);
    }

    public function __call($name, $arguments)
    {
        $res = ($this->builder)->$name(...$arguments);
        if ($res === $this->builder) {
            return $this;
        }

        return $res;
    }

    protected function hasJoin()
    {
        return count((array) $this->builder->getQuery()->joins) > 0;
    }
}