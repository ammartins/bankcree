<?php

namespace AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use AccountBundle\Entity\Transactions;
use AccountBundle\Form\TransactionsType;
// For forms
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TransactionsController extends Controller
{
  /**
   *
   * @param int $currentYear
   * @param int $currentMonth
   * @param Request $request
   *
   * @Route("/{currentYear}/{currentMonth}", name="home")
   */
  public function indexAction($currentYear, $currentMonth, Request $request)
  {

    # TODO FFS CLEAN THIS MESS
    //$currentMonth = $request->query->get('currentMonth')
    //? str_replace('#', '', $request->query->get('currentMonth')) : date('m');
    $currentYear  = $currentYear ? $currentYear : date('Y');

    $em = $this->getDoctrine()->getManager();

    $transactions     = $em->getRepository('AccountBundle:Transactions')
    ->findAllByMonth($currentMonth, $currentYear);
    $graphDataType    = $em->getRepository('AccountBundle:Transactions')
    ->getDescriptionUsage($currentMonth, $currentYear);
    $graphDataDay     = $em->getRepository('AccountBundle:Transactions')
    ->getDescriptionPerDayInMonth($currentMonth, $currentYear);
    $monthsData       = $em->getRepository('AccountBundle:Transactions')
    ->getMonthsForName($currentYear);
    $graphMonthYear   = $em->getRepository('AccountBundle:Transactions')
    ->graphMonthYear($currentYear);
    $graphMonthYear2  = $em->getRepository('AccountBundle:Transactions')
    ->graphMonthYear($currentYear-1);
    $monthsData       = $em->getRepository('AccountBundle:Transactions')
    ->getMonths($currentYear);
    $allYears         = $em->getRepository('AccountBundle:Transactions')
    ->getAllYears();
    $descriptionData  = $em->getRepository('AccountBundle:Transactions')
    ->getDescriptionPerMonth($currentMonth, $currentYear);
    $amountDay        = $em->getRepository('AccountBundle:Transactions')
    ->getAmountPerDay($currentMonth, $currentYear);
    $transactionType  = $em->getRepository('AccountBundle:TransactionType')
    ->findAll();

    // serializer ... maybe should move this to Repository
    $serializer           = $this->get('jms_serializer');
    $graphDataType        = $serializer->serialize($graphDataType, 'json');
    $graphDataDay         = $serializer->serialize($graphDataDay, 'json');
    $graphAmountDay       = $serializer->serialize($amountDay, 'json');
    $graphMonthYear       = $serializer->serialize($graphMonthYear, 'json');
    $graphMonthYear2      = $serializer->serialize($graphMonthYear2, 'json');

    //$dataTransactionType  = $serializer->serialize($transactionType, 'json');
    //$descriptionData  = $serializer->serialize($descriptionData, 'json');
    // serializer ... maybe should move this to Repository

    return $this->render('AccountBundle:Default:index.html.twig',
      array(
        'transactions'            => $transactions,
        'data'                    => $graphDataType,
        'dataDay'                 => $graphDataDay,
        'months'                  => $monthsData,
        'currentMonth'            => $currentMonth,
        'descriptionData'         => $descriptionData,
        'descriptionDay'          => $amountDay,
        'graphDay'                => $graphAmountDay,
        'dataTransactionType'     => $transactionType,
        'currentMonth'            => $currentMonth,
        'years'                   => $allYears,
        "currentYear"             => $currentYear,
        //"getDescriptionUsageYear" => $getDescriptionUsageYear,
        "graphMonth"              => $graphMonthYear,
        "graphMonth2"             => $graphMonthYear2,
      )
    );
  }

  /**
   * @Route("/show/{currentYear}/{currentMonth}/{id}", name="show")
   *
   * @param int $currentYear
   * @param int $id
   * @param int $currentMonth
   * @param Request $request
   */
  public function showAction($currentYear, $currentMonth, $id, Request $request)
  {
    $em           = $this->getDoctrine()->getManager();
    $transaction  = $em->getRepository('AccountBundle:Transactions')->find($id);

    return $this->render('AccountBundle:Default:show.html.twig',
      array(
        'transaction'   => $transaction,
        'currentMonth'  => $currentMonth,
        "currentYear"   => $currentYear,
      )
    );
  }

  /**
   * @Route("/edit/{currentYear}/{currentMonth}/{id}", name="edit")
   *
   * @param int $currentYear
   * @param int $currentMonth
   * @param int $id
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function editAction($currentYear, $currentMonth, $id, Request $request)
  {
    // Get Transaction
    $transaction  = $this->get('account.account_repository')->find($id);
    // Generate Form for Edit
    $form         = $this->createForm(TransactionsType::class, $transaction);
    $form->handleRequest($request);

    // If the form is being submitted and it is valid lets save this
    if ($form->isSubmitted() && $form->isValid())
    {
        $this->get('account.account_service')->save($transaction);
        $this->addFlash('notice', 'Transaction was successfully updated.');

        return $this->redirectToRoute('home',
          array(
            'currentYear'   => $currentYear,
            'currentMonth' => $currentMonth
          ),301);
    }

    return $this->render('AccountBundle:Default:edit.html.twig',
      array(
        'transaction'   => $transaction,
        'form'          => $form->createView(),
        'currentMonth'  => $currentMonth,
        "currentYear"   => $currentYear,
      )
    );
  }
}
