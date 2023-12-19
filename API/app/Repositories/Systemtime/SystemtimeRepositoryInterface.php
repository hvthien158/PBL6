<?php

namespace App\Repositories\Systemtime;

use App\Repositories\RepositoryInterface;

interface SystemtimeRepositoryInterface extends RepositoryInterface
{
    public function insertGetID(array $attribute = []);
}
