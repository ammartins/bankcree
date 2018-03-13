<?php

namespace TransactionsBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class UserFilter extends SQLFilter
{
    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string
     */
    public function addFilterConstraint(
        ClassMetadata $targetEntity,
        $targetTableAlias
    ) {
        if ($this->getParameter('userId')) {
            return sprintf(
                '%s.account_id = %s',
                $targetTableAlias,
                $this->getParameter('userId')
            );
        }
    }
}
