<?php
	if(!isset($_GET['size']))
		fuck_up(400, 'size parameter is missing.');
	if(!isset($_GET['filter']))
		fuck_up(400, 'filter parameter is missing.');
	
	$img_size = (int) $_GET['size'];
	$filter = $_GET['filter'];
	
	if($filter == 'filter' || !file_exists("filter/$filter.php"))
		fuck_up(400, 'Invalid filter.');
	
	if(($img_size & ($img_size - 1)) != 0)
		fuck_up(400, 'Size has to be a power of two.');

	$max = 512;
	if($img_size > $max)
		fuck_up(400, "Image size is too big, maximum is $max");
	
	$max_samples = log($img_size, 2);
	$def_samples = $max_samples - 2;
	$min_samples = $max_samples - 4;
	
	if(isset($_GET['samples']))
		$samples = $_GET['samples'];
	else $samples = $def_samples;
	
	if($samples > $max_samples)
		fuck_up(400, "Too many samples for size $img_size, the max is $max_samples");
	else if($samples < $min_samples)
		fuck_up(400, "Too few samples for size $img_size, the min is $min_samples");
	
	if(isset($_GET['seed']))	
		mt_srand(crc32($_GET['seed']));
	
	$table = array2d($img_size, $img_size);	
	build_table();
	
	$total = 1.0;
	for($i = 1; $i < $samples - 1; $i++)
		$total += 1.0 / ($i + 1);
	
	include_once("filter/$filter.php");
	$filter = new FilterObj();
	
	$img = imagecreatetruecolor($img_size, $img_size);
	$allocations = array();
	
	build_image();
	
	header('Content-Type: image/png');
	ob_start();
	imagepng($img);
	$output = ob_get_clean();
	echo base64_encode($output);
	
	// ============ FUNCTIONS AND ALL THAT STUFF ============
	
	// Simplex Noise
	
	function build_table() {
		global $samples;
		
		for($i = 0; $i < $samples; $i++)
			build_sample($i);
	}

	function build_sample($sample) {
		global $table, $img_size;
		
		$table_size = (int) pow(2, $sample + 2);
		$ampl = 1.0 / ($sample + 1);

		$temp_table_size = (int) $table_size + 2;
		$grid_size = $img_size / $table_size;
		$temp_table = array2d($temp_table_size, $temp_table_size);

		for($i = 0; $i < $temp_table_size; $i++)
			for($j = 0; $j < $temp_table_size; $j++) {
				$val = mt_rand_float();
				$temp_table[$i][$j] = $val;
			}

		for($i = 0; $i < $table_size; $i++)
			for($j = 0; $j < $table_size; $j++) {
				$bx = $i * $grid_size;
				$by = $j * $grid_size;

				for($i1 = 0; $i1 < $grid_size; $i1++)
					for($j1 = 0; $j1 < $grid_size; $j1++)
						$table[$bx + $i1][$by + $j1] += interpolate_noise($temp_table, $i + 1, $j + 1, $i1, $j1, $grid_size) * $ampl;
			}
			
		gc_collect_cycles();
	}

	function interpolate_noise($table, $x, $y, $x1, $y1, $gs) {
		$fx = (float) $x1 / (float) $gs;
		$fy = (float) $y1 / (float) $gs;

		$x_interp1 = lerp($table[$x][$y], $table[$x + 1][$y], $fx);
		$x_interp2 = lerp($table[$x][$y + 1], $table[$x + 1][$y + 1], $fx);
		$y_interp = lerp($x_interp1, $x_interp2, $fy);

		return $y_interp;
	}
	
	// Image Handling
	function set_color($x, $y, $r, $g, $b) {
		global $img, $allocations;
	
		$packed = pack_color($r, $g, $b);
		if(!array_key_exists($packed, $allocations))
			$allocations[$packed] = imagecolorallocate($img, $r, $g, $b);
		
		imagesetpixel($img, $x, $y, $allocations[$packed]);
	}
	
	function build_image() {
		global $table, $img_size, $total, $filter;
		
		for($i = 0; $i < $img_size; $i++)
			for($j = 0; $j < $img_size; $j++) {
				$value = $table[$i][$j] / $total;
				$color = $filter->set_color($value);
				set_color($i, $j, $color['r'], $color['g'], $color['b']);
			}	
	}
	
	// Helpers
	
	function fuck_up($code, $error) {
		http_response_code($code);
		die("<h1>$code</h1>Something broke D:<br><b>Error Message: <i>$error</i></b>");
	}
	
	function array2d($x, $y) {
		$inarray = array_fill(0, $x, 0);
		$array = array_fill(0, $x, $inarray);
		
		return $array;
	}
	
	function mt_rand_float() {
		return (float) mt_rand()/(float) mt_getrandmax();
	}
	
	function lerp($a, $b, $x) {
		return $a + $x * ($b - $a);
	}

	function pack_color($r, $g, $b) {
		return ($r << 16) | ($g << 8) | $b;
	}
	

	
?>