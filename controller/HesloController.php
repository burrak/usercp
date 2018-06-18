<?php

class HesloController extends Controller {

   var $user;

   public function proceed($params) {
      $this->user = new User($_SESSION['id']);
      $this->headerView = 'main';
      $this->navigationView = 'menu';

      if ((isset($this->dataForm['old'])) && (isset($this->dataForm['new'])) && (isset($this->dataForm['check']))) {
         $data = $this->user->changePassword($this->dataForm['old'], $this->dataForm['new'], $this->dataForm['check']);
         $this->alertView = $data['view_alert'];
         $this->data['alert'] = $data['alert'];
      }

      $hesloForm = new FormFactory('change_password', 'heslo', 'POST');
      $hesloForm->addPassword(array('name' => 'old', 'title' => 'Staré heslo', 'required' => 'required'));
      $hesloForm->addPassword(array('name' => 'new', 'title' => 'Nové heslo', 'required' => 'required'));
      $hesloForm->addPassword(array('name' => 'check', 'title' => 'Znovu nové heslo heslo', 'required' => 'required'));
      $hesloForm->addSubmit('Změnit heslo');

      $this->contentView = 'zmenaHesla';
      $this->data['form'] = $hesloForm->renderForm();
   }

}
