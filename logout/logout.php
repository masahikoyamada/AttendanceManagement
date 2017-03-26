<?php
	// セッション開始
	session_start();

	$adminFlg = 0;
	if ( $_SESSION['adminFlg'] == "1" ) {
		// 管理者の場合
		$adminFlg = "1";
	} else {
		// 一般ユーザの場合
		$adminFlg = "0";
	}

	// セッションクリア
	$_SESSION = array();

	// セッション削除
	session_destroy();

	// 権限によってログインページを変更
	if ( $adminFlg == "1" ) {
		header("Location: ../login/adminlogin.php");
	} else {
		header("Location: ../login/login.php");
	}

	// 処理終了
	exit;
?>