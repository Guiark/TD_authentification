<?php
// init_base.php — (re)construit la base SQLite du labo « Ma bibliothèque ».
// À exécuter avant de démarrer le labo, ou pour tout remettre à zéro.
// Usage : php init_base.php

$chemin = __DIR__ . '/biblio.db';
$pdo = new PDO('sqlite:' . $chemin);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// On repart d'une base propre à chaque exécution.
$pdo->exec('DROP TABLE IF EXISTS livres');
$pdo->exec('DROP TABLE IF EXISTS membres');

$pdo->exec('CREATE TABLE livres (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    titre   TEXT NOT NULL,
    auteur  TEXT NOT NULL,
    annee   INTEGER
)');

// tentatives et bloque_jusqu_a : prévues pour limiter les essais de connexion
// (bloque_jusqu_a = horodatage Unix de fin de blocage, ou NULL).
$pdo->exec('CREATE TABLE membres (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    login          TEXT NOT NULL UNIQUE,
    motdepasse     TEXT NOT NULL,
    role           TEXT NOT NULL DEFAULT \'membre\',
    tentatives     INTEGER NOT NULL DEFAULT 0,
    bloque_jusqu_a INTEGER
)');

$livres = [
    ['Le Comte de Monte-Cristo', 'Alexandre Dumas', 1844],
    ['Notre-Dame de Paris',      'Victor Hugo',     1831],
    ['Vingt mille lieues',       'Jules Verne',     1870],
    ['Germinal',                 'Émile Zola',      1885],
    ['Le Tour du monde',         'Jules Verne',     1873],
];
$ins = $pdo->prepare('INSERT INTO livres (titre, auteur, annee) VALUES (?, ?, ?)');
foreach ($livres as $l) {
    $ins->execute($l);
}

// Mots de passe stockés avec password_hash() (vu au TP Injection SQL & XSS).
$membres = [
    ['alice', 'secret123',        'membre'],
    ['bob',   'chocolat',         'membre'],
    ['admin', 'MotDePasseAdmin!', 'admin'],
];
$insM = $pdo->prepare('INSERT INTO membres (login, motdepasse, role) VALUES (?, ?, ?)');
foreach ($membres as $m) {
    $insM->execute([$m[0], password_hash($m[1], PASSWORD_DEFAULT), $m[2]]);
}

echo 'Base reconstruite : ' . $chemin . PHP_EOL;
echo count($livres) . ' livres, ' . count($membres) . ' membres.' . PHP_EOL;
