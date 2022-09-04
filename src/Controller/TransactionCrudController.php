<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionCrudController extends AbstractController
{
    /**
     * @Route("/transaction/crud", name="app_transaction_crud")
     */
    public function index(): Response
    {
        return $this->render('transaction_crud/index.html.twig', [
            'controller_name' => 'TransactionCrudController',
        ]);
    }
}
