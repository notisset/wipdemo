<?php
require_once('DatabaseBean.php');

class Post extends DatabaseBean {

    protected $tableName = "post";
    protected $tableFields = ['id','content','created_at'];
    protected $tableProtectedFields = ['id'];

    public $content;
    public $created_at;
}