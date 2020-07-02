<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Timber\Timber;

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;

if($post->post_type == 'produit'):

    $terms = array();

    foreach ($post->terms('catalogue') as $term):
        array_push($terms, $term->slug);
    endforeach;

    $args = array(
        'post_type' => 'produit',
        'orderby' => 'rand',
        'posts_per_page' => 3,
        'post__not_in' => array($post->ID),
        'tax_query' => array(
            array(
                'taxonomy' => 'catalogue',
                'field' => 'slug',
                'terms' => $terms
            )
        )

    );


    $context['similaire'] = Timber::get_posts($args);

endif;

Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );

