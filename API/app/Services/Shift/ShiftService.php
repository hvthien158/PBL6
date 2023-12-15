<?php

namespace App\Services\Shift;

use App\Http\Resources\ShiftResource;
use App\Models\Shift;
use App\Repositories\Shift\ShiftRepositoryInterface;

class ShiftService implements ShiftServiceInterface
{
    public function __construct(protected ShiftRepositoryInterface $shiftRepository)
    {}

    public function search($skip, $request)
    {
        $itemsPerPage = 10;
        if ($request->name != '') {
            $shift = $this->shiftRepository->findByNameLikeAndSkip($request->name, $skip, $itemsPerPage);
            $totalPage = floor($this->shiftRepository->countByNameLike($request->name) / $itemsPerPage) + 1;
        } else {
            $totalPage = floor($this->shiftRepository->countAll() / $itemsPerPage) + 1;
            $shift = $this->shiftRepository->getLimit($skip, $itemsPerPage);
        }
        return [
            'totalPage' => $totalPage,
            'shift' => ShiftResource::collection($shift),
        ];
    }

    public function createNewShift(array $attribute = [])
    {
        return $this->shiftRepository->create($attribute);
    }

    public function updateShift($shift, array $attribute = [])
    {
        return $this->shiftRepository->update($shift, $attribute);
    }

    public function getAll()
    {
        return $this->shiftRepository->selectAll();
    }

    public function findShift($id)
    {
        return $this->shiftRepository->find($id);
    }
}
