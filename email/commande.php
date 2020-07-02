<html>
<body>

Bonjour Administrateur <br>

Vous avez reçue une nouvelle commande du site Meditec SARL. <br>

Voici les informations du client

<br><br>

<ul>
    <li><b>Nom</b> : <?= $data['nom'] ?></li>
    <li><b>Ville</b> : <?= $data['ville'] ?></li>
    <li><b>Email</b> : <?= $data['email'] ?></li>
    <li><b>Telephone</b> : <?= $data['phone'] ?></li>
    <li><b>Information complementaire</b> : <?= $data['message'] ?></li>
</ul>

<br><br>

<h3>Produit de la commande</h3>

<table>
    <thead>
    <tr>
        <th>Nom du produit</th>
        <th>Quantite</th>
        <th>Prix unitaire</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($produits as $produit): ?>
            <tr>
                <td><?= $produit['produit']->title ?></td>
                <td><?= $produit['qte'] ?></td>
                <td><?= $produit['prix'] ?></td>
                <td><?= $produit['prix']*$produit['qte'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>

<br><br>
Pour un total de <b><?= $total ?></b>

<p>
    Veuillez entrer en contact avec ce client dans les brefs delais.
</p>


</body>
</html>





