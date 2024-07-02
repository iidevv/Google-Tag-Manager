<?php

namespace Iidev\GoogleTagManager\Controller\Customer;

use XCart\Extender\Mapping\Extender as Extender;
use Iidev\GoogleTagManager\Core\FrontendTracking;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    protected function processAddItemSuccess($item)
    {
        $tracking = new FrontendTracking();
        $tracking->doAddToCart($item);

        parent::processAddItemSuccess($item);
    }
    protected function doActionDelete()
    {
        $item = $this->getCart()->getItemByItemId(\XLite\Core\Request::getInstance()->cart_id);

        if($item) {
            $tracking = new FrontendTracking();
            $items[] = $item; 
            $tracking->doRemoveFromCart($items);
        }

        parent::doActionDelete();
    }
    protected function doActionClear()
    {
        $cart = $this->getCart();
        if($cart) {
            $tracking = new FrontendTracking();
            $tracking->doRemoveFromCart($cart->getItems()->toArray());
        }
        parent::doActionClear();
    }
}
