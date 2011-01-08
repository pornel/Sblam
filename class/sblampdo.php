<?php

abstract class SblamPDO extends PDO
{
    public static function create($dsn, $user, $pass)
    {
        if (substr($dsn,0,6) == 'pgsql:') return new SblamPgsqlPDO($dsn,$user,$pass);
        return new SblamMysqlPDO($dsn,$user,$pass);
    }

    function prepareExecute($q,array $data)
    {
        $statement = $this->prepare($q);
		if (!$statement) throw new PDOException(implode(',',$this->errorInfo().'// '.$q));
		if (!$statement->execute($data)) throw new PDOException(implode(',',$statement->errorInfo().'// '.$q));
		return $statement;
    }

    abstract function getTables();
    abstract function getProcesslist();
}

class SblamMysqlPDO extends SblamPDO
{
    function getTables()
    {
        return $this->query("/*maxtime10*/SHOW table status")->fetchAll(PDO::FETCH_ASSOC);
    }

    function getProcesslist()
    {
        return $this->query("/*maxtime2*/SHOW processlist")->fetchAll(PDO::FETCH_ASSOC);
    }
}

class SblamPgsqlPDO extends SblamPDO
{
    function getTables()
    {
        return array();
    }

    function getProcesslist()
    {
        return array();
    }
}