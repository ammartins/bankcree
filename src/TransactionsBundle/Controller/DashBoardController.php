<?php

namespace TransactionsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DashBoardController extends Controller
{
    /**
     * @Route("/main/{year}/{month}", name="main_dashboard")
     */
    public function mainDashAction($year, $month)
    {
        // Entity Manager
        $em = $this->getDoctrine()->getManager();
        // Serializer
        $serializer = $this->get('jms_serializer');
        // Get Loggedin user
        $user = $this->get('security.context')->getToken()->getUser();
        if ($user->getBankName() === "") {
            $this->addFlash('error', 'Please Set Your Bank in Profile Page.');
        }

        // Current Month Transactions
        $currentTransactions = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->findAllByMonthYear($month, $year);

        if (!count($currentTransactions)) {
            // Redirect to the Latest Month and Year available
            $lastTransaction = $em
                ->getRepository('TransactionsBundle:Transactions')
                ->findLastOne();
            $date = $lastTransaction[0]->getCreateAt();

            return $this
               ->redirectToRoute(
                   'main_dashboard',
                   array(
                       'year' => $date->format('Y'),
                       'month' => $date->format('m'),
                   )
               );
        }

        // Get Data for Pie Chart Group By Category
        $graphDataType = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->getDescriptionUsage($month, $year);

        // Get Data For Daily Graph
        $amountDay = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->getAmountPerDay($month, $year);
        $graphAmountDay = $serializer->serialize($amountDay, 'json');

        $monthsData = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->getMonths($year);
        $allYears = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->getAllYears();

        $graphDataType = $serializer->serialize($graphDataType, 'json');

        $profits = $expenses = 0;
        $ignoreSavings = $user->getIgnoreSavings();
        foreach ($currentTransactions as $transaction) {
            if ($transaction->getCategories() and
                $transaction->getCategories()->getSavings() and
                $ignoreSavings
            ) {
                continue;
            }
            if ($transaction->getAmount() > 0) {
                $profits += $transaction->getAmount();
                continue;
            }
            $expenses += $transaction->getAmount();
        }

        // Current transactions group by day and category type
        $transactionsDay = $em
            ->getRepository('TransactionsBundle:Transactions')
            ->finAllGroupDay($month, $year);

        $transactionsDay = $serializer->serialize($transactionsDay, 'json');

        return $this->render(
            'TransactionsBundle:main:dash.html.twig',
            array(
                'data' => array(
                        'profits' => $profits,
                        'expenses' => $expenses,
                        'pieChart' => $graphDataType,
                    ),
                'transactions' => $currentTransactions,
                'months' => $monthsData,
                'years' => $allYears,
                'month' => $month,
                'year' => $year,
                'dataJson' => $serializer->serialize($currentTransactions, 'json'),
                'graphDay' => $graphAmountDay,
                'graphMonth' => $transactionsDay,
            )
        );
    }
}
