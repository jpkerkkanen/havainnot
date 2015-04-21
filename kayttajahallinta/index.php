<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();    // Aloitetaan istunto.
if($_SESSION['tunnistus'] != 'kunnossa')
{
    header("Location: ../index.php");
    exit;
}
else
{
    echo "Taalla ei ole mitaan kiinnostavaa!";
}

?>
