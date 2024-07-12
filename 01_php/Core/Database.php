<?php

namespace Core;
use PDO;
use PDOException;

class Database {

    public $connection;
    public $statement;

    public function __construct($config, $username='root', $password='') {
        // $host = '127.0.0.1';
        // $port = '3306';
        // $db = '01PHPLrn';
        // $user = 'root';
        // $pass = '';
        // $charset = 'utf8';

        // $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

        $dsn='mysql:'.http_build_query($config, '', ';');

        $options = [
            // PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $username,  $password, $options);
            // echo "Successfully connected to the database<br>";
        } catch (PDOException $e) {
            echo "Failed to connect to the database: " . $e->getMessage() . "<br>";
            exit;
        }
    }

    public function query($query, $params=[]) {
        try {
            $this->statement = $this->connection->prepare($query);
            $this->statement->execute($params);
            return $this;
        } catch (PDOException $e) {
            echo "Failed to execute query: " . $e->getMessage() . "<br>";
            return [];
        }
    }

    public function find(){
        return $this->statement->fetch();
    }

    public function findOrFail(){
        $result=$this->find();

        if(!$result){
            abort();
        }

        return $result;

    }

    public function fetch(){
        return $this->statement->fetch();
    }

    public function fetchAll(){
        return $this->statement->fetchAll();
    }


};
