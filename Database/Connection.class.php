<?php

class Connection
{
    private static $connection;

    public function connect()
    {
        //read parameters in the ini configuration file

        $params = parse_ini_file('database.ini');

        if($params === false)
        {
            throw new \Exception("Error reading database configuration file");
        }

        //connect to the postgresql database

        $conStr = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $params['host'],
            $params['port'],
            $params['database'],
            $params['user'],
            $params['password']
        );

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    /**
     * return an instance of the connection object
     * @return type
     */
    public static function get()
    {
        if(null === static::$connection)
        {
            static::$connection = new static();
        }

        return static::$connection;
    }

    protected function __construct()
    {
        
    }

    public function __clone()
    {
        
    }


    public function __wakeup()
    {
        
    }
}