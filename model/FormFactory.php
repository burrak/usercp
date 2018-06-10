<?php

class FormFactory {

   private $name;
   private $action;
   private $method;
   private $form;

   public function __construct($name, $action, $method) {
      $this->name = $name;
      $this->action = $action;
      $this->method = $method;
      $form_header = '<form action="' . $this->action . '" method="' . $this->method . '" name="' . $this->name . '">' . PHP_EOL . '<table class="' . $this->name . '">' . PHP_EOL . '';
      $this->form .= $form_header;
   }

   public function addText($params) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . $params['title'] . PHP_EOL . '</td>' . PHP_EOL . '<td>' . PHP_EOL . '<input type="text" name="text_' . $params['name'] . '" id="' . $params['name'] . '" value="' . $params['value'] . '" ' . $params['required'] . '>' . PHP_EOL . '</td>' . PHP_EOL;
      $this->form .= $input;
   }

   public function addPassword($params) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . $params['title'] . PHP_EOL . '</td>' . PHP_EOL . '<td>' . PHP_EOL . '<input type="password" name="password_' . $params['name'] . '" id="' . $params['name'] . '" ' . $params['required'] . '>' . PHP_EOL . '</td>' . PHP_EOL;
      $this->form .= $input;
   }

   public function addEmail($params) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . $params['title'] . PHP_EOL . '</td>' . PHP_EOL . '<td>' . PHP_EOL . '<input type="email" name="email_' . $params['name'] . '" id="' . $params['name'] . '" value="' . $params['value'] . '" ' . $params['required'] . '>' . PHP_EOL . '</td>' . PHP_EOL;
      $this->form .= $input;
   }

   public function addSubmit($name, $value) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . '<button type="submit" name="' . $name . '">' . $value . '</button>' . PHP_EOL . '</td>' . PHP_EOL . '</tr>' . PHP_EOL;
      $this->form .= $input;
   }

   public function addSelect($params) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . $params['title'] . PHP_EOL . '</td>' . PHP_EOL . '<td>' . PHP_EOL . '<select name="select_' . $params['name'] . '" id="' . $params['name'] . '">' . PHP_EOL;
      $this->form .= $input;
      foreach ($params['options'] as $key => $value) {
         $this->form .= '<option value="' . $key . '">' . $value . '</option>' . PHP_EOL;
      }
      $this->form .= '</select>' . PHP_EOL . '</td>' . PHP_EOL . '</tr>' . PHP_EOL;
   }

   public function renderForm() {
      $token1 = bin2hex(random_bytes(25));
      $token2 = bin2hex(random_bytes(25));
      $_SESSION['token'] = $token2;
      $this->form .= '</table>' . PHP_EOL . '<input type="hidden" name="token" value="' . $token2 . '">' . PHP_EOL . '</form>';
      return $this->form;
   }

}
