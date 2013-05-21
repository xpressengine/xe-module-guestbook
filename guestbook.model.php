<?php
    /**
     * @class  guestbookModel
     * @author NHN (developers@xpressengine.com)
     * @brief  guestbook module Model class
     **/

    class guestbookModel extends module {

		/**
		 * @brief initialization
		 **/
		function init() {
		}

		/**
		 * @brief get guestbook item list
		 **/
        function getGuestbookItemList($vars){
            // set sorting infor
            if(!in_array($vars->sort_index, array('guestbook_item_srl','list_order','last_update'))) $vars->sort_index = 'guestbook_item_srl';
            if(!in_array($vars->order_type, array('desc','asc'))) $vars->order_type = 'asc';

            $args->module_srl = $vars->module_srl;
            $args->page = $vars->page;
            $args->list_count = $vars->list_count;
            $args->page = $vars->page?$vars->page:1;
            $args->list_count = $vars->list_count?$vars->list_count:20;
            $args->page_count = $vars->page_count?$vars->page_count:10;
            $args->sort_index = $vars->sort_index;
            $args->order_type = $vars->order_type;
			$args->parent_srl = 0;

			if($vars->search_keyword){
				$args->user_id_search = $vars->search_keyword;
				$args->user_name_search = $vars->search_keyword;
				$args->nick_name_search = $vars->search_keyword;
				$args->homepage_search = $vars->search_keyword;
				$args->email_address_search = $vars->search_keyword;
				$args->ipaddress_search = $vars->search_keyword;
				$args->content_search = $vars->search_keyword;
			}

            $output = executeQueryArray('guestbook.getGuestbookItemList',$args);

            if(!$output->toBool() || !$output->data) return array();

            return $output;
        }

        function getChildGuestbookItemList($vars){
            // set sorting infor
            if(!in_array($vars->sort_index, array('guestbook_item_srl','list_order','last_update'))) $vars->sort_index = 'guestbook_item_srl';
            if(!in_array($vars->order_type, array('desc','asc'))) $vars->order_type = 'asc';

            $args->module_srl = $vars->module_srl;
            $args->parent_srl = $vars->guestbook_item_srl;
			$args->order_type = $vars->order_type;

            $output = executeQueryArray('guestbook.getChildGuestbookItemList',$args);

            if(!$output->toBool() || !$output->data) return array();

            return $output;
        }

		/**
		 * @brief get memberInfo
		 **/
		function getMemberInfo($vars){
			$args->email_address = $vars->email_address;
			$args->password = $vars->password;

			$output = executeQueryArray('guestbook.getMemberInfo',$args);
			if(!$output->toBool() || !$output->data) return array();

			return $output;
		}

        function getGuestbookItem($guestbook_item_srl){
            $oMemberModel = &getModel('member');

            $args->guestbook_item_srl = $guestbook_item_srl;
            $output = executeQuery('guestbook.getGuestbookItem',$args);
            if($output->data->member_srl){
				$profile_info = $oMemberModel->getProfileImage($output->data->member_srl);
                if($profile_info) $output->data->profile_image = $profile_info->src;
            }
            return $output;
        }

        /**
         * @brief get guestbookItem count based on module_srl
         **/
        function getGuestbookItemCount($module_srl, $search_obj = NULL) {
            $args->module_srl = $module_srl;

            $output = executeQuery('guestbook.getGuestbookItemCount', $args);

            // return total count
            $total_count = $output->data->count;
            return (int)$total_count;
        }

		 function getGuestbookItemComment($guestbook_item_srl) {
			$args->guestbook_item_srl = $guestbook_item_srl;
			$output = executeQueryArray('guestbook.getGuestbookItemComment', $args);

			if(!$output->toBool() || !$output->data) return array();
			return $output;
		 }

		/**
		 * @brief return module name in sitemap
		 **/
		function triggerModuleListInSitemap(&$obj)
		{
			array_push($obj, 'guestbook');
		}
    }
?>
