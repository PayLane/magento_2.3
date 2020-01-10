<?php

declare(strict_types=1);

/**
 * File: HashValidatorTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model\Request;

use PeP\PaymentGateway\Api\HashComparerInterface;
use PeP\PaymentGateway\Model\Request\HashValidator;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class HashValidatorTest
 * @package PeP\PaymentGateway\Test\Unit\Model\Request
 */
class HashValidatorTest extends TestCase
{
    /**
     * @var string
     */
    private const HASH_PARAM = 'hash';

    /**
     * @var string
     */
    private const STATUS_PARAM = 'status';

    /**
     * @var string
     */
    private const AMOUNT_PARAM = 'amount';

    /**
     * @var string
     */
    private const CURRENCY_PARAM = 'currency';

    /**
     * @var string
     */
    private const DESCRIPTION_PARAM = 'description';

    /**
     * @var string
     */
    private const ID_3DSECURE_AUTH_PARAM = 'id_3dsecure_auth';

    /**
     * @var HashComparerInterface|MockObject
     */
    private $hashComparerMock;

    /**
     * @var HashValidator
     */
    private $hashValidator;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        //Internal mocks
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)->getMock();

        //Dependencies mocks
        $this->hashComparerMock = $this->getMockBuilder(HashComparerInterface::class)->getMock();

        $this->hashValidator = new HashValidator($this->hashComparerMock, self::ID_3DSECURE_AUTH_PARAM);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testValidateCorrectlyRunsHashComparing(): void
    {
        $isValid = true;

        $this->requestMock->expects($this->exactly(6))
            ->method('getParam')
            ->withConsecutive(
                [self::HASH_PARAM, ''],
                [self::STATUS_PARAM, ''],
                [self::DESCRIPTION_PARAM, ''],
                [self::AMOUNT_PARAM, ''],
                [self::CURRENCY_PARAM, ''],
                [self::ID_3DSECURE_AUTH_PARAM, '']
            )->willReturn('anything');

        $this->hashComparerMock->expects($this->once())
            ->method('compareHashes')
            ->with('anything', 'anything', 'anything', 'anything', 'anything', 'anything')
            ->willReturn($isValid);

        $this->hashValidator->validate($this->requestMock);
    }
}
