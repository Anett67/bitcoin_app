<?php
namespace App\Service;

use App\Entity\TotalEarnings;
use App\Repository\CryptoRepository;
use App\Repository\TotalEarningsRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class Crypto 
{
    private $cryptoRepository;

    private $userRepository;

    private $earningsRepository;

    private $manager;

    public function __construct(
        CryptoRepository $cryptoRepository, 
        UserRepository $userRepository,
        TotalEarningsRepository $totalEarningsRepository,
        EntityManagerInterface $manager
    )
    {
        $this->cryptoRepository = $cryptoRepository;
        $this->userRepository = $userRepository;
        $this->earningsRepository = $totalEarningsRepository;
        $this->manager = $manager;
    }

    /**
     * Returns data of all cryptocurrencies saved in database from coinmarket API 
     */
    public function getCryptoData($symbols = null) 
    {
        if(!$symbols) {
            $symbols = $this->getSymbolList();
        }

        $url = 'https://sandbox-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
        $parameters = [
            "symbol" => $symbols,
            "convert" => 'EUR'
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: b54bcf4d-1bca-4e8e-9a24-22ff2c3d462c'
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL


        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers 
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        $response = json_decode($response);
        curl_close($curl); // Cjson_decode($response)lose request

        return $response->data;
    }
    
    /**
     * Returns the list of the symbols of all cryptocurrencies in database
     */
    public function getSymbolList() 
    {
        $crypto_listing = $this->cryptoRepository->findAll();

        $symbols = [];

        foreach ($crypto_listing as $crypto) {
            $symbols[] = $crypto->getSymbol();
        }

        return implode(',', $symbols);
    }

    /**
     * Updates lat price of each crypto in database
     */
    public function updateCryptoPrices()
    {
        $data = $this->getCryptoData();

        foreach ($data as $crypto) {
            $symbol = $crypto->symbol;
            $price = round($crypto->quote->EUR->price, 2);
            $crypto_to_update = $this->cryptoRepository->findOneBy(['symbol' => $symbol]);
            $crypto_to_update->setLastPrice($price);
            $this->manager->persist($crypto_to_update);
        }
        
        $this->manager->flush();
    }

    /**
     * Calculates users current earnings on all of his active transactions
     */
    public function calculateEarnings($user = null)
    {
        $users = $user ? [$user] : $this->userRepository->findAll();

        foreach ($users as $user) {
            $transactions = $user->getTransactions();

            $earnings = new TotalEarnings();
            $earnings->setUser($user);

            $earnings_on_transactions = 0;

            foreach ($transactions as $transaction) {
                $paid_price = $transaction->getPrice();
                $current_value = $transaction->getQuantity() * $transaction->getCrypto()->getLastPrice();
                $earning_on_transaction = $current_value - $paid_price;

                $transaction->setEarnings($earning_on_transaction);
                $this->manager->persist($transaction);
                
                $earnings_on_transactions += $earning_on_transaction;
            }

            $earnings->setCreatedAt(new DateTimeImmutable('now'));
            if (count($transactions) === 0) {
                $earnings->setAmount(0);
            } else {
                $earnings->setAmount($earnings_on_transactions);
            }

            $this->manager->persist($earnings);
            $this->manager->flush();
        }
    }
}