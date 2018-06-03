<?php

class Postavy {

   const AT_LOGIN_RENAME = 1;
   const AT_LOGIN_RESET_SPELLS = 2;
   const AT_LOGIN_RESET_TALENTS = 4;
   const AT_LOGIN_CUSTOMIZE = 8;
   const AT_LOGIN_RESET_PET_TALENTS = 16;
   const AT_LOGIN_FIRST = 32;
   const AT_LOGIN_CHANGE_FACTION = 64;
   const AT_LOGIN_CHANGE_RACE = 128;

   private $account_id;

   public function __construct($id) {
      $this->account_id = $id;
   }

   private function raceHandler($rasa) {
      switch ($rasa) {
         case 1:
            return 'Human';
            break;

         case 2:
            return 'Orc';
            break;

         case 3:
            return 'Dwarf';
            break;

         case 4:
            return 'Night Elf';
            break;

         case 5:
            return 'Undead';
            break;

         case 6:
            return 'Tauren';
            break;

         case 7:
            return 'Gnome';
            break;

         case 8:
            return 'Troll';
            break;

         case 10:
            return 'Blood Elf';
            break;

         case 11:
            return 'Draenei';
            break;
      }
   }

   private function classHandler($class) {
      switch ($class) {
         case 1:
            return 'Warrior';
            break;

         case 2:
            return 'Paladin';
            break;

         case 3:
            return 'Hunter';
            break;

         case 4:
            return 'Rogue';
            break;

         case 5:
            return 'Priest';
            break;

         case 6:
            return 'Death Knight';
            break;

         case 7:
            return 'Shaman';
            break;

         case 8:
            return 'Mage';
            break;

         case 9:
            return 'Warlock';
            break;

         case 11:
            return 'Druid';
            break;
      }
   }

   public function getPostavy($race, $class) {
      $postavy = Db::query('SELECT * FROM characters.characters WHERE account=?', array($this->account_id));
      foreach ($postavy as $key => &$char) {
         if ($race == 1) {
            $char['race'] = $this->raceHandler($char['race']);
         }
         if ($class == 1) {
            $char['class'] = $this->classHandler($char['class']);
         }
      }
      return $postavy;
   }

   private function getJednaPostava($postava_id) {
      return Db::queryOne('SELECT * FROM characters.characters WHERE guid=? LIMIT 1', array($postava_id));
   }
   
   private function getSmazanePostavy($race, $class) {
      $smazane = Db::query('SELECT * FROM characters.characters WHERE deleteInfos_Account=?', array($this->account_id));
      foreach ($smazane as $key => &$char) {
         if ($race == 1) {
            $char['race'] = $this->raceHandler($char['race']);
         }
         if ($class == 1) {
            $char['class'] = $this->classHandler($char['class']);
         }
      }
      return $smazane;      
   }

   private function postavaCheck($postava_id, $original_page = 'detail', $je_smazana = 0) {
      $postava_check = $this->getJednaPostava($postava_id);

      if ($postava_check == '') {
         return array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => $original_page, 'view_alert' => 'error', 'alert' => 'Postava neexistuje');
      } elseif ((($postava_check['account'] != $this->account_id) && ($je_smazana == 0)) || (($postava_check['deleteInfos_Account'] != $this->account_id) && ($je_smazana == 1))) {
         return array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => $original_page, 'view_alert' => 'error', 'alert' => 'Postava není tvoje');
      } else {
         return $postava_check;
      }
   }

   private function postavaCheckOnline($postava_id, $postava_online) {
      if ($postava_online == 1) {
         return array($this->returnPostava($postava_id), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Postava musí být offline'));
      } else {
         return false;
      }
   }

   public function returnPostava($postava_id) {
      $postava = $this->postavaCheck($postava_id);

      if (isset($postava['race'])) {
         $postava['race'] = $this->raceHandler($postava['race']);
      }
      if (isset($postava['race'])) {
         $postava['class'] = $this->classHandler($postava['class']);
      }

      if (!isset($postava['view_header'])) {
         $postava['view_header'] = 'main';
      }
      if (!isset($postava['view_navigation'])) {
         $postava['view_navigation'] = 'menu';
      }
      if (!isset($postava['view_content'])) {
         $postava['view_content'] = 'postavaDetail';
      }
      if (!isset($postava['alert'])) {
         $postava['alert'] = '';
      }
      return $postava;
   }
   
   public function returnSmazanePostavy($race, $class) {
      //var_dump($this->getSmazanePostavy(1, 1));
      return array($this->getSmazanePostavy($race, $class), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaSmazane'));
      
   }
   
   public function returnSluzby() {
      return Db::query('SELECT * FROM auth.ucp_prices');
   }

   private function priceCheck($service) {
      $credits = Db::queryOne('SELECT credits FROM auth.account WHERE id=?', array($this->account_id));
      $price = Db::queryOne('SELECT price FROM auth.ucp_prices WHERE service=?', array($service));

      if ($credits['credits'] > $price['price']) {
         return $credits['credits'] - $price['price'];
      } else {
         return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Nemáš dostatek kreditů.'));
      }
   }

   public function unstuck($postava_id) {

      $postava = $this->postavaCheck($postava_id);
      if (!isset($postava['guid'])) {
         return $postava;
      }
      if (isset($postava['guid'])) {
         $onlinecheck = $this->postavaCheckOnline($postava['guid'], $postava['online']);
         if ($onlinecheck == FALSE) {
            $pricecheck = $this->priceCheck('unstuck');
            if (!is_int($pricecheck)) {
               return $pricecheck;
            } else {

               if (isset($postava['online'])) {
                  if (strtotime($postava['unstuckCooldown']) > time()) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Postavu bude možné unstucknout až ' . date("d.m.Y \\v\\e H:i:s", strtotime($postava['unstuckCooldown']))));
                  }
               }
               if ((isset($postava['online'])) && ($postava['online'] == 0)) {
                  $homebind = Db::query('SELECT * FROM characters.character_homebind WHERE guid=?', array($postava['guid']));
                  $query = Db::queryTransaction(array('DELETE FROM characters.character_aura WHERE guid=:guid', 'UPDATE characters.characters SET playerFlags=0, position_x=:x, position_y=:y, position_z=:z, map=:map, unstuckCooldown=DATE_ADD(NOW(), INTERVAL 5 HOUR) WHERE guid=:guid', 'UPDATE auth.account SET credits=:credits WHERE id=:id'), array(array(':guid' => $postava_id), array(':x' => $homebind[0]['posX'], ':y' => $homebind[0]['posY'], ':z' => $homebind[0]['posZ'], ':map' => $homebind[0]['mapId'], ':guid' => $postava['guid']), array(':credits' => $pricecheck, ':id' => $this->account_id)));
                  if ($query == TRUE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'success', 'alert' => 'Postava byla unstucknutá'));
                  }
                  if ($query == FALSE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Něco se pokazilo. Zkus to znovu.'));
                  }
               }
            }
         } else {
            return $onlinecheck;
         }
      }
   }

   public function rename($postava_id) {

      $postava = $this->postavaCheck($postava_id);
      if (!isset($postava['guid'])) {
         return $postava;
      }
      if (isset($postava['guid'])) {
         $onlinecheck = $this->postavaCheckOnline($postava['guid'], $postava['online']);
         if ($onlinecheck == FALSE) {
            $pricecheck = $this->priceCheck('rename');
            if (!is_int($pricecheck)) {
               return $pricecheck;
            } else {
               if ($postava['at_login'] & self::AT_LOGIN_RENAME) {
                  return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Postava už má rename.'));
               } else {
                  $set_at_login = $postava['at_login'] | self::AT_LOGIN_RENAME;
                  $query = Db::queryTransaction(array('UPDATE characters.characters SET at_login=:at_login WHERE guid=:guid', 'UPDATE auth.account SET credits=:credits WHERE id=:id'), array(array(':at_login' => $set_at_login, ':guid' => $postava_id), array(':credits' => $pricecheck, ':id' => $this->account_id)));
                  if ($query == TRUE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'success', 'alert' => 'Bylo vyvoláno rename.'));
                  }
                  if ($query == FALSE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Něco se pokazilo. Zkus to znovu.'));
                  }
               }
            }
         } else {
            return $onlinecheck;
         }
      }
   }

   public function racechange($postava_id) {

      $postava = $this->postavaCheck($postava_id);
      if (!isset($postava['guid'])) {
         return $postava;
      }
      if (isset($postava['guid'])) {
         $onlinecheck = $this->postavaCheckOnline($postava['guid'], $postava['online']);
         if ($onlinecheck == FALSE) {
            $pricecheck = $this->priceCheck('race_change');

            if (!is_int($pricecheck)) {
               return $pricecheck;
            } else {
               if ($postava['at_login'] & self::AT_LOGIN_CHANGE_RACE) {
                  return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Postava už má změnu rasy.'));
               } else {
                  $set_at_login = $postava['at_login'] | self::AT_LOGIN_CHANGE_RACE;
                  $query = Db::queryTransaction(array('UPDATE characters.characters SET at_login=:at_login WHERE guid=:guid', 'UPDATE auth.account SET credits=:credits WHERE id=:id'), array(array(':at_login' => $set_at_login, ':guid' => $postava_id), array(':credits' => $pricecheck, ':id' => $this->account_id)));
                  if ($query == TRUE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'success', 'alert' => 'Byla vyvolána změna rasy.'));
                  }
                  if ($query == FALSE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Něco se pokazilo. Zkus to znovu.'));
                  }
               }
            }
         } else {
            return $onlinecheck;
         }
      }
   }

   public function factionchange($postava_id) {

      $postava = $this->postavaCheck($postava_id);
      if (!isset($postava['guid'])) {
         return $postava;
      }
      if (isset($postava['guid'])) {
         $onlinecheck = $this->postavaCheckOnline($postava['guid'], $postava['online']);
         if ($onlinecheck == FALSE) {
            $pricecheck = $this->priceCheck('faction_change');

            if (!is_int($pricecheck)) {
               return $pricecheck;
            } else {
               if ($postava['at_login'] & self::AT_LOGIN_CHANGE_FACTION) {
                  return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Postava už má změnu frakce.'));
               } else {
                  $set_at_login = $postava['at_login'] | self::AT_LOGIN_CHANGE_FACTION;
                  $query = Db::queryTransaction(array('UPDATE characters.characters SET at_login=:at_login WHERE guid=:guid', 'UPDATE auth.account SET credits=:credits WHERE id=:id'), array(array(':at_login' => $set_at_login, ':guid' => $postava_id), array(':credits' => $pricecheck, ':id' => $this->account_id)));
                  if ($query == TRUE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'success', 'alert' => 'Byla vyvolána změna frakce.'));
                  }
                  if ($query == FALSE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Něco se pokazilo. Zkus to znovu.'));
                  }
               }
            }
         } else {
            return $onlinecheck;
         }
      }
   }
   
   public function apperancechange($postava_id) {

      $postava = $this->postavaCheck($postava_id);
      if (!isset($postava['guid'])) {
         return $postava;
      }
      if (isset($postava['guid'])) {
         $onlinecheck = $this->postavaCheckOnline($postava['guid'], $postava['online']);
         if ($onlinecheck == FALSE) {
            $pricecheck = $this->priceCheck('apperance_change');

            if (!is_int($pricecheck)) {
               return $pricecheck;
            } else {
               if ($postava['at_login'] & self::AT_LOGIN_CUSTOMIZE) {
                  return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Postava už má customizaci.'));
               } else {
                  $set_at_login = $postava['at_login'] | self::AT_LOGIN_CUSTOMIZE;
                  $query = Db::queryTransaction(array('UPDATE characters.characters SET at_login=:at_login WHERE guid=:guid', 'UPDATE auth.account SET credits=:credits WHERE id=:id'), array(array(':at_login' => $set_at_login, ':guid' => $postava_id), array(':credits' => $pricecheck, ':id' => $this->account_id)));
                  if ($query == TRUE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'success', 'alert' => 'Byla vyvolána customizace postavy.'));
                  }
                  if ($query == FALSE) {
                     return array($this->returnPostava($postava['guid']), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaDetail', 'view_alert' => 'error', 'alert' => 'Něco se pokazilo. Zkus to znovu.'));
                  }
               }
            }
         } else {
            return $onlinecheck;
         }
      }
   }
   
   public function obnovPostavu($postava_id) {
      $postava = $this->postavaCheck($postava_id, 'obnoveni', 1);
      if(!isset($postava['guid'])) {
         return $postava;
      } else {
         $query = Db::queryUpdate('UPDATE characters.characters SET account=deleteInfos_Account, name=guid, deleteInfos_Account= NULL, deleteDate= NULL, deleteInfos_Name= NULL, at_login=1 WHERE guid=?', array($postava_id));
         if ($query == TRUE) {
            return array($this->returnSmazanePostavy(1, 1), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaSmazane', 'view_alert' => 'success', 'alert' => 'Postava '.$postava['deleteInfos_Name'].' byla obnovena.')); 
         }
         if ($query == FALSE) {
            return array($this->returnSmazanePostavy(1, 1), array('view_header' => 'main', 'view_navigation' => 'menu', 'view_content' => 'postavaSmazane', 'view_alert' => 'error', 'alert' => 'Postavu '.$postava['deleteInfos_Name'].' se nepodařilo obnovit. Zkus to znovu.')); 
         }
      }
      
   }
   
   

}
