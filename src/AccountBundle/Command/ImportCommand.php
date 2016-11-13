<?php

// src/AppBundle/Command/GreetCommand.php
namespace AccountBundle\Command;

use AccountBundle\Entity\Transactions;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:csv')
            ->setDescription('import transactions')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'locations of the file'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileLocation = $input->getArgument('name');
        $fileContent = file_get_contents($fileLocation);
        $fileContentArray = explode( "\n", $fileContent);

        $doctrine   = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $skip = 1;

        if ($fileContent)
        {
          foreach ($fileContentArray as $line)
          {
            if ( $skip ) {
                $skip = FALSE;
                continue;
            }
            if ( empty($line) ) {
                continue;
            }
            $info = explode(";", $line);
            $correctDate = substr($info[2],0,4).'-'.substr($info[2],4,2).'-'.substr($info[2],6,2);
            $Date = new \DateTime($correctDate);

            # Generate Hash
            $hashString = $line;
            $hash = hash('md5', $hashString, False);

            // Check if this is already on DB and if so continue
            // Should probably clean this a bit
            if ($em->getRepository('AccountBundle:Transactions')->getTransactionByHash($hash))
            {
                continue;
            }

            $transaction = new Transactions();

            $transaction->setTransactionHash($hash);
            $transaction->setCreateAt($Date);
            $transaction->setAmount(floatval(str_replace(',', '.', str_replace('.', '', $info[6]))));
            $transaction->setstartsaldo(floatval(str_replace(',', '.', str_replace('.', '', $info[4]))));
            $transaction->setEndsaldo(floatval(str_replace(',', '.', str_replace('.', '', $info[5]))));
            $transaction->setDescription($info[7]);
            $transaction->setShortDescription('');
            $transaction->setAccountId(1);

            $em->persist($transaction);
            $em->flush();

            return;
          }
        }
        print "Please use a csv file with content";
    }
}

?>
