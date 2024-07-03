<?php

namespace Iidev\GoogleTagManager\Core;

use Iidev\GoogleTagManager\Core\FrontendTracking;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Auth extends \XLite\Core\Auth
{
    public function login($login, $password, $secureHash = null)
    {
        $result = parent::login($login, $password, $secureHash = null);

        $profile = $this->getProfile();

        if ($profile) {
            $tracking = new FrontendTracking();
            $tracking->doLogin($profile);
        }

        return $result;
    }
}
