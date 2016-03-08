<?php

namespace AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class DefaultController extends Controller
{
  /**
   * @Route("/")
   */
   public function indexAction(Request $request)
   {
      $currentMonth = $request->query->get('currentMonth') ? str_replace('#', '', $request->query->get('currentMonth')) : date('m');
      $currentYear  = date('Y');

      $em = $this->getDoctrine()->getManager();

      $transactions     = $em->getRepository('AccountBundle:Transactions')
        ->findAllByMonth($currentMonth);
      $graphDataType    = $em->getRepository('AccountBundle:Transactions')
        ->getDescriptionUsage($currentMonth);
      $graphDataDay     = $em->getRepository('AccountBundle:Transactions')
        ->getDescriptionPerDayInMonth($currentMonth);
      $monthsData       = $em->getRepository('AccountBundle:Transactions')
        ->getMonths($currentYear);
      $descriptionData  = $em->getRepository('AccountBundle:Transactions')
        ->getDescriptionPerMonth($currentMonth, $currentYear);

      // serializer ... maybe should move this to Repository
      $serializer       = $this->get('jms_serializer');
      $graphDataType    = $serializer->serialize($graphDataType, 'json');
      $graphDataDay     = $serializer->serialize($graphDataDay, 'json');
      //$descriptionData  = $serializer->serialize($descriptionData, 'json');
      // serializer ... maybe should move this to Repository

      return $this->render('AccountBundle:Default:index.html.twig',
        array(
          'transactions'      => $transactions,
          'data'              => $graphDataType,
          'dataDay'           => $graphDataDay,
          'months'            => $monthsData,
          'currentMonth'      => $currentMonth,
          'descriptionData'   => $descriptionData
        )
      );
    }
}
