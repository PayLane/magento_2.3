<?php

declare(strict_types=1);

/**
 * File: TransferFactoryTest.php
 *
 
 
 */
namespace PeP\PaymentGateway\Test\Unit\Gateway\Http;

use PeP\PaymentGateway\Gateway\Http\TransferFactory;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class TransferFactoryTest
 */
class TransferFactoryTest extends TestCase
{
    /**
     * @var TransferFactory
     */
    private $transferFactory;

    /**
     * @var TransferInterface|MockObject
     */
    private $transferMock;

    /**
     * @var TransferBuilder|MockObject
     */
    private $transferBuilderMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->transferBuilderMock = $this->createMock(TransferBuilder::class);
        $this->transferMock = $this->createMock(TransferInterface::class);

        $this->transferFactory = new TransferFactory(
            $this->transferBuilderMock
        );
    }

    /**
     * @return void
     */
    public function testCreateCorrectlyBuildsTransferObject(): void
    {
        $request = ['data1', 'data2'];

        $this->transferBuilderMock->expects($this->once())
            ->method('setBody')
            ->with($request)
            ->willReturnSelf();

        $this->transferBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($this->transferMock);

        $this->assertSame($this->transferMock, $this->transferFactory->create($request));
    }
}
