<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApplicationAvailabilityTest extends WebTestCase
{
    use Factories, ResetDatabase;

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public static function urlProvider(): \Generator
    {
        yield ["homepage" => '/'];
        yield ['/en'];
        yield ['/en/blog/'];
        yield ['/en/login'];
        yield ['/en/blog/search'];
    }

    /**
     * @dataProvider urlProviderAdmin
     * @dataProvider urlProviderUser
     */
    public function testPageIsRedirect($url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects('/en/login');
    }

    public static  function urlProviderAdmin(): \Generator
    {
        yield ['admin_post_index' => '/en/admin/post/'];
        yield ['admin_post_new' => '/en/admin/post/new'];
    }

    public static  function urlProviderUser(): \Generator
    {
        yield ['user_edit' => '/en/profile/edit'];
        yield ['user_change_password' => '/en/profile/change-password'];
    }


}