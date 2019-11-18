<?php

namespace SF3\Extensions\Log;

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
			'context'    => count($context) > 0 ? $context : '',
			'level'      => $level,
			'level_name' => $levelName,
			'channel'    => $this->name,
			'datetime'   => $ts,
			'extra'      => '',
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