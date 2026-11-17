<?php
// Création d'un compte membre.
require __DIR__ . '/_commun.php';

$erreur = '';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    $req = bdd()->prepare('SELECT COUNT(*) FROM membres WHERE login = :login');
    $req->execute([':login' => $login]);
    $existe = (int) $req->fetchColumn() > 0;

    if ($login === '' || $motdepasse === '') {
        $erreur = 'Le login et le mot de passe sont obligatoires.';
    } elseif ($existe) {
        $erreur = 'Ce login est déjà pris.';
    } elseif ($motdepasse !== $confirmation) {
        $erreur = 'Les deux mots de passe ne correspondent pas.';
    // FAILLE 4 — aucune règle sur le mot de passe :
    // « 1 » ou « azerty » sont acceptés.
    } else {
        $req = bdd()->prepare('INSERT INTO membres (login, motdepasse) VALUES (:login, :mdp)');
        $req->execute([':login' => $login, ':mdp' => password_hash($motdepasse, PASSWORD_DEFAULT)]);
        $message = 'Compte créé : vous pouvez maintenant vous connecter.';
    }
}

entete('Créer un compte');
echo '<h1>Créer un compte</h1>';
if ($erreur !== '') {
    echo '<p class="erreur">' . e($erreur) . '</p>';
}
if ($message !== '') {
    echo '<p class="ok">' . e($message) . '</p>';
}
echo '<form method="post" action="">'
   . '<p><label>Login <input name="login" required></label></p>'
   . '<p><label>Mot de passe <input type="password" name="motdepasse" required></label></p>'
   . '<p><label>Confirmation <input type="password" name="confirmation" required></label></p>'
   . '<p><button>Créer le compte</button></p></form>';
pied();
