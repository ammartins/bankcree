<?php

namespace BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Budget
 *
 * @ORM\Table(name="budget")
 * @ORM\Entity(repositoryClass="BudgetBundle\Repository\BudgetRepository")
 */
class Budget
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
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="CategoriesBundle\Entity\Categories", mappedBy="name")
     */
    private $categories;

    /**
     * @var int
     *
     * @ORM\Column(name="budgetLimit", type="integer")
     */
    private $budgetLimit;

    public function __construct()
    {
        $this->categoriess = new ArrayCollection();
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
     * @return Budget
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
     * Set Categoriess
     *
     * @param array $categories
     *
     * @return Budget
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get Categoriess
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set budgetLimit
     *
     * @param integer $budgetLimit
     *
     * @return Budget
     */
    public function setBudgetLimit($budgetLimit)
    {
        $this->budgetLimit = $budgetLimit;

        return $this;
    }

    /**
     * Get budgetLimit
     *
     * @return int
     */
    public function getBudgetLimit()
    {
        return $this->budgetLimit;
    }
}
