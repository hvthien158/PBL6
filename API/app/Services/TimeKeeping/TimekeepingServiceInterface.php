<?php

namespace App\Services\TimeKeeping;

interface TimekeepingServiceInterface
{
    /**
     * Handle user check in request
     * @return mixed
     */
    public function checkIn();

    /**
     * Handle user check out request
     * @return mixed
     */
    public function checkOut();

    /**
     * Get today's checkin/checkout status and remote/leave status
     * @return mixed
     */
    public function getToday();

    /**
     * Search timekeeping via multi condition
     * @param $from
     * @param $to
     * @param $role
     * @param $user_id
     * @return mixed
     */
    public function search($from, $to, $role, $user_id = null);

    /**
     * Get user's timekeeping status between 2 month (using for exporting)
     * @param $fromMonth
     * @param $toMonth
     * @param $userID
     * @return mixed
     */
    public function getBetweenMonth($fromMonth, $toMonth, $userID);

    /**
     * Get user's timekeeping status between 2 date
     * @param $request
     * @return mixed
     */
    public function getBetweenDate($request);

    /**
     * Get list timekeeping in month and year
     * @param $request
     * @return mixed
     */
    public function getInMonth($request);

    /**
     * Handle user update timekeeping request
     * @param $request
     * @return mixed
     */
    public function customUpdate($request);

    /**
     * For admin to statistic from list timekeeping of any user
     * @param $skip
     * @param $request
     * @return mixed
     */
    public function timeKeepingStatistic($skip, $request);

    /**
     * Get list timekeeping that user is waiting admin to accept
     * @param $fromMonth
     * @param $toMonth
     * @return mixed
     */
    public function getByWaitingAccept($fromMonth, $toMonth);
}
