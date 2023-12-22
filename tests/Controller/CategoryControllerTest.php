<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CategoryRepository $repository;
    private string $path = '/admin/category/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->repository = $this->manager->getRepository(Category::class);

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Category index');
    }

    public function testCreateNewCategory(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));
        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'category[name]' => 'Test category',
        ]);

        self::assertResponseRedirects('/admin/category/');
        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShowCategory(): void
    {
        $fixture = new Category();
        $fixture->setName('Test name');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);

    }

    public function testEditCategory(): void
    {
        $fixture = new Category();
        $fixture->setName('Test name');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', '/admin/category/'.$fixture->getId().'/edit');

        $this->client->submitForm('Save', [
            'category[name]' => 'Test category',
        ]);

        self::assertResponseRedirects('/admin/category/');

        $updatedFixture = $this->repository->find($fixture->getId());

        self::assertSame('Test category', $updatedFixture->getName());
    }

    public function testDeleteCategory(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Category();
        $fixture->setName('Test name');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/category/');
    }
}