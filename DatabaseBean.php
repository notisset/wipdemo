<?php
require_once('conf.php');

class DatabaseBean{
    protected $tableName;
    protected $tableFields = ['id'];
    protected $tableProtectedFields = ['id'];

    public $id;

    /**
     * @var PDO
     */
    protected $database;

    public function __construct($values = null){
        if(!is_null($values)){
            $this->populate($values);
        }
        try{
            $this->database = new PDO( sprintf("mysql:host=%s;dbname=%s", DB_HOST, DB_NAME) , DB_USER, DB_PASS);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }catch (PDOException $e){
            throw $e;
        }
    }

    public function __destruct(){
        $this->database = null;
    }

    /**
     * @param $values[]
     * @return bool
     */
    public function populate($values){
        foreach($values as $field => $value){
            $_property = $field;
            if(property_exists($this, $_property)){
                $this->$_property = $value;
            }
        }
        return true;
    }

    /**
     * @return Post[]
     */
    public function all(){
        $_ret = [];
        $_sql = sprintf(
            'SELECT * FROM %s ORDER BY id DESC',
            $this->tableName
        );
        $_statement = $this->database->prepare($_sql);
        $_statement->execute(array($this->tableName));
        $_arr = $_statement->fetchAll();
        foreach($_arr as $_row){
            $_ret[] = new Post($_row);
        }
        return $_ret;
    }

    /**
     * @param $id
     * @return bool
     */
    public function find($id){
        $_sql = sprintf(
            'SELECT * FROM %s WHERE id = ? LIMIT 1',
            $this->tableName
        );
        $_statement = $this->database->prepare($_sql);
        try{
            $_statement->execute(array($id));
            $_fetched = $_statement->fetchAll();
            if(is_array($_fetched) && count($_fetched)){
                return $this->populate(array_pop($_fetched));
            }else{
                return false;
            }
        }catch (PDOException $e){
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function create(){
        if(property_exists($this, 'created_at')){
            $this->created_at = date('Y-m-d H:i:s');
        }
        $_sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->tableName,
            implode(',',array_keys($this->attributes())),
            implode(',', array_fill(0, sizeof($this->attributes()), '?'))
        );
        $_statement = $this->database->prepare($_sql);
        try{
            $_statement->execute(array_values($this->attributes()));
            $this->id = $this->database->lastInsertId();
            return true;
        }catch (PDOException $e){
            throw $e;
        }

    }

    /**
     * @return bool
     */
    public function save(){
        if(!$this->isLinked()){
            return false;
        }
        if(property_exists($this, 'updated_at')){
            $this->updated_at = date('Y-m-d H:i:s');
        }
        $_rr = [];
        foreach($this->attributesEditable() as $field => $value){
            $_rr[] = "{$field} = ?";
        }
        $_sql = sprintf(
            'UPDATE %s SET %s WHERE id = ?',
            $this->tableName,
            implode(',',$_rr)
        );
        $_statement = $this->database->prepare($_sql);
        try{
            $_statement->execute(array_merge(array_values($this->attributesEditable()),array($this->id)));
            return true;
        }catch (PDOException $e){
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function delete(){
        if(!$this->isLinked()){
            return false;
        }
        $_sql = sprintf(
            'DELETE FROM %s WHERE id = ?',
            $this->tableName
        );
        $_statement = $this->database->prepare($_sql);
        try{
            $_statement->execute(array($this->id));
            return true;
        }catch (PDOException $e){
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function attributes(){
        $_ret =[];
        foreach($this->tableFields as $field){
            $_ret[$field] = $this->$field;
        }
        return $_ret;
    }

    /**
     * @return array
     */
    public function attributesEditable(){
        $_ret =[];
        foreach($this->tableFields as $field){
            if(in_array($field, $this->tableProtectedFields)){
                continue;
            }
            $_ret[$field] = $this->$field;
        }
        return $_ret;
    }

    /**
     * @return bool
     */
    public function isLinked(){
        return isset($this->id);
    }
} 