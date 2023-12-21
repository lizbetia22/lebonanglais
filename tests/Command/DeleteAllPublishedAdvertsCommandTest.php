<?php

namespace App\Tests\Command;

use App\Command\DeleteAllPublishedAdvertsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DeleteAllPublishedAdvertsCommandTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('DeleteAllPublishedAdverts');
        $commandTester = new CommandTester($command);
        $daysAgo = 7;
        $commandTester->execute([
            'days' => $daysAgo,
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('All published adverts was deleted which were created ' . $daysAgo . ' : days ago', $output);

    }

}