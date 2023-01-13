<?php

namespace App\DataFixtures;

use App\Entity\CheckList;
use App\Entity\Task;
use App\Entity\User;
use App\Utils\FakerTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    use FakerTrait;

    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $colors = [
            "rgba(75,56,179,.18)",
            "rgba(53,119,241,.18)",
            "rgba(69,203,133,.18)",
            "rgba(41,156,219,.18)",
            "rgba(240,101,72,.18)",
            "rgba(255,190,11,.18)",
            "rgba(33,37,41,.18)",
            "rgba(243,246,249,.18)",
        ];
        $roles = ['user', 'admin', 'manager'];

        for ($i=1; $i < random_int(30, 50); $i++) {
            $user = new User;

            $user->setEmail('user-' . $i . '@react-api.com')
                ->setPassword($this->passwordEncoder->hashPassword($user, 'password'))
                ->setRoles(User::ROLES[$this->randomElement($roles)])
                ->setUsername('username_' . $i)
                ->setCreatedAt($this->setDateTimeBetween('-10 years', '-2 years'))
                ;
            
            for ($c=1; $c < random_int(4, 10); $c++) {
                $list = new CheckList;
                $list->setName('liste ' . $c)
                    ->setCreatedAt($this->setDateTimeAfter($user->getCreatedAt()))
                    ->setDueDate(is_int($c / random_int(2, 4)) ? null : $this->setDateTimeAfter($list->getCreatedAt()))
                    ->setIsOpen(!is_int($c / random_int(2, 4)))
                    ->setColor($this->randomElement($colors))
                ;
                
                for ($t=1; $t < random_int(1, 20); $t++) {
                    $task = new Task;

                    $task->setName('t' . $c . '.l' . $t)
                        ->setIsDone(!is_int($t / random_int(2, 4)))
                        ->setCreatedAt($this->setDateTimeAfter($list->getCreatedAt()))
                    ;

                    $list->addTask($task);
                }

                $user->addCheckList($list);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
