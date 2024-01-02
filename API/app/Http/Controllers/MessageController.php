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
use App\Services\Department\DepartmentServiceInterface;
use App\Services\Message\MessageServiceInterface;
use App\Services\TimeKeeping\TimekeepingServiceInterface;
use App\Services\User\UserServiceInterface;
use App\Services\UserDeviceToken\UserDeviceTokenServiceInterface;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\NotificationSendController;
use App\Repositories\UserDeviceToken\UserDeviceTokenRepositoryInterface;

class MessageController extends Controller
{

    public function __construct(
        protected MessageServiceInterface $messageService,
        protected TimekeepingServiceInterface $timekeepingService,
        protected DepartmentServiceInterface $departmentService,
        protected UserServiceInterface $userService,
        protected UserDeviceTokenServiceInterface $userDeviceTokenService
    ) {
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAllMessage()
    {
        return MessageResource::collection($this->messageService->getAll());
    }

    public function getLimitMessage()
    {
        try {
            if ($this->departmentService->checkManager()) {
                return MessageResource::collection($this->messageService->getLimit5Message());
            }
            return response()->json(['message' => ResponseMessage::AUTHORIZATION_ERROR], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getLimitUnreadMessage()
    {
        try {
            if ($this->departmentService->checkManager()) {
                return MessageResource::collection($this->messageService->getLimitUnreadMessage());
            }
            return response()->json(['message' => ResponseMessage::AUTHORIZATION_ERROR], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @param CreateMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRequest(CreateMessageRequest $request)
    {
        try {
            if ($this->messageService->customCreate($request)) {
//                $notification = new NotificationSendController(
//                    $this->departmentService,
//                    $this->userService,
//                    $this->userDeviceTokenService,
//                    $this->messageService
//                );
//                $notification->sendNotification(array_merge($request->toArray(), ['type' => 1]));
                return response()->json(['message' => ResponseMessage::OK]);
            }
            return response()->json([], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    { //change from unread to read
        if (!$request->input('id')) {
            return response()->json(['message' => ResponseMessage::VALIDATION_ERROR], 422);
        }
        try {
            $this->messageService->markAsReadMessage($request->input('id'));
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function markAsConfirmed(Request $request)
    {
        if (!$request->input('id')) {
            return response()->json(['message' => ResponseMessage::VALIDATION_ERROR], 422);
        }
        try {
            $this->messageService->markAsConfirmedMessage($request->input('id'));
//            $notification = new NotificationSendController(
//                $this->departmentService,
//                $this->userService,
//                $this->userDeviceTokenService,
//                $this->messageService
//            );
//            $notification->sendNotification(array_merge($request->toArray(),
//                [
//                    'type' => 0,
//                    'title' => 'Manager department confirm your request',
//                    'content' => '...'
//                ]));
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
