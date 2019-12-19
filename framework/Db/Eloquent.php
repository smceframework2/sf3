<?php

namespace EF2\Db;
use Illuminate\Database\Capsule\Manager as Capsule;
use EF2\Db\Eloquent\Criteria;

class Eloquent
{
    public $capsule;

    /*
     *
     *
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'database',
    'username'  => 'root',
    'password'  => 'password',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',

     */
    public function __construct($connectingArr)
    {
	    $this->capsule = new Capsule;
	    $this->capsule->addConnection($connectingArr);


    }


	public function connect()
    {
	    // Make this Capsule instance available globally via static methods... (optional)
	    $this->capsule->setAsGlobal();

	    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
	    $this->capsule->bootEloquent();

	    return $this->capsule;
    }

	public static function createCommand($command,...$params)
	{
		return Capsule::select($command,$params);
	}
}