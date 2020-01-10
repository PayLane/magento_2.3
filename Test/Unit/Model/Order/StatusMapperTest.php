<?php

declare(strict_types=1);

/**
 * File: StatusMapperTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Order;

use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Model\Order\StatusMapper;
use PeP\PaymentGateway\Model\Notification\Data;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class StatusMapperTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Order
 */
class StatusMapperTest extends TestCase
{
    /**
     * @var string
     */
    private $status = 'test';

    /**
     * @var GeneralConfigProviderInterface|MockObject
     */
    private $generalConfigProviderMock;

    /**
     * @var StatusMapper
     */
    private $statusMapper;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->generalConfigProviderMock = $this->getMockBuilder(GeneralConfigProviderInterface::class)
            ->getMock();

        $this->statusMapper = new StatusMapper($this->generalConfigProviderMock);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testMapWhenStatusIsPending(): void
    {
        $this->generalConfigProviderMock->expects($this->once())
            ->method('getPendingOrderStatus')
            ->willReturn($this->status);

        $this->assertSame($this->status, $this->statusMapper->map(Data::STATUS_PENDING));
    }

    /**
     * @test
     *
     * @return void
     */
    public function testMapWhenStatusIsPerformed(): void
    {
        $this->generalConfigProviderMock->expects($this->once())
            ->method('getPerformedOrderStatus')
            ->willReturn($this->status);

        $this->assertSame($this->status, $this->statusMapper->map(Data::STATUS_PERFORMED));
    }

    /**
     * @test
     *
     * @return void
     */
    public function testMapWhenStatusIsCleared(): void
    {
        $this->generalConfigProviderMock->expects($this->once())
            ->method('getClearedOrderStatus')
            ->willReturn($this->status);

        $this->assertSame($this->status, $this->statusMapper->map(Data::STATUS_CLEARED));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferentStatusesToBeMappedAsError
     *
     * @param string $statusToBeMappedAsError
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testMapWhenStatusIsError(string $statusToBeMappedAsError): void
    {
        $this->generalConfigProviderMock->expects($this->once())
            ->method('getErrorOrderStatus')
            ->willReturn($this->status);

        $this->assertSame($this->status, $this->statusMapper->map($statusToBeMappedAsError));
    }

    /**
     * @return array
     */
    public function provideDifferentStatusesToBeMappedAsError(): array
    {
        return [
            [Data::STATUS_ERROR],
            ['sth_other']
        ];
    }
}
