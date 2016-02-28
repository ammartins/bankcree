<?php

namespace AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $currentMonth = date('m');

        $em = $this->getDoctrine()->getManager();

        $transactions = $em->getRepository('AccountBundle:Transactions')
            ->findAllByMonth($currentMonth);

        return $this->render('AccountBundle:Default:index.html.twig',
          array('transactions' => $transactions)
        );
    }
}
