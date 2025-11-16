<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PurchaseControllerTest extends WebTestCase
{
    private function createAndLoginUser(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $email = 'purchase-'.uniqid().'@example.com';
        $user->setEmail($email);
        $user->setFirstName('Repo');
        $user->setLastName('Test');
        $user->setAddressLine1('1 rue du Repo');
        $user->setPostalCode('75000');
        $user->setCity('Paris');
        $user->setCountry('France');
        $user->setPassword($hasher->hashPassword($user, 'MotDePasse123!'));

        $em->persist($user);
        $em->flush();

        $client->loginUser($user);

        return $client;
    }

    public function testPurchaseCreatesOrder(): void
    {
        $client = $this->createAndLoginUser();

        // On appelle la route d’achat
        $client->request('POST', '/purchase', [
            'product_id' => 1,
            'quantity'   => 1,
        ]);

        // On vérifie juste qu’on est bien redirigé (et donc pas d’erreur 500/404)
        $this->assertResponseRedirects('/');
    }
}
