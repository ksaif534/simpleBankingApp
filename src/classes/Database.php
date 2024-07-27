<?php

namespace App\classes;

class Database{
    protected $pdo;
    protected $dsn;
    public $filename;

    public function __construct($pdo, $dsn) {
        $this->pdo = $pdo;
        $this->dsn = $dsn;
        $this->filename = 'no file available';
    }

    public function getPDO(){
        return $this->pdo;
    }

    public function getData($tableName){
        $query  = 'SELECT * FROM ' . $tableName;
        $stmt   = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function storeAdminCliData($optionName,$optionValue){
        $readExistingData   = 'SELECT * FROM users WHERE role IS NULL';
        $readStmt           = $this->getPDO()->prepare($readExistingData);
        $readStmt->execute();
        $existingData       = $readStmt->fetchAll();
        if (count($existingData) > 0) {
            $latestData     = $existingData[count($existingData) - 1];
            $query          = 'UPDATE users SET '.$optionName.' = :'.$optionName.' WHERE id = '.$latestData['id'].'';
            $params         = [
                ':'.$optionName => $optionValue
            ];
        }else{
            $query          = 'INSERT INTO users('.$optionName.') VALUES(:'.$optionName.')';
            $params         = [
                ':'.$optionName  => $optionValue
            ];
        }
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute($params);
    }

    public function getConnection() {
        try {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
    }

    public function getFileName(){
        return $this->filename;
    }
}

?>