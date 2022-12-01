<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function getUserInfo(int $id): User
    {
        return $this->userRepository->find($id);
    }

}