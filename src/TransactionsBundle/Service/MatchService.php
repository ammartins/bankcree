<?php

namespace TransactionsBundle\Service;

use TransactionsBundle\Entity\Transactions;
use CategoriesBundle\Entity\Categories;
use CategoriesBundle\Repository\CategoriesRepository;
use TransactionsBundle\Repository\TransactionsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;

class MatchService
{
    /**
     * @var \TransactionsBundle\Repository\TransactionsRepository
     */
    protected $transactionsRepository;
    protected $categoryRepository;
    protected $entityManager;

    public function __construct(
        TransactionsRepository $transactionsRepository,
        CategoriesRepository $categoryRepository,
        EntityManager $entityManager
    ) {
        $this->transactionsRepository = $transactionsRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    public function match($matches, $transaction, $category)
    {
        $matchDescription = $this->cleanUp($transaction->getDescription());
        $type = $this->categoryRepository->findById($category);

        if ($transaction->getCategories() != null) {
            return;
        }

        foreach ($matches as $match) {
            if ($type[0]->getAccountId() != $match->getAccountId()) {
                continue;
            }

            $score = 0;
            $special = 0;

            $itemDescription = $this->cleanUp($match->getDescription());
            $customRegex = $type[0]->getCustomRegex();

            // If Category contains a custom regex just match against it
            if ($type[0]->getCustomRegex() &&
                preg_match(
                    "/$customRegex/",
                    $transaction->getDescription()
                )
            ) {
                $transaction->setCategories($type[0]);
                $transaction->setMatchPercentage(100);

                $this->entityManager->persist($transaction);
                $this->entityManager->flush();

                continue;
            }

            foreach ($itemDescription as $item1) {
                if (in_array($item1, $matchDescription)) {
                    $score += 1;
                }
            }

            $matchPercent = round((($score*100)/(count($itemDescription))), 0);

            if ($matchPercent >= 100 || ($matchPercent >= 90 && $match->getAmount() == $transaction->getAmount())) {
                $transaction->setCategories($type[0]);
                $transaction->setMatchPercentage($matchPercent);

                $this->entityManager->persist($transaction);
                $this->entityManager->flush();

                $score = 0;
                $special = 0;

                break;
            }
        }
    }

    public function cleanUp($description)
    {
        $description = preg_replace(
            "/\w{3,}\s+\w+\:[A-Z0-9]+\s+[0-9]{2,}.[0-9]{2,}.[0-9]{2,}\/[0-9]{2,}\.[0-9]{2,}\s/",
            '',
            $description
        );
        $description = preg_replace("/,\w{4,}/", '', $description);
        $description = preg_replace("/SEPA Incasso algemeen doorlopend Incassant:/", '', $description);
        $description = preg_replace('/(\d{1,2}[.\/])+\d{1,2}/', '', $description);
        $description = preg_replace("/\d\d-\d\d-\d\d\d\d/", "", $description);
        $description = preg_replace('/\w+:[A-Z0-9]+/', '', $description);
        $description = preg_replace("/,PAS[0-9]{3}/", '', $description);
        $description = array_filter(preg_split('/[\s\/\*]/', $description));

        foreach ($description as $key => $item1) {
            if ($item1 == 'TRTP' || $item1 == 'IBAN'
                || $item1 == 'BIC' || $item1 == 'NAME'
                || $item1 == 'EREF' || $item1 == 'SEPA'
                || $item1 == 'REMI' || $item1 == 'CSID'
                || $item1 == 'Incasso' || $item1 == 'MARF'
                || $item1 == '' || $item1 == 'algemeen'
                || $item1 == 'doorlopend' || $item1 == 'IBAN:'
                || $item1 == 'Overboeking' || $item1 == 'INGBNL2A'
                || $item1 == 'BIC:' || $item1 == 'Omschrijving:'
                || $item1 == 'SEPA' || $item1 == 'OVERBOEKING'
                || $item1 == 'BEA' || $item1 == 'NOTPROVIDED'
                || $item1 == 'Naam:' || preg_match('/PAS[0-9]{3}/', $item1)
            ) {
                unset($description[$key]);
                continue;
            }
        }

        return $description;
    }

    public function matchToClean($toBeSave, $transaction)
    {
        $results = array();
        $categorieDesc = $this->cleanUp($toBeSave->getDescription());

        foreach ($transaction as $item) {
            if ($item->getCategories()) {
                continue;
            }

            $score = 0;
            $special = 0;

            $customRegex = $toBeSave->getCategories()->getCustomRegex();

            // If Category contains a custom regex just match against it
            if ($toBeSave->getCategories()->getCustomRegex() &&
                preg_match(
                    "/$customRegex/",
                    $item->getDescription()
                )
            ) {
                $item->setMatchPercentage(100);
                $results[$item->getId()] = $item;
                continue;
            }

            $itemDescription = preg_replace('!\s+!', ' ', $item->getDescription());
            $itemDescription = preg_split('/[\s\/\*]/', $itemDescription);
            $itemDescription = $this->cleanUp($item->getDescription());

            foreach ($itemDescription as $item1) {
                if (in_array(strtolower($item1), array_map('strtolower', $categorieDesc))
                ) {
                    $score += 1;
                }
            }

            $matchPercent = round((($score*100)/(count($itemDescription))), 0);

            if ($matchPercent > 50) {
                $item->setMatchPercentage($matchPercent);
                $results[$item->getId()] = $item;
                $score = 0;
                $special = 0;
                continue;
            }
        }

        return $results;
    }
}
