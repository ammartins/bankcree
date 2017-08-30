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
        $transactions = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->findBy(array('categories' => null));

        foreach ($transactions as $key => $transaction) {
            if (
                (
                    $transaction->getPossibleMatch()
                    // && $transaction->getMatchPercentage() > 75
                )
                || $transaction->getCategories()
            ) {
                $possibleMatch = $em
                    ->getRepository('CategoriesBundle:Categories')
                    ->findById($transaction->getPossibleMatch())[0];
                unset($transactions[$key]);
                continue;
            }
        }

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
        $matchService = $this
            ->getApplication()
            ->getKernel()
            ->getContainer()
            ->get('transactions.match');

        foreach ($matchedT as $match) {
            if (!$match->getCategories()) {
                continue;
            }

            $matches = $matchService->match($match, $transactions, $category);

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
}
