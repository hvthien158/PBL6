<?php

namespace App\Repositories\TimeKeeping;

use App\Repositories\RepositoryInterface;

interface TimeKeepingRepositoryInterface extends RepositoryInterface
{
    public function checkIn($userID);

    public function checkOut($userID);

    public function getTimeKeepingToday();

    public function getListTimeKeepingFiltered($from, $to, $role, $user_id = null);

    public function getListTimeKeepingBetween($userID, $fromMonth, $toMonth);

    public function getListTimeKeepingAround($request);

    public function getListTimeKeepingInMonth($request);

    public function customUpdate($request);

    public function timeKeepingStatistic($skip, $request);
}
