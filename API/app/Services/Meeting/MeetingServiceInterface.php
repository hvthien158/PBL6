<?php

namespace App\Services\Meeting;

use Illuminate\Http\Request;

interface MeetingServiceInterface
{
    public function getAll();
    public function getLimit($skip, $take);
    public function createNewMeeting(Request $request);
    public function deleteMeeting($meetingID);
}
