<?php

namespace ImporterBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class DoctrineExceptionListener
{
    public function onPdoException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        // dump($exception->getTrace());
        // die;

        if ($exception instanceof PDOException) {
            die('By My Hand');
        }
    }
}
