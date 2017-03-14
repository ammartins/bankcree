<?php

namespace AccountBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class UserFilter extends SQLFilter
{
    /**
     * Gets the SQL query part to add to a query.
     *
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     *
     * @return string The constraint SQL if there is available, empty string otherwise.
    */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // dump($targetTableAlias);
        // dump($targetEntity);
        // dump($this);
        // dump(sprintf('%s.discontinued = %s', $targetTableAlias, $this->getParameter('discontinued')));
        // exit;
        return "";
        // return sprintf('%s.discontinued = %s', $targetTableAlias, $this->getParameter('discontinued'));
    }
}
