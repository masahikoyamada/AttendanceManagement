<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="x-ua-compatible" content="IE=9" />
		<meta http-equiv="x-ua-compatible" content="IE=EmulateIE9" />
		<title>社員登録</title>
		<link rel="stylesheet" type="text/css" href="../css/common.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
	    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
		<script>
           $(function() {
             $("#datepicker").datepicker();
           });
           $(function() {
             $("#datepicker_1").datepicker();
             $("#datepicker_1").datepicker("option", "showOn", 'button');
           });
           $(function() {
             $("#datepicker_2").datepicker();
             $("#datepicker_2").datepicker("option", "showOn", 'both');
           });
           $(function() {
             $("#datepicker_3").datepicker();
             $("#datepicker_3").datepicker("option", "showOn", 'both');
             $("#datepicker_3").datepicker("option", "buttonImageOnly", true);
             $("#datepicker_3").datepicker("option", "buttonImage", 'ico_calendar.png');
           });
		</script>
	</head>
	<body>
	<script type="text/javascript">
		window.onunload = function(){};
		history.forward();
	</script>
	<?php
		require (dirname(__FILE__) . "/../common/common.php");
		// DB接続
		$con = getConnection();
		$completeFlg = false;

		if ( isset($_POST["loginId"]) ) {
			$error_message = array();
			// 登録ボタンが押下された場合
			// ---------------------------
			// 未入力チェック
			// ---------------------------
			if (empty($_POST["loginId"])) {
				array_push($error_message, "ログインIDを入力して下さい。<br/>");
			}

			if (empty($_POST["password"])) {
				array_push($error_message, "パスワードを入力して下さい。<br/>");
			}

			if (empty($_POST["password2"])) {
				array_push($error_message, "パスワード(確認用)を入力して下さい。<br/>");
			}

			if (empty($_POST["staffNo"])) {
				array_push($error_message, "社員番号を入力して下さい。<br/>");
			}

			if (empty($_POST["staffName"])) {
				array_push($error_message, "社員名を入力して下さい。<br/>");
			}

			if (empty($_POST["mailAddress"])) {
				array_push($error_message, "メールアドレスを入力して下さい。<br/>");
			}

			if (empty($_POST["hireDate"])) {
				array_push($error_message, "入社日を入力して下さい。<br/>");
			}

			// ---------------------------
			// パスワードチェック
			// ---------------------------
			if ( !empty($_POST["password"]) && !empty($_POST["password2"]) ) {
				// パスワードとパスワード(確認用)が入力されている場合
				if ($_POST["password"] != $_POST["password2"]) {
					array_push($error_message, "パスワードとパスワード(確認用)が違います。<br/>");
				}
			}

			// ---------------------------
			// メールアドレスチェック
			// ---------------------------
			if ( !empty( $_POST["mailAddress"] ) ) {
				// メールアドレス正規表現チェック
				if ( !is_mail($_POST["mailAddress"]) ) {
					array_push($error_message, "メールアドレスが不正です。<br/>");
				}

			}

			if (count($error_message)) {
			} else {
				// ---------------------------
				// DB登録チェック
				// ---------------------------
				// ログインIDの重複チェック
				$loginSql = sprintf('select loginId from login where loginId = "%s" and (adminFlg = "0" or adminFlg = "1" or adminFlg = "2") and deleteFlg = "0"',
				               mysqli_real_escape_string($con, $_POST["loginId"]));

				// SQL実行
				$result = mysqli_query($con, $loginSql);

				// 結果を連想配列で取得
				$recordSet = mysqli_fetch_assoc($result);

				if ( !empty($recordSet) ) {
					// 登録されている場合
					array_push($error_message, "入力されたログインIDは既に登録されています。<br/>");
				}

				// 社員番号の重複チェック
				$staffSql = sprintf('select staffNo from staff where staffNo = "%s" and deleteFlg = "0"',
				               mysqli_real_escape_string($con, $_POST["staffNo"]));

				// SQL実行
				$resStaff = mysqli_query($con, $staffSql);

				// 結果を連想配列で取得
				$recordSetStaff = mysqli_fetch_assoc($resStaff);

				if ( !empty($recordSetStaff) ) {
					// 登録されている場合
					array_push($error_message, "入力された社員番号は既に登録されています。<br/>");
				}
				if (count($error_message)) {
				} else {
					// エラーメッセージが存在しない場合
					// 登録処理を行う。
					// オートコミットをOFF
					mysqli_autocommit($con, FALSE);

					// ---------------------------
					// ログインテーブル
					// ---------------------------
					$insLogin = sprintf('insert into login
					                     (
					                      loginId,
					                      password,
					                      staffNo,
					                      adminFlg,
					                      updateUser,
					                      updateDate,
					                      deleteFlg
					                     )
					                     values
					                     (
					                      "%s",
					                      "%s",
					                      %d,
					                      "%s",
					                      "%s",
					                      now(),
					                      "0"
					                     )',
					                    mysqli_real_escape_string($con, $_POST["loginId"]),
					                    mysqli_real_escape_string($con, password_hash($_POST["password"], PASSWORD_DEFAULT)),
					                    mysqli_real_escape_string($con, $_POST["staffNo"]),
					                    mysqli_real_escape_string($con, $_POST["adminFlg"]),
					                    mysqli_real_escape_string($con, "Administrator")
					            );
					// SQL実行
					mysqli_query($con, $insLogin);

					// ---------------------------
					// 社員テーブル
					// ---------------------------
					$insStaff = sprintf('insert into staff
					                     (
					                      staffNo,
					                      staffName,
					                      departmentId,
					                      mailAddress,
					                      hireDate,
					                      updateUser,
					                      updateDate,
					                      deleteFlg
					                     )
					                     values
					                     (
					                      %d,
					                      "%s",
					                      "%s",
					                      "%s",
					                      "%s",
					                      "%s",
					                      now(),
					                      "0"
					                     )',
					                     mysqli_real_escape_string($con, $_POST["staffNo"]),
					                     mysqli_real_escape_string($con, $_POST["staffName"]),
					                     mysqli_real_escape_string($con, $_POST["department"]),
					                     mysqli_real_escape_string($con, $_POST["mailAddress"]),
					                     mysqli_real_escape_string($con, str_replace("/", "", $_POST["hireDate"])),
					                     mysqli_real_escape_string($con, "Administrator")
					            );

					// SQL実行
					mysqli_query($con, $insStaff);
					// トランザクションコミット
					if (!mysqli_commit($con)) {
						exit();
					}

					$completeFlg = true;
				}
			}
		}
	?>



		<!-- コンテナ開始 -->
		<div id="container">
			<!-- ページ開始 -->
			<div id="page">
				<!-- ヘッダ開始 -->
				<div id="header">
					<p class="catch"><strong>勤怠管理システム</strong></p>
					<div align="right"><a href="../logout/logout.php">ログアウト</a></div>
					<hr class="none">
				</div>
				<!-- ヘッダ終了 -->
				<!-- コンテンツ開始 -->
				<div id="content">
					<!-- メインカラム開始 -->
					<div id="main">
						<div class="section normal update">
							<div class="heading">
								<h2>社員登録</h2>
							</div>
							<form action="createStaff.php" method="post" enctype='text/css'>
								<table width="650">
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

									      // 登録完了メッセージの表示
									      if ( $completeFlg ) {
									      	echo '<tr>';
											echo "ログインユーザの登録が完了しました。<br/>";
											echo "下記のリンクからログイン画面へ遷移して下さい。<br/>";
											echo "<a href='../login/login.php'>ログインはこちら</a>";
											echo '</tr>';
									      }
									?>

									<?php
										if ( !$completeFlg) {
									?>
									<tr>
										<td>ログインID：</td>
										<td>
											<input type="text" name="loginId" id="loginId" value="<?php if (isset($_POST["loginId"])) {echo $_POST["loginId"];}?>"/>
										</td>
									</tr>
									<tr>
										<td>パスワード：</td>
										<td>
											<input type="password" name="password" id="password" />
										</td>
									</tr>
									<tr>
										<td>パスワード(確認用)：</td>
										<td>
											<input type="password" name="password2" id="password2" />
										</td>
									</tr>
									<tr>
										<td>社員番号：</td>
										<td>
											<input type="text" name="staffNo" id="staffNo" value="<?php if (isset($_POST["staffNo"])) { echo $_POST["staffNo"]; } ?>"/>
										</td>
									</tr>
									<tr>
										<td>社員名：</td>
										<td>
											<input type="text" name="staffName" id="staffName" value="<?php if (isset($_POST["staffName"])) { echo $_POST["staffName"]; } ?>"/>
										</td>
									</tr>
									<tr>
										<td>メールアドレス：</td>
										<td>
											<input type="text" name="mailAddress" id="mailAddress" value="<?php if (isset($_POST["mailAddress"])) { echo $_POST["mailAddress"]; } ?>"/>
										</td>
									</tr>
									<tr>
										<td>部署：</td>
										<td>
											<?php

												$selSql = sprintf('select departmentId, departmentName from department');
												 // SQL実行
												$selSqlResult = mysqli_query($con, $selSql);

												// DB接続を閉じる
												closeConnection($con);
												if (mysqli_num_rows($selSqlResult) != 0) {
													echo '<select name="department" id="department" >';
													foreach($selSqlResult as $data) {
														if (isset($_POST["department"])) {
															if ($_POST["department"] == $data["departmentId"]) {
																echo '<option value="' . $data["departmentId"] .'" selected>' . $data["departmentName"] . '</option>';
															} else {
																echo '<option value="' . $data["departmentId"] .'" >' . $data["departmentName"] . '</option>';
															}
														} else {
															echo '<option value="' . $data["departmentId"] .'" >' . $data["departmentName"] . '</option>';
														}
													}
													echo '</select>';
												}
											?>
										</td>
									</tr>
									<tr>
										<td>入社日：</td>
										<td>
											<input type="text" name="hireDate" id="datepicker"  readonly="readonly" value="<?php if (isset($_POST["hireDate"])) { echo $_POST["hireDate"]; } ?>">
										</td>
									</tr>
									<tr>
										<td>権限</td>
										<td><input type="radio" name="adminFlg" value="0"
											<?php
												if (isset($_POST["adminFlg"])) {
													if ( $_POST["adminFlg"] == "0" ) {
														echo "checked";
													}
												} else {
													echo "checked";
												}
											?>
											 >一般
										<input type="radio" name="adminFlg" value="2"
											<?php
												if (isset($_POST["adminFlg"])) {
													if ( $_POST["adminFlg"] == "2" ) {
														echo "checked";
													}
												}
											?>
										>管理者</td>
									</tr>
									<tr>
										<td colspan='2' align='center'>
											<input type="submit" value="登録" />
										</td>
									</tr>
									<?php
									}
									?>
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
							<p><a href="./createStaff.php">社員登録</a>
							<?php
								if ( $_SESSION['adminFlg'] == "1" ) {
									echo '<br/>';
									echo '<a href="./selectStaff.php">一覧検索</a>';
								}
							?>
							</p>
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