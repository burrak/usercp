<?php

class LoginController extends Controller {

   public function proceed($params) {

      if (isset($_POST['form_login'])) {

         $loginhandler = new LoginHandler($this->dataForm['login'], $this->dataForm['heslo']);
         $overheslo = $loginhandler->checkLogin();

         if (isset($overheslo['id'])) {

            $this->redirect('login');
         } else {
            $this->contentView = $overheslo['view_content'];
            $this->alertView = $overheslo['view_alert'];
            $this->data['login'] = $this->dataForm['login'];
            $this->data['alert'] = $overheslo['alert'];
         }
      }
      if ((isset($_SESSION['id'])) && ($_SESSION['id'] != 0)) {
         $user = new User($_SESSION['id']);
         $userdata = $user->returnLogin();
         $this->headerView = 'main';
         $this->navigationView = 'menu';
      }
      if (((!isset($_SESSION['id'])) || ($_SESSION['id'] == 0)) && (!isset($this->dataForm['login_sbumit'])) && ((!isset($params[0])))) {
         if (!isset($this->dataForm['login']))
            $this->dataForm['login'] = '';
         $loginForm = new FormFactory('login', '/login', 'POST');
         $loginForm->addText(array('name' => 'login', 'title' => 'Login', 'value' => $this->dataForm['login'], 'required' => 'required'));
         $loginForm->addPassword(array('name' => 'heslo', 'title' => 'Heslo', 'required' => 'required'));
         $loginForm->addSubmit('Login');
         $this->data['loginForm'] = $loginForm->renderForm();
         $this->contentView = 'login';
      }

      if (isset($params[0]) && ($params[0] == 'logout')) {
         session_destroy();
         $this->redirect('login');
      }

      if ((isset($params[0])) && ($params[0] == 'registrace')) {
         if (!isset($this->dataForm['login_reg']))
            $this->dataForm['login_reg'] = '';
         if (!isset($this->dataForm['email_reg']))
            $this->dataForm['email_reg'] = '';
         $form = new FormFactory('registrace', 'login/registrace', 'POST');
         $form->addText(array('name' => 'login_reg', 'title' => 'Login', 'value' => $this->dataForm['login_reg'], 'required' => 'required'));
         $form->addPassword(array('name' => 'password_reg', 'title' => 'Heslo', 'required' => 'required'));
         $form->addPassword(array('name' => 'password_reg_confirm', 'title' => 'Kontrola hesla', 'required' => 'required'));
         $form->addEmail(array('name' => 'email_reg', 'title' => 'E-mail', 'value' => $this->dataForm['email_reg'], 'required' => 'required'));
         $form->addSubmit('Registrovat');

         if (isset($_POST['form_registrace'])) {
            if (!isset($this->dataForm['alert'])) {
               $loginhandler = new LoginHandler($this->dataForm['login_reg'], $this->dataForm['password_reg']);
               $registrace = $loginhandler->registrace($this->dataForm['login_reg'], $this->dataForm['password_reg'], $this->dataForm['password_reg_confirm'], $this->dataForm['email_reg']);
               $this->alertView = $registrace['view_alert'];
               $this->contentView = $registrace['view_content'];
               $this->data['alert'] = $registrace['alert'];
               $this->data['form'] = $form->renderForm();
               $this->contentView = 'registrace';
            } else {
               $this->alertView = $this->dataForm['view_alert'];
               $this->data['alert'] = $this->dataForm['alert'];
               $this->data['form'] = $form->renderForm();
               $this->contentView = 'registrace';
            }
         } else {
            $this->data['form'] = $form->renderForm();
            $this->contentView = 'registrace';
         }
      }
   }

}
