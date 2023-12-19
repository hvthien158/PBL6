<?php

namespace App\Repositories\TimeKeeping;

use App\Repositories\RepositoryInterface;

interface TimeKeepingRepositoryInterface extends RepositoryInterface
{
    public function insertGetId($attribute = []);

    public function getByDateBetweenAndOrderByDesc($from, $to, $userID);

    public function getInMonthYear($month, $year, $userID);

    public function updateByDateAndUserID($date, $userID, $attribute = []);
}
