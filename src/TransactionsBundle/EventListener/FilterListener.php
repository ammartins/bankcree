<?php

namespace TransactionsBundle\EventListener;

use Doctrine\ORM\EntityManager;

class FilterListener
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * @Observe("kernel.request", priority = -1)
     */
    public function onKernelRequest($event)
    {
        $user = $event->getRequest()
            ->getSession()
            ->getBag('attributes')
            ->get('user_id');

        if ($user) {
            $filter = $this->em
                ->getFilters()
                ->enable('user_filter');

            $filter->setParameter('userId', $user->getId());
        }
    }
}
