<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class LoginTest extends PantherTestCase
{
    public function testLoginSuccessful(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $backendLink = $crawler->selectLink('Browse backend')->link();
        $crawler = $client->click($backendLink);

        $form = $crawler->selectButton('Sign in')->form([
            '_username' => 'jane_admin',
            '_password' => 'kitten',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('h1', 'Post List');
    }
}
