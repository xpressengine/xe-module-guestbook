<?php
/**
 * @class  guestbookController
 * @author NHN (developers@xpressengine.com)
 * @brief  guestbook module Controller class
 **/

class guestbookController extends guestbook {

	/**
	 * @brief initialization
	 **/
	function init() {
	}

	/**
	 * @brief insert Guestbook Item (document) 
	 **/
	function procGuestbookInsertGuestbookItem(){
		$val = Context::gets('mid','nick_name','password','content','parent_srl','guestbook_item_srl','page');

		// set
		$obj->module_srl = $this->module_srl;
		$obj->content = $val->content;

		// update
		if($val->guestbook_item_srl>0){
			$obj->user_name = $obj->nick_name = $val->nick_name;
			$obj->password = md5($val->password);

			$obj->guestbook_item_srl = $val->guestbook_item_srl;
			$output = executeQuery('guestbook.updateGuestbookItem', $obj);

		// insert
		}else{
			// if logined
			if(Context::get('is_logged')) {
				$logged_info = Context::get('logged_info');
				$obj->member_srl = $logged_info->member_srl;
				$obj->user_id = $logged_info->user_id;
				$obj->user_name = $logged_info->user_name;
				$obj->nick_name = $logged_info->nick_name;
				$obj->email_address = $logged_info->email_address;
				$obj->homepage = $logged_info->homepage;
			}else{
				$obj->user_name = $obj->nick_name = $val->nick_name;
				$obj->password = md5($val->password);
				$oGuestbookModel = &getModel('guestbook');

				// only registered user can insert guestbook items
				$memberInfo = $oGuestbookModel->getMemberInfo($obj);
				if($memberInfo->data){
					$obj->member_srl = $memberInfo->data[0]->member_srl;
					$obj->user_id = $memberInfo->data[0]->user_id;
					$obj->nick_name = $memberInfo->data[0]->nick_name;
					$obj->email_address = $memberInfo->data[0]->email_address;
					$obj->homepage = $memberInfo->data[0]->homepage;
				}else{
					// for reply/for add message
					if($val->parent_srl>0)
						return new Object(-1, 'Invalid user, only registered user can add a commment to the message.');
					else
						return new Object(-1, 'Invalid user, only registered user can add a message to guestbook.');
				}
			}

			$obj->guestbook_item_srl = getNextSequence();
			// reply
			if($val->parent_srl>0){
				$obj->parent_srl = $val->parent_srl;
				$obj->list_order = $obj->parent_srl * -1;
			}else{
				$obj->list_order = $obj->guestbook_item_srl * -1;
			}
			$output = executeQuery('guestbook.insertGuestbookItem', $obj);
		}
		if(!$output->toBool()) return $output;

		$obj->guestbook_count = 1;
		$this->add('page',$val->page?$val->page:1);

	}

	/**
	 * @brief Guestbook item delete
	 **/
	function procGuestbookDeleteGuestbookItem(){
		$guestbook_item_srl = Context::get('guestbook_item_srl');
        if(!$guestbook_item_srl) return new Object(-1,'msg_invalid_request');

        $logged_info = Context::get('logged_info');

        if(!($logged_info->is_admin == 'Y'|| $_SESSION['own_textyle_guestbook'][$guestbook_item_srl])) return new Object(-1,'msg_not_permitted');

		$output = $this->deleteGuestbookItem($guestbook_item_srl);
	}

	function deleteGuestbookItem($guestbook_item_srl){
		$oGuestbookModel = &getModel('guestbook');
		$output = $oGuestbookModel->getGuestbookItem($guestbook_item_srl);
		$oGuest = $output->data;

		if(!$oGuest) return new Object(-1,'msg_invalid_request');

		// delete children
		$pobj->parent_srl = $guestbook_item_srl;
		$output = executeQueryArray('guestbook.getGuestbookItem', $pobj);
		if($output->data){
			foreach($output->data as $k=>$v){
				$poutput = $this->deleteGuestbookItem($v->guestbook_item_srl);
				if(!$poutput->toBool()) return $poutput;
			}
		}
		
		$obj->guestbook_item_srl = $guestbook_item_srl;
		$output = executeQuery('guestbook.deleteGuestbookItem', $obj);
		if(!$output->toBool()) return $output;

		return $output;
	}

}
?>
