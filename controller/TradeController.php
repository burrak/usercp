<?php

class TradeController extends Controller {

   public function proceed($params) {
      $postavy = new Postavy($_SESSION['id']);
      $postavyTrade = $postavy->getPostavy(1, 1);
      
      if (!empty($postavyTrade[0])) {
         foreach ($postavyTrade as $key => $value) {
            $postavyTradeList[$value['guid']] = $value['name'] . ' (' . $value['race'] . ' ' . $value['class'] . ' level ' . $value['level'] . ')';
         }
      }
      
      $formTrade = new FormFactory('trade', 'trade', 'POST');
      $formTrade->addSelect(array('title' => 'Vyměnit postavu', 'name' => 'postava_owned', 'options' => $postavyTradeList));
      $formTrade->addText(array('title' => 'za', 'name' => 'postava', 'value' => '', 'required' => 'required'));
      $formTrade->addSubmit('Vyměnit postavy');
      
      if((isset($params[0])) && ($params[0] == 'confirm')) {
         $postavy->confirmTrade($params[1]);
      }

      if ((isset($this->dataForm['postava'])) && (isset($this->dataForm['postava_owned']))) {
         $trade = $postavy->postavaTrade($this->dataForm['postava'], $this->dataForm['postava_owned']);
         $this->headerView = $trade['view_header'];
         $this->navigationView = $trade['view_navigation'];
         $this->alertView = $trade['view_alert'];
         $this->contentView = $trade['view_content'];
         $this->data['alert'] = $trade['alert'];
         $this->data['formTrade'] = $formTrade->renderForm();
      }

      

      if(!isset($this->dataForm['postava'])) {
      $this->headerView = 'main';
      $this->navigationView = 'menu';
      $this->alertView = '';
      $this->contentView = 'trade';
      $this->data['alert'] = '';
      $this->data['formTrade'] = $formTrade->renderForm();
      $this->data['tradeByMe'] = $postavy->tradeByMe();
      $this->data['tradeToMe'] = $postavy->tradeToMe();
      }
   }

}
