<?php

$part = tr_taxonomy('fabriquant', 'fabriquants');
$part->setHierarchical();
$part->addPostType('produit');
$part->setMainForm(function(){
    $form = tr_form();
    echo $form->image('image')->setLabel('Image du fabriquant');
    echo $form->checkbox('show_onliste')->setLabel('Afficher dans la liste des partenaires ?');
});

