<?php
declare(strict_types=1);

/**
 * File:Data.php
 *
 
 */

namespace PeP\PaymentGateway\Model\Notification;

/**
 * Class Data
 * @package PeP\PaymentGateway\Model\Notification
 */
class Data
{
    /**
     * @var string
     */
    public const STATUS_PENDING = 'PENDING';

    /**
     * @var string
     */
    public const STATUS_PERFORMED = 'PERFORMED';

    /**
     * @var string
     */
    public const STATUS_CLEARED = 'CLEARED';

    /**
     * @var string
     */
    public const STATUS_ERROR = 'ERROR';

    /**
     * @var string
     */
    public const MODE_MANUAL = 'manual';

    /**
     * @var string
     */
    public const MODE_AUTO = 'auto';
}
