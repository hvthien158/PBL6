<?php

namespace App\Http\Controllers;

use App\Common\ResponseMessage;
use App\Models\Meeting;
use App\Services\Meeting\MeetingServiceInterface;
use App\Services\MeetingAttendee\MeetingAttendeeServiceInterface;
use Illuminate\Http\Request;

class MeetingController extends Controller
{

    public function __construct(
        protected MeetingServiceInterface $meetingService,
        protected MeetingAttendeeServiceInterface $attendeeService,
    )
    {}

    public function getAll(){
        return $this->meetingService->getAll();
    }

    public function getLimit(Request $request){
        return $this->meetingService->getLimit($request->skip, $request->take);
    }

    public function getMyScheduleMeeting(){
        return $this->attendeeService->getByAttendeeID(auth()->id());
    }

    public function createNewScheduleMeeting(Request $request){
        $this->meetingService->createNewMeeting($request);
        return response()->json(['message' => ResponseMessage::CREATE_SUCCESS], 201);
    }

    public function markAsRead(Request $request){
        if (!$request->input('meeting_id')) {
            return response()->json(['message' => ResponseMessage::VALIDATION_ERROR], 422);
        }
        try {
            $this->attendeeService->markAsRead($request->input('meeting_id'));
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteScheduleMeeting(Request $request){
        return $this->meetingService->deleteMeeting($request->meeting_id);
    }
}
