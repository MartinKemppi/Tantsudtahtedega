<?php
$kasutaja = 'tarpv22';
$serverinimi = 'localhost';
$parool = '123456';
$andmebaas = 'tarpv22';
$yhendus = new mysqli($serverinimi,$kasutaja,$parool,$andmebaas);
$yhendus -> set_charset('UTF8');
?>