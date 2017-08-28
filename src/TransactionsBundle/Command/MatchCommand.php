<?php

// src/AppBundle/Command/GreetCommand.php
namespace TransactionsBundle\Command;

use \Entity\Transactions;
use Categories\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MatchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('match:payments')
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $category = $input->getArgument('transaction_type');
        $em = $this->getContainer()->get('doctrine')->getManager();

        // List of Transactions Without Category
        $transactions = $em->getRepository('TransactionsBundle:Transactions')->findBy(
            array('categories' => null)
        );

        if ($category === "all") {
            dump('Mattching all Types');
            $categories = $em
                ->getRepository('CategoriesBundle:Categories')
                ->findAll();

            foreach ($categories as $category) {
                if ($category->getParent() === null) {
                    continue;
                }

                dump("Matching ".$category->getName()." : ");

                $matchedT = $em
                    ->getRepository('TransactionsBundle:Transactions')
                    ->findBy(
                        array(
                            'categories' => $category->getId()
                        )
                    );

                $this->cycleTransactions(
                    $matchedT,
                    $transactions,
                    $category->getId()
                );
            }
        }

        if ($category !== "all") {
            $matchedT = $em
                ->getRepository('TransactionsBundle:Transactions')
                ->findBy(
                    array(
                        'categories' => $category
                    )
                );

            $this->cycleTransactions(
                $matchedT,
                $transactions,
                $category
            );
        }
    }

    protected function cycleTransactions(
        $matchedT,
        $transactions,
        $category
    ) {
        $results = array();

        foreach ($matchedT as $match) {
            if (!$match->getCategories()) {
                continue;
            }

            $matches =  $this->match($match, $transactions, $category);

            if (count($matches) === 0) {
                continue;
            }

            foreach ($matches as $matchR) {
                $results[
                        $match
                            ->getCategories()
                            ->getId()
                    ]
                    [
                        $matchR->getId()
                    ] = $matchR;
            }
        }
    }

    // TODO OMG PLEASE REMOVE THIS CODE FROM HERE
    public function match($match, $transaction, $category)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $results = array();

        $matchDescription = array_filter(preg_split(
            '/[\s\/\*]/',
            $match->getDescription()
        ));

        foreach ($transaction as $item) {
            $score   = 0;
            $special = 0;

            $itemDescription = preg_split(
                '/[\s\/\*]/',
                preg_replace(
                    '!\s+!',
                    ' ',
                    $item->getDescription()
                )
            );

            foreach ($itemDescription as $item1) {
                if ($item1 == 'TRTP' || $item1 == 'IBAN' ||
                    $item1 == 'BIC' || $item1 == 'NAME' ||
                    $item1 == 'EREF' || $item1 == 'SEPA' ||
                    $item1 == 'REMI' || $item1 == 'CSID' ||
                    $item1 == 'Incasso' || $item1 == 'MARF' ||
                    $item1 == '' || $item1 == 'algemeen' ||
                    $item1 == 'doorlopend' || $item1 == 'IBAN:' ||
                    $item1 == 'Overboeking' || $item1 == 'INGBNL2A' ||
                    $item1 == 'BIC:' || $item1 == 'Omschrijving:' ||
                    $item1 == 'SEPA' || $item1 == 'OVERBOEKING' ||
                    $item1 == 'BEA'
                ) {
                    $special += 1;
                    continue;
                }

                if (in_array($item1, $matchDescription)) {
                    $score += 1;
                }
            }

            $matchPercent = round(
                (($score*100)/(count($itemDescription)-$special)),
                0
            );

            $type = $em
                ->getRepository('CategoriesBundle:Categories')
                ->findById($category);

            if ($item->getPossibleMatch() || $item->getCategories()) {
                continue;
            }

            if ($matchPercent >= 90) {
                $item->setCategories($type[0]);

                $em->persist($item);
                $em->flush();

                $results[$item->getId()] = $item;
                $score = 0;
                $special = 0;

                continue;
            }

            if (
                $matchPercent > 50
                // && $item->getAmount() === $match->getAmount()
                && !$item->getPossibleMatch()
            ) {
                dump(
                    "Match for "
                    .$matchPercent
                    ." Marking as Possible "
                    .$item->getDescription()
                    ." to "
                    .$match->getDescription()
                );

                $item->setPossibleMatch($type[0]->getId());
                $em->persist($item);
                $em->flush();
                continue;
            }
        }
        return $results;
    }
}
