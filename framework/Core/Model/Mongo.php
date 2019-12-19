<?php

namespace EF2\Core\Model;

use EF2\Db\MongoCli as M;
use MongoCollection;
use MongoDate;
use MongoId;

class Mongo
{

	private static $dbname;
	private static $tablename;
	private static $db;

	/**
	 * ServiceLogs constructor.
	 *
	 *
	 * dbname, db ve table ayarı
	 */
	public function __construct()
	{

	}

	private static function getTableName()
	{
		$class=get_called_class();
		$table=explode("\\",$class);
		$table=end($table);

		$cl=new $class;

		if(isset($cl->table))
		{
			return $cl->table;
		}
		return strtolower($table);
	}

	private function getCollection()
	{
		self::$dbname=M::getDb();
		self::$tablename=self::getTableName();
		self::$db=M::getMongo(self::$dbname);

		return new MongoCollection(self::$db, self::$tablename);
	}

	protected function table()
	{
		return $this->getCollection();
	}

	protected function newDate($timeStamp=null)
	{
		if($timeStamp==null)
		{
			$timeStamp=time();
		}
		return new MongoDate($timeStamp);
	}

	public function mongoId($id)
	{
		return new MongoId($id);
	}
}