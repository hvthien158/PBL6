<?php

namespace App\Services\Systemtime;

use App\Repositories\Systemtime\SystemtimeRepositoryInterface;

class SystemtimeService implements SystemtimeServiceInterface
{
    public function __construct(protected SystemtimeRepositoryInterface $systemtimeRepository)
    {}

    public function insertGetID($attribute = [])
    {
        return $this->systemtimeRepository->insertGetId($attribute);
    }
}
