<?php

namespace App\Repositories\TimeKeeping;

use App\Common\ResponseMessage;
use App\Common\Role;
use App\Common\TimeByShift;
use App\Http\Resources\TimeKeepingResource;
use App\Models\Department;
use App\Models\Shift;
use App\Models\Systemtime;
use App\Models\TimeKeeping;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class TimeKeepingRepository extends BaseRepository implements TimeKeepingRepositoryInterface
{

    public function getModel()
    {
        return TimeKeeping::class;
    }


    public function insertGetId($attribute = [])
    {
        return $this->model->insertGetId($attribute);
    }

    public function getByDateBetweenAndOrderByDesc($from, $to, $userID)
    {
        return $this->model->where('user_id', $userID)
            ->whereBetween('_date', [$from, $to])
            ->orderBy('_date', 'desc')->get();
    }

    public function getInMonthYear($month, $year, $userID)
    {
        if ($month && $year) {
            return $this->model->where('user_id', $userID)->whereMonth('_date', $month)->whereYear('_date', $year)->get();
        } else if ($year) {
            return $this->model->where('user_id', $userID)->whereYear('_date', $year)->get();
        }
        return [];
    }

    public function updateByDateAndUserID($date, $userID, $attribute = [])
    {
        $this->model->where('user_id', '=', $userID)
            ->where('_date', '=', $date)
            ->update($attribute);
    }
}
