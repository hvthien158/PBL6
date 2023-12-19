<?php

namespace App\Services\Systemtime;

interface SystemtimeServiceInterface
{
    /**
     * Create a new systemtime instance and get its id
     * @param array $attribute
     * @return mixed
     */
    public function insertGetID(array $attribute = []);
}
