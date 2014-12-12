<?php
	require('filter_abstract.php');
	
	class FilterObj extends Filter {
		
		public function set_color($value) {
			$gs = (int) ($value * 0xFF);
			return array('r' => $gs, 'g' => $gs, 'b' => $gs);
		}

	}
?>