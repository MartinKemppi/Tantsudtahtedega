<?php
//AB kasutaja, serverrinimi, salasõna, AB nimi -> ühendame seda andtud väärtusega, lisame tähte koodering
$kasutaja = 'd123177_martinke';
$serverinimi = 'd123177.mysql.zonevs.eu';
$parool = 'dusperin1234';
$andmebaas = 'd123177_andmebaas';
$yhendus = new mysqli($serverinimi,$kasutaja,$parool,$andmebaas);
$yhendus -> set_charset('UTF8');
?>