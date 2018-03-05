<?php

namespace TransactionsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use TransactionsBundle\Entity\Transactions;
use TransactionsBundle\Form\TransactionsType;

// For forms
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// use AccountBUndle\Entity\Accounts;

class TransactionsController extends Controller
{
  /**
   * This is the landing dashboard for all UserRepository
   *
   * @param Request $request
   *
   * @Route("/", name="dashboard")
   */
  public function dashAction()
  {
    /**
     * Get all Months of the Current Year and Link to the Table tha shows
     * all the transactions from that month
     */
    $em = $this->getDoctrine()->getManager();

    $data = $em
      ->getRepository('TransactionsBundle:Transactions')
      ->groupByYear();

    $matched = $em
      ->getRepository('TransactionsBundle:Transactions')
      ->getMatched();

    $years = $em
      ->getRepository('TransactionsBundle:Transactions')
      ->getYears();

    return $this->render(
      'TransactionsBundle:account:dash.html.twig',
      array(
        'data' => $data[0],
        'matched' => $matched,
        'years' => $years,
      )
    );
  }

  /**
   * Prevision of upcoming month
   *
   * @Route("/prevision", name="prevision")
   */
  public function previsionAction()
  {
    $em = $this->getDoctrine()->getManager();
    $data = $em
      ->getRepository('TransactionsBundle:Transactions')
      ->getPrevision();

    $test = [];

    foreach ($data as $transaction) {
      if (get_class($transaction) === "TransactionsBundle\Entity\Transactions") {
        $test[$transaction->getCategories()->getName()][] = $transaction;
      }
    }

    return new RedirectResponse($this->generateUrl('dashboard'));
  }

  /**
   *
   * @param int $year
   * @param int $month
   *
   * @Route("/finance/{year}/{month}", name="home")
   */
  public function indexAction($year, $month)
  {
  $year  = $year ? $year : date('Y');
  $em = $this->getDoctrine()->getManager();

  $transactions = $em->getRepository('TransactionsBundle:Transactions')
  ->findAllByMonth($month, $year);
  $graphDataType = $em->getRepository('TransactionsBundle:Transactions')
  ->getDescriptionUsage($month, $year);
  $graphDataDay = $em->getRepository('TransactionsBundle:Transactions')
  ->getDescriptionPerDayInMonth($month, $year);
  $monthsData = $em->getRepository('TransactionsBundle:Transactions')
  ->getMonthsForName($year);
  $graphMonthYear = $em->getRepository('TransactionsBundle:Transactions')
  ->graphMonthYear($year);
  $graphMonthYear2 = $em->getRepository('TransactionsBundle:Transactions')
  ->graphMonthYear($year-1);
  $income = $em->getRepository('TransactionsBundle:Transactions')
  ->getIncomeExpensiveYear($year, 1);
  $expenses = $em->getRepository('TransactionsBundle:Transactions')
  ->getIncomeExpensiveYear($year, 0);
  $monthsData = $em->getRepository('TransactionsBundle:Transactions')
  ->getMonths($year);
  $allYears = $em->getRepository('TransactionsBundle:Transactions')
  ->getAllYears();
  $descriptionData = $em->getRepository('TransactionsBundle:Transactions')
  ->getDescriptionPerMonth($month, $year);
  $amountDay = $em->getRepository('TransactionsBundle:Transactions')
  ->getAmountPerDay($month, $year);
  $spendsPerDay = $em->getRepository('TransactionsBundle:Transactions')
  ->getSpendsPerDay($month, $year);
  $monthSpents = $em->getRepository('TransactionsBundle:Transactions')
  ->getDescriptionPerMonth($month, $year);

  $numberOfDays = cal_days_in_month(CAL_GREGORIAN,$month,$year);
  $spends = $spendsPerDay[0][1]/$numberOfDays;

  $serializer = $this->get('jms_serializer');
  $graphDataType = $serializer->serialize($graphDataType, 'json');
  $graphDataDay = $serializer->serialize($graphDataDay, 'json');
  $graphAmountDay = $serializer->serialize($amountDay, 'json');
  $graphMonthYear = $serializer->serialize($graphMonthYear, 'json');
  $graphMonthYear2 = $serializer->serialize($graphMonthYear2, 'json');
  $income = $serializer->serialize($income, 'json');
  $expenses = $serializer->serialize($expenses, 'json');

  return $this->render(
  'TransactionsBundle:default:index.html.twig',
  array(
  'transactions' => $transactions,
  'monthSpents' => $monthSpents,
  'data' => $graphDataType,
  'dataDay' => $graphDataDay,
  'months' => $monthsData,
  'month' => $month,
  'descriptionData' => $descriptionData,
  'descriptionDay' => $amountDay,
  'graphDay' => $graphAmountDay,
  'month' => $month,
  'years' => $allYears,
  "year" => $year,
  "graphMonth" => $graphMonthYear,
  "graphMonth2" => $graphMonthYear2,
  "income" => $income,
  "expenses" => $expenses,
  "spends" => $spends,
  "menu" => 1,
  )
  );
  }

  /**
  * @Route("/contact", name="contact")
  */
  public function contactAction()
  {
  // $user = $this->get('security.token_storage')->getToken()->getUser();
  // $user->getId();

  return $this->render('TransactionsBundle:account:contact.html.twig');
  }

  /**
  * @Route("/transactions/show/{year}/{month}/{id}", name="show")
  *
  * @param int $year
  * @param int $id
  * @param int $month
  * @param Request $request
  */
  public function showAction($year, $month, $id)
  {
  $em = $this->getDoctrine()->getManager();
  $transaction = $em->getRepository('TransactionsBundle:Transactions')->find($id);

  return $this->render(
  'TransactionsBundle:default:show.html.twig',
  array(
  'transaction' => $transaction,
  'month' => $month,
  "year" => $year,
  )
  );
  }

  /**
   * @Route("/transactions/edit/{year}/{month}/{id}", name="edit")
   *
   * @param int $year
   * @param int $month
   * @param int $id
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function editAction($year, $month, $id, Request $request)
  {
    // Get Transaction
    $em = $this->getDoctrine()->getManager();
    $transaction = $this->get('account.account_repository')->find($id);

    // Generate Form for Edit
    $form = $this->createForm(
      TransactionsType::class,
      $transaction,
      array (
        'entity_manager' => $em
      )
    );
    $form->handleRequest($request);

    // If the form is being submitted and it is valid lets save this
    if ($form->isSubmitted() && $form->isValid()) {
      $this->get('account.account_service')->save($transaction);
      $this->addFlash('notice', 'Transaction was successfully updated.');

      return $this->redirectToRoute(
        'home',
        array(
          'year' => $year,
          'month' => $month
        ),
        301
      );
    }

    return $this->render(
      'TransactionsBundle:default:edit.html.twig',
      array(
        'transaction' => $transaction,
        'form' => $form->createView(),
        'month' => $month,
        "year" => $year,
      )
    );
  }
}
