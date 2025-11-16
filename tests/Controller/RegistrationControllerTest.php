<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testUserRegistrationCreatesAccount(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var UserRepository $userRepo */
        $userRepo = $container->get(UserRepository::class);

        $countBefore = count($userRepo->findAll());

        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();

        $email = 'register-'.uniqid().'@example.com';

        // On prend le premier formulaire de la page
        $form = $crawler->filter('form')->first()->form([
            'registration_form[lastName]'      => 'Test',
            'registration_form[firstName]'     => 'User',
            'registration_form[addressLine1]'  => '1 rue des Tests',
            'registration_form[addressLine2]'  => '',
            'registration_form[postalCode]'    => '75000',
            'registration_form[city]'          => 'Paris',
            'registration_form[country]'       => 'France',
            'registration_form[email]'         => $email,
            'registration_form[plainPassword]' => 'MotDePasse123!',
            'registration_form[agreeTerms]'    => true,
        ]);

        $client->submit($form);

        // Attends une redirection après succès (ex: vers la home)
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        $countAfter = count($userRepo->findAll());
        $this->assertSame($countBefore + 1, $countAfter);

        $user = $userRepo->findOneBy(['email' => $email]);
        $this->assertNotNull($user);
    }
}
