<?php

class User extends LoginHandler {

   private $id;

   public function __construct($id) {
      $this->id = $id;
   }

   public function getDataId() {
      $result = Db::queryOne('SELECT * FROM auth.account WHERE id=?', array($this->id));
      return $result;
   }

   public function returnLogin() {
      $data = Db::queryOne('SELECT username FROM auth.account WHERE id=?', array($this->id));
      return $data['username'];
   }

   public function returnCredits() {
      $data = Db::queryOne('SELECT credits FROM auth.account WHERE id=?', array($this->id));
      return $data['credits'];
   }

   public function returnLastIp() {
      $data = Db::queryOne('SELECT last_ip FROM auth.account WHERE id=?', array($this->id));
      return $data['last_ip'];
   }

   public function changePassword($heslo_old, $heslo_new, $heslo_new_check) {
      $login_str = $this->getDataId($this->id);
      $check_password = $this->checkPassword($login_str['username'], $heslo_old, $login_str['sha_pass_hash']);
      if ($check_password != 'TRUE') {
         return array('view_alert' => 'error', 'alert' => 'Špatné heslo');
      } else {
         if ($heslo_new !== $heslo_new_check) {
            return array('view_alert' => 'error', 'alert' => 'Hesla nesouhlasí');
         } else {
            if ($heslo_old == $heslo_new) {
               return array('view_alert' => 'error', 'alert' => 'Nové heslo nemůže být stejné jako staré');
            } else {
               $password_hash = $this->hashPassword($login_str['username'], $heslo_new);
               $update_password = Db::queryUpdate('UPDATE auth.account SET sha_pass_hash=:password_hash WHERE id=:id', array('password_hash' => $password_hash, 'id' => $login_str['id']));
               if ($update_password == 'TRUE') {
                  return array('view_alert' => 'success', 'alert' => 'Heslo bylo změněno');
               }
            }
         }
      }
   }

   public function registrace($login, $heslo, $heslo_confirm, $email) {
      
   }

}
