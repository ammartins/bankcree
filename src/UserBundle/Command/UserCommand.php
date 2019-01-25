<?php

namespace UserBundle\Command;

use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Create new user')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Userame for login'
            )
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'User email'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'Account Password'
            )
            ->addArgument(
                'bankaccount',
                InputArgument::REQUIRED,
                'Account Number'
            )
            ->addArgument(
                'isSavings',
                InputArgument::REQUIRED,
                'Savings Account'
            )
            ->addArgument(
                'ignoreSavings',
                InputArgument::REQUIRED,
                'Savings Account'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $accountNumber = $input->getArgument('bankaccount');
        $isSavings = $input->getArgument('isSavings');
        $ignoreSavings = $input->getArgument('ignoreSavings');

        // $isActive = 1;
        $createdAt = new \DateTime();

        $doctrine   = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $user = new user();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setCreatedAt($createdAt);
        $user->setPassword(password_hash($password, 1));
        $user->setBankAccount($accountNumber);
        $user->setIsSavings($isSavings);
        $user->setIgnoreSavings($ignoreSavings);

        $em->persist($user);
        $em->flush();

        return;
    }
}
