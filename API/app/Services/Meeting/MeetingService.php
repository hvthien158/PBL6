<?php

namespace App\Services\Meeting;

use App\Repositories\Meeting\MeetingRepositoryInterface;
use App\Repositories\MeetingAttendee\MeetingAttendeeRepositoryInterface;
use Illuminate\Http\Request;

class MeetingService implements MeetingServiceInterface
{
    public function __construct(
        protected MeetingRepositoryInterface $meetingRepository,
        protected MeetingAttendeeRepositoryInterface $meetingAttendeeRepository
    )
    {}

    public function getAll()
    {
        return $this->meetingRepository->selectAll();
    }

    public function getLimit($skip, $take)
    {
        return $this->meetingRepository->selectLimit($skip, $take);
    }

    public function createNewMeeting(Request $request)
    {
        $meetingID = $this->meetingRepository->insertGetId([
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
            'detail' => $request->detail ?: 'None',
            'user_id' => auth()->id()
        ]);

        foreach ($request->attendees as $attendee){
            $this->meetingAttendeeRepository->create([
                'meeting_id' => $meetingID,
                'attendee_id' => $attendee,
            ]);
        }
    }

    public function deleteMeeting($meetingID)
    {
        $this->meetingAttendeeRepository->deleteByMeetingID($meetingID);
        $this->meetingRepository->delete($meetingID);
    }
}
