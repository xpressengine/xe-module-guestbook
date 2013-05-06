<?php
    /**
     * @class  guestbookAdminView
     * @author NHN (developers@xpressengine.com)
     * @brief  guestbook module admin view class
     **/

    class guestbookAdminView extends guestbook {

        function init() {
			// get module_srl if it exists
            $module_srl = Context::get('module_srl');
            if(!$module_srl && $this->module_srl) {
                $module_srl = $this->module_srl;
                Context::set('module_srl', $module_srl);
            }

            // module model class
            $oModuleModel = &getModel('module');

            // get module_info based on module_srl
            if($module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
                if(!$module_info) {
                    Context::set('module_srl','');
                    $this->act = 'list';
                } else {
                    ModuleModel::syncModuleToSite($module_info);
                    $this->module_info = $module_info;
                    Context::set('module_info',$module_info);
                }
            }

            if($module_info && $module_info->module != 'guestbook') return $this->stop("msg_invalid_request");

            // get module category
            $module_category = $oModuleModel->getModuleCategories();
            Context::set('module_category', $module_category);

            // set the module template path (modules/guestbook/tpl)
            $template_path = sprintf("%stpl/",$this->module_path);
            $this->setTemplatePath($template_path);

			// get order (sorting) target
			foreach($this->order_target as $key) $order_target[$key] = Context::getLang($key);
            $order_target['list_order'] = Context::getLang('document_srl');
            $order_target['last_update'] = Context::getLang('last_update');
            Context::set('order_target', $order_target);

			$oSecurity = new Security();
			$oSecurity->encodeHTML('module_info.' , 'module_category..');
        }
       
		// display guestbook module admin panel 
	    function dispGuestbookAdminContent() {
			$args->sort_index = "module_srl";
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;
            $args->s_module_category_srl = Context::get('module_category_srl');

			$s_mid = Context::get('s_mid');
			if($s_mid) $args->s_mid = $s_mid;

			$s_browser_title = Context::get('s_browser_title');
			if($s_browser_title) $args->s_browser_title = $s_browser_title;

            $output = executeQueryArray('guestbook.getGuestbookList', $args);
            ModuleModel::syncModuleToSite($output->data);

            // setup module variables, context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('guestbook_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

			$oSecurity = new Security();
			$oSecurity->encodeHTML('guestbook_list..');

            // set template file
            $this->setTemplateFile('index');
		}

		function dispGuestbookAdminGuestbookInfo() {
            $this->dispGuestbookAdminInsertGuestbook();
        }

		 /**
         * @brief display insert guestbook admin page
         **/
        function dispGuestbookAdminInsertGuestbook() {
			if(!in_array($this->module_info->module, array('admin','blog','guestbook'))) {
                return $this->alertMessage('msg_invalid_request');
            }

			//get skin list
			$oModuleModel = &getModel('module');
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

			$mskin_list = $oModuleModel->getSkins($this->module_path, "m.skins");
			Context::set('mskin_list', $mskin_list);

			//get layout list
            $oLayoutModel = &getModel('layout');
            $layout_list = $oLayoutModel->getLayoutList();
            Context::set('layout_list', $layout_list);

			$mobile_layout_list = $oLayoutModel->getLayoutList(0,"M");
			Context::set('mlayout_list', $mobile_layout_list);

			$oSecurity = new Security();
			$oSecurity->encodeHTML('skin_list..', 'mskin_list..');
			$oSecurity->encodeHTML('layout_list..', 'mlayout_list..');

			$this->setTemplateFile('guestbook_insert');
        }

		/**
         * @brief display delete guestbook page
         **/
        function dispGuestbookAdminDeleteGuestbook() {
            if(!Context::get('module_srl')) return $this->dispGuestbookAdminContent();
            if(!in_array($this->module_info->module, array('admin', 'guestbook'))) {
                return $this->alertMessage('msg_invalid_request');
            }

            $module_info = Context::get('module_info');

            $oGuestbookModel = &getModel('guestbook');
            $guestbookItem_count = $oGuestbookModel->getGuestbookItemCount($module_info->module_srl);
			$module_info->guestbookItem_count = $guestbookItem_count;

            Context::set('module_info',$module_info);

            // set template file
            $this->setTemplateFile('guestbook_delete');
        }       

        /**
         * @brief display the grant information
         **/
        function dispGuestbookAdminGrantInfo() {
            // get the grant infotmation from admin module 
            $oModuleAdminModel = &getAdminModel('module');
            $grant_content = $oModuleAdminModel->getModuleGrantHTML($this->module_info->module_srl, $this->xml_info->grant);
            Context::set('grant_content', $grant_content);

            $this->setTemplateFile('grant_list');
        }
    }

?>
