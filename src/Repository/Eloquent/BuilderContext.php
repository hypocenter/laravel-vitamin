<?php

namespace Hypocenter\LaravelVitamin\Repository\Eloquent;


use Hypocenter\LaravelVitamin\Repository\Contracts\Criteria;

class BuilderContext
{
    private $sharedLock    = false;
    private $lockForUpdate = false;
    private $with          = null;
    private $paginate      = null;
    private $useCursor     = false;

    /**
     * @var Criteria[]|callable[]
     */
    private $criteria = [];

    /**
     * @return bool
     */
    public function isSharedLock(): bool
    {
        return $this->sharedLock;
    }

    /**
     * @param bool $sharedLock
     */
    public function setSharedLock(bool $sharedLock): void
    {
        $this->sharedLock = $sharedLock;
    }

    /**
     * @return bool
     */
    public function isLockForUpdate(): bool
    {
        return $this->lockForUpdate;
    }

    /**
     * @param bool $lockForUpdate
     */
    public function setLockForUpdate(bool $lockForUpdate): void
    {
        $this->lockForUpdate = $lockForUpdate;
    }

    /**
     * @return null
     */
    public function getWith()
    {
        return $this->with;
    }

    /**
     * @param null $with
     */
    public function setWith($with): void
    {
        $this->with = $with;
    }

    /**
     * @return null
     */
    public function getPaginate()
    {
        return $this->paginate;
    }

    /**
     * @param array|null $paginate
     */
    public function setPaginate(array $paginate): void
    {
        $this->paginate = $paginate;
    }

    public function setCriteria(array $criteria)
    {
        $this->criteria = $criteria;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @return bool
     */
    public function isUseCursor(): bool
    {
        return $this->useCursor;
    }

    /**
     * @param bool $useCursor
     */
    public function setUseCursor(bool $useCursor): void
    {
        $this->useCursor = $useCursor;
    }
}