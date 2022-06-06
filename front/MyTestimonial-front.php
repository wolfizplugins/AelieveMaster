<?php

if (!class_exists('WLF_Front')) {
	class WLF_Front {

			public function __construct() {
				
				add_action( 'wp_enqueue_scripts', array($this,'MyTestimonial_enqueue_scripts' ));
				
			}

			public function MyTestimonial_enqueue_scripts() {
				
				wp_enqueue_script( 'jQuery');
				// wp_enqueue_style( 'my-theme', plugins_url('MyTestimonial/assets/css/frontStyle.css'), false );
				wp_enqueue_script( 'my-js', WLF_URL.'/assets/js/frontScript.js', array('jquery'), false );
				
			}
		
		}  
	$WLF_Front = new WLF_Front();
}
 
