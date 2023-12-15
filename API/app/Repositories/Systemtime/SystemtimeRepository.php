<?php

namespace App\Repositories\Systemtime;

use App\Models\Systemtime;
use App\Repositories\BaseRepository;

class SystemtimeRepository extends BaseRepository implements SystemtimeRepositoryInterface
{

    public function getModel()
    {
        return Systemtime::class;
    }

    public function insertGetID(array $attribute = [])
    {
        return $this->model->insertGetID($attribute);
    }
}
