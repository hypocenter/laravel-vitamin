<?php

namespace Hypocenter\LaravelVitamin\Repository\Eloquent;


use Hypocenter\LaravelVitamin\Container\Contracts\BootableInterface;
use Hypocenter\LaravelVitamin\Repository\Contracts\Criteria;
use Hypocenter\LaravelVitamin\Repository\Contracts\Repository;
use Hypocenter\LaravelVitamin\Validator\AbstractValidator;
use Hypocenter\LaravelVitamin\Validator\Contracts\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

abstract class EloquentRepository implements Repository, BootableInterface
{
    protected $modelClass;
    /**
     * @var Model
     */
    protected $model;

    protected $fillable         = null;
    protected $fillableOnUpdate = null;

    protected $validatorClass;
    /**
     * @var AbstractValidator
     */
    protected $validator;

    /**
     * @var \ReflectionProperty
     */
    private $rpFillable;

    /**
     * @var BuilderContext
     */
    protected $context;

    public function boot()
    {
        $this->model      = new $this->modelClass;
        $this->rpFillable = new \ReflectionProperty($this->modelClass, 'fillable');
        $this->rpFillable->setAccessible(true);
        $this->validator = $this->validatorClass ? resolve($this->validatorClass) : null;
    }

    /**
     * @param      $model
     * @param null $scene
     *
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($model, $scene = null)
    {
        return $this->validator->validate($model, $scene);
    }

    protected function context()
    {
        return $this->context = $this->context ?: new BuilderContext();
    }

    protected function query()
    {
        $query         = new Query($this->model->newQuery(), $this->context());
        $this->context = null;

        return $query;
    }

    public function new()
    {
        return $this->model->newInstance();
    }

    public function find($id, $columns = ['*'])
    {
        return $this->query()->find($id, $columns);
    }

    public function findOrFail($id)
    {
        return $this->query()->findOrFail($id);
    }

    public function wrap($idOrModel)
    {
        if ($idOrModel instanceof Model) {
            return $idOrModel;
        }

        return $this->find($idOrModel);
    }

    public function wrapOrFail($idOrModel)
    {
        if ($idOrModel instanceof Model) {
            return $idOrModel;
        }

        return $this->findOrFail($idOrModel);
    }

    public function all()
    {
        return $this->query()->all();
    }

    public function exists(array $where): bool
    {
        return $this->query()->where($where)->exists();
    }

    /**
     * @param $data
     *
     * @return Model
     * @throws \Illuminate\Validation\ValidationException
     */
    final public function create($data)
    {
        $model = $this->new();
        $this->save($model, $data);
        return $model;
    }

    /**
     * @param $id
     * @param $data
     *
     * @return bool|Model
     * @throws \Illuminate\Validation\ValidationException
     */
    final public function update($id, $data)
    {
        $model = $this->query()->findOrFail($id);
        return $this->save($model, $data);
    }

    final public function fill($model, $data)
    {
        assert($model instanceof Model);

        $fillable = null;

        if ($model->exists) {
            $fillable = $this->fillableOnUpdate ?: $this->fillable ?: [];
        } else if ($this->fillable) {
            $fillable = $this->fillable;
        }

        $this->rpFillable->setValue($model, $fillable);

        $model->fill($fillable ? array_only($data, $fillable) : $data);

        return $model;
    }

    /**
     * @param      $model
     * @param null $data
     *
     * @return bool|Model
     * @throws \Illuminate\Validation\ValidationException
     */
    final public function save($model, $data = null)
    {
        assert($model instanceof Model);

        if (!is_null($data)) {
            $this->fill($model, $data);
        }

        if ($this->validator) {
            $this->validator->validate($model, Validator::SCENE_SAVE);
        }

        return $model->save() ? $model : false;
    }

    /**
     * @param $id
     *
     * @return bool|null|Model
     * @throws \Exception
     */
    final public function delete($id)
    {
        $model = $id instanceof Model ? $id : $this->query()->findOrFail($id);
        $res = $model->delete();
        return $res ? $model : $res;
    }

    public function lockForUpdate()
    {
        $this->context()->setLockForUpdate(true);
        return $this;
    }

    public function sharedLock()
    {
        $this->context()->setSharedLock(true);
        return $this;
    }

    public function with($relation)
    {
        $this->context()->setWith($relation);
        return $this;
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->context()->setPaginate([$perPage, $columns, $pageName, $page]);
        return $this;
    }

    public function useCursor()
    {
        $this->context()->setUseCursor(true);
        return $this;
    }

    /**
     * @param Criteria|Criteria[]|callable|callable[] $criteria
     * @param bool                                    $append
     *
     * @return $this|static
     */
    public function criteria($criteria, $append = true)
    {
        if ($append) {
            $context = $this->context();
            $criteria = array_merge($context->getCriteria(), Arr::wrap($criteria));
            $this->context()->setCriteria($criteria);
        } else {
            $this->context()->setCriteria(Arr::wrap($criteria));
        }

        return $this;
    }
}
