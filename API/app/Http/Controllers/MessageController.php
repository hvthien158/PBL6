<?php

namespace App\Http\Controllers;

use App\Common\ResponseMessage;
use App\Http\Requests\CreateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function getAllMessage(){
        return MessageResource::collection(Message::all());
    }

    public function getLimitMessage(){
        return MessageResource::collection(DB::table('messages')
            ->orderBy('id', 'desc')
            ->take(5)->get());
    }

    public function getLimitUnreadMessage(){
        return MessageResource::collection(DB::table('messages')
            ->where('is_read', '=', 0)
            ->orderBy('id', 'desc')
            ->take(5)->get());
    }

    public function createRequest(CreateMessageRequest $request){
        try {
            $timekeeping = DB::table('time_keepings')
                ->where('_date', '=', $request->time_keeping_date)
                ->where('user_id', '=', auth()->id());

            if($timekeeping->first()->user_id != auth()->id()){
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
            $message->user_id = auth()->id();
            $message->time_keeping_id = $timekeeping->first()->id;
            $message->save();

            return response()->json(['message' => ResponseMessage::OK]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function checkReadMessage(Request $request){ //change from unread to read
        $validator = Validator::make($request->all(), [
           'id' => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json(['message' => ResponseMessage::VALIDATION_ERROR], 422);
        }

        try {
            DB::table('messages')
                ->where('id', '=', $request->input('id'))
                ->update(['is_read' => 1]);
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function checkPassMassage(Request $request){ //accept user request
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json(['message' => ResponseMessage::VALIDATION_ERROR], 422);
        }

        try {
            $messages = DB::table('messages')
                ->where('id', '=', $request->input('id'));

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
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
