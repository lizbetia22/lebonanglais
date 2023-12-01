<?php

namespace App\Command;

use App\Entity\Advert;
use App\Entity\Picture;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'DeleteAllPublishedAdverts',
    description: 'Delete all published adverts created X days ago',
)]
class DeleteAllPublishedAdvertsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('days', null, InputOption::VALUE_REQUIRED, 'days ago');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $daysAgo = $input->getArgument('days');

            if (!is_numeric($daysAgo) || $daysAgo <= 0) {
                $io->error('Incorrect numbers of days');
                return Command::FAILURE;
            }

            $currentDate = new DateTime();
            $currentDate->modify('-' . $daysAgo . ' days');

            $io->success('Adverts was found which were created ' . $daysAgo . ' days ago');

            $adverts = $this->entityManager->getRepository(Advert::class)->createQueryBuilder('a')
                ->where('a.publishedAt IS NOT NULL')
                ->andWhere('a.publishedAt <= :currentDate')
                ->setParameter('currentDate', $currentDate)
                ->getQuery()
                ->getResult();

            foreach ($adverts as $advert) {
                $pictures = $this->entityManager->getRepository(Picture::class)->createQueryBuilder('b')
                    ->where('b.advert = :advert')
                    ->setParameter('advert', $advert)
                    ->getQuery()
                    ->getResult();

                foreach ($pictures as $picture) {
                    $this->entityManager->remove($picture);
                }

                $this->entityManager->remove($advert);
            }

            $this->entityManager->flush();

            $io->success('All published adverts was deleted which were created ' . $daysAgo . ' : days ago');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
