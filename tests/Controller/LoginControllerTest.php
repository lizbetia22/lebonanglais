<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $client->getCrawler()->filter('form')->count(),
            'There should be at least one form element on the page.'
        );
    }
}
