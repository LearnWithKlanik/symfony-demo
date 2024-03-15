<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class MultiLanguageTest extends PantherTestCase
{
    public function testChangeLanguage(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'Welcome to the Symfony Demo application');

        $selector = $crawler->filter('.language-selector-dropdown-button');
        $this->assertCount(1, $selector);
        $client->click($selector->link());

        $selectLanguage = $crawler->filter('a:lang(fr)');
        $client->waitForVisibility('#locale-selector-modal');

        $this->assertTrue($selectLanguage->isDisplayed());

        $client->click($selectLanguage->link());

        $this->assertSelectorTextContains('h1', 'Bienvenue sur l\'application Symfony Demo');
    }
}
