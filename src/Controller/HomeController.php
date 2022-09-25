<?php

namespace App\Controller;

use App\Repository\TotalEarningsRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(TransactionRepository $transactionRepository, TotalEarningsRepository $totalEarningsRepository): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home/index.html.twig', [
            'earnings' => $totalEarningsRepository->findOneBy(['user' => $this->getUser()], ['createdAt' => 'DESC'])->getAmount(),
            'transactions' => $transactionRepository->findBy(['user' => $this->getUser()])
        ]);
    }

    /**
     * @Route("/gains", name="earnings")
     */
    public function earnings(ChartBuilderInterface $chartBuilder, TotalEarningsRepository $totalEarningsRepository): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        $earnings = $totalEarningsRepository->findBy(['user' => $this->getUser()]);

        $data = [];

        foreach ($earnings as $earning) {
            $data[] = $earning->getAmount();
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May'],
            'datasets' => [
                [
                    'label' => 'Historique des gains',
                    'backgroundColor' => 'rgb(31, 195, 108)',
                    'borderColor' => 'rgb(31, 195, 108)',
                    'data' => $data,
                ],
            ],
            'options' => [
                'scales' => [
                    'y' => [
                        'title' => [
                            'display' => true,
                            'text' => '€'
                        ]
                    ]
                ]
            ]
        ]);

        return $this->render('home/earnings.html.twig', [
            'chart' => $chart,
        ]);
    }
}
