<?php
/* 
Plugin Name: Outerbridge Password Generator 
Plugin URI: http://outerbridge.co.uk/ 
Description: Password Generator is a plugin written by Outerbridge which adds a widget to Wordpress which generates various length random passwords (with or without special characters).
Author: Outerbridge
Version: 1.3
Author URI: http://outerbridge.co.uk/
Tags: password generator, special characters, strong password
License: GPL v2
*/

/**
 *
 * v1.3 140829 Tested and stable up to WP4.0
 *
 * v1.2 140430 Tested and stable up to WP3.9
 * v1.1 131212 Tested and stable up to WP3.8 and updated author name
 * v1.0 120103 stable up to WP3.3
 * v0.1 110827 initial release
 *
 */

class obr_password_generator extends WP_Widget{
	// version
	public $obr_password_generator = '1.3';
	
	// constructor
	function obr_password_generator(){
		$widget_ops = array('classname' => 'oouterbridge_pass_gen_widget', 'description' => __("Create strong passwords quickly and easily using this widget.  Various password lengths available as well as the option to use symbols as well as alphanumerics."));
		$control_ops = array('width' => 300, 'height' => 300);
		$this->WP_Widget('obr_pass_gen', __('Outerbridge Password Generator'), $widget_ops, $control_ops);
		add_action('wp_head', array(&$this, 'obr_header'));
	}
	
	// functions
	function widget($args, $instance){
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ($title){
			echo $before_title,$title,$after_title;
		} else {
			echo $before_title,'Password Generator',$after_title;
		}
		if (isset($_POST['pg_length'])){
			$formposted = true;
			$pg_length = strip_tags(stripslashes($_POST['pg_length']));
			if ($pg_length > 18 || $pg_length < 6){
				$pg_length = 12;
			}
			$chk_symbols = strip_tags(stripslashes($_POST['chk_symbols']));
		} else {
			$formposted = false;
			$pg_length = 12;
			$chk_symbols = true;
		}
		echo '<form action="';
		$path = $_SERVER['REQUEST_URI'];
		if (strlen($path)){
			echo $path;
		} else {
			echo './';
		}
		echo '" method="POST">';
		echo '<ul><li>Password length?';
		echo '<select name="pg_length" title="Password length?"><optgroup label="Recommended">';
		for ($i = 12; $i <= 18; $i++){
			echo '<option';
			if ($i == $pg_length){
				echo ' selected="selected"';
			}
			echo ' value="',$i,'">',$i,'</option>';
		}
		echo '</optgroup>';
		echo '<optgroup label="Other">';
		for ($i = 6; $i <= 11; $i++){
			echo '<option';
			if ($i == $pg_length){
				echo ' selected="selected"';
			}
			echo ' value="',$i,'">',$i,'</option>';
		}	
		echo '</optgroup></select></li>';
		echo '<li><label for="chk_symbols">Include symbols?</label><input name="chk_symbols"';
		if ($chk_symbols){
			echo ' checked="checked"';
		}
		echo 'type="checkbox" title="Include symbols?" /></li></ul>';
		echo '<input type="submit" value="Generate password" name="submit" title="Generate Password" /><br />';
		if ($formposted){
			echo '<p>New Password: <strong>',$this->obr_generate_password($pg_length, $chk_symbols),'</strong></p>';
		}
		echo '</form>';
		echo $after_widget;
	}

	function obr_generate_password($length, $symbols){
		$random_chars = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($symbols){
			$random_chars .= "+-=_*!@#$%+-=_*!@#$%+-=_*!@#$%+-=_*!@#$%";
		}
		$getstring = "";
		$password = "";
		while(strlen($password) < $length){
			$addstring = substr($random_chars, mt_rand(0, strlen($random_chars) - 1), 1);
			// Avoid duplicates
			if (strlen($getstring) > 0){
				if (!strstr($password, $getstring)){
					//append to the password
					$password .= $addstring;
				}
			} else {
				$password .= $addstring;
			}
		}
		return $password;
	}

	function obr_header(){
		echo "\n".'<!-- Using Outerbridge Password Generator.  Find out more at http://outerbridge.co.uk/tools/ -->'."\n";
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}
	
	function form($instance){
		$instance = wp_parse_args((array) $instance, array('title'=>'Password Generator'));
		$title = htmlspecialchars($instance['title']);
		echo '<p style="text-align:right;"><label for="'.$this->get_field_name('title').'">'.__('Title:').' <input style="width: 250px;" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" /></label></p>';
	}

	function obr_install(){
		// add in a version number
		add_option('obr_password_generator', $obr_password_generator);
		// check for updates
		$installed_ver = get_option("obr_password_generator");
		if($installed_ver != $obr_password_generator){
			echo 'Outerbridge Password Generator updated to version ',$obr_password_generator;
			// update specifics go here
			require_once(ABSPATH.'wp-admin/includes/upgrade.php');
			update_option("obr_password_generator", $obr_password_generator);
		}
	}
}

function obr_password_generator_init(){
	register_widget('obr_password_generator');
}
add_action('widgets_init', 'obr_password_generator_init');

?>
