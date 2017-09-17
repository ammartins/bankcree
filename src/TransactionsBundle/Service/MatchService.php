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

    public function match($match, $transaction, $category, $output)
    {
        // dump('Matching '.count($transaction));
        $results = array();

        $matchDescription = $match->getDescription();

        $matchDescription = preg_replace(
            '/(\d{1,2}[.\/])+\d{1,2}/',
            '',
            $matchDescription
        );

        $matchDescription = preg_replace(
            '/\w+:[A-Z0-9]+/',
            '',
            $matchDescription
        );

        $matchDescription = array_filter(preg_split(
            '/[\s\/\*]/',
            $matchDescription
        ));

        foreach ($transaction as $item) {
            $score   = 0;
            $special = 0;

            $itemDescription = preg_replace(
                '/(\d{1,2}[.\/])+\d{1,2}/',
                '',
                $item->getDescription()
            );

            $itemDescription = preg_replace(
                '/\w+:[A-Z0-9]+/',
                '',
                $itemDescription
            );

            $itemDescription = preg_split(
                '/[\s\/\*]/',
                preg_replace(
                    '!\s+!',
                    ' ',
                    $itemDescription
                )
            );

            foreach ($itemDescription as $item1) {
                if ($item1 == '') {
                    continue;
                }
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
                    // $special += 1;
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

            $type = $this->transactionsRepository->findById($category);
            $type = $this->categoryRepository->findById($category);

            if ($matchPercent >= 100 && $item->getCategories() == NULL) {
                $item->setCategories($type[0]);

                $this->entityManager->persist($item);
                $this->entityManager->flush();

                $results[$item->getId()] = $item;
                $score = 0;
                $special = 0;

                continue;
            }

            if (
                $matchPercent > 70
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
                $item->setMatchPercentage($matchPercent);
                $this->entityManager->persist($item);
                $this->entityManager->flush();

                continue;
            }
        }

        return $results;
    }
}
