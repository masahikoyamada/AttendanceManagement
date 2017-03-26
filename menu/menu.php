<!-- サイドバー開始 -->
<div id="nav">
	<div class="section emphasis">
		<div class="heading">
			<h2>メニュー</h2>
		</div>
		<p>
			<?php
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
			?>
		</p>
	</div>
</div>
<!-- サイドバー終了 -->