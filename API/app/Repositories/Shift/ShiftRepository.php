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

    public function countByNameLike($name)
    {
        return $this->model->whereRaw('LOWER(name) like ?', ['%' . $name . '%'])->count();
    }

    public function findByNameLikeAndSkip($name, $skip, $take)
    {
        return $this->model->whereRaw('LOWER(name) like ?', ['%' . $name . '%'])->skip($skip * 10)->take($take)->get();
    }

    public function getLimit($skip, $take)
    {
        return $this->model->skip($skip * $take)->take($take)->get();
    }

    public function countAll()
    {
        return $this->model->count();
    }
}
