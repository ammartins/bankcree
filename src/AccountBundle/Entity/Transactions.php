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
     * @ORM\Column(name="transaction_id", type="integer")
     */
    private $transactionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_at", type="datetime")
     */
    private $createAt;

    /**
     * @var string
     *
     * @ORM\Column(name="startsaldo", type="decimal", precision=3, scale=0)
     */
    private $startsaldo;

    /**
     * @var string
     *
     * @ORM\Column(name="endsaldo", type="decimal", precision=3, scale=0)
     */
    private $endsaldo;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=3, scale=0)
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transactionId
     *
     * @param integer $transactionId
     * @return Transactions
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return integer 
     */
    public function getTransactionId()
    {
        return $this->transactionId;
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
}
