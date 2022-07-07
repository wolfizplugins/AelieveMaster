<?php
add_settings_section(
	'wlf_setting_tab',
	 __( 'Cloudflare API', 'wf-jk-textdomain' ),
	'wf_jk_generals_callback',
	'wlf_register_general_settings'
);
add_settings_section(
	'wlf_general_setting',
	 __( 'General Setting', 'wf-jk-textdomain' ),
	'wf_general_setting_callback',
	'wlf_register_post_settings'
);

add_settings_section(
	'wlf_log_setting',
	 __( 'Log Setting', 'wf-jk-textdomain' ),
	'wf_log_setting_callback',
	'wlf_register_log_settings'
);

add_settings_section(
	'wlf_plg_setting',
	 __( 'Plugin Update Cache', 'wf-jk-textdomain' ),
	'wf_plg_setting_callback',
	'wlf_register_plg_settings'
);
add_settings_section(
	'wlf_man_setting',
	 __( 'Manual Cache Clean', 'wf-jk-textdomain' ),
	'wf_man_setting_callback',
	'wlf_register_man_settings'
);
add_settings_section(
	'wlf_crn_setting',
	 __( 'Set up cron job', 'wf-jk-textdomain' ),
	'wf_jk_crn_callback',
	'wlf_register_crn_settings'
);
function wf_jk_generals_callback() { }
function wf_general_setting_callback() { }
function wf_log_setting_callback() { }
function wf_plg_setting_callback() { }
function wf_man_setting_callback() { }
function wf_jk_crn_callback(){ }

add_settings_field(
	'wf_cron_time',
	__( 'Recurring Time', 'wf-jk-textdomain' ),
	'wlf_crn_jobs',
	'wlf_register_crn_settings',
	'wlf_crn_setting'
);

register_setting(
	'wlf_crn_setting-page',
	'wf_cron_time'
);


add_settings_field(
	'wf_log_days',
	__( 'Keep log for day(s)', 'wf-jk-textdomain' ),
	'wlf_post_type_input',
	'wlf_register_log_settings',
	'wlf_log_setting'
);

register_setting(
	'wlf_log_setting-page',
	'wf_log_days'
);

// add_settings_field(
// 	'wf_manual',
// 	__( 'Manual Cache Clean', 'wf-jk-textdomain' ),
// 	'wlf_man_input',
// 	'wlf_register_man_settings',
// 	'wlf_man_setting'
// );

// register_setting(
// 	'wlf_man-page',
// 	'wf_manual'
// );

add_settings_field(
	'wf_post_unique',
	__( 'Select Post Types', 'wf-jk-textdomain' ),
	'wlf_post_type_chekbox',
	'wlf_register_post_settings',
	'wlf_general_setting'
);

register_setting(
	'wlf_general_setting-page',
	'wf_post_unique'
);

add_settings_field(
	'wf_gutinberg_status',
	__( 'Disable Gutenberg', 'wf-jk-textdomain' ),
	'wlf_gutinberg_chekbox',
	'wlf_register_post_settings',
	'wlf_general_setting'
);

register_setting(
	'wlf_general_setting-page',
	'wf_gutinberg_status'
);

add_settings_field(
	'wf_conf_id_unique',
	__( 'Cloudflare App', 'wf-jk-textdomain' ),
	'wf_jk_jira_confulace_id_callback',
	'wlf_register_general_settings',
	'wlf_setting_tab'
);

register_setting(
	'wlf_setting_tab-page',
	'wf_conf_id_unique'
);

add_settings_field(
	'wf_conf_token_unique',
	__( 'Cloudflare Email', 'wf-jk-textdomain' ),
	'wf_jk_jira_confulace_token_callback',
	'wlf_register_general_settings',
	'wlf_setting_tab'
);

register_setting(
	'wlf_setting_tab-page',
	'wf_conf_token_unique'
);
add_settings_field(
	'wf_conf_zone_unique',
	__( 'Cloudflare Key', 'wf-jk-textdomain' ),
	'wf_jk_jira_confulace_zone_callback',
	'wlf_register_general_settings',
	'wlf_setting_tab'
);

register_setting(
	'wlf_setting_tab-page',
	'wf_conf_zone_unique'
);

add_settings_field(
	'wf_plg_selected',
	__( 'Select Plugins', 'wf-jk-textdomain' ),
	'wlf_plg_update',
	'wlf_register_plg_settings',
	'wlf_plg_setting'
);

register_setting(
	'wlf_plg_setting-page',
	'wf_plg_selected'
);
// add_settings_field(
// 	'wf_conf_url',
// 	__( 'Cloudflare Key', 'wf-jk-textdomain' ),
// 	'wf_jk_jira_confulace_url_callback',
// 	'wlf_register_general_settings',
// 	'wlf_setting_tab'
// );

// register_setting(
// 	'wlf_setting_tab-page',
// 	'wf_conf_url'
// );

function wf_jk_page_id_callack() {
	$sel_pages =get_option( 'wf_jk_all_pages' );
	$sel_pages =is_array( $sel_pages ) ? $sel_pages : array();
	?>
		<select name="wf_jk_all_pages[]" multiple class="wf_jk_all_pages_select2" style="width: 350px;">
		<?php
		$get_page_query = new WP_Query();
		$all_wf_pages   = $get_page_query->query( array(
			'post_type' => 'page',
			'posts_per_page' => -1
		));
		foreach ($all_wf_pages as $page) {
			$post  = get_page($page);
			$title = $post->post_title;
			$id    = $post->ID;
			?>
			<option value="<?php echo esc_attr($id); ?>"
				<?php 
				if (in_array($id, $sel_pages)) {
					esc_html_e('selected', 'wf-jk-textdomain');
				} 
				?>
				>
				<?php echo esc_attr($title); ?>
			</option>
			<?php
		} 
		?>
		</select>
		<p><?php echo esc_html__( 'Enter page id where you want plugin JS/CSS to work', 'wf-jk-textdomain' ); ?></p>
	<?php
}

function wlf_gutinberg_chekbox(){
	?>
	<input type="checkbox" <?php echo get_option('wf_gutinberg_status')?'checked':''; ?> name="wf_gutinberg_status"> Check to Disable
	<?php
}
function wlf_plg_update()
{
	$installed_plugins = get_plugins();
	$selected_plugins = get_option('wf_plg_selected');
	foreach ($installed_plugins as  $key=>$value) {
		?>
			<input <?php if(!empty($selected_plugins)){ echo in_array($value['TextDomain'], $selected_plugins)?"checked":"";} ?> type="checkbox" name="wf_plg_selected[]" class="plgchkbx" value="<?php echo $value['TextDomain']; ?>" >&nbsp;<?php echo $value['Name']; ?><br /><br />
		<?php
	}
}
function wlf_post_type_chekbox() {
	 $meta_id = get_option( 'wf_post_unique' );
	 global $wp_post_types;
	 ?>
	 <!-- <input type="checkbox" name="wf_post_unique[]" id="selectall">All <br /><br /> -->
	 <?php
	 foreach ($wp_post_types as $valuePostType) {
	 	?>

	 	<input <?php if(!empty($meta_id)){ echo in_array($valuePostType->name, $meta_id)?"checked":"";} ?> type="checkbox" name="wf_post_unique[]" class="allchkbx" value="<?php echo $valuePostType->name; ?>" >&nbsp;<?php echo $valuePostType->label; ?><br /><br />
	 	<?php
	 	// echo $valuePostType->name.'<br />';
	 }
	?>
	<!-- <input type="text" name="wf_post_unique" id="wf_meta_keyword_id" value="<?php echo esc_attr( $meta_id ); ?>"> -->
	<!-- <p><?php echo esc_html__( '', 'wf-jk-textdomain' ); ?></p> -->
	<?php
}
function wlf_post_type_input() {
	 $meta_id = get_option( 'wf_log_days' );
	?>
	<input type="number" name="wf_log_days" min="1" value="<?php echo $meta_id; ?>">
	<?php 
	
}
function wlf_crn_jobs() {
	 $meta_id = get_option( 'wf_cron_time' );
	?>
	<input type="number" name="wf_cron_time" min="1" value="<?php echo $meta_id; ?>">
	<p>Time should be in hours for recurrence of cron job.</p>
	<?php 
	
}
function wlf_man_input() {
		
}
function wf_jk_jira_confulace_id_callback() {
	 $meta_id = get_option( 'wf_conf_id_unique' );
	?>
	<input type="text" disabled name="wf_conf_id_unique" id="wf_meta_keyword_id" value="https://api.cloudflare.com/client/v4/zones">
	<p><?php echo esc_html__( '', 'wf-jk-textdomain' ); ?></p>
	<?php
}

function wf_jk_jira_confulace_token_callback() {
	 $meta_token = get_option( 'wf_conf_token_unique' );
	?>
	<input type="text" name="wf_conf_token_unique" id="wf_meta_keyword_token" value="<?php echo esc_attr( $meta_token ); ?>">
	<p><?php echo esc_html__( '', 'wf-jk-textdomain' ); ?></p>
	<?php
}

function wf_jk_jira_confulace_zone_callback() {
	 $meta_token = get_option( 'wf_conf_zone_unique' );
	?>
	<input type="text" name="wf_conf_zone_unique" id="wf_meta_keyword_zone" value="<?php echo esc_attr( $meta_token ); ?>">
	<p><?php echo esc_html__( '', 'wf-jk-textdomain' ); ?></p>
	<?php
}

function wf_jk_jira_confulace_url_callback() {
	 $meta_url = get_option( 'wf_conf_url' );
	?>
	<input type="text" name="wf_conf_url" id="wf_meta_keyword_url" value="<?php echo esc_attr( $meta_url ); ?>">
	<p><?php echo esc_html__( '', 'wf-jk-textdomain' ); ?></p>
	<?php
}

function wf_jk_jira_template_shortcode_url_callback() {
	?>
	<input type="hidden" name="wf_jira_temp">
	<p><?php echo esc_html__( '[wf_jira_faqs_temp]', 'wf-jk-textdomain' ); ?></p>
	<?php
}

function wf_jk_jira_shortcode_search_url_callback() {
	?>
	<input type="hidden" name="wf_jira_temp">
	<p><?php echo esc_html__( '[wf_jira_faqs_search_form]', 'wf-jk-textdomain' ); ?></p>
	<?php
}
