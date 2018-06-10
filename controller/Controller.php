<?php

abstract class Controller {

   // Pole, jehož indexy jsou poté viditelné v šabloně jako běžné proměnné
   protected $data = array();
   // Název šablony bez přípony
   protected $headerView = "";
   protected $contentView = "";
   protected $navigationView = "";
   protected $errorView = "";
   protected $alertView = "";
   // Hlavička HTML stránky
   protected $header = array('title' => '', 'keywords' => '', 'description' => '');
   public $dataForm = array();

   // Vyrenderuje pohled
   public function __construct() {
      if (!empty($_POST)) {

         if ($_POST['token'] == $_SESSION['token']) {
            $Form = new FormHandler();
            $this->dataForm = $Form->handleForm($_POST);
            //var_dump($this->dataForm);
         }
      }
   }

   private function osetri($x = null) {
      if (!isset($x))
         return null;
      elseif (is_string($x))
         return htmlentities($x, ENT_QUOTES);
      elseif (is_array($x)) {
         foreach ($x as $k => $v) {
            $x[$k] = $this->osetri($v);
         }
         return $x;
      } else
         return $x;
   }

   public function renderHeader() {
      if ($this->headerView) {
         $user = new User($_SESSION['id']);
         $userlogin = $user->returnLogin();
         $usercredits = $user->returnCredits();
         $userip = $user->returnLastIp();
         $this->data['username'] = $userlogin;
         $this->data['credits'] = $usercredits;
         $this->data['last_ip'] = $userip;
         extract($this->osetri($this->data));
         extract($this->data, EXTR_PREFIX_ALL, "");
         require("view/" . $this->headerView . ".phtml");
      }
   }

   public function renderNavigation() {
      if ($this->navigationView) {
         extract($this->osetri($this->data));
         extract($this->data, EXTR_PREFIX_ALL, "");
         require("view/" . $this->navigationView . ".phtml");
      }
   }

   public function renderContent() {
      if ($this->contentView) {
         extract($this->osetri($this->data));
         extract($this->data, EXTR_PREFIX_ALL, "");
         require("view/" . $this->contentView . ".phtml");
      }
   }

   public function renderAlert() {
      if ($this->alertView) {
         extract($this->osetri($this->data));
         extract($this->data, EXTR_PREFIX_ALL, "");
         require("view/" . $this->alertView . ".phtml");
      }
   }

   public function renderPaginace() {
      if ($this->paginaceView) {
         extract($this->osetri($this->data));
         extract($this->data, EXTR_PREFIX_ALL, "");
         require("view/" . $this->paginaceView . ".phtml");
      }
   }

   // Přesměruje na dané URL
   public function redirect($url) {
      header("Location: /$url");
      header("Connection: close");
      exit;
   }

   // Hlavní metoda controlleru
   abstract function proceed($params);
}
