<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use Iidev\GoogleTagManager\Core\FrontendTracking;
use XLite\Core\Session;

/**
 * Checkout
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    protected function saveAnonymousProfile()
    {
        parent::saveAnonymousProfile();

        Session::getInstance()->checkout_signup = true;
    }
}
