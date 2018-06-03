<?php

namespace Hypocenter\LaravelVitamin\Repository\Contracts;


interface Repository
{
    public function validate($model, $scene = null);

    public function find($id, $columns = ['*']);

    public function findOrFail($id);

    public function create($data);

    public function update($id, $data);

    public function save($model, $data = null);

    public function exists(array $where): bool;

    public function delete($id);

    /**
     * @return static
     */
    public function lockForUpdate();

    /**
     * @return static
     */
    public function sharedLock();

    /**
     * @param $relation
     *
     * @return static
     */
    public function with($relation);

    /**
     * @param null   $perPage
     * @param array  $columns
     * @param string $pageName
     * @param null   $page
     *
     * @return static
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null);

    /**
     * @param Criteria|Criteria[]|callable|callable[] $criteria
     *
     * @return static
     */
    public function criteria($criteria);
}