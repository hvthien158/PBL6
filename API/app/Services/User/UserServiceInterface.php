<?php

namespace App\Services\User;

interface UserServiceInterface
{
    /**
     * Get 1 user object
     * @param $id
     * @return mixed
     */
    public function findUser($id);

    /**
     * Get all data of user model
     * @return mixed
     */
    public function getAll();

    /**
     * Search user via multi condition
     * @param $skip
     * @param $request
     * @return mixed
     */
    public function search($skip, $request);

    /**
     * Handle user update profile request
     * @param $user
     * @param $request
     * @return mixed
     */
    public function updateProfile($user, $request);

    /**
     * Check whether email exists
     * @param $email
     * @return mixed
     */
    public function checkEmail($email);

    /**
     * Get all users have role admin
     * @return mixed
     */
    public function getListAdmin();

    /**
     * Create new user with given attribute
     * @param array $attribute
     * @return mixed
     */
    public function createNewUser(array $attribute = []);

    /**
     * Update the given user
     * @param $user
     * @param $attribute
     * @return mixed
     */
    public function updateUser($user, $attribute = []);

    /**
     * Update user via their id
     * @param $id
     * @param $attribute
     * @return mixed
     */
    public function updateByID($id, $attribute = []);
}
