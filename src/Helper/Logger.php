<?php

namespace Iidev\GoogleTagManager\Helper;


class Logger extends \XLite\Logger
{
    const TYPE = 'MeasurementProtocolLogger';


    /**
     * Runtime id
     *
     * @var string
     */
    protected static $runtimeId;

    protected static $filesRepositories = array(
        LC_DIR_COMPILE => 'compiled classes repository',
        LC_DIR_ROOT => 'X-Cart root',
    );

    public static function getLogPath()
    {
        $type = static::TYPE;

        $dir = static::getCustomLogPath($type);
        $fileName = static::getCustomLogFileName($type);
        $path = $dir . $fileName;

        return $path;
    }

    public static function isLogExist()
    {
        $path = static::getLogPath();
        return file_exists($path) && filesize($path) > 0;
    }

    public static function getCustomLogFileName($type)
    {
        return $type . '.' . date('Y-m-d') . '.log';
    }

    public static function getCustomLogPath($type)
    {
        $path = date('Y/m');

        return LC_DIR_LOG . $path . LC_DS;
    }

    public static function logMessage($message, $useBackTrace = false, $backTraceSlice = 2)
    {
        $type = static::TYPE;

        $dir = static::getCustomLogPath($type);
        $fileName = static::getCustomLogFileName($type);

        $path = $dir . $fileName;

        \Includes\Utils\FileManager::mkdirRecursive($dir);

        $header = static::getLogFileHeader();

        if (!file_exists($path) || strlen($header) > filesize($path)) {
            @file_put_contents($path, $header);
        }

        if (!is_string($message)) {
            $message = var_export(static::prepareData($message), true);
        }

        $message = trim('[' . @date('H:i:s.u') . '] ' . $message) . PHP_EOL
            . 'Runtime id: ' . static::getRuntimeId() . PHP_EOL
            . 'SAPI: ' . PHP_SAPI . '; '
            . 'IP: ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'n/a') . PHP_EOL
            . (\XLite\Core\Request::getInstance()->isBot() ? 'Is BOT: true' . PHP_EOL : '')
            . 'URI: ' . static::getCurrentURIForLog() . PHP_EOL
            . 'Method: ' . (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'n/a') . PHP_EOL;

        // Add debug backtrace
        if ($useBackTrace) {
            $backTrace = static::getBackTrace($backTraceSlice);
            $message .= 'Backtrace:' . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $backTrace) . PHP_EOL;
        }

        $message .= PHP_EOL . '-------------------------------------------' . PHP_EOL;

        @file_put_contents($path, $message, FILE_APPEND);

        return $path;
    }


    protected static function detectClassName($class)
    {
        return get_class($class);
    }

    protected static function getBackTraceArgs(array $l)
    {
        $args = array();
        if (!isset($l['args'])) {
            $l['args'] = array();
        }

        foreach ($l['args'] as $arg) {

            if (is_bool($arg)) {
                $args[] = $arg ? 'true' : 'false';

            } elseif (is_int($arg) || is_float($arg)) {
                $args[] = $arg;

            } elseif (is_string($arg)) {
                if (is_callable($arg)) {
                    $args[] = 'lambda function';

                } else {
                    $args[] = '\'' . $arg . '\'';
                }

            } elseif (is_resource($arg)) {

                $args[] = (string)$arg;

            } elseif (is_array($arg)) {
                if (is_callable($arg)) {
                    $args[] = 'callback ' . static::detectClassName($arg[0]) . '::' . $arg[1];

                } else {
                    $args[] = 'array(' . count($arg) . ')';
                }

            } elseif (is_object($arg)) {
                if (
                    is_callable($arg)
                    && class_exists('Closure')
                    && $arg instanceof \Closure
                ) {
                    $args[] = 'anonymous function';

                } else {
                    $args[] = 'object of ' . static::detectClassName($arg);
                }

            } elseif (!isset($arg)) {
                $args[] = 'null';

            } else {
                $args[] = 'variable of ' . gettype($arg);
            }
        }

        return '(' . implode(', ', $args) . ')';
    }

    protected static function prepareBackTrace(array $backTrace, $slice = 0)
    {
        $patterns = array_keys(static::$filesRepositories);
        $placeholders = preg_replace('/^(.+)$/Ss', '<\1>/', array_values(static::$filesRepositories));

        $slice = max(0, $slice) + 1;

        $trace = array();

        foreach ($backTrace as $l) {

            if (0 < $slice) {
                $slice--;

            } else {

                $parts = array();

                if (isset($l['file'])) {

                    $parts[] = 'file ' . str_replace($patterns, $placeholders, $l['file']);

                } elseif (isset($l['class'], $l['function'])) {

                    $parts[] = 'method ' . $l['class'] . '::' . $l['function'] . static::getBackTraceArgs($l);

                } elseif (isset($l['function'])) {

                    $parts[] = 'function ' . $l['function'] . static::getBackTraceArgs($l);

                }

                if (isset($l['line'])) {
                    $parts[] = $l['line'];
                }

                if ($parts) {
                    $trace[] = implode(' : ', $parts);
                }
            }
        }

        return $trace;
    }

    public static function getBackTrace($slice = 2)
    {
        return static::prepareBackTrace(debug_backtrace(false), $slice);
    }

    protected static function getDestLogPath()
    {
        return LC_DIR_LOG . date('Y/m') . LC_DS . static::TYPE . '.log.' . date('Y-m-d') . '.php';
    }

    public static function rotateLog()
    {
        if (static::isLogExist()) {
            $path = static::getLogPath();
            $moveTo = static::getDestLogPath();
            rename($path, $moveTo);
        }
    }

    protected static function getLogFileHeader()
    {
        return '<' . '?php die(); ?' . '>' . PHP_EOL;
    }

    protected static function prepareData($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = static::prepareData($v);
            }

        } elseif (is_object($data)) {
            $data = \Doctrine\Common\Util\Debug::export($data, 2);
        }

        return $data;
    }

    protected static function getRuntimeId()
    {
        if (!isset(static::$runtimeId)) {
            static::$runtimeId = hash('md4', uniqid('runtime', true), false);
        }

        return static::$runtimeId;
    }

    protected static function getCurrentURIForLog()
    {
        $result = '';

        if (isset($_SERVER['REQUEST_URI'])) {
            $result .= rtrim($_SERVER['REQUEST_URI'], '?&');
        }

        if ($result && \XLite\Core\Request::getInstance()->isPost()) {
            $paramsQuery = parse_url($result, PHP_URL_QUERY);
            $path = parse_url($result, PHP_URL_PATH);
            parse_str($paramsQuery, $params);

            if (!isset($params['target'])) {
                $params['target'] = \XLite\Core\Request::getInstance()->target;
            }

            if (!isset($params['action'])) {
                $params['action'] = \XLite\Core\Request::getInstance()->action;
            }

            $result = $path . '?' . http_build_query($params, null, '&');
        }

        return $result;
    }

}