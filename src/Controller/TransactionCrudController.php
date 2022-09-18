<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionCrudController extends AbstractController
{
    /**
     * @Route("/nouveau", name="create_transaction")
     */
    public function createTransaction(Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('transaction_crud/createEditTransaction.html.twig', [
            'page_title' => 'Ajouter une transaction',
            'action' => 'create', 
        ]);
    }
    /**
     * @Route("/modification", name="edit_transaction")
     */
    public function editTransaction(Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('transaction_crud/createEditTransaction.html.twig', [
            'page_title' => 'Ajouter un montant',
            'action' => 'edit',
        ]);
    }

    /**
     * @Route("/supprimer", name="delete_transaction")
     */
    public function deleteTransaction(Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('transaction_crud/deleteTransaction.html.twig', [
            'controller_name' => 'TransactionCrudController',
        ]);
    }
}
