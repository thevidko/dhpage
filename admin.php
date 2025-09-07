<?php
session_start();

// Mock pass
$spravne_heslo = "mock";

$config_file = 'config.json';
$zprava = '';

// Zpracování přihlášení
if (isset($_POST['heslo'])) {
    if ($_POST['heslo'] == $spravne_heslo) {
        $_SESSION['loggedin'] = true;
    } else {
        $zprava = '<p style="color: red;">Špatné heslo!</p>';
    }
}

// Zpracování odhlášení
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Zpracování uložení formuláře
if ($_SESSION['loggedin'] && isset($_POST['ulozit'])) {
    $data = [
        'prijimameNovePacienty' => isset($_POST['prijimame']),
        'jeDovolena' => isset($_POST['dovolena']),
        'dovolenáOd' => htmlspecialchars($_POST['dovolenaOd']),
        'dovolenáDo' => htmlspecialchars($_POST['dovolenaDo'])
    ];

    // Uloží data do JSON souboru s formátováním pro lepší čitelnost
    if (file_put_contents($config_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        $zprava = '<p style="color: green;">Nastavení bylo úspěšně uloženo!</p>';
    } else {
        $zprava = '<p style="color: red;">Nepodařilo se uložit nastavení. Zkontrolujte oprávnění souboru config.json.</p>';
    }
}

// Načtení aktuální konfigurace pro zobrazení ve formuláři
$config = json_decode(file_get_contents($config_file));

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace webu</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; color: #333; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background-color: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { text-align: center; color: #0056b3; }
        form div { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 0.5rem; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        input[type="checkbox"] { margin-right: 0.5rem; }
        button { width: 100%; padding: 0.7rem; background-color: #0056b3; color: white; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; }
        button:hover { background-color: #004494; }
        .logout { text-align: center; margin-top: 1rem; }
        .logout a { color: #555; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Administrace webu</h1>
        <?= $zprava ?>

        <?php if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']): ?>
            <form method="POST">
                <div>
                    <label for="heslo">Heslo:</label>
                    <input type="password" id="heslo" name="heslo" required>
                </div>
                <button type="submit">Přihlásit se</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <div>
                    <input type="checkbox" id="prijimame" name="prijimame" <?php if ($config->prijimameNovePacienty) echo 'checked'; ?>>
                    <label for="prijimame">Přijímám nové pacienty</label>
                </div>
                <hr>
                <div>
                    <input type="checkbox" id="dovolena" name="dovolena" <?php if ($config->jeDovolena) echo 'checked'; ?>>
                    <label for="dovolena">Zobrazit informaci o dovolené</label>
                </div>
                <div>
                    <label for="dovolenaOd">Dovolená od:</label>
                    <input type="text" id="dovolenaOd" name="dovolenaOd" value="<?= htmlspecialchars($config->dovolenáOd) ?>">
                </div>
                <div>
                    <label for="dovolenaDo">Dovolená do:</label>
                    <input type="text" id="dovolenaDo" name="dovolenaDo" value="<?= htmlspecialchars($config->dovolenáDo) ?>">
                </div>
                <button type="submit" name="ulozit">Uložit nastavení</button>
            </form>
            <div class="logout">
                <a href="?logout=1">Odhlásit se</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>