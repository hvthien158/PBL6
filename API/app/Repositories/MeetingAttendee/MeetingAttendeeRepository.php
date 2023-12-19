<?php

namespace App\Repositories\MeetingAttendee;

use App\Models\MeetingAttendee;
use App\Repositories\BaseRepository;

class MeetingAttendeeRepository extends BaseRepository implements MeetingAttendeeRepositoryInterface
{

    public function getModel()
    {
        return MeetingAttendee::class;
    }

    public function deleteByMeetingID($meetingID)
    {
        return $this->model->where('meeting_id', $meetingID)->delete();
    }

    public function updateRead($meetingID, $userID)
    {
        return $this->model->where('meeting_id', '=', $meetingID)
            ->where('user_id', '=', $userID)
            ->update(['is_read' => true]);
    }
}
