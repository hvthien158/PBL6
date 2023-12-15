<?php

namespace App\Repositories\Message;

use App\Models\Message;
use App\Models\Department;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{

    public function getModel()
    {
        return Message::class;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLimit5Message($userInDepartment)
    {
        return Message::whereIn('user_id', $userInDepartment)->orderBy('id', 'desc')->take(5)->get();
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLimitUnreadMessage($userInDepartment)
    {
        return Message::whereIn('user_id', $userInDepartment)->where('is_read', 0)->orderBy('id', 'desc')->take(5)->get();
    }

    /**
     * @param $title
     * @param $content
     * @param $timekeepingID
     * @param $userID
     * @return bool
     */
    public function createWithTimestamp($title, $content, $timekeepingID, $userID)
    {
        $message = new Message();
        $message->title = $title;
        $message->content = $content;
        $message->user_id = $userID;
        $message->time_keeping_id = $timekeepingID;
        return $message->save();
    }

    /**
     * @param $id
     * @return void
     */
    public function updateRead($id)
    {
        return $this->model->where('id', '=', $id)
            ->update(['is_read' => 1]);
    }
}
