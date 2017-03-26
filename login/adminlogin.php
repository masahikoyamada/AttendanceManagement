<?php
	require (dirname(__FILE__) . "/../common/common.php");

	if ( isset($_POST['loginId']) || isset($_POST['password']) ) {
		// ログインID または パスワードが入力された場合

		$error_message = array();
		if ( empty($_POST['loginId']) ) {
			// ログインIDが未入力の場合
			array_push($error_message, "ログインIDを入力してください。<br/>");
		}

		if ( empty($_POST['password']) ) {
			array_push($error_message, "パスワードを入力してください。");
		}

		if ( count($error_message) ) {
		} else {

			// POST情報設定
			$loginId = htmlspecialchars($_POST['loginId'], ENT_QUOTES);
			$password = htmlspecialchars($_POST['password'], ENT_QUOTES);

			// DB接続
			$con = getConnection();

			// ログインテーブルから情報取得
			$sql = sprintf('select staffNo, password from login where loginId = "%s" and adminFlg != "0" and deleteFlg = "0"',
			               mysqli_real_escape_string($con, $loginId));

			// SQL実行
			$result = mysqli_query($con, $sql);

			// 結果を連想配列で取得
			$recordSet = mysqli_fetch_assoc($result);

			// DB接続を閉じる
			closeConnection($con);

			if ( empty($recordSet) ||
					!password_verify($password, $recordSet["password"])) {
				// 結果が取得できない場合
				array_push($error_message, "ログインIDまたはパスワードを間違えています。");
			} else {
				// 結果が取得できた場合
				// セッションスタート
				session_start();

				// セッションにログイン情報を格納
				// ログインID
				$_SESSION['userId'] = $loginId;

				// 社員ID
				$_SESSION['staffNo'] = $recordSet["staffNo"];

				// 管理者フラグ
				$_SESSION['adminFlg'] = "1";

				// 社員選択画面へ遷移
				header("Location: ../staff/selectStaff.php");

				// 終了
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="x-ua-compatible" content="IE=9" >
		<meta http-equiv="x-ua-compatible" content="IE=EmulateIE9" >
		<meta name="viewport" content="width=1200, minimum-scale=0.1">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="expires" content="0">
		<title>管理者ログイン</title>
		<link rel="stylesheet" type="text/css" href="../css/common.css">
	</head>
	<body>
	<script type="text/javascript">
		window.onunload = function(){};
		history.forward();
	</script>
		<!-- コンテナ開始 -->
		<div id="container">
			<!-- ページ開始 -->
			<div id="page">
				<!-- ヘッダ開始 -->
				<div id="header">
					<p class="catch"><strong>勤怠管理システム</strong></p>
					<hr class="none">
				</div>
				<!-- ヘッダ終了 -->
				<!-- コンテンツ開始 -->
				<div id="content">
					<!-- メインカラム開始 -->
					<div id="main">
						<div class="section normal update">
							<div class="heading">
								<h2>管理者ログイン</h2>
							</div>
							<form action="adminlogin.php" method="post" enctype='text/css'>
								<table width="600">
									<?php
									      // エラーメッセージを出力する
									      if (isset($error_message)) {
									         if (count($error_message)) {
									         	echo '<tr>';
									         	echo "<div style='border-style: solid ; border-width: 1px; padding: 10px 5px 10px 20px; border-color: red;'><font color='#ff0000'>";
									            foreach ($error_message as $message) {
									                  print($message);
									            }
									            echo "</font></div>";
									            echo '</tr>';
									         }
									      }
									?>
									<tr>
										<td>ログイン名：</td>
										<td>
											<input type="text" name="loginId" id="loginId" value="<?php if (isset($_POST['loginId'])) { echo $_POST['loginId'];} ?>" />
										</td>
									</tr>
									<tr>
										<td>パスワード：</td>
										<td>
											<input type="password" name="password" id="password" />
										</td>
									</tr>
									<tr>
										<td colspan='2' align='center'>
											<input type="submit" value="ログイン" />
										</td>
									</tr>
								</table>
							</form>
						</div>
					</div>
					<!-- メインカラム終了 -->
					<hr class="clear">
				</div>
				<!-- コンテンツ終了 -->
				<!-- フッタ開始 -->
				<div id="footer">

				</div>
				<!-- フッタ終了 -->
			</div>
			<!-- ページ終了 -->
		</div>
		<!-- コンテナ終了 -->
	</body>
</html>