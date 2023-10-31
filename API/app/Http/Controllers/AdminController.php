<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api']);
    }
    public function createUser(CreateUserRequest $request){
        $this->authorize('create', User::class);
        User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password), 
            'department_id' => $request->department_id,
            'address' => $request->address,
            'DOB' => $request->DOB,
            'phone_number' => $request->phone_number,
            'salary' => $request->salary,
            'position' => $request->position,
            'role' => $request->role
        ]);
        return response()->json(['message', 'Tạo User mới thành công']);
    }
    public function updateUser(UpdateUserRequest $request, User $user, $id){
        $this->authorize('update', $user);
        try {
            $user = User::find($id)->update([
                'name'=> $request->name,
                'email'=> $request->email,
                'department_id' => $request->department_id,
                'address'=> $request->address,
                'DOB'=> $request->DOB,
                'phone_number'=> $request->phone_number,
                'salary'=> $request->salary,
                'position'=> $request->position,
                'role'=> $request->role
            ]);
            return response()->json(['message','Chỉnh sửa thành công']);
        }catch(\Exception $e){
            return response()->json(['message'=> $e->getMessage()]);
        }
    }
    public function deleteUser(User $user, $id){
        $this->authorize('delete', $user);
        try {
            $user = User::find($id);
            if($user){
                $user->delete();
            }
            return response()->json(['message','Xóa thành công']);
        }catch(\Exception $e){
            return response()->json(['message'=> $e->getMessage()]);
        }
    }
}
