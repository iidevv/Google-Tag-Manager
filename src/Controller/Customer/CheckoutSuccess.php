<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use Iidev\GoogleTagManager\Core\BackendTracking;
use Iidev\GoogleTagManager\Core\FrontendTracking;
use XCart\Extender\Mapping\Extender;

/**
 * Checkout success controller
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    protected function doNoAction()
    {
        parent::doNoAction();

        // if (
        //     !\XLite\Core\Request::getInstance()->isAJAX()
        //     && in_array($this->getTarget(), ['checkout_success', 'checkoutSuccess'])
        //     && $this->getOrder()
        // ) {
        //     $tracking = new FrontendTracking;
        //     $tracking->doPurchase($this->getOrder());
        // }
    }
}
