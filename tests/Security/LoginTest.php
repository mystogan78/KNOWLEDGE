<?php

namespace App\Tests\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginTest extends WebTestCase
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
        $user->setIsVerified(true); // ✅ très important pour que le login réussisse
        $user->setPassword($hasher->hashPassword($user, 'MotDePasse123!'));

        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testUserCanLogin(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $em     = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        // email unique pour ce test
        $email = 'login-'.uniqid().'@example.com';

        $this->createValidUser($em, $hasher, $email);

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        // On prend le premier formulaire de la page
        $form = $crawler->filter('form')->first()->form([
            // ⚠ si dans ton formulaire les name sont différents (_username/_password),
            // il faudra adapter ces clés
            'email'    => $email,
            'password' => 'MotDePasse123!',
        ]);

        $client->submit($form);

        // On attend une redirection après login réussi
        $this->assertResponseRedirects('/'); // adapte si tu rediriges ailleurs après login
        $client->followRedirect();
    }
}
