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
     * @ORM\OneToMany(targetEntity="Categories", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Categories", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\Column(name="is_parent", type="boolean", nullable=true)
     */
    private $isParent;

    /**
     * @ORM\Column(name="company_logo", type="string", nullable=true, length=255)
     */
    private $companyLogo;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getIsParent()
    {
        return $this->isParent;
    }

    public function setIsParent($isParent)
    {
        $this->isParent = $isParent;

        return $this;
    }

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

    public function getCompanyLogo()
    {
        return $this->companyLogo;
    }

    public function setCompanyLogo($companyLogo)
    {
        $this->company_logo = $companyLogo;
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
