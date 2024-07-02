<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use XLite\InjectLoggerTrait;
use XCart\Extender\Mapping\Extender;
use Iidev\GoogleTagManager\Core\API;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    use InjectLoggerTrait;
    protected function doActionRegister()
    {
        $result = parent::doActionRegister();

        // if ($result && $this->getModelForm()) {
        //     $api = new API();

        //     $profile = $this->getModelForm()->getModelObject();
        //     $businessCatetory = \XLite\Core\Request::getInstance()->business_category;

        //     $api->createAndSubscribeProfile($profile->getLogin(), ["\$source" => 'signup', "Category" => $businessCatetory]);
        // }

        return $result;
    }
}
