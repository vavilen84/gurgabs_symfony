<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Enum\User as UserEnum;

class CreateAdminCommand extends Command
{
    private $em;

    protected static $defaultName = 'app:create-admin';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create admin console command')
            ->addArgument('email', InputArgument::REQUIRED, 'E-Mail')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = new User();

        $user->setPassword($password);
        $user->setEmail($email);
        $user->setRoles([UserEnum::ROLE_USER, UserEnum::ROLE_ADMIN]);

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Admin is created!');

        return 0;
    }
}
