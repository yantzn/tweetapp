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

	$('#test').on('click',function(){
		$.ajax({
		      type: "POST",
		      url: "delete4_user.php",
		      //Ajax通信が成功した場合に呼び出されるメソッド
		      success: function(){
		        //デバッグ用 アラートとコンソール
		        alert("a");
		        window.location.href = '/';
		      },
		      //Ajax通信が失敗した場合に呼び出されるメソッド
		      error: function(XMLHttpRequest, textStatus, errorThrown){
		        alert('Error : ' + errorThrown);
		        $('#modal').modal('hide');
				/*
		        $("#XMLHttpRequest").html("XMLHttpRequest : " + XMLHttpRequest.status);
		        $("#textStatus").html("textStatus : " + textStatus);
		        $("#errorThrown").html("errorThrown : " + errorThrown);
		        */
		      }
	    });
	});	
});