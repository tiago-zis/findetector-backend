<?php

namespace App\Command;

use App\Entity\TermsOfUse;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:create-terms-of-use')]
class CreateTermsOfUseCommand extends Command
{

    private $hasher;
    private $em;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->hasher = $userPasswordHasher;
        $this->em = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // configure an argument
            ->addArgument('terms', InputArgument::REQUIRED, 'Terms of use argument');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from(TermsOfUse::class, 'r')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(1);
        $result = $qb->getQuery()->getResult();

        $version = "1.00";

        if (!empty($result)) {
            $data = $result[0];
            $version = (float)$data->getVersion();
            $version += 0.01;
        }

        $terms = new TermsOfUse();
        $terms->setContent($input->getArgument('terms'));
        $terms->setVersion($version);
        $this->em->persist($terms);
        $this->em->flush();

        return Command::SUCCESS;
    }
}
