<!-- ヘッダ開始 -->
<div id="header">
	<p class="catch"><strong>勤怠管理システム</strong>
		<?php
			require (dirname(__FILE__) . "/../common/common.php");

			// 社員検索から遷移した場合
			if ( empty($_SESSION['staffNo']) ) {
				// セッション情報の社員番号が空白の場合
				$staffNo = $_GET['staffNo'];
				// セッション情報に格納
				// パラメータの社員番号を設定
				$_SESSION['staffNo'] = $_GET['staffNo'];
				$_SESSION['staffNoBk'] = $_GET['staffNoBk'];
				$_SESSION['staffName'] = $_GET['staffName'];
				$_SESSION['department'] = $_GET['department'];
				$_SESSION['workYears'] = $_GET['workYears'];
				$_SESSION['workMonth'] = $_GET['workMonth'];
				$_SESSION['searchType'] = $_GET['searchType'];
			} else {
				$staffNo = $_SESSION['staffNo'];
			}

			$con = getConnection();
			$sql = sprintf('select staffName from staff where staffNo = "%s" and deleteFlg = "0"',
			               mysqli_real_escape_string($con, $staffNo));
			$result = mysqli_query($con, $sql);

			// 結果を連想配列で取得
			$recordSet = mysqli_fetch_assoc($result);

			// セッションに社員名を格納
			//$_SESSION['staffName'] = $recordSet["staffName"];
			echo '<div align="right">' . $staffNo . '&nbsp;&nbsp;&nbsp;' . $recordSet["staffName"] . '&nbsp;&nbsp;&nbsp; <a href="../logout/logout.php">ログアウト</a></div>';

			if ( $_SESSION['adminFlg'] == "1" ) {
				// 戻るボタン表示
			?>
			<br/><div align="right"><input type="button" name="back" onclick="selectStaffBack('<?php echo $_SESSION['staffNoBk'];?>',
						'<?php echo $_SESSION['staffName']; ?>',
						'<?php echo $_SESSION['department']; ?>',
						'<?php echo $_SESSION['workYears'];?>',
						'<?php echo $_SESSION['workMonth']; ?>',
						'<?php echo $_SESSION['searchType']; ?>');" value="戻る"></div>
			<?php
			}
			?>
	</p>
	<hr class="none">
</div>
<!-- ヘッダ終了 -->