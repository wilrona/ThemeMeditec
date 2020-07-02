<?php

use Timber\Timber;

add_action('wp_ajax_reset_panier', 'reset_panier_callback');
add_action('wp_ajax_nopriv_reset_panier', 'reset_panier_callback');

function reset_panier_callback(){

    $count = 0;
    if (isset($_SESSION['panier'])) {
        $produits = $_SESSION['panier'];
        $count = count($produits);
    }

    echo $count;
    wp_die();
}


add_action('wp_ajax_load_ressource', 'load_ressource_callback');
add_action('wp_ajax_nopriv_load_ressource', 'load_ressource_callback');



function load_ressource_callback()
{
  check_ajax_referer('load_ressource_security', 'security');

  $term_id = $_POST['current_rubrique'];

  $args = array(
    'post_status' => 'publish',
    'post_type' => 'ressource',
    'posts_per_page' => '-1',
    'tax_query' => array(
      array(
        'taxonomy' => 'rubrique',
        'field' => 'id',
        'terms' => $term_id,
      )
    )
  );

  $query = new WP_Query($args);

  ?>

  <div class="uk-overflow-auto uk-margin-medium-top">

    <table class="uk-table uk-table-small uk-table-middle uk-table-divider dataTable uk-table-striped">
      <thead>
        <tr>
          <th>Nom du fichier</th>
          <th style="width:20%">Annee</th>
          <th style="width:10%"></th>
        </tr>
      </thead>
      <tbody>

        <?php
        while ($query->have_posts()) : $query->the_post();

          ?>

          <tr>
            <td>
              <?= get_the_title() ?> </br>
              <small>
                <?php $terms = wp_get_post_terms(get_the_ID(), 'rubrique', array("fields" => "all")) ?>


                <?php

                $tax = '';

                foreach ($terms as $term) {

                  if ($term->parent) :
                    $tax .= ' - <b>' . $term->name . '</b>';
                  else :
                    $tax .= '<b>' . $term->name . '</b>';
                  endif;
                }

                echo $tax;
                ?>
              </small>


            </td>
            <td><?= tr_posts_field('annee', get_the_ID()) ?> </td>
            <td>
              <a href="#" uk-toggle="target: #modal" class="uk-button uk-button-primary uk-button-small add_cart"><i class="fas fa-cart-plus"> </i> </a>
            </td>
          </tr>

        <?php endwhile; ?>

      </tbody>
    </table>
  </div>

  <script>
    initDataTable();
  </script>


  <?php


  wp_die();
}



add_action('wp_ajax_update_cart', 'update_cart_callback');
add_action('wp_ajax_nopriv_update_cart', 'update_cart_callback');


function update_cart_callback(){
    check_ajax_referer('update_cart_security', 'security');

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
        $produits = [];
    } else {
        $produits = $_SESSION['panier'];
    }

    $id_current = $_POST['current_item'];
    $action = $_POST['actions'];

    if($action === 'update'){

        $qte = $_POST['value_item'];

        $index = array_search($id_current, array_column($produits, 'produit'));

        $produits[$index]['qte'] = $qte;

        $total = 0;

        foreach ($produits as $produit):

            $total += $produit['qte'] * $produit['prix'];

        endforeach;

        $data = array(
            'id' => $id_current,
            'item_montant' => number_format($qte * $produits[$index]['prix'], 0, ",", " "),
            'total' => number_format($total, 0, ",", " ")
        );

        echo json_encode($data);

    }elseif ($action === 'delete'){

        $index = array_search($id_current, array_column($produits, 'produit'));

        unset($produits[$index]);

        $data = array(
            'id' => $id_current,
            'empty' => count($produits)
        );

        echo json_encode($data);
    }

    $_SESSION['panier'] = array_values($produits);


    wp_die();
}





add_action('wp_ajax_load_cart', 'load_cart_callback');
add_action('wp_ajax_nopriv_load_cart', 'load_cart_callback');


function load_cart_callback()
{
  check_ajax_referer('load_cart_security', 'security');

  if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
    $produits = [];
  } else {
    $produits = $_SESSION['panier'];
  }

  $id_current = $_POST['current_item'];
  if($_POST['current_qte'] == 0):
      $qte = 1;
  else:
      $qte = $_POST['current_qte'];
  endif;

    $message = 'Votre produit a été ajouté avec success';

    $current_post = get_post($id_current);

    $prix = $current_post->prix;
    if($current_post->promo):
        $prix = $current_post->prix_promo;
    endif;

    if (!search($produits, 'produit', $id_current)) :

        $panier = array(
                'produit' => $id_current,
                'qte' => $qte,
                'prix' => $prix
        );

        array_push($produits, $panier);

    else:

        $index = array_search($id_current, array_column($produits, 'produit'));

        $produits[$index]['qte'] += $qte;

        if($produits[$index]['prix'] != $prix) $produits[$index]['prix'] = $prix;

    endif;

    $_SESSION['panier'] = $produits;

    ?>

        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-body">
          <h1 class="uk-h2 uk-text-center"><?= $message ?></h1>
        </div>
        <div class="uk-modal-footer uk-text-right" uk-margin>
          <a href="<?= get_permalink(tr_options_field('options.page_panier')) ?>" class="uk-button uk-button-theme-2 uk-button-small uk-border-rounded">Consulter le panier</a>
          <button class="uk-button uk-button-theme-3 uk-button-small uk-modal-close uk-border-rounded" type="button">Continuer la selection</button>
        </div>

    <?php

wp_die();
}
