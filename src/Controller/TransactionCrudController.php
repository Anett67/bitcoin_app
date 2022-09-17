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
     * @Route("/modification/{id}", name="edit_transaction", requirements={"id":"\d+"})
     */
    public function createEditTransaction(Transaction $id = null, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('transaction_crud/createEditTransaction.html.twig', [
            'page_title' => $id ? 'Mise à jour de la transaction' : 'Ajouter une transaction',
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="delete_transaction", requirements={"id":"\d+"})
     */
    public function deleteTransaction(Transaction $id, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('transaction_crud/deleteTransaction.html.twig', [
            'controller_name' => 'TransactionCrudController',
        ]);
    }
}
