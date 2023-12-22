<?php

namespace Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Advert;
use App\Entity\Category;
use App\Repository\AdvertRepository;
use http\Client\Response;

class AdvertControllerApiTest extends ApiTestCase
{
    private $client;
    private $entityManager;

    private $advertRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->advertRepositoryMock = $this->createMock(AdvertRepository::class);
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testGetAdverts()
    {
        $this->client->request('GET', '/api/adverts');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }


    public function testGetAdvertById()
    {
        $fixtureCategory = new Category();
        $fixtureCategory->setName('Test category');

        $this->entityManager->persist($fixtureCategory);
        $this->entityManager->flush();

        $fixture = new Advert();
        $fixture->setTitle('Test title');
        $fixture->setContent('Test content');
        $fixture->setAuthor('Test Author');
        $fixture->setPrice(100);
        $fixture->setEmail('test@example.com');
        $fixture->setCategory($fixtureCategory);

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/adverts/' . $fixture->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testCreateAdvert()
    {
        $fixtureCategory = new Category();
        $fixtureCategory->setName('Test category');

        $this->entityManager->persist($fixtureCategory);
        $this->entityManager->flush();

        $response = $this->client->request(
            'POST',
            '/api/adverts',
            [
                'headers' => [
                    'ACCEPT' => 'application/ld+json',
                    'CONTENT-TYPE' => 'application/ld+json',
                ],
                'json' => [
                    "title" => "Test title",
                    "content" => "Test content",
                    "author" => "Test Author",
                    "email" => "test@example.com",
                    "category" => '/api/categories/' . $fixtureCategory->getId(),
                    "price" => 100,
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}