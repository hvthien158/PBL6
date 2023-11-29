<?php

namespace App\Repositories\Message;

use App\Models\Message;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;
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
    public function getLimit5Message()
    {
        return DB::table('messages')
            ->orderBy('id', 'desc')
            ->take(5)->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLimitUnreadMessage()
    {
        return DB::table('messages')
            ->where('is_read', '=', 0)
            ->orderBy('id', 'desc')
            ->take(5)->get();
    }

    /**
     * @param $request
     * @param $userID
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function customCreate($request, $userID)
    {
        $timekeeping = DB::table('time_keepings')
            ->where('_date', '=', $request->time_keeping_date)
            ->where('user_id', '=', $userID);

        if($timekeeping->first()->user_id != $userID){
            return response()->json(['error' => 'Not allow'], 405);
        }

        if($request->input('title') == 'Leave/remote work request'){
            $timekeeping->update([
                'admin_accept_status' => 1
            ]);
        } else if ($request->input('title') == 'Checkin/checkout request'){
            $timekeeping->update([
                'admin_accept_time' => 1
            ]);
        }

        $message = new Message();
        $message->title = $request->input('title');
        $message->content = $request->input('content');
        $message->user_id = $userID;
        $message->time_keeping_id = $timekeeping->first()->id;
        return $message->save();
    }

    /**
     * @param $id
     * @return void
     */
    public function checkReadMessage($id)
    {
        DB::table('messages')
            ->where('id', '=', $id)
            ->update(['is_read' => 1]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function checkPassMessage($id)
    {
        $messages = DB::table('messages')
            ->where('id', '=', $id);

        $message = $messages->first();
        $timekeeping = DB::table('time_keepings')
            ->where('id', '=', $message->time_keeping_id);
        if($message->title == 'Leave/remote work request'){
            $timekeeping->update(['admin_accept_status' => 2]);
        } else if($message->title === 'Checkin/checkout request') {
            $timekeeping->update(['admin_accept_time' => 2]);
            DB::table('systemtimes')
                ->where('id', '=', $message->time_keeping_id)
                ->update([
                    'time_check_in' => $timekeeping->first()->time_check_in,
                    'time_check_out' => $timekeeping->first()->time_check_out,
                ]);
        } else {
            return response()->json(['error' => 'Title incorrect'], 400);
        }

        $messages->update(['is_check' => 1, 'is_read' => 1]);
    }
}
