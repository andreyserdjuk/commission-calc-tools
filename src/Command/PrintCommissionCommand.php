<?php

declare(strict_types=1);

namespace CommissionCalc\Command;

use CommissionCalc\RawCommissionCalcInterface;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrintCommissionCommand extends Command
{
    protected static $defaultName = 'comcalc:print-commission';

    private RawCommissionCalcInterface $commissionCalc;

    public function __construct(RawCommissionCalcInterface $commissionCalc)
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

        foreach ($lines as $paymentData) {
            try {
                $commission = $this->commissionCalc->calcCommission($paymentData);
                $output->writeln($commission);
            } catch (ClientExceptionInterface $e) {
                $output->writeln('External service is broken: ' . $e->getMessage());
            } catch (Exception $e) {
                $output->writeln($e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
