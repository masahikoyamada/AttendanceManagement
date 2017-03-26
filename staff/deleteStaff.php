<?php
	require (dirname(__FILE__) . "/../common/common.php");

	session_start();
	// DB接続
	$con = getConnection();

	// オートコミットをOFF
	mysqli_autocommit($con, FALSE);

	// 社員番号に紐づくログインテーブルを削除
	// update文発行
	$delLoginSql = sprintf('update login
	                  set
                        deleteFlg = "1",
                        updateUser = "%s",
                        updateDate = now()
                      where
                        staffNo = %d
	                  ',
                 mysqli_real_escape_string($con, $_SESSION["userId"]),
                 mysqli_real_escape_string($con, $_GET["staffNo"])
                 );
	// SQL実行
	mysqli_query($con, $delLoginSql);

	// 社員番号に紐づく社員テーブルを削除
	// update文発行
	$delStaffSql = sprintf('update staff
	                  set
                        deleteFlg = "1",
                        updateUser = "%s",
                        updateDate = now()
                      where
                        staffNo = %d
	                  ',
                 mysqli_real_escape_string($con, $_SESSION["userId"]),
                 mysqli_real_escape_string($con, $_GET["staffNo"])
                 );
	// SQL実行
	mysqli_query($con, $delStaffSql);

	// 社員番号に紐づく勤怠テーブルを削除
	// update文発行
	$delAttendanceSql = sprintf('update attendance
	                  set
                        deleteFlg = "1",
                        updateUser = "%s",
                        updateDate = now()
                      where
                        staffNo = %d
	                  ',
                 mysqli_real_escape_string($con, $_SESSION["userId"]),
                 mysqli_real_escape_string($con, $_GET["staffNo"])
                 );
	// SQL実行
	mysqli_query($con, $delAttendanceSql);

	// 社員番号に紐づく勤怠基準テーブルを削除
	// update文発行
	$delWorkStandardSql = sprintf('update workStandard
	                  set
                        deleteFlg = "1",
                        updateUser = "%s",
                        updateDate = now()
                      where
                        staffNo = %d
	                  ',
                 mysqli_real_escape_string($con, $_SESSION["userId"]),
                 mysqli_real_escape_string($con, $_GET["staffNo"])
                 );
	// SQL実行
	mysqli_query($con, $delWorkStandardSql);


	// トランザクションコミット
	if (!mysqli_commit($con)) {
		exit();
	}

	// 社員検索画面をリロードする。
	header("Location: ./selectStaff.php");

	// 終了
	exit;
?>