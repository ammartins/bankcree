<?php

namespace TransactionsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FilterListener
{
    public function __construct(
        EntityManager $em,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Observe("kernel.request", priority = -1)
     */
    public function onKernelRequest()
    {
        if ($user = $this->getUser()) {
            if ($user == "anon.") {
                return "";
            }
            $filter = $this->em->getFilters()->enable('user_filter');
            $filter->setParameter('userId', $user->getId());
        }
    }

    private function getUser()
    {
        $token = $this->tokenStorage->getToken();
        if ($token) {
            $user = $token->getUser();
            return $user;
        }

        return null;
    }
}
