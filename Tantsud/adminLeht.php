<?php
require_once ('conf.php');
session_start();
//punktid nulliks
if(isset($_REQUEST["punktid0"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET punktid = 0 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["punktid0"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}
//komment tühiks
if(isset($_REQUEST["komment0"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET kommentaarid = '' WHERE id=?");
    $kask->bind_param("i", $_REQUEST["komment0"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}
// peitmine
if(isset($_REQUEST["peitmine"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET avalik = 0 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["peitmine"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}
// näitmine
if(isset($_REQUEST["naitmine"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET avalik = 1 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["naitmine"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}
function isAdmin(){
    return isset($_SESSION['onAdmin']) && $_SESSION['onAdmin'] == 1;
}
?>
<!doctype html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Leht</title>
    <link rel="stylesheet" type="text/css" href="mainstyle.css">
    <script>
        function avaModalLog() {
            document.getElementById("modal_log").style.display = "flex";
        }

        function suleModalLog() {
            document.getElementById("modal_log").style.display = "none";
        }

        window.onclick = function (event) {
            var modalLog = document.getElementById("modal_log");
            if (event.target == modalLog) {
                suleModalLog();
            }
        }
    </script>
</head>
<body>
    <div id="modal_log">
        <div class="modal__window">
            <a class="modal__close" href="#">X</a>
            <?php
            require 'login.php';
            ?>
        </div>
    </div>
<header>
    <h1>Tantsud tähtedega</h1>
    <?php
    if(isset($_SESSION['kasutaja'])){
        ?>
        <h1>Tere, <?="$_SESSION[kasutaja]"?></h1>
        <a href="logout.php">Logi välja</a>
        <?php
    } else {
        ?>
        <div class="open">
            <a href="#modal_log" onclick="avaModalLog()">Logi sisse</a>
        </div>
        <?php
    }
    ?>
</header>
<nav>
    <ul>
        <li><a href="haldusLeht.php">Kasutaja leht</a></li>
        <?php
        if(isAdmin()){
            echo '<li><a href="adminLeht.php">Administreerimis leht</a></li>';
        }
        ?>
    </ul>
</nav>
<h2>Administreerimisleht</h2>
<table>
    <tr>
        <th>Tantsupaari nimi</th>
        <th>Punktid</th>
        <th>Kuupäev</th>
        <th>Kommentaarid</th>
        <th>Avalik</th>
        <th>Kustuta kommentaarid</th>
        <th>Punktid tühista</th>
        <th>Peida / Näita</th>
    </tr>
    <?php
    global $yhendus;
    $kask = $yhendus->prepare("select id, tantsupaar, punktid, ava_paev, kommentaarid, avalik from tantsud");
    $kask->bind_result($id, $tantsupaar,$punktid, $paev, $kommentaarid, $avalik);
    $kask->execute();
    while($kask->fetch()){
        $tekst="Näita";
        $seisund="naitmine";
        $tekst2="Kasutaja ei näe";
        if($avalik == 1){
            $tekst="Peida";
            $seisund="peitmine";
            $tekst2="Kasutaja näeb";
        }
        echo "<tr>";
        $tantsupaar=htmlspecialchars($tantsupaar);
        echo "<td>".$tantsupaar."</td>";
        echo "<td>".$punktid."</td>";
        echo "<td>".$paev."</td>";
        echo "<td>".$kommentaarid."</td>";
        echo "<td>".$avalik."/".$tekst2."</td>";
        echo "<td><a href='?komment0=$id'>Kommentaarid kustuta</a></td>";
        echo "<td><a href='?punktid0=$id'>Punktid nulliks</a></td>";
        echo "<td><a href='?$seisund=$id'>$tekst</a></td>";
        echo "</tr>";
    }
    ?>
</table>
</body>
</html>