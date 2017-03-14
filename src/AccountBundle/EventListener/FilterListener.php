<?php

namespace AccountBundle\EventListener;

use Doctrine\ORM\EntityManager;

class FilterListener
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        // $this->session = $sesison;
        // dump($this->session);
        // dump($this);
    }


    /**
     * @Observe("kernel.request", priority = -1)
     */
    public function onKernelRequest($event)
    {
        // dump($event);
        // dump($event->getRequest()->getSession()->getMetadataBag());
        // dump(get_class_methods($event));
        // dump(get_class_methods($event->getKernel()));
        // dump($event->getRequest());
        // dump(get_class_methods($event));
        // dump($this->get('account.account_repository'));
        // dump(get_class_methods($event));
        // dump(get_class_methods($event->getRequest()));
        // dump($event->getRequest()->getSession());
        // dump($event->getRequest()->getUserInfo());
        // dump($event->getRequest()->getUser());

        // dump($event->getRequest()->getSession());
        // exit;
        // dump($event->getRequest()->getSession()->getId());
        // dump($event->getRequest()->getSession()->getName());
        // dump($event->getRequest()->getSession()->getIterator());
        $filter = $this->em
            ->getFilters()
            ->enable('user_filter');
        //
        $filter->setParameter('discontinued', false);
    }
}
