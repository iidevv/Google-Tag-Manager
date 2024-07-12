<?php

namespace Iidev\GoogleTagManager\Core;

use XLite\Core\Config;
use XLite\InjectLoggerTrait;
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

    protected function isSuccessfulCode($code)
    {
        return in_array((int) $code, [200, 201, 202, 204], true);
    }

    public function event($data)
    {
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
        // $data = json_encode($data, JSON_NUMERIC_CHECK);

        $this->getLogger('Measurement Protocol')->debug(__FUNCTION__ . 'Request. Initial data', [
            $data
        ]);

        $url = $this->getGAUrl();

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

        $this->getLogger('Measurement Protocol')->debug(__FUNCTION__ . 'Response', [
            $url,
            $response->code,
            $response ? $response->headers : 'empty',
            $response ? $response->body : 'empty',
            $request->getErrorMessage(),
        ]);

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
}
