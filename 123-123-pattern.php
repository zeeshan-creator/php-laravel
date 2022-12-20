<?php
 $byday=['1', '2', '3'];
 // echo $byday[2];
 $no = 0;
 for ($i = 0; $i < 12; $i++) {
		echo $byday[$no];

		if ($no == count($byday)) {
			$no = 0;
		} else {
			$no += 1;
		}
	}
	
?> 
