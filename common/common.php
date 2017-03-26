<?php
	/**
	 * MySQL接続コネクション取得
	 *
	 * @param  なし
	 * @return mysqli $con
	 */
	function getConnection() {
		// MySQL接続
		//$con = mysqli_connect('sddb0040156987.cgidb', 'sddbNDA2ODIz', 'OZG0y&M4', 'sddb0040156987') or die(mysqli_connect_error());
		$con = mysqli_connect('localhost', 'root', '', 'mac_attendance') or die(mysqli_connect_error());
		mysqli_set_charset($con, 'UTF8');
		date_default_timezone_set('Asia/Tokyo');
		return $con;
	}

	/**
	 * MySQL接続を閉じる
	 *
	 * @param mysqli $con
	 * @return なし
	 */
	function closeConnection( mysqli $con ) {
		// MySQL接続を閉じる
		mysqli_close($con);
	}

	/**
	 * エラーメッセージ表示
	 *
	 * @param String $errorMessage
	 * @return なし
	 */
	function dispErrorMessage( $errorMessage ) {
		echo "<div style='border-style: solid ; border-width: 1px; padding: 10px 5px 10px 20px; border-color: red;'><font color='#ff0000'>" . $errorMessage . "</font></div>";
	}


	/**
	 * 指定した日付から現在日付の月の差分を取得
	 *
	 * @param Date $start
	 * @param Date $end
	 * @return なし
	 */
	function getMonthDiff( $start, $end ) {

		$date1=$end;

		$date2=$start;

		$month1=date("Y",$date1)*12+date("m",$date1);
		$month2=date("Y",$date2)*12+date("m",$date2);

		$diff = $month1 - $month2;
		return $diff;
	}

	function inputCheck( $value ) {
		if ( empty($value) ) {
			return false;
		} else {
			return true;
		}
	}

	/*
	 *
	 * 英数小文字8ケタのパスワードを生成する
	 * @params $length:
	 */
	function create_passwd(){
		$length = 8;
	    //vars
	    $pwd = array();
	    $pwd_strings = array(
	        "sletter" => range('a', 'z'),
	        "cletter" => range('A', 'Z'),
	        "number"  => range('0', '9'),
	    );

	    //logic
	    while (count($pwd) < $length) {
	        // 3種類必ず入れる
	        if (count($pwd) < 3) {
	            $key = key($pwd_strings);
	            next($pwd_strings);
	        } else {
	        // 後はランダムに取得
	            $key = array_rand($pwd_strings);
	        }
	        $pwd[] = $pwd_strings[$key][array_rand($pwd_strings[$key])];
	    }
	    // 生成したパスワードの順番をランダムに並び替え
	    shuffle($pwd);

	    return implode($pwd);
	}

	/**
	 * システム日付取得
	 */
	function getNow() {
		return date('Ymd', strtotime(date('Y-m-d')));
	}

	/**
	 * メールアドレスチェック
	 * @param String $mailAddress メールアドレス
	 * @return boolean 正しい場合：true
	 *                 不正の場合：false
	 */
	function is_mail ( $mailAddress ) {
		return filter_var($mailAddress, FILTER_VALIDATE_EMAIL) && !preg_match('/@\[[^\]]++\]\z/', $mailAddress);
	}
?>