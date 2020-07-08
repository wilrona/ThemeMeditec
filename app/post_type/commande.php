<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 01/05/2018
 * Time: 09:51
 */


$post_type = tr_post_type('Commande', 'Commandes');

$post_type->setIcon('shopping-basket');
$post_type->setArgument('supports', ['title']);
$post_type->setAdminOnly();

$box1 = tr_meta_box('information_commande')->setLabel('Informations du client');
$box1->addPostType( $post_type->getId() );
$box1->setCallback(function(){
    global $post;

    $info = get_post_meta($post->ID, 'cmd')[0];

    ?>

    <table class="uk-table uk-table-condensed">
        <tr>
            <td>Nom du client</td>
            <td><?= $info['client']['nom'] ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?= $info['client']['email'] ?></td>
        </tr>
        <tr>
            <td>Telephone</td>
            <td><?= $info['client']['phone'] ?></td>
        </tr>
        <tr>
            <td>Ville de residence</td>
            <td><?= $info['client']['ville'] ?></td>
        </tr>
        <tr>
            <td>Information complementaire</td>
            <td><?= $info['client']['message'] ?></td>
        </tr>
    </table>

    <?php


});

$box2 = tr_meta_box('information_client')->setLabel('Informations commande');
$box2->addPostType( $post_type->getId() );
$box2->setCallback(function(){
    global $post;

    $info = get_post_meta($post->ID, 'cmd')[0];
    $total = 0;
    ?>

    <table class="uk-table uk-table-condensed">
        <thead>
        <tr>
            <th>Nom du produit</th>
            <th>Quantite</th>
            <th>Prix unitaire</th>
            <th>Montant</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($info['panier']): ?>
            <?php foreach ($info['panier'] as $produit): ?>
                <tr>
                    <td><?= $produit['produit']->title ?></td>
                    <td><?= $produit['qte'] ?></td>
                    <td><?= $produit['prix'] ?></td>
                    <td><?= $produit['prix']*$produit['qte'] ?></td>
                </tr>
            <?php
                $total +=  $produit['prix']*$produit['qte'];
                endforeach;
            ?>
        <?php else: ?>

            <tr>
                <td colspan="4">Aucune Information</td>
            </tr>

        <?php endif; ?>

        </tbody>

        <tfoot>
        <tr>
            <th colspan="3" class="uk-text-right"> Total</th>
            <th colspan="1"><?= $total ?></th>
        </tr>
        </tfoot>
    </table>

    <?php

});


