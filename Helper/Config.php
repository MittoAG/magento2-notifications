<?php

namespace Mitto\Notifications\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Mitto\Notifications\Helper
 */
class Config extends AbstractHelper
{
    const SECTION_ID = 'mitto_notifications';
    const GROUP_CUSTOMER = 'customer';
    const GROUP_ADMINISTRATOR = 'administrator';

    /**
     * @param $eventName
     * @param string $area
     * @return mixed
     */
    public function getTemplateId($eventName, $area = self::GROUP_CUSTOMER)
    {
        return $this->scopeConfig->getValue(
            implode('/', [self::SECTION_ID, $area, $eventName]),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $eventName
     * @return mixed
     */
    public function getCustomerTemplateId($eventName)
    {
        return $this->getTemplateId($eventName, self::GROUP_CUSTOMER);
    }

    /**
     * @param $eventName
     * @return int
     */
    public function getAdministratorTemplateId($eventName)
    {
        return $this->getTemplateId($eventName, self::GROUP_ADMINISTRATOR);
    }
}
