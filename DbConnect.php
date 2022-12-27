<?php

class DbConnect
{
  private static $login = 'root';
  private static $password = '';
  private static $host = 'localhost';
  private static $db_name = 'users';
  

  private static function getDSN(){
    return "mysql:dbname=".self::$db_name.";host=".self::$host;
  }

  public static function getConnection(){
    return new PDO(self::getDSN(), self::$login, self::$password, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
  }

}
//echo DbConnect::$db_name;
//echo DbConnect::DB_NAME;
//$pdo = DbConnect::getConnection();
//var_dump($pdo);


function d($arr){
  echo '<pre>';
  print_r($arr);
  echo '</pre>';
}
function dd($arr){
  echo '<pre>';
  print_r($arr);
  echo '</pre>';
  die('Выполнение дальнейшего кода приостановлено');
}