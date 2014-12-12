<?php

	abstract class Filter {
		
		/*
		 Should return an array so that
		 $array['r'] is the red value
		 $array['g'] is the green value
		 $array['b'] is the red value
		 All of these should be between 0x00 and 0xFF.
		 $value is between 0.0 and 1.0 (exclusive)
		*/
		public abstract function set_color($value);
		
	}
?>