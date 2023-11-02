<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware(['auth:api', 'checkrole']);
    }
    public function user($id = null){
        if($id){
            $user = User::where('id',$id)->get();
        } else{
            $this->authorize('adminView', User::class);
            $user = User::orderBy('id','asc')->get();
        }
        return UserResource::collection($user);
    }
}
