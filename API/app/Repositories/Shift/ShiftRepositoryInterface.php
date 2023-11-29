<?php

namespace App\Repositories\Shift;

use App\Repositories\RepositoryInterface;

interface ShiftRepositoryInterface extends RepositoryInterface
{
    public function getAllFiltered($skip, $request);
}
