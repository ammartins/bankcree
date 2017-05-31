<?php

namespace LoginBundle\Command;

use LoginBundle\Entity\User;
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
          ->addOption(
              'yell',
              null,
              InputOption::VALUE_NONE,
              'If set, the task will yell in uppercase letters'
          );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
      $username = $input->getArgument('username');
      $email = $input->getArgument('email');
      $password = $input->getArgument('password');
      // $isActive = 1;
      $createdAt = new \DateTime();

      $doctrine   = $this->getContainer()->get('doctrine');
      $em = $doctrine->getManager();

      $user = new user();
      $user->setUsername($username);
      $user->setEmail($email);
      $user->setCreatedAt($createdAt);
      $user->setPassword(password_hash($password, 1));
      
      $em->persist($user);
      $em->flush();

      return;
  }
}
