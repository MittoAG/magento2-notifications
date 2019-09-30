<?php

namespace Mitto\Notifications\Observer;

use Magento\Customer\Model\Data\Customer;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Mitto\Core\Api\SMSTemplateRepositoryInterface;
use Mitto\Core\Model\Renderer;
use Mitto\Core\Model\Sender;
use Mitto\Notifications\Helper\Config;

/**
 * Class Common
 * @package Mitto\Notifications\Observer
 */
class Common implements ObserverInterface
{
    /**
     * @var Config
     */
    private $configHelper;
    /**
     * @var Renderer
     */
    private $renderer;
    /**
     * @var Sender
     */
    private $sender;
    /**
     * @var SMSTemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * Common constructor.
     * @param Config $configHelper
     * @param Renderer $renderer
     * @param Sender $sender
     * @param SMSTemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        Config $configHelper,
        Renderer $renderer,
        Sender $sender,
        SMSTemplateRepositoryInterface $templateRepository
    ) {
        $this->configHelper = $configHelper;
        $this->renderer = $renderer;
        $this->sender = $sender;
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        if (($customerTemplateId = $this->configHelper->getCustomerTemplateId($eventName))
            && ($customerPhone = $this->getCustomerPhoneNumber($observer))) {
            try {
                $customerTemplate = $this->templateRepository->getById($customerTemplateId);
                $this->sender->send(
                    $customerPhone,
                    $this->renderer->renderTemplate($customerTemplate, $observer->getData()),
                    $customerTemplate->getFrom()
                );
            } catch (NoSuchEntityException $e) {
            }
        }
        if ($adminTemplateId = $this->configHelper->getAdministratorTemplateId($eventName)) {
            try {
                $adminTemplate = $this->templateRepository->getById($adminTemplateId);
                $this->sender->notifyAdministrators(
                    $this->renderer->renderTemplate($adminTemplate, $observer->getData()),
                    $adminTemplate->getFrom()
                );
            } catch (NoSuchEntityException $e) {
            }
        }
    }

    /**
     * @param DataObject $observer
     * @return bool|string|null
     */
    protected function getCustomerPhoneNumber(DataObject $observer)
    {
        if ($observer->hasData('customer')) {
            /** @var Customer $customer */
            $customer = $observer->getData('customer');
            foreach ($customer->getAddresses() as $address) {
                if ($address->getTelephone()) {
                    return $address->getTelephone();
                }
            }
        }
        if ($observer->hasData('order')) {
            /** @var Order $order */
            $order = $observer->getData('order');
            foreach ($order->getAddresses() as $address) {
                if ($address->getTelephone()) {
                    return $address->getTelephone();
                }
            }
        }
        if ($observer->hasData('invoice')) {
            /** @var Invoice $invoice */
            $invoice = $observer->getData('invoice');
            if ($invoice->getBillingAddressId() && $invoice->getBillingAddress()->getTelephone()) {
                return $invoice->getBillingAddress()->getTelephone();
            }
            if ($invoice->getShippingAddressId() && $invoice->getShippingAddress()->getTelephone()) {
                return $invoice->getShippingAddress()->getTelephone();
            }
        }
        if ($observer->hasData('creditmemo')) {
            /** @var Creditmemo $creditmemo */
            $creditmemo = $observer->getData('creditmemo');
            if ($creditmemo->getBillingAddressId() && $creditmemo->getBillingAddress()->getTelephone()) {
                return $creditmemo->getBillingAddress()->getTelephone();
            }
            if ($creditmemo->getShippingAddressId() && $creditmemo->getShippingAddress()->getTelephone()) {
                return $creditmemo->getShippingAddress()->getTelephone();
            }
        }
        return false;
    }
}
