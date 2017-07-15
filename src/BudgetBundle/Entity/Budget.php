<?php

namespace BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Budget
 *
 * @ORM\Table(name="budget")
 * @ORM\Entity(repositoryClass="AccountBundle\Repository\BudgetRepository")
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
     * @var array
     *
     * @ORM\OneToMany(targetEntity="CategoriesBundle\Entity\Categories", mappedBy="name")
     */
    private $Categoriess;

    /**
     * @var int
     *
     * @ORM\Column(name="goal", type="integer")
     */
    private $goal;

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
     * Set Categoriess
     *
     * @param array $Categoriess
     *
     * @return Budget
     */
    public function setCategoriess($Categoriess)
    {
        $this->Categoriess = $Categoriess;

        return $this;
    }

    /**
     * Get Categoriess
     *
     * @return array
     */
    public function getCategoriess()
    {
        return $this->Categoriess;
    }

    /**
     * Set goal
     *
     * @param integer $goal
     *
     * @return Budget
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * Get goal
     *
     * @return int
     */
    public function getGoal()
    {
        return $this->goal;
    }
}
