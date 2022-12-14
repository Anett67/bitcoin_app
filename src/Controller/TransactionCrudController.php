<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Transaction;
use App\Form\TransactionDeleteType;
use App\Form\TransactionCreationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use App\Service\Crypto as ServiceCrypto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionCrudController extends AbstractController
{
    /**
     * @Route("/nouveau", name="create_transaction")
     */
    public function createEditTransaction(Request $request, EntityManagerInterface $manager, TransactionRepository $transactionRepository): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        $transaction = new Transaction();
        $transactionForm = $this->createForm(TransactionCreationType::class, $transaction);
        $transactionForm->handleRequest($request);

        if($transactionForm->isSubmitted() && $transactionForm->isValid()){
            $quantity = $transactionForm->get('quantity')->getData();
            $unit_price = $transactionForm->get('price')->getData();

            if ($transactionToEdit = $transactionRepository->findOneBy([
                'user' => $this->getUser(), 
                'crypto' => $transactionForm->get('crypto')->getData()
            ])) {
                $transactionToEdit->setQuantity($transactionToEdit->getQuantity() + $quantity);
                $transactionToEdit->setPrice($transactionToEdit->getPrice() + ($quantity * $unit_price));
                $transactionToEdit->setUpdatedAt(new DateTimeImmutable());
                $manager->persist($transactionToEdit);
                $manager->flush();

                $this->addFlash('success', 'Le montant a bien été ajouté.');
                return $this->redirectToRoute('dashboard');
            }

            $transaction->setCreatedAt(new DateTimeImmutable());
            $transaction->setUser($this->getUser());
            $transaction->setUpdatedAt(new DateTimeImmutable());
            $transaction->setPrice($quantity * $unit_price);
            $transaction->setEarnings(0);
            $manager->persist($transaction);
            $manager->flush();

            $this->addFlash('success', 'La transaction a bien été ajouté.');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('transaction_crud/createEditTransaction.html.twig', [
            'page_title' => 'Ajouter un montant',
            'form' => $transactionForm->createView()
        ]);
    }

    /**
     * @Route("/supprimer", name="delete_transaction")
     */
    public function deleteTransaction(Request $request, EntityManagerInterface $manager, ServiceCrypto $crypto, TransactionRepository $transactionRepository): Response
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute('app_login');
        }

        $transaction = new Transaction();
        $transactionForm = $this->createForm(TransactionDeleteType::class, $transaction);
        $transactionForm->handleRequest($request);

        if($transactionForm->isSubmitted() && $transactionForm->isValid()){
            $quantity = $transactionForm->get('quantity')->getData();
            $crypto->updateCryptoPrices();
            $price = $transaction->getCrypto()->getLastPrice();

            if ($transactionToEdit = $transactionRepository->findOneBy([
                'user' => $this->getUser(), 
                'crypto' => $transactionForm->get('crypto')->getData()
            ])) {
                $new_quantity = $transactionToEdit->getQuantity() - $quantity;
                if($new_quantity < 0) {
                    $this->addFlash('error', 'Vous ne disposez pas de ce montant de cette crypto-monnaie. Vous pouvez supprimer un maximum de 6.'  . $transactionToEdit->getQuantity() .'.');
                    return $this->redirectToRoute('dashboard');
                }
                $new_quantity = $transactionToEdit->getQuantity() - $quantity;
                if($new_quantity === 0) {
                    $transactionToEdit->setCrypto(null);
                    $manager->remove($transactionToEdit);
                    $manager->flush();
                    $this->addFlash('success', 'La transaction a bien été supprimé.');
                    return $this->redirectToRoute('dashboard');
                };
                $transactionToEdit->setQuantity($new_quantity);
                $transactionToEdit->setPrice($transactionToEdit->getPrice() - ($quantity * $price));
                $transactionToEdit->setUpdatedAt(new DateTimeImmutable());
                $manager->persist($transactionToEdit);
                $manager->flush();

                $this->addFlash('success', 'Le montant a bien été supprimé.');
                return $this->redirectToRoute('dashboard');
            }else {
                $this->addFlash('error', 'Vous n\'avez pas de transaction avec cette crypto-monnaie.');
                return $this->redirectToRoute('dashboard');
            }
            
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('transaction_crud/deleteTransaction.html.twig', [
            'controller_name' => 'TransactionCrudController',
            'form' => $transactionForm->createView()
        ]);
    }
}
