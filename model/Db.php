<?php

class Db {

   private static $connection;
   private static $settings = array(
       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
       PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
       PDO::ATTR_EMULATE_PREPARES => false,
   );

   public static function connect() {
      self::$connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD, self::$settings);
   }

   public static function query($query, $params = array()) {
      $return = self::$connection->prepare($query);
      $return->execute($params);
      return $return->fetchAll();
   }

   public static function queryOne($query, $params = array()) {
      $return = self::$connection->prepare($query);
      $return->execute($params);
      return $return->fetch();
   }

   public static function queryInsert($query, $params = array()) {
      $return = self::$connection->prepare($query);
      return $return->execute($params);
   }

   public static function queryUpdate($query, $params = array()) {
      $return = self::$connection->prepare($query);
      return $return->execute($params);
   }

   public static function queryDelete($query, $params = array()) {
      $return = self::$connection->prepare($query);
      return $return->execute($params);
   }

   public static function queryTransaction($queries = array(), $params = array()) {
      self::$connection->beginTransaction();
      try {
         foreach ($queries as $key => $query) {
            $result = self::$connection->prepare($query);
            $result->execute($params[$key]);
         }
         self::$connection->commit();
         return true;
      } catch (Exception $ex) {

         echo $ex->getMessage();
         self::$connection->rollback();
         return false;
      }
   }

}
