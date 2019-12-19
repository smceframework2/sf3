<?php

namespace EF2\Process;

/**
 * Multi-thread / task manager
 */
class Thread
{
	/**
	 * Assoc array of pid with active threads
	 * @var array
	 */
	protected $_activeThreads = [];
	/**
	 * Maximum number of child threads that can be created by the parent
	 * @var int
	 */
	protected $maxThreads = 5;

	/**
	 * Class constructor
	 *
	 * @param int $maxThreads Maximum number of child threads that can be created by the parent
	 */
	public function __construct($maxThreads = 5)
	{
		$this->maxThreads = $maxThreads;
	}

	/**
	 * Start the task manager
	 *
	 * @param AbstractTask $task Task to start
	 *
	 * @return void
	 */
	public function start(\Closure $func)
	{
		$pid = pcntl_fork();
		if ($pid == -1) {
			throw new \Exception('[Pid:' . getmypid() . '] Could not fork process');
		} // Parent thread
		elseif ($pid) {
			$this->_activeThreads[$pid] = true;

			// Reached maximum number of threads allowed
			if ($this->maxThreads == count($this->_activeThreads)) {
				// Parent Process : Checking all children have ended (to avoid zombie / defunct threads)
				while (!empty($this->_activeThreads)) {
					$endedPid = pcntl_wait($status);
					if (-1 == $endedPid) {
						$this->_activeThreads = [];
					}
					unset($this->_activeThreads[$endedPid]);
				}
			}
		} // Child thread
		else {
			$func();

			posix_kill(getmypid(), 9);
		}
		pcntl_wait($status, WNOHANG);
	}
}