<?php

namespace AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

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

    /**
     * @Route("/graph")
     */
    public function graphAction()
    {
        $em = $this->getDoctrine()->getManager();

        $currentMonth = date('m');
        $graphData = $em->getRepository('AccountBundle:Transactions')->getDescriptionUsage($currentMonth);

        $serializer = $this->get('jms_serializer');
        $graphData  = $serializer->serialize($graphData, 'json');

        return $this->render('AccountBundle:Default:graph.html.twig',
            array('data' => $graphData)
        );
    }
}
