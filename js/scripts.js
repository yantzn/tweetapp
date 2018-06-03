$(function() {
	//アカウント作成がボタンが投下された場合
	$('.modal-footer button').click(function(){
		var button = $(this);
		if ( button.attr("data-dismiss") != "modal" ){
			var inputs = $('form input');
			var title = $('.modal-title');
			var progress = $('.progress');
			var progressBar = $('.progress-bar');

			inputs.attr("disabled", "disabled");
			button.hide();
			progress.show();
			progressBar.animate({width : "100%"}, 100);

			progress.delay(1000)
			.fadeOut(600);

			button.text("Close")
				.removeClass("btn-primary")
				.addClass("btn-success")
				.blur()
				.delay(1600)
				.fadeIn(function(){
				title.text("アカウントを作成しました。");
				button.attr("data-dismiss", "modal");
			});
		}
	});

	//投稿エリアにフォーカスされた場合
	$("input[name='tweet_msg']").focus(function(){
		$(this).css({'height':'100','padding-bottom':'60px'});
	}).blur(function(){
		$(this).css({'height':'','padding-bottom':''});
	});
    //投稿ボタンの有効化/無効化
	$("input[name='tweet_msg']").blur(function(){
		//投稿エリアが未入力の場合　　
		if($(this).val() == ""){
            //投稿ボタンを無効化する
			$("#tweet").prop("disabled", true);
	　　} else{
            //投稿ボタンを有効化する
            $("#tweet").prop("disabled", false);
	　　}
	});

	// 確認ダイアログの表示(JQuery)
	function ShowJQueryConfirmDialog() {
		var strTitle = "確認ダイアログ";
		var strComment = "これは確認ダイアログです。";
		// ダイアログのメッセージを設定
		$( "#show_dialog" ).html( strComment );
		// ダイアログを作成
		$( "#show_dialog" ).dialog({
			modal: true,
			title: strTitle,
			buttons: {
				"OK": function() {
					$( this ).dialog( "close" );
					ShowJQueryMessageDialog( "OKがクリックされました" );
				},
				"キャンセル": function() {
					$( this ).dialog( "close" );
					ShowJQueryMessageDialog( "キャンセルがクリックされました" );
				}
			}
		});
	}
});