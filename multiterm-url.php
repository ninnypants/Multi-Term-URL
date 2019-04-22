<?php
/*
Plugin Name: Multi Tag URL
Plugin URI: http://ninnypants.com
Description: Allow tags urls to contain multiple tags
Version: 1.0
Author: ninnypants
Author URI: http://ninnypants.com
License: GPL2

Copyright 2014  Tyrel Kelsey  (email : tyrel@ninnypants.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Multi_Tag_URL {
	public static $rewrite_rule;

	public static function get_rewrite_rule() {
		if ( ! empty( self::$rewrite_rule ) ) {
			return self::$rewrite_rule;
		}

		global $wp_rewrite;

		$tax = get_taxonomy( 'post_tag' );

		$front = '';
		if ( $tax->rewrite['with_front']  ) {
			$front = ltrim( $wp_rewrite->front, '/' );
		}

		self::$rewrite_rule = $front . $tax->rewrite['slug'] . '/(.+)/?$';

		return self::$rewrite_rule;
	}

	public static function add_rewrites( $wp_rewrite ) {
		$rewrite_rule = self::get_rewrite_rule();
		$new_rules = array();
		$new_rules[ $rewrite_rule ] = 'index.php?multitag=$matches[1]';
		$wp_rewrite->rules = array_merge( $new_rules, $wp_rewrite->rules );
	}

	public static function flush_rules() {
		$rules = get_option( 'rewrite_rules' );
		$rewrite_rule = self::get_rewrite_rule();
		if ( ! isset( $rules[ $rewrite_rule ] ) ) {
			flush_rewrite_rules();
		}

	}

	public static function add_multitag_query_var( $vars ) {
		$vars[] = 'multitag';
		return $vars;
	}

	public static function multi_tag( $wp_query ) {
		$multitag = $wp_query->get( 'multitag' );
		if ( $wp_query->is_main_query() && ! empty( $multitag ) ) {
			$tax_query = array(
				array(
					'taxonomy' => 'post_tag',
					'field' => 'slug',
					'terms' => explode( '/', $multitag ),
					'operator' => 'AND',
				),
			);

			$wp_query->set( 'tax_query', $tax_query );
		}
	}
}
add_action( 'wp_loaded', array( 'Multi_Tag_URL', 'flush_rules' ) );
add_filter( 'generate_rewrite_rules', array( 'Multi_Tag_URL', 'add_rewrites' ) );
add_filter( 'query_vars', array( 'Multi_Tag_URL', 'add_multitag_query_var' ) );
add_action( 'pre_get_posts', array( 'Multi_Tag_URL', 'multi_tag' ) );
