<?php

namespace App\Http\Controllers;

use App\Common\ResponseMessage;
use App\Common\Role;
use App\Http\Requests\MonthYearRequest;
use App\Http\Requests\TimeRequest;
use App\Http\Requests\UpdateTimeKeepingRequest;
use App\Http\Resources\TimeKeepingResource;
use App\Models\Systemtime;
use App\Models\TimeKeeping;
use App\Models\User;
use App\Repositories\TimeKeeping\TimeKeepingRepository;
use App\Repositories\TimeKeeping\TimeKeepingRepositoryInterface;
use DateTimeZone;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Parser\Block\ParagraphParser;

class TimeKeepingController extends Controller
{

    public function __construct(protected TimeKeepingRepositoryInterface $timeKeepRepo)
    {
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIn()
    {
        try {
            $this->timeKeepRepo->checkIn(auth()->id());
            return response()->json(['message' => ResponseMessage::OK]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOut()
    {
        try {
            $result = $this->timeKeepRepo->checkOut(auth()->id());
            if($result){
                return response()->json(['message' => ResponseMessage::OK]);
            } else {
                return response()->json(['message' => 'Invalid time checkout'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @return object
     */
    public function getTimeKeeping()
    {
        return response()->json($this->timeKeepRepo->getTimeKeepingToday());
    }
    /**
     * @return object
     */
    public function getListTimeKeeping($from, $to, $user_id = null)
    {
        $validator = Validator::make(['from' => $from, 'to' => $to], [
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['failed_data' => $validator->failed()], 400);
        }

        return TimeKeepingResource::collection(
            $this->timeKeepRepo->getListTimeKeepingFiltered($from, $to, auth()->user()->role, $user_id)
        );
    }

    /**
     * @param mixed $userId
     * @param mixed $fromMonth
     * @param mixed $toMonth
     *
     * @return object
     */
    function getTimeKeepingExport($fromMonth, $toMonth, $userId)
    {
        if ($userId && $fromMonth && $toMonth) {
            try {
                $timekeeping = $this->timeKeepRepo->getListTimeKeepingBetween($userId, $fromMonth, $toMonth);
                if (count($timekeeping) != 0) {
                    return TimeKeepingResource::collection($timekeeping);
                } else {
                    return response()->json(['message' => ResponseMessage::NOT_FOUND_ERROR], 404);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
    }

    /**
     * @param TimeRequest $request
     *
     * @return object
     */
    public function searchByAroundTime(TimeRequest $request)
    {
        return TimeKeepingResource::collection($this->timeKeepRepo->getListTimeKeepingAround($request));
    }

    /**
     * @param MonthYearRequest $request
     *
     * @return object
     */
    public function searchByMonth(MonthYearRequest $request)
    {
        return $this->timeKeepRepo->getListTimeKeepingInMonth($request);
    }

    public function updateTimeKeeping(UpdateTimeKeepingRequest $request)
    {
        $this->timeKeepRepo->customUpdate($request);

        return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
    }
}
