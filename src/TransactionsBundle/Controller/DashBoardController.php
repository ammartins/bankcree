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
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($user->getBankName() === "") {
            $this->addFlash('error', 'Please Set Your Bank in Profile Page.');
        }

        // Current Month Transactions
        $currentTransactions = $em->getRepository('TransactionsBundle:Transactions')->findAllByMonthYear($month, $year);
        // dump($currentTransactions);
        // exit;

        // In fone redirect for the Last Year/Month Available
        if (!count($currentTransactions)) {
            // Redirect to the Latest Month and Year available
            $lastTransaction = $em->getRepository('TransactionsBundle:Transactions')->findLastOne();
            $date = $lastTransaction[0]->getCreateAt();

            return $this->redirectToRoute('main_dashboard',array('year' => $date->format('Y'),'month' => $date->format('m')));
        }

        // Get Data for Pie Chart Group By Category
        $graphDataType = $em->getRepository('TransactionsBundle:Transactions')->getDescriptionUsage($month, $year);
        $graphDataType = $serializer->serialize($graphDataType, 'json');

        // Get Data For Daily Graph
        $amountDay = $em->getRepository('TransactionsBundle:Transactions')->getAmountPerDay($month, $year);
        $amountDay = $serializer->serialize($amountDay, 'json');

        // Get Months of the current Year in display
        $monthsData = $em->getRepository('TransactionsBundle:Transactions')->getMonths($year);

        // Get all years in place
        $allYears = $em->getRepository('TransactionsBundle:Transactions')->getAllYears();

        // Get Average Recurring payments and show them in a map of predictions
        $recurringAvg = $em->getRepository('TransactionsBundle:Transactions')->getAveragePayments();
        $recurringAvg = $serializer->serialize($recurringAvg, 'json');

        // Get Amount spent per day
        $amountPerDay = $em->getRepository('TransactionsBundle:Transactions')->getDailySpent($month, $year);
        $amountPerDay = $serializer->serialize($amountPerDay, 'json');

        // Get Last Month expenses per day
        $previousMonth = $em->getRepository('TransactionsBundle:Transactions')->getDailySpent($month-1, $year);
        $previousMonth = $serializer->serialize($previousMonth, 'json');        

        // Current transactions group by day and category type
        $transactionsDay = $em->getRepository('TransactionsBundle:Transactions')->findAllGroupByDay($month, $year);


        $profits = $expenses = 0;
        $ignoreSavings = $user->getIgnoreSavings();

        foreach ($currentTransactions as $transaction) {
            if (
                $transaction->getCategories() and
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

        foreach ($transactionsDay as $key => $tday) {
            $trans = $em->getRepository('TransactionsBundle:Transactions')->findById($tday['id']);
            if ($trans[0]->getCategories()) {
                $transactionsDay[$key]['category'] = $trans[0]->getCategories()->getName();
                $transactionsDay[$key]['savings'] = $trans[0]->getCategories()->getSavings();
                continue;
            }
            $transactionsDay[$key]['id'] = "";
        }
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
                'graphDay' => $amountDay,
                'graphMonth' => $transactionsDay,
                'recurringAvg' => $recurringAvg,
                'amountPerDay' => $amountPerDay,
                'previousMonth' => $previousMonth,
            )
        );
    }
}
