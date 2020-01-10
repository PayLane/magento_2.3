<?php

declare(strict_types=1);

/**
 * File: HashComparerTest.php
 *
 
 
 */

namespace PeP\PaymentGateway\Test\Unit\Model;

use PeP\PaymentGateway\Api\Config\GeneralConfigProviderInterface;
use PeP\PaymentGateway\Model\HashComparer;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class HashValidatorTest
 * @package PeP\PaymentGateway\Test\Unit\Model
 */
class HashComparerTest extends TestCase
{
    /**
     * @var GeneralConfigProviderInterface|MockObject
     */
    private $generalConfigProviderMock;

    /**
     * @var HashComparer
     */
    private $hashComparer;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->generalConfigProviderMock = $this->getMockBuilder(GeneralConfigProviderInterface::class)
            ->getMock();

        $this->hashComparer = new HashComparer($this->generalConfigProviderMock);
    }

    /**
     * @test
     *
     * @return void
     */
    public function testValidateHashCorrectlyComparesHash(): void
    {
        $hashSalt = 'a';

        $this->generalConfigProviderMock->expects($this->exactly(2))
            ->method('getHashSalt')
            ->willReturn($hashSalt);

        $this->assertTrue($this->hashComparer->compareHashes(
            sha1('a|b|d|c|e|f'),
            'b',
            'c',
            'd',
            'e',
            'f'
        ));

        $this->assertFalse($this->hashComparer->compareHashes(
            sha1('a|b|d|c|e|g'),
            'b',
            'c',
            'd',
            'e',
            'f'
        ));
    }
}
