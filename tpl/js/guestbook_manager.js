/**
 * @file   modules/guestbook/tpl/js/guestbook_manager.js
 * @author NHN (developers@xpressengine.com)
 * @brief  guestbook module javascript
 **/
function doCartSetup(url) {
    var module_srl = new Array();
    jQuery('#fo_list input[name=cart]:checked').each(function() {
        module_srl[module_srl.length] = jQuery(this).val();
    });

    if(module_srl.length<1) return;

    url += "&module_srls="+module_srl.join(',');
    popopen(url,'modulesSetup');
}

