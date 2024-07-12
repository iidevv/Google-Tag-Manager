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

        if (!$this->getShippingStatus() || !$this->getPaymentStatus())
            return;

        $shippingStatus = $this->getShippingStatus()->getCode();

        $paymentStatus = $this->getPaymentStatus()->getCode();

        if ($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_PAID && $shippingStatus === \XLite\Model\Order\Status\Shipping::STATUS_SHIPPED) {
            $tracking = new BackendTracking;

            $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($this->getOrderId());

            if (!$order instanceof \XLite\Model\Order) {
                return;
            }

            $tracking->doPurchase($order);
        }
    }

}
