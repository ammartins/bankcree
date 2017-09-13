<?php

namespace AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AccountController extends Controller
{
    /**
     * @Route("/account")
     */
    public function indexAction()
    {
        return $this->render('AccountBundle:Default:index.html.twig');
    }
}
