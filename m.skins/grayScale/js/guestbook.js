jQuery(function($){

	$('.btn_srch').click(function(){
		$(this).next().toggle();
	});

	$('.searchkey').keydown(function(){
		$('.btn_cancel').css('display','inline-block');
	});

	$('.btn_cancel').click(function(){
		$('.searchkey')[0].value='';
		$(this).hide();
		$('.searchkey').focus();
	});
	
	/*-- document view more --*/
	var view_rage = 5;
	var more_btn = $('#getMore');
	var documentLst = '.document_list>:gt('+(view_rage-1)+')';
	$(documentLst).css('display','none');
	more_btn.click(function(){
		view_rage +=5;
		documentLst = '.document_list>:lt('+(view_rage)+')';
		$(documentLst).slideDown('slow');
	});	

	/*-- comment view more --*/
	var view_rage = 5;
	var more_btn = $('#getMore');
	var commentLst = '.comment_list>:gt('+(view_rage-1)+')';
	$(commentLst).css('display','none');
	more_btn.click(function(){
		view_rage +=5;
		commentLst = '.comment_list>:lt('+(view_rage)+')';
		$(commentLst).slideDown('slow');
	});	
	
	/*-- add comment --*/
	$('.btn_add').click(function(){
		$('.write_area').slideToggle('slow');
	});
	
	/*-- delete comment --*/
	$('.btn_del').click(function(){
		var offset = $(this).offset().top;
		var top = offset - 400;
		var guestbook_item_srl = $(this).find("a").attr("data");
		var confirm = ".confirm#"+guestbook_item_srl;
		$('.confirm').fadeOut();
		$(confirm).css('margin-top', top);
		$(confirm).fadeIn();
	})
	
	$('.btn_cls').click(function(){
		$('.write_area').slideUp('slow');
	});

	$('.btn_cancel2').click(function(){
		$('.confirm').fadeOut();
	})
	

})