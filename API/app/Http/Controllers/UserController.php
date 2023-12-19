<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\User\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(protected UserServiceInterface $userService)
    {
    }

    /**
     * @param int|null $id
     *
     * @return object
     */
    public function user(int $id = null)
    {
        if ($id) {
            return new UserResource($this->userService->findUser($id));
        } else {
            $this->authorize('adminView', User::class);
            return UserResource::collection($this->userService->getAll());
        }
    }
}
