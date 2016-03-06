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
        $graphDataType = $em->getRepository('AccountBundle:Transactions')
            ->getDescriptionUsage($currentMonth);
        $graphDataDay  = $em->getRepository('AccountBundle:Transactions')
            ->getDescriptionPerDayInMonth($currentMonth);

        // serializer ... maybe should move this to Repository
        $serializer = $this->get('jms_serializer');
        $graphDataType  = $serializer->serialize($graphDataType, 'json');

        $serializer = $this->get('jms_serializer');
        $graphDataDay  = $serializer->serialize($graphDataDay, 'json');
        // serializer ... maybe should move this to Repository

        return $this->render('AccountBundle:Default:index.html.twig',
          array(
            'transactions' => $transactions,
            'data'    => $graphDataType,
            'dataDay' => $graphDataDay
          )
        );
    }
}
