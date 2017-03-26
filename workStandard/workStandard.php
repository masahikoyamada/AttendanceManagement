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
		<title>基準値設定</title>
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
								<h2>基準値設定</h2>
							</div>
							<?php
								if ( isset($_POST["startT"]) ) {
									// 登録ボタンが押下された場合
									// 勤務基準テーブルから社員番号に紐づく情報を取得
									$sqlWs = sprintf('select startTime, endTime, recess from workstandard where staffNo = "%s" and deleteFlg = "0"',
									                 mysqli_real_escape_string($con, $staffNo));

									// 勤務基準情報
									$resultWs = mysqli_query($con, $sqlWs);

									// 画面の情報を変数に格納
									$startTime = sprintf("%02d", $_POST["startT"]) . sprintf("%02d", $_POST["startH"]);
									$endTime = sprintf("%02d",$_POST["endT"]) . sprintf("%02d",$_POST["endH"]);
									$recess = $_POST["recess"];

									if ( mysqli_num_rows($resultWs) == 0 ) {
										// 登録されていない場合
										// insert文発行
										$insSql = sprintf('insert into workstandard
										                  (
										                    staffNo,
			                                                startTime,
			                                                endTime,
			                                                recess,
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
										                   now(),
										                   "0"
										                  )',
										                  mysqli_real_escape_string($con, $staffNo),
										                  mysqli_real_escape_string($con, $startTime),
										                  mysqli_real_escape_string($con, $endTime),
										                  mysqli_real_escape_string($con, $recess),
										                  mysqli_real_escape_string($con, $_SESSION['userId'])
										);
										// SQL実行
										 mysqli_query($con, $insSql);
									} else {
										// 登録されている場合
										// update文発行
										$upSql = sprintf('update workstandard
										                  set
			                                                startTime = "%s",
			                                                endTime = "%s",
			                                                recess = "%s",
			                                                updateUser = "%s",
			                                                updateDate = now()
			                                              where
			                                                staffNo = "%s"
										                  ',
										                  mysqli_real_escape_string($con, $startTime),
										                  mysqli_real_escape_string($con, $endTime),
										                  mysqli_real_escape_string($con, $recess),
										                  mysqli_real_escape_string($con, $_SESSION['userId']),
										                  mysqli_real_escape_string($con, $staffNo)
										);
										// SQL実行
										 mysqli_query($con, $upSql);
									}
								}
							?>
							<form action="workStandard.php" method="post" enctype='text/css'>
								<table>
									<tr>
										<td align="left">開始時間：</td>
										<td>
											<?php
												// 勤務基準テーブルから社員番号に紐づく情報を取得

												$sqlWs = sprintf('select startTime, endTime, recess from workstandard where staffNo = "%s" and deleteFlg = "0"',
												                 mysqli_real_escape_string($con, $staffNo));

												$resultWs = mysqli_query($con, $sqlWs);

												// 勤務基準情報
												$recordSet = mysqli_fetch_assoc($resultWs);

												// 開始時
												echo '<select name="startT">';
												for ($i = 0; $i <= 23; $i++) {
													if (substr($recordSet["startTime"], 0, 2) == sprintf("%02d", $i)) {
														echo '<option value="' . sprintf("%02d", $i) .'" selected>' . sprintf("%02d", $i) . '</option>';
													} else {
														echo '<option value="' . sprintf("%02d", $i) .'">' . sprintf("%02d", $i) . '</option>';
													}
												}
												echo '</select>';
												echo '：';

												// 開始分
												echo '<select name="startH">';
												if (substr($recordSet["startTime"], 2, 2) == sprintf("%02d", 0)) {
													echo '<option value="0" selected >00</option>';
												} else {
													echo '<option value="0">00</option>';
												}

												if (substr($recordSet["startTime"], 2, 2) == sprintf("%02d", 15)) {
													echo '<option value="15" selected >15</option>';
												} else {
													echo '<option value="15">15</option>';
												}

												if (substr($recordSet["startTime"], 2, 2) == sprintf("%02d", 30)) {
													echo '<option value="30" selected >30</option>';
												} else {
													echo '<option value="30">30</option>';
												}

												if (substr($recordSet["startTime"], 2, 2) == sprintf("%02d", 45)) {
													echo '<option value="45" selected >45</option>';
												} else {
													echo '<option value="45">45</option>';
												}
												echo '</select>';
												echo '';
											?>
										</td>
									</tr>
									<tr>
										<td align="left">終了時間：</td>
										<td>
											<?php

												// 終了時
												echo '<select name="endT">';
												for ($i = 0; $i <= 23; $i++) {
													if (substr($recordSet["endTime"], 0, 2) == sprintf("%02d", $i)) {
														echo '<option value="' . sprintf("%02d", $i) .'" selected>' . sprintf("%02d", $i) . '</option>';
													} else {
														echo '<option value="' . sprintf("%02d", $i) .'">' . sprintf("%02d", $i) . '</option>';
													}
												}
												echo '</select>';
												echo '：';

												// 終了分
												echo '<select name="endH">';
												if (substr($recordSet["endTime"], 2, 2) == sprintf("%02d", 0)) {
													echo '<option value="0" selected >00</option>';
												} else {
													echo '<option value="0">00</option>';
												}

												if (substr($recordSet["endTime"], 2, 2) == sprintf("%02d", 15)) {
													echo '<option value="15" selected >15</option>';
												} else {
													echo '<option value="15">15</option>';
												}

												if (substr($recordSet["endTime"], 2, 2) == sprintf("%02d", 30)) {
													echo '<option value="30" selected >30</option>';
												} else {
													echo '<option value="30">30</option>';
												}

												if (substr($recordSet["endTime"], 2, 2) == sprintf("%02d", 45)) {
													echo '<option value="45" selected >45</option>';
												} else {
													echo '<option value="45">45</option>';
												}
												echo '</select>';
												echo '</td>';
											?>
										</td>
									</tr>
									<tr>
										<td align="left"> 休憩時間：</td>
										<td>
											<input type="text" name="recess" size="5" maxlength="4" style="text-align: right;" value="<?php echo $recordSet["recess"]; ?>" />
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