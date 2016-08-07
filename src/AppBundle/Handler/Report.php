<?php

namespace AppBundle\Handler;

use AppBundle\Event;
use AppBundle\Response\Document;
use AppBundle\Response\Message;
use AppBundle\TimeParser;
use AppBundle\TotalsCalculator;
use Knp\Snappy\GeneratorInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Report
 * @package AppBundle\Handler
 * Generates a PDF report
 * Profit and Loss for the period
 */
class Report extends AbstractHandler
{
    protected $regularExpression = '/^[\/]?report\s(?P<interval>.+)$/i';

    /** @var TotalsCalculator */
    private $calculator;

    /** @var GeneratorInterface */
    private $pdf;

    /** @var  string */
    private $cacheDir;

    /** @var  Filesystem */
    private $filesystem;

    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param GeneratorInterface $pdf
     */
    public function setPdfGenerator(GeneratorInterface $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * @param TotalsCalculator $calculator
     */
    public function setTotalsCalculator(TotalsCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;

        $event->stopPropagation();
        
        try {
            $period = $this->getPeriod();
            $wallet = $this->getWallet($event);

            $expenses = $this->em->getRepository('AppBundle:Expense')->findByPeriod($wallet, $period);
            $incomes = $this->em->getRepository('AppBundle:Income')->findByPeriod($wallet, $period);

            $this->calculator->setCurrency($wallet->getDefaultCurrency());
            $revenueCategories = $this->calculator->sumByCategory($incomes, 'income');
            $expenseCategories = $this->calculator->sumByCategory($expenses, 'expense');
            $totalRevenue = $this->calculator->sum($incomes);
            $totalExpense = $this->calculator->sum($expenses);
            $grandTotal = $totalRevenue->subtract($totalExpense);

            $startDate = $period->getStartDate()->format('Y-m-d');
            $endDate = $period->getEndDate()->format('Y-m-d');

            // file system dances. Check if all the directories exist and stuff
            $filePath = $this->cacheDir.DIRECTORY_SEPARATOR.'reports'.DIRECTORY_SEPARATOR
                .$wallet->getAccount().DIRECTORY_SEPARATOR
                .'pnl-'.$startDate.'-'.$endDate .'.pdf';
            $fileName = "Profit and Loss {$startDate} - {$endDate}.pdf";

            $this->filesystem->mkdir(pathinfo($filePath, PATHINFO_DIRNAME));

            if ($this->filesystem->exists($filePath))
                $this->filesystem->remove($filePath);

            $html = $this->render(':report:pnl.html.twig', [
                'period' => $period, 'revenue' => $revenueCategories, 'expenses' => $expenseCategories,
                'totalRevenue' => $totalRevenue, 'totalExpense' => $totalExpense, 'grandTotal' => $grandTotal
            ]);

            $this->pdf->generateFromHtml($html, $filePath);

            $event->setResponse(new Document($filePath, $fileName));
        } catch (\Exception $e) {
            $content = $this->render(':message/errors:period.md.twig', ['message' => $event->getMessageText()]);
            $event->setResponse(new Message($content, $event->getMessageId()));
        }
    }

    /**
     * @return \League\Period\Period
     */
    private function getPeriod()
    {
        return (new TimeParser())->parse($this->matches['interval']);
    }
}
