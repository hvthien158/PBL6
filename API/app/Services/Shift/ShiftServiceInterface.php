<?php

namespace App\Services\Shift;

interface ShiftServiceInterface
{
    /**
     * Search shift via multi condition
     * @param $skip
     * @param $request
     * @return mixed
     */
    public function search($skip, $request);

    /**
     * Create new shift
     * @param array $attribute
     * @return mixed
     */
    public function createNewShift(array $attribute = []);

    /**
     * Update the given shift
     * @param $shift
     * @param array $attribute
     * @return mixed
     */
    public function updateShift($shift, array $attribute = []);

    /**
     * Get all data of shift model
     * @return mixed
     */
    public function getAll();

    /**
     * Get 1 shift object
     * @param $id
     * @return mixed
     */
    public function findShift($id);
}
