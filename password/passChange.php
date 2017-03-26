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
		<title>パスワード変更</title>
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
				<?php
					$contents = "";

					//出力バッファリングを開始
					ob_start();
					//出力バッファに外部ファイルを読み込む
					include('../header/header.php');
					$contents = ob_get_contents();
					ob_end_clean();

					echo $contents;
				?>
				<!-- ヘッダ終了 -->
				<!-- コンテンツ開始 -->
				<div id="content">
					<!-- メインカラム開始 -->
					<div id="main">
						<div class="section normal update">
							<div class="heading">
								<h2>パスワード変更</h2>
							</div>
							<?php
								$completeFlg = false;
								if ( isset($_POST["oldPass"]) ) {
									// 登録ボタンが押下された場合
									// 入力チェック
									$error_message = array();

									//-------------------
									// 未入力チェック
									//-------------------
									if ( empty($_POST["oldPass"]) ) {
										array_push($error_message, "旧パスワードを入力して下さい。<br/>");
									}

									if ( empty($_POST["newPass"]) ) {
										array_push($error_message, "新パスワードを入力して下さい。<br/>");
									}

									if ( empty($_POST["newPassConfirm"]) ) {
										array_push($error_message, "新パスワード(確認用)を入力して下さい。<br/>");
									}

									$passFlg = false;

									if (count($error_message)) {
									} else {
										$passFlg = true;
									}

									//-------------------
									// パスワードチェック
									//-------------------
									if ( $passFlg ) {
										if ( $_POST["newPass"] != $_POST["newPassConfirm"] ) {
											// 新パスワードと新パスワード(確認用)が一致しない場合
											array_push($error_message, "入力された新パスワードと新パスワード(確認用)が違います。<br/>");
										}

										if (count($error_message)) {
										} else {
											// ログインテーブルから社員番号に紐づく情報を取得
											$sqlWs = sprintf('select staffNo, password from login where staffNo = "%s" and adminFlg != "1" and deleteFlg = "0"',
											                 mysqli_real_escape_string($con, $staffNo));

											// SQL実行
											$resultWs = mysqli_query($con, $sqlWs);

											// 連想配列で取得
											$recordSet = mysqli_fetch_assoc($resultWs);

											if ( password_verify($_POST["newPass"], $recordSet["password"]) ) {
												array_push($error_message, "現在登録されているパスワードと新パスワードが同じです。<br/>");
											} else if ( !password_verify($_POST["oldPass"], $recordSet["password"]) ) {
												array_push($error_message, "現在登録されているパスワードと旧パスワードが違います。<br/>");
											}

											// エラーが存在しない場合
											if (count($error_message)) {
											} else {
												// 画面の情報を変数に格納
												$password = $_POST["newPass"];

												// パスワードのハッシュ化
												$password = password_hash($password, PASSWORD_DEFAULT);
												if ( mysqli_num_rows($resultWs) == 0 ) {
												} else {
													// 登録されている場合
													// update文発行
													$upSql = sprintf('update login
													                  set
						                                                password = "%s",
						                                                updateUser = "%s",
						                                                updateDate = now()
						                                              where
						                                                staffNo = %d
													                  ',
													                  mysqli_real_escape_string($con, $password),
													                  mysqli_real_escape_string($con, $_SESSION['userId']),
													                  mysqli_real_escape_string($con, $staffNo)
													);
													// SQL実行
													mysqli_query($con, $upSql);
													$completeFlg = true;
												}
											}

										}

									}
								}
							?>
							<form action="passChange.php" method="post" enctype='text/css'>
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
									         	echo 'パスワードの変更が完了しました。<br/>変更したパスワードでログインを行ってください。';
									            echo "</font></div>";
									            echo '</tr>';
									      }
									?>
									<tr>
										<td align="left"> 旧パスワード：</td>
										<td>
											<input type="password" name="oldPass" id="oldPass" />
										</td>
									</tr>
									<tr>
										<td align="left"> 新パスワード：</td>
										<td>
											<input type="password" name="newPass" id="newPass" />
										</td>
									</tr>
									<tr>
										<td align="left"> 新パスワード(確認用)：</td>
										<td>
											<input type="password" name="newPassConfirm" id="newPassConfirm" />
										</td>
									</tr>
									<tr>
										<td colspan='2' align='center'>
											<input type="submit" value="登録" />
										</td>
									</tr>
								</table>
							</form>
						</div>
					</div>
					<!-- メインカラム終了 -->
					<!-- サイドバー開始 -->
					<?php
						$contents = "";

						//出力バッファリングを開始
						ob_start();
						//出力バッファに外部ファイルを読み込む
						include('../menu/menu.php');
						$contents = ob_get_contents();
						ob_end_clean();

						echo $contents;
					?>
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