<?php

namespace App\Repositories\Shift;

use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use App\Repositories\BaseRepository;

class ShiftRepository extends BaseRepository implements ShiftRepositoryInterface
{

    public function getModel()
    {
        return Shift::class;
    }

    public function getAllFiltered($skip, $request)
    {
        $itemsPerPage = 10;
        if ($request->name != '') {
            $shift = Shift::whereRaw('LOWER(name) like ?', ['%' . $request->name . '%'])->skip($skip * 10)->take($itemsPerPage)->get();
            $totalPage = floor(Shift::whereRaw('LOWER(name) like ?', ['%' . $request->name . '%'])->count() / $itemsPerPage) + 1;
        } else {
            $totalPage = floor(Shift::count() / $itemsPerPage) + 1;
            $shift = Shift::skip($skip * $itemsPerPage)->take($itemsPerPage)->get();
        }
        return [
            'totalPage' => $totalPage,
            'shift' => ShiftResource::collection($shift),
        ];
    }
}
