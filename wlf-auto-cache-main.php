<?php
/**
 * Plugin Name: Aelieve Master
 * Description:  Manage your site cache.
 * Version:1.0.0
 * Author:Wolfiz
 */



if (!class_exists('wlf_auto_cache_main_main')) {

	class wlf_auto_cache_main_main { 
		
		public function __construct() {

			$this->wlf_global_constents_vars();
			$this->wlf_gutinberg_status();
			add_action('wp_loaded', array($this,'cache_init'), 1);
			add_action('admin_init', array($this,'init_hook'), 1);
			add_action( 'admin_menu', array($this,'my_cpt_admin_submenu'));
			add_filter( 'post_row_actions', array( $this,'add_row_actions'), 10, 1 );
			add_action( 'wp_ajax_wlf_delete_log', array($this,'wlf_delete_log'), 10 );
			add_action( 'upgrader_process_complete',  array($this,'my_upgrade_function'),10, 2);

			if (is_admin()) {
				include 'admin/MyTestimonial-add.php';
			} 
			else {
				// include 'front/MyTestimonial-front.php';
			}
		} 

		public function my_upgrade_function( $upgrader_object, $options ) {
			$selected_plugins = get_option('wf_plg_selected');

			$plugin = explode('/', $options['plugins'][0])[0];

			if( in_array($plugin, $selected_plugins) ){
				 // -do- cache clear of the domain
				// Clear the cache.
				if ( function_exists( 'rocket_clean_domain' ) ) {
					rocket_clean_domain();
					update_option('wp_upd','Cache cleared');
				}
			}		   

		}

		public function wlf_gutinberg_status(){

			if(get_option('wf_gutinberg_status')){

				add_filter('use_block_editor_for_post', '__return_false', 10);
			}

		}

		public function init_hook(){

			include_once WLF_PLUGIN_DIR . 'admin/wlf_general_setting.php';
		}
		
		public function wlf_global_constents_vars() {

			if ( ! defined( 'WLF_URL' ) ) {
				define( 'WLF_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'WLF_BASENAME' ) ) {
				define( 'WLF_BASENAME', plugin_basename( __FILE__ ) );
			}

			if ( ! defined( 'WLF_PLUGIN_DIR' ) ) {
				define( 'WLF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

		}

		public function wporg_dashboard_widget_render() {
			$latest = new MyTestimonial_ShortCodes();
			$latest->MyTestimonial_LatestPosts();
		}

		public function add_row_actions( $actions ) {
			if ( get_post_type() === 'wlf_auto_cache' ) {
				 // $actions[] = "<span class='bld'> ID : " . get_the_ID() . '</span>';
			}

			return $actions;
		}

		public function my_cpt_admin_submenu() {
			add_submenu_page(
				'edit.php?post_type=wlf_auto_cache',
				'Settings',
				'Settings',
				'manage_options',
				'setting-sub',
				array($this,'main_settings')
			);
			add_submenu_page(
				'edit.php?post_type=wlf_auto_cache',
				'Log',
				'Log',
				'manage_options',
				'log-sub',
				array($this,'main_settings_logs')
			);

		}
		public function define_post_name($post_type)
		{
			global $wp_post_types;
			$retval = "";
			foreach ($wp_post_types as $value) {
				if($value->name == $post_type){
					$retval = $value->label;
				}
			}
			return $retval;
		}
		public function wlf_delete_log()
		{
			$log_data = get_option('log_data_2');
			$created_date = date('d-M-Y');
			$keeplogsfor = get_option('wf_log_days');
			$count=0;
			// echo $keeplogsfor;
			foreach ($log_data as $key=>$value) {
				// echo $value['date_time'].'<br />';
				$old_date = $value['date_time']; 
				$old_date_timestamp = strtotime($old_date);
				$new_date = date('d-M-Y', $old_date_timestamp);  

				// echo $created_date-$new_date;
				if($created_date-$new_date>=$keeplogsfor){
					unset($log_data[$key]);
					$count++;
				}
				
			}
			update_option('log_data_2',$log_data);
			echo $count;
			die();
			
		}
		public function main_settings_logs()
		{
			?>
				<div>
					<div>
					<div class="dashboard_heading">
						<h1 class="dashboard_heading1"><?php esc_html_e('Cache Log'); ?></h1>
						<div class="dashboard_button">
							<button class="button button-primary clear-logs">Clear Logs</button><br />
							<img class="loader-img" src="<?php echo WLF_URL.'assets/img/loader.gif' ?>">
						</div>
					</div> 
					</div>
					<div class="main_value_div">
						<div class="view_all_data_class">
							<table class="af_sm_table">
									<tr class="first_row_table">
										<th>Rule</th>
										<th>Implemented On</th>
										<th>Trigger</th>
										<th>Action</th>
										<th>Status</th>
										<th>Updated On</th>						
									</tr>
									<?php
							    	// 	update_option('log_data_2','');
									$log_data = get_option('log_data_2');
									if($log_data){
										// echo sizeof($log_data);
										$size = sizeof($log_data);
										foreach ($log_data as $val) {
										?>
											<tr>
												<td>
													<p><a href="#"><?php echo $val['ID']; ?></a></p>
												</td>
												<td><p>
													<?php echo  '<b>'.$this->define_post_name(get_post_type($val['On'])).'</b> <br />'.get_the_title($val['On']); ?>
												</p>
												</td>
												<td>
													<p>
														<?php
														echo '<b>'.$this->define_post_name($val['trigger_type']).'</b><br />';
														if(in_array('all',  $val['Trigger']))
														{
															echo "All";
														}
														else{
															foreach ($val['Trigger'] as $key=>$value) {
																echo get_the_title($value);
																if (next($val['Trigger'])==true) 
																	echo ' , ';
													
															}
														}
														?>
													</p>
													<!-- <p>Trigger</p> -->
												</td>
												<td>
													<p>
													<?php
													echo '<b>'.$this->define_post_name($val['action_type']).'</b> <br />';
													if(in_array('all',  $val['Action']))
													{
														echo "All";
													}
													else{
														foreach ($val['Action'] as $value) {
															echo get_the_title($value);
															if (next($val['Action'])==true) 
																echo ' , ';
															
														}
													}
													?>
													</p>
													<!-- <p>Action</p> -->
												</td>
												<td>
													<div class="sync_button" style="padding: 0px!important;display: flex;">
														<input type="button" style="<?php echo $val['cloudflare']?"background-color: green;":"background-color: gray" ?>"  class="button button-primary wf_c_done" value="C">
														<input type="button" style="margin-left: 10px;border:0px;<?php  echo $val['wp_rocket']=='1'?"background-color: green":"background-color: gray" ?>"  class="button button-primary wf_r_done" value="R">
													</div>
												</td>
												<td>
													<p><?php 
													$old_date = $val['date_time']; 
													$old_date_timestamp = strtotime($old_date);
													$new_date = date('d-M-Y \a\t h:i:s a', $old_date_timestamp);   
													echo $new_date; ?></p>
												</td>
											</tr>
											<?php 
										}

									 }else
									 { ?>
											<td>
												<p>No logs found</p>
											</td>
									<?php 
								} ?>

							</table>
						</div>
					</div>
				</div>
			  <?php
		}

		public function main_settings(){

			if ( isset( $_POST['tabs_shipping_verify_action_nonce'] ) && wp_verify_nonce( isset( $_GET['tab'] ) ) ) {
			print esc_html__( 'Sorry, your nonce did not verify.', 'wf-jk-textdomain' );
			exit;
		}
		if (isset($_GET['tab'])) {
			 $active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		} else {
			$active_tab ='wlf_setting_tab';
		}

		?>
	  <div class="af_sl_Options">
			<div class="wrap woocommerce">
				<h2><?php echo esc_html__( 'Settings', 'wf-jk-textdomain' ); ?>
				</h2>
				<?php settings_errors(); ?> 
				<h2 class="nav-tab-wrapper">
					<a href="?post_type=wlf_auto_cache&page=setting-sub&tab=wlf_setting_tab" class="nav-tab <?php echo esc_attr( $active_tab ) === 'wlf_setting_tab' ? 'nav-tab-active' : ''; ?>">
						<?php
						echo esc_html__( 'Cloudflare Api', 'wf-jk-textdomain' );
						?>
					</a>
					<!-- <a href="?post_type=wlf_auto_cache&page=setting-sub&tab=wlf_sync_tab" class="nav-tab <?php echo esc_attr( $active_tab ) === 'wlf_sync_tab' ? 'nav-tab-active' : ''; ?>">
						<?php
						echo esc_html__( 'Sync Data', 'wf-jk-textdomain' );
						?>
					</a> -->
					<a href="?post_type=wlf_auto_cache&page=setting-sub&tab=wlf_general_setting" class="nav-tab <?php echo esc_attr( $active_tab ) === 'wlf_general_setting' ? 'nav-tab-active' : ''; ?>">
						<?php
						echo esc_html__( 'General Setting', 'wf-jk-textdomain' );
						?>
					</a>
					<a href="?post_type=wlf_auto_cache&page=setting-sub&tab=wlf_log_tab" class="nav-tab <?php echo esc_attr( $active_tab ) === 'wlf_log_tab' ? 'nav-tab-active' : ''; ?>">
						<?php
						echo esc_html__( 'Log Setting', 'wf-jk-textdomain' );
						?>
					</a>	
					<a href="?post_type=wlf_auto_cache&page=setting-sub&tab=wlf_plg_tab" class="nav-tab <?php echo esc_attr( $active_tab ) === 'wlf_plg_tab' ? 'nav-tab-active' : ''; ?>">
						<?php
						echo esc_html__( 'Plugin Update Cache', 'wf-jk-textdomain' );
						?>
					</a>	
					<a href="?post_type=wlf_auto_cache&page=setting-sub&tab=wlf_man_tab" class="nav-tab <?php echo esc_attr( $active_tab ) === 'wlf_man_tab' ? 'nav-tab-active' : ''; ?>">
						<?php
						echo esc_html__( 'Manual Cache Clean', 'wf-jk-textdomain' );
						?>
					</a>	    			    				
				</h2>
			</div>			
			<form method="post" action="options.php" class="afacr_options_form"> 
			<?php
			if ( 'wlf_setting_tab' === $active_tab ) {
				settings_fields( 'wlf_setting_tab-page' );
				if(empty( get_option('wf_conf_token_unique'))){
				do_settings_sections( 'wlf_register_general_settings' );
				?>
				<input type="button" name="savesettings" class="button-primary wlf_cloud_set" value="Connect">
				<?php
				}
				else{
					?>
					<div class="main_data_div">
						<div class="flex">

							<div class="sync_button">
								<?php 
							echo '<h3>'.get_option('selected_web').'</h3>';
							?>
							<p>
								<input type="button" name="wf_jk_sync_data" class="button button-primary wf_sync_data" value="Disconnect"><p id="success">Status : Connected with cloudflare</p>
							</p>
							</div>

						</div>
							<div id="wf_refresh_ajax">
						</div>

					</div>
					<?php
				}
				// submit_button();
			}
			if ( 'wlf_general_setting' === $active_tab ) {
				settings_fields( 'wlf_general_setting-page' );
				do_settings_sections( 'wlf_register_post_settings' );
				submit_button();
			}
			if ('wlf_sync_tab'===$active_tab) {
				include_once WLF_PLUGIN_DIR . '/admin/wlf_table_temp_cloudflare.php';
			}	
			if ('wlf_log_tab'===$active_tab) {
				settings_fields( 'wlf_log_setting-page' );
				do_settings_sections( 'wlf_register_log_settings' );
				submit_button();
			}
			if ('wlf_plg_tab'===$active_tab) {
				settings_fields( 'wlf_plg_setting-page' );
				do_settings_sections( 'wlf_register_plg_settings' );
				submit_button();
			}
			if ('wlf_man_tab'===$active_tab) {
				settings_fields( 'wlf_man_setting-page' );
				// do_settings_sections( 'wlf_register_man_settings' );
				// submit_button();
				?>
				<p style="margin-left:10px"><?php echo get_option("cache_status"); ?></p>
				<input type="button" name="savesettings" style="margin:10px" class="button-primary wlf_manual_cache" value="Clear Cache">
				<?php
			}
				  
				
			} 
		public function cache_init() {
			$labels = array(
			'name'                  => 'Aelieve Master',
			'singular_name'         => 'Aelieve Master',
			'menu_name'             => 'Aelieve Master',
			'name_admin_bar'        => 'Aelieve Master',
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New',
			'new_item'              => 'New Cache Rule',
			'edit_item'             => 'Edit Cache Rule',
			'view_item'             => 'View Cache Rule',
			'all_items'             => 'Cache Rules',
			'search items'          => 'Search Cache Rule',
			'parent_item_colon'     => 'Parent Cache Rule',
			'not_found'             => 'No Cache Rule Found',
			'not_found_in_trash'    => 'No Cache Rule Found In Trash',
			);
			$args   = array(
			'labels'                => $labels,
			'public'                => true,
			'publicaly_queryable'   => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'query_var'             => true,
			'capability_type'       => 'post',
			'has_archive'           => true,
			'Hierarchical'          => false,
			'menu_position'         => null,
			'menu_icon'             => '"' .WLF_URL.'/assets/img/logo (1).svg'. '"',
			'supports'              => array(''),
			);
			register_post_type('wlf_auto_cache', $args);

			$labels = array(
			'name'                  => 'Hidden',
			'singular_name'         => 'Hidden',
			'menu_name'             => 'Hidden',
			'name_admin_bar'        => 'Hidden',
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New',
			'new_item'              => 'New Cache Rule',
			'edit_item'             => 'Edit Cache Rule',
			'view_item'             => 'View Cache Rule',
			'all_items'             => 'Cache Rules',
			'search items'          => 'Search Cache Rule',
			'parent_item_colon'     => 'Parent Cache Rule',
			'not_found'             => 'No Cache Rule Found',
			'not_found_in_trash'    => 'No Cache Rule Found In Trash',
			);
			$args   = array(
			'labels'                => $labels,
			'public'                => true,
			'publicaly_queryable'   => true,
			'show_ui'               => false,
			'show_in_menu'          => true,
			'query_var'             => true,
			'capability_type'       => 'post',
			'has_archive'           => true,
			'Hierarchical'          => false,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-format-quote',
			'supports'              => array(''),
			);
			register_post_type('hd_wlf_auto_cache', $args);
		}
	}
	$wlf_auto_cache_main_main = new wlf_auto_cache_main_main();
}