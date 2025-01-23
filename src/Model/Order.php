<?php

namespace Iidev\GoogleTagManager\Model;

use XCart\Extender\Mapping\Extender;
use Iidev\GoogleTagManager\Core\BackendTracking;

/**
 *
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Order
{
    public function setPaymentStatus($paymentStatus = null)
    {
        parent::setPaymentStatus($paymentStatus);

        if (!$this->getPaymentStatus())
            return;

        $paymentStatus = $this->getPaymentStatus()->getCode();
        $oldStatus = $this->getOldPaymentStatusCode();

        $tracking = new BackendTracking;

        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($this->getOrderId());

        if (!$order instanceof \XLite\Model\Order) {
            return;
        }

        if ($paymentStatus === $oldStatus) {
            return;
        }

        if ($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_PAID) {
            $tracking->doPurchase($order);
        }
        if ($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_REFUNDED) {
            $tracking->doRefund($order);
        }
    }

    public function setShippingStatus($shippingStatus = null)
    {
        parent::setShippingStatus($shippingStatus);

        if ($this->isCompletedOrder()) {
            $tracking = new BackendTracking;

            $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($this->getOrderId());

            if (!$order instanceof \XLite\Model\Order) {
                return;
            }

            $tracking->doPurchase($order);
        }
    }

    protected function isCompletedOrder(): bool
    {
        $paymentStatus = $this->getPaymentStatus()?->getCode();
        $shippingStatus = $this->getShippingStatus()?->getCode();
        $oldShippingStatus = $this->oldShippingStatus?->getCode();

        if (!$oldShippingStatus || $oldShippingStatus == $shippingStatus) {
            return false;
        }

        if (!$shippingStatus || !$paymentStatus) {
            return false;
        }

        if($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_PAID && $shippingStatus === \XLite\Model\Order\Status\Shipping::STATUS_SHIPPED) {
            return true;
        }

        if($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_PAID && $shippingStatus === \XLite\Model\Order\Status\Shipping::STATUS_DELIVERED) {
            return true;
        }

        return false;
    }

}
