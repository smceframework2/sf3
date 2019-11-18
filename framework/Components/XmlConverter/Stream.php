<?php

namespace SF3\Components\XmlConverter;


class Stream
{
	private $data = null;
	private $line = 1;

	public function __construct($data)
	{
		$this->data = $data;
	}

	public function matches($string, $modifier = '')
	{
		$stream = substr($this->data, 0, strlen($string));
		if (strpos($modifier, 'i') !== false)
		{
			$stream = strtolower($stream);
			$string = strtolower($string);
		}
		return $stream == $string;
	}

	public function current()
	{
		return $this->data[0];
	}

	public function next($length = 1)
	{
		$c = substr($this->data, 0, $length);
		$this->data = substr($this->data, $length);

		for ($i = 0; $i < strlen($c); ++$i)
		{
			if ($c[$i] == "\n")
			{
				$this->line++;
			}
		}
		return $c;
	}

	public function flush()
	{
		$r = $this->data;
		$this->data = null;

		return $r;
	}

	public function isEmpty()
	{
		return empty($this->data);
	}

	public function line()
	{
		return $this->line;
	}

	public function readTo($char)
	{
		$result = '';
		while ($this->current() != $char)
		{
			$result .= $this->next();
		}

		return $result;
	}
}