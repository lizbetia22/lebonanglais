<?php

namespace Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Controller\AdminCategoryController;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerApiTest extends ApiTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testGetAllCategories()
    {
        $this->client->request('GET', '/api/categories');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testGetCategoryShow()
    {
        $fixture = new Category();
        $fixture->setName('Test name');

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/categories/' . $fixture->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

}