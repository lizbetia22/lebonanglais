<?php

namespace App\Test\Controller;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminUserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdminUserRepository $repository;
    private string $path = '/admin/user/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->repository = $this->manager->getRepository(AdminUser::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('AdminUser index');
    }

    public function testCreateNewUser(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'admin_user[username]' => 'Test name',
            'admin_user[email]' => 'test@gmail.com',
            'admin_user[plainPassword]' => 'azerty',
        ]);

        self::assertResponseRedirects('/admin/user/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShowUser(): void
    {
        $fixture = new AdminUser();
        $fixture->setUsername('Test name');
        $fixture->setEmail('test@gmail.com');
        $fixture->setPlainPassword('azerty');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('AdminUser');
    }

    public function testEditUser(): void
    {
        $fixture = new AdminUser();
        $fixture->setUsername('Test name');
        $fixture->setEmail('test@example.com');
        $fixture->setPlainPassword('azerty');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'admin_user[username]' => 'Test name',
            'admin_user[email]' => 'test@example.com',
            'admin_user[plainPassword]' => 'azerty',
        ]);

        self::assertResponseRedirects('/admin/user/');

        $updatedFixture = $this->repository->find($fixture->getId());

        self::assertSame('Test name', $updatedFixture->getUsername());
        self::assertSame('test@example.com', $updatedFixture->getEmail());
    }


    public function testRemoveUser(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new AdminUser();
        $fixture->setUsername('Test name');
        $fixture->setEmail('test@gmail.com');
        $fixture->setPlainPassword('azerty');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/user/');
    }
}