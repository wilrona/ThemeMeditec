<?php
/* Template Name: Panier page */

use Timber\Timber;

if (!isset($_SESSION['panier'])) {
    $produits = [];
} else {
    $produits = $_SESSION['panier'];
}

$context = Timber::get_context();

$panier = [];
$context['total'] = 0;

foreach ($produits as $produit):

    $current_post = Timber::get_post($produit['produit']);

    $data = array(
        'produit' => $current_post,
        'qte' => $produit['qte'],
        'prix' => $produit['prix']
    );

    array_push($panier, $data);

    $context['total'] += $produit['qte'] * $produit['prix'];

endforeach;


if($_POST){

    $numero_cmd = date('Y').'/'.generateParrainCode(4);

    $email = \WP_Mail::init();
    $email->subject('Nouvelle Commande No '.$numero_cmd);
    $email->to(strtolower(tr_options_field('options.email_company')));
    $email->template(get_template_directory() .'/email/commande.php', ['produits' => $panier, 'data' => $_POST, 'total' => $context['total']]);
    $email->sendAsHTML(true);
    $email->send();

    unset($_SESSION['panier']);

    $saved = array(
        'client' => $_POST,
        'panier' => $panier
    );

    $id = wp_insert_post(array(
       'post_type' => 'commande',
       'post_title' => 'Commande No '. $numero_cmd,
       'post_status' => 'publish'
    ));

    add_post_meta($id, 'cmd', $saved);

    wp_redirect(get_permalink(tr_options_field('options.page_panier')."?success=true"));
}


$post = new TimberPost();
$context['post'] = $post;

$context['panier'] = $panier;


Timber::render(  'page-panier.twig' , $context );