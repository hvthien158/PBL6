<?php

namespace App\Services\MeetingAttendee;

use App\Common\SQLOperator;
use App\Repositories\Meeting\MeetingRepositoryInterface;
use App\Repositories\MeetingAttendee\MeetingAttendeeRepositoryInterface;

class MeetingAttendeeService implements MeetingAttendeeServiceInterface
{

    public function __construct(
        protected MeetingAttendeeRepositoryInterface $attendeeRepository,
        protected MeetingRepositoryInterface $meetingRepository
    )
    {}

    public function getByMeetingID($meetingID)
    {
        return $this->attendeeRepository->selectByMultiKeys([
           ['meeting_id', SQLOperator::EQUAL, $meetingID]
        ]);
    }

    public function getByAttendeeID($attendeeID)
    {
        $temp = $this->attendeeRepository->selectByMultiKeys([
           ['attendee_id', SQLOperator::EQUAL, $attendeeID]
        ]);
        $meetings = [];
        foreach ($temp as $meetingAttendee){
            $meetings[] = $meetingAttendee->meeting_id;
        }
        return $this->meetingRepository->selectByMultiKeys([
           ['id', SQLOperator::IN, $meetings]
        ]);
    }

    public function create(array $attribute = [])
    {
        return $this->attendeeRepository->create($attribute);
    }

    public function deleteByMeetingID($meetingID)
    {
        return $this->attendeeRepository->deleteByMeetingID($meetingID);
    }


    public function markAsRead($meetingID)
    {
        return $this->attendeeRepository->updateRead($meetingID, auth()->id());
    }
}
