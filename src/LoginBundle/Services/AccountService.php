<?php

namespace LoginBundle\Service;

use TransactionsBundle\Entity\Transactions;
use TransactionsBundle\Repository\TransactionsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AccountService implements AccountServiceInterface
{
    /**
     * @var \TransactionsBundle\Repository\TransactionsRepository
     */
    protected $transactionRepo;

    /**
     * @param \TransactionsBundle\Repository\TransactionsRepository $transactionRepo
     */
    public function __construct(
        PromoRepositoryInterface $transactionRepo
    ) {
        $this->transactionRepo = $transactionRepo;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $transaction = $this->transactionRepo->find($id);
        return $transaction;
    }

    /**
     * @inheritdoc
     */
    public function getAll($updatedSince = null)
    {
        return is_null($updatedSince)
        ? $this->transactionRepo->findAll()
        : $this->transactionRepo->findUpdatedSince($updatedSince);
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
        $transaction = $this->transactionRepo->persist($transaction);
        return $transaction;
    }
}
