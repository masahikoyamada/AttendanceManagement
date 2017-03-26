<?php
	if ( isset($_POST["startT01"]) ) {
		require (dirname(__FILE__) . "/../common/common.php");
		session_start();
	}

	$weekday = array( "日", "月", "火", "水", "木", "金", "土" );

	$arrCode = array( "", "祭", "有", "半", "特", "夏", "欠", "遅", "早" );

	// 月初取得
	$firstDate = date('Y-m-d', strtotime('first day of ' . $_POST["selectWorkYear"] . "-". $_POST["selectWorkMonth"]));
	// 日数取得
	$dateCount = date("t", mktime(0, 0, 0, substr($firstDate, 5, 2), 1, substr($firstDate, 0, 4)));

	$staffNo = $_SESSION["staffNo"];

	// 本日日付取得
	$now = getNow();
	// DB接続
	$con = getConnection();

	if ( isset($_POST["startT01"]) ) {
		// 登録ボタンが押下された場合

		// オートコミットをOFF
		mysqli_autocommit($con, FALSE);
		for ( $i = 1; $i <= $dateCount; $i++ ) {
			// 文字列結合
			// 開始時間
			$startTime = sprintf("%02d", $_POST["startT".sprintf("%02d", $i)]) . sprintf("%02d", $_POST["startH".sprintf("%02d", $i)]);
			// 終了時間
			$endTime = sprintf("%02d", $_POST["endT".sprintf("%02d", $i)]) . sprintf("%02d", $_POST["endH".sprintf("%02d", $i)]);
			// 休憩
			$recess = $_POST["recess" . sprintf("%02d", $i)];
			// 勤務時間
			$officeHours = $_POST["hiOfficeHours" . sprintf("%02d", $i)];
			// 残業時間
			$overTime = $_POST["hiOvertime" . sprintf("%02d", $i)];
			// 作業備考
			$note = $_POST["note" . sprintf("%02d", $i)];
			// コード
			$code = $_POST["code" . sprintf("%02d", $i)];

			// 日付の結合
			$workingDay = $_POST["selectWorkDate"] . sprintf("%02d", $i);
			// 登録するデータが既に登録されているかのチェック
			$selSql = sprintf('select staffNo from attendance where staffNo = %d and workingDay = "%s"',
			          mysqli_real_escape_string($con, $staffNo),
			          mysqli_real_escape_string($con, $workingDay));
			 // SQL実行
			$selSqlResult = mysqli_query($con, $selSql);

			if (mysqli_num_rows($selSqlResult) == 0) {
				// Insert文発行
				$insSql = sprintf('insert into attendance
				                  (
				                    staffNo,
                                    workingDay,
                                    startTime,
                                    endTime,
                                    recess,
                                    officeHours,
                                    overTime,
                                    note,
                                    code,
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
				                   "%s",
				                   "%s",
				                   "%s",
				                   "%s",
				                   now(),
				                   "0"
				                  )',
				                  mysqli_real_escape_string($con, $staffNo),
				                  mysqli_real_escape_string($con, $workingDay),
				                  mysqli_real_escape_string($con, $startTime),
				                  mysqli_real_escape_string($con, $endTime),
				                  mysqli_real_escape_string($con, $recess),
				                  mysqli_real_escape_string($con, $officeHours),
				                  mysqli_real_escape_string($con, $overTime),
				                  mysqli_real_escape_string($con, $note),
				                  mysqli_real_escape_string($con, $code),
				                  mysqli_real_escape_string($con, $_SESSION['userId'])
				);
				// SQL実行
				 mysqli_query($con, $insSql);
			} else {
				// Update文発行
				$upSql = sprintf('update attendance
				                  set
                                    startTime = "%s",
                                    endTime = "%s",
                                    recess = "%s",
                                    officeHours = "%s",
                                    overTime = "%s",
                                    note = "%s",
                                    code = "%s",
                                    updateUser = "%s",
                                    updateDate = now()
                                  where
                                    staffNo = "%s"
                                    and workingDay = "%s"
				                  ',
				                  mysqli_real_escape_string($con, $startTime),
				                  mysqli_real_escape_string($con, $endTime),
				                  mysqli_real_escape_string($con, $recess),
				                  mysqli_real_escape_string($con, $officeHours),
				                  mysqli_real_escape_string($con, $overTime),
				                  mysqli_real_escape_string($con, $note),
				                  mysqli_real_escape_string($con, $code),
				                  mysqli_real_escape_string($con, $_SESSION['userId']),
				                  mysqli_real_escape_string($con, $staffNo),
				                  mysqli_real_escape_string($con, $workingDay)
				);
				// SQL実行
				 mysqli_query($con, $upSql);
			}
		}

		// 提出のチェックボックスにチェックがついている場合
		if ( !empty( $_POST["subMission"] ) ) {
			// UPDATE文発行
			$upSubmission = sprintf('update attendancesubmission
									 set
									    submissionStatus = "2",
									    updateUser = "%s",
									    updateDate = now()
									 where
									    staffNo = %d
									    and workYears = "%s"
									',
									mysqli_real_escape_string($con, $_SESSION['userId']),
									mysqli_real_escape_string($con, $staffNo),
									mysqli_real_escape_string($con, $_POST["selectWorkDate"]));

			// SQL実行
			mysqli_query($con, $upSubmission);
		} else {
			// UPDATE文発行
			$upSubmission = sprintf('update attendancesubmission
									 set
									    submissionStatus = "1",
									    updateUser = "%s",
									    updateDate = now()
									 where
									    staffNo = %d
									    and workYears = "%s"
									',
									mysqli_real_escape_string($con, $_SESSION['userId']),
									mysqli_real_escape_string($con, $staffNo),
									mysqli_real_escape_string($con, $_POST["selectWorkDate"]));

			// SQL実行
			mysqli_query($con, $upSubmission);
		}

		// トランザクションコミット
		if (!mysqli_commit($con)) {
			exit();
		}
		// 入力年月選択画面を表示
		header("Location: ./selectWorkDate.php?selectWorkYear=" . substr($_POST["selectWorkDate"], 0, 4) . "&selectWorkMonth=" . substr($_POST["selectWorkDate"], 4, 2));

		// 終了
		exit;
	}
?>
	<script type="text/javascript">
		window.onunload = function(){};
		history.forward();

		function setCalcTime(obj, day) {

			// hiddenから年を取得
			var year = document.workInputList.selectWorkDate.value.substr(0, 4);

			// hiddenから月を取得
			var month = document.workInputList.selectWorkDate.value.substr(4, 2);

			// 選択されたname属性を取得
			var name = obj.getAttribute('name');

			// 取得したname属性から日付の文字列を取得
			var strDate = name.substr(name.length - 2, 2);

			// 取得したname属性から日付以外の文字列を取得
			var strName = name.slice(0, -2);

			if (strName == "startT"
			|| strName == "startH"
			|| strName == "endT"
			|| strName == "endH") {
				// 選択されたindexを取得
				var idx = obj.selectedIndex;

				// 値取得
				var value = obj.options[idx].value;

				if (strName == "startT") {
					// 開始時を選択した場合
					// 開始分
					var startH = getSelectValue("startH" + strDate);
					// 終了時
					var endT = getSelectValue("endT" + strDate);
					// 終了分
					var endH = getSelectValue("endH" + strDate);

					// 勤務時間の計算
					calcTime(year, month, day, value, startH, endT, endH, strDate);
				} else if (strName == "startH") {
					// 開始分を選択した場合
					// 開始時
					var startT = getSelectValue("startT" + strDate);
					// 終了時
					var endT = getSelectValue("endT" + strDate);
					// 終了分
					var endH = getSelectValue("endH" + strDate);

					// 勤務時間の計算
					calcTime(year, month, day, startT, value, endT, endH, strDate);
				} else if (strName == "endT") {
					// 終了時を選択した場合
					// 開始時
					var startT = getSelectValue("startT" + strDate);
					// 開始分
					var startH = getSelectValue("startH" + strDate);
					// 終了時
					var endH = getSelectValue("endH" + strDate);

					// 勤務時間の計算
					calcTime(year, month, day, startT, startH, value, endH, strDate);
				} else if (strName == "endH") {
					// 終了分を選択した場合
					// 開始時
					var startT = getSelectValue("startT" + strDate);
					// 開始分
					var startH = getSelectValue("startH" + strDate);
					// 終了時
					var endT = getSelectValue("endT" + strDate);

					// 勤務時間の計算
					calcTime(year, month, day, startT, startH, endT, value, strDate);
				}
			} else {
				// 休憩時間が変更された場合
				if (obj.value != "") {
					var strRecess = ( obj.value * 3600000 ) / ( 60 * 60 * 1000 );

					// 開始時
					var startT = getSelectValue("startT" + strDate);
					// 開始分
					var startH = getSelectValue("startH" + strDate);
					// 終了時
					var endT = getSelectValue("endT" + strDate);
					// 終了時
					var endH = getSelectValue("endH" + strDate);
					// 勤務時間の計算
					calcTime(year, month, day, startT, startH, endT, endH, strDate, strRecess);
				}
			}
		}

		function getSelectValue( name ) {
			var obj = document.getElementsByName(name)[0];
			var idx = obj.selectedIndex;
			var val = obj.options[idx].value;
			return ('0' + val).slice(-2);
		}

		function calcTime(year, month, day, startT, startH, endT, endH, strDate, recess) {
			// 合計計算
			var date1 = new Date (parseInt(year), parseInt(month - 1), parseInt(day), parseInt(endT), parseInt(endH));
			var date2 = new Date (parseInt(year), parseInt(month - 1), parseInt(day), parseInt(startT), parseInt(startH));

			var calcTime = ( date1.getTime() - date2.getTime() ) / ( 60 * 60 * 1000 );

			// 休憩時間を引く
			if ( recess == null) {
				var recess = 'recess' + strDate;
				calcTime = calcTime - ( document.getElementsByName(recess)[0].value * 3600000 ) / ( 60 * 60 * 1000 );
			} else {
				calcTime = calcTime - recess;
			}

			// 勤務時間を設定
			var officeHours = 'officeHours' + strDate;
			// hiddenに勤務時間を設定
			var hiOfficeHours = 'hiOfficeHours' + strDate;
			// コードを設定
			var code = 'code' + strDate;
			var start = startT + startH;
			var startStandard = document.getElementById('startStandard').value;
			var end = endT + endH;
			var endStandard = document.getElementById('endStandard').value;

			selectedCode ( code, startStandard, endStandard, start, end );

			if ( calcTime > 0 ) {
				// 勤務時間
				document.getElementsByName(officeHours)[0].value = calcTime;
				document.getElementsByName(hiOfficeHours)[0].value = calcTime;
			} else {
				// 勤務時間
				document.getElementsByName(officeHours)[0].value = 0;
				document.getElementsByName(hiOfficeHours)[0].value = 0;
			}

			// 残業時間を設定
			var overtime = 'overtime' + strDate;
			// hiddenに残業時間を設定
			var hiOvertime = 'hiOvertime' + strDate;

			var calcOvertime = calcTime - document.workInputList.workstandard.value;
			if ( calcOvertime > 0 ) {
				document.getElementsByName(overtime)[0].value = calcOvertime;
				document.getElementsByName(hiOvertime)[0].value = calcOvertime;
			} else {
				document.getElementsByName(overtime)[0].value = 0;
				document.getElementsByName(hiOvertime)[0].value = 0;
			}
		}

		function isNumber( numVal ) {
			var pattern = /^[-]?([1-9]\d*|0)(\.\d+)?$/;
			return pattern.test(numVal)
		}

		function changeTr( obj ) {

			var strDate = obj.name.substr(obj.name.length - 2, 2);
			var strTr1 = 'Tr' + strDate;
			var strTr = document.getElementById(strTr1);
			var startT = 'startT' + strDate;
			var startH = 'startH' + strDate;
			var endT = 'endT' + strDate;
			var endH = 'endH' + strDate;
			var recess = 'recess' + strDate;
			var officeHours = 'officeHours' + strDate;
			var overtime = 'overtime' + strDate;
			var hiOfficeHours = 'hiOfficeHours' + strDate;
			var hiOvertime = 'hiOvertime' + strDate;

			if ( obj.value == "1" ) {
				// 祭を選択した場合
				strTr.style.backgroundColor = "#ff69b4";
				// 開始
				selectedDefault(startT);
				selectedDefault(startH);
				// 終了
				selectedDefault(endT);
				selectedDefault(endH);
				// 休憩
				document.getElementsByName(recess)[0].value = "";
				// 勤務時間
				document.getElementsByName(officeHours)[0].value = "";
				document.getElementsByName(hiOfficeHours)[0].value = "";
				// 残業時間
				document.getElementsByName(overtime)[0].value = "";
				document.getElementsByName(hiOvertime)[0].value = "";
			} else {
				strTr.style.backgroundColor = "#F2FFFF";
				document.getElementsByName(recess)[0].value = 1.0;
			}
		}

		function selectedCode ( code, startStandard, endStandard, start, end ) {
			var option = "";
			var selected = 0;
			var selectCode = "0";

			// 現在の選択状況を取得
			selected = document.getElementsByName(code)[0].selectedIndex;
			// コード取得
			option = document.getElementsByName(code)[0].getElementsByTagName('option');

			if ( parseInt(startStandard) < parseInt(start) ) {
				// 基準の開始時間よりも開始時間が大きい場合
				// コード「遅」を選択
				selectCode = "7";
			} else {
				// 上記以外
				selectCode = "0";
			}

			if ( selectCode !== "7" && selected != 7 ) {
				// 「遅」が選択されていない場合
				if ( parseInt(endStandard) > parseInt(end) ) {
					// 基準の終了時間よりも終了時間が小さい場合
					// コード「早」を選択
					selectCode = "8";
				} else {
					// 上記以外
					selectCode = "0";
				}
			}

			for ( i=0; i < option.length; i++) {
				if ( option[i].value == selectCode ) {
					option[i].selected = true;
					break;
				}
			}
		}

		function selectedDefault( times ) {
			option = document.getElementsByName(times)[0].getElementsByTagName('option');
			for ( i=0; i < option.length; i++) {
				if ( option[i].value == "00" ) {
					option[i].selected = true;
					break;
				}
			}
		}
	</script>
			<div class="section normal update">
			<form  name ="workInputList" action="workInputList.php" method="post" enctype='text/css'>
			<?php
				// 勤務テーブルからログインしている社員の情報を取得
				$sql = sprintf('select
                                       strdate,
                                       staffNo,
                                       workingDay,
                                       startTime,
                                       endTime,
                                       recess,
                                       officeHours,
                                       overtime,
                                       note,
                                       code
                                 from(
                                      select date_format(date_add("%s", interval tmp.generate_series - 1 day), "%s") as strdate
                                      from (SELECT 0 generate_series
                                            FROM DUAL
                                            WHERE (@num:=1-1)*0
                                            UNION ALL
                                            SELECT @num:=@num+1
                                            FROM `information_schema`.COLUMNS
                                            LIMIT %d) as tmp
                                 ) as t1
                                 left outer join (
                                     select
                                            staffNo,
                                            workingDay,
                                            startTime,
                                            endTime,
                                            recess,
                                            officeHours,
                                            overtime,
                                            note,
                                            code
                                     from
                                             attendance
                                     where
                                            staffNo = %d
                                            and deleteFlg = "0"
                                 ) as at
                                 on t1.strdate = at.workingDay',
                               mysqli_real_escape_string($con, $firstDate),
                               mysqli_real_escape_string($con, "%Y%m%d"),
                               mysqli_real_escape_string($con, $dateCount),
                               mysqli_real_escape_string($con, $staffNo));

				// SQL実行
				$result = mysqli_query($con, $sql);

				// 勤務基準テーブルから社員番号に紐づく情報を取得
				$sqlWs = sprintf('select startTime, endTime, recess from workstandard where staffNo = "%s" and deleteFlg = "0"',
				                 mysqli_real_escape_string($con, $staffNo));

				$resultWs = mysqli_query($con, $sqlWs);

				// 勤務基準情報
				$recordSet = mysqli_fetch_assoc($resultWs);

				$startWs = strtotime( $recordSet["startTime"] );
				$endWs = strtotime( $recordSet["endTime"] );
				$reWs = ( $endWs - $startWs ) / (60 * 60);
				$reWs = $reWs - $recordSet["recess"];

				// 勤怠提出情報取得
				$submissionSql = sprintf('select
                                                 submissionStatus
                                          from
                                                 attendancesubmission
                                          where
                                                 staffNo = %d
                                                 and workYears = "%s"',
						                   mysqli_real_escape_string($con, $staffNo),
						                   mysqli_real_escape_string($con, $_POST["selectWorkYear"] . $_POST["selectWorkMonth"]));

				// SQL実行
				$resultSubmission = mysqli_query($con, $submissionSql);
				$recordSetSubmission = mysqli_fetch_assoc($resultSubmission);

				// 一覧画面表示
				$ua=$_SERVER['HTTP_USER_AGENT'];
				if((strpos($ua,'iPhone')!==false)||(strpos($ua,'iPod')!==false)||(strpos($ua,'Android')!==false)) {
					echo '<table border="1" width="850">';
				} else {
					echo '<table border="1" width="650">';
				}

				echo '<tr bgcolor="#C0C0C0">';
				echo '<td rowspan="2">日</td>';
				echo '<td rowspan="2">曜<br/>日</td>';
				echo '<td rowspan="2"><center>コード</center></td>';
				echo '<td colspan="3"><center>就業時間</center></td>';
				echo '<td rowspan="2"><center>勤務<br/>時間</center></td>';
				echo '<td rowspan="2"><center>残業<br/>時間</center></td>';
				echo '<td rowspan="2"><center>作業備考</center></td>';
				echo '</tr><td bgcolor="#C0C0C0"><center>開始</center></td>';
				echo '<td bgcolor="#C0C0C0"><center>終了</center></td>';
				echo '<td bgcolor="#C0C0C0"><center>休憩</center></td>';
				echo '</tr>';

				// 合計算出用
				$sumRecess = 0;
				$sumOfficeHours = 0;
				$sumOverTime = 0;
				$salaried = 0;
				// 所定勤務数
				$predeterminedDays = 0;
				// 欠勤日数
				$absence = 0;
				// 遅刻・早退数
				$lateLleaveEarly = 0;

				// 月初から月末までのループ
				while($data = mysqli_fetch_array($result)) {
					// 背景色
					$bgcolor = "#F2FFFF";
					if ( date("w", strtotime($data["strdate"])) == "0"
						|| $data["code"] == "1" ) {
						// 日曜またはの場合背景色を赤に変更
						$bgcolor = "#ff69b4";
					} else if ( date("w", strtotime($data["strdate"])) == "6" ) {
						// 土曜の場合背景色を青に変更
						$bgcolor = "#87ceeb";
					} else {
						// そのほかの場合は、デフォルト設定
						$bgcolor = "#F2FFFF";
					}
					echo '<tr bgcolor="' . $bgcolor . '" id="Tr' . sprintf("%02d", substr($data["strdate"], 6, 2)) . '">';
					// 日付
					echo '<td>' . substr($data["strdate"], 6, 2) .'</td>';

					// 曜日
					echo '<td><center>'. $weekday[date("w", strtotime($data["strdate"]))] .'</center></td>';

					// コード
					echo '<td>';
					echo '<select name="code'. sprintf("%02d", substr($data["strdate"], 6, 2)) . '" onChange="changeTr(this)">';
					for ($i = 0; $i < 9; $i++) {
						if (!empty($data["code"]) && $data["code"] == $i) {
							// カウントと登録されているコードが一致した場合
							echo '<option value="' . $i .'" selected>' . $arrCode[$data["code"]] . '</option>';
						} else if ( ( $i == 6 ) && ( empty($data["startTime"]) ) && ( ( intval($now) >= intval($data["strdate"]) )
								&& ( (date("w", strtotime($data["strdate"])) != "0")  && ( date("w", strtotime($data["strdate"])) != "6")) )) {
							// 本日日付以前の場合 かつ 開始時間、終了時間を登録していない場合
							// 「欠」を選択する
							echo '<option value="6" selected>' . $arrCode[6] . '</option>';
						} else {
							echo '<option value="' . $i .'">' . $arrCode[$i] . '</option>';
						}
					}
					echo '</select>';
					echo '</td>';

					if ( !empty($data["code"]) ) {
						if ( $data["code"] == "2" ) {
							// 有給日数のカウント
							$salaried = $salaried + 1;
						} else if ( $data["code"] == "6" ) {
							// 欠勤日数のカウント
							$absence = $absence + 1;
						} else if ( ($data["code"] == "7") || ($data["code"] == "8") ) {
							// 遅刻・早退数のカウント
							$lateLleaveEarly = $lateLleaveEarly + 1;
						}
					}

					// 開始時
					echo '<td><select name="startT'. sprintf("%02d", substr($data["strdate"], 6, 2)) .'" onchange="setCalcTime(this, '. "'" . substr($data["strdate"], 6, 2) . "'" . ')">';
					for ($i = 0; $i <= 23; $i++) {
						if (substr($data["startTime"], 0, 2) == sprintf("%02d", $i)) {
							echo '<option value="' . sprintf("%02d", $i) .'" selected>' . sprintf("%02d", $i) . '</option>';
						} else {
							echo '<option value="' . sprintf("%02d", $i) .'">' . sprintf("%02d", $i) . '</option>';
						}
					}
					echo '</select>';
					echo '：';

					// 開始分
					echo '<select name="startH' . sprintf("%02d", substr($data["strdate"], 6, 2)) .'" onchange="setCalcTime(this, '. "'" . substr($data["strdate"], 6, 2) . "'" . ')">';
					if (substr($data["startTime"], 2, 2) == sprintf("%02d", 0)) {
						echo '<option value="' . sprintf("%02d", 0) . '" selected >00</option>';
					} else {
						echo '<option value="' . sprintf("%02d", 0) . '">00</option>';
					}

					if (substr($data["startTime"], 2, 2) == sprintf("%02d", 15)) {
						echo '<option value="15" selected >15</option>';
					} else {
						echo '<option value="15">15</option>';
					}

					if (substr($data["startTime"], 2, 2) == sprintf("%02d", 30)) {
						echo '<option value="30" selected >30</option>';
					} else {
						echo '<option value="30">30</option>';
					}

					if (substr($data["startTime"], 2, 2) == sprintf("%02d", 45)) {
						echo '<option value="45" selected >45</option>';
					} else {
						echo '<option value="45">45</option>';
					}
					echo '</select>';
					echo '</td>';

					// 終了時
					echo '<td>'. '<select name="endT'. sprintf("%02d", substr($data["strdate"], 6, 2)) .'" onchange="setCalcTime(this, '. "'" . substr($data["strdate"], 6, 2) . "'" . ')">';
					for ($i = 0; $i <= 23; $i++) {
						if (substr($data["endTime"], 0, 2) == sprintf("%02d", $i)) {
							echo '<option value="' . sprintf("%02d", $i) .'" selected>' . sprintf("%02d", $i) . '</option>';
						} else {
							echo '<option value="' . sprintf("%02d", $i) .'">' . sprintf("%02d", $i) . '</option>';
						}
					}
					echo '</select>';
					echo '：';

					// 終了分
					echo '<select name="endH' . sprintf("%02d", substr($data["strdate"], 6, 2)) .'" onchange="setCalcTime(this, '. "'" . substr($data["strdate"], 6, 2) . "'" . ')">';
					if (substr($data["endTime"], 2, 2) == sprintf("%02d", 0)) {
						echo '<option value="' . sprintf("%02d", 0) . '" selected >00</option>';
					} else {
						echo '<option value="' . sprintf("%02d", 0) . '">00</option>';
					}

					if (substr($data["endTime"], 2, 2) == sprintf("%02d", 15)) {
						echo '<option value="15" selected >15</option>';
					} else {
						echo '<option value="15">15</option>';
					}

					if (substr($data["endTime"], 2, 2) == sprintf("%02d", 30)) {
						echo '<option value="30" selected >30</option>';
					} else {
						echo '<option value="30">30</option>';
					}

					if (substr($data["endTime"], 2, 2) == sprintf("%02d", 45)) {
						echo '<option value="45" selected >45</option>';
					} else {
						echo '<option value="45">45</option>';
					}
					echo '</select>';
					echo '</td>';

					// 休憩
					if ( !empty($data["recess"]) ) {
						// 休憩が登録されている場合
						echo '<td>' . '<input type="text" name="recess' . sprintf("%02d", substr($data["strdate"], 6, 2)) . '" size="6" maxlength="4" style="ime-mode:disabled; text-align: right;" value="' . $data["recess"] . '" onchange="setCalcTime(this, ' ."'". substr($data["strdate"], 6, 2) . "'" .')"  /></td>';
						$sumRecess = $sumRecess + $data["recess"];
						if ( !(date("w", strtotime($data["strdate"])) == "0")
						  || !(date("w", strtotime($data["strdate"])) == "6")
						  || !($data["code"] == "1") ) {
						  	// 所定勤務日の算出
							$predeterminedDays = $predeterminedDays + 1;
						  }
					} else {
						// 休憩が登録されていない場合
						if ( date("w", strtotime($data["strdate"])) == "0"
						  || date("w", strtotime($data["strdate"])) == "6"
						  || $data["code"] == "1" ) {
							// 土日または祭日の場合
							echo '<td>' . '<input type="text" name="recess' . sprintf("%02d", substr($data["strdate"], 6, 2)) . '" size="6" maxlength="4" style="ime-mode:disabled; text-align: right;" value="" onchange="setCalcTime(this, ' ."'". substr($data["strdate"], 6, 2) . "'" .')"  /></td>';
						} else {
							// 土日以外の場合
							// 基準値の休憩時間を設定
							echo '<td>' . '<input type="text" name="recess' . sprintf("%02d", substr($data["strdate"], 6, 2)) . '" size="6" maxlength="4" style="ime-mode:disabled; text-align: right;" value="' . $recordSet["recess"] . '" onchange="setCalcTime(this, ' ."'". substr($data["strdate"], 6, 2) . "'" .')"  /></td>';
							$sumRecess = $sumRecess + $recordSet["recess"];
						  	// 所定勤務日の算出
							$predeterminedDays = $predeterminedDays + 1;
						}
					}

					// 勤務時間
					echo '<td>' . '<input type="text" name="officeHours' . sprintf("%02d", substr($data["strdate"], 6, 2)) . '" size="3" style="text-align: right;" maxlength="4" disabled="disabled" value="' . $data["officeHours"] . '" /></td>';
					$sumOfficeHours = $sumOfficeHours + $data["officeHours"];

					// 残業時間
					echo '<td>' . '<input type="text" name="overtime' . sprintf("%02d", substr($data["strdate"], 6, 2)) . '" size="3" style="text-align: right;" maxlength="4" disabled="disabled" value="' . $data["overtime"] . '" /></td>';
					$sumOverTime = $sumOverTime + $data["overtime"];

					// 作業備考
					echo '<td>' . '<input type="text" name="note' . sprintf("%02d", substr($data["strdate"], 6, 2)) . '" size="10" maxlength="50" value="' . $data["note"]. '" /></td>';
					echo '</tr>';

					echo '<input type="hidden" name="hiOfficeHours' . substr($data["strdate"], 6, 2) . '" value="' . $data["officeHours"] . ' "/>';
					echo '<input type="hidden" name="hiOvertime' . substr($data["strdate"], 6, 2) . '" value="' . $data["overtime"] . '"/>';
				}

				// 所定勤務日を元に基準時間の算出
				$criterionTime = $reWs * $predeterminedDays;

				// 時間差の算出
				$timeLag = $sumOfficeHours - $criterionTime;

				echo '<tr bgcolor="#F2FFFF">';
				echo '<td colspan="5">合計';
				echo '</td>';
				// 休憩合計
				echo '<td>';
				echo '<input type="text" name="sumRecess" size="6" style="text-align: right;" disabled="disabled" value="'. $sumRecess .'">';
				echo '</td>';
				// 勤務時間合計
				echo '<td>';
				echo '<input type="text" name="sumOfficeHours" size="3" style="text-align: right;" disabled="disabled" value="'. $sumOfficeHours .'">';
				echo '</td>';
				// 残業時間合計
				echo '<td>';
				echo '<input type="text" name="sumOverTime" size="3" style="text-align: right;" disabled="disabled" value="'. $sumOverTime .'">';
				echo '</td>';
				echo '<td></td>';
				echo '</tr>';

				// 登録ボタン
				echo '<tr bgcolor="#F2FFFF">';
				echo '<td colspan="9" align="center">';
				if ( $recordSetSubmission["submissionStatus"] == "2" ) {
					echo '<input type="checkbox" name="subMission" value="2" checked>完了';
				} else {
					echo '<input type="checkbox" name="subMission" value="2">完了';
				}
				echo '<br/>※ 全ての入力が完了しましたら、完了にチェックをつけて登録ボタンを押下してください。';
				echo '<br/>';
				echo '<input type="submit" value="登録" />';
				echo '</td>';
				echo '</tr>';
				echo '</table>';
				echo '<br/><br/>';

				// 摘要
				echo '<table  border="1" width="650">';
				echo '<tr bgcolor="#C0C0C0">';
				echo '<td rowspan="3"><div Align="center">摘<br/>要</div></td>';
				echo '<td colspan="1"><div Align="center">実働時間</div></td>';
				echo '<td colspan="1"><div Align="center">基準時間</div></td>';
				echo '<td colspan="1"><div Align="center">時間差</div></td>';
				echo '<td colspan="1"><div Align="center">所定勤務日</div></td>';
				echo '<td colspan="1"><div Align="center">有給日数</div></td>';
				echo '<td colspan="1"><div Align="center">欠勤日数</div></td>';
				echo '<td colspan="1"><div Align="center">遅刻・早退</div></td>';
				echo '<td rowspan="3"><div Align="center">基<br/>準</div></td>';
				echo '<td colspan="1"><div Align="center">開始</div></td>';
				echo '<td colspan="1"><div Align="right">' . substr($recordSet["startTime"], 0, 2) . ':'. substr($recordSet["startTime"], 2, 2) . '</div></td>';
				echo '</tr>';
				echo '<td rowspan="2" bgcolor="#C0C0C0"><div Align="right">' . $sumOfficeHours . 'H</div></td>';
				echo '<td rowspan="2" bgcolor="#C0C0C0"><div Align="right">' . $criterionTime . 'H</div></td>';
				echo '<td rowspan="2" bgcolor="#C0C0C0"><div Align="right">' . $timeLag . 'H</div></td>';
				echo '<td rowspan="2" bgcolor="#C0C0C0"><div Align="right">' . $predeterminedDays . '日</div></td>';
				echo '<td rowspan="2" bgcolor="#C0C0C0"><div Align="right">' . $salaried . '日</div></td>';
				echo '<td rowspan="2" bgcolor="#C0C0C0"><div Align="right">' . $absence  . '日</div></td>';
				echo '<td rowspan="2" bgcolor="#C0C0C0"><div Align="right">' . $lateLleaveEarly . '回</div></td>';
				echo '<td bgcolor="#C0C0C0">終了</td>';
				echo '<td bgcolor="#C0C0C0"><div Align="right">' . substr($recordSet["endTime"], 0, 2) . ':'. substr($recordSet["endTime"], 2, 2) . '</div></td>';
				echo '<tr>';
				echo '<td bgcolor="#C0C0C0">休憩</td>';
				echo '<td bgcolor="#C0C0C0"><div Align="right">' . $recordSet["recess"] . '</div></td>';
				echo '</tr>';
				echo '</table>';

				// DB接続を閉じる
				closeConnection($con);
			?>
			<input type="hidden" name="selectWorkDate" value="<?php echo $_POST["selectWorkYear"] . $_POST["selectWorkMonth"]; ?>" />
			<input type="hidden" name="selectWorkYear" value="<?php echo $_POST["selectWorkYear"]; ?>" />
			<input type="hidden" name="selectWorkMonth" value="<?php echo $_POST["selectWorkMonth"]; ?>" />
			<input type="hidden" name="workstandard" value="<?php echo $reWs; ?>" />
			<input type="hidden" name="startStandard" id="startStandard" value="<?php echo $recordSet["startTime"]; ?>" />
			<input type="hidden" name="endStandard" id="endStandard" value="<?php echo $recordSet["endTime"]; ?>" />
		</form>
	</div>