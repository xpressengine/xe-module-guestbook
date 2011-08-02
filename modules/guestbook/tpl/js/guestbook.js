/**
 * @file   modules/guestbook/tpl/js/guestbook.js
 * @author NHN (developers@xpressengine.com)
 * @brief  guestbook module javascript
 **/

function insertGuestbookItem(obj,filter){
	jQuery(':text,:password',obj).each(function(){
		var jthis = jQuery(this);
		if(jthis.attr('title') && jthis.val() == jthis.attr('title')) jthis.val('');
	});
	var email = jQuery('[name=email_address].request',obj);
	if(email.length>0 && !jQuery.trim(email.val())){
		alert(jQuery('[name=msg_input_email_address]',obj).val());
		email.eq(0).focus();
		return false;
	}
	var homepage = jQuery('[name=homepage].request',obj);
	if(homepage.length>0 && !jQuery.trim(homepage.val())){
		alert(jQuery('[name=msg_input_homepage]',obj).val());
		homepage.eq(0).focus();
		return false;
	}

	return procFilter(obj,filter);
}

function completeInsertGuestbookItem(ret_obj){
    var page = ret_obj.page;

	location.href=current_url.setQuery('act','dispGuestbookContent').setQuery('mid',current_mid).setQuery('page',page).setQuery('reply','').setQuery('modify','');
}

function deleteGuestbookItem(guestbook_item_srl,page){
    var params = new Array();
    params['guestbook_item_srl'] = guestbook_item_srl;
	
	var response_tags = new Array('error','message','page','mid');
    exec_xml('guestbook', 'procGuestbookDeleteGuestbookItem', params, completeReload, response_tags);
}

function completeReload(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    location.reload();
}