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

        $totalEarnings = $totalEarningsRepository->findOneBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'earnings' => $totalEarnings ? $totalEarnings->getAmount() : 0,
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

        $earnings = $totalEarningsRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC'], 30);

        $data = [];
        $labels = [];

        foreach ($earnings as $key => $earning) {
            if(intval($key) % 5 === 0) {
                $labels[] = $earning->getCreatedAt()->format('d/m/Y');
                continue;
            }
            $labels[] = '';
        }
        
        foreach ($earnings as $earning) {
            $data[] = $earning->getAmount();
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Historique des gains (derniers 30 jours)',
                    'backgroundColor' => 'rgb(31, 195, 108)',
                    'borderColor' => 'rgb(31, 195, 108)',
                    'data' => array_reverse($data),
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
