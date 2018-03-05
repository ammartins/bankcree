<?php

namespace TransactionsBundle\Service;

use TransactionsBundle\Entity\Transactions;
use CategoriesBundle\Entity\Categories;
use CategoriesBundle\Repository\CategoriesRepository;
use TransactionsBundle\Repository\TransactionsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;

class HelperService
{
  /**
   * @var \TransactionsBundle\Repository\TransactionsRepository
   */
  protected $transRepo;
  protected $categoryRepository;
  protected $entityManager;

  public function __construct(
    TransactionsRepository $transRepo,
    CategoriesRepository $categoryRepository,
    EntityManager $entityManager
  ) {
    $this->transactionsRepository = $transRepo;
    $this->categoryRepository = $categoryRepository;
    $this->entityManager = $entityManager;
  }

  public function calculateSavings($data)
  {
    foreach ($data[0] as $key => $account) {
        foreach ($data[1] as $savings) {
          if ($account['year'] == $savings['year'] and $account['month'] == $savings['month']) {
            $data[0][$key][2] -= $savings[2];
          }
        }
    }
    return $data[0];
  }

}
