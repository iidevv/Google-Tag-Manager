<?php

namespace Iidev\GoogleTagManager\Core;

use XLite\Core\Config;
use XLite\InjectLoggerTrait;
use Iidev\GoogleTagManager\Helper\Logger;
use Exception;

class API
{
    use InjectLoggerTrait;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getGAUrl()
    {
        $apiSecret = Config::getInstance()->Iidev->GoogleTagManager->mp_secret_key;
        $measurementId = Config::getInstance()->Iidev->GoogleTagManager->ga_public_key;

        return "https://www.google-analytics.com/mp/collect?api_secret=" . $apiSecret . "&measurement_id=" . $measurementId;
    }

    /**
     * @return string
     */
    public function getDebugGAUrl()
    {
        $apiSecret = Config::getInstance()->Iidev->GoogleTagManager->mp_secret_key;
        $measurementId = Config::getInstance()->Iidev->GoogleTagManager->ga_public_key;

        return "https://www.google-analytics.com/debug/mp/collect?api_secret=" . $apiSecret . "&measurement_id=" . $measurementId;
    }

    public function isDebugMode() {
        return (bool) Config::getInstance()->Iidev->GoogleTagManager->is_debug;
    }

    protected function isSuccessfulCode($code)
    {
        return in_array((int) $code, [200, 201, 202, 204], true);
    }

    public function event($data)
    {
        if($this->isDebugMode()) {
            $this->doDebugRequest($data);
        }

        $result = $this->doRequest($data);

        return $this->isSuccessfulCode($result->code);
    }

    /**
     * @param array $data
     *
     * @return \PEAR2\HTTP\Request\Response
     * @throws \Exception
     */
    protected function doRequest($data = [])
    {
        $data = json_encode($data);

        $url = $this->getGAUrl();

        $request = new \XLite\Core\HTTP\Request($url);

        $request->verb = "POST";

        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Content-Type', 'application/json');


        $request->body = $data;

        $response = $request->sendRequest();

        if ($response->code === 409) {
            return $response;
        }

        if (!$response || !$this->isSuccessfulCode($response->code)) {
            $this->getLogger('Measurement Protocol')->error(__FUNCTION__ . 'Response error', [
                $response->body,
                $response->code
            ]);
        }

        return $response;
    }

    /**
     * @param array $data
     */
    protected function doDebugRequest($data = [])
    {
        $data = json_encode($data);

        $this->getLogger('Measurement Protocol')->debug(__FUNCTION__ . 'Request. Initial data', [
            $data
        ]);

        $url = $this->getDebugGAUrl();

        $request = new \XLite\Core\HTTP\Request($url);

        $request->verb = "POST";

        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Content-Type', 'application/json');


        $request->body = $data;

        $this->getLogger('Measurement Protocol')->debug(__FUNCTION__ . 'Request', [
            $url,
            $request->headers,
            $request->body,
        ]);

        $response = $request->sendRequest();

        Logger::logMessage($response);
    }
}
