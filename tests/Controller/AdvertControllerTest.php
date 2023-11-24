<?php

namespace App\Test\Controller;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvertControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdvertRepository $repository;
    private string $path = '/advert/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Advert::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Advert index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'advert[title]' => 'Testing',
            'advert[content]' => 'Testing',
            'advert[author]' => 'Testing',
            'advert[email]' => 'Testing',
            'advert[price]' => 'Testing',
            'advert[state]' => 'Testing',
            'advert[createdAt]' => 'Testing',
            'advert[publishedAt]' => 'Testing',
            'advert[category]' => 'Testing',
        ]);

        self::assertResponseRedirects('/advert/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Advert();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPrice('My Title');
        $fixture->setState('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setPublishedAt('My Title');
        $fixture->setCategory('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Advert');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Advert();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPrice('My Title');
        $fixture->setState('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setPublishedAt('My Title');
        $fixture->setCategory('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'advert[title]' => 'Something New',
            'advert[content]' => 'Something New',
            'advert[author]' => 'Something New',
            'advert[email]' => 'Something New',
            'advert[price]' => 'Something New',
            'advert[state]' => 'Something New',
            'advert[createdAt]' => 'Something New',
            'advert[publishedAt]' => 'Something New',
            'advert[category]' => 'Something New',
        ]);

        self::assertResponseRedirects('/advert/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getAuthor());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPrice());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getPublishedAt());
        self::assertSame('Something New', $fixture[0]->getCategory());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Advert();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPrice('My Title');
        $fixture->setState('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setPublishedAt('My Title');
        $fixture->setCategory('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/advert/');
    }
}
