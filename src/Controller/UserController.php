<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Services\UserService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Route('/user-info/{id}', name: 'user_info')]
    public function userInfo(int $id, UserService $service)
    {
        $user = $service->getUserInfo($id);

        return $this->render('user-info.html.twig', [
            'user' => $user,
            'userBirthday' => $user->getDateOfBirth()->format('d-m-Y'),
            'phones' => $user->getPhones()
        ]);
    }

    #[Route('/user/top-up-phone/{amount}', name: 'user_info')]
    public function purhase(Phone $phone, int $amount)
    {


    }

}