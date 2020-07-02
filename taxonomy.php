<?php

use Timber\Timber;
use Timber\PostQuery;

global $paged;
if (!isset($paged) || !$paged){
    $paged = 1;
}

$context = Timber::get_context();

$tax = get_queried_object();
$context['tax'] = $tax;


if($_GET['child']){
    $args = array(
        'order' => 'ASC',
        'posts_per_page' => 6,
        'paged' => $paged,
        'post_type' => 'produit',
        'tax_query' => array(
            array(
                'taxonomy' => $tax->taxonomy,
                'field' => 'slug',
                'terms' => $_GET['child']
            )
        )
    );
}else{
    $args = array(
        'order' => 'ASC',
        'posts_per_page' => 6,
        'paged' => $paged,
        'post_type' => 'produit',
        'tax_query' => array(
            array(
                'taxonomy' => $tax->taxonomy,
                'field' => 'slug',
                'terms' => $tax->slug
            )
        )
    );
}



$context['posts'] = new PostQuery($args);
$context['paged'] = $paged;


$child_category = array();

foreach (get_term_children($tax->term_id, $tax->taxonomy) as $child_id):
    $child = get_term($child_id);
    array_push($child_category, $child);
endforeach;

$context['child_category'] = $child_category;

Timber::render( array( 'taxonomy-' . $tax->taxonomy. '.twig', 'taxonomy.twig' ), $context );