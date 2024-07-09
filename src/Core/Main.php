<?php

namespace Iidev\GoogleTagManager\Core;

use XLite\InjectLoggerTrait;
use XLite\Core\Session;

class Main extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;
    protected static $instance;

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getProfileData(\XLite\Model\Profile $profile, $event)
    {

        return [
            "event" => $event,
            "user_id" => $profile->getProfileId(),
            "membership" => $profile->getMembershipId() ? "pro member" : "non-member",
            "total_orders" => $profile->getOrdersCount()
        ];
    }

    public function getAddedToCartData($item)
    {
        $product = $item->getProduct();

        $data = [
            'event' => 'add_to_cart',
            'ecommerce' => [
                "value" => $item->getPrice() * $item->getAmount(),
                "currency" => $this->getCurrencyCode(),
                "items" => [
                    [
                        "item_id" => $item->getSku(),
                        "item_name" => $item->getName(),
                        "item_brand" => $product->getBrandName(),
                        "item_variant" => $this->getProductVariantName($item),
                        "price" => (int) $item->getPrice(),
                        "quantity" => $item->getAmount()
                    ]
                ]
            ]
        ];

        if ($product->getNetMarketPrice()) {
            $data["ecommerce"]["items"][0]["discount"] = round($product->getNetMarketPrice() - $product->getPrice(), 2);
        }

        $data["ecommerce"]["items"][0] = array_merge($data["ecommerce"]["items"][0], $this->getProductCategories($product));

        return $data;
    }

    public function getRemovedFromCartData($items)
    {
        $data = [
            'event' => 'remove_from_cart',
            'ecommerce' => [
                "value" => $this->getItemsTotal($items),
                "currency" => $this->getCurrencyCode(),
                "items" => $this->getItems($items)
            ]
        ];

        return $data;
    }
    public function getAddedToWishlistData($product)
    {
        $data = [
            'event' => 'add_to_wishlist',
            'ecommerce' => [
                "value" => (int) $product->getPrice(),
                "currency" => $this->getCurrencyCode(),
                "items" => [
                    [
                        "item_id" => $product->getVariant() ? $product->getVariant()->getSku() : $product->getSku(),
                        "item_name" => $product->getName(),
                        "item_brand" => $product->getBrandName(),
                        "price" => (int) $product->getPrice(),
                        "quantity" => 1
                    ]
                ]
            ]
        ];

        if ($product->getNetMarketPrice()) {
            $data["ecommerce"]["items"][0]["discount"] = round($product->getNetMarketPrice() - $product->getPrice(), 2);
        }

        $data["ecommerce"]["items"][0] = array_merge($data["ecommerce"]["items"][0], $this->getProductCategories($product));

        return $data;
    }

    public function getBeginCheckoutData()
    {

        $cart = \XLite::getController()->getCart();

        $data = [
            'event' => 'begin_checkout',
            'ecommerce' => [
                "value" => $cart->getTotal(),
                "currency" => $this->getCurrencyCode(),
                "items" => $this->getItems($cart->getItems())
            ]


        ];

        if (Session::getInstance()->coupon) {
            $data["coupon"] = Session::getInstance()->coupon;
        }

        return $data;
    }

    public function getPurchaseData($order)
    {
        $data = [
            'event' => 'purchase',
            'transaction_id' => (string) $order->getOrderId(),
            'shipping' => round($order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING), 2),
            'tax' => round($order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX), 2),
            "value" => $order->getTotal(),
            "currency" => $this->getCurrencyCode(),
            "items" => $this->getItems($order->getItems())
        ];

        if (Session::getInstance()->coupon) {
            $data["coupon"] = Session::getInstance()->coupon;

            unset(Session::getInstance()->coupon);
        }

        return $data;
    }

    public function getViewedProductData($product)
    {
        $data = [
            'event' => 'view_item',
            'ecommerce' => [
                "value" => $product->getPrice(),
                "currency" => $this->getCurrencyCode(),
                "items" => [
                    [
                        "item_id" => $product->getVariant() ? $product->getVariant()->getSku() : $product->getSku(),
                        "item_name" => $product->getName(),
                        "item_brand" => $product->getBrandName(),
                        "item_url" => $product->getURL(),
                        "item_image_url" => $product->getImageURL(),
                        "price" => $product->getPrice(),
                    ]
                ]
            ]
        ];

        if ($product->getNetMarketPrice()) {
            $data["ecommerce"]["items"][0]["discount"] = round($product->getNetMarketPrice() - $product->getPrice(), 2);
        }

        $data["ecommerce"]["items"][0] = array_merge($data["ecommerce"]["items"][0], $this->getProductCategories($product));

        return $data;
    }

    public function getViewedCartData($cart)
    {
        $data = [
            'event' => 'view_cart',
            'ecommerce' => [
                "value" => $cart->getTotal(),
                "currency" => $this->getCurrencyCode(),
                "items" => $this->getItems($cart->getItems())
            ]
        ];

        return $data;
    }

    protected function getProductCategories($product)
    {
        $categories = [];
        foreach ($product->getCategories() as $key => $category) {
            if ($key == 0) {
                $categories["item_category"] = $category->getName();
            } else {
                $categories["item_category" . $key + 1] = $category->getName();
            }
        }
        return $categories;
    }

    protected function getProductVariantName($item)
    {
        $attr = [];

        foreach ($item->getSortedAttributeValues() as $value) {
            $attr[] = $value->getActualName() . ":" . $value->getActualValue();
        }

        return implode(', ', $attr);
    }

    protected function getItemsTotal($items)
    {
        return array_reduce($items, function ($acc, $item) {
            return $acc + ($item->getPrice() * $item->getAmount());
        }, 0);
    }

    protected function getItems($cartItems)
    {
        $items = [];

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduct();
            $items[] = [
                "item_id" => $cartItem->getSku(),
                "item_name" => $cartItem->getName(),
                "item_brand" => $product->getBrandName(),
                "item_variant" => $this->getProductVariantName($cartItem),
                "price" => (int) $product->getPrice(),
                "quantity" => $cartItem->getAmount(),
            ];

            $items[count($items) - 1] = array_merge($items[count($items) - 1], $this->getProductCategories($product));

            if ($product->getNetMarketPrice()) {
                $items[count($items) - 1]["discount"] = round($product->getNetMarketPrice() - $product->getPrice(), 2);
            }

            if (Session::getInstance()->coupon) {
                $items[count($items) - 1]["coupon"] = Session::getInstance()->coupon;
            }
        }
        return $items;
    }

    protected function getCurrencyCode()
    {
        return \XLite::getInstance()->getCurrency()->getCode();
    }

    protected function __construct()
    {
        parent::__construct();
    }
}
