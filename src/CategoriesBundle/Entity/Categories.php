<?php

namespace CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categories
 *
 * @ORM\Table(name="transaction_type")
 * @ORM\Entity(repositoryClass="CategoriesBundle\Repository\CategoriesRepository")
 */
class Categories
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="Categoriess")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recurring", type="boolean")
     */
    private $recurring;

    /**
     * @var boolean
     *
     * @ORM\Column(name="discard", type="boolean", nullable=true)
     */
    private $discard;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Categories
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     *
     * @return Categories
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * Get accountId
     *
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Get recurring
     *
     * @return boolean
     */
    public function getRecurring()
    {
        return $this->recurring;
    }

    /**
     * Set recurring
     *
     * @param boolean $recurring
     * @return Transactions
     */
    public function setRecurring($recurring)
    {
        $this->recurring = $recurring;
        return $this;
    }

    /**
     * Get discard
     *
     * @return boolean
     */
    public function getDiscard()
    {
        return $this->discard;
    }

    /**
     * Set discard
     *
     * @param boolean $discard
     * @return Transactions
     */
    public function setDiscard($discard)
    {
        $this->discard = $discard;
        return $this;
    }
}
