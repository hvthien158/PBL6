<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(protected UserRepositoryInterface $userRepo)
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
            return new UserResource($this->userRepo->find($id));
        } else {
            $this->authorize('adminView', User::class);
            return UserResource::collection($this->userRepo->getAll());
        }
    }
}
