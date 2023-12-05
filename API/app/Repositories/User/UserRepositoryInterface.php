<?php

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function getAllFiltered($request, $id);

    public function updateProfile($user, $request);

    public function checkEmail($email);

    public function getListAdmin();
}
