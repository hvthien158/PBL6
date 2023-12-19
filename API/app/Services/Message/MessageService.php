<?php

namespace App\Services\Message;

use App\Common\MessageTitle;
use App\Common\SQLOperator;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Systemtime\SystemtimeRepositoryInterface;
use App\Repositories\TimeKeeping\TimeKeepingRepositoryInterface;
use Illuminate\Support\Collection;

class MessageService implements MessageServiceInterface
{
    public function __construct(
        protected MessageRepositoryInterface $messageRepository,
        protected DepartmentRepositoryInterface $departmentRepository,
        protected TimeKeepingRepositoryInterface $timeKeepingRepository,
        protected SystemtimeRepositoryInterface $systemtimeRepository,
    )
    {}

    public function getLimit5Message()
    {
        $userInDepartment = $this->departmentRepository->getAllUserInDepartmentOfManager();
        if($userInDepartment){
            return $this->messageRepository->getLimit5Message($userInDepartment);
        }
        return Collection::empty();
    }

    public function getLimitUnreadMessage()
    {
        $userInDepartment = $this->departmentRepository->getAllUserInDepartmentOfManager();
        if($userInDepartment){
            return $this->messageRepository->getLimitUnreadMessage($userInDepartment);
        }
        return Collection::empty();
    }

    public function customCreate($request)
    {
        $timekeeping = $this->timeKeepingRepository->getModelByMultiKeys([
            ['_date', SQLOperator::EQUAL, $request->time_keeping_date],
            ['user_id', SQLOperator::EQUAL, auth()->id()]
        ]);
        if ($timekeeping->first()->user_id != auth()->id()) {
            return false;
        }

        if ($request->input('title') == MessageTitle::LEAVE_REMOTE_REG) {
            $timekeeping->update([
                'admin_accept_status' => 1
            ]);
        } else if ($request->input('title') == MessageTitle::CHECKIN_CHECKOUT_REQ) {
            $timekeeping->update([
                'admin_accept_time' => 1
            ]);
        }

        return $this->messageRepository->createWithTimestamp(
            $request->title,
            $request->content,
            $timekeeping->first()->id,
            auth()->id(),
        );
    }

    public function markAsReadMessage($id)
    {
        return $this->messageRepository->updateRead($id);
    }

    public function markAsConfirmedMessage($id)
    {
        $message = $this->messageRepository->find($id);
        $timekeeping = $this->timeKeepingRepository->getModelByMultiKeys([
            ['id', SQLOperator::EQUAL, $message->time_keeping_id]
        ]);
        if ($message->title == MessageTitle::LEAVE_REMOTE_REG) {
            $timekeeping->update(['admin_accept_status' => 2]);
        } else if ($message->title === MessageTitle::CHECKIN_CHECKOUT_REQ) {
            $timekeeping->update(['admin_accept_time' => 2]);
            $this->systemtimeRepository->updateByID($message->time_keep_id, [
                'time_check_in' => $timekeeping->first()->time_check_in,
                'time_check_out' => $timekeeping->first()->time_check_out,
            ]);
        } else {
            return false;
        }
        return $this->messageRepository->updateByID($id, [
            'is_check' => 1,
            'is_read' => 1
        ]);
    }

    public function getAll()
    {
        return $this->messageRepository->selectAll();
    }

    public function findMessage($id)
    {
        return $this->messageRepository->find($id);
    }
}
