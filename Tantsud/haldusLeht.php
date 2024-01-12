<?php
require_once ('conf.php');
session_start();
//punktide lisamine
if(isset($_REQUEST["heatants"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET punktid = punktid+1 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["heatants"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}
//punktide vähendamine
if(isset($_REQUEST["pahatants"])){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET punktid = punktid-1 WHERE id=?");
    $kask->bind_param("i", $_REQUEST["pahatants"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
}
if(isset($_REQUEST["paarinimi"]) && !empty($_REQUEST["paarinimi"]) && isAdmin()){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO tantsud (tantsupaar,ava_paev) values (?, NOW())");
    $kask->bind_param("s", $_REQUEST["paarinimi"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
    exit();
}
if(isset($_REQUEST["komment"])){
    if(isset($_REQUEST["uuskomment"]) && !empty(trim($_REQUEST["uuskomment"]))){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE tantsud SET kommentaarid=Concat(kommentaarid, ?) WHERE id=?");
    $kommentaarplus=$_REQUEST["uuskomment"]."\n";
    $kask->bind_param("si",$kommentaarplus, $_REQUEST["komment"]);
    $kask->execute();
    header("Location: $_SERVER[PHP_SELF]");
    $yhendus->close();
    exit();
    }
}
if(isset($_REQUEST["kustuta"])){
    global $yhendus;
    $kask = $yhendus->prepare("DELETE FROM tantsud WHERE id=?");
    $kask->bind_param("i", $_REQUEST["kustuta"]);
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
    <title>Haldus leht</title>
    <link rel="stylesheet" type="text/css" href="mainstyle.css">
    <script>
        function avaModalLog() {
            document.getElementById("modal_log").style.display = "flex";
        }

        function avaModalReg() {
            document.getElementById("modal_reg").style.display = "flex";
        }

        function suleModalLog() {
            document.getElementById("modal_log").style.display = "none";
        }

        function suleModalReg() {
            document.getElementById("modal_reg").style.display = "none";
        }

        window.onclick = function (event) {
            var modalLog = document.getElementById("modal_log");
            if (event.target == modalLog) {
                suleModalLog();
            }

            var modalReg = document.getElementById("modal_reg");
            if (event.target == modalReg) {
                suleModalReg();
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
    <div id="modal_reg">
        <div class="modal__window">
            <a class="modal__close" href="#"">X</a>
            <?php
            require 'register.php';
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
    <?php
    if(isset($_SESSION['kasutaja'])){
        ?>
        <!--
        <div class="open">
            <a href="#modal_reg" onclick="avaModalReg()">Registreeri</a>
        </div>
        -->
        <?php
    } else {
        ?>
        <div class="open">
            <a href="#modal_reg" onclick="avaModalReg()">Registreeri</a>
        </div>
        <?php
    }
    ?>
</header>
<nav>
    <ul>
        <?php
        if(isset($_SESSION['kasutaja']))
        {
            echo '<li><a href="haldusLeht.php">Kasutaja leht</a></li>';
            if(isAdmin()){
                echo '<li><a href="adminLeht.php">Administreerimis leht</a></li>';
        }
        }
        else{
            echo '<li id="pealeht">Sisu nägemiseks logi sisse!</li>';
        }
        ?>
    </ul>
</nav>
    <?php
    if(isset($_SESSION['kasutaja']))
    {
        echo '<h2>Punktide lisamine</h2>';
    }
    ?>
<?php
if(isset($_SESSION['kasutaja']))
{
?>
        <div style="overflow-x: auto;">
            <table>
                <tr>
                    <th>Tantsupaari nimi</th>
                    <th>Punktid</th>
                    <th>Kuupäev</th>
                    <th>Kommentaarid</th>
                    <th>Haldus</th>
                    <?php
                    if(!isAdmin()){
                        echo "<th colspan='2'>Punktid +1 / -1</th>";
                    }
                    ?>
                </tr>
                <?php
                global $yhendus;
                $kask = $yhendus->prepare("select id, tantsupaar, punktid, ava_paev, kommentaarid from tantsud where avalik = 1");
                $kask->bind_result($id, $tantsupaar,$punktid, $ava_paev, $kommentaar);
                $kask->execute();
                while($kask->fetch()){
                    echo "<tr>";
                    if(isset($_REQUEST["lisakom"]) && intval($_REQUEST["lisakom"])==$id){
                        $tantsupaar=htmlspecialchars($tantsupaar);
                        echo "<td>".$tantsupaar."</td>";
                        echo "<td>".$punktid."</td>";
                        echo "<td>".$ava_paev."</td>";
                        echo "<td>".nl2br(htmlspecialchars($kommentaar))."</td>";
                        echo "<td>
                        <form action='haldusLeht.php' id='lisakom'>
                            <input type='hidden' value='$id' name='komment'>
                            <input type='text' name='uuskomment' id='uuskomment'>                       
                            <input type='submit' value='OK'>
                        </form>
                    </td>";
                    }
                    else{
                        $tantsupaar=htmlspecialchars($tantsupaar);
                        echo "<td>".$tantsupaar."</td>";
                        echo "<td>".$punktid."</td>";
                        echo "<td>".$ava_paev."</td>";
                        echo "<td>".nl2br(htmlspecialchars($kommentaar))."</td>";
                        if(isAdmin()){
                            echo "<td><a href='?kustuta=$id'>Kustuta</a></td>";
                        }
                        if(!isAdmin()){
                            echo "<td><a href='haldusLeht.php?lisakom=$id'>Lisa kommentaar</td>";
                            echo "<td><a href='?heatants=$id'>Lisa +1 punkt</a></td>";
                            echo "<td><a href='?pahatants=$id'>Võta 1 punkt maha</a></td>";
                        }
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <?php if(isAdmin()){ ?>
            <form action="?">
            <label for="paarinimi">Lisa uus paar</label>
            <input type="text" name="paarinimi" id="paarinimi">
            <input type="submit" value="Lisa paar">
        </form>
        <?php } ?>
<?php
}
?>
</body>
</html>