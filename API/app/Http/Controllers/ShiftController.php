<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\Shift\ShiftRepositoryInterface;
use Illuminate\Http\Request;


class ShiftController extends Controller
{

    public function __construct(protected ShiftRepositoryInterface $shiftRepo)
    {
    }

    /**
     * @param null|int $id
     *
     * @return object
     */
    public function index($id = null)
    {
        if($id == null){
            return ShiftResource::collection($this->shiftRepo->getAll());
        }
        return new ShiftResource($this->shiftRepo->find($id));
    }
}
