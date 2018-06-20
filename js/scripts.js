$(function() {

	//投稿ボタンを無効化する
	$("#tweet").prop("disabled", true);

	//投稿エリアにフォーカスされた場合
	$("input[name='tweet_msg']").focus(function(){
		$(this).css({'height':'100','padding-bottom':'60px'});
	}).blur(function(){
		$(this).css({'height':'','padding-bottom':''});
		//投稿エリアが未入力の場合　　
		if($(this).val() == ""){
			//投稿ボタンを無効化する
			$("#tweet").prop("disabled", true);
		} else{
			//投稿ボタンを有効化する
			$("#tweet").prop("disabled", false);
		}
	});
    	
});