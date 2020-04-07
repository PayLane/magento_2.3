<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace PeP\PaymentGateway\Setup\Patch\Data;

use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class NotificationPatch implements
DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
         /**
         * Prepare database for install
         */
        $this->moduleDataSetup->getConnection()->startSetup();

        $salesInstaller = $this->salesSetupFactory->create();

        $salesInstaller->addAttribute(
            'order',
            'paylane_notification_timestamp',
            ['type' => 'int', 'visible' => false, 'default' => 0]
        );
        $salesInstaller->addAttribute(
            'order',
            'paylane_notification_status',
            ['type' => 'varchar', 'visible' => false, 'default' => '', 'length'=> 3]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }
}
