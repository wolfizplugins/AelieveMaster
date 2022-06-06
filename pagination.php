<?php
if (!class_exists('MyTestimonial_pagination')) {
	class MyTestimonial_pagination {
		public function __construct() {
		}
		public function pagination( $testimonials) {
			$tax = explode('/', $_SERVER['PATH_INFO'])[1];
			if ($tax=='testimonial_cats' || $tax=='testimonial-tags') {
				$this->MyTestimonial_pagination();
			}
			?>
				<div id="pagination">
					<div id="right">
						<?php
						$total_pages = $testimonials->max_num_pages;
										
						if ($total_pages > 1) {

							$current_page = max(1, get_query_var('paged'));
									
							echo paginate_links(array(
								'base' => get_pagenum_link(1) . '%_%',
								'format' => '/page/%#%',
								'current' => $current_page,
								'total' => $total_pages,
								'prev_text'    => __('<button class="btn">« prev</button>'),
								'next_text'    => __('<button class="btn">next »</button>'),
							));
						}  
						?>
					</div>
				</div>
			<?php
		}
		public function MyTestimonial_pagination() {
			?>
			<style>
			  #pagination{
				width: 70%!important;
			  }
			</style>
			<?php
		}
		public function pagination2( $testimonials, $no) {
			$this->MyTestimonial_pagination();
			?>
				<div id="pagination">
					<div id="right">
						<?php
						$total_pages = $no-1;
										
						if ($total_pages > 1) {

							$current_page = max(1, get_query_var('paged'));
									
							echo paginate_links(array(
								'base' => get_pagenum_link(1) . '%_%',
								'format' => '/page/%#%',
								'current' => $current_page,
								'total' => $total_pages,
								'prev_text'    => __('<button class="btn">« prev</button>'),
								'next_text'    => __('<button class="btn">next »</button>'),
							));
						}  
						?>
					</div>
				</div>
			<?php
		}
	}
	$MyTestimonial_pagination = new MyTestimonial_pagination();
}
?>
