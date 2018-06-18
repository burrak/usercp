<?php

class FormHandler {

   const TEXT = 'text';
   const PASSWORD = 'password';
   const EMAIL = 'email';
   const SELECT = 'select';

   private function sanitize($name, $value, $type) {
      if ($type == self::TEXT) {
         $sanitized = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
         if ($value !== $sanitized) {
            return array('view_alert' => 'error', 'alert' => 'Některé z polí obsahuje nepovolené znaky');
         }
      }
      if ($type == self::EMAIL) {
         $sanitized = filter_var($value, FILTER_SANITIZE_EMAIL);
         if ($value !== $sanitized) {
            return array('view_alert' => 'error', 'alert' => 'Některé z polí obsahuje nepovolené znaky');
         }
      }
   }

   private function validate($name, $value, $type) {
      if ($type == self::EMAIL) {
         $sanitized = filter_var($value, FILTER_VALIDATE_EMAIL);
         if ($value !== $sanitized) {
            return array('view_alert' => 'error', 'alert' => 'Špatný formát e-mailu');
         }
      }
   }

   public function handleForm($params) {
      foreach ($_SESSION[$params['token']] as $key => $value) {
         $sanitize = $this->sanitize($key, $params[$key], $value);
         if (!empty($sanitize)) {
            return $sanitize;
         }
      }
      foreach ($_SESSION[$params['token']] as $key => $value) {
         $validate = $this->validate($key, $params[$key], $value);
         if (!empty($validate)) {
            return $validate;
         }
      }

      foreach ($_SESSION[$params['token']] as $key => $value) {
         if ($value != '') {
            $data[$key] = $params[$key];
         /*} elseif ((preg_match('#^'.self::PASSWORD.'#', $key)) === 1) {
            $newkey = str_replace(self::PASSWORD, '', $key);
            $data[$newkey] = $value;
         } elseif ((preg_match('#^'.self::EMAIL.'#', $key)) === 1) {
            $newkey = str_replace(self::EMAIL, '', $key);
            $data[$newkey] = $value;
         } elseif ((preg_match('#^'.self::SELECT.'#', $key)) === 1) {
            $newkey = str_replace(self::SELECT, '', $key);
            $data[$newkey] = $value;*/
         }
      }

      var_dump($data);
      return $data;
      
   }

}
