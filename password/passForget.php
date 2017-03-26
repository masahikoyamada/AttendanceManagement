<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="x-ua-compatible" content="IE=9" >
		<meta http-equiv="x-ua-compatible" content="IE=EmulateIE9" >
		<meta name="viewport" content="width=1200, minimum-scale=0.1">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="expires" content="0">
		<title>パスワード再発行</title>
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
								<h2>パスワード再発行</h2>
							</div>
							<?php
								require (dirname(__FILE__) . "/../common/common.php");

								$completeFlg = false;
								if ( isset($_POST["staffNo"]) ) {
									// 登録ボタンが押下された場合
									// 入力チェック
									$error_message = array();
									$userFlg = false;
									//-------------------
									// 未入力チェック
									//-------------------
									if ( empty($_POST["staffNo"]) ) {
										array_push($error_message, "社員番号を入力して下さい。<br/>");
									}

									if ( empty($_POST["loginId"]) ) {
										array_push($error_message, "ログインIDを入力して下さい。<br/>");
									}

									if (count($error_message)) {
									} else {
										$userFlg = true;
									}

									//-------------------
									// ユーザチェック
									//-------------------
									if ( $userFlg ) {
										$con = getConnection();

										// ログインテーブルから社員番号に紐づく情報を取得
										$sqlWs = sprintf('select staffNo, password from login where loginId = "%s" and staffNo = %d and adminFlg != "1" and deleteFlg = "0"',
										                 mysqli_real_escape_string($con, $_POST["loginId"]),
										                 mysqli_real_escape_string($con, $_POST["staffNo"]));

										// SQL実行
										$resultWs = mysqli_query($con, $sqlWs);

										// 結果を連想配列で取得
										$recordSet = mysqli_fetch_assoc($resultWs);

										if ( empty($recordSet) ) {
											array_push($error_message, "入力された社員番号とログインIDに紐づく情報が登録されていません。<br/>");
										} else {

											// ランダムでパスワードを発行
											$password = create_passwd();
											// 登録されている場合
											// update文発行
											$upSql = sprintf('update login
											                  set
				                                                password = "%s",
				                                                updateUser = "%s",
				                                                updateDate = now()
				                                              where
				                                                loginId = "%s"
				                                                and staffNo = %d
											                  ',
											                  mysqli_real_escape_string($con, password_hash($password, PASSWORD_DEFAULT)),
											                  mysqli_real_escape_string($con, "Administrator"),
											                  mysqli_real_escape_string($con, $_POST["loginId"]),
											                  mysqli_real_escape_string($con, $_POST["staffNo"]));
											// SQL実行
											mysqli_query($con, $upSql);
											$completeFlg = true;
										}

									}
								}
							?>
							<form action="passForget.php" method="post" enctype='text/css'>
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

									      if ( $completeFlg ) {
									         	echo '<tr>';
									         	echo "<div style='border-style: solid ; border-width: 1px; padding: 10px 5px 10px 20px; border-color: red;'><font color='#ff0000'>";
									         	echo 'パスワードの発行が完了しました。<br/>';
									         	echo '下記のパスワードでログインを行い、パスワード変更を行ってください。<br/><br/>';
									         	echo '【パスワード】' . $password;
									            echo "</font></div>";
									            echo '</tr>';
									      }
									?>
									<tr>
										<td align="left"> 社員番号：</td>
										<td>
											<input type="text" name="staffNo" />
										</td>
									</tr>
									<tr>
										<td align="left"> ログインID：</td>
										<td>
											<input type="text" name="loginId" />
										</td>
									</tr>
									<tr>
										<td colspan='2' align='center'>
											<input type="submit" value="パスワード発行" />
										</td>
									</tr>
								</table>
							</form>
						</div>
					</div>
					<!-- メインカラム終了 -->
					<!-- サイドバー開始 -->
					<div id="nav">
						<div class="section emphasis">
							<div class="heading">
								<h2>メニュー</h2>
							</div>
							<p><a href="../login/login.php">ログイン画面へ</a></p>
						</div>
					</div>
					<!-- サイドバー終了 -->
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