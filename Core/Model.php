<?php

namespace Core;

use \PDO;
use PDOException;
use stdClass;
use Exception;

class Model
{
    
    protected $pdo;
    protected $table;
    public $query;
    public $data;
    protected $bind = array();
    /** @var string $primary table primary key field */
    private $primary;
    /** @var array $required table required fields */
    private $required;
    /** @var string */
    protected $params;
    /** @var string */
    protected $group;
    /** @var string */
    protected $order;
    /** @var int */
    protected $limit;
    /** @var int */
    protected $offset;
    /** @var \PDOException|null */
    public $fail;


    public function __construct(array $required, $primary = 'id', $table = null){
        global $pdo;
        global $pdo;
        $this->pdo = $pdo;
        $this->required = $required;
        $this->primary = $primary;
        $this->table = explode("\\\\", addslashes(strip_tags(trim(strtolower(get_class($this))))))[1];
        if ($table != null) $this->table = addslashes(strip_tags(trim(strtolower(get_class($table)))));
        if (empty($this->data)) $this->data = new \stdClass();

    }

    private function filter(array $data)
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }
        return $filter;
    }


    protected function required()
    {

        foreach ($this->required as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }
        return true;
    }

    public function get($all = true){
        $retorno = array();
        $sql = $this->pdo->prepare("SELECT * FROM $this->table");
        $sql->execute();
        if ($sql->rowCount() > 0):
            if ($all) $this->data = $sql->fetchAll(PDO::FETCH_CLASS, static::class);
            if (!$all) $this->data = $sql->fetchObject(static::class);
        endif;
        return $this;
    }

    public function find($where = null, $rawWhere = false, array $params = array()){
        if ($where) {
            if ($rawWhere == true){
                $this->query = "SELECT * FROM {$this->table} WHERE {$where}";
                foreach ($params as $param => $value){
                    $this->bind[$param] = $value;
                }
            } else {
                $culumns = implode(", ", array_keys($where));
                $bind = array();
                $this->query = "SELECT * FROM {$this->table} WHERE ";
                $count = 0;
                foreach ($where as $culumn => $value) {
                    if ($count > 0 && $count != count($where)) $this->query .= " AND ";
                    $this->bind[":" . $culumn] = $value;
                    $this->query .= $culumn . " = :" . $culumn;
                    $count++;

                }
            }
            return $this;
        }
        $this->query = "SELECT * FROM {$this->table} ";
        return $this;
    }

    public function count(){
        $sql = $this->pdo->query("SELECT * FROM {$this->table}");
        $sql->execute();
        return $sql->rowCount();

    }

    public function group(string $column)
    {
        $this->group = " GROUP BY {$column}";
        return $this;
    }

    public function order(string $column)
    {
        $this->order = " ORDER BY {$column}";

        return $this;
    }

    public function limit(int $limit)
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    public function offset(int $offset)
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    protected function safe()
    {
        $safe = (array)$this->data;
        unset($safe[$this->primary]);

        return $safe;
    }

    public function save()
    {
        $primary = $this->primary;
        $id = null;

        try {

            /** Update */
//            if (!empty($this->data->$primary)) {
//                $id = $this->data->$primary;
//                $this->update($this->safe(), $this->primary . " = :id", "id={$id}");
//            }

            /** Create */
            if (empty($this->data->$primary)) {
                $id = $this->create($this->safe());
            }

            if (!$id) {
                return false;
            }

            $this->data = $this->findById($id);
            return true;
        } catch (\Exception $exception) {
            $this->fail = $exception;
            return false;
        }
    }
    public function findById(int $id)
    {
        $find = $this->find([$this->primary => $id]);
        return $find->fetch();
    }

    public function fetch($all = false)
    {
        try {
            $sql = $this->pdo->prepare($this->query . $this->group . $this->order . $this->limit . $this->offset);
            if(count($this->bind) > 0){
                foreach ($this->bind as $bind => $value){
                    $sql->bindValue($bind, $value);
                }
            }
            $sql->execute();
            if (!$sql->rowCount()) {
                return null;
            }
            if ($all) {
                return $sql->fetchAll(PDO::FETCH_CLASS, static::class);
            }
            return $sql->fetchObject(static::class);
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    public function delete(array $data){
        if(!is_array($data)) return false;

        $retorno = array();
        $sql = "DELETE FROM $this->table WHERE";
        $count = 0;
        foreach ($data as $column => $value){
            if($count > 0 && $count != count($data)) $sql .= " AND ";
            $sql .= " ".$column." = :".$column;
            $count++;
        }
        $sql = $this->pdo->prepare($sql);
        foreach ($data as $column => $value){
            $sql->bindValue(":$column", $value);
        }
        if ($sql->execute()) return true;

        return false;
    }
    public function create(array $data)
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));
            $sql = $this->pdo->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");
            $sql->execute($this->filter($data));
            return $this->pdo->lastInsertId();
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            echo $this->fail;
            return null;
        }
    }

    public function validId($id){
        if (is_int($id)) {
            $promo = $this->findById($id);
            var_dump($promo);
            if (!is_null($promo)) return true;
        }
            return false;
    }

    public function where(array $data, $all = false){
        if(!is_array($data)) return array();
        $retorno = array();

        $sql = "SELECT * FROM $this->table WHERE";
        $count = 0;
        foreach ($data as $column => $value){
            if($count > 0 && $count != count($data)) $sql .= " AND ";
            $sql .= " ".$column." = :".$column;
            $count++;
        }
        $sql = $this->pdo->prepare($sql);
        foreach ($data as $column => $value){
            $column = ":".$column;
            $sql->bindValue($column, $value);
        }
        $sql->execute();

        if ($sql->rowCount() > 0):
            if($all) return $sql->fetchAll(PDO::FETCH_CLASS, static::class);
            if(!$all) return $sql->fetchObject(static::class);
        endif;
        return array();
    }

}


?>