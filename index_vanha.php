<?php
session_start();    // Aloitetaan istunto.
require_once('../asetukset/valtuudet.php');
require_once('../asetukset/yleinen.php');

if(!isset($_SESSION['tunnistus']) || $_SESSION['tunnistus'] != 'kunnossa')
{
    header("Location: ../tunnistus.php?piip=".time());
    exit;
}
else if($_SESSION['tiedot']->valtuudet == Valtuudet::$RAJOITETTU_KUVIEN_KATSELU){
    header("Location: ../kuvatoiminnot/kuvat.php");
    exit;
}

// Tarkistetaan istunnon laiskan ajan kesto ja kirjataan laiska ulos:
else if(isset($_SESSION['viim_aktiivisuus']) &&
    ((time()-$_SESSION['viim_aktiivisuus']) > Aikarajat::$LAISKA_ISTUNTOAIKA)){

    require_once('../kayttajahallinta/php_kayttajahallintametodit.php');

    $kansiotaso = 2;    // Toisella tasolla.
    toteuta_passiivinen_ulos_toiminto($kansiotaso);
    header("Location: ../tunnistus.php?piip=".time());
}

else
{
    require_once('../php_yleinen/php_yleismetodit.php');
    require_once('../php_yleinen/aika.php');
    require_once('../php_yleinen/Tietokantaolio.php');
    require_once('../php_yleinen/html.php');
    require_once('../php_yleinen/Ilmoitus.php');
    require_once('../php_yleinen/Ilmoitusrajapinta.php');
    require_once('../php_yleinen/Malliluokkapohja.php');
    require_once('../php_yleinen/Kuva.php');
    
    require_once('../php_yleinen/Kontrolleripohja.php');
    require_once('../php_yleinen/Nakymapohja.php');
    require_once('../asetukset/tietokantayhteys.php');
    require_once('../asetukset/Kielet.php');
    require_once('../liikuntamuistio/muistioasetukset.php');
    require_once('../kayttajahallinta/php_kayttajahallintametodit.php');
    require_once('../yhteiset/php_yhteiset.php');
    require_once('../yhteiset/Palaute.php');
    require_once('../yhteiset/Parametrit.php');
    require_once('../kuvatoiminnot/php_kuvametodit.php');
    require_once('../viestit/Viesti.php');

    require_once('../pikakommentointi/Pikakommenttitekstit.php');
    require_once('../pikakommentointi/Pikakommentti.php');
    require_once('../pikakommentointi/Kontrolleri_pikakommentit.php');

    require_once('bongausasetukset.php');
    
    require_once('html_tulostus.php');
    require_once('lajiluokat/Kontrolleri_lj.php');
    require_once('lajiluokat/Kuvaus.php');
    require_once('lajiluokat/Lajiluokka.php');
    require_once('lajiluokat/Nakymat_lj.php');
    require_once('havainnot/Havainto.php');
    require_once('havainnot/Havaintokontrolleri.php');
    require_once('havainnot/Havaintonakymat.php');



    $kirjautumistieto = '';
    if (isset($_SESSION['tiedot']))
    {
        $kirjautumistieto =
        "Kirjautunut: ".$_SESSION['tiedot']->etunimi;
    }

    $aika = anna_nyk_viikonp_suomeksi()." ".date("d.m.Y");
    $aikailmoitus = "T&auml;n&auml;&auml;n on $aika";

    // Yhdistetään tietokantaan:
    $tietokantaolio = new Tietokantaolio($dbtyyppi, $dbhost, $dbuser, $dbsalis);
    $tietokantaolio->yhdista_tietokantaan($dbnimi);
    $omaid = $_SESSION['tiedot']->id;

    // Tarkistetaan, ettei käyttäjää ole potkaistu ulos:
    if(!online($omaid, $tietokantaolio)){
        $kansiotaso = 2;
        toteuta_kirjaudu_ulos($tietokantaolio, $dbnimi, $kansiotaso);
        exit;
    }

    // Verkkosivujen hallitsijan totuusarvo:
    $omat_valtuudet = $_SESSION['tiedot']->valtuudet;
    $kuningas = on_kuningas_pika($omat_valtuudet);

    $etun = isset($_POST['etunimi']) ? $_POST['etunimi']: "";


    // Luodaan uusi palauteolio:************************************************
    $palauteolio = new Palaute();

    // Haetaan parametrit yhteen olioon:
    $kokoelmanimi = Kuva::$KUVAT_BONGAUS;
    $parametriolio = new Parametrit($kokoelmanimi,$omaid,$tietokantaolio);
    
    // Luodaan Havaintokontrolleri- ja Havaintonakymaluokan oliot:
    $havaintokontrolleri = new Havaintokontrolleri($tietokantaolio, $parametriolio);
    $havaintonakymat = new Havaintonakymat($tietokantaolio, $parametriolio);
    
    // Samoin lajiluokan jutut:
    $lajiluokkakontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);
    $lajiluokkanakymat = new Nakymat_lj();
    /**************************************************************************/

    
    // Yleisten toimintomuuttujien arvojen haku/alustus:
    $perustoiminto = isset($_REQUEST[Bongaustoimintonimet::$perustoiminto]) ?
                        $_REQUEST[Bongaustoimintonimet::$perustoiminto]: "";

    $havaintotoiminto = isset($_REQUEST[Bongaustoimintonimet::$havaintotoiminto]) ?
                        $_REQUEST[Bongaustoimintonimet::$havaintotoiminto]: "";

    $lajiluokkatoiminto = isset($_REQUEST[Bongaustoimintonimet::$lajiluokkatoiminto]) ?
                        $_REQUEST[Bongaustoimintonimet::$lajiluokkatoiminto]: "";

    $kuvatoiminto = isset($_REQUEST[Bongaustoimintonimet::$kuvatoiminto]) ?
                        $_REQUEST[Bongaustoimintonimet::$kuvatoiminto]: "";

    $yllapitotoiminto = isset($_REQUEST[Bongaustoimintonimet::$yllapitotoiminto]) ?
                        $_REQUEST[Bongaustoimintonimet::$yllapitotoiminto]: "";

    // Tekstien käännös.
    Kielet::kaanna($parametriolio->kieli_id);


    // Poistumisnappi
    $id=Yleisarvoja::$ulosnappiID;
    $name = Bongaustoimintonimet::$perustoiminto;
    $value = Bongauspainikkeet::$KIRJAUDU_ULOS_VALUE;
    $ulosnappi = luo_uloskirjauspainike($id, $name, $value);
    

    // Sivun pääsisältö:
    $sisalto = "";
    $tiedot = $kirjautumistieto.". ".$aikailmoitus;

    // Käyttäjälle tuleva huomautusviesti:
    $ilmoitus = "";
    $kielivalikko = "";     // Ei vielä mitään.


    // Toimintojen toteutukset (ainakin metodikutsut, jos toiminto pitkä)
    if($perustoiminto != ""){
        if($perustoiminto == Bongauspainikkeet::$KIRJAUDU_ULOS_VALUE){
            $kansiotaso = 2;
            toteuta_kirjaudu_ulos($tietokantaolio, $dbnimi, $kansiotaso);
        }
    }
    else if($havaintotoiminto != ""){
        if($havaintotoiminto == Bongauspainikkeet::$KATSO_HAVAINTO_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if(($havaintotoiminto == Bongauspainikkeet::$UUSI_HAVAINTO_VALUE)){

            $palauteolio = $havaintokontrolleri->toteuta_nayta_yksi_uusi_lomake();
        }
        
        else if(($havaintotoiminto == 
                        Bongauspainikkeet::$NAYTA_MONEN_HAVAINNON_VALINTA_VALUE)){

            $palauteolio = $havaintokontrolleri->
                                        toteuta_nayta_moniuusitallennuslomake();
        }
        
        else if(($havaintotoiminto == 
                        Bongauspainikkeet::$TALLENNA_MONTA_HAV_KERRALLA_VALUE)){

            $palauteolio = $havaintokontrolleri->toteuta_tallenna_monta_uutta();
        }
        
        else if($havaintotoiminto == Bongauspainikkeet::$TALLENNA_UUSI_HAVAINTO_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_tallenna_uusi();
        }
        else if($havaintotoiminto == Bongauspainikkeet::$PERUMINEN_HAVAINTO_VALUE){
            $ilmoitus = Bongaustekstit::$ilm_havainnon_lisays_tai_muokkaus_peruttu;
            
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            $palauteolio->set_ilmoitus($ilmoitus);
        }
    
        else if($havaintotoiminto ==
                        Bongauspainikkeet::$TALLENNA_MUOKKAUS_HAVAINTO_VALUE){
           
            $palauteolio = $havaintokontrolleri->toteuta_tallenna_muokkaus();
        }
        else if($havaintotoiminto == Bongauspainikkeet::$POISTA_HAVAINTO_VALUE){
            $ilmoitus = "";
            $sisalto = nayta_poistovarmistus_hav($parametriolio->id_hav);
        }
        else if($havaintotoiminto == Bongauspainikkeet::$POISTOVAHVISTUS_HAVAINTO_VALUE){
            $palauteolio = toteuta_poisto_hav($parametriolio);
        }
        else if($havaintotoiminto == Bongauspainikkeet::$PERU_POISTO_HAVAINTO_VALUE){
            $ilmoitus = Bongaustekstit::$ilm_havainnon_poisto_peruttu;
            //$sisalto = hae_havainnot($parametriolio);
            
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($havaintotoiminto == Bongauspainikkeet::$TAKAISIN_HAVAINTOIHIN_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($havaintotoiminto ==
                        Bongauspainikkeet:: $HAVAINNOT_VALITSE_LAJILUOKKA_VALUE){
            //$sisalto = hae_havainnot($parametriolio);
            
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
        }
        
        else if($havaintotoiminto == 
                        Bongauspainikkeet::$HAVAINNOT_MONIKOPIOI_ITSELLE_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_kopioi_itselle();
        }
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_NAYTA_MONIMUOKKAUSLOMAKE_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_nayta_monimuokkauslomake();
        }
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_TALLENNA_VALITTUJEN_MUOKKAUS_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_tallenna_muokkaus();
        }
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_POISTA_VALITUT_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_nayta_poistovarmistus();
        }
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_MONIPOISTOVAHVISTUS_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_poista();
        }
        
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_MONIPOISTON_PERUMINEN_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
        }
  
        //===========================================================================
    }
    else if($lajiluokkatoiminto != ""){
        if($lajiluokkatoiminto == Bongauspainikkeet::$KATSO_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if($lajiluokkatoiminto == Bongauspainikkeet::$UUSI_LAJILUOKKA_VALUE){

            $palauteolio = $lajiluokkakontrolleri->toteuta_nayta_lajiluokkalomake();
        }
        else if($lajiluokkatoiminto == 
                            Bongauspainikkeet::$TALLENNA_UUSI_LAJILUOKKA_VALUE){
            $palauteolio = $lajiluokkakontrolleri->toteuta_tallenna_uusi();
        }
        else if($lajiluokkatoiminto ==
                                Bongauspainikkeet::$PERUMINEN_LAJILUOKKA_VALUE){
            $ilmoitus = Bongaustekstit::$ilm_lajiluokka_peruminen;
            //$sisalto = hae_havainnot($parametriolio);
            
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($lajiluokkatoiminto ==
                                Bongauspainikkeet::$MUOKKAA_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if($lajiluokkatoiminto ==
                        Bongauspainikkeet::$TALLENNA_MUOKKAUS_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if($lajiluokkatoiminto ==
                                    Bongauspainikkeet::$POISTA_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if($lajiluokkatoiminto ==
                            Bongauspainikkeet::$POISTOVAHVISTUS_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if($lajiluokkatoiminto ==
                                Bongauspainikkeet::$PERU_POISTO_LAJILUOKKA_VALUE){
            $ilmoitus = "HUU";
            //$sisalto = hae_havainnot($parametriolio);
            
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($lajiluokkatoiminto == Bongauspainikkeet::$TAKAISIN_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            $palauteolio->set_ilmoitus($ilmoitus);
        }

    }
    else if($kuvatoiminto != ""){
        if($kuvatoiminto == Bongauspainikkeet::$UUSI_KUVA_VALUE){
            $parametriolio->set_uusi_kuva(true);
            $parametriolio->set_naytettavan_id_hav($parametriolio->id_hav);
            $palauteolio = toteuta_nayta_kuvalomake($parametriolio);
            $palauteolio->set_oikean_palkin_naytto(false);
        }
        else if($kuvatoiminto == Bongauspainikkeet::$TALLENNA_UUSI_KUVA_VALUE){
            $parametriolio->kokoelmanimi == Kuva::$KUVAT_BONGAUS;
            $palauteolio = toteuta_kuvan_tallennus($parametriolio);
        }
        else if($kuvatoiminto == Bongauspainikkeet::
                                    $HAVAINNOT_LISAA_KUVA_VALITTUIHIN_VALUE){
            $palauteolio = $havaintokontrolleri->
                                        toteuta_nayta_kuvalomake_havaintoihin();
        }
        else if($kuvatoiminto == Bongauspainikkeet::$PERUMINEN_KUVA_VALUE){
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            $palauteolio->set_oikean_palkin_naytto(true);
        }
        else if($kuvatoiminto == Kuva::$peruminen_kuva_value){
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            $palauteolio->set_oikean_palkin_naytto(true);
        }
        
        else if($kuvatoiminto == Bongauspainikkeet::$MUOKKAA_KUVA_VALUE){
            $parametriolio->set_uusi_kuva(false);   // Vanha kuva
            $parametriolio->set_naytettavan_id_hav($parametriolio->id_hav);
            $palauteolio = toteuta_nayta_kuvalomake($parametriolio);
            $palauteolio->set_oikean_palkin_naytto(true);
        }
        else if($kuvatoiminto == Bongauspainikkeet::$TALLENNA_MUOKKAUS_KUVA_VALUE){
            $palauteolio = toteuta_tallenna_kuvamuutokset($parametriolio);
            $palauteolio->set_oikean_palkin_naytto(true);
        }
        else if($kuvatoiminto == Bongauspainikkeet::$POISTA_KUVA_VALUE){
            $palauteolio = toteuta_nayta_kuvan_poistovarmistus($parametriolio);
            $palauteolio->set_oikean_palkin_naytto(true);
        }
        else if($kuvatoiminto == Bongauspainikkeet::$POISTOVAHVISTUS_KUVA_VALUE){
            $palauteolio = toteuta_kuvan_poisto($parametriolio);
            $palauteolio->set_oikean_palkin_naytto(true);
        }
        else if($kuvatoiminto == Bongauspainikkeet::$PERU_POISTO_KUVA_VALUE){
            $palauteolio = toteuta_kuvan_poiston_peruminen($parametriolio);
        }
        else if($kuvatoiminto == Bongauspainikkeet::$NAYTA_KUVA_ALBUMIT_VALUE){
            $palauteolio = toteuta_bongausalbumeiden_naytto($parametriolio);
        }
        else if($kuvatoiminto == Bongauspainikkeet::$NAYTA_KUVA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if($kuvatoiminto == Bongauspainikkeet::$NAYTA_ESIKATSELUKUVAT_VALUE){
            $palauteolio = toteuta_nayta_esikatselukuvat($parametriolio);
            $palauteolio->set_oikean_palkin_naytto(true);
        }
        else if($kuvatoiminto == Bongauspainikkeet::$KAYNNISTA_DIAESITYS_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        
        /*****************  korjaus 3.2011 *********************************/
        /*else if($kuvatoiminto == "Luo pikkukuvat"){
            $ilmoitus = luo_bongauspikkukuvat_muutos_maalis2011($tietokantaolio);
            $sisalto = hae_havainnot($parametriolio);
        }*/
        /***************** korjaus 3.2011 loppu *******************************/
    }
    else if($yllapitotoiminto != ""){

    }

    // Oletustoimintona näytetään viimeiset havainnot. Jos yläluokkaa ei ole
    // määritelty, näytetään kaikki luokat.
    else{
        if($parametriolio->ylaluokka_id_lj == -1){ // Tilanne sivulle tullessa 1. kertaa.
            //$sisalto = hae_havainnot($parametriolio);
            
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            //$palauteolio->set_ilmoitus($ilmoitus);
        }
        else{
            //$sisalto = hae_havainnot($parametriolio);
            
            $palauteolio = $havaintokontrolleri->toteuta_nayta();
            //$palauteolio->set_ilmoitus($ilmoitus);
        }
    }

    // Uusien yleisten viestien tarkistus (jos mennään viesteihin);
    $sisaankirjautumisaika = $_SESSION['kirjautumisaika'];
    $teema = 1; // Yleinen-luokan tunnus.
    $uusien_yleisten_lkm =
                hae_uusien_viestien_lkm($parametriolio->omaid,
                                        $teema,
                                        $parametriolio->tietokantaolio,
                                        $sisaankirjautumisaika);

    // Linkit:
    $href = "../index.php?uusien_yl_lkm=".$uusien_yleisten_lkm;
    $linkit = "";

    // Hallinta ja peruskäyttäjä:
    if($_SESSION['tiedot']->valtuudet < Valtuudet::$RAJOITETTU){
        $linkit .= "<a href=$href>Etusivu (keskustelu)</a>";
        $linkit .= "<a href='../kuvatoiminnot/kuvat.php'>Kuva-albumit</a>";
        $linkit .= "<a href='../liikuntamuistio/muistio.php'>Liikuntamuistio</a>";
        $linkit .= $ulosnappi;

        $linkit .= "<br />";
        $linkit .= "<b>Arkisto</b>";
        $linkit .= Havaintonakymat::nayta_arkistolinkit();
    }

    // Kun rajoitus on päällä:
    else{
        echo $ulosnappi;
    }
}

// Haetaan jakson nro ja havaintomäärät:
$nyk_vuosi = anna_nyk_vuoden_nro();
$nyk_kk = anna_nyk_kk_nro();

$nyk_puolivuotiskauden_nro = ($nyk_vuosi-2009)*2;
if($nyk_kk < 7){
    $nyk_puolivuotiskauden_nro--;
}

// Jos palauteolio on aktivoitu, haetaan sieltä tiedot:
if($palauteolio->kaytossa()){
    $sisalto = $palauteolio->get_sisalto();
    $ilmoitus = $palauteolio->get_ilmoitus();
    if($palauteolio->get_nayta_kiintolinkit() == false){
        $linkit = "";
    }
    if($palauteolio->get_oikean_palkin_naytto() == true){
        $oikea_palkki= 
            "<span class = 'korostus'>Lajim&auml;&auml;r&auml;t (sangen varmat):</span><br />".
                        Havainto::hae_havaintomaarat($parametriolio->ylaluokka_id_lj,
                                            $parametriolio->tietokantaolio,
                                            $nyk_puolivuotiskauden_nro);
    }
    else {
        $oikea_palkki="";
    }
}
else{
    $oikea_palkki = "<span class = 'korostus'>Lajim&auml;&auml;r&auml;t (sangen varmat):</span><br />".
                        Havainto::hae_havaintomaarat($parametriolio->ylaluokka_id_lj,
                                            $parametriolio->tietokantaolio,
                                            $nyk_puolivuotiskauden_nro);
}

// Tulostetaan sivun html:
echo nayta_bongaussivu($parametriolio,
                        $omat_valtuudet,
                        $kielivalikko,
                        $sisalto,
                        $ilmoitus,
                        $tiedot,
                        $linkit,
                        $oikea_palkki,
                        $parametriolio->ylaluokka_id_lj);

?>
