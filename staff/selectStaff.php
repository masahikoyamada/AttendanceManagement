<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta http-equiv="content-script-type" content="text/javascript" />
		<meta http-equiv="x-ua-compatible" content="IE=9" />
		<meta http-equiv="x-ua-compatible" content="IE=EmulateIE9" />
		<meta name="viewport" content="width=1200, minimum-scale=0.1">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="expires" content="0">
		<title>社員検索</title>
		<link rel="stylesheet" type="text/css" href="../css/common.css">
	</head>
	<body>
	<script type="text/javascript">
		function deleteDialog( staffNo ) {
			var message = "社員番号 " + staffNo + "を削除します。\r\nよろしいですか?";
			if ( window.confirm(message)) {
				// OKが押下された場合
				// 削除処理を行う。
				location.href = "./deleteStaff.php?staffNo=" + staffNo;
			}
		}
	</script>
		<!-- コンテナ開始 -->
		<div id="container">
			<!-- ページ開始 -->
			<div id="page">
				<!-- ヘッダ開始 -->
				<div id="header">
					<p class="catch"><strong>勤怠管理システム</strong>
					<div align="right"><a href="../logout/logout.php">ログアウト</a></div>
					</p>
					<hr class="none">
				</div>
				<!-- ヘッダ終了 -->
				<!-- コンテンツ開始 -->
				<div id="content">
					<!-- メインカラム開始 -->
					<div id="main">
						<div class="section normal update">
							<div class="heading">
								<h2>一覧検索</h2>
							</div>
							<form action="selectStaff.php" method="post" enctype='text/css'>
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

									      if (!isset($_POST["staffNo"]) && isset($_GET["staffNoBk"])) {
									      		// パラメータ値をPOSTに格納
									      		$_POST["staffNo"] = $_GET["staffNoBk"];
									      		$_POST['staffName'] = $_GET['staffName'];
									      		$_POST['department'] = $_GET['department'];
									      		$_POST['workYears'] = $_GET['workYears'];
									      		$_POST['workMonth'] = $_GET['workMonth'];
									      		$_POST['searchType'] = $_GET['searchType'];
									      }
									?>
									<tr>
										<td>社員番号</td>
										<td><input type="text" name="staffNo" id="staffNo" value="<?php if (isset($_POST["staffNo"])) { echo $_POST["staffNo"]; } ?>"/></td>
									</tr>
									<tr>
										<td>社員名</td>
										<td>
											<input type="text" name="staffName" id="staffName" value="<?php if (isset($_POST["staffName"])) { echo $_POST["staffName"]; } ?>"/>
											<input type="radio" name="searchType" value="0"
												<?php
													if (isset($_POST["searchType"])) {
														if ( $_POST["searchType"] == "0" ) {
															echo "checked";
														}
													} else {
														echo "checked";
													}
												?>
												 >完全一致
											<input type="radio" name="searchType" value="1"
												<?php
													if (isset($_POST["searchType"])) {
														if ( $_POST["searchType"] == "1" ) {
															echo "checked";
														}
													} else {
														echo "checked";
													}
												?>
											>部分一致
										</td>
									</tr>
									<tr>
										<td>部署</td>
										<td>
											<?php
													require (dirname(__FILE__) . "/../common/common.php");
												// DB接続
												$con = getConnection();

												$selSql = sprintf('select departmentId, departmentName from department');
												 // SQL実行
												$selSqlResult = mysqli_query($con, $selSql);

												// DB接続を閉じる
												closeConnection($con);

												if (mysqli_num_rows($selSqlResult) != 0) {
													echo '<select name="department" id="department" >';
													echo '<option value="0"></option>';
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
										<td>勤務表年月</td>
										<td>
											<?php
												// DB接続
												$con = getConnection();

												$selSql = sprintf('select DISTINCT substring(workYears, 1, 4) as workYears from attendancesubmission order by workYears asc');
												 // SQL実行
												$selSqlResult = mysqli_query($con, $selSql);

												if (mysqli_num_rows($selSqlResult) != 0) {
													echo '<select name="workYears" id="workYears" >';
													echo '<option value="0"></option>';
													foreach($selSqlResult as $data) {
														if (isset($_POST["workYears"])) {
															if ($_POST["workYears"] == $data["workYears"]) {
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


												if (mysqli_num_rows($selSqlResult) != 0) {
													echo '<select name="workMonth" id="workMonth" >';
													echo '<option value="0"></option>';
													foreach($selSqlResult as $data) {
														if (isset($_POST["workMonth"])) {
															if ($_POST["workMonth"] == $data["workMonth"]) {
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
									</tr>
									<tr>
										<td colspan='2' align='center'>
											<input type="submit" value="検索" />
										</td>
									</tr>
								</table>
							</form>
							<?php
								if (isset($_POST["staffNo"])) {
									// 検索ボタンが押下された場合
									echo '<div id="contentPc">';
									if ( empty($_POST["staffNo"])
										&& empty($_POST["staffName"])
										&& empty($_POST["department"])
										&& empty($_POST["workYears"])
										&& empty($_POST["workMonth"])) {
										// 検索条件を指定していない場合
										// 全件検索を行う
										// DB接続
										$con = getConnection();
										$serStaff = sprintf('select s.staffNo as staffNo, s.staffName as staffName, d.departmentName as departmentName, s.hireDate as hireDate, sub.workYears as workYears, sub.submissionStatus as submissionStatus
				                                             from staff s
				                                             left outer join department d
				                                             on (s.departmentId = d.departmentId)
				                                             left outer join login l
				                                             on (s.staffNo = l.staffNo)
				                                             left outer join attendancesubmission sub
				                                             on (s.staffNo = sub.staffNo)
				                                             where l.adminFlg = "0"
				                                             and l.deleteFlg = "0"
				                                             order by s.staffNo asc, sub.workYears asc');
				                        // SQL実行
				                        $result = mysqli_query($con, $serStaff);

				                        // DB接続を閉じる
				                        closeConnection($con);
									} else {
										// 検索条件を指定している場合
										$staffNoWhere    = '';
										$staffNameWhere  = '';
										$departmentWhere = '';
										$workYears       = '';
										$workMonth       = '';

										// 社員番号
										if ( !empty($_POST["staffNo"]) ) {
											// 社員番号が指定している場合
											$staffNoWhere = ' and s.staffNo = ' . $_POST["staffNo"];
										}

										// 社員名
										if ( !empty( $_POST["staffName"]) ) {
											// 社員名が指定されている場合
											if ($_POST["searchType"] == "1") {
												// 部分一致の場合
												$staffNameWhere = ' and s.staffName like "%%' . $_POST["staffName"] . '%%" ';
											} else {
												// 完全一致の場合
												$staffNameWhere = ' and s.staffName = "' . $_POST["staffName"] . '" ';
											}
										}

										// 部署
										if ( !empty( $_POST["department"]) ) {
											// 部署が選択された場合
											$departmentWhere = ' and d.departmentId = "'. $_POST["department"] .'" ';
										}

										// 勤怠年度(年)
										if ( !empty( $_POST["workYears"]) ) {
											$workYears = ' and substring(workYears, 1, 4) = "' . $_POST["workYears"] . '" ';
										}

										// 勤怠年度(月)
										//
										if ( !empty( $_POST["workMonth"]) ) {
											$workMonth = ' and substring(workYears, 5, 2) = "' . $_POST["workMonth"] . '" ';
										}

										// DB接続
										$con = getConnection();
										// SQL文
										$serStaff = sprintf('select s.staffNo as staffNo, s.staffName as staffName, d.departmentName as departmentName, s.hireDate as hireDate, sub.workYears as workYears, sub.submissionStatus as submissionStatus
				                                             from staff s
				                                             left outer join department d
				                                             on (s.departmentId = d.departmentId)
				                                             left outer join login l
				                                             on (s.staffNo = l.staffNo)
                                                             left outer join attendancesubmission sub
                                                             on (s.staffNo = sub.staffNo)
                                                             where l.adminFlg != "1" and s.deleteFlg = "0" '
				                                             . $staffNoWhere . $staffNameWhere . $departmentWhere .
												             $workYears . $workMonth .
				                                             ' order by s.staffNo asc, sub.workYears asc');
				                        // SQL実行
				                        $result = mysqli_query($con, $serStaff);

				                        // DB接続を閉じる
				                        closeConnection($con);
									}

									if (mysqli_num_rows($result) >= 1) {
										// セッションのstaffNoの初期化
										$_SESSION['staffNo'] = "";

										// 検索結果が1件以上の場合
										echo '<div>';
										echo '<table border="1" width="600">';
										echo '<tr bgcolor="#C0C0C0">';
										echo '<td>社員番号</td>';
										echo '<td>社員名</td>';
										echo '<td>部署</td>';
										echo '<td>入社日</td>';
										echo '<td>勤務表年月</td>';
										echo '<td>入力状況</td>';
										echo '<td></td>';
										echo '<td></td>';
										echo '<tr/>';
										$cnt = 1;
										// 検索結果のループ
										foreach ($result as $data) {
											echo '<tr>';
											echo '<td>' . $data["staffNo"] . '</td>';
											echo '<td>' . $data["staffName"] . '</td>';
											echo '<td>' . $data["departmentName"] . '</td>';
											echo '<td>' . substr($data["hireDate"], 0, 4) . '/' . substr($data["hireDate"], 4, 2) . '/' . substr($data["hireDate"], 6, 2) . '</td>';
											echo '<td>' . substr($data["workYears"], 0, 4) . '/' . substr($data["workYears"], 4, 2) . '</td>';
											if ( $data["submissionStatus"] == "1" ) {
												echo '<td>未入力</td>';
											} else {
												echo '<td>完了</td>';
											}
											echo '<td><a href="../work/selectWorkDate.php?staffNo=' . $data["staffNo"] .'&selectWorkYear=' .substr($data["workYears"], 0, 4)
											. '&selectWorkMonth=' . substr($data["workYears"], 4, 2)
											. '&staffName=' . $_POST["staffName"]
											. '&department=' . $_POST["department"]
											. '&workYears=' . $_POST["workYears"]
											. '&workMonth=' . $_POST["workMonth"]
											. '&searchType=' . $_POST["searchType"]
											. '&staffNoBk=' . $_POST["staffNo"]
											. '">勤務表入力</a></td>';
											echo '<td><input type="button" value="削除" onClick="deleteDialog('. $data["staffNo"] .')"></td>';
											echo '</tr>';
										}
										echo '</table>';
										echo '</div>';
									} else {
										// 検索結果が0件の場合
										echo '検索結果が0件でした。';
									}
									echo '</div>';
								}
							?>
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