<?php
class User
{
    
private $id;

public function __construct($id) 
{
    $this->id = $id;
}  


public function getDataId()
{
    $result = Db::queryOne('SELECT username, last_ip FROM auth.account WHERE id=?', array($id));
    return $result;    
}

public function returnLogin()
{
    $data = Db::queryOne('SELECT username, credits FROM auth.account WHERE id=?', array($this->id));
    return $data;
}

public function returnLastIp()
{
    $data = Db::queryOne('SELECT last_ip FROM auth.account WHERE id=?', array($this->id));
    return $data['last_ip'];
}

}