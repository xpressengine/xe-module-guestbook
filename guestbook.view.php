<?php
    /**
     * @class  guestbookView
     * @author NHN (developers@xpressengine.com)
     * @brief  guestbook us module View class
     **/

    class guestbookView extends guestbook {

        /**
         * @brief initialize guestbook view class.
         **/
		function init() {
			if(!$this->grant->access) return new Object(-1,'msg_not_permitted');

			if($this->module_info->list_count) $this->list_count = $this->module_info->list_count;
            if($this->module_info->page_count) $this->page_count = $this->module_info->page_count;

           /**
             * get skin template_path
             * if it is not found, default skin is xe_contact
             **/
            $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            if(!is_dir($template_path)||!$this->module_info->skin) {
                $this->module_info->skin = 'xe_guestbook_official';
                $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            }
            $this->setTemplatePath($template_path);		

            /**
             * get extra variables from xe_module_extra_vars table, context set
             **/
            $oModuleModel = &getModel('module');
            $extra_keys = $oModuleModel->getModuleExtraVars($this->module_info->module_srl);
            Context::set('extra_keys', $extra_keys);

			// load contact.js
			Context::addJsFile($this->module_path.'tpl/js/guestbook.js');	

		}

        /**
         * @brief display Guestbook content
         **/
        function dispGuestbookContent() {
			$reply = Context::get('replay');
            $modify = Context::get('modify');

			$this->dispGuestbookContentList();

			Context::addJsFilter($this->module_path.'tpl/filter', 'insert_guestbookitem.xml');
			// set template_file to be guestbook.html
            $this->setTemplateFile('guestbook');
        }

		/**
         * @brief display guestbook content list
         **/
		function dispGuestbookContentList(){
			if(!Context::get('page')) Context::set('page', 1);
	
	        $oGuestbookModel = &getModel('guestbook');

            // set up basic args
            $args->module_srl = $this->module_srl; 
            $args->page = Context::get('page');
            $args->list_count = $this->list_count; 
            $args->page_count = $this->page_count; 
	
            // set up sorting args
            $args->sort_index = Context::get('sort_index');
            $args->order_type = Context::get('order_type');
            if(!in_array($args->sort_index, $this->order_target)) $args->sort_index = $this->module_info->order_target?$this->module_info->order_target:'list_order';
            if(!in_array($args->order_type, array('asc','desc'))) $args->order_type = $this->module_info->order_type?$this->module_info->order_type:'asc';

			// search keyword
			$args->search_keyword = Context::get('search_keyword');

			$output = $oGuestbookModel->getGuestbookItemList($args);
			$guestbook_list = array();

			// get child guestbook items
			if($output->data){
				foreach($output->data as $guestbook_item){
					$guestbook_list[$guestbook_item->guestbook_item_srl] = $guestbook_item;
					$argsc->module_srl = $guestbook_item->module_srl;
					$argsc->guestbook_item_srl = $guestbook_item->guestbook_item_srl;

					$output2 = $oGuestbookModel->getChildGuestbookItemList($argsc);
					if($output2->data){
						foreach($output2->data as $guestbook_child_item){
							$guestbook_list[$guestbook_child_item->guestbook_item_srl] = $guestbook_child_item;
						}
					}
				}
			}

            Context::set('guestbook_list', $guestbook_list);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('page_navigation', $output->page_navigation);

			$security = new Security();
			$security->encodeHTML('guestbook_list..');

		}

    }



?>
