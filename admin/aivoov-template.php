<?php
/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

get_header();

?><div class="aivoov-content-start"></div><?php
if ( have_posts() ) {

    while ( have_posts() ) {

        the_post(); 
        if ( '1' == get_option('aivoov_read_title') ) {
            ?><p><?php the_title(); ?></p><break time="1s"></break><?php
        }

        the_content();

    }

}
?><div class="aivoov-content-end"></div><?php

get_footer();
