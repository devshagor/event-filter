<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

class LocationFilterOne extends \Elementor\Widget_Base {

	public function get_name() {
        return "locationfilterone";
    }

	public function get_title() {
        return esc_html__( "Location Filter","locatefilter");
    }

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_categories() {
        return array('eventfilter');
    }

	protected function register_controls() {
        $this->start_controls_section(
			'location_title',
			[
				'label' => esc_html__( 'Location Title', 'locatefilter' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'placeholder_text',
			[
				'label' => esc_html__( 'Placeholder Text', 'locatefilter' ),
				'type' => Controls_Manager::TEXT,
				'default'=> esc_html__("Search by location or event","locatefilter"),
				'placeholder' => esc_html__( 'Enter Placeholder Text title', 'locatefilter' ),
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Filter Style', 'locatefilter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		); 

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Button Color', 'locatefilter' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-filter .filter-btn' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings_for_display();
			$placeholder_text = $settings['placeholder_text'];
		
		wp_enqueue_script('event_scripts');
		wp_enqueue_style('event_style');
?>

<div class="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="search-filter">
					<input class="filte-input" type="text" placeholder="<?php echo esc_html($placeholder_text); ?>">
					<button type="button" class="filter-btn" data-toggle="modal" data-target="#exampleModal">
						<?php echo esc_html__('Filter','textdomain'); ?>
					</button>
					
					<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-body">
								<?php
									$categories = get_terms('category', array('hide_empty' => false));
								?>

									<div class="row">
										<div class="col-md-4">
											<ul class="cat-list">
												<li>
													<a class="cat-list-item active" href="#!" data-slug="">
														<?php 
															echo esc_html__('All Events','textdomain');
														?>
														
													</a>
												</li>

												<?php foreach($categories as $category) : ?>
													<li>
													<a class="cat-list-item" href="#!" data-slug="<?php echo esc_attr($category->slug); ?>">
														<?php 
														echo esc_html($category->name);
														?>
														<span>
															(<?php echo esc_html($category->count); ?>)
														</span>
													</a>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
										<div class="col-md-4">
											<ul class="cat-list">
												<li>
													<a class="cat-list-item active" href="#!" data-slug="">
														<?php 
															echo esc_html__('All Locations','textdomain');
														?>
													</a>
												</li>

												<?php

												$locations = get_terms('location', array('hide_empty' => false));
												foreach($locations as $location) : ?>
													<li>
													<a class="cat-list-item" href="#!" data-slug="<?php echo esc_attr($location->slug); ?>">
														<?php 
														echo esc_html($location->name);
														?>
														<span>
															(<?php echo esc_html($location->count); ?>)
														</span>
													</a>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			
			</div>
		</div>
		<div class="row event-row">
			<?php 
				$categories = get_categories();
				
				$events_query = new WP_Query([
					'post_type' => 'post',
					'posts_per_page' => -1,
					'order_by' => 'date',
					'order' => 'desc',
				]);
				
				if($events_query->have_posts()): ?>
					<?php
						while($events_query->have_posts()) : $events_query->the_post();
							?>
							<div class="col-md-4">
								<div class="event-item">
									<?php if(has_post_thumbnail()): ?>
										<div class="thumb">
											<a href="<?php echo esc_url(the_permalink()); ?>">
												<?php 
													the_post_thumbnail();
												?>
										</a>
										</div>
									<?php endif; ?>
									<div class="content">
										<h2 class="event-title">
											<a href="<?php echo esc_url(the_permalink()); ?>">
												<?php echo esc_html(the_title()); ?>
											</a>
										</h2>
										<p>
											<?php 
												echo wp_trim_words( get_the_excerpt(), 10, '...' );
											?>
										</p>
									</div>
								</div>
							</div>
							<?php
						endwhile;
					?>
				<?php wp_reset_postdata(); 
				endif; 
			?>
		</div>
	</div>
</div>
		
		<?php 
		
    }

	protected function content_template() {}

}