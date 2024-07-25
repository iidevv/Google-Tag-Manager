<?php

namespace Iidev\GoogleTagManager\Core;

use Iidev\GoogleTagManager\Core\Main;
use \XLite\Core\Event;
use XLite\InjectLoggerTrait;

class FrontendTracking
{
    use InjectLoggerTrait;

    public function doCheckoutRegister(\XLite\Model\Profile $profile)
    {
        $event = "checkout_signup";
        $main = Main::getInstance();

        return $main->getProfileData($profile, $event);
    }

    public function doRegister(\XLite\Model\Profile $profile)
    {
        $event = "signup";
        $main = Main::getInstance();

        Event::gtmProfile($main->getProfileData($profile, $event));
    }

    public function doLogin(\XLite\Model\Profile $profile)
    {
        $main = Main::getInstance();
        Event::gtmProfile($main->getProfileData($profile, "login"));
    }

    public function doLogout(\XLite\Model\Profile $profile)
    {
        $main = Main::getInstance();
        return $main->getProfileData($profile, "logout");
    }

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
        return $main->getPurchaseData($order, 'purchase');
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
}