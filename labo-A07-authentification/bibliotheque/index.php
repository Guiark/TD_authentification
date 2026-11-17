<?php
// index.php — accueil : catalogue des livres.
require __DIR__ . '/_commun.php';

$livres = bdd()->query('SELECT titre, auteur, annee FROM livres ORDER BY titre')
               ->fetchAll(PDO::FETCH_ASSOC);

entete('Accueil');
echo '<h1>Ma bibliothèque</h1>';
$moi = membre_connecte();
if ($moi === null) {
    echo '<p>Bienvenue ! Connectez-vous ou créez un compte.</p>';
} else {
    echo '<p class="ok">Connecté en tant que ' . e($moi['login'])
       . ' (rôle : ' . e($moi['role']) . ').</p>';
}
echo '<h2>Catalogue</h2><table><tr><th>Titre</th><th>Auteur</th><th>Année</th></tr>';
foreach ($livres as $l) {
    echo '<tr><td>' . e($l['titre']) . '</td><td>' . e($l['auteur'])
       . '</td><td>' . (int) $l['annee'] . '</td></tr>';
}
echo '</table>';
pied();
