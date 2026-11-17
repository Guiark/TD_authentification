<?php
// Formulaire de connexion des membres.
require __DIR__ . '/_commun.php';

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';

    $req = bdd()->prepare('SELECT id, login, motdepasse, role
                           FROM membres WHERE login = :login');
    $req->execute([':login' => $login]);
    $membre = $req->fetch(PDO::FETCH_ASSOC);

    // FAILLE 1 — énumération : le message dit si le login existe.
    if ($membre === false) {
        $erreur = 'Ce login n\'existe pas.';
    } elseif (!password_verify($motdepasse, $membre['motdepasse'])) {
        // FAILLE 2 — aucune limite au nombre d'essais.
        $erreur = 'Mot de passe incorrect.';
    } else {
        // FAILLE 3 — l'identifiant de session n'est pas renouvelé.
        $_SESSION['membre'] = [
            'id'    => (int) $membre['id'],
            'login' => $membre['login'],
            'role'  => $membre['role'],
        ];
        header('Location: mon_compte.php');
        exit;
    }
}

entete('Connexion');
echo '<h1>Connexion</h1>';
if ($erreur !== '') {
    echo '<p class="erreur">' . e($erreur) . '</p>';
}
echo '<form method="post" action="">'
   . '<p><label>Login <input name="login" required></label></p>'
   . '<p><label>Mot de passe <input type="password" name="motdepasse" required></label></p>'
   . '<p><button>Se connecter</button></p></form>';
pied();
