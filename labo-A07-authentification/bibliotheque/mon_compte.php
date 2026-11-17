<?php
// Page « Mon compte » : réservée aux membres connectés.
require __DIR__ . '/_commun.php';
exiger_connexion();
$moi = membre_connecte();

entete('Mon compte');
echo '<h1>Mon compte</h1>';
echo '<p class="ok">Vous êtes connecté en tant que ' . e($moi['login'])
   . ' (rôle : ' . e($moi['role']) . ').</p>';
echo '<p>Bienvenue dans votre espace de lecteur.</p>';
pied();
