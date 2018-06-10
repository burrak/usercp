<?php

class Paginace {

   const NA_STRANU = 20;

   public function getNumberRows($id, $smer) {
      if ($smer == 'od') {
         return array(Db::queryOne("SELECT COUNT(*) FROM zpravy WHERE od=? AND smazano_od=0", array($id)), self::NA_STRANU);
      }

      if ($smer == 'pro') {
         return array(Db::queryOne("SELECT COUNT(*) FROM zpravy WHERE pro=? AND smazano_pro=0", array($id)), self::NA_STRANU);
      }
   }

}
