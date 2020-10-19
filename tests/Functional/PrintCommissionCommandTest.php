<?php

namespace CommissionCalc\Tests\Functional;

use CommissionCalc\Command\PrintCommissionCommand;
use CommissionCalc\Exception\BadPaymentDataException;
use CommissionCalc\Exception\ProviderConnectivityException;
use CommissionCalc\RawCommissionCalcInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Checks whether command prints output correctly and properly prints exceptions messages.
 */
class PrintCommissionCommandTest extends TestCase
{
    /**
     * @covers \CommissionCalc\Command\PrintCommissionCommand::execute()
     */
    public function testExecute()
    {
        $input1 = '{"bin":"45717360","amount":"100.00","currency":"EUR"}';
        $input2 = '{"bin":"516793","amount":"50.00","currency":"USD"}';
        $input3 = '{"bin":"45417360","amount":"10000.00","currency":"JPY"}';

        $output1 = '1.0';
        $output2 = '1.2';
        $output3 = '1.3';

        $expectedOutput = $output1 . PHP_EOL
            . $output2 . PHP_EOL
            . $output3 . PHP_EOL;

        $inputData = implode(
            PHP_EOL,
            [
                $input1,
                $input2,
                $input3,
            ]
        );

        /** @var MockObject|RawCommissionCalcInterface $commissionCalc */
        $commissionCalc = $this->getMockBuilder(RawCommissionCalcInterface::class)
            ->onlyMethods(['calcCommission'])
            ->getMock()
        ;

        $commissionCalc
            ->expects($this->exactly(3))
            ->method('calcCommission')
            ->withConsecutive([$input1], [$input2], [$input3])
            ->will(
                $this->returnValueMap(
                    [
                        [$input1, $output1],
                        [$input2, $output2],
                        [$input3, $output3],
                    ]
                )
            )
        ;

        $command = new PrintCommissionCommand($commissionCalc);
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'data' => $inputData,
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertEquals($expectedOutput, $output);
    }

    /**
     * @covers \CommissionCalc\Command\PrintCommissionCommand::execute()
     */
    public function testExecuteWhenProviderFails()
    {
        $inputData = '{"bin":"45717360","amount":"100.00","currency":"EUR"}';
        $errorMessage = 'cannot connect to provider';
        $expectedOutput = 'External service is broken: ' . $errorMessage . PHP_EOL;

        /** @var MockObject|RawCommissionCalcInterface $commissionCalc */
        $commissionCalc = $this->getMockBuilder(RawCommissionCalcInterface::class)
            ->onlyMethods(['calcCommission'])
            ->getMock()
        ;

        $commissionCalc
            ->expects($this->exactly(1))
            ->method('calcCommission')
            ->willThrowException(new ProviderConnectivityException($errorMessage))
        ;

        $command = new PrintCommissionCommand($commissionCalc);
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'data' => $inputData,
            ]
        );

        $output = $commandTester->getDisplay();
        $this->assertEquals($expectedOutput, $output);
    }

    /**
     * @covers \CommissionCalc\Command\PrintCommissionCommand::execute()
     */
    public function testExecuteWithBadInputData()
    {
        $inputData = 'bad data';
        $errorMessage = 'exception message';
        $expectedOutput = 'Cannot parse payment data: "bad data", error: "exception message"' . PHP_EOL;

        /** @var MockObject|RawCommissionCalcInterface $commissionCalc */
        $commissionCalc = $this->getMockBuilder(RawCommissionCalcInterface::class)
            ->onlyMethods(['calcCommission'])
            ->getMock()
        ;

        $commissionCalc
            ->expects($this->exactly(1))
            ->method('calcCommission')
            ->willThrowException(new BadPaymentDataException($errorMessage))
        ;

        $command = new PrintCommissionCommand($commissionCalc);
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'data' => $inputData,
            ]
        );

        $output = $commandTester->getDisplay();
        $this->assertEquals($expectedOutput, $output);
    }
}
