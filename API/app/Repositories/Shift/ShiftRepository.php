<?php

namespace App\Repositories\Shift;

use App\Models\Shift;
use App\Repositories\BaseRepository;

class ShiftRepository extends BaseRepository implements ShiftRepositoryInterface
{

    public function getModel()
    {
        return Shift::class;
    }
}
