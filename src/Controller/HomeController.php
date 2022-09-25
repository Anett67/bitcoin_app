<?php

namespace App\Controller;

use App\Service\Crypto;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(TransactionRepository $transactionRepository, Crypto $crypto): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home/index.html.twig', [
            'earnings' => 0,
            'transactions' => $transactionRepository->findAll()
        ]);
    }

    /**
     * @Route("/gains", name="earnings")
     */
    public function earnings(): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('home/earnings.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
