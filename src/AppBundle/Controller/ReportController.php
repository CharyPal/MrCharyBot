<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Wallet;
use League\Period\Period;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * @Route("reports/{id}/{from}/{to}")
     * @ParamConverter("wallet", class="AppBundle:Wallet")
     * @ParamConverter("from", options={"format": "Y-m-d"})
     * @ParamConverter("to", options={"format": "Y-m-d"}))
     *
     * @param Wallet $wallet
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return Response
     */
    public function profitAndLossAction(Wallet $wallet, \DateTime $from, \DateTime $to)
    {
        $period = new Period($from, $to);

        $expenses = $this->getDoctrine()->getRepository('AppBundle:Expense')->findByPeriod($wallet, $period);
        $incomes = $this->getDoctrine()->getRepository('AppBundle:Income')->findByPeriod($wallet, $period);

        $totals = $this->get('app.totals_calculator');
        $totals->setCurrency($wallet->getDefaultCurrency());
        
        $revenueCategories = $totals->sumByCategory($incomes, 'income');
        $expenseCategories = $totals->sumByCategory($expenses, 'expense');
        $totalRevenue = $totals->sum($incomes);
        $totalExpense = $totals->sum($expenses);
        $grandTotal = $totalRevenue->subtract($totalExpense);

        return $this->render(':report:pnl.html.twig', [
            'period' => $period, 'revenue' => $revenueCategories, 'expenses' => $expenseCategories,
            'totalRevenue' => $totalRevenue, 'totalExpense' => $totalExpense, 'grandTotal' => $grandTotal
        ]);
    }
}
