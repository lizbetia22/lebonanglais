<?php

namespace Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Advert;
use App\Entity\Picture;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureControllerApiTest extends ApiTestCase
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

    public function testGetPictures()
    {
        $this->client->request('GET', '/api/pictures');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testGetPictureById()
    {
        $fixtureCategory = new Category();
        $fixtureCategory->setName('My Title');

        $this->entityManager->persist($fixtureCategory);
        $this->entityManager->flush();

        $fixtureAdvert = new Advert();
        $fixtureAdvert->setTitle('My Title');
        $fixtureAdvert->setContent('My Title');
        $fixtureAdvert->setAuthor('My Title');
        $fixtureAdvert->setPrice(12);
        $fixtureAdvert->setEmail('testnew@gmail.com');
        $fixtureAdvert->setCategory($fixtureCategory);

        $this->entityManager->persist($fixtureAdvert);
        $this->entityManager->flush();

        $fixture = new Picture();
        $fixture->setPath('test_path.jpg');
        $fixture->setCreatedAt(new \DateTime());
        $fixture->setAdvert($fixtureAdvert);

        $this->entityManager->persist($fixture);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/pictures/' . $fixture->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testCreatePicture()
    {
        $fixtureCategory = new Category();
        $fixtureCategory->setName('My Title');

        $this->entityManager->persist($fixtureCategory);
        $this->entityManager->flush();

        $fixtureAdvert = new Advert();
        $fixtureAdvert->setTitle('My Title');
        $fixtureAdvert->setContent('My Title');
        $fixtureAdvert->setAuthor('My Title');
        $fixtureAdvert->setPrice(12);
        $fixtureAdvert->setEmail('testnew@gmail.com');
        $fixtureAdvert->setCategory($fixtureCategory);

        $this->entityManager->persist($fixtureAdvert);
        $this->entityManager->flush();

        $file = new UploadedFile(
            __DIR__ . '/res/picture.png',
            'picture.png',
            'image/png'
        );

        $this->client->request(
            'POST',
            '/api/pictures',
            [
                'headers' => [
                    'ACCEPT' => 'application/ld+json',
                ],
                'extra' => [
                    'files' => [
                        'file' => $file
                    ]
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}