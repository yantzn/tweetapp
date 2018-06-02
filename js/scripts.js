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

	$('#myModal').on('hidden.bs.modal', function (e) {
		var inputs = $('form input');
		var title = $('.modal-title');
		var progressBar = $('.progress-bar');
		var button = $('.modal-footer button');

		inputs.removeAttr("disabled");

		title.text("アカウントを作成する");

		progressBar.css({ "width" : "0%" });

      // POSTメソッドで送るデータを定義します var data = {パラメータ名 : 値};
//      var data = {'request' : $('#create_password').val()};

      /**
       * Ajax通信メソッド
       * @param type  : HTTP通信の種類
       * @param url   : リクエスト送信先のURL
       * @param data  : サーバに送信する値
       */
/*
      $.ajax({
        type: "POST",
        url: "index.php",
        data: data,
      }).success(function(data, dataType) {
        // successのブロック内は、Ajax通信が成功した場合に呼び出される
		button.removeClass("btn-success")
				.addClass("btn-primary")
				.text("Ok")
				.removeAttr("data-dismiss");
        // PHPから返ってきたデータの表示
        alert(data);
      }).error(function(XMLHttpRequest, textStatus, errorThrown) {
        // 通常はここでtextStatusやerrorThrownの値を見て処理を切り分けるか、単純に通信に失敗した際の処理を記述します。

        // this;
        // thisは他のコールバック関数同様にAJAX通信時のオプションを示します。

        // エラーメッセージの表示
        alert('Error : ' + errorThrown);
      });

      // サブミット後、ページをリロードしないようにする
      return false;


*/
      		button.removeClass("btn-success")
				.addClass("btn-primary")
				.text("Ok")
				.removeAttr("data-dismiss");

	});

});