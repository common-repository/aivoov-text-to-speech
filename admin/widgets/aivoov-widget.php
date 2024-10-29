<?php
class Elementor_Aivoov_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'aivoov_widget';
	}

	public function get_title() {
		return esc_html__( 'AiVOOV', 'elementor-addon' );
	}

	public function get_icon() {
		return 'aivoov-logo';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	public function get_keywords() {
		return [ 'aivoov', 'voice', 'player' ];
	}

	protected function render() {
		 echo do_shortcode("[aivoov_player]");
	}
}