<?php

namespace App\Services\User;

use App\Common\Role;
use App\Common\SQLOperator;
use App\Http\Controllers\GoogleDriveController;
use App\Repositories\User\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {}

    public function search($skip, $request)
    {
        $itemsPerPage = 8;
        $user = $this->userRepository->getModelByMultiKeys([
            ['LOWER(name) like ?', SQLOperator::RAW_SQL, '%' . $request->name . '%'],
            ['LOWER(email) like ?', SQLOperator::RAW_SQL, '%' . $request->email . '%'],
            ['LOWER(address) like ?', SQLOperator::RAW_SQL, '%' . $request->address . '%'],
            ['LOWER(phone_number) like ?', SQLOperator::RAW_SQL, '%' . $request->phoneNumber . '%'],
            ['position', SQLOperator::EQUAL, $request->position],
            ['role', SQLOperator::EQUAL, $request->role],
            ['department_id', SQLOperator::EQUAL, $request->department]
        ]);
        $totalUser = $this->userRepository->count($user);
        return [
            'total' => $totalUser,
            'user' => $this->userRepository->selectLimit($skip, $itemsPerPage, $user),
        ];
    }

    public function updateProfile($user, $request)
    {
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $googleDriver = new GoogleDriveController();
            $path = $googleDriver->googleDriveFileUpload($avatar);
            $user = $this->userRepository->update($user, array_merge($request->validated(), ['avatar' => $path]));
        } else {
            $user = $this->userRepository->update($user, array_merge($request->validated()));
        }
        return $user;
    }

    public function checkEmail($email)
    {
        return $this->userRepository->selectByMultiKeys([
           ['email', SQLOperator::EQUAL, $email]
        ]);
    }
    public function getListAdmin() {
        return $this->userRepository->selectByMultiKeys([
           ['role', SQLOperator::EQUAL, Role::ADMIN]
        ]);
    }

    public function createNewUser(array $attribute = [])
    {
        return $this->userRepository->create($attribute);
    }

    public function updateUser($user, $attribute = [])
    {
        return $this->userRepository->update($user, $attribute);
    }

    public function findUser($id)
    {
        return $this->userRepository->find($id);
    }

    public function updateByID($id, $attribute = [])
    {
        return $this->userRepository->updateByID($id, $attribute);
    }

    public function getAll()
    {
        return $this->userRepository->selectAll();
    }
}
