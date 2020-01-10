<?php

declare(strict_types=1);

/**
 * File: CommandResolverTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Gateway\Command\CreditCard\CaptureStrategyCommand;

use Exception;
use PeP\PaymentGateway\Api\Order\Payment\TransactionProviderInterface;
use PeP\PaymentGateway\Gateway\Command\CreditCard\CaptureStrategyCommand\CommandResolver;
use PeP\PaymentGateway\Test\Unit\Model\Config\Methods\CreditCardConfigProviderTestTrait;
use PeP\PaymentGateway\Test\Unit\Gateway\PayLaneRestClientTestTrait;
use PeP\PaymentGateway\Test\Unit\Gateway\SubjectReaderTestTrait;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment;
use MSlwk\TypeSafeArray\ObjectArray;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class CommandResolverTest
 * @package PeP\PaymentGateway\Test\Unit\Gateway\Command\CreditCard\CaptureStrategyCommand
 */
class CommandResolverTest extends TestCase
{
    use CreditCardConfigProviderTestTrait, SubjectReaderTestTrait, PayLaneRestClientTestTrait;

    /**
     * @var string
     */
    private const SALE = 'sale';

    /**
     * @var string
     */
    private const SALE_3DS = 'sale_3ds';

    /**
     * @var string
     */
    private const CAPTURE = 'settlement';

    /**
     * @var string
     */
    private const ID_AUTHORIZATION = 'id_authorization';

    /**
     * @var string
     */
    private const IS_CARD_ENROLLED = 'is_card_enrolled';

    /**
     * @var string
     */
    private const ACTIVE_AUTH_STATUS = 'ACTIVE';

    /**
     * @var TransactionProviderInterface|MockObject
     */
    private $transactionProviderMock;

    /**
     * @var CommandResolver
     */
    private $commandResolver;

    /**
     * @var TransactionInterface|MockObject
     */
    private $transactionMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCreditCardConfigProvider();
        $this->setUpSubjectReader();
        $this->setUpPayLaneRestClient();

        //Internal mocks
        $this->transactionMock = $this->getMockBuilder(TransactionInterface::class)->getMock();

        $this->paymentInfoMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        //Dependencies mocks
        $this->transactionProviderMock = $this->getMockBuilder(TransactionProviderInterface::class)->getMock();

        $this->commandResolver = new CommandResolver(
            $this->payLaneRestClientFactoryMock,
            $this->creditCardConfigProviderMock,
            $this->transactionProviderMock,
            $this->subjectReaderMock
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function testResolveCommandToBeUsedWhenCaptureTransactionExist(): void
    {
        $this->expectationsForGettingPaymentInfo();

        $this->transactionProviderMock->expects($this->once())
            ->method('getByTxnType')
            ->with($this->paymentInfoMock, TransactionInterface::TYPE_CAPTURE)
            ->willReturn(
                new ObjectArray(TransactionInterface::class, [$this->transactionMock])
            );

        $this->assertSame('', $this->commandResolver->resolveCommandToBeUsed($this->paymentDataObjectMock));
    }

    /**
     * @test
     *
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testResolveCommandToBeUsedWhenPayLaneRestClientThrowsException(): void
    {
        $emptyTransactionArray = new ObjectArray(TransactionInterface::class);
        $this->expectationsForGettingPaymentInfo();

        $this->transactionProviderMock->expects($this->exactly(2))
            ->method('getByTxnType')
            ->withConsecutive(
                [$this->paymentInfoMock, TransactionInterface::TYPE_CAPTURE],
                [$this->paymentInfoMock, TransactionInterface::TYPE_AUTH]
            )
            ->willReturnOnConsecutiveCalls(
                $emptyTransactionArray,
                new ObjectArray(TransactionInterface::class, [$this->transactionMock])
            );

        $idAuthorization = '45sdfsd';

        $this->expectationsForCreatingPaylaneRestClient();
        $this->paymentInfoMock->expects($this->at(0))
            ->method('getAdditionalInformation')
            ->with(self::ID_AUTHORIZATION)
            ->willReturn($idAuthorization);
        $this->payLaneRestClientMock->expects($this->once())
            ->method('getAuthorizationInfo')
            ->with([self::ID_AUTHORIZATION => $idAuthorization])
            ->willThrowException(new Exception());

        $this->expectationsForGetting3DSecureConfigurationValue(false);

        $this->assertSame(self::SALE, $this->commandResolver->resolveCommandToBeUsed($this->paymentDataObjectMock));
    }

    /**
     * @test
     *
     * @dataProvider provideDifferent3DSecureConfigurationValuesAndCardEnrollmentValueAndExpectedCommand
     *
     * @param bool $is3DSecureCheckEnabled
     * @param bool|null $isCardEnrolled
     * @param string $commandToBeExecute
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testResolveCommandToBeUsedWhenAuthorizationAndCaptureTransactionDontExist(
        bool $is3DSecureCheckEnabled,
        ?bool $isCardEnrolled,
        string $commandToBeExecute
    ): void {
        $emptyTransactionArray = new ObjectArray(TransactionInterface::class);
        $this->expectationsForGettingPaymentInfo();

        $this->transactionProviderMock->expects($this->exactly(2))
            ->method('getByTxnType')
            ->withConsecutive(
                [$this->paymentInfoMock, TransactionInterface::TYPE_CAPTURE],
                [$this->paymentInfoMock, TransactionInterface::TYPE_AUTH]
            )
            ->willReturnOnConsecutiveCalls(
                $emptyTransactionArray,
                $emptyTransactionArray
            );

        $this->expectationsForGetting3DSecureConfigurationValue($is3DSecureCheckEnabled);

        if ($is3DSecureCheckEnabled && $isCardEnrolled !== null) {
            //We assume that 3DSecureCheck is enabled so it makes sense to check if card is enrolled
            $this->paymentInfoMock->expects($this->once())
                ->method('getAdditionalInformation')
                ->with(self::IS_CARD_ENROLLED)
                ->willReturn($isCardEnrolled);
        } else {
            $this->paymentInfoMock->expects($this->never())
                ->method('getAdditionalInformation')
                ->with(self::IS_CARD_ENROLLED);
        }

        $this->assertSame(
            $commandToBeExecute,
            $this->commandResolver->resolveCommandToBeUsed($this->paymentDataObjectMock)
        );
    }

    /**
     * @test
     *
     * @dataProvider provideDifferent3DSecureConfigurationValuesAndCardEnrollmentValueAndExpectedCommand
     *
     * @param bool $is3DSecureCheckEnabled
     * @param bool|null $isCardEnrolled
     * @param string $commandToBeExecute
     *
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testResolveCommandToBeUsedWhenCaptureTransactionDoesntExistButAuthorizationIsExpired(
        bool $is3DSecureCheckEnabled,
        ?bool $isCardEnrolled,
        string $commandToBeExecute
    ): void {
        $emptyTransactionArray = new ObjectArray(TransactionInterface::class);
        $this->expectationsForGettingPaymentInfo();

        $this->transactionProviderMock->expects($this->exactly(2))
            ->method('getByTxnType')
            ->withConsecutive(
                [$this->paymentInfoMock, TransactionInterface::TYPE_CAPTURE],
                [$this->paymentInfoMock, TransactionInterface::TYPE_AUTH]
            )
            ->willReturnOnConsecutiveCalls(
                $emptyTransactionArray,
                new ObjectArray(TransactionInterface::class, [$this->transactionMock])
            );

        $this->expectationsForCheckingIfAuthorizationIsExpired(true);
        $this->expectationsForGetting3DSecureConfigurationValue($is3DSecureCheckEnabled);

        if ($is3DSecureCheckEnabled && $isCardEnrolled !== null) {
            //We assume that 3DSecureCheck is enabled so it makes sense to check if card is enrolled
            $this->paymentInfoMock->expects($this->at(1))
                ->method('getAdditionalInformation')
                ->with(self::IS_CARD_ENROLLED)
                ->willReturn($isCardEnrolled);
        }

        $this->assertSame(
            $commandToBeExecute,
            $this->commandResolver->resolveCommandToBeUsed($this->paymentDataObjectMock)
        );
    }

    /**
     * @test
     *
     * @return void
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function testResolveCommandToBeUsedWhenCaptureTransactionDoesntExistAndAuthorizationNotExpired(): void
    {
        $emptyTransactionArray = new ObjectArray(TransactionInterface::class);
        $this->expectationsForGettingPaymentInfo();

        $this->transactionProviderMock->expects($this->exactly(2))
            ->method('getByTxnType')
            ->withConsecutive(
                [$this->paymentInfoMock, TransactionInterface::TYPE_CAPTURE],
                [$this->paymentInfoMock, TransactionInterface::TYPE_AUTH]
            )
            ->willReturnOnConsecutiveCalls(
                $emptyTransactionArray,
                new ObjectArray(TransactionInterface::class, [$this->transactionMock])
            );
        $this->expectationsForCheckingIfAuthorizationIsExpired(false);

        $this->assertSame(self::CAPTURE, $this->commandResolver->resolveCommandToBeUsed($this->paymentDataObjectMock));
    }

    /**
     * @param bool $expired
     * @return void
     */
    private function expectationsForCheckingIfAuthorizationIsExpired(bool $expired): void
    {
        $idAuthorization = '45sdfsd';
        $expired ? $status = 'NOT_ACTIVE' : $status = self::ACTIVE_AUTH_STATUS;

        $this->expectationsForCreatingPaylaneRestClient();
        $this->paymentInfoMock->expects($this->at(0))
            ->method('getAdditionalInformation')
            ->with(self::ID_AUTHORIZATION)
            ->willReturn($idAuthorization);
        $this->payLaneRestClientMock->expects($this->once())
            ->method('getAuthorizationInfo')
            ->with([self::ID_AUTHORIZATION => $idAuthorization])
            ->willReturn(['success' => true, 'status' => $status]);
    }

    /**
     * @return array
     */
    public function provideDifferent3DSecureConfigurationValuesAndCardEnrollmentValueAndExpectedCommand(): array
    {
        return [
            [true, true, self::SALE_3DS],
            [true, false, self::SALE],
            [false, null, self::SALE],
            [false, null, self::SALE]
        ];
    }
}
