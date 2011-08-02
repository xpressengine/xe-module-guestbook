/**
 * @file   modules/guestbook/js/guestbook_admin.js
 * @author NHN (developers@xpressengine.com)
 * @brief  guestbook javascript
 **/

/* after insert/update guestbook module */
function completeInsertGuestbook(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    var page = ret_obj['page'];
    var module_srl = ret_obj['module_srl'];

    alert(message);

    var url = current_url.setQuery('act','dispGuestbookAdminGuestbookInfo');
    if(module_srl) url = url.setQuery('module_srl',module_srl);
    if(page) url.setQuery('page',page);
    location.href = url;
}

/* after delete guestbook module */
function completeDeleteGuestbook(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var page = ret_obj['page'];
    alert(message);

    var url = current_url.setQuery('act','dispGuestbookAdminContent').setQuery('module_srl','');
    if(page) url = url.setQuery('page',page);
    location.href = url;
}

