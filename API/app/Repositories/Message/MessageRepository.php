<?php

namespace App\Repositories\Message;

use App\Models\Message;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;

class MessageRepository extends BaseRepository implements RepositoryInterface
{

    public function getModel()
    {
        return Message::class;
    }
}
