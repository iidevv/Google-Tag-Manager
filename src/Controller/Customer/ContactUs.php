<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use XCart\Extender\Mapping\Extender;

use Iidev\GoogleTagManager\Core\API;

/**
 * @Extender\Mixin
 */
class ContactUs extends \CDev\ContactUs\Controller\Customer\ContactUs
{

    protected function doActionSend()
    {
        // $email = \XLite\Core\Request::getInstance()->email;
        
        // $api = new API();
        // $api->createAndSubscribeProfile($email, ["\$source" => 'catalog_request']);

        parent::doActionSend();
    }
}
