<?php

namespace App\Repositories\Shift;

use App\Repositories\RepositoryInterface;

interface ShiftRepositoryInterface extends RepositoryInterface
{
    public function countAll();

    public function countByNameLike($name);

    public function findByNameLikeAndSkip($name, $skip, $take);

    public function getLimit($skip, $take);
}
