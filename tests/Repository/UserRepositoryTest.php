<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRepositoryTest extends KernelTestCase
{
    private function createValidUser(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        string $email
    ): User {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setAddressLine1('1 rue des Tests');
        $user->setPostalCode('75000');
        $user->setCity('Paris');
        $user->setCountry('France');
        $user->setPassword($hasher->hashPassword($user, 'MotDePasse123!'));

        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testFindByEmailReturnsUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $em     = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);
        $repo   = $container->get(UserRepository::class);

        $email = 'repo-'.uniqid().'@example.com';

        $this->createValidUser($em, $hasher, $email);

        $found = $repo->findOneBy(['email' => $email]);

        $this->assertNotNull($found);
        $this->assertSame($email, $found->getEmail());
    }
}
