<?php
/*
 * Tääällä kootaan yhteen kaikki pikakommentointiin liittyvät testit.
 */
require_once 'Pikakommenttitestaus.php';
require_once 'Kontrolleri_pk_testaus.php';
require_once 'Testiapu.php';

/**
 * Kutsuu kaikkia pikakommentointiin liittyviä testejä ja palauttaa 
 * Testipalaute-luokan olion.
 * @param Tietokantaolio $tietokantaolio
 * @return Testipalaute $testipalaute
 */
function toteuta_pikakommentointitestit($tietokantaolio, $parametriolio) {
    $mj = "";

    $otsikko = "Pikakommentointitestien tarkempi sis&auml;lt&ouml;";
    //=========================================================================
    $pikakommenttitestiolio = new Pikakommenttitestaus($tietokantaolio,
                                                    $parametriolio);

    $pikakommenttitestiolio->testaa();  // Suorittaa testit ja siivoukset

    $mj .= $pikakommenttitestiolio->tulosta_testikommentit();

    if ($pikakommenttitestiolio->virheilmoitusten_lkm() == 0) {
        $virheilmoitukset = "<span style='color:green'>
                            <b>Pikakommentti-luokka: Virheit&auml;
                            ei havaittu!</b></span>";
    } else {
        $virheilmoitukset = "<span style='color:red'>
                        Pikakommentti-luokka: Virheit&auml; tuli " .
                $pikakommenttitestiolio->virheilmoitusten_lkm() .
                " kpl. Alla ilmoitukset:</span>" .
                $pikakommenttitestiolio->tulosta_virheilmoitukset();
    }


    //==========================================================================
    $mj .= "<h2>Testataan Kontrolleri_pikakommentti-luokan metodeita</h12>";
    $kontrolleritestiolio = new Kontrolleri_pk_testaus($tietokantaolio,
                                                    $parametriolio);
    $kontrolleritestiolio->suorita_testit_ja_siivoa();

    if ($kontrolleritestiolio->virheilmoitusten_lkm() == 0) {
        $virheilmoitukset .= "<span style='color:green'>
                            <b><br />Kontrolleri_pikakommentti-luokka:
                            Virheit&auml; ei havaittu!</b></span>";
    } else {
        $virheilmoitukset .= "<br /><span style='color:red'>
                        Kontrolleri_pikakommentti-luokka: Virheit&auml; tuli " .
                $kontrolleritestiolio->virheilmoitusten_lkm() .
                " kpl. Alla ilmoitukset:</span><br />" .
                $kontrolleritestiolio->tulosta_virheilmoitukset();
    }

    $mj .= $kontrolleritestiolio->tulosta_testikommentit();

    $sisalto = $mj;

    return new Testipalaute($otsikko, $virheilmoitukset, $sisalto, 
                            $kontrolleritestiolio->virheilmoitusten_lkm()+
                            $pikakommenttitestiolio->virheilmoitusten_lkm());
    //==========================================================================
}

?>