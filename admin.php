<?php
session_start();

$spravne_heslo = "mock";
$config_file = 'config.json';
$zprava = '';

if (isset($_POST['heslo'])) {
    if ($_POST['heslo'] == $spravne_heslo) {
        $_SESSION['loggedin'] = true;
    } else {
        $zprava = '<p style="color: red;">Špatné heslo!</p>';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] && isset($_POST['ulozit'])) {
    $data = [
        'prijimameNovePacienty' => isset($_POST['prijimame']),
        'zobrazitOznameni' => isset($_POST['zobrazitOznameni']),
        'oznameniText' => htmlspecialchars($_POST['oznameniText']), // Změna zde
        'oteviraciDoba' => array_map('htmlspecialchars', $_POST['oteviraciDoba']),
        'cenik' => [
            'dospeli' => array_map('htmlspecialchars', $_POST['cenik']['dospeli']),
            'deti' => array_map('htmlspecialchars', $_POST['cenik']['deti'])
        ]
    ];

    if (file_put_contents($config_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        $zprava = '<p style="color: green;">Nastavení bylo úspěšně uloženo!</p>';
    } else {
        $zprava = '<p style="color: red;">Nepodařilo se uložit nastavení!</p>';
    }
}

$config = json_decode(file_get_contents($config_file));

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace webu Výborná DH</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; color: #333; display: flex; justify-content: center; padding: 2rem 0; }
        .container { background-color: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
        h1, h2 { text-align: center; color: #03590c; }
        h2 { margin-top: 2rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }
        form div { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], input[type="password"], textarea { width: 100%; padding: 0.5rem; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-family: sans-serif; }
        textarea { min-height: 80px; }
        input[type="checkbox"] { margin-right: 0.5rem; }
        button { width: 100%; padding: 0.7rem; background-color: #03590c; color: white; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; margin-top: 1rem; }
        button:hover { background-color: #004494; }
        .logout { text-align: center; margin-top: 1rem; }
        .logout a { color: #555; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Administrace webu Výborná DH</h1>
        <?= $zprava ?>

        <?php if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']): ?>
            <form method="POST">
                <div><label for="heslo">Heslo:</label><input type="password" id="heslo" name="heslo" required></div>
                <button type="submit">Přihlásit se</button>
                
            </form>
        <?php else: ?>
            <form method="POST">
                <h2>Nastavení zobrazení</h2>
                <div><input type="checkbox" id="prijimame" name="prijimame" <?php if ($config->prijimameNovePacienty) echo 'checked'; ?>><label for="prijimame">Přijímám nové pacienty</label></div>
                <hr>
                <div><input type="checkbox" id="zobrazitOznameni" name="zobrazitOznameni" <?php if ($config->zobrazitOznameni) echo 'checked'; ?>><label for="zobrazitOznameni">Zobrazit speciální oznámení (dovolená, svátky...)</label></div>
                <div>
                    <label for="oznameniText">Text oznámení:</label>
                    <textarea id="oznameniText" name="oznameniText"><?= htmlspecialchars($config->oznameniText) ?></textarea>
                </div>

                <h2>Otevírací doba</h2>
                <div><label>Pondělí:</label><input type="text" name="oteviraciDoba[po]" value="<?= htmlspecialchars($config->oteviraciDoba->po) ?>"></div>
                <div><label>Úterý:</label><input type="text" name="oteviraciDoba[ut]" value="<?= htmlspecialchars($config->oteviraciDoba->ut) ?>"></div>
                <div><label>Středa:</label><input type="text" name="oteviraciDoba[st]" value="<?= htmlspecialchars($config->oteviraciDoba->st) ?>"></div>
                <div><label>Čtvrtek:</label><input type="text" name="oteviraciDoba[ct]" value="<?= htmlspecialchars($config->oteviraciDoba->ct) ?>"></div>
                <div><label>Pátek:</label><input type="text" name="oteviraciDoba[pa]" value="<?= htmlspecialchars($config->oteviraciDoba->pa) ?>"></div>
                <div><label>Sobota:</label><input type="text" name="oteviraciDoba[so]" value="<?= htmlspecialchars($config->oteviraciDoba->so) ?>"></div>
                <div><label>Neděle:</label><input type="text" name="oteviraciDoba[ne]" value="<?= htmlspecialchars($config->oteviraciDoba->ne) ?>"></div>
                
                <h2>Ceník</h2>
                <h3>Dospělí</h3>
                <div><label>Vstupní návštěva:</label><input type="text" name="cenik[dospeli][vstupni]" value="<?= htmlspecialchars($config->cenik->dospeli->vstupni) ?>"></div>
                <div><label>Kontrolní návštěva:</label><input type="text" name="cenik[dospeli][kontrolni]" value="<?= htmlspecialchars($config->cenik->dospeli->kontrolni) ?>"></div>
                <h3>Děti</h3>
                <div><label>Děti do 6 let:</label><input type="text" name="cenik[deti][do6let]" value="<?= htmlspecialchars($config->cenik->deti->do6let) ?>"></div>
                <div><label>Děti do 15 let:</label><input type="text" name="cenik[deti][do15let]" value="<?= htmlspecialchars($config->cenik->deti->do15let) ?>"></div>
                <div><label>Zoubková prohlídka nanečisto:</label><input type="text" name="cenik[deti][nanecisto]" value="<?= htmlspecialchars($config->cenik->deti->nanecisto) ?>"></div>

                <button type="submit" name="ulozit">Uložit všechna nastavení</button>
            </form>
            <div class="logout"><a href="?logout=1">Odhlásit se</a></div>
        <?php endif; ?>
    </div>
</body>
</html>