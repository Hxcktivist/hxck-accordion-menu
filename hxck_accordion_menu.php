<?php
/*
		Plugin Name: Hxck accordion menu
		Plugin URI: https://github.com/Hxcktivist/hxck-accordion-menu
		Tags: jquery, dropdown, menu, vertical accordion, animated, css, navigation, accordion
		Description: Создание вертикального аккардеон из любого пользовательского меню Wordpress с помощью jQuery. Добавление меню с помощью виджетов. Функции включают - Сохраненное состояния с помощью файлов cookie и опцию выбора событий «Нажатия» или «Наведения» для запуска меню и самое главное, сохраняет позицию меню на выбранной странице.
		Author: Hxcktivist
		Version: 3.1.3
		Author URI: https://github.com/Hxcktivist
*/

global $registered_skins;

class dc_jqaccordion {

	function dc_jqaccordion(){
		global $registered_skins;
	
		if(!is_admin()){
		
			// Header styles
			add_action( 'init', array('dc_jqaccordion', 'header') );
		
			// Shortcodes
			add_shortcode( 'dcwp-jquery-accordion', 'dcwp_dc_jqaccordion_shortcode' );
		}
		add_action( 'wp_footer', array('dc_jqaccordion', 'footer') );
		
		$registered_skins = array();
	}

	function header(){
		
		// Scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jqueryhoverintent', dc_jqaccordion::get_plugin_directory() . '/js/jquery.hoverIntent.minified.js', array('jquery') );
		wp_enqueue_script( 'jquerycookie', dc_jqaccordion::get_plugin_directory() . '/js/jquery.cookie.js', array('jquery') );
		wp_enqueue_script( 'dcjqaccordion', dc_jqaccordion::get_plugin_directory() . '/js/jquery.dcjqaccordion.2.9.js', array('jquery') );
	}
	
	function footer(){
		//echo "\n\t";
	}
	
	function options(){}

	function get_plugin_directory(){
		return WP_PLUGIN_URL . '/hxck-accordion-menu';	
	}

};

// Include the widget
include_once('hxck_accordion_menu_widget.php');

// Initialize the plugin.
$dcjqaccordion = new dc_jqaccordion();

// Register the widget
add_action('widgets_init', create_function('', 'return register_widget("dc_jqaccordion_widget");'));

/**
* Create a menu shortcode
*/
function dcwp_dc_jqaccordion_shortcode($atts){
	
	extract(shortcode_atts( array(
		'menu' => '',
		'event' => 'click',
		'auto_close' => 'false',
		'save' => 'false',
		'expand' => 'false',
		'disable' => 'false',
		'close' => 'false',
		'count' => 'false',
		'menu_class' => 'menu',
		'disable_class' => '',
		'expand_class' => 'current-menu-item',
		'hover' => '600',
		'animation' => 'slow',
		'skin' => 'No Theme',
		'id' => ''
	), $atts));
	
	$_SESSION['dc_jqaccordion_menu'] = $_SESSION['dc_jqaccordion_menu'] != '' ? $_SESSION['dc_jqaccordion_menu'] + 1 : 1 ;
	$id = $id == '' ? 's'.$_SESSION['dc_jqaccordion_menu'] : 's'.$id ;
	$menuId = 'dc_jqaccordion_widget-'.$id.'-item';
	$out = '';
	
	if($skin != 'No Theme'){
		$out .= "\n\t<link rel=\"stylesheet\" href=\"".dc_jqaccordion::get_plugin_directory()."/skin.php?widget_id=".$id."&amp;skin=".strtolower($skin)."\" type=\"text/css\" media=\"screen\"  />";
	}
	
	$out .= '<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery("#'.$menuId.'").dcAccordion({
						eventType: "'.$event.'",
						hoverDelay: '.$hover.',
						menuClose: '.$close.',
						autoClose: '.$auto_close.',
						saveState: '.$save.',
						autoExpand: '.$expand.',
						classExpand: "'.$expand_class.'",
						classDisable: "'.$disable_class.'",
						showCount: '.$count.',
						disableLink: '.$disable.',
						cookie: "'.$menuId.'",
						speed: "'.$animation.'"
					});
				});
			</script>';
	$out .= '<div class="dcjq-accordion" id="'.$menuId.'">';
	$out .= wp_nav_menu( 
					array( 
						'fallback_cb' => '', 
						'menu' => $menu,
						'menu_class' => $menu_class,
						'echo' => false
						) 
					);
	$out .= '</div>';
	return $out;
}
?>