<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="x-ua-compatible" content="IE=9" >
		<meta http-equiv="x-ua-compatible" content="IE=EmulateIE9" >
		<meta name="viewport" content="target-densitydpi=device-dpi, width=960, maximum-scale=1.0, user-scalable=yes">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="expires" content="0">
		<title>勤務年月選択</title>
		<link rel="stylesheet" type="text/css" href="../css/common.css">
	</head>
	<body>
		<script type="text/javascript">
			window.onunload = function(){};
			history.forward();

			function selectStaffBack( staffNo, staffName, department, workYears, workMonth, searchType ) {
				location.href = "../staff/selectStaff.php?staffNoBk=" + staffNo + "&staffName=" + staffName
				+ "&department=" + department + "&workYears=" + workYears + "&workMonth=" + workMonth + "&searchType=" + searchType;
			}
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

					$ua=$_SERVER['HTTP_USER_AGENT'];
					if((strpos($ua,'iPhone')!==false)||(strpos($ua,'iPod')!==false)||(strpos($ua,'Android')!==false)) {
						// メニューを表示
						echo '<div class="heading">';
						echo '<h2>メニュー</h2>';
						echo '</div>';
						echo '<p>';
						if ( $_SESSION['adminFlg'] != "1" ) {
							echo '<a href="../work/selectWorkDate.php?staffNo=' . $staffNo .'">入力年月選択</a>';
							echo '<br/>';
						}
						echo '<a href="../workStandard/workStandard.php?staffNo=' . $staffNo .'">基準値設定</a>';
						echo '<br/>';
						echo '<a href="../password/passChange.php?staffNo=' . $staffNo .'">パスワード変更</a>';
						if ( $_SESSION['adminFlg'] == "1" ) {
							echo '<br/>';
							echo '<a href="../staff/selectStaff.php">社員検索</a>';
						}
						echo '</p>';
					}
				?>
				<!-- ヘッダ終了 -->
				<!-- コンテンツ開始 -->
				<?php
					if((strpos($ua,'iPhone')!==false)||(strpos($ua,'iPod')!==false)||(strpos($ua,'Android')!==false)) {
						// スマホまたはiphone
						echo '<div>';
						echo '<div>';
					} else {
						// PC
						echo '<div id="content">';
						echo '<div id="main">';
					}
				?>
					<!-- メインカラム開始 -->
						<div class="section normal update">
							<div class="heading">
								<h2>入力年月選択</h2>
							</div>
							<form action="selectWorkDate.php" method="post" enctype='text/css'>
								<table>
									<tr>
										<td align="left">入力年月：</td>
										<td>
											<?php

												$disabledFlg = "";
												// 管理者チェック
												if ( $_SESSION['adminFlg'] == "1" ) {
													$disabledFlg = "disabled";
												}


												if ( isset($_GET["selectWorkYear"]) ) {
													$_POST["selectWorkYear"] = $_GET["selectWorkYear"];
												}

												if ( isset($_GET["selectWorkMonth"]) ) {
													$_POST["selectWorkMonth"] = $_GET["selectWorkMonth"];
												}

												$selSql = sprintf('select DISTINCT substring(workYears, 1, 4) as workYears from attendancesubmission order by workYears asc');
												// SQL実行
												$selSqlResult = mysqli_query($con, $selSql);

												if ( mysqli_num_rows( $selSqlResult ) != 0 ) {
													echo '<select name="selectWorkYear" id="selectWorkYear"  ' . $disabledFlg . '>';
													foreach( $selSqlResult as $data ) {
														if (isset($_POST["selectWorkYear"])) {
															if ($_POST["selectWorkYear"] == $data["workYears"]) {
																echo '<option value="' . $data["workYears"] .'" selected>' . $data["workYears"] . '</option>';
															} else {
																echo '<option value="' . $data["workYears"] .'" >' . $data["workYears"] . '</option>';
															}
														} else {
															echo '<option value="' . $data["workYears"] .'" >' . $data["workYears"] . '</option>';
														}
													}
													echo '</select>&nbsp;&nbsp;年&nbsp;&nbsp;';
												}

												$selSql = sprintf('select DISTINCT substring(workYears, 5, 2) as workMonth from attendancesubmission order by workYears asc');
												// SQL実行
												$selSqlResult = mysqli_query($con, $selSql);


												if ( mysqli_num_rows( $selSqlResult ) != 0 ) {
													echo '<select name="selectWorkMonth" id="selectWorkMonth" ' . $disabledFlg . '>';
													foreach( $selSqlResult as $data ) {
														if ( isset($_POST["selectWorkMonth"]) ) {
															if ( $_POST["selectWorkMonth"] == $data["workMonth"] ) {
																echo '<option value="' . $data["workMonth"] .'" selected>' . $data["workMonth"] . '</option>';
															} else {
																echo '<option value="' . $data["workMonth"] .'" >' . $data["workMonth"] . '</option>';
															}
														} else {
															echo '<option value="' . $data["workMonth"] .'" >' . $data["workMonth"] . '</option>';
														}
													}
													echo '</select>&nbsp;&nbsp;月&nbsp;&nbsp;';
												}

												// DB接続を閉じる
												closeConnection($con);

											?>
										</td>
										<?php
											if ( $_SESSION['adminFlg'] != "1" ) {
												echo "<td colspan='2' align='center'>";
												echo '<input type="submit" value="検索" />';
												echo '</td>';
											}
										?>
									</tr>
								</table>
							</form>
						</div>
						<?php
							if ( isset($_POST["selectWorkYear"]) ) {
							// 検索ボタンが押下された場合
								$contents = "";
								//出力バッファリングを開始
								ob_start();
								//出力バッファに外部ファイルを読み込む
								include('./workInputList.php');
								$contents = ob_get_contents();
								ob_end_clean();
								echo $contents;
								$_POST = array();
							}
						?>
					</div>
					<!-- メインカラム終了 -->
					<!-- サイドバー開始 -->
					<?php
						if((strpos($ua,'iPhone')!==false)||(strpos($ua,'iPod')!==false)||(strpos($ua,'Android')!==false)) {
						} else {
							$contents = "";
							//出力バッファリングを開始
							ob_start();
							//出力バッファに外部ファイルを読み込む
							include('../menu/menu.php');
							$contents = ob_get_contents();
							ob_end_clean();
							echo $contents;

						}
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