<?php

class PostavyController extends Controller {

   public function proceed($params) {
      $postavy = new Postavy($_SESSION['id']);





      if ((isset($params[0]) && (isset($params[1])))) {
         $character = $postavy->returnPostava($params[1]);
         $sluzby = $postavy->returnSluzby();
         if ($params[0] == 'unstuck') {
            $character = $postavy->unstuck($params[1]);
            $character = array_merge($character[0], $character[1]);
         }
         if ($params[0] == 'rename') {
            $character = $postavy->rename($params[1]);
            $character = array_merge($character[0], $character[1]);
         }
         if ($params[0] == 'race_change') {
            $character = $postavy->racechange($params[1]);
            $character = array_merge($character[0], $character[1]);
         }
         if ($params[0] == 'faction_change') {
            $character = $postavy->factionchange($params[1]);
            $character = array_merge($character[0], $character[1]);
         }
         if ($params[0] == 'apperance_change') {
            $character = $postavy->apperancechange($params[1]);
            $character = array_merge($character[0], $character[1]);
         }

         foreach ($character as $key => $char) {
            $this->data[$key] = $char;
         }

         $this->data['sluzby'] = $sluzby;
         $this->headerView = $character['view_header'];
         $this->navigationView = $character['view_navigation'];
         if (isset($character['view_alert'])) {
            $this->alertView = $character['view_alert'];
         }
         $this->contentView = $character['view_content'];
      
         
      } elseif (((isset($params[0])) && ($params[0] == 'obnoveni'))) {
         $smazane = $postavy->returnSmazanePostavy(1, 1);

         if ((isset($_POST['postava'])) && (isset($_POST['obnovit_postavu'])) && ($_POST['token'] == $_SESSION['token'])) {
            unset($_SESSION['token']);
            $obnovit = $postavy->obnovPostavu($_POST['postava']);
            $obnovit = array_merge($obnovit[0], $obnovit[1]);
            $this->headerView = $obnovit['view_header'];
            $this->navigationView = $obnovit['view_navigation'];
            $this->alertView = $obnovit['view_alert'];
            $this->contentView = $obnovit['view_content'];
            $this->data['alert'] = $obnovit['alert'];
            $this->data['smazane'] = $obnovit[0];
         } else {

            $this->headerView = $smazane[1]['view_header'];
            $this->navigationView = $smazane[1]['view_navigation'];
            if (isset($character['view_alert'])) {
               $this->alertView = $smazane[1]['view_alert'];
            }
            $this->contentView = $smazane[1]['view_content'];
            $this->data['smazane'] = $smazane[0];
            
         }
      }

      if (!isset($params[0])) {
         $seznampostav = $postavy->getPostavy(1, 1);

         $this->headerView = 'main';
         $this->navigationView = 'menu';
         $this->contentView = 'seznamPostav';
         $this->data['postavy'] = $seznampostav;
      }
   }

}
