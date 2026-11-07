<?php
require_once 'Wow.php';
session_start();

if (isset($_SESSION['intentos'])) {
    Raid::$intentosTotales = $_SESSION['intentos'];
    Raid::$bossesDerrotados = $_SESSION['derrotados'];
} else {
    $_SESSION['intentos'] = 0;
    $_SESSION['derrotados'] = 0;
}

if (!isset($_SESSION['j1']) || isset($_POST['reiniciar'])) {
    Raid::NuevoIntento(); 
    $_SESSION['intentos'] = Raid::$intentosTotales;
    
    $_SESSION['j1'] = new Jugador("Linuss");
    $_SESSION['b1'] = new Boss("Illidan");
    $_SESSION['victoria_registrada'] = false;
    
    $_SESSION['log'] = "¡Comienza la pelea (Intento #" . Raid::$intentosTotales . ")! Linuss vs Illidan.";
}

$j1 = $_SESSION['j1'];
$b1 = $_SESSION['b1'];

// Usamos el atributo heredado ->Vida
if (isset($_POST['accion']) && $b1->Vida > 0 && $j1->Vida > 0) {
    $opcion = $_POST['accion'];
    ob_start(); 

    if ($opcion == "1") { $j1->AtaqueBasico($b1); } 
    elseif ($opcion == "2") { $j1->GolpeHeroico($b1); } 
    elseif ($opcion == "3") { $j1->UsarPocion(); } 
    elseif ($opcion == "4") { $j1->UsarPocionMana(); }

    if ($b1->Vida > 0) {
        echo "\n--- TURNO DEL BOSS ---\n";
        if ($b1->Vida <= 250 && $b1->Rage == false) {
            $b1->Fase2();
        }
        $danioDelBoss = $b1->Rage ? 15 : 7; 
        echo "{$b1->Nombre} ataca a {$j1->Nombre} haciéndole {$danioDelBoss} de daño.\n";
        $j1->RecibirDanio($danioDelBoss); // Ahora usamos el método heredado

        if ($j1->Vida <= 0) {
            echo "\n{$j1->Nombre} recibió un golpe mortal... WIPE.\n";
        }
    } else {
        if (!$_SESSION['victoria_registrada']) {
            Raid::BossDerrotado(); 
            $_SESSION['derrotados'] = Raid::$bossesDerrotados;
            $_SESSION['victoria_registrada'] = true;
        }
        echo "\n¡VICTORIA! {$b1->Nombre} murió después de " . Raid::$intentosTotales . " intentos.\n";
    }
    $_SESSION['log'] .= "\n-----------------------------\n" . ob_get_clean();
}
$registroBatalla = $_SESSION['log'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Simulador de Raid - POO Herencia</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #1e1e1e; color: #fff; text-align: center; padding: 20px; }
        .panel { background-color: #333; padding: 20px; border-radius: 10px; display: inline-block; width: 600px; margin-bottom: 20px;}
        .stats { font-size: 18px; margin: 10px 0; font-weight: bold;}
        .raid-stats { color: #f1c40f; font-size: 16px; margin-bottom: 15px;}
        .hp-boss { color: #ff4c4c; }
        .hp-jugador { color: #4cff4c; }
        .mana { color: #4c4cff; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; font-size: 16px; border: none; border-radius: 5px;}
        .btn-ataque { background-color: #ddd; }
        .btn-especial { background-color: #ff9900; color: white;}
        .btn-pocion { background-color: #00cc66; color: white;}
        .btn-mana { background-color: #3399ff; color: white;}
        .btn-reinicio { background-color: #ff3333; color: white;}
        pre { background-color: #000; padding: 15px; border-radius: 5px; text-align: left; font-size: 16px; height: 300px; overflow-y: auto;}
    </style>
</head>
<body>
    <h1>Batalla contra el Jefe (Con Herencia)</h1>
    <div class="panel">
        <div class="stats raid-stats">⭐ Bosses Derrotados: <?= Raid::$bossesDerrotados ?> | Intento: #<?= Raid::$intentosTotales ?> ⭐</div>
        <div class="stats hp-boss">ENEMIGO: <?= $b1->Nombre ?> - Vida: <?= $b1->Vida ?></div>
        <hr>
        <div class="stats hp-jugador">JUGADOR: <?= $j1->Nombre ?> - Vida: <?= $j1->Vida ?></div>
        <div class="stats mana">Maná: <?= $j1->Mana ?> | Pociones Vida: <?= $j1->Pociones ?> | Pociones Maná: <?= $j1->PocionesMana ?></div>
    </div>
    <div class="panel">
        <pre><?= $registroBatalla ?></pre>
    </div>
    <form method="POST">
        <?php if ($b1->Vida > 0 && $j1->Vida > 0): ?>
            <button type="submit" name="accion" value="1" class="btn-ataque">Ataque Básico</button>
            <button type="submit" name="accion" value="2" class="btn-especial">Golpe Heroico (20 Maná)</button>
            <button type="submit" name="accion" value="3" class="btn-pocion">Poción Vida</button>
            <button type="submit" name="accion" value="4" class="btn-mana">Poción Maná</button>
        <?php else: ?>
            <button type="submit" name="reiniciar" value="1" class="btn-reinicio">Reiniciar Pelea</button>
        <?php endif; ?>
    </form>
    <script> document.querySelector('pre').scrollTop = document.querySelector('pre').scrollHeight; </script>
</body>
</html>