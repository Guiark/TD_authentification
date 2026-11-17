<?php
// _commun.php — fonctions partagées par toutes les pages du labo.
// Chaque page commence par : require __DIR__ . '/_commun.php';

// Cookie de session protégé (vu au TP Injection SQL & XSS).
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// Connexion unique à la base SQLite (créée par init_base.php).
function bdd(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . __DIR__ . '/biblio.db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}

// Échappement HTML de toute donnée affichée (vu au TP Injection SQL & XSS).
function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

// Renvoie le membre connecté (tableau id, login, role) ou null.
function membre_connecte(): ?array
{
    return $_SESSION['membre'] ?? null;
}

// Exige d'être connecté : sinon, renvoie vers la page de connexion.
function exiger_connexion(): void
{
    if (membre_connecte() === null) {
        header('Location: connexion.php');
        exit;
    }
}

// En-tête HTML et menu. Le menu s'adapte au membre connecté.
function entete(string $titre): void
{
    $moi = membre_connecte();
    echo '<!doctype html><html lang="fr"><head><meta charset="utf-8">';
    echo '<title>' . e($titre) . ' — Ma bibliothèque</title>';
    echo '<style>body{font-family:sans-serif;max-width:860px;margin:2rem auto;padding:0 1rem;color:#333}'
       . 'nav{background:#5F9E8E;padding:.6rem 1rem;border-radius:6px}'
       . 'nav a{color:#fff;margin-right:1rem;text-decoration:none;font-weight:bold}'
       . 'h1{color:#5F9E8E}table{border-collapse:collapse;margin:1rem 0}'
       . 'td,th{border:1px solid #9ECFBF;padding:4px 10px}th{background:#E8F5F0}'
       . '.erreur{color:#C00000;font-weight:bold}.ok{color:#2A6E2A;font-weight:bold}'
       . 'form{margin:1rem 0}input,select{margin:.2rem}</style></head><body><nav>';
    echo '<a href="index.php">Accueil</a>';
    if ($moi === null) {
        echo '<a href="connexion.php">Connexion</a>';
        echo '<a href="inscription.php">Créer un compte</a>';
    } else {
        echo '<a href="mon_compte.php">Mon compte</a>';
        echo '<a href="deconnexion.php">Déconnexion (' . e($moi['login']) . ')</a>';
    }
    echo '</nav><main>';
}

// Pied de page. Pour le labo UNIQUEMENT, on y affiche l'identifiant de
// session afin de l'observer (à ne jamais faire sur un vrai site).
function pied(): void
{
    echo '</main><hr><footer><small>Identifiant de session (labo) : <code>'
       . e(session_id()) . '</code></small></footer></body></html>';
}
