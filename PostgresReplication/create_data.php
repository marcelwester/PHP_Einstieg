<?php
	function simpleRandString ($len = 8, $list = '23456789ABDEFGHJKMNQRTYabdefghijkmnqrty') {
		$str = '';
		if (is_numeric ($len) && !empty ($list)) {
			mt_srand ((double) microtime () * 1000000);
			while (strlen ($str) < $len) {
				$str .= $list[mt_rand (0, strlen ($list)-1)];
			}
		}
		return $str;
	}


	
		
	echo "start transaction;\n";
	for ($id=100; $id<10100;$id++) {
		echo "insert into public.company (id,name,age,address,salary) values (".$id.",'".simpleRandString(43)."',".simpleRandString(2,"0123456789").",'".simpleRandString(12)."',".simpleRandString(4,"0123456789").");\n";
	}
	echo "end transaction;\n";
?>
	