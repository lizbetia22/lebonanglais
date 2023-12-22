<?php

namespace App\Tests\Command;

use App\Command\DeleteNotAttachedImagesCommand;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class DeleteNotAttachedImagesCommandTest extends KernelTestCase
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

        $command = $application->find('DeleteNotAttachedImages');
        $commandTester = new CommandTester($command);
        $daysAgo = 7;
        $commandTester->execute([
            'days' => $daysAgo,
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('All pictures was deleted which were created  ' . $daysAgo . ' days ago', $output);
    }

    public function testExecuteIncorrectValue()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('DeleteAllPublishedAdverts');
        $commandTester = new CommandTester($command);
        $daysAgo = '0';

        $commandTester->execute([
            'days' => '0',
        ]);

        $commandTester->execute([
            'days' => $daysAgo,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Incorrect numbers of days', $output);
    }

}