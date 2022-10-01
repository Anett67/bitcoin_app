<?php

namespace App\Controller;

use App\Repository\TotalEarningsRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\callback;

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

        for ($i = 0; $i < count($earnings); $i++) {
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
                    'label' => 'Historique des gains (30 jours)',
                    'backgroundColor' => 'rgb(31, 195, 108)',
                    'borderColor' => 'rgb(31, 195, 108)',
                    'data' => array_reverse($data),
                ]
            ]
        ]);
        $chart->setOptions([
            'elements' => [
                'line' => [
                    'tension' => '0.5'
                ],
                'point' => [
                    'pointStyle' => 'line',
                    'borderWidth' => 0
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ]
            ],
            'scales' => [
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => '€',
                        'color' => 'rgb(255,255,255)'
                    ],
                    'ticks' => [
                        'display' => false
                    ],
                    'grid' => [
                        'borderColor' => 'rgb(255,255,255)'
                    ]
                ],
                'x' => [
                    'position' => 'center',
                    'title' => [
                        'display' => true,
                        'text' => 'date',
                        'align' => 'end',
                        'color' => 'rgb(255,255,255)'
                    ],
                    'ticks' => [
                        'display' => false
                    ],
                    'grid' => [
                        'borderColor' => 'rgb(255,255,255)'
                    ]
                ]
            ]
        ]);

        return $this->render('home/earnings.html.twig', [
            'chart' => $chart,
        ]);
    }
}
