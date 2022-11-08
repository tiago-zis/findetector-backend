<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{

    private $hasher;
    private $em; 

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->hasher = $userPasswordHasher;
        $this->em = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $user = new User();
        $user->setName("Admin");
        $user->setEmail('admin@email.com');
        $user->setIsVerified(true);
        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                '123456789'
            )
        );

        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;
    }
}