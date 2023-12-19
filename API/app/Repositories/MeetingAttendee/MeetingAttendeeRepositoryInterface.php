<?php

namespace App\Repositories\MeetingAttendee;

use App\Repositories\RepositoryInterface;

interface MeetingAttendeeRepositoryInterface extends RepositoryInterface
{
    public function deleteByMeetingID($meetingID);
    public function updateRead($meetingID, $userID);
}
