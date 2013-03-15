<?php
    /**
     * @class  guestbookAdminController
     * @author NHN (developers@nhn.com)
     * @brief  guestbook module admin controller class
     **/

    class guestbookAdminController extends guestbook {

        /**
         * @brief initialization
         **/
        function init() {
        }

        /**
         * @brief insert Guestbook module
         **/
        function procGuestbookAdminInsertGuestbook($args = null) {
            // get module model/module controller
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');

            // get variables from admin page form
            $args = Context::getRequestVars();
            $args->module = 'guestbook';
            $args->mid = $args->guestbook_name;
            unset($args->guestbook_name);

            if(!in_array($args->order_target,$this->order_target)) $args->order_target = 'list_order';
            if(!in_array($args->order_type,array('asc','desc'))) $args->order_type = 'asc';

			// if module_srl exists
            if($args->module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
                if($module_info->module_srl != $args->module_srl) unset($args->module_srl);
            }

            // insert/update guestbook module, depending on whether module_srl exists or not 
            if(!$args->module_srl) {
                $output = $oModuleController->insertModule($args);
                $msg_code = 'success_registed';
            } else {
                $output = $oModuleController->updateModule($args);
                $msg_code = 'success_updated';
            }

            if(!$output->toBool()) return $output;

            $this->add('page',Context::get('page'));
            $this->add('module_srl',$output->get('module_srl'));
            $this->setMessage($msg_code);

        	if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
				$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'module_srl', $output->get('module_srl'), 'act', 'dispGuestbookAdminGuestbookInfo');
				header('location:'.$returnUrl);
				return;
			}

        }

        /**
         * @brief delete Guestbook module
         **/
        function procGuestbookAdminDeleteGuestbook() {
            $module_srl = Context::get('module_srl');

			$obj->module_srl = $module_srl;
			$oGuestbookModel = &getModel('guestbook');
			$oGuestbookItemList = $oGuestbookModel->getGuestbookItemList($obj);



			// delete module's items
			$oGuestbookController = &getController('guestbook');
			if(count($oGuestbookItemList->data)>0){
				foreach($oGuestbookItemList->data as $oGuestbookItem){
					$oGuestbookController->deleteGuestbookItem($oGuestbookItem->guestbook_item_srl);
				}
			}
			
            $oModuleController = &getController('module');
            $output = $oModuleController->deleteModule($module_srl);
            if(!$output->toBool()) return $output;

            $this->add('module','guestbook');
            $this->add('page',Context::get('page'));
            $this->setMessage('success_deleted');

        	if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
				$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'module_srl', $output->get('module_srl'), 'act', 'dispGuestbookAdminContent');
				header('location:'.$returnUrl);
				return;
			}
        }

    }
?>
