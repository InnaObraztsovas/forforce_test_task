<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    // зная ID пользователя получаем его имя, год рождения и список телефонных номеров;
    #[Route('/user-info/{id}', name: 'user_info', methods: 'GET')]
    public function userInfo(int $id): Response
    {
        $repository =  $this->entityManager->getRepository(User::class);
        /** @var User $user */
        $user = $repository->find($id);
        if (!$user) {
            return $this->json('No user found for id' . $id, 404);
        }
        if (!empty($user->getPhones())) {
            foreach ($user->getPhones() as $phone) {
                /** @var Phone $phone */
                $phones[] = $phone->formatNumber();
            }
        }

        return $this->json([
            'name' => $user->getName(),
            'birthday' => $user->getDateOfBirth()->format('d-m-Y'),
            'phones' => $phones ?? [],
        ]);
    }

    //возможность пополнить любой из номеров на сумму до 100грн. максимум за одно пополнение;
    #[Route('/phone/purchase', name: 'phone_purchase', methods: 'POST')]
    public function purchase(Request $request): Response
    {
        $data = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        if ((int)$data['amount'] > 100) {
            return $this->json('You can pay max 100 grn for every payment', 400);
        }
        $rep = $this->entityManager->getRepository(Phone::class);
        /** @var Phone $phone */
        $phone = $rep->find($data['phone_id']);
        if (!$phone){
            return $this->json('The number is not found', 404);
        }

        $sum = $phone->getBalance() + (int) $data['amount'];
        $phone->setBalance($sum);
        $this->entityManager->flush();
        return $this->json('The phone was purchased!');
    }

    //возможность добавить нового пользователя;
    #[Route('/add-user', name: 'add_user', methods: 'POST')]
    public function addUser( Request $request): Response
    {
        $data = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $date = new DateTime($data['date_of_birth']);
        $user = new User();
        $user->setName($data['name']);
        $user->setDateOfBirth($date);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json('The new user is created', 201);
    }

    //возможность добавить для пользователя номер мобильного телефона;
    #[Route('/add-phone', name: 'add_phone', methods: 'POST')]
    public function addPhone(Request $request): Response
    {
        $data = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($data['user_id']);
        if (!$user) {
            return $this->json('No user found for id' . $data['user_id'], 404);
        }

        $phone = new Phone();
        $phone->setCountryCode($data['country_code']);
        $phone->setOperatorCode($data['operator_code']);
        $phone->setNumber($data['number']);
        $phone->setBalance(0);
        $phone->setUser($user);
        $this->entityManager->persist($phone);
        $this->entityManager->flush();

        return $this->json('The new phone number for ' . $data['user_id'] . ' is created', 201);

    }

    //возможность удалить всю информацию о пользователе вместе с номерами телефонов;
    #[Route('/delete-user/{id}', name: 'delete_user', methods: 'POST')]
    public function deleteUser(int $id): Response
    {
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->find($id);
        if (!empty($user)) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
        return $this->json('The user was deleted', 200);
    }

}