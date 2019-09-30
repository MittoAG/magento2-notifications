<?php

namespace Mitto\Notifications\Plugin\Model\Order;

use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class ShipmentPlugin
 * @package Mitto\Notifications\Plugin\Model\Order
 */
class ShipmentPlugin
{
    /**
     * @var ManagerInterface
     */
    private $_eventManager;

    /**
     * ShipmentPlugin constructor.
     * @param ManagerInterface $_eventManager
     */
    public function __construct(ManagerInterface $_eventManager)
    {
        $this->_eventManager = $_eventManager;
    }

    /**
     * @param Shipment $subject
     * @param $result
     * @return mixed
     */
    public function afterRegister(Shipment $subject, $result)
    {
        $this->_eventManager->dispatch(
            'sales_order_shipment_register_after',
            [
                'shipment' => $subject,
            ]
        );
        return $result;
    }
}
