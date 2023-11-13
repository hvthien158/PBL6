<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @return void
     */
    public function __construct(){
        $this->middleware(['auth:api', 'checkrole']);
    }

    /**
     * @param int|null $id
     *
     * @return object
     */
    public function user(int $id = null){
        if($id){
            $user = User::where('id',$id)->get();
            return UserResource::collection($user);
        } else{
            $this->authorize('adminView', User::class);
            $user = User::orderBy('id','asc')->get();
            return UserResource::collection($user);
        }
    }
}
