<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

use Timber\Timber;
use Timber\PostQuery;

$templates = 'search.twig';
$context = Timber::get_context();

global $paged;
if (!isset($paged) || !$paged){
    $paged = 1;
}



$args = array(
    'order' => 'ASC',
    's' => get_search_query(),
    'posts_per_page' => 6,
    'paged' => $paged,
    'post_type' => 'produit',
    'meta_query' => array(
        array(
            'key' => 'prix',
            'value' => 0,
            'type' => 'numeric',
            'compare' => '>'
        )
    )
);

$context['title'] = ''. get_search_query();
$context['posts'] = new PostQuery($args);

Timber::render( $templates, $context );
