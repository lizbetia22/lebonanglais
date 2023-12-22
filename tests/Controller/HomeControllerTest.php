<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/home');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorExists('h1', 'HomeController');
    }
}
