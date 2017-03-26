<?php
	require (dirname(__FILE__) . "/../common/common.php");

	// SQL実行
	$resultStaff = selectAllStaff();

	// システム日付取得
	$now = getNow();

	$subStrNow = substr( $now, '0', '6' );

	// 取得した社員情報のループ
	foreach ( $resultStaff as $recordSetStaff ) {
		// 勤怠提出テーブルに今月分の情報が登録されているかのチェック
		$checkSubmission = checkAttendanceSubmission( $recordSetStaff['staffNo'], $subStrNow );

		// 勤怠提出テーブルに今月分の情報を未提出で登録
		if ( empty( $checkSubmission ) ) {
			insertAttendanceSubmission ( $recordSetStaff['staffNo'], $subStrNow );
		}
	}


	/**
	 * 社員情報を全て取得
	 * @return 社員情報
	 */
	function selectAllStaff () {
		// DB接続
		$con = getConnection();


		// 社員情報を全て取得
		$staffSql = sprintf('select staffNo from staff where deleteFlg = "0" order by staffNo asc');

		// SQL実行
		$resultStaff = mysqli_query($con, $staffSql);

		// DB切断
		closeConnection($con);

		// 結果を返却
		return $resultStaff;
	}

	/**
	 * 勤怠提出テーブルから社員番号と作業年月に紐づく情報が存在するかのチェック
	 * @param int $staffNo
	 * @param String $workYears
	 * @return 勤怠提出テーブル情報
	 */
	function checkAttendanceSubmission ( $staffNo, $workYears ) {
		// DB接続
		$con = getConnection();

		// 勤怠提出テーブルから情報取得
		$sql = sprintf('select
			                  staffNo,
			                  workYears
			                from
			                    attendancesubmission
			                where
			                       staffNo = "%s"
			                       and workYears = "%s"',
				mysqli_real_escape_string($con, $staffNo),
				mysqli_real_escape_string($con, $workYears));

		// SQL実行
		$result = mysqli_query($con, $sql);

		// 結果を連想配列で取得
		$recordSet = mysqli_fetch_assoc($result);

		// DB接続を閉じる
		closeConnection($con);

		// 結果を返却
		return $recordSet;

	}

	/**
	 * 勤怠提出テーブルに情報を登録
	 * @param int $staffNo
	 * @param String $workYears
	 */
	function insertAttendanceSubmission ( $staffNo, $workYears ) {
		// DB接続
		$con = getConnection();
		// 勤怠提出テーブルに現在日付の情報を登録

		// Insert文発行
		$insSql = sprintf('insert into attendancesubmission
			                  (
			                    staffNo,
                                workYears,
                                submissionStatus,
                                updateUser,
                                updateDate,
                                deleteFlg
                               )
							   values
							   (
							    %d,
							    "%s",
							    "1",
							    "Administrator",
							    now(),
							    "0"
							   )',
				mysqli_real_escape_string($con, $staffNo),
				mysqli_real_escape_string($con, $workYears)
				);

		// SQL実行
		mysqli_query($con, $insSql);

		// DB接続を閉じる
		closeConnection($con);
	}

?>