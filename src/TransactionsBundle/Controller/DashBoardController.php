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

        // In no data, redirect for the Last Year/Month Available
        if (!count($currentTransactions)) {
            // Redirect to the Latest Month and Year available
            $lastTransaction = $em->getRepository('TransactionsBundle:Transactions')->findLastOne();
            if (!count($lastTransaction)) {
                return $this
                    ->redirectToRoute('profile');
            }
            $date = $lastTransaction[0]->getCreateAt();

            return $this
            ->redirectToRoute('main_dashboard', array('year' => $date->format('Y'),'month' => $date->format('m')));
        }

        // Get Data for Pie Chart Group By Category
        $graphDataType = $em->getRepository('TransactionsBundle:Transactions')->getDescriptionUsage($month, $year);
        $graphDataTypeP = $em->getRepository('TransactionsBundle:Transactions')->getDescriptionUsage($month-1, $year);
        
        $groupExpenses = $graphDataType;
        $parents = [];
        foreach ($groupExpenses as $cat) {
            if ($cat['total'] > 0) {
                continue;
            }
            if ($cat[0]->getParent()) {
                if (!array_key_exists($cat[0]->getParent()->getName(), $parents)) {
                    $parents[$cat[0]->getParent()->getName()] = $cat['total'];
                    continue;
                }
                $parents[$cat[0]->getParent()->getName()] += $cat['total'];
                continue;
            }
            if (!array_key_exists($cat[0]->getName(), $parents)) {
                $parents[$cat[0]->getName()] = $cat['total'];
                continue;
            }
            $parents[$cat[0]->getName()] += $cat['total'];
            continue;
        }

        $groupExpenses = $graphDataTypeP;
        $parentsP = [];
        foreach ($groupExpenses as $cat) {
            if ($cat['total'] > 0) {
                continue;
            }
            if ($cat[0]->getParent()) {
                if (!array_key_exists($cat[0]->getParent()->getName(), $parentsP)) {
                    $parentsP[$cat[0]->getParent()->getName()] = $cat['total'];
                    continue;
                }
                $parentsP[$cat[0]->getParent()->getName()] += $cat['total'];
                continue;
            }
            if (!array_key_exists($cat[0]->getName(), $parentsP)) {
                $parentsP[$cat[0]->getName()] = $cat['total'];
                continue;
            }
            $parentsP[$cat[0]->getName()] += $cat['total'];
            continue;
        }

        $graphDataType = $serializer->serialize($graphDataType, 'json');
        $graphDataTypeP = $serializer->serialize($graphDataTypeP, 'json');
        $parents = $serializer->serialize($parents, 'json');

        // Get Months of the current Year in display
        $monthsData = $em->getRepository('TransactionsBundle:Transactions')->getMonths($year);

        // Get all years in place
        $allYears = $em->getRepository('TransactionsBundle:Transactions')->getAllYears();


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
        $parentsP = $serializer->serialize($parentsP, 'json');

        // dump($currentTransactions);
        // exit;

        return $this->render(
            'TransactionsBundle:main:dash.html.twig',
            array(
                'data' => array(
                        'profits' => $profits,
                        'expenses' => $expenses,
                        'pieChart' => $graphDataType,
                        'pieChartP' => $graphDataTypeP,
                        'parents' => $parents,
                        'parentsP' => $parentsP,
                    ),
                'transactions' => $currentTransactions,
                'months' => $monthsData,
                'years' => $allYears,
                'month' => $month,
                'year' => $year,
            )
        );
    }
}