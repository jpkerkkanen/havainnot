<?php
    /**
     * Tämän sivun tarkoituksena on testata sivuston ja yksittäisten
     * metodien toimintaa.
     */
    require_once('testisivu.php');
    require_once('../asetukset/tietokantayhteys.php');
    require_once('../asetukset/Valtuudet.php');
    require_once('../asetukset/yleinen.php');
    require_once('../asetukset/Kielet.php');
    
    require_once('../html_tulostus.php');
    
    require_once('../php_yleinen/perustus/Ilmoitus.php');
    require_once('../php_yleinen/perustus/Pohja.php');
    require_once('../php_yleinen/perustus/Tietokantaolio.php');
    require_once('../php_yleinen/perustus/Tietokantarivi.php');
    require_once('../php_yleinen/perustus/Tietokantasolu.php');
    require_once('../php_yleinen/perustus/Kontrolleripohja.php');
    require_once('../php_yleinen/perustus/Malliluokkapohja.php');
    require_once('../php_yleinen/perustus/Nakymapohja.php');
    require_once('../php_yleinen/perustus/Perustustekstit.php');
    
    require_once('../php_yleinen/testaus_yleinen/Testialusta.php');
    require_once('../php_yleinen/testaus_yleinen/Testipalaute.php');
    require_once('../php_yleinen/testaus_yleinen/malliluokkapohjatestaus/Malliluokkapohjatesti.php');
    require_once('../php_yleinen/testaus_yleinen/malliluokkapohjatestaus/Testiolio.php');
      
    require_once('../php_yleinen/Aika.php');
    require_once('../php_yleinen/Html.php');
    require_once('../php_yleinen/Asetuspohja.php');
    require_once('../php_yleinen/Merkit.php');
    require_once('../php_yleinen/Yleismetodit.php');
    require_once('../php_yleinen/Tekstityokalupalkki.php');
    
    require_once('../kayttajahallinta/Henkilo.php');
    require_once('../kayttajahallinta/Tunnukset.php');
    require_once('../kayttajahallinta/Aktiivisuus.php');
    require_once('../kayttajahallinta/Poppoo.php');
    require_once('../kayttajahallinta/Kayttajakontrolleri.php');
    require_once('../kayttajahallinta/Kayttajanakymat.php');
    require_once('../kayttajahallinta/Kayttajatekstit.php');
    
    require_once('../kayttajahallinta/testaus/Kayttajatestaus.php');
    require_once('../kayttajahallinta/testaus/Kayttajakontrolleritestaus.php');

    require_once('../yhteiset/Palaute.php');
    require_once('../yhteiset/Parametrit.php');
   
    // Matematiikka
    /*require_once('../php_yleinen/matematiikka/Murtolukurivi.php');
    require_once('../php_yleinen/matematiikka/Murtoluku.php');
    require_once('../php_yleinen/matematiikka/Laskuri.php');
    require_once('../php_yleinen/matematiikka/Kaavaeditori.php');*/
    
    // Pikakommentit
    require_once('../pikakommentointi/Pikakommenttikontrolleri.php');
    require_once('../pikakommentointi/Pikakommenttinakymat.php');
    require_once('../pikakommentointi/Pikakommentti.php');
    require_once('../pikakommentointi/Pikakommenttitekstit.php');
    require_once('../pikakommentointi/testaus/Testiapu.php');
    require_once('../pikakommentointi/testaus/Kontrolleri_pk_testaus.php');
    require_once('../pikakommentointi/testaus/Pikakommenttitestaus.php');
    require_once('../pikakommentointi/testaus/testikooste_pikakommentit.php');
    
    // Bongaus:
    require_once('../bongaus/bongausasetukset.php');
    require_once('../bongaus/havainnot/Havainto.php');
    require_once('../bongaus/havainnot/Havaintokontrolleri.php');
    require_once('../bongaus/havainnot/Havaintonakymat.php');
    require_once('../bongaus/lajiluokat/Lajiluokka.php');
    require_once('../bongaus/lajiluokat/Kuvaus.php');
    require_once('../bongaus/lajiluokat/Kontrolleri_lj.php');
    require_once('../bongaus/lajiluokat/Nakymat_lj.php');
    require_once('../bongaus/testaus/testikooste_bongaus.php');

    // Kuvatoiminnot:
    require_once('../kuvat/Kuvatekstit.php');
    require_once('../kuvat/Kuva.php');
    require_once('../kuvat/Kuvakontrolleri.php');
    require_once('../kuvat/Kuvanakymat.php');
    require_once('../kuvat/Lajikuvalinkki.php');
    require_once('../kuvat/Havaintokuvalinkki.php');
    
    require_once('../kuvat/testaus/Testiapu_kuvat.php');
    require_once('../kuvat/testaus/Kuvatestaus.php');
    require_once('../kuvat/testaus/Kuvakontrolleritestaus.php');
    
    
    // Yhdistetään tietokantaan:
    $tietokantaolio = new Tietokantaolio($dbtyyppi, $dbhost, $dbuser, $dbsalis);
    $tietokantaolio->yhdista_tietokantaan($dbnimi);

    // Luodaan palauteolio;
    $palauteolio = new Palaute();

    // Luodaan parametriolio:
    $parametriolio = new Parametrit($tietokantaolio);

    $virheiden_lkm = 0; /* Virhetoimintojen lkm */
    // Poistetaan ensin kaikki kokonaisuudet, joiden otsikko on 'Testiotsikko':

    $ilmoitus = "Testausta";
    
    //==========================================================================
    //==========================================================================
    //
    $sisalto = "<h3>Testataan erinäisiä luokkia</h3>";
    
    $malliluokkapohjatesti = new Malliluokkapohjatesti($tietokantaolio, $parametriolio);
    $kayttajatoimintotesti = new Kayttajatestaus($tietokantaolio, $parametriolio);
    $kayttajakontrolleritestaus = new Kayttajakontrolleritestaus($tietokantaolio, $parametriolio);
    $kuvatestaus = new Kuvatestaus($tietokantaolio, $parametriolio);
    $kuvakontrolleritestaus = new Kuvakontrolleritestaus($tietokantaolio, $parametriolio);
    
    //============================== Kuvat ========================
    //$kuvatesti = new Kuvatestaus($tietokantaolio);
    //============================== Kuvat ========================
    
    $testipalauteoliot = array(
        $malliluokkapohjatesti->toteuta_malliluokkapohjatestit(),
        $kayttajatoimintotesti->toteuta_kayttajatestit(),
        $kayttajakontrolleritestaus->toteuta_testit(),
        toteuta_pikakommentointitestit($tietokantaolio,$parametriolio),
        toteuta_bongaustestit($tietokantaolio, $parametriolio),
        $kuvatestaus->toteuta_kuvatestit(),
        $kuvakontrolleritestaus->toteuta_testit()
    );
    
    $virheilmoitukset = "";
    $otsikot_ja_ilmoitukset = "";
    foreach ($testipalauteoliot as $testipalaute) {
        $virheilmoitukset .= $testipalaute->get_virheilmoitukset();
        $otsikot_ja_ilmoitukset .= "<h3 style='color: blue'>".
                                        $testipalaute->get_otsikko()."</h3>";
        $otsikot_ja_ilmoitukset .= $testipalaute->get_sisalto()."<br/>";
        $otsikot_ja_ilmoitukset .= "===============================".
                                    "================================<br/>";
        $virheiden_lkm += $testipalaute->get_virheilmoitusten_lkm();
    }
    
    $sisalto .= $virheilmoitukset.$otsikot_ja_ilmoitukset;
    //=============== Mallipohjaluokan yms. testaus loppuu =====================
    //==========================================================================

    /***************************************************************************/
    /****************************** Kokonaistulos *******************************/
    $oikea_palkki = "";
    $vasen_palkki = "Vasen palkki";
    if($virheiden_lkm == 0){
        $sisalto .= "<h2>Testauksen tulos: testaus onnistui,
                    virheit&auml; ei l&ouml;ytynyt!</h2>";
    }
    else{
        $sisalto .= "<h2 class='virhe'>Testauksen tulos: havaittiin ".$virheiden_lkm."
            virhett&auml;</h2>";
    }

    $oikea_palkki = "<b>Virheit&auml;: ".$virheiden_lkm."</b><br/>";


    echo nayta_testisivu($oikea_palkki,
                        $vasen_palkki,
                        $sisalto,
                        $ilmoitus);



?>
