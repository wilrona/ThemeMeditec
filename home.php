<?php
/* Template Name: Home page */

use Timber\Timber;

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$context['services'] = array();

if($post->list_services):
    foreach ($post->list_services as $service):
        $current_post = Timber::get_post($service['service']);
        $context['services'][] = $current_post;
    endforeach;
endif;

$context['catalogues'] = array();

if($post->list_catalogue):
    foreach ($post->list_catalogue as $catalogue):
        $current_tax = Timber::get_term($catalogue['catalogue'], "catalogue");
        $context['catalogues'][] = $current_tax;
    endforeach;
endif;

$args = array(
    'post_type' => 'produit',
    'orderby' => 'rand',
    'posts_per_page' => $post->nbre_produit ? $post->nbre_produit : 3
);

$context['produits'] = Timber::get_posts($args);

Timber::render(  'page-home.twig' , $context );