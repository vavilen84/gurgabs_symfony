<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestEmailCommand extends Command
{
    protected static $defaultName = 'app:test-email';
    protected $mailer;

    public function __construct(string $name = null, \Swift_Mailer $mailer)
    {
        parent::__construct($name);
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Send test email command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('vladimir.teplov@gmail.com')
            ->setBody('body');

        $this->mailer->send($message);

        $io->success('Success!');

        return 0;
    }
}
