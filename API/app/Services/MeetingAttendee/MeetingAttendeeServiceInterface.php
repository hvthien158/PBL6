<?php

namespace App\Services\MeetingAttendee;

interface MeetingAttendeeServiceInterface
{
    /**
     * Get list of attendee ID
     * @param $meetingID
     * @return mixed
     */
    public function getByMeetingID($meetingID);

    /**
     * Get list of meeting instance of the attendee
     * @param $attendeeID
     * @return mixed
     */
    public function getByAttendeeID($attendeeID);
    public function create(array $attribute = []);
    public function deleteByMeetingID($meetingID);
    public function markAsRead($meetingID);
}
