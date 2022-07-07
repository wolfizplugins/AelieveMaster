<?php
if (!class_exists('MyTestimonial_Add')) {

	class MyTestimonial_Add {
	
		public function __construct() {
			// echo '<pre>';
			// print_r(get_option('cron'));
			// echo '</pre>';

			add_action( 'add_meta_boxes', array($this,'add_meta_box'));
			add_action( 'save_post', array($this,'UserDetails'), 10, 1);
			add_action( 'manage_wlf_auto_cache_posts_custom_column', array($this,'wpdocs_posts_custom_columns'), 5, 2 );
			add_filter( 'manage_wlf_auto_cache_posts_columns', array($this,'wpdocs_posts_thumb_columns'), 5 );
			add_action( 'admin_enqueue_scripts', array($this,'wlf_admin_enque_scripts'), 10 );
			add_action( 'wp_ajax_wlf_repeater_fields', array($this,'wlf_repeater_fields'), 10 );
			add_action( 'wp_ajax_wlf_repeater_fields_delete', array($this,'wlf_repeater_fields_delete'), 10 );
			add_action( 'wp_ajax_wlf_select_data', array($this,'wlf_select_data'), 10 );
			// add_action( 'wp_ajax_wlf_select_data_initial', array($this,'wlf_select_data_initial'), 10 );
			add_action( 'wp_ajax_wlf_cloudflare_connection', array($this,'wlf_cloudflare_connection'), 10 );
			add_action( 'wp_ajax_save_tkn_web_sel', array($this,'save_tkn_web_sel'), 10 );
			add_action( 'wp_ajax_wlf_cloud_disc', array($this,'wlf_cloud_disc'), 10 );
			add_action( 'wp_ajax_wlf_manual_cache', array($this,'wlf_manual_cache'), 10 );
			add_action( 'elementor/editor/after_save', array($this,'SaveLogsForElementor'),10 ,2 );
			add_action( 'wp_ajax_wlf_data_for_action', array($this,'wlf_data_for_action'), 10 );
		}
	
		public function SaveLogsForElementor($post_id, $editor_data )
		{
			$this->wlf_auto_cache_manage($post_id);
		}

		public function wlf_data_for_action() {

			if(isset($_POST['selectedtrigger2'])){
				$pro = $_POST['selectedtrigger2'];
			}
			$data = get_post_meta($pro,'_elementor_conditions');
			$type = get_post_meta($pro,'_elementor_template_type',true);
			$typearray = array("header","footer","popup");
			$status;
			if(in_array($type, $typearray)){
				// $valsend = array();
				// foreach ($data as $value) {
				// 	foreach($value as $val){
				// 		$val1 = explode('/',$val)[3];
				// 		if(explode('/',$val)[2]=="page"){
				// 			$valsend[] = $val1;
				// 		}
				// 	}
				// }
				// $response       = array(
				// 	'sel_data' => $valsend,
				// 	);
				// echo wp_json_encode( $response );
				$status = 1;
			}
			else {
				$status = 0;
			}
			echo $status;
			die();
		}

		public function renderFields($pid)
		{

			if(get_post_meta($pid,'wlf_fields',true)){

					$fields=get_post_meta($pid,'wlf_fields',true);
					$first = true;

					foreach (array_reverse($fields) as $value) { 

						$unm_sel_val =array();
						$act_unm_sel_val =array();

						$sec_field_vals=array();
						$act_sec_field_vals=array();

						$unm_sel_val=get_post_meta($pid,'unm'.$value,true);
						$act_unm_sel_val=get_post_meta($pid,'act_unm'.$value,true);
						// echo $unm_sel_val;
						$sec_field_vals=(array)get_post_meta($pid,'wlf_fields_loop'.$value,true);
						$act_sec_field_vals=(array)get_post_meta($pid,'act_wlf_fields_loop'.$value,true);

						// print_r($sec_field_vals);

						?>
						<?php if (!$first)
    					{ ?>
						<span id="wlf_headers" style="float:right" ><input type="button" id="sps" title="delete this rule" class="button button-primary wf_jk_sync_data_jira wlf_del"value='Remove Trigger/Action' data-postId="<?php echo esc_attr($pid); ?>" data-delId="<?php echo esc_attr($value); ?>" /></span>
						<?php  } $first = false; ?>
						<div class="wlf_cache_divs">
						<div class="wsl-row" style="display: flex;">
							<div class="wsl-col-6-80">
								<h1>Trigger</h1>
							</div>
						</div>
						<div class="wsl-row" style="display: flex;">
							<div class="wsl-col-6-80">
								<h4>Select Post Type</h4>
								<?php 
								global $wp_post_types;
								?>
								<select data-id="<?php echo esc_attr(get_the_id()); ?>" data-val="<?php echo $value; ?>"  name="unm<?php echo esc_attr($value);?>" class="select_post_type type_select">
									<option selected>Select Post Type</option>
										<?php
										$meta_id = get_option( 'wf_post_unique' );
										if(!empty($meta_id)){
											foreach($meta_id as $valuePostType) {										
										?>
											<option value="<?php echo $valuePostType; ?>" 
												<?php echo $unm_sel_val==$valuePostType?"selected":""; ?> >
												<?php echo $this->define_post_name($valuePostType); ?>
													
												</option>
										<?php }
										}										 
										?>
									</select>
							</div>
							<div class="wsl-col-6-80">
								<h4>Select Specific</h4>
								<?php
								global $wp_post_types;
								$args       = array(
											'post_type' => $unm_sel_val,
											'numberposts' => -1
										);
								$prs       = get_posts( $args );
								?>
								<div style="display: flex;">
								<select data-val="<?php echo esc_attr($value);?>" id="wlf_multi_select<?php echo esc_attr($value); ?>" multiple name="wlf_fields_loop<?php echo esc_attr($value);?>[]" class="wlf_multi_select">
									<option <?php echo in_array('all', $sec_field_vals)?"selected":''; ?>  value="<?php echo 'all'; ?>">All</option>
									<?php
										foreach ($prs as $valuePostType) {
										?>
											 <option  value="<?php echo esc_attr($valuePostType->ID) ?>" 
											<?php if( in_array($valuePostType->ID, $sec_field_vals)){
												echo "selected";
											}?> >
											<?php echo esc_attr($valuePostType->post_title); ?>
												
											</option>
									<?php }  ?>		

									</select>									
									</div>
							</div>

						</div>
						<div class="wsl-row" style="display: flex;">
							<div class="wsl-col-6-80">
								<h1>Action</h1>
							</div>
						</div>
						<div class="wsl-row" id="no-display-<?php echo $value; ?>" style="display: flex;">
							<div class="wsl-col-6-80">
								<h4>Select Post Type</h4>
								<?php 
								global $wp_post_types;
								?>
								<select id="wlf_act_<?php echo esc_attr($value); ?>" data-id="<?php echo esc_attr(get_the_id()); ?>" data-val="<?php echo esc_attr($value); ?>"  name="act_unm<?php echo esc_attr($value);?>" class="select_post_type act_type_select">
									<option selected>Select Post Type</option>
										<?php
										$meta_id = get_option( 'wf_post_unique' );
										if(!empty($meta_id)){
											foreach($meta_id as $valuePostType) {										
										?>
											<option value="<?php echo $valuePostType; ?>" 
												<?php echo $act_unm_sel_val==$valuePostType?"selected":""; ?> >
												<?php echo $this->define_post_name($valuePostType); ?>
													
												</option>
										<?php }
										}										 
										?>
									</select>
									<!-- <p><?php echo $value; ?></p>   -->
							</div>
							<div class="wsl-col-6-80">
								<h4>Select Specific</h4>
								<?php
								global $wp_post_types;
								$args       = array(
											'post_type' => $act_unm_sel_val,
											'numberposts' => -1
										);
								$prs       = get_posts( $args );

								?>
								<div style="display: flex;">
								<select id="act_wlf_multi_select<?php echo $value; ?>" data-ids="<?php echo $value; ?>" multiple name="act_wlf_fields_loop<?php echo $value;?>[]" class="wlf_multi_select act_wlf">
									<option <?php echo in_array('all', $act_sec_field_vals)?"selected":''; ?>  value="<?php echo 'all'; ?>">All</option>
									<?php
									
										foreach ($prs as $valuePostType) {
										?>
											 <option  value="<?php echo esc_attr($valuePostType->ID) ?>" 
											<?php if( in_array($valuePostType->ID, $act_sec_field_vals)){
												echo "selected";
											}?> >
											<?php echo $valuePostType->post_title; ?>
												
											</option>
									<?php }  ?>		

									</select>
									<!-- <p><?php print_r($sec_field_vals); ?></p>   -->

									<?php if (!$first)
    								{ ?>
									<!-- <span id="wlf_headers"><input type="button" id="sps" title="delete this rule" class="button button-primary wf_jk_sync_data_jira wlf_del"value='-' data-postId="<?php echo $pid; ?>" data-delId="<?php echo $value; ?>" /></span> -->
								<?php  } $first = false; ?>
									</div>
							</div>
						</div>
					</div>
						<hr class="hr_style" />
	
						<?php
					}
				}else{

				}
		}

		public function wlf_cloudflare($page,$p){

			if(in_array('all', $page)){

				$page = array();
				//here
				$args = array(
				'post_type' => $p,
				'numberposts' => -1,
				'fields'=>'ids',			
				);
				$ids  = get_posts( $args );
				foreach ($ids as $value) {
					$page[] = $value;
				}
			}
			$status;
			$curl = curl_init();
			$web = get_option('selected_web');

			$zon = get_option('selected_zon');
			$tkn = get_option('wf_conf_token_unique');
			$zzn = get_option('wf_conf_zone_unique');

			$pages_to_clean_preload = array();
			$domain = get_site_url();
			foreach ($page as $eachpage) {

				$pages_to_clean_preload[] = $domain.'/'.basename(get_permalink($eachpage));
				// $pages_to_clean_preload[] = get_permalink($eachpage);

			}

			$urls = implode(',', $pages_to_clean_preload);
			$data = '{
			    "files": [
			        '.json_encode( $urls ).',
			        {
			            "url": '.json_encode( $urls ).',
			            "headers": {
			                "Origin": "https://www.cloudflare.com",
			                "CF-IPCountry": "US",
			                "CF-Device-Type": "desktop"
			            }
			        }
			    ]
			}';
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/'.$zon.'/purge_cache/',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $data,
			  CURLOPT_HTTPHEADER => array(
			    'X-Auth-Email: '.$tkn,
			    'X-Auth-Key: '.$zzn,
			    'Content-Type: application/json'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			$respjson = json_decode($response);

			if($respjson->success){
				return true;
			}

			return false;

		}

		public function wlf_wp_rocket($page,$p){

			if(in_array('all', $page)){

				$page = array();
				//here
				$args = array(
				'post_type' => $p,
				'numberposts' => -1,
				'fields'=>'ids',			
				);
				$ids  = get_posts( $args );

				foreach ($ids as $value) {
					$page[] = $value;
				}

			}
			
		    $status=0;
			// Load WordPress.
			require(trailingslashit( ABSPATH ) .'wp-load.php' );
			define( 'WP_USE_THEMES', false );
			$pages_to_clean_preload = array();
			$domain = get_site_url();
			foreach ($page as $eachpage) {
				$pages_to_clean_preload[] = $domain.'/'.basename(get_permalink($eachpage));
				// $pages_to_clean_preload[] = get_permalink( $eachpage );
			}

			if ( function_exists( 'rocket_clean_post' ) ) {

				foreach( $pages_to_clean_preload as $page_to_clean) {
					$data = rocket_clean_post( url_to_postid ( $page_to_clean ) );
					$status = $data;
				}
			}

			return $status;

		}

		public function wlf_auto_cache_manage($id){

			// update_option('hookfire','fired on=>'.$id);
			$postType =  get_post_type($id);
			// update_option('hookfire','post type of this=>'.$postType);

			$post_upfront_id=$id;
			$args = array(
				'post_type' => 'wlf_auto_cache',
				'numberposts' => -1,
				'fields'=>'ids',			
			);
			$ids  = get_posts( $args );
			$data = array();	
			$date = current_time('d-M-Y h:i:s a');

			foreach ($ids as $id) {
				$fields = get_post_meta($id,'wlf_fields',true);

				foreach ($fields as $field) {

					$unm = get_post_meta($id,'unm'.$field,true);
					
					if($unm==$postType){
						$act_unm = get_post_meta($id,'act_unm'.$field,true);
						$wlf_fields_loop = get_post_meta($id,'wlf_fields_loop'.$field,true);
						
						$conditions = array("header","footer","popup");
						$meta = get_post_meta($post_upfront_id,'_elementor_template_type',true);
						update_option('data',$meta);
						if(in_array($meta, $conditions))
						{
							// here68
							$act = get_post_meta($post_upfront_id,'_elementor_conditions');
							$valsend = array();
							foreach ($act as $value) {
								foreach($value as $val){
									$val1 = explode('/',$val)[3];
									if(!$val1){
										$valsend[] = "all";
									}
									else{
										if(explode('/',$val)[2]=="page"){
											$valsend[] = $val1;
										}
									}
								}
							}
							// update_option('data',$valsend);

			                $status = $this->wlf_wp_rocket($valsend,"page");
			                $status1 = $this->wlf_cloudflare($valsend,"page");

							$data[] = array(
								'ID' => $id,
								'On' => $post_upfront_id,
								'trigger_type' => $unm,
								'Trigger' => $wlf_fields_loop,
								'action_type' => "page",
								'Action' => $valsend,
								'date_time' => $date,
								'cloudflare' => $status1?$status1:'0',
								'wp_rocket' => $status?$status:'0',
							);
						}
						else{

						if(in_array('all', $wlf_fields_loop)){
							$act_wlf_fields_loop = get_post_meta($id,'act_wlf_fields_loop'.$field,true);
							$status;
							$status1;

							// foreach ($act_wlf_fields_loop as $field) 
				   			//{
				                $status = $this->wlf_wp_rocket($act_wlf_fields_loop,$act_unm);
				                $status1 = $this->wlf_cloudflare($act_wlf_fields_loop,$act_unm);

				            // }
							$data[] = array(
								'ID' => $id,
								'On' => $post_upfront_id,
								'trigger_type' => $unm,
								'Trigger' => $wlf_fields_loop,
								'action_type' => $act_unm,
								'Action' => $act_wlf_fields_loop,
								'date_time' => $date,
								'cloudflare' => $status1?$status1:'0',
								'wp_rocket' => $status?$status:'0',
							);
						}
						elseif (in_array($post_upfront_id, $wlf_fields_loop)) 
						{
							$act_wlf_fields_loop = get_post_meta($id,'act_wlf_fields_loop'.$field,true);
							$status;
							// foreach ($act_wlf_fields_loop as $field) 
				   			//{
				                $status = $this->wlf_wp_rocket($act_wlf_fields_loop,$act_unm);
				                $status1 = $this->wlf_cloudflare($act_wlf_fields_loop,$act_unm);

				            // }
							
							$data[] = array(
								'ID' => $id,
								'On' => $post_upfront_id,
								'trigger_type' => $unm,
								'Trigger' => $wlf_fields_loop,
								'action_type' => $act_unm,
								'Action' => $act_wlf_fields_loop,
								'date_time' => $date,
								'cloudflare' => $status1?$status1:'0',
								'wp_rocket' => $status?$status:'0',

							);
                            // update_option('hookfire  ',$status);
						}
						else
						{
							continue;
						}
						}
					}
					else
					{
						continue;
					}
                        
				}
			}
			if(get_option('log_data_2')){
				$old_data = get_option('log_data_2');
				$new_arr = array_merge($data,$old_data);
				update_option('log_data_2',$new_arr);
			}
			else{
				update_option('log_data_2',$data);
			}

		}

		public function UserDetails( $post_id ) {
			// print_r(get_post_type($id));
			// die();

			if (defined('DOING_AJAX') && DOING_AJAX) { 

			}
			else{
				$this->wlf_auto_cache_manage($post_id);
			}
			// print_r(get_post_meta($post_id,'wlf_fields',true));
			// die();

			if(get_post_meta($post_id,'wlf_fields',true)){

				$data=get_post_meta(get_the_id(),'wlf_fields',true);

					foreach ($data as $value) {

						$unm='unm'.$value;
						$field='wlf_fields_loop'.$value;

						$unm1='act_unm'.$value;
						$field1='act_wlf_fields_loop'.$value;
						
						if(isset($_POST[$unm])){

							$val=$_POST[$unm];
							update_post_meta($post_id ,$unm,$val);

						}
						if(isset($_POST[$field])){

							$val=$_POST[$field];
							update_post_meta($post_id ,$field,$val);

						}

						if(isset($_POST[$unm1])){

							$val=$_POST[$unm1];
							update_post_meta($post_id ,$unm1,$val);

						}
						if(isset($_POST[$field1])){

							$val=$_POST[$field1];
							update_post_meta($post_id ,$field1,$val);

						}
					}
				
			}
		}

		public function reorder_columns( $columns) {

			$my_columns = array();
			$title      = 'title'; 

			foreach ($columns as $key => $value) {

				if ($key==$title) {

					$my_columns['cstmID']       = '';
					$my_columns['postType']      = '';   // Move author column before title column
					$my_columns['linkedPages']      = '';
					$my_columns['date']        = '';   // Move date column before title column
				}

				$my_columns[$key] = $value;
			}
			return $my_columns;
		}

		public function wpdocs_posts_thumb_columns( $columns ) {

			unset($columns['title']);
			unset($columns['date']);

			$post_new_columns = array(
			   'cstmID'      => esc_html__( 'Rules', 'text_domain' ),
			   'Trigger'      => esc_html__( 'Trigger', 'text_domain' ),
			   'Action'      => esc_html__( 'Action', 'text_domain' ),
			   'dat'      => esc_html__( 'Date', 'text_domain' ),
			   ''      => esc_html__( '', 'text_domain' ),
			   // 'status'      => esc_html__( 'Status', 'text_domain' ),
			   'actions'      => esc_html__( 'Manage', 'text_domain' ),
			   

			);
			return array_merge( $columns, $post_new_columns );

		}

		public function wpdocs_posts_custom_columns( $column_name, $id ) {
			global $post;
			if ( 'cstmID' === $column_name ) {
				echo '<b>Rule # '.get_the_id().'</b>';
			}
			if ( 'status' === $column_name ) {
				?>
				<div class="sync_button" style="padding: 0px!important;">
					<input type="button" style="background-color: gray;"  class="button button-primary wf_c_done" value="C">
					<input type="button" style=""  class="button button-primary wf_r_done" value="R">
				</div>
				<?php
			}
			if ( 'dat' === $column_name ) {
				$old_date = date($post->post_modified);              // returns Saturday, January 30 10 02:06:34
				$old_date_timestamp = strtotime($old_date);
				$new_date = date('d-M-Y \a\t h:i:s a', $old_date_timestamp);   
				echo '<b>Updated</b>'.'<br />'.$new_date;
			}
			if ( 'Trigger' === $column_name ) {
				$wlf_trigger = get_post_meta($id,'wlf_fields',true);
				if(!empty($wlf_trigger)){
					foreach ($wlf_trigger as $value) {
						if(!empty( get_post_meta($id,'wlf_fields_loop'.$value,true))){
							echo '<b>'.$this->define_post_name(get_post_meta($id,'unm'.$value,true)).'</b>'.' [';
							echo in_array('all',get_post_meta($id,'wlf_fields_loop'.$value,true))?' All ':sizeof(get_post_meta($id,'wlf_fields_loop'.$value,true));
							echo ']'.'<br />';
						}
						// if(!empty( get_post_meta($id,'wlf_fields_loop'.$value,true))){
						// 	echo '<b>'.$this->define_post_name(get_post_meta($id,'unm'.$value,true)).'</b>'.' ['.sizeof(get_post_meta($id,'wlf_fields_loop'.$value,true)).']'.'<br />';
						// }
						else{
							echo '-'.'<br />';
						}
					}
				}
			}

			if ( 'Action' === $column_name ) {
				$act_wlf = get_post_meta($id,'wlf_fields',true);
				if(!empty($act_wlf)){
					foreach ($act_wlf as $value) {
						if(!empty( get_post_meta($id,'act_wlf_fields_loop'.$value,true))){
							echo '<b>'.$this->define_post_name(get_post_meta($id,'act_unm'.$value,true)).'</b>'.' [';
							echo in_array('all',get_post_meta($id,'act_wlf_fields_loop'.$value,true))?' All ':sizeof(get_post_meta($id,'act_wlf_fields_loop'.$value,true));
							echo ']'.'<br />';
						}
						else{
							echo '-'.'<br />';
						}
					}
				}
			}

			if ( 'actions' === $column_name ) {
				$base_url=get_bloginfo('wpurl');
				?>
				<div class="sync_button" style="padding: 0px!important;">
					<a href="<?php echo $base_url.'/wp-admin/post.php?post='.get_the_id().'&action=edit' ?>" style="margin:0px!important;" name="wf_jk_sync_data" class="button button-primary" > Edit </a>
					<input type="button" style="" name="wf_jk_sync_data" class="button button-primary wf_del_data" value="Delete">
				</div>
				<?php
			}

		}

		public function add_meta_box() {

				add_meta_box(
					'Details',
					__( 'Details', 'textdomain' ),
					array( $this, 'render_meta_box_content' ),
					'wlf_auto_cache',
					'advanced',
					'high',
				);

		}

		public function wlf_admin_enque_scripts() {

			$url = isset($_SERVER['REQUEST_URI'])?sanitize_text_field($_SERVER['REQUEST_URI']):'';

			if (strstr($url, 'wlf_auto_cache') || strstr($url, 'action=edit') || strstr($url, 'post_type=wlf_auto_cache')) {
				// Add Styling
				wp_enqueue_style( 'wselect2-css', WLF_URL . '/assets/css/select2.css' , array(), '4.1.0-rc.0');
				wp_enqueue_style( 'wstyle-css', WLF_URL . '/assets/css/style1.css' , array(), '4.1.0-rc.0');
				wp_enqueue_style( 'wstyle-setting-css', WLF_URL . '/assets/css/wlf_settings.css' , array(), '4.1.0-rc.0');
				wp_enqueue_style( 'wlmain-css', WLF_URL . '/assets/css/main.css',  array(), '4.1.0-rc.0');

				//Add the Select2 JavaScript file
				wp_enqueue_script( 'wselect2-js', WLF_URL . '/assets/js/select2.js' , 'jquery', '4.1.0-rc.0', false);
				//Add a JavaScript file to initialize the Select2 elements
				wp_enqueue_script( 'wselect2-init', WLF_URL . '/assets/js/main.js', 'jquery', '4.1.0-rc.0', false);
				$wlf_extra_data = array(
				'admin_url' => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'wlf-ajax-nonce' ),
				);
				wp_localize_script( 'wselect2-init', 'wlf_ajax_var', $wlf_extra_data );
			}

		}

		public function wlf_repeater_fields_delete(){

			if(isset($_POST['delID']) && isset($_POST['postID'])){

				$id  = $_POST['delID'];
				$pid = $_POST['postID'];
				$id1 = 'unm'.$id;
				$id2 = 'wlf_fields_loop'.$id;

				$dataArray = get_post_meta($pid, 'wlf_fields' ,true);

				if (($key = array_search($id, $dataArray)) !== false) {
				    unset($dataArray[$key]);
				    update_post_meta($pid, 'wlf_fields' ,$dataArray);
				}

			}

			$this->renderFields($pid);
			
			die();

		}

		public function wlf_repeater_fields(){
			if(isset($_POST['postID'])){

				$postID=$_POST['postID'];
				$post_id = wp_insert_post(array (
			    'post_type' => 'hd_wlf_auto_cache',
			    'post_title' => '',
			    'post_content' => '',
			    'post_status' => 'publish',
			    'comment_status' => 'closed',   // if you prefer
			    'ping_status' => 'closed',      // if you prefer
			));
			}

			if(get_post_meta($postID,'wlf_fields',true)){

				$old_data=get_post_meta($postID,'wlf_fields',true);
				$new_data=array($post_id);
				$new_array=array_merge($new_data,$old_data);
				update_post_meta($postID, 'wlf_fields' ,$new_array);

			}
			else{
				$new_data=array($post_id);
				update_post_meta($postID, 'wlf_fields',$new_data);
			}

				$this->renderFields($postID);
				
			die();

		}

		public function wlf_select_data() {

			if(isset($_POST['wlfselected'])){
				$pro = $_POST['wlfselected'];
			}
			$args       = array(
				'post_type'   => $pro,
				'numberposts' => -1
			);

			$pros       = get_posts( $args );
			$sec_field_vals=(array)get_post_meta($_POST['id'],'wlf_fields_loop'.$_POST['val'],true);
			ob_start();

			?>
			<select multiple id="af_er_edit_review_products_can_be_review" name="" class="wlf_multi_select">
				<option <?php echo in_array('all', $sec_field_vals)?"selected":''; ?>  value="<?php echo 'all'; ?>">All</option>
				<?php if($pro!='Select Post Type'){ 
				
					
					foreach ($pros as $valuePostType) {
					?>
						 <option  value="<?php echo esc_attr($valuePostType->ID) ?>" 
						<?php if( in_array($valuePostType->ID, $sec_field_vals)){
							echo "selected";
						}?> >
						<?php echo $valuePostType->post_title; ?>
							
						</option>
				<?php } }?>
									

			</select>
			<?php
			$dat = ob_get_clean();
			$response       = array(
				'sel_data' => $dat,
				);
				echo wp_json_encode( $response );
			die();

		}

		public function render_meta_box_content() {

			global $wp_post_types;
			?>
			<!-- <span id="wlf_header"><h3>Trigger</h3><input type="button" id="sps" title="add new rule" class="button button-primary wf_jk_sync_data_jira wlf_add"value='+' data-theId="<?php echo get_the_ID(); ?>" /></span> -->
			<!-- <span id="wlf_header"><input type="button" id="sps" title="add new rule" class="button button-primary wf_jk_sync_data_jira wlf_add"value='+' data-theId="<?php echo get_the_ID(); ?>" /></span> -->

				<span id="wlf_header" style="justify-content: end;">
					<input type="button" style="float:right;font-size: 16px!important;" id="sps" title="add new rule" class="button button-primary wf_jk_sync_data_jira wlf_add"	value='Add Trigger/Action' data-theId="<?php echo get_the_ID(); ?>" />
				</span>
			
			<div class="wlf_main_div wlf">
				<?php
				$this->renderFields(get_the_id());
				
				?>
			<!-- </div>  -->
			</div>

			<?php            
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
		public function wlf_cloudflare_connection(){
			if(isset($_POST['token'])){
				$token = $_POST['token'];
			}
			if(isset($_POST['zone'])){
				$zone = $_POST['zone'];
			}
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/?per_page=300',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
			  'X-Auth-Email: '.$token,
			  'X-Auth-Key: '.$zone,
			  'Content-Type: application/json',
			)));

			$response = curl_exec($curl);
			curl_close($curl);
			$st_response = json_decode($response);
			if(	$st_response->success == 1 ){
				// print_r($st_response->result);
				$result = $st_response->result;
				?>
				Select Website <select class="selected_web" name="selected_web">
				<?php
				foreach ($result as $value) {
					?>
					<option value="<?php echo $value->name.','.$value->id; ?>" ><?php echo $value->name; ?></option>
					<?php
				}
				?>
				</select>
				<input type="button" name="wlf_selectweb_btn" class="button-primary wlf_selectweb_btn" value="save">
				<br />
			<?php
			}
			else if($st_response->success == ''){
				// $retarr = array('type'=>'error','code'=>$st_response->errors[0]->error_chain[0]->code,'message'=>$st_response->errors[0]->error_chain[0]->message);
				echo 'error_6111';
			}
			// print_r( json_decode( $response ) );
			die();
		}

		public function save_tkn_web_sel()
		{

			update_option('selected_web',$_POST['selweb']);
			update_option('selected_zon',$_POST['selzon']);
			update_option('wf_conf_token_unique',$_POST['seltkn']);
			update_option('wf_conf_zone_unique',$_POST['selzzn']);
            
			die();
		}
		public function wlf_cloud_disc()
		{

			update_option('wf_conf_token_unique','');
			update_option('wf_conf_zone_unique','');
			die();

		}
		public function wlf_manual_cache()
		{
			
			if ( function_exists( 'rocket_clean_domain' ) ) {
				rocket_clean_domain();
				update_option('cache_status','Cache cleared manually @ '.current_time('d-M-Y h:i:s a'));
			}
			else{
				rocket_clean_domain();
				update_option('cache_status','Cache cleared manually @ '.current_time('d-M-Y h:i:s a').' without  rocket_clean_domain function entrance.');
			}
			echo "ok";
			die();
		}
	}
	$MyTestimonial_add = new MyTestimonial_Add();
}