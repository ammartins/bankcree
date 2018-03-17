<?php

namespace TransactionsBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use ImporterBundle\Entity\Import;

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
        if (!$targetEntity->getReflectionClass()->name == 'ImporterBundle\Entity\Imported') {
            if ($this->getParameter('userId')) {
                return sprintf(
                    '%s.account_id = %s',
                    $targetTableAlias,
                    $this->getParameter('userId')
                );
            }
        }

        return "";
    }
}
