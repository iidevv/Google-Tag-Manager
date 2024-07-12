<?php

namespace Iidev\GoogleTagManager\Model\Payment;

use XCart\Extender\Mapping\Extender;
use \XLite\Core\Request;
use XLite\Core\Database;
use Iidev\GoogleTagManager\Model\MeasurementProtocol;

/**
 * @Extender\Mixin
 */
class Transaction extends \XLite\Model\Payment\Transaction
{

    public function handleCheckoutAction()
    {
        $return = parent::handleCheckoutAction();

        if ($return === static::COMPLETED && Request::getInstance()->client_id && Request::getInstance()->session_id) {
            $measurementProtocol = new MeasurementProtocol();

            $measurementProtocol->setOrderId($this->getOrder()->getOrderId());
            $measurementProtocol->setMpClientId(Request::getInstance()->client_id);
            $measurementProtocol->setMpSessionId(Request::getInstance()->session_id);
            $measurementProtocol->setDatePlaced($this->getOrder()->getDate());

            Database::getEM()->persist($measurementProtocol);
            Database::getEM()->flush();
        }

        return $return;
    }

}
