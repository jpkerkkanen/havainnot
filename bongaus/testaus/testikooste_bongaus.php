<?php
/*
 * Täällä kootaan yhteen kaikki pikakommentointiin liittyvät testit. Alla
 * kutsutaan tämän kansion testitiedostoja (ei tartte näitä erikseen kutsua
 * myöhemmin).
 */
require_once 'Lajiluokkatestaus.php';
require_once 'Kuvaustestaus.php';
require_once 'Havaintotestaus.php';
require_once 'Kontrolleri_lj_testaus.php';
require_once '../kuvat/Havaintokuvalinkki.php';
require_once '../kuvat/Lajikuvalinkki.php';



/**
 * Kutsuu kaikkia pikakommentointiin liittyviä testejä ja palauttaa Testipalaute 
 * -luokan olion.
 * @param Tietokantaolio $tietokantaolio
 * @param Parametrit $parametriolio
 * @return \Testipalaute
 */
function toteuta_bongaustestit($tietokantaolio, $parametriolio) {
    $sisalto = "";
    $virheilmoitukset = "";
    $otsikko = "Bongaustestit alkavat";
    $virheilm_lkm = 0;
    $testausoliot = array();    // Sisältää eri testioliot:
    
    // Luodaan testausoliot taulukkoon:
    array_push($testausoliot, new Lajiluokkatestaus($tietokantaolio, $parametriolio));
    array_push($testausoliot, new Kuvaustestaus($tietokantaolio, $parametriolio));
    array_push($testausoliot, new Havaintotestaus($tietokantaolio, $parametriolio));
    
    // Tämä on kesken:
    //array_push($testausoliot, new Kontrolleri_lj_testaus($tietokantaolio));

    foreach ($testausoliot as $testiolio) {
        if($testiolio instanceof Testialusta){
            $testiolio->testaa();  // Suorittaa testit ja siivoukset
            $sisalto .= $testiolio->tulosta_testikommentit();
            $virheilmoitukset .= $testiolio->tulosta_virheilmoitukset();
            $virheilm_lkm += $testiolio->virheilmoitusten_lkm();
        }
    }
    return new Testipalaute($otsikko, $virheilmoitukset, $sisalto, $virheilm_lkm);
    //==========================================================================
}
?>