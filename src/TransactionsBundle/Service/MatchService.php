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

    public function match($match, $transactions, $category)
    {
        $results = array();

        $matchDescription = $this->cleanUp($match->getDescription());

        foreach ($transactions as $item) {
            $score   = 0;
            $special = 0;
            $itemDescription = $this->cleanUp($item->getDescription());

            foreach ($itemDescription as $item1) {
                if (in_array($item1, $matchDescription)) {
                    $score += 1;
                }
            }

            $matchPercent = round((($score*100)/(count($itemDescription))), 0);

            $type = $this->transactionsRepository->findById($category);
            $type = $this->categoryRepository->findById($category);

            if ($matchPercent >= 100 && $item->getCategories() == null) {
                $item->setCategories($type[0]);
                $item->setMatchPercentage($matchPercent);

                $this->entityManager->persist($item);
                $this->entityManager->flush();

                $results[$item->getId()] = $item;

                $score = 0;
                $special = 0;

                continue;
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
        $categorieDesc = preg_split('/[\s\/\*]/', $toBeSave->getDescription());
        $categorieDesc = $this->cleanUp($toBeSave->getDescription());

        foreach ($transaction as $item) {
            if ($item->getCategories()) {
                continue;
            }

            $score = 0;
            $special = 0;

            $itemDescription = $item->getDescription();
            $itemDescription = preg_replace('!\s+!', ' ', $itemDescription);
            $itemDescription = preg_split('/[\s\/\*]/', $itemDescription);

            $itemDescription = $this->cleanUp($item->getDescription());

            foreach ($itemDescription as $item1) {
                if (in_array(strtolower($item1), array_map('strtolower', $categorieDesc))
                ) {
                    $score += 1;
                }
            }

            $matchPercent = round((($score*100)/(count($itemDescription))), 0);

            if ($matchPercent > 60) {
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
