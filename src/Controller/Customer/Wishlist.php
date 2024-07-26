<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use XCart\Extender\Mapping\Extender as Extender;
use Iidev\GoogleTagManager\Core\FrontendTracking;
/**
 * @Extender\Mixin
 */
class Wishlist extends \QSL\MyWishlist\Controller\Customer\Wishlist
{
    protected function doActionAddToWishlist()
    {
        parent::doActionAddToWishlist();

        if (!$this->getWishlistProduct())
            return;

        $tracking = new FrontendTracking();
        $tracking->doAddToWishlist($this->getWishlistProduct());

    }
}
