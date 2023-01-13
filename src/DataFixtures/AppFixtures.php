<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Utils\FakerTrait;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    use FakerTrait;

    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify;
        $userList = [];

        for ($i = 0; $i < random_int(30, 80); $i++) {
            $user = new User;
            $user->setEmail($faker->email())
                ->setUsername($faker->userName())
                ->setPassword($this->passwordEncoder->hashPassword($user, 'password'))
                ->setCreatedAt($this->setDateTimeBetween('-4 years', '-6 months'))
                ->setRoles(($i / random_int(1, 6)) === 0 ? ['ROLE_ADMIN'] : ['ROLE_USER'])
            ;

            $userList[$i] = $user;
            $manager->persist($user);
        }

        for ($i = 0; $i < random_int(50, 150); $i++) {
            $post = new Post;
            $state = ($i/4) === 0 ? array_rand(Post::STATES) : Post::STATES['Publié'];

            $post->setTitle($faker->words(random_int(2, 5), true))
                ->setContent($this->setPageContent())
                ->setState($state)
                ->setAuthor($this->randomElement($userList))
                ->setCreatedAt($this->setDateTimeAfter($post->getAuthor()->getCreatedAt()))
                ->setUpdatedAt($state === Post::STATES['Nouveau'] ? null : $this->setRandomDatetimeAfter($post->getCreatedAt()))
                ->setSlug($slugify->slugify($post->getTitle()))
            ;

            if ($post->getState() === Post::STATES['Publié']) {
                $post->setPublisedAt($this->setRandomDatetimeAfter($post->getCreatedAt()));
            }

            for ($i=0; $i < random_int(0, 10); $i++) {
                $comment = new Comment;

                $comment->setContent($faker->text(random_int(5, 100)))
                    ->setCreatedAt($this->setRandomDatetimeAfter($post->getCreatedAt()))
                    ->setAuthor($this->randomElement($userList))
                ;

                $post->addComment($comment);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }
}
