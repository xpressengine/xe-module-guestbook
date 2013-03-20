<?php

require_once(_XE_PATH_.'modules/guestbook/guestbook.view.php');

class guestbookMobile extends guestbookView {
		function init()
		{
           /**
             * get skin template_path
             * if it is not found, default skin is grayScale
             **/
            $template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
            if(!is_dir($template_path)||!$this->module_info->skin) {
                $this->module_info->mskin = 'grayScale';
                $template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
            }
            $this->setTemplatePath($template_path);

			// load guestbook.js
			Context::addJsFile($this->module_path.'tpl/js/guestbook.js');
		}

		function dispGuestbookContent() {
			parent::dispGuestbookContent();
			$oGuestbookModel = &getModel('guestbook');
			$guestbook_list = Context::get('guestbook_list');

			foreach($guestbook_list as $key => $val){
				$commentList = $oGuestbookModel->getGuestbookItemComment($val->guestbook_item_srl);
				$commentList = $commentList->data;
				$guestbook_list [$key]->commentCount = count($commentList);
			}

			Context::set('guestbook_list',$guestbook_list);
		}

		function displayItemInfo() {
			$vars = Context::getRequestVars();
			$guestbook_item_srl = $vars->guestbook_item_srl;

			$oGuestbookModel = &getModel('guestbook');
			$guestbook_item = $oGuestbookModel->getGuestbookItem($guestbook_item_srl);
			$comment_list = $oGuestbookModel->getGuestbookItemComment($guestbook_item_srl);

			Context::set('guestbook_item',$guestbook_item->data);
			Context::set('comment_list',$comment_list->data);
			$this->setTemplateFile('item_info');
		}

		function dispInsertGuestbookItem() {
			$vars = Context::getRequestVars();
			$guestbook_item_srl = $vars->guestbook_item_srl;
			$oGuestbookModel = &getModel('guestbook');
			$output = $oGuestbookModel->getGuestbookItem($guestbook_item_srl);
			$guestbook_item = $output->data;
			if($guestbook_item&&!$guestbook_item->parent_srl) $guestbook_item->parent_srl = $guestbook_item->guestbook_item_srl;

			Context::set('guestbook_item',$guestbook_item);

			 $this->setTemplateFile('add_guestbook_item');

		 }

}


?>
