<?php

namespace Iidev\GoogleTagManager\View\Header;

use XLite\View\AView;
use XLite\Core\Config;
use XCart\Extender\Mapping\ListChild;
use Iidev\GoogleTagManager\Core\FrontendTracking;
use XLite\Core\Session;

/**
 * @ListChild (list="head", zone="customer")
 */
class TrackingSnippet extends AView
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Iidev/GoogleTagManager/tracking_snippet.js';

        return $list;
    }
    public function getGoogleTagManagerId()
    {
        return Config::getInstance()->Iidev->GoogleTagManager->container_id;
    }

    public function isCartPage()
    {
        if ($this->getTarget() == 'cart' && $this->getCart() && $this->getCart()->getItems()) {
            return true;
        }

        return false;
    }

    public function isCheckoutPage()
    {
        if ($this->getTarget() == 'checkout' && $this->getCart() && $this->getCart()->getItems()) {
            return true;
        }

        return false;
    }

    public function isSubscriptionPage()
    {
        if ($this->getTarget() == 'subscription_page') {
            return true;
        }

        return false;
    }

    public function getCheckoutSignupData()
    {
        $profile = $this->getOrder()->getOrigProfile();

        if ($profile && Session::getInstance()->checkout_signup) {
            $tracking = new FrontendTracking();
            return $tracking->doCheckoutRegister($profile);
        }
        return false;
    }

    public function isCheckoutSuccessPage()
    {
        if ($this->getTarget() == 'checkoutSuccess' && $this->getOrder()) {
            return true;
        }

        return false;
    }

    public function isProductPage()
    {
        if ($this->getTarget() == 'product' && $this->getProduct()) {
            return true;
        }

        return false;
    }

    protected function getCheckoutSuccessPageData()
    {
        $tracking = new FrontendTracking();
        return $tracking->doPurchase($this->getOrder());
    }

    protected function getCheckoutPageData()
    {
        $tracking = new FrontendTracking();
        return $tracking->doBeginCheckout();
    }

    protected function getCartPageData()
    {
        $tracking = new FrontendTracking();
        return $tracking->doViewCart($this->getCart());
    }

    protected function getProductPageData()
    {
        $tracking = new FrontendTracking();
        return $tracking->doViewProduct($this->getProduct());
    }

    /**
     * @param $category
     *
     * @return string
     */
    protected function getCategoryPathName(\XLite\Model\Category $category)
    {
        return $this->executeCachedRuntime(static function () use ($category) {
            $categoryPath = $category->getPath();

            if (count($categoryPath) > 5) {
                $categoryPath = array_merge(array_slice($categoryPath, 0, 4), end($categoryPath));
            }

            $categoryName = implode(
                ', ',
                array_map(
                    static function ($elem) {
                        return $elem->getName();
                    },
                    $categoryPath
                )
            );

            return $categoryName;
        }, ['getCategoryPathName', $category->getCategoryId()]);
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Iidev/GoogleTagManager/header/tracking_snippet.twig';
    }
}
