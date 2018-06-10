<?php

class ErrorController extends Controller {

   public function proceed($params) {
      // Hlavička požadavku
      header("HTTP/1.0 404 Not Found");
      // Hlavička stránky
      $this->header['title'] = 'Chyba 404';
      // Nastavení šablony
      $this->view = 'error';
   }

}
