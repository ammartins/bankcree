<?php

namespace BudgetBundle\Repository;

use CategoriesBundle\Entity\Categories;
use TransactionsBundle\Entity\Transactions;

/**
 * BudgetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BudgetRepository extends \Doctrine\ORM\EntityRepository
{
    public function findBudgets()
    {
        $budgets = $this->getEntityManager()
            ->createQuery(
                "SELECT tt.name, sum(t.amount) as amount, IDENTITY(tt.parent) as parent
                FROM TransactionsBundle:Transactions t 
                JOIN CategoriesBundle:Categories tt
                WHERE t.categories IN
                (
                    SELECT tt.id
                    FROM CategoriesBundle:Categories cc
                    JOIN BudgetBundle:Budget b WITH cc.name = b.name
                )
                AND YEAR(t.createAt) = ".date("Y")."
                AND MONTH(t.createAt) = ".date("m")."
                AND tt.id = t.categories
                GROUP BY tt.name"
            )->execute();

        return $budgets;
    }
}
