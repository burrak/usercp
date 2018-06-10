<?php

class FormHandler {

   const TEXT = 'text_';
   const PASSWORD = 'password_';
   const EMAIL = 'email_';
   const SELECT = 'select_';

   private function sanitize($name, $value) {
      if (strpos($name, self::TEXT)) {
         $sanitized = filter_var($value, self::FILTER_SANITIZE_FULL_SPECIAL_CHARS);
         if ($value !== $sanitized) {
            return array('view_aler' => 'error', 'alert' => 'Některé z polí obsahuje nepovolené znaky');
         }
      }
      if (strpos($name, self::EMAIL)) {
         $sanitized = filter_var($value, self::FILTER_SANITIZE_EMAIL);
         if ($value !== $sanitized) {
            return array('view_aler' => 'error', 'alert' => 'Některé z polí obsahuje nepovolené znaky');
         }
      }
   }

   private function validate($name, $value) {
      if (strpos($name, self::EMAIL)) {
         $sanitized = filter_var($value, self::FILTER_VALIDATE_EMAIL);
         if ($value !== $sanitized) {
            return array('view_aler' => 'error', 'alert' => 'Špatný formát e-mailu');
         }
      }
   }

   public function handleForm($params) {
      foreach ($params as $key => $value) {
         $sanitize = $this->sanitize($key, $value);
         if (!empty($sanitize)) {
            return $sanitize;
         }
      }
      foreach ($params as $key => $value) {
         $validate = $this->validate($key, $value);
         if (!empty($validate)) {
            return $validate;
         }
      }

      foreach ($params as $key => $value) {
         if ((preg_match('#^'.self::TEXT.'#', $key)) === 1) {
            $newkey = str_replace(self::TEXT, '', $key);
            $data[$newkey] = $value;
         } elseif ((preg_match('#^'.self::PASSWORD.'#', $key)) === 1) {
            $newkey = str_replace(self::PASSWORD, '', $key);
            $data[$newkey] = $value;
         } elseif ((preg_match('#^'.self::EMAIL.'#', $key)) === 1) {
            $newkey = str_replace(self::EMAIL, '', $key);
            $data[$newkey] = $value;
         } elseif ((preg_match('#^'.self::SELECT.'#', $key)) === 1) {
            $newkey = str_replace(self::SELECT, '', $key);
            $data[$newkey] = $value;
         }
      }
      return $data;
      
   }

}
