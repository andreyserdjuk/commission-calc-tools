<?php

namespace CommissionCalc\Command;

use CommissionCalc\CommissionCalcInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class PrintCommissionCommand extends Command
{
    protected static $defaultName = 'comcalc:print-commission';

    private CommissionCalcInterface $commissionCalc;

    public function __construct(CommissionCalcInterface $commissionCalc)
    {
        parent::__construct();
        $this->commissionCalc = $commissionCalc;
    }

    protected function configure()
    {
        $this
            ->setDescription('Calculate commission from input')
            ->addArgument(
                'data',
                InputArgument::REQUIRED,
                'Text consists of lines in format: {"bin":"45717360","amount":"100.00","currency":"EUR"}'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $input->getArgument('data');
        $lines = explode("\n", $data);

        foreach ($lines as $line) {
            $paymentData = json_decode($line, true);
            $bin = $paymentData['bin'];
            $amount = $paymentData['amount'];
            $currency = $paymentData['currency'];
            try {
                $commission = $this->commissionCalc->calcCommission($bin, $amount, $currency);
            } catch (Throwable $e) {
                $output->writeln($e->getMessage());
                $output->writeln($e->getTraceAsString());
                return Command::SUCCESS;
            }
            $output->writeln($commission);
        }

        return Command::SUCCESS;
    }
}
