<?php
namespace App\Models;

use PDO;

class DB extends PDO
{
    const HOST = 'localhost';
    const PORT = '3306';
    const USER = 'login';
    const PASS = 'password';
    const DB_NAME = 'vse';

    private static $_db;
    private static $stmt;
    private static $error;

    private static $tableName;

    public static function getDBConnect(){
        $dsn = "mysql:host=". self::HOST . ";port=" . self::PORT . ";dbname=" . self::DB_NAME ;
        $option  = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

        try{
            self::$_db = new PDO($dsn, self::USER, self::PASS, $option);
        }
        catch(\PDOException $e) {
            echo $e->getMessage();
            self::$error = $e->getMessage();
        }

        return self::$_db;
    }

    //get all goods from table
    public static function getAll($table){
        $goodsData = array();
        
        self::$stmt = self::getDBConnect()->prepare('SELECT * FROM '.$table);
        self::$stmt->execute();
        while ($row = self::$stmt->fetchAll(PDO::FETCH_ASSOC)) {
            $goodsData = $row;
        }
        
        return $goodsData;
    }

    public static function insertData($table, $total){
        $ids = $_POST['ids'];
        $status = 'new';

        self::$stmt = self::getDBConnect()->prepare('insert into '.$table.' values(NULL, now(), :ids, :total, :status)');
        self::$stmt->bindValue(':ids', $ids);
        self::$stmt->bindValue(':total', $total);
        self::$stmt->bindValue(':status', $status);
        self::$stmt->execute();
        $lastID = DB::getDBConnect()->lastInsertId();

        return $lastID;
    }

    public static function generateData($table){
        $data_array = array();

        for($i=0;$i<20;$i++){
            $randomSum = round(1 + mt_rand() / mt_getrandmax() * (5000 - 1), 2);
            $data_array[] = array('NULL', 'Thing'.($i+1), $randomSum);
        }

        self::getDBConnect()->beginTransaction();
        self::$stmt = self::getDBConnect()->prepare('insert into '.$table.' values(NULL, :thing, :price)');
        foreach($data_array as $insert_data){
            self::$stmt->bindParam(':thing', $insert_data[1]);
            self::$stmt->bindParam(':price', $insert_data[2]);
            self::$stmt->execute();
        }
        self::getDBConnect()->commit();
        
        return $result;
    }

    public static function getTotal($table){
        $ids = $_POST['ids'];
        self::$stmt = self::getDBConnect()->prepare('SELECT sum(price) as total FROM '.$table.' WHERE id in('.$ids.')');
        self::$stmt->execute();
        $data = self::$stmt->fetch(PDO::FETCH_OBJ);
        
        return $data->total;
    }
    
    public static function getOrder($table, $orderId, $total){
        self::$stmt = self::getDBConnect()->prepare('SELECT * FROM '.$table.' WHERE id=:id and total=:total and status=:status');
        self::$stmt->bindValue(':id', $orderId, PDO::PARAM_INT);
        self::$stmt->bindValue(':total', $total);
        self::$stmt->bindValue(':status', "new", PDO::PARAM_STR);
        self::$stmt->execute();
        
        return self::$stmt->rowCount();
    }
    
    public static function paidOrder($table, $orderId){
        self::$stmt = self::getDBConnect()->prepare('UPDATE '.$table.' set status=:status WHERE id=:id');
        self::$stmt->bindValue(':id', $orderId, PDO::PARAM_INT);
        self::$stmt->bindValue(':status', "paid", PDO::PARAM_STR);
        $result = self::$stmt->execute();
        
        return $result;
    }
}

