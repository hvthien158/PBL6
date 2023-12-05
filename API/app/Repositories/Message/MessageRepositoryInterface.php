<?php

namespace App\Repositories\Message;

use App\Repositories\RepositoryInterface;

interface MessageRepositoryInterface extends RepositoryInterface
{
    public function getLimit5Message();

    public function getLimitUnreadMessage();

    public function customCreate($request, $userID);

    //Change status after admin read request
    public function markAsReadMessage($id);

    //Change status after admin confirm request
    public function markAsConfirmedMessage($id);
}
