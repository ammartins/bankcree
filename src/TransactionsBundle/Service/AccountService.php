<?php

namespace TransactionsBundle\Service;

use TransactionsBundle\Entity\Transactions;
use TransactionsBundle\Repository\TransactionsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AccountService implements AccountServiceInterface
{
  /**
   * @var \\Repository\TransactionsRepository
   */
  protected $transactionsRepository;

  /**
   * @param \\Repository\TransactionsRepository $transactionsRepository
   */
  public function __construct(
    PromoRepositoryInterface $transactionsRepository
  ) {
    $this->transactionsRepository = $transactionsRepository;
  }

  /**
   * @inheritdoc
   */
  public function get($id)
  {
    $transaction = $this->transactionsRepository->find($id);
    return $transaction;
  }

  /**
   * @inheritdoc
   */
  public function getAll($updatedSinceTimestamp = null)
  {
    return is_null($updatedSinceTimestamp)
    ? $this->transactionsRepository->findAll()
    : $this->transactionsRepository->findUpdatedSince($updatedSinceTimestamp);
  }

  /**
   * @inheritdoc
   */
  public function setUsed($id)
  {
    $promo = $this->get($id);
    $promo->setIsUsed(true);
    return $this->save($promo);
  }

  /**
   * @inheritdoc
   */
  public function save(Transactions $transaction)
  {
    $transaction = $this->transactionsRepository->persist($transaction);
    return $transaction;
  }
}
