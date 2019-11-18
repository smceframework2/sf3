<?php

namespace SF3\Extensions;

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{
    public function __construct(string $name, $handlers = [], $processors = [])
    {
        parent::__construct($name, $handlers, $processors);
    }

    public function addRecord($level, $message, array $context = [])
    {
        if (!$this->handlers) {
            $this->pushHandler(new StreamHandler('php://stderr', static::DEBUG));
        }

        $levelName = static::getLevelName($level);

        // check if any handler will handle this message so we can return early and save cycles
        $handlerKey = null;
        reset($this->handlers);
        while ($handler = current($this->handlers)) {
            if ($handler->isHandling(['level' => $level])) {
                $handlerKey = key($this->handlers);
                break;
            }

            next($this->handlers);
        }

        if (null === $handlerKey) {
            return false;
        }

        if (!static::$timezone) {
            static::$timezone = new \DateTimeZone(date_default_timezone_get() ? : 'UTC');
        }

        // php7.1+ always has microseconds enabled, so we do not need this hack
        if ($this->microsecondTimestamps && PHP_VERSION_ID < 70100) {
            $ts = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), static::$timezone);
        } else {
            $ts = new \DateTime(null, static::$timezone);
        }
        $ts->setTimezone(static::$timezone);

        $record = [
            'message'    => (string)$message,
            'context'    => $context,
            'level'      => $level,
            'level_name' => $levelName,
            'channel'    => $this->name,
            'datetime'   => $ts,
            'extra'      => [],
        ];

        foreach ($this->processors as $processor) {
            $record = call_user_func($processor, $record);
        }

        while ($handler = current($this->handlers)) {
            if (true === $handler->handle($record)) {
                break;
            }

            next($this->handlers);
        }

        return true;
    }
}

class Log
{
    protected static $instance;

    protected static $format = '[%datetime%] %channel%.%level_name%: %message%';

    /**
     * Method to return the Monolog instance
     *
     * @return \Monolog\Logger
     */
    static public function getLogger()
    {
        if (!self::$instance) {
            self::configureInstance();
        }

        return self::$instance;
    }

    /**
     * Configure Monolog to use a rotating files system.
     *
     * @return Logger
     */

    public static function setStorageFiFle($filename = '')
    {
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }
        $logger = new Logger('SF3');
        $logger->pushHandler($handler = new StreamHandler($filename, 5));
        $handler->setFormatter(new ColoredLineFormatter(null, self::$format));

        self::$instance = $logger;
    }

    protected static function configureInstance()
    {
        $dir = dirname(__FILE__) . '/../../storage/logs/';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $logger = new Logger('SF3');
        $logger->pushHandler($handler = new StreamHandler($dir . 'ef-' . date('Y-m-d') . '.log', LOGGER::INFO));
        $handler->setFormatter(new ColoredLineFormatter(null, self::$format));

        self::$instance = $logger;
    }

    /**
     * Get a default Monolog formatter instance.
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected static function getDefaultFormatter()
    {
        return new LineFormatter(null, null, true);
    }

    public static function debug($message, array $context = [])
    {
        self::getLogger()->addDebug($message, $context);
    }

    public static function info($message, array $context = [])
    {
        self::getLogger()->addInfo($message, $context);
    }

    public static function notice($message, array $context = [])
    {
        self::getLogger()->addNotice($message, $context);
    }

    public static function warning($message, array $context = [])
    {
        self::getLogger()->addWarning($message, $context);
    }

    public static function error($message, array $context = [])
    {
        self::getLogger()->addError($message, $context);
    }

    public static function critical($message, array $context = [])
    {
        self::getLogger()->addCritical($message, $context);
    }

    public static function alert($message, array $context = [])
    {
        self::getLogger()->addAlert($message, $context);
    }

    public static function emergency($message, array $context = [])
    {
        self::getLogger()->addEmergency($message, $context);
    }
}