<?php

namespace TransactionsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use TransactionsBundle\Entity\Transactions;
use TransactionsBundle\Form\TransactionsType;

// For forms
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        $data = $em->getRepository('TransactionsBundle:Transactions')->groupByYear();

        return $this->render(
            'TransactionsBundle:account:dash.html.twig',
            array(
                'data' => $data,
            )
        );
    }

    /**
     *
     * @param int $currentYear
     * @param int $currentMonth
     *
     * @Route("/transactions/{currentYear}/{currentMonth}", name="home")
     */
    public function indexAction($currentYear, $currentMonth)
    {
        $currentYear  = $currentYear ? $currentYear : date('Y');
        $em = $this->getDoctrine()->getManager();

        $transactions = $em->getRepository('TransactionsBundle:Transactions')
            ->findAllByMonth($currentMonth, $currentYear);
        $graphDataType = $em->getRepository('TransactionsBundle:Transactions')
            ->getDescriptionUsage($currentMonth, $currentYear);
        $graphDataDay = $em->getRepository('TransactionsBundle:Transactions')
            ->getDescriptionPerDayInMonth($currentMonth, $currentYear);
        $monthsData = $em->getRepository('TransactionsBundle:Transactions')
            ->getMonthsForName($currentYear);
        $graphMonthYear = $em->getRepository('TransactionsBundle:Transactions')
            ->graphMonthYear($currentYear);
        $graphMonthYear2 = $em->getRepository('TransactionsBundle:Transactions')
            ->graphMonthYear($currentYear-1);
        $income = $em->getRepository('TransactionsBundle:Transactions')
            ->getIncomeExpensiveYear($currentYear, 1);
        $expenses = $em->getRepository('TransactionsBundle:Transactions')
            ->getIncomeExpensiveYear($currentYear, 0);
        $monthsData = $em->getRepository('TransactionsBundle:Transactions')
            ->getMonths($currentYear);
        $allYears = $em->getRepository('TransactionsBundle:Transactions')
            ->getAllYears();
        $descriptionData = $em->getRepository('TransactionsBundle:Transactions')
            ->getDescriptionPerMonth($currentMonth, $currentYear);
        $amountDay = $em->getRepository('TransactionsBundle:Transactions')
            ->getAmountPerDay($currentMonth, $currentYear);
        $spendsPerDay = $em->getRepository('TransactionsBundle:Transactions')
            ->getSpendsPerDay($currentMonth, $currentYear);

        $numberOfDays = cal_days_in_month(
            CAL_GREGORIAN,
            $currentMonth,
            $currentYear
        );
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
                'data' => $graphDataType,
                'dataDay' => $graphDataDay,
                'months' => $monthsData,
                'currentMonth' => $currentMonth,
                'descriptionData' => $descriptionData,
                'descriptionDay' => $amountDay,
                'graphDay' => $graphAmountDay,
                'currentMonth' => $currentMonth,
                'years' => $allYears,
                "currentYear" => $currentYear,
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
    * @Route("/transactions/show/{currentYear}/{currentMonth}/{id}", name="show")
    *
    * @param int $currentYear
    * @param int $id
    * @param int $currentMonth
    * @param Request $request
    */
    public function showAction($currentYear, $currentMonth, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $transaction = $em->getRepository('TransactionsBundle:Transactions')->find($id);

        return $this->render(
            'TransactionsBundle:default:show.html.twig',
            array(
                'transaction' => $transaction,
                'currentMonth' => $currentMonth,
                "currentYear" => $currentYear,
            )
        );
    }

    /**
    * @Route("/transactions/edit/{currentYear}/{currentMonth}/{id}", name="edit")
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
                    'currentYear' => $currentYear,
                    'currentMonth' => $currentMonth
                ),
                301
            );
        }

        return $this->render(
            'TransactionsBundle:default:edit.html.twig',
            array(
                'transaction' => $transaction,
                'form' => $form->createView(),
                'currentMonth' => $currentMonth,
                "currentYear" => $currentYear,
            )
        );
    }
}
