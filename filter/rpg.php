<?php
	require('filter_abstract.php');
	
	$colors = array();
	fill(0, 90, array('r' => 0, 'g' => 29, 'b' => 172));
	fill(90, 110, array('r' => 6, 'g' => 42, 'b' => 255));
	fill(110, 135, array('r' => 50, 'g' => 84, 'b' => 255));
	fill(135, 142, array('r' => 239, 'g' => 211, 'b' => 0));
	fill(142, 164, array('r' => 18, 'g' => 189, 'b' => 0));
	fill(164, 176, array('r' => 10, 'g' => 110, 'b' => 0));
	fill(176, 192, array('r' => 102, 'g' => 60, 'b' => 0));
	fill(192, 255, array('r' => 238, 'g' => 238, 'b' => 238));
	
	function blend($c0, $c1) {
		$c0a = alpha($c0);
		$c1a = alpha($c1);
		
		$total_alpha = $c0a + $c1a;
		$weight0 = $c0a / $total_alpha;
		$weight1 = $c1a / $total_alpha;

		$r = $weight0 * $c0['r'] + $weight1 * $c1['r'];
		$g = $weight0 * $c0['g'] + $weight1 * $c1['g'];
		$b = $weight0 * $c0['b'] + $weight1 * $c1['b'];

		return array('r' => $r, 'g' => $g, 'b' => $b);
	}
	
	function alpha($array) {
		return array_key_exists('a', $array) ? $array['a'] : 255;
	}
	
	function fill($from, $to, $color) {
		global $colors;
		
		for($i = $from; $i < $to; $i++)
			$colors[$i] = $color;
	}
	
	class FilterObj extends Filter {
	
		public function set_color($value) {
			global $colors;
			
			$gs = (int) ($value * 0xFF);
			return blend(array('r' => $gs, 'g' => $gs, 'b' => $gs, 'a' => 194), $colors[$gs]);
		}

	}
	
?>