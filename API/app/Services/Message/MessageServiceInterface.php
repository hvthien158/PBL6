<?php

namespace App\Services\Message;

interface MessageServiceInterface
{
    /**
     * Get 1 message object
     * @param $id
     * @return mixed
     */
    public function findMessage($id);

    /**
     * Get all data of message model
     * @return mixed
     */
    public function getAll();

    /**
     * Get 5 newest messages (includes read messages)
     * @return mixed
     */
    public function getLimit5Message();

    /**
     * Get 5 newest unread messages
     * @return mixed
     */
    public function getLimitUnreadMessage();

    /**
     * Create new admin message and change status "not yet" -> "waiting"
     * @param $request
     * @return mixed
     */
    public function customCreate($request);

    /**
     * Change status after admin read request
     * @param $id
     * @return mixed
     */
    public function markAsReadMessage($id);

    /**
     * Change status after admin confirm request
     * @param $id
     * @return mixed
     */
    public function markAsConfirmedMessage($id);
}
