<?php

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $dsn = DB_DRIVER . ':' . DB_NAME;
        $this->pdo = new PDO($dsn);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}