<?php

namespace App\Repositories\Meeting;

use App\Models\Meeting;
use App\Repositories\BaseRepository;

class MeetingRepository extends BaseRepository implements MeetingRepositoryInterface
{

    public function getModel()
    {
        return Meeting::class;
    }

    public function insertGetId($attribute = [])
    {
        return $this->model->insertGetId($attribute);
    }
}
