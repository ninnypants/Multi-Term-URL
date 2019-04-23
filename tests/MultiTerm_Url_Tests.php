<?php

namespace Plugin\MultiTermUrl;

class MultiTerm_Url_Tests extends \WP_UnitTestCase {

	public function testUrls() {

		// Set the permalinks for posts.
		$this->set_permalink_structure( '/%postname%/' );

		$this->go_to( site_url( 'tag/tag1/tag2' ) );

		$tax_query = get_query_var( 'tax_query' );

		$expected = [
			'relation' => 'AND',
		];

		$expected[] = [
			'taxonomy' => 'post_tag',
			'field' => 'slug',
			'terms' => [
				'tag1',
				'tag2',
			],
			'operator' => 'AND',
		];

		$this->assertSame( $expected, $tax_query );

		// Add additional tests here.
	}
}
