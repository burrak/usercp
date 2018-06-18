<?php

class FormFactory {

   private $name;
   private $action;
   private $method;
   private $form;
   private $token;

   public function __construct($name, $action, $method) {
      $this->name = 'form_'.$name;
      $this->action = $action;
      $this->method = $method;
      $form_header = '<form action="' . $this->action . '" method="' . $this->method . '" name="' . $this->name . '">' . PHP_EOL . '<table class="' . $this->name . '">' . PHP_EOL . '';
      $this->form .= $form_header;
      $this->token = bin2hex(random_bytes(25));
      $_SESSION['token'] = $this->token;
   }

   public function addText($params) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . $params['title'] . PHP_EOL . '</td>' . PHP_EOL . '<td>' . PHP_EOL . '<input type="text" name="'.$params['name'].'" id="' . $params['name'] . '" value="' . $params['value'] . '" ' . $params['required'] . '>' . PHP_EOL . '</td>' . PHP_EOL;
      $this->form .= $input;
      $_SESSION[$this->token][$params['name']] = 'text';
   }

   public function addPassword($params) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . $params['title'] . PHP_EOL . '</td>' . PHP_EOL . '<td>' . PHP_EOL . '<input type="password" name="' . $params['name'] . '" id="' . $params['name'] . '" ' . $params['required'] . '>' . PHP_EOL . '</td>' . PHP_EOL;
      $this->form .= $input;
      $_SESSION[$this->token][$params['name']] = 'password';
   }

   public function addEmail($params) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . $params['title'] . PHP_EOL . '</td>' . PHP_EOL . '<td>' . PHP_EOL . '<input type="email" name="' . $params['name'] . '" id="' . $params['name'] . '" value="' . $params['value'] . '" ' . $params['required'] . '>' . PHP_EOL . '</td>' . PHP_EOL;
      $this->form .= $input;
      $_SESSION[$this->token][$params['name']] = 'email';
   }

   public function addSubmit($value) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . '<button type="submit" name="' . $this->name . '">' . $value . '</button>' . PHP_EOL . '</td>' . PHP_EOL . '</tr>' . PHP_EOL;
      $this->form .= $input;
   }

   public function addSelect($params) {
      $input = '<tr>' . PHP_EOL . '<td>' . PHP_EOL . $params['title'] . PHP_EOL . '</td>' . PHP_EOL . '<td>' . PHP_EOL . '<select name="select_' . $params['name'] . '" id="' . $params['name'] . '">' . PHP_EOL;
      $this->form .= $input;
      foreach ($params['options'] as $key => $value) {
         $this->form .= '<option value="' . $key . '">' . $value . '</option>' . PHP_EOL;
      }
      $this->form .= '</select>' . PHP_EOL . '</td>' . PHP_EOL . '</tr>' . PHP_EOL;
      $_SESSION[$this->token][$params['name']] = 'select';
   }

   public function renderForm() {

      $this->form .= '</table>' . PHP_EOL . '<input type="hidden" name="token" value="' . $this->token . '">' . PHP_EOL . '</form>';
      return $this->form;
   }

}
