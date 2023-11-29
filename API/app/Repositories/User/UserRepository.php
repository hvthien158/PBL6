<?php

namespace App\Repositories\User;

use App\Http\Controllers\GoogleDriveController;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    public function getModel()
    {
        return User::class;
    }

    /**
     * @param $request
     * @param $id
     * @return array
     */
    public function getAllFiltered($request, $id)
    {
        $itemsPerPage = 8;
        $user = User::orderBy('id', 'asc');
        if ($request->name != '') {
            $user->whereRaw('LOWER(name) like ?', ['%' . $request->name . '%']);
        }
        if ($request->email != '') {
            $user->whereRaw('LOWER(email) like ?', ['%' . $request->email . '%']);
        }
        if ($request->address != '') {
            $user->whereRaw('LOWER(address) like ?', ['%' . $request->address . '%']);
        }
        if ($request->phoneNumber != '') {
            $user->whereRaw('LOWER(phone_number) like ?', ['%' . $request->phoneNumber . '%']);
        }
        if ($request->position != '') {
            $user->where('position', $request->position);
        }
        if ($request->role != '') {
            $user->where('role', $request->role);
        }
        if ($request->department != '') {
            $user->where('department_id', $request->department);
        }
        $totalUser = $user->count();
        return [
            'total' => $totalUser,
            'user' => $user->skip($id * $itemsPerPage)->take($itemsPerPage)->get()
        ];
    }

    /**
     * @param $user
     * @param $request
     * @return mixed
     */
    public function updateProfile($user, $request)
    {
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $googleDriver = new GoogleDriveController();
            $path = $googleDriver->googleDriveFileUpload($avatar);
            $user->update(array_merge($request->validated(), ['avatar' => $path]));
        } else {
            $user->update(array_merge($request->validated()));
        }
        return $user;
    }

    /**
     * @param $email
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function checkEmail($email)
    {
        return DB::table('users')->where('email', '=', $email)->first();
    }
}
