<?php

// src/AppBundle/Command/GreetCommand.php
namespace AccountBundle\Command;

use AccountBundle\Entity\Transactions;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MatchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('match:payments')
            ->setDescription('Match Payments of a Certain Type')
            ->addArgument(
                'transaction_type',
                InputArgument::OPTIONAL,
                'id of the transaction type to match'
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
        $typeId = $input->getArgument('transaction_type');

        $doctrine   = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $verify = $em->getRepository('AccountBundle:Transactions')->findBy(array('transactionType' => $typeId));
        $toMatch = $em->getRepository('AccountBundle:Transactions')->findBy(array('transactionType' => null ));
        $results[] = array();

        foreach ($verify as $match) {
            if ($match->getTransactionType()) {
                $matches =  $this->match($match, $toMatch, $typeId);
                if (count($matches) > 0) {
                    foreach ($matches as $matchR) {
                        $results[$match->getTransactionType()->getId()][$matchR->getId()] = $matchR;
                    }
                } else {
                    continue;
                }
            }
        }

        /*
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
        */
//        print "Please use a csv file with content";
    }

    // TODO OMG PLEASE REMOVE THIS CODE FROM HERE
    public function match($toBeSave, $transaction, $typeId)
    {
        $results = array();
        $transactionDescription = preg_split('/[\s\/\*]/', $toBeSave->getDescription());
        $doctrine   = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        foreach ($transaction as $item) {
            if ($item->getTransactionType()) {
                continue;
            }

            $score = 0;
            $special = 0;

            $itemDescription = $item->getDescription();
            $itemDescription = preg_replace('!\s+!', ' ', $itemDescription);
            $itemDescription = preg_split('/[\s\/\*]/', $itemDescription);

            foreach ($itemDescription as $item1) {
                if ($item1 == 'TRTP' || $item1 == 'IBAN' || $item1 == 'BIC' ||
                    $item1 == 'NAME' || $item1 == 'EREF' || $item1 == 'SEPA' ||
                    $item1 == 'REMI' || $item1 == 'CSID' || $item1 == 'Incasso' ||
                    $item1 == 'MARF' || $item1 == '' || $item1 == 'algemeen' ||
                    $item1 == 'doorlopend' || $item1 == 'IBAN:' ||
                    $item1 == 'Overboeking' || $item1 == 'INGBNL2A' ||
                    $item1 == 'BIC:' || $item1 == 'Omschrijving:' ||
                    $item1 == 'SEPA'
                ) {
                    $special += 1;
                    continue;
                }

                if (in_array($item1, $transactionDescription)) {
                    $score += 1;
                }

								$matchPercent = round((($score*100)/(count($itemDescription)-$special)), 0);
                if ($matchPercent >= 75) {
										$type = $em->getRepository('AccountBundle:TransactionType')->findById($typeId);
										$item->setTransactionType($type[0]);

										$em->persist($item);
										$em->flush();
//dump($item);
//dump($score);
//dump($item->getDescription());
//dump($item->getId());
//dump(round((($score*100)/(count($itemDescription)-$special)), 0));
//die;
                    $results[$item->getId()] = $item;
                    $score = 0;
                    $special = 0;
                    continue;
                }
            }
        }

        // Even more hugly code :P
        return $results;
    }

}

?>
