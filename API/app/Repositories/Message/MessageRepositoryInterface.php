<?php

namespace App\Repositories\Message;

use App\Repositories\RepositoryInterface;

interface MessageRepositoryInterface extends RepositoryInterface
{
    public function getLimit5Message($userInDepartment);

    public function getLimitUnreadMessage($userInDepartment);

    public function createWithTimestamp($title, $content, $timekeepingID, $userID);

    public function updateRead($id);
}
