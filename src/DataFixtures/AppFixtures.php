<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadUser($manager);
    }

//  наповнюєм бд фейковими данними
    public function loadUser(ObjectManager $manager)
    {
        $operatorCode = [50, 67, 63, 68];
        for ($i = 1; $i <= 2000; $i++){
            $user = new User();
            $user->setName('User ' . $i);
            $randomDate = mt_rand(1, 28) . "-" . mt_rand(1, 12) . "-" . mt_rand(1945, 2015);
            $date = \DateTime::createFromFormat('d-m-Y', $randomDate);
            $user->setDateOfBirth($date);
            $this->addReference('user_'.$i, $user);
            for ($a = 1; $a <= rand(1, 3); $a++){
                $phone = new Phone();
                /** @var User $user */
                $user = $this->getReference('user_'.$i);
                $phone->setUser($user);
                $phone->setCountryCode('380');
                $phone->setOperatorCode($operatorCode[array_rand($operatorCode)]);
                $phone->setNumber(rand(1000000, 9999999));
                $phone->setBalance(rand(-50, 150));
                $manager->persist($phone);
            }
            $manager->persist($user);
        }

        $manager->flush();
    }

}
