<?php

namespace App\Repositories\User;

use App\Http\Controllers\GoogleDriveController;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Common\Role;
class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    public function getModel()
    {
        return User::class;
    }
}
