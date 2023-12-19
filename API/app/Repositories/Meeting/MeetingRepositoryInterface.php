<?php

namespace App\Repositories\Meeting;

use App\Repositories\RepositoryInterface;

interface MeetingRepositoryInterface extends RepositoryInterface
{
    public function insertGetId($attribute = []);
}
