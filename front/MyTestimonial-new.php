<?php
if (!class_exists('MyTestimonial_new')) {
	class MyTestimonial_new {
		public function __construct() {
			add_shortcode( 'AddNewTestimonial', array($this,'viewAll'));
			add_action( 'wp_enqueue_scripts', array($this,'MyTestimonial_enqueue_scripts' ));
		}
		public function MyTestimonial_enqueue_scripts() {
			wp_enqueue_script( 'jQuery');
			wp_enqueue_style( 'my-theme', plugins_url('MyTestimonial/assets/css/frontStyle.css'), false );
			wp_enqueue_script( 'my-js', plugins_url('MyTestimonial/assets/js/frontScript.js'), array('jquery'), false );
		}
		public function viewAll() { 
			  $args         = array(
				'post_type'   => 'My_Testimonial',
				'post_status' => 'publish',
			   );
			  $testimonials = new WP_Query( $args );
			  if ( $testimonials->have_posts() ) {
					?>
				<div id="MyTestimonial_front_main">
					<?php
					while ( $testimonials->have_posts() ) {
						$testimonials->the_post();
						?>
						<div class="MyTestimonial_front_each" >
						<?php 
						if (has_post_thumbnail()) {
							?>
							  <img class="imgs" src="
							  <?php 
								echo get_the_post_thumbnail(get_the_ID());
						} else {
							?>
						  <img class='imgs' src="<?php echo plugins_url('MyTestimonial/assets/img/placeholder.png'); ?>" />
						<?php } ?>
						  <p data-title="<?php echo get_the_ID(); ?>" class="post_Title"><?php echo get_the_title(); ?></p>
						<?php if (get_the_content()!='') { ?>
						  <p class="post_Content"><?php echo get_the_content(); ?></p>
						  <!-- 
							<?php 
							if (strlen(get_the_content())<27) {
								?>
								 -->
							<!-- <p class="post_Content">.</p> -->
							<!-- <?php } ?> -->
						  <?php } else { ?>
						  <p class="post_Content">No description provided.</p>
						  <!-- <p class="post_Content">.</p> -->
						  <?php } ?>
						  <div class="bottom">
						  <p class="post_Dated"><?php echo get_the_date(); ?></p>
						<?php
						if (!empty(get_post_meta( get_the_ID(), 'client_name' ))) { 
							?>
						  <p class="post_Dated">By: <?php echo get_post_meta( get_the_ID(), 'client_name' )[0]; ?></p>
							<?php 
						} else {
							echo 'Unknown Author';} 
						?>
						  </div>
						</div>
						<?php
					}
					  wp_reset_postdata();
					?>
				  </div>
				  <?php
			  }
				
		}
	}
	$MyTestimonial_new = new MyTestimonial_new();
}
