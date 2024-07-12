<?php

namespace Iidev\GoogleTagManager\Core;

use Iidev\GoogleTagManager\Core\Main;
use Iidev\GoogleTagManager\Core\API;
use XLite\Core\Config;
use XLite\Core\Database;
use \XLite\Model\Base\Surcharge;

class BackendTracking
{
    public function doPurchase(\XLite\Model\Order $order)
    {
        if (!$this->isMPEnabled())
            return;

        $data = $this->getOrderEventData($order, 'purchase');

        if (!$data)
            return;

        $api = new API();

        $api->event($data);

        $this->setPurchaseCompletedDate($order->getOrderId());
    }

    public function doRefund(\XLite\Model\Order $order)
    {
        if (!$this->isMPEnabled())
            return;

        $data = $this->getOrderEventData($order, 'refund');

        if (!$data)
            return;

        $api = new API();

        $api->event($data);
    }

    private function isTestMode()
    {
        return (bool) Config::getInstance()->Iidev->GoogleTagManager->mp_test_mode;
    }
    private function isMPEnabled()
    {
        return (boolean) Config::getInstance()->Iidev->GoogleTagManager->mp_enabled;
    }
    private function getOrderEventData(\XLite\Model\Order $order, $eventName = '')
    {
        $measurementProtocol = Database::getRepo('Iidev\GoogleTagManager\Model\MeasurementProtocol')->findOneBy([
            'orderId' => $order->getOrderId()
        ]);

        if (!$measurementProtocol)
            return null;

        if ($eventName === 'purchase' && $measurementProtocol->getDateCompleted())
            return null;

        $main = Main::getInstance();
        $items = $order->getItems();

        $eventName = $this->isTestMode() ? $eventName . "_test" : $eventName;

        $data = [
            "client_id" => (string) $measurementProtocol->getMpClientId(),
            "timestamp_micros" => (string) (time() * 1000000),
            "non_personalized_ads" => false,
            "events" => [
                [
                    "name" => $eventName,
                    "params" => [
                        "items" => $main->getItems($items),
                        'transaction_id' => (string) $order->getOrderId(),
                        'shipping' => round($order->getSurchargeSumByType(Surcharge::TYPE_SHIPPING), 2),
                        'tax' => round($order->getSurchargeSumByType(Surcharge::TYPE_TAX), 2),
                        "value" => $order->getTotal(),
                        "currency" => $main->getCurrencyCode(),
                        "session_id" => (int) $measurementProtocol->getMpSessionId(),
                    ]
                ]
            ]
        ];

        if ($this->isTestMode()) {
            $data['events'][0]['params']['debug_mode'] = 1;
        }

        return $data;
    }

    private function setPurchaseCompletedDate($orderId)
    {
        $measurementProtocol = Database::getRepo('Iidev\GoogleTagManager\Model\MeasurementProtocol')->findOneBy([
            'orderId' => $orderId
        ]);

        if (!$measurementProtocol)
            return;

        $measurementProtocol->setDateCompleted(time());

        Database::getEM()->persist($measurementProtocol);
        Database::getEM()->flush();

    }
}