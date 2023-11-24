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
        $this->repository = static::getContainer()->get('doctrine')->getRepository(AdminUser::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('AdminUser index');

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
            'admin_user[email]' => 'Testing',
            'admin_user[username]' => 'Testing',
            'admin_user[roles]' => 'Testing',
            'admin_user[password]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/user/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new AdminUser();
        $fixture->setEmail('My Title');
        $fixture->setUsername('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('AdminUser');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new AdminUser();
        $fixture->setEmail('My Title');
        $fixture->setUsername('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'admin_user[email]' => 'Something New',
            'admin_user[username]' => 'Something New',
            'admin_user[roles]' => 'Something New',
            'admin_user[password]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/user/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getUsername());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getPassword());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new AdminUser();
        $fixture->setEmail('My Title');
        $fixture->setUsername('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/user/');
    }
}
