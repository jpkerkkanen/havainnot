<?php
/*
 * Tämä on vain sitä varten, ettei kukaan näe tiedostolistausta suoraan
 * selaimelle.
 */
session_start();    // Aloitetaan istunto.
if($_SESSION['tunnistus'] != 'kunnossa')
{
    header("Location: ../tunnistus.php?piip=".time());
    exit;
}
else
{
    echo "Taalla ei ole mitaan kiinnostavaa!";
}

?>
