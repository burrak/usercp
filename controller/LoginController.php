<?php
class LoginController extends Controller 
{
  public function proceed($params)
  {
  
  if(isset($_POST['login_submit']))
    { 
      $loginhandler = new LoginHandler($_POST['login'], $_POST['heslo']);
      $overheslo = $loginhandler->checkLogin();
      
      if($overheslo=='TRUE')
      {
        
         $this->redirect('login');
        
      }
      else
      {
         $this->data['login'] = $_POST['login'];
         $this->data['error'] = $overheslo;
         $this->errorView = 'error';
         $this->contentView = 'login';
         
      }
      
    }
  if((isset($_SESSION['id'])) && ($_SESSION['id'] != 0))
    {
      $user = new User($_SESSION['id']);
      $userdata = $user->returnLogin();
      $this->headerView = 'main';
      $this->navigationView = 'menu';
    }
  if(((!isset($_SESSION['id'])) || ($_SESSION['id'] == 0)) && (!isset($_POST['login_sbumit'])) && ((!isset($params[0]))))
    {
      $this->contentView = 'login';
    }
    
   if(isset($params[0]) && ($params[0]=='logout'))
   {
      session_destroy();
      $this->redirect('login');
   }
   
   if((isset($params[0])) && ($params[0] == 'registrace')) {
      if(!isset($_POST['login_reg'])) $_POST['login_reg'] = '';
      if(!isset($_POST['email_reg'])) $_POST['email_reg'] = '';
      $form = new FormFactory('registrace_form', 'login/registrace', 'POST');
      $form->addText(array('name' => 'login_reg', 'title' => 'Login', 'value' => $_POST['login_reg'], 'required' => 'required'));
      $form->addPassword(array('name' => 'password_reg', 'title' => 'Heslo', 'required' => 'required'));
      $form->addPassword(array('name' => 'password_reg_confirm', 'title' => 'Kontrola hesla', 'required' => 'required'));
      $form->addEmail(array('name' => 'email_reg', 'title' => 'E-mail', 'value' => $_POST['email_reg'], 'required' => 'required'));
      $form->addSubmit('register', 'Registrovat');      
      
      if((isset($_POST['register'])) && ($_POST['token'] == $_SESSION['token'])) {
         unset($_SESSION['token']);
         $loginhandler = new LoginHandler($_POST['login_reg'], $_POST['password_reg']);
         $registrace = $loginhandler->registrace($_POST['login_reg'], $_POST['password_reg'], $_POST['password_reg_confirm'], $_POST['email_reg']);
         $this->alertView = $registrace['view_alert'];
         $this->contentView = $registrace['view_content'];
         $this->data['alert'] = $registrace['alert'];
         $this->data['form'] = $form->renderForm();
      } else {
      $this->data['form'] = $form->renderForm();
      $this->contentView = 'registrace';
      }
      
      
      
      

   }
   

   
  }
  
  
}