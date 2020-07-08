<?php

$post_type = tr_post_type('Produit', 'Produits');

$post_type->setIcon('shopping-bag');
$post_type->setArgument('supports', ['title', 'thumbnail', 'editor']);
$post_type->removeColumn('date');
$post_type->addColumn('prix', true, 'Prix', function($value){
    echo $value;
});
$post_type->addColumn('prix_promo', true, 'Prix promo', function($value){
    echo $value;
});

$post_type->addColumn('promo', true, 'Promo en cours ?', function($value){
    echo $value ? 'OUI' : 'NON';
});
//$post_type->addColumn('date');

$box1 = tr_meta_box('information')->setLabel('Informations complementaires');
$box1->addPostType( $post_type->getId() );
$box1->setCallback(function(){

    $form = tr_form();

    echo $form->text('reference')->setLabel('Reference du produit');
    echo $form->editor('breve')->setLabel('Breve description');

});

$box2 = tr_meta_box('pricing')->setLabel('Informations sur le prix');
$box2->addPostType( $post_type->getId() );
$box2->setCallback(function(){

    $form = tr_form();
    echo $form->text('prix')->setLabel('Montant du produit')->setType('number')->setAttribute('value', tr_posts_field('prix') ? tr_posts_field('prix') : 0);
    echo $form->text('prix_promo')->setLabel('Montant promotionnel')->setType('number')->setAttribute('value', tr_posts_field('prix_promo') ? tr_posts_field('prix_promo') : 0);
    echo $form->toggle('promo')->setLabel('Activer la promotion ?');

});


$box3 = tr_meta_box('galery')->setLabel('Image du produit');
$box3->addPostType( $post_type->getId() );
$box3->setCallback(function(){

    $form = tr_form();
    echo $form->gallery('image')->setLabel('Image du produit');
});