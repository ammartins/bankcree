<?php

namespace AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="AccountBundle\Repository\TransactionsRepository")
 */
class Transactions
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="transaction_hash", type="string")
     */
    private $transactionHash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_at", type="datetime")
     */
    private $createAt;

    /**
     * @var string
     *
     * @ORM\Column(name="startsaldo", type="float")
     */
    private $startsaldo;

    /**
     * @var string
     *
     * @ORM\Column(name="endsaldo", type="float")
     */
    private $endsaldo;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="string", length=255)
     */
    private $shortDescription;

    /**
     * @var int
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @ORM\ManyToOne(targetEntity="TransactionType")
     * @ORM\JoinColumn(name="transaction_type", referencedColumnName="id")
     */
    private $transactionType;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transactionHash
     *
     * @param integer $transactionHash
     * @return Transactions
     */
    public function setTransactionHash($transactionHash)
    {
        $this->transactionHash = $transactionHash;

        return $this;
    }

    /**
     * Get transactionHash
     *
     * @return integer
     */
    public function getTransactionHash()
    {
        return $this->transactionHash;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return Transactions
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set startsaldo
     *
     * @param string $startsaldo
     * @return Transactions
     */
    public function setStartsaldo($startsaldo)
    {
        $this->startsaldo = $startsaldo;

        return $this;
    }

    /**
     * Get startsaldo
     *
     * @return string
     */
    public function getStartsaldo()
    {
        return $this->startsaldo;
    }

    /**
     * Set endsaldo
     *
     * @param string $endsaldo
     * @return Transactions
     */
    public function setEndsaldo($endsaldo)
    {
        $this->endsaldo = $endsaldo;

        return $this;
    }

    /**
     * Get endsaldo
     *
     * @return string
     */
    public function getEndsaldo()
    {
        return $this->endsaldo;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return Transactions
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Transactions
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Transactions
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     * @return Transactions
     */
    public function setaccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return integer
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set transactionType
     *
     * @param integer $transactionType
     * @return Transactions
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * Get transactionType
     *
     * @return integer
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }
}
