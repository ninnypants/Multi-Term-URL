<?php
/*
Plugin Name: Multi Term URL
Plugin URI: http://ninnypants.com
Description: Allow urls to contain multiple terms.
Version: 2.0
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

namespace Plugin\MultiTermUrl;

function get_rewrite_rule() {
	global $wp_rewrite;

	/**
	 * What taxonomies have multiterm support.
	 *
	 * @var $taxonomies Array of taxonomies with multiterm support.
	 */
	$taxonomies = apply_filters( 'multiterm_taxonomies', [ 'post_tag' ] );

	if ( empty( $taxonomies ) ) {
		return;
	}

	$rewrite_parts = [];
	foreach ( $taxonomies as $tax ) {
		$tax = get_taxonomy( $tax );
		// Support rewrite fronts.
		$front = '';
		if ( $tax->rewrite['with_front'] ) {
			$front = ltrim( $wp_rewrite->front, '/' );
		}

		$rewrite_parts[] = trailingslashit( $front . $tax->rewrite['slug'] );
	}

	return '((?:' . implode( '|', $rewrite_parts ) . ')(?:(?!' . implode( '|', $rewrite_parts ) . ').)+)+?';
}

/**
 * Process multi term support.
 *
 * @param \WP $wp
 */
function process_multiterm_url( $wp ) {
	$pattern = '#' . get_rewrite_rule() . '#i';
	if ( preg_match_all( $pattern, $wp->request, $matches ) ) {

		// Map rewrite slugs to taxonomy names.
		$taxonomies   = get_taxonomies( [ 'public' => true ], 'objects' );
		$taxonomy_map = [];
		foreach ( $taxonomies as $tax ) {
			$taxonomy_map[ $tax->rewrite['slug'] ] = $tax->name;
		}

		// Build tax query.
		$tax_query = [
			'relation' => 'AND',
		];
		foreach ( $matches[0] as $term_string ) {
			$terms        = array_filter( explode( '/', $term_string ) );
			$rewrite_slug = array_shift( $terms );
			$tax_query[]  = [
				'taxonomy' => $taxonomy_map[ $rewrite_slug ],
				'field'    => 'slug',
				'terms'    => $terms,
				'operator' => 'AND',
			];
		}
		$wp->query_vars['tax_query'] = $tax_query;
		unset( $wp->query_vars['error'] );
	}
}
add_action( 'parse_request', __NAMESPACE__ . '\process_multiterm_url' );
