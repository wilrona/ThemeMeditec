<?php
/* Template Name: FAQ page */

use Timber\Timber;

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$args = array(
    'post_type' => 'faq',
    'post_per_page' => -1
);

$context['faqs'] = Timber::get_posts($args);

Timber::render(  'page-faq.twig' , $context );