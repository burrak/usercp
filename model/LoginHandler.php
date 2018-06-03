<?php
class LoginHandler
{
    private $login;
    private $heslo;
    
    public function __construct($login, $heslo) 
    {
        $this->login = $login;
        $this->heslo = $heslo;
    }
    
    private function getDataLogin($login)
    {
    return Db::queryOne('SELECT * FROM auth.account WHERE username=?', array($login));   
    }
    
    private function checkEmail($email) {
       return Db::queryOne('SELECT id FROM auth.account WHERE reg_mail=?', array($email));
    }
    
    private function hashPassword($login, $heslo) {
       $login = strtoupper($login);
       $heslo = strtoupper($heslo);
       $hash = sha1($login.':'.$heslo);
       $hash = strtoupper($hash);
       return $hash;
    }

    private function checkPassword($login, $heslo, $heslo_hash)
    {
        $sha_heslo = $this->hashPassword($login, $heslo);  
     
        if($sha_heslo == $heslo_hash)
        {
            return true;
        }
        else 
        {
            return false;        
        }
    }
    
    public function checkLogin()
    {
        $loginDb = $this->getDataLogin($this->login);
        $passCheck = $this->checkPassword($this->login, $this->heslo, $loginDb['sha_pass_hash']);
        
        if($passCheck=='TRUE')
        {
            $_SESSION['id'] = $loginDb['id'];
            return true;
        }
        else
        {
            return 'Špatné heslo';
        }
    }
    
    public function registrace($login, $heslo, $heslo_confirm, $email) {
       $accountcheck = $this->getDataLogin($login);
       $emailcheck = $this->checkEmail($email);

       
       if(isset($accountcheck['id'])) {
          return array('view_content' => 'registrace', 'view_alert' => 'error', 'alert' => 'Uživatel s tímto jménem již existuje');
       } 
       if(isset($emailcheck['id'])) {
          return array('view_content' => 'registrace', 'view_alert' => 'error', 'alert' => 'Tento e-mail již je registrovaný');
       }
       if($heslo != $heslo_confirm) {
          return array('view_content' => 'registrace', 'view_alert' => 'error', 'alert' => 'Hesla nesouhlasí');
       }
       if((!isset($accountcheck['id'])) && (!isset($emailcheck['id']))) {
          $hash = $this->hashPassword($login, $heslo);
          $query = Db::queryInsert('INSERT INTO auth.account (username, sha_pass_hash, reg_mail) VALUES (:username, :sha, :email)', array(':username' => $login, ':sha' => $hash, 'email' => $email));
          if($query == TRUE) {
             return array('view_content' => 'login', 'view_alert' => 'success', 'alert' => 'Registrace proběhla úspěšně, nyní se můžeš přihlásit.');
          }
          if($query == FALSE) {
             return array('view_content' => 'registrace', 'view_alert' => 'error', 'alert' => 'Něco se pokazilo. Zkus to znovu.');
          }
       }
    }
}
