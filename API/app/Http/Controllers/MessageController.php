<?php

namespace App\Http\Controllers;

use App\Common\ResponseMessage;
use App\Http\Requests\CreateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\TimeKeeping\TimeKeepingRepository;
use App\Repositories\TimeKeeping\TimeKeepingRepositoryInterface;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Department\DepartmentRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\NotificationSendController;
use App\Repositories\UserDeviceToken\UserDeviceTokenRepositoryInterface;

class MessageController extends Controller
{

    public function __construct(
        protected MessageRepositoryInterface $messageRepo,
        protected TimeKeepingRepositoryInterface $timeKeepingRepository,
        protected DepartmentRepositoryInterface $departmentRepo,
        protected UserRepositoryInterface $userRepo,
        protected UserDeviceTokenRepositoryInterface $userDeviceTokenRepo
    ) {
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAllMessage()
    {
        return MessageResource::collection($this->messageRepo->getAll());
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getLimitMessage()
    {
        return MessageResource::collection($this->messageRepo->getLimit5Message());
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getLimitUnreadMessage()
    {
        return MessageResource::collection($this->messageRepo->getLimitUnreadMessage());
    }

    /**
     * @param CreateMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRequest(CreateMessageRequest $request)
    {
        try {
            if ($this->messageRepo->customCreate($request, auth()->id())) {
                $notification = new NotificationSendController($this->departmentRepo, $this->userRepo, $this->userDeviceTokenRepo, $this->messageRepo);
                $notification->sendNotification(array_merge($request->toArray(), ['type' => 1]));
                return response()->json(['message' => ResponseMessage::OK]);
            }
            return response()->json([], 400);
            return response()->json(['message' => ResponseMessage::OK]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkReadMessage(Request $request)
    { //change from unread to read
        if (!$request->input('id')) {
            return response()->json(['message' => ResponseMessage::VALIDATION_ERROR], 422);
        }
        try {
            $this->messageRepo->checkReadMessage($request->input('id'));
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPassMassage(Request $request)
    { 
        //accept user request
        if (!$request->input('id')) {
            return response()->json(['message' => ResponseMessage::VALIDATION_ERROR], 422);
        }
        try {
            $this->messageRepo->checkPassMessage($request->input('id'));
            $notification = new NotificationSendController($this->departmentRepo, $this->userRepo, $this->userDeviceTokenRepo, $this->messageRepo);
                $notification->sendNotification(array_merge($request->toArray(), 
                [
                    'type' => 0,
                    'title' => 'Manager department confirm your request',
                    'content' => '...'
                ]));
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
