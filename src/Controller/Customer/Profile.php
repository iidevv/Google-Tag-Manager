<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use Iidev\GoogleTagManager\Core\FrontendTracking;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    protected function doActionRegister()
    {
        $result = parent::doActionRegister();

        if ($result && $this->getModelForm()) {
            $profile = $this->getModelForm()->getModelObject();

            $tracking = new FrontendTracking();
            $tracking->doRegister($profile);
        }

        return $result;
    }
}
