<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use Iidev\GoogleTagManager\Core\FrontendTracking;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Session;

/**
 * @Extender\Mixin
 */
class Login extends \XLite\Controller\Customer\Login
{

    protected function doActionLogoff()
    {
        $profile = $this->getProfile();
        $profileData = null;

        if ($profile) {
            $tracking = new FrontendTracking();
            $profileData = $tracking->doLogout($profile);
        }

        parent::doActionLogoff();

        Session::getInstance()->profile_data = $profileData;
    }
}
