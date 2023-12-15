<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Gets all records
     * @return mixed
     */
    public function selectAll();

    /**
     * Count all or list record in param
     * @param $modelParam
     * @return int
     */
    public function count($modelParam = null);

    /**
     * Select one record by id
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Execute query as multi "where" statements
     * @param array $attribute
     * @return mixed
     */
    public function getModelByMultiKeys(array $attribute = [[]]);

    /**
     * Execute query as a "select" statement
     * @param array $attribute
     * @return mixed
     */
    public function selectByMultiKeys(array $attribute = [[]]);

    /**
     * Select a limit number of result
     * @param $skip
     * @param $take
     * @param $modelParam
     * @return mixed
     */
    public function selectLimit($skip, $take, $modelParam = null);

    /**
     * Create new record
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes = []);

    /**
     * Update the given model
     * @param $modelParam
     * @param $attributes
     * @return mixed
     */
    public function update($modelParam, array $attributes = []);


    /**
     * Update model via id
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function updateByID($id, array $attributes = []);

    /**
     * Delete model via id
     * @param $id
     * @return mixed
     */
    public function delete($id);
}
