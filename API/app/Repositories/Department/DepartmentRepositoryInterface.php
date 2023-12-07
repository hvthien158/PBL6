<?php

namespace App\Repositories\Department;

use App\Repositories\RepositoryInterface;

interface DepartmentRepositoryInterface extends RepositoryInterface
{
    public function getAllFiltered($skip, $request);

    public function checkManager($id);
}
