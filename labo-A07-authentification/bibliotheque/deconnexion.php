<?php
// Déconnexion du membre.
require __DIR__ . '/_commun.php';

// FAILLE 5 — on efface seulement le cookie dans le navigateur :
// la session, elle, reste ouverte sur le serveur.
setcookie(session_name(), '', time() - 3600, '/');

header('Location: index.php');
exit;
