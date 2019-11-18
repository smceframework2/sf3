<?php

namespace SF3\Model;

use Illuminate\Database\Eloquent\Model as Model;
use SF3\Model\Eloquent\Criteriainject;

class Eloquent extends Model
{

	public static function model()
	{
		return new Criteriainject(self::getTableName());
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



}