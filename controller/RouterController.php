<?php

// Router je speciální typ controlleru, který podle URL adresy zavolá
// správný controller a jím vytvořený pohled vloží do šablony stránky

class RouterController extends Controller {

   // Instance controlleru
   protected $controller;

   // Metoda převede pomlčkovou variantu controlleru na název třídy
   private function dashesToCamel($text) {
      $str = str_replace('-', ' ', $text);
      $str = ucwords($str);
      $str = str_replace(' ', '', $str);
      return $str;
   }

   // Naparsuje URL adresu podle lomítek a vrátí pole parametrů
   private function parseURL($url) {
      // Naparsuje jednotlivé části URL adresy do asociativního pole
      $parsedURL = parse_url($url);
      // Odstranění počátečního lomítka
      $parsedURL["path"] = ltrim($parsedURL["path"], "/");
      // Odstranění bílých znaků kolem adresy
      $parsedURL["path"] = trim($parsedURL["path"]);
      // Rozbití řetězce podle lomítek
      $splitRoute = explode("/", $parsedURL["path"]);
      return $splitRoute;
   }

   // Naparsování URL adresy a vytvoření příslušného controlleru
   public function proceed($params) {
      $parsedURL = $this->parseURL($params[0]);

      if (((!isset($_SESSION['id'])) || ($_SESSION['id'] == 0)) && ($parsedURL[0] != 'login')) {
         $this->redirect('login');
      }



      if (empty($parsedURL[0]))
         $this->redirect('login');
      // kontroler je 1. parametr URL
      $controllerClass = $this->dashesToCamel(array_shift($parsedURL)) . 'Controller';

      if (file_exists('controller/' . $controllerClass . '.php'))
         $this->controller = new $controllerClass;
      else
         $this->redirect('error');

      // Volání controlleru
      $this->controller->proceed($parsedURL);

      // Nastavení proměnných pro šablonu
      $this->data['title'] = $this->controller->header['title'];
      $this->data['description'] = $this->controller->header['description'];
      $this->data['keywords'] = $this->controller->header['keywords'];



      // Nastavení hlavní šablony
      $this->contentView = 'layout';
   }

   /* public function checkLoged()
     {
     if((!isset($_SESSION['id']))AND( $this->p!='login'))
     {
     $this->redirect('login');
     }
     } */
}
