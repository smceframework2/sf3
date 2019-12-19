<?php

namespace EF2\Db;


use MongoClient;
use MongoConnectionException;

class MongoCli
{

	private static $mongo;
	private static $conn=false;
	private static $connectStr;
	private static $dbname;

	/*
	 *
	'host'      => 'localhost',
	'port       => 27017,
	'database'  => 'database',
	'username'  => 'root',
	'password'  => 'password'

	 */
	public function __construct($connectingArr)
	{
		self::$dbname=$connectingArr["database"];
		self::$connectStr="mongodb://".$connectingArr["username"].":".$connectingArr["password"]."@localhost:".$connectingArr["port"]."/".$connectingArr["database"];

	}


	public function connect()
	{
		self::$mongo=$this->getMongoClient(self::$connectStr);
	}


	public static function getDb()
	{
		return self::$dbname;
	}


	/**
	 * @param string $seeds
	 * @param array $options
	 * @param int $retry
	 * @return MongoClient
	 *
	 * retry connecttion
	 */
	private function getMongoClient($seeds = "", $retry = 7) {
		try {
			self::$conn=true;
			return new MongoClient($seeds);
		} catch(MongoConnectionException $e) {

			self::$conn=false;
		}
		if ($retry > 0) {
			return $this->getMongoClient($seeds, --$retry);
		}else{
			echo "mg conn error;";
			exit;
		}

	}

	/**
	 * @param $dbname
	 * @return mixed
	 *
	 * mongodb getdb
	 */
	public static function getMongo($dbname)
	{

		$mongo=self::$mongo->selectDB($dbname);
		if($mongo==$dbname)
		{
			return $mongo;
		}


	}
}