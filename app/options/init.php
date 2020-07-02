<?php
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// Setup Form
$form = tr_form()->useJson()->setGroup($this->getName());
?>

<h1>Theme Options</h1>
<div class="typerocket-container">
    <?php
    echo $form->open();

    $company_infos = function () use ($form) {

        echo '<h2 class="uk-padding-remove-bottom uk-text-center uk-margin-top">Information de l\'entreprise</h2>';

        echo $form->image('icon')->setLabel('Icone du site')->setHelp('Visible dans le coin de l\'onglet du navigateur')->setSetting('button', 'Inserer l\'icone');
        echo $form->image('logo')->setLabel('Logo du site web')->setSetting('button', 'Inserer le logo');
        echo $form->image('header')->setLabel('Image Fond header')->setSetting('button', 'Inserer une image de fond');
        echo $form->image('shop_home')->setLabel('Image Fond shop home')->setSetting('button', 'Inserer une image');

        echo '<hr >';

        echo $form->text('lienfacebook')->setLabel('Lien facebook');
        echo $form->text('lienlinkedin')->setLabel('Lien linkedin');
        echo $form->text('lienwhatsapp')->setLabel('Lien whatsapp');
        echo $form->text('lienyoutube')->setLabel('Lien youtube');
        echo $form->text('lientwitter')->setLabel('Lien twitter');
        echo $form->text('lieninstagram')->setLabel('Lien instagram');

        echo '<hr >';

        echo $form->editor('message_contact')->setLabel('Message du block de contact');
        echo $form->text('adresse_company')->setLabel('Adresse de l\'entreprise');
        echo $form->text('numero_company')->setLabel('Numero de telephone');
        echo $form->text('email_company')->setLabel('Adresse Email');

        echo '<hr >';

        echo $form->text('form_devis')->setLabel('Shortcode Formulaire de devis');
        echo $form->text('carte_map')->setLabel('Shortcode Carte google');
        echo $form->text('carte_marker')->setLabel('Shortcode Marker');


    };

    $config_page = function () use ($form){
        
        echo '<h2 class="uk-padding-remove-bottom uk-text-center uk-margin-top">Configuration des pages</h2>';

        echo $form->search('page_panier')->setLabel('Page panier')->setPostType('page');

    };

    $config_ville = function () use ($form){

        echo '<h2 class="uk-padding-remove-bottom uk-text-center uk-margin-top">Configuration des villes de livraison</h2>';

        echo $form->repeater('vill_config')->setLabel('Liste des villes')->setFields([
                $form->text('name')->setLabel('Nom de la ville')
        ]);
    };




    // Save
    $save = $form->submit('Enregistrement');

    // Layout
    tr_tabs()->setSidebar($save)
        ->addTab('Entreprise', $company_infos)
        ->addTab('Pages', $config_page)
        ->addTab('Villes', $config_ville)
        ->render();
    echo $form->close();
    ?>

</div>