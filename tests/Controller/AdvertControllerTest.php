<?php

namespace App\Test\Controller;

use App\Entity\Advert;
use App\Entity\Category;
use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\WorkflowInterface;

class AdvertControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdvertRepository $repository;
    private CategoryRepository $categoryRepository;
    private string $path = '/admin/advert/';
    private EntityManagerInterface $manager;
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Advert::class);
        $this->categoryRepository = static::getContainer()->get('doctrine')->getRepository(Category::class);
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->request('GET', $this->path);
        $this->client->followRedirect();

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Advert index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testShow(): void
    {
        $fixtureCategory = new Category();
        $fixtureCategory->setName('My Title');

        $this->manager->persist($fixtureCategory);
        $this->manager->flush();

        $fixture = new Advert();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setPrice(12);
        $fixture->setEmail('testnew@gmail.com');
        $fixture->setCategory($fixtureCategory);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('/admin/'.$fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Advert');
        self::assertSelectorTextContains('body', 'My Title');
    }


    public function testUpdateState(): void
    {
        $fixtureCategory = new Category();
        $fixtureCategory->setName('My Title');

        $this->manager->persist($fixtureCategory);
        $this->manager->flush();

        $fixture = new Advert();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setAuthor('My Title');
        $fixture->setPrice(12);
        $fixture->setEmail('testnew@gmail.com');
        $fixture->setCategory($fixtureCategory);

        $this->manager->persist($fixture);
        $this->manager->flush();

        $url = '/admin/'.$fixture->getId().'/publish';
        $this->client->request('POST',$url);

        $updatedFixture = $this->repository->find($fixture->getId());
        self::assertSame('published', $updatedFixture->getState());
    }

}