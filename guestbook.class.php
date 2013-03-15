<?php
    /**
     * @class  guestbook
     * @author NHN (developers@xpressengine.com)
     * @brief  guestbook module high class
     **/

    class guestbook extends ModuleObject {

        var $skin = "xe_guestbook_official"; ///< skin name
		var $order_target = array('list_order', 'regdate','last_update');
        /**
         * @brief module installation
         **/
        function moduleInstall() {
            // action forward get module controller and model
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');
			$oModuleController->insertTrigger('member.getMemberMenu', 'guestbook', 'controller', 'triggerMemberMenu', 'after');
			$oModuleController->insertTrigger('menu.getModuleListInSitemap', 'guestbook', 'model', 'triggerModuleListInSitemap', 'after');

			//2012-11-15 Add a spamfilter 
			$oModuleController->insertTrigger('guestbook.insertGuestbookItem', 'spamfilter', 'controller', 'triggerInsertDocument', 'before');
            return new Object();
        }

        /**
         * @brief check update method
         **/
        function checkUpdate() {
            $oModuleModel = &getModel('module');
			// 2012. 09. 11 when add new menu in sitemap, custom menu add
			if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'guestbook', 'model', 'triggerModuleListInSitemap', 'after')) return true;
			//2012-11-15 Add a spamfilter 
			if(!$oModuleModel->getTrigger('guestbook.insertGuestbookItem', 'spamfilter', 'controller', 'triggerInsertDocument', 'before')) return true;
            return false;
        }

        /**
         * @brief update module
         **/
        function moduleUpdate() {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
			// 2012. 09. 11 when add new menu in sitemap, custom menu add
			if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'guestbook', 'model', 'triggerModuleListInSitemap', 'after'))
				$oModuleController->insertTrigger('menu.getModuleListInSitemap', 'guestbook', 'model', 'triggerModuleListInSitemap', 'after');

			//2012-11-15 Add a spamfilter 
			if(!$oModuleModel->getTrigger('guestbook.insertGuestbookItem', 'spamfilter', 'controller', 'triggerInsertDocument', 'before'))
				$oModuleController->insertTrigger('guestbook.insertGuestbookItem', 'spamfilter', 'controller', 'triggerInsertDocument', 'before');

            return new Object(0, 'success_updated');
        }

		function moduleUninstall() {
			$output = executeQueryArray("guestbook.getAllGuestbook");
			if(!$output->data) return new Object();
			set_time_limit(0);
			$oModuleController =& getController('module');
			foreach($output->data as $faq)
			{
				$oModuleController->deleteModule($faq->module_srl);
			}
			return new Object();
		}

        /**
         * @brief create cache file
         **/
        function recompileCache() {
        }

    }
?>
