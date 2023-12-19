<?php

namespace App\Services\Department;

interface DepartmentServiceInterface
{
    /**
     * Get all data of department model
     * @return mixed
     */
    public function getAll();

    /**
     * Search department via multi condition
     * @param $skip
     * @param $request
     * @return array
     */
    public function search($skip, $request);

    /**
     * Get 1 department object
     * @param $user_id
     * @return mixed
     */
    public function find($user_id);

    /**
     * Get all user in 1 department
     * @param $departmentID
     * @return mixed
     */
    public function getAllUserInDepartment($departmentID);

    /**
     * Create a new department
     * @param $attribute
     * @return mixed
     */
    public function create($attribute = []);

    /**
     * Update the given department
     * @param $department
     * @param $attribute
     * @return mixed
     */
    public function update($department, $attribute = []);

    /**
     * Check where user is a manager of some department or not
     * @return mixed
     */
    public function checkManager();
}
