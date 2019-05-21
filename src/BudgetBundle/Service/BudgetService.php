<?php

namespace BudgetBundle\Service;

use TransactionsBundle\Repository\TransactionsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\ORM\EntityManager;
use BudgetBundle\Entity\Budget;
use Symfony\Component\Finder\Finder;

use TransactionsBundle\Entity\Transactions;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BudgetService
{
    protected $entityManager;

    public function __construct(
        EntityManager $entityManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function getBudgets($year, $month)
    {
        if (!$year && !$month) {
            $year = date('Y');
            $month = date('m');
        }
        $budgets = $this->em->getRepository('BudgetBundle:Budget')->findAll();
        $budgetStatus = $this->em->getRepository('BudgetBundle:Budget')->findBudgets($year, $month);
        $budgetStatusYear = $this->em->getRepository('BudgetBundle:Budget')->findBudgetPerYear($year);

        $monthBudgets = $this->getMonthlyBudgets($budgets, $budgetStatus);
        $annualBudgets = $this->getAnnualyBudgets($budgets, $budgetStatusYear);

        return [$monthBudgets, $annualBudgets];
    }

    public function getMonthlyBudgets($budgets, $budgetStatus)
    {
        $result = [];
        foreach ($budgets as $budget) {
            if ($budget->getAnnually()) {
                continue;
            }
            $result[$budget->getName()] = [
                "name" => $budget->getName(),
                "limit" => $budget->getBudgetLimit(),
                "amount" => 0,
                "annually" => $budget->getAnnually(),
                "id" => $budget->getId()
            ];
            foreach ($budgetStatus as $budgetst) {
                if ($budgetst["name"] === $budget->getName()) {
                    $result[$budget->getName()]["amount"] += $budgetst["amount"]*-1;
                    continue;
                }
                if ($budgetst['parent']) {
                    $parentCategory = $this
                        ->em
                        ->getRepository('CategoriesBundle:Categories')
                        ->findById($budgetst['parent']);
                    if ($parentCategory[0]->getName() === $budget->getName()) {
                        $result[$parentCategory[0]->getName()]["amount"] += $budgetst["amount"]*-1;
                    }
                }
            }
        }

        return $result;
    }

    public function getAnnualyBudgets($budgets, $budgetStatusYear)
    {
        $result = [];
        foreach ($budgets as $budget) {
            if (!$budget->getAnnually()) {
                continue;
            }
            $result[$budget->getName()] = [
                "name" => $budget->getName(),
                "limit" => $budget->getBudgetLimit(),
                "amount" => 0,
                "annually" => $budget->getAnnually(),
                "id" => $budget->getId()
            ];
            foreach ($budgetStatusYear as $budgetst) {
                if ($budgetst["name"] === $budget->getName()) {
                    $result[$budget->getName()]["amount"] += $budgetst["amount"]*-1;
                    continue;
                }
                if ($budgetst['parent']) {
                    $parentCategory = $this
                        ->em
                        ->getRepository('CategoriesBundle:Categories')
                        ->findById($budgetst['parent']);
                    if ($parentCategory[0]->getName() === $budget->getName()) {
                        $result[$parentCategory[0]->getName()]["amount"] += $budgetst["amount"]*-1;
                    }
                }
            }
        }

        return $result;
    }
}
