<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Session;
use \XLite\Core\Event;

/**
 * Coupon
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class Coupon extends \CDev\Coupons\Controller\Customer\Coupon
{
    /**
     * @inheritdoc
     */
    protected function doActionAdd()
    {
        parent::doActionAdd();

        $code = (string) \XLite\Core\Request::getInstance()->code;
        $coupon = \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon')
            ->findOneByCode($code);

        if ($coupon) {
            Event::gtmCouponApplied(['event' => 'coupon_applied']);
            Session::getInstance()->coupon = $code;
        }
    }

    /**
     * @inheritdoc
     */
    protected function doActionRemove()
    {
        parent::doActionRemove();

        Session::getInstance()->coupon = "";
    }
}
