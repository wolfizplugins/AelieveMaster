<?php
if (!class_exists('MyTestimonial_ShortCodes')) {
	class MyTestimonial_ShortCodes {
		public function __construct() {
			add_shortcode( 'Categories', array($this,'MyTestimonial_Categories'));
			add_shortcode( 'LatestPosts', array($this,'MyTestimonial_LatestPosts'));
		}
		public function MyTestimonial_Categories() {
			$terms = get_terms( array(
			'taxonomy' => 'testimonial_cats',
			'hide_empty' => false,
			'number' => 5
			) );
			foreach ($terms as $cat) {
				?>
						<tr>
							<td><span class="dashicons dashicons-open-folder"></span> <a href="<?php echo '../testimonial_cats/' . $cat->slug; ?>">
																										  <?php 
																											echo $cat->name;
																											$the_query = new WP_Query( array(
																											'post_type' => 'my_testimonials',
																											'tax_query' => array(
																											array(
																											'taxonomy' => 'testimonial_cats',
																											'terms' => $cat
																											)
																											)
																											) );
																										  $count       = $the_query->found_posts;
																										  echo '&nbsp;(' . $count . ')';  
																											?>
							</a></td><br />
							 <!-- <td><span class="dashicons dashicons-open-folder"></span>  <?php echo $cat->name; ?></td><br /> -->
						</tr>
				<?php 
			} 
		}
		public function MyTestimonial_LatestPosts() {
			$args           = array(
				'post_type'      => 'my_testimonials',
				'posts_per_page' => 5
			  );
			  $testimonials = new WP_Query( $args );
			if ( $testimonials->have_posts() ) {
				while ( $testimonials->have_posts() ) {
					$testimonials->the_post();
					?>
						<tr>
							 <td><a href="<?php echo get_post_field( 'post_name', get_post() ); ?>" data-title="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></a></td><br />
						</tr>
					<?php
				}
			}
		}
	}
	$MyTestimonial_ShortCodes = new MyTestimonial_ShortCodes();
}
