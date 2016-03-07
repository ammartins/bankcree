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
      $currentYear  = date('Y');

      $em = $this->getDoctrine()->getManager();

      $transactions   = $em->getRepository('AccountBundle:Transactions')
        ->findAllByMonth($currentMonth);
      $graphDataType  = $em->getRepository('AccountBundle:Transactions')
        ->getDescriptionUsage($currentMonth);
      $graphDataDay   = $em->getRepository('AccountBundle:Transactions')
        ->getDescriptionPerDayInMonth($currentMonth);
      $monthsData     = $em->getRepository('AccountBundle:Transactions')
        ->getMonths($currentYear);

      // serializer ... maybe should move this to Repository
      $serializer = $this->get('jms_serializer');
      $graphDataType  = $serializer->serialize($graphDataType, 'json');
      $graphDataDay  = $serializer->serialize($graphDataDay, 'json');
      //$monthsData  = $serializer->serialize($monthsData, 'json');
      // serializer ... maybe should move this to Repository

      return $this->render('AccountBundle:Default:index.html.twig',
        array(
          'transactions'  => $transactions,
          'data'          => $graphDataType,
          'dataDay'       => $graphDataDay,
          'months'        => $monthsData,
          'currentMonth'  => $currentMonth
        )
      );
    }
}
