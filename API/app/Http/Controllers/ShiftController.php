<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\Shift\ShiftRepositoryInterface;
use App\Services\Shift\ShiftServiceInterface;
use Illuminate\Http\Request;


class ShiftController extends Controller
{

    public function __construct(protected ShiftServiceInterface $shiftService)
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
            return ShiftResource::collection($this->shiftService->getAll());
        }
        return new ShiftResource($this->shiftService->findShift($id));
    }
}
