<?php

namespace App\Repositories\TimeKeeping;

use App\Models\TimeKeeping;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;

class TimeKeepingRepository extends BaseRepository implements TimeKeepingRepositoryInterface
{

    public function getModel()
    {
        return TimeKeeping::class;
    }
}
