<?php

namespace Iidev\GoogleTagManager\Core;

use Iidev\GoogleTagManager\Core\Main;
use \XLite\Core\Event;
use XLite\InjectLoggerTrait;

class FrontendTracking
{
    use InjectLoggerTrait;

    public function doViewCart(\XLite\Model\Cart $cart)
    {
        $main = Main::getInstance();
        return $main->getViewedCartData($cart);
    }
    public function doViewProduct(\XLite\Model\Product $product)
    {
        $main = Main::getInstance();
        return $main->getViewedProductData($product);
    }

    public function doBeginCheckout()
    {
        $main = Main::getInstance();
        return $main->getBeginCheckoutData();
    }

    public function doPurchase($order)
    {
        $main = Main::getInstance();
        return $main->getPurchaseData($order);
    }

    public function doAddToCart(\XLite\Model\OrderItem $item)
    {
        $main = Main::getInstance();
        Event::gtmAddedToCart($main->getAddedToCartData($item));

        // Free gift added to cart event
        if (\XLite\Core\Request::getInstance()->_source === 'gift') {
            Event::gtmFreeGift(['event' => 'free_gift']);
        }
    }

    public function doRemoveFromCart($items)
    {
        $main = Main::getInstance();
        Event::gtmRemovedFromCart($main->getRemovedFromCartData($items));
    }

    public function doAddToWishlist($item)
    {
        $main = Main::getInstance();
        Event::gtmAddedToWishlist($main->getAddedToWishlistData($item));
    }
}