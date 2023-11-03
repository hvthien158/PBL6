<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use Illuminate\Http\Request;


class ShiftController extends Controller
{
    /**
     * @param null|int $id
     * 
     * @return object
     */
    public function index($id = null)
    {
        return ($id == null) ? ShiftResource::collection(Shift::all()) : ShiftResource::collection(Shift::where('id', $id)->get());
    }
}
