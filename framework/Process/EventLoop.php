<?php

namespace EF2\Process;

use Closure;
use EF2\Process\Thread;

class EventLoop
{
	private $intervalProcess = [];
	private $timerProcess    = [];
	private $thread;

	public function __construct($maxThreads = 5)
	{
		$this->thread = new Thread($maxThreads);
	}

	public function addPeriodicTimer($isThread, $interval, Closure $func)
	{
		$this->intervalProcess[] = [
			"isThread"    => $isThread,
			"interval"    => $interval,
			"func"        => $func,
			"processtime" => $this->millisecond(),
		];
	}

	public function addTimer($isThread, $interval, Closure $func)
	{
		$this->timerProcess[] = [
			"isThread"    => $isThread,
			"interval"    => $interval,
			"func"        => $func,
			"processtime" => $this->millisecond(),
		];
	}

	public function run()
	{
		$e = $this;

		while (1) {
			foreach ($this->intervalProcess as $key => $value) {
				$e->ieventProcess($key);
			}

			foreach ($this->timerProcess as $key => $value) {
				$e->teventProcess($key);
			}
		}
	}

	private function ieventProcess($key)
	{
		if ($this->millisecond() >= $this->intervalProcess[$key]["processtime"] + $this->intervalProcess[$key]["interval"] * 1000) {
			$this->intervalProcess[$key]["processtime"] = $this->millisecond();
			$func                                       = $this->intervalProcess[$key]["func"];

			if ($this->intervalProcess[$key]["isThread"]) {
				$this->thread->start(function () use ($func) {
					$func();
				});
			} else {
				$func();
			}
		}
	}

	private function teventProcess($key)
	{
		if ($this->millisecond() >= $this->timerProcess[$key]["processtime"] + $this->timerProcess[$key]["interval"] * 1000) {
			$func = $this->timerProcess[$key]["func"];

			if ($this->timerProcess[$key]["isThread"]) {
				unset($this->timerProcess[$key]);
				$this->thread->start(function () use ($func) {
					$func();
				});
			} else {
				unset($this->timerProcess[$key]);
				$func();
			}
		}
	}

	private function millisecond()
	{
		return round(microtime(true) * 1000);
	}
}