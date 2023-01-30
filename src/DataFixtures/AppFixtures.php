<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Photo;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->loadPhotos($manager);
        $this->loadUsers($manager); 

        $manager->flush();
    }
    public function loadPhotos(ObjectManager $manager)
    {
        for ($i = 1; $i <= 5; $i++) {
            $photo = new Photo();
            $photo->setTitle('Photo numéro ' . $i);
            $photo->setPostAt((new \DateTimeImmutable())->add(\DateInterval::createFromDateString('-' . $i . ' week')));
            $manager->persist($photo);
        }
        $manager->flush();
    }
    public function loadUsers(ObjectManager $manager): Void
    {
        $user = new User();
        $user->setEmail('user1@dwwm.fr');
        $user->setPseudo('user_1');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user1'));
        $manager->persist($user);
        $manager->flush();
    }
}
    

