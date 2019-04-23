=== Multi Term URL ===
Contributors: ninnypants
Tags: rewrites, multiple terms
Donate link: https://ninnypants.com/plugins/
Requires at least: 4.0
Tested up to: 5.1.1
Requires PHP: 5.6
Stable tag: trunk
License: GPL2

Adds support for multiple terms and taxonomies to be used in site urls. Allowing urls like `https://site.com/tag/tag-1/tag-2/` and `https://site.com/tag/tag-1/tag-2/custom-tax/term-1/term-2/` to return posts instead of a 404.

## Taxonomy Support
The plugin supports the `post_tag` taxonomy by default, but support for other taxonomies can be added with the `multiterm_taxonomies` filter. You should use the taxonomy name and not the rewrite slug.

`
<?php
add_filter( 'multiterm_taxonomies', function ( $taxonomies ) {
	$taxonomies[] = 'custom-taxonomy';
	return $taxonomies;
} );
`

== Changelog ==
= 2.0 =
* Support for multiple dynamic taxonomies.
* Rewrite into php 5.6+

= 1.0 =
* Initial release with only post_tag support.
