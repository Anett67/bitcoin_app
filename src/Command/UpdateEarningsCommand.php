<?php

namespace App\Command;

use App\Service\Crypto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateEarningsCommand extends Command
{
    protected static $defaultName = 'app:update-earnings';
    protected static $defaultDescription = 'Updates users total earnings';

    private $crypto;

    public function __construct(Crypto $crypto)
    {
        $this->crypto = $crypto;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->crypto->updateCryptoPrices();
        $this->crypto->calculateEarnings();

        $io->success('New earnings data successfully saved.');

        return Command::SUCCESS;
    }
}
