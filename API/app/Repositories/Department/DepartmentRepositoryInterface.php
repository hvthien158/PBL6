<?php

namespace App\Repositories\Department;

use App\Repositories\RepositoryInterface;

interface DepartmentRepositoryInterface extends RepositoryInterface
{
    public function getOrderAscWithUserAndManager($modelParam = null);

    public function getAllUserInDepartmentOfManager();
}
