<?php
// Tänne tulevat ajax-kyselyt index.php-sivulta.
session_start();    // Aloitetaan istunto.


// Liekö alla oleva tarpeellinen, muttei siitä haittaakaan lie.
/*if(!isset($_SESSION['tunnistus']) || $_SESSION['tunnistus'] != 'kunnossa')
{
    echo Yleisarvoja::$istunto_vanhentunut;
    exit(); // Varmistetaan, että mitään alla olevaa ei suoriteta.
}

// Tarkistetaan istunnon laiskan ajan kesto ja kirjataan laiska ulos:
else if(isset($_SESSION['viim_aktiivisuus']) &&
    ((time()-$_SESSION['viim_aktiivisuus']) > Aikarajat::$LAISKA_ISTUNTOAIKA)){

    require_once('../kayttajahallinta/php_kayttajahallintametodit.php');

    echo Yleisarvoja::$istunto_vanhentunut;
    $kansiotaso = 2;    // Toisella tasolla.
    toteuta_passiivinen_ulos_toiminto($kansiotaso);
    exit;
}

// Huomaa, että käyttäjä on voinut uloskirjautua toisesta selaimesta!
else    // Jos tunnistus on kunnossa.
{*/

/********************************************************************/
// Tarkistetaan, ettei käyttäjää ole potkaistu tai itse kirjautunut ulos:
// Tämä voisi olla aiemmin, mutta en halunnut rasittaa liian usein
// tapahtuvaksi.
/*if(!online($_SESSION['tiedot']->id, $tietokantaolio)){
    $kansiotaso = 2;
    toteuta_passiivinen_ulos_toiminto($kansiotaso);
    echo Yleisarvoja::$istunto_vanhentunut;
    exit;
}*/
/******************************************************************/
    // Asetetaan aikavyöhyke:
    date_default_timezone_set  ('Europe/Helsinki'); // Vaaditaan alkaen MySQL5.1
    
    require_once('../asetukset/yleinen.php');
    $koodaus = Yleisasetuksia::$koodaus;

    /************************ KYSELYT *****************************************/
    $kysymys = isset($_REQUEST['kysymys']) ? $_REQUEST['kysymys']: "";
    /************************ KELLONAIKA **************************************/
    // Ensin otetaan "kevyet" tiedustelut, joihin ei tarvita tietokantayhteyttä.
    // Kellonaikakysely:
    
    if($kysymys == "kellonaika"){
        echo date("\k\l\o H:i:s");
    }

    
    else{      // Raskaammat kyselyt:
        
        // Haetaan asetukset ja avataan yhteys tietokantaan:
        require_once('../asetukset/tietokantayhteys.php');
        require_once('../asetukset/Valtuudet.php');
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

        require_once('../php_yleinen/Aika.php');
        require_once('../php_yleinen/Asetuspohja.php');
        require_once('../php_yleinen/Html.php');
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

        require_once('../yhteiset/Palaute.php');
        require_once('../yhteiset/Parametrit.php');

        // Bongaus:
        require_once('../bongaus/bongausasetukset.php');
        require_once('../bongaus/havainnot/Havainto.php');
        require_once('../bongaus/havainnot/Havaintokontrolleri.php');
        require_once('../bongaus/havainnot/Havaintonakymat.php');
        require_once('../bongaus/havainnot/Havaintojakso.php');
        require_once('../bongaus/havainnot/Havaintojaksolinkki.php');
         require_once('../bongaus/havainnot/Havaintopaikka.php');
        require_once('../bongaus/havainnot/Lisaluokitus.php');
        require_once('../bongaus/lajiluokat/Lajiluokka.php');
        require_once('../bongaus/lajiluokat/Kuvaus.php');
        require_once('../bongaus/lajiluokat/Kontrolleri_lj.php');
        require_once('../bongaus/lajiluokat/Nakymat_lj.php');

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

        // Kuvat
        require_once('../kuvat/Kuvatekstit.php');
        require_once('../kuvat/Kuva.php');
        require_once('../kuvat/Kuvakontrolleri.php');
        require_once('../kuvat/Kuvanakymat.php');
        require_once('../kuvat/Lajikuvalinkki.php');
        require_once('../kuvat/Havaintokuvalinkki.php');
        //======================================================================

        // Yhdistetään tietokantaan:
        $tietokantaolio = new Tietokantaolio($dbtyyppi, $dbhost, $dbuser, $dbsalis);
        $tietokantaolio->yhdista_tietokantaan($dbnimi);

        // Haetaan parametrit ja luodaan palauteolio;
        /**
         * @var Parametrit
         */
        $parametriolio = new Parametrit($tietokantaolio);
        $palauteolio = new Palaute();

        // Luodaan käyttäjäolio, jotta käyttäjän tiedot ovat saatavilla:
        $omaid = $parametriolio->get_omaid();
        $kayttaja = new Henkilo($omaid, $tietokantaolio);
        
        $kayttajakontrolleri = new Kayttajakontrolleri($tietokantaolio, 
                                                        $parametriolio);
        
        // Luodaan Havaintokontrolleri-luokan olio:
        $havaintokontrolleri = 
                new Havaintokontrolleri($tietokantaolio, $parametriolio);

        $lajiluokkakontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);

        // Luodaan käsiteltävä (tai tyhjä) pikakommentti:
        $nykyinen_pikakommentti = new Pikakommentti($tietokantaolio,
                                                    $parametriolio->get_pk_id());
        
        $pikakommenttikontrolleri = 
                new Pikakommenttikontrolleri($tietokantaolio, 
                                            $parametriolio, 
                                            $nykyinen_pikakommentti);
        
        // Kuvakontrolleri:
        $kuvakontrolleri = new Kuvakontrolleri($tietokantaolio, $parametriolio);
        $kuvanakymat = $kuvakontrolleri->get_kuvanakymat();
        
        $havaintonakymat = new Havaintonakymat($tietokantaolio, $parametriolio, $kuvanakymat);
        
        /*=================================================================*/
        /*=================================================================*/
        /*=================== KAYTTAJATOIMINNOT ALKAA =====================*/
        if($kysymys === "kirjaudu"){
            
            $kayttajakontrolleri->toteuta_sisaankirjautuminen($palauteolio);
            
            // Onnistuiko vai ei?
            $onnistuminen = 0;
            if($palauteolio->get_kirjautuminen_ok()){
                $onnistuminen = 1;
            }
            
            $ilmoitus = htmlspecialchars(
                                    $palauteolio->tulosta_kaikki_ilmoitukset());
            
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<ilmoitus>'.$ilmoitus.'</ilmoitus>';
            echo '<onnistuminen>'.$onnistuminen.'</onnistuminen>';
            echo '</tiedot>';
        }
        
        else if ($kysymys === "tarkista_poppootunnus") {
            
            $tunnus = $parametriolio->poppoo_kayttajatunnus;
            $osuma = Poppoo::etsi_poppootunnus($tunnus, $tietokantaolio);
            
            // Elementti, johon lomake tulee, jos onnistuu:
            $elem_id = Id::$sisalto;
            $id_kirjautumisdivi = Kayttajanakymat::$id_kirjautumisdivi;
            $id_oikea_palkki = Id::$palkki_oikea;
            $oikea_palkki_html = "";
            $lomakehtml = "";
            
            if($osuma === Poppoo::$EI_LOYTYNYT_TIETOKANNASTA){
                $ilmoitus = Kayttajatekstit::$poppooilmoitus_tunnusta_ei_loytynyt;
                $onnistuminen = 0;
            } else{
                $onnistuminen = 1;
                $ilmoitus = Kayttajatekstit::$poppooilmoitus_tunnus_ok_rekisteroidy;
                
                $kayttajakontrolleri->toteuta_nayta_tietolomake_uusi($palauteolio);
                $lomakehtml = $palauteolio->get_sisalto();
                $oikea_palkki_html = htmlspecialchars($palauteolio->get_oikea_palkki());
            }
            
            // Muista nuo htmlspecialchars()-metodit! Yllättävän usein ongelmia
            // ilman niitä!
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<ilmoitus>'.htmlspecialchars($ilmoitus).'</ilmoitus>';
            echo '<onnistuminen>'.$onnistuminen.'</onnistuminen>';
            echo '<lomakehtml>'.htmlspecialchars($lomakehtml).'</lomakehtml>';
            echo '<elem_id>'.$elem_id.'</elem_id>';
            echo '<id_kirjautumisdivi>'.$id_kirjautumisdivi.'</id_kirjautumisdivi>';
            echo '<id_oikea_palkki>'.$id_oikea_palkki.'</id_oikea_palkki>';
            echo '<html_oikea_palkki>'.$oikea_palkki_html.'</html_oikea_palkki>';
            echo '</tiedot>';
        }
        
        else if($kysymys === "hae_poppoohenkilon_tiedot"){
            $henkilo = new Henkilo($parametriolio->henkilo_id, $tietokantaolio);
            if($henkilo->olio_loytyi_tietokannasta){
                $html = $kayttajakontrolleri->kayttajanakymat->
                                    nayta_henkilotiedot($henkilo, false);
            } else{
                $html = Kayttajatekstit::$virheilmoitus_henkiloa_ei_loytynyt;
            }
            $elem_id = Id::$sisalto;
            
            // Muista nuo htmlspecialchars()-metodit! Yllättävän usein ongelmia
            // ilman niitä!
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<html>'.htmlspecialchars($html).'</html>';
            echo '<elem_id>'.$elem_id.'</elem_id>';
            echo '</tiedot>';
        }
        
        else if($kysymys === "hae_poppoohenkilon_tiedot_admin"){
            
            // Adminversio:
            $kayttajakontrolleri->toteutad_nayta_henkilotiedot($palauteolio);
            $html = $palauteolio->get_sisalto();
            
            $elem_id = Id::$sisalto;
            
            // Muista nuo htmlspecialchars()-metodit! Yllättävän usein ongelmia
            // ilman niitä!
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<html>'.htmlspecialchars($html).'</html>';
            echo '<elem_id>'.$elem_id.'</elem_id>';
            echo '</tiedot>';
        }
        
        else if($kysymys === "hae_poppootiedot"){
            $poppoo = new Poppoo($parametriolio->poppoon_id, $tietokantaolio);
            if($poppoo->olio_loytyi_tietokannasta){
                $kayttajakontrolleri->toteuta_nayta_poppootiedot($palauteolio);
                $html = $palauteolio->get_sisalto();
                $nimet = $palauteolio->get_vasen_palkki();
                
            } else{
                $html = Kayttajatekstit::$poppooilmoitus_ei_loytynyt;
                $nimet = "";
            }
            
            $elem_id = Id::$sisalto;
            $elem2_id = Id::$palkki_vasen;
            
            // Muista nuo htmlspecialchars()-metodit! Yllättävän usein ongelmia
            // ilman niitä!
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<html>'.htmlspecialchars($html).'</html>';
            echo '<nimet>'.htmlspecialchars($nimet).'</nimet>';
            echo '<elem_id>'.$elem_id.'</elem_id>';
            echo '<elem2_id>'.$elem2_id.'</elem2_id>';
            echo '</tiedot>';
        }
        
        else if($kysymys === "hae_poppootiedot_admin"){
            $poppoo = new Poppoo($parametriolio->poppoon_id, $tietokantaolio);
            if($poppoo->olio_loytyi_tietokannasta){
                
                // Seuraavassa pieni ero: toteutad (turhan helppo sekoittaa)
                $kayttajakontrolleri->toteutad_nayta_poppootiedot($palauteolio);
                $html = $palauteolio->get_sisalto();
                $nimet = $palauteolio->get_vasen_palkki();
                
            } else{
                $html = Kayttajatekstit::$poppooilmoitus_ei_loytynyt;
                $nimet = "";
            }
            
            $elem_id = Id::$sisalto;
            $elem2_id = Id::$palkki_vasen;
            
            // Muista nuo htmlspecialchars()-metodit! Yllättävän usein ongelmia
            // ilman niitä!
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<html>'.htmlspecialchars($html).'</html>';
            echo '<nimet>'.htmlspecialchars($nimet).'</nimet>';
            echo '<elem_id>'.$elem_id.'</elem_id>';
            echo '<elem2_id>'.$elem2_id.'</elem2_id>';
            echo '</tiedot>';
        }
        
        else if($kysymys === "hae_aktiivisuusajat"){
            $aktiivisuustaulukko = array();

            // Aika, jonka mittaisen passiivisuuden jälkeen käyttäjän
            // online-arvo asetetaan nollaksi.
            $katkaisuaika = Aikarajat::$LAISKA_ISTUNTOAIKA;


            // Haetaan linjalla olevien henkilöiden viimeisin aktiivisuusaika.
            // Rajoitetaan osumat niin, että vain erilaiset otetaan mukaan.
            // Muuten joku voi esiintyä useamman kerran, jos aktiivisuusaika
            // osuu samalle sekunnille.
            // VAROITUS: raskas haku! Indeksit välttämättömiä!
            $hakulause =
                "SELECT DISTINCT(he.id), he.etunimi, ak.viimeksi_aktiivi AS vika
                FROM aktiivisuus ak
                JOIN henkilot he
                ON he.id = ak.henkilo_id
                WHERE he.online = 1
                AND ak.viimeksi_aktiivi =
                    (SELECT MAX(viimeksi_aktiivi)
                    FROM aktiivisuus
                    WHERE henkilo_id = he.id)
                ORDER BY ak.viimeksi_aktiivi";

            //AND (he.id <> $omaid)

            $hakutulos = $tietokantaolio->tee_OMAhaku($hakulause);
            if($hakutulos != false){
                $aktiivisuustaulukko =
                    $tietokantaolio->hae_osumarivit_olioina($hakutulos);
            }

            $html = "<span class='korostus'>Linjoilla</span>
                    (viimeksi aktiivinen):<br />";
            if(sizeof($aktiivisuustaulukko) == 0){
                    $html .= "Ei muita kirjautuneita!";
            }
            else{
                foreach($aktiivisuustaulukko as $aktiivi){

                    // Tarkistetaan, onko henkilö ollut liian passiivinen:
                    if($aktiivi->vika == null){
                        $html .= $aktiivi->etunimi." (??)<br />";
                        // Henkilöllä ei aktiivisuusmerkintöjä.
                    }
                    else if(time() - $aktiivi->vika > $katkaisuaika){
                        $sisaan = false; // Ulos!
                        aseta_online($sisaan, $aktiivi->id, $tietokantaolio);
                        $html .= $aktiivi->etunimi." (Aikakatkaistu!)<br />";
                    }
                    else{
                        $aika_sek = time() - $aktiivi->vika;
                        $viim_akt = $aktiivi->vika;
                        if($aika_sek > 3599){
                            //$viim_akt = "(yli 1 h)";
                            $viim_akt = round(($aika_sek-3600)/60);
                            $viim_akt = "(n. 1h ".$viim_akt." min)";
                        }
                        else if($aika_sek > 60){
                            $viim_akt = round($aika_sek/60);
                            $viim_akt = "(n. ".$viim_akt." min)";
                        }
                        else{
                            $viim_akt = "(< 1 min)";
                        }

                        $html.= $aktiivi->etunimi." ".$viim_akt."<br />";
                    }
                }
            }
            echo $html;
        }
        /*=================================================================*/
        /*=================================================================*/
        /*=================== KAYTTAJATOIMINNOT LOPPU =====================*/
        
        //==================================================================
        //================= Havainnot ======================================
        /*********************** havaintojen haku henkilön mukaan  ********/
        else if($kysymys === "nayta_henkilon_havainnot"){
            $havaintokontrolleri->
                            toteuta_hae_henkilon_havainnot($palauteolio);
            $palaute = $palauteolio->get_sisalto();
            echo $palaute;
        }
        
        /*********************** havaintojen haku vakipaikan mukaan  ********/
        else if($kysymys === "nayta_vakipaikan_havainnot"){
            $havaintokontrolleri->
                    toteuta_hae_vakipaikan_havainnot($palauteolio);
            $palaute = $palauteolio->get_sisalto();
            
            echo $palaute;
        }
        
        /*********************** havaintojen haku vakipaikan mukaan  ********/
        else if($kysymys === "nayta_vakipaikan_pinnalajit"){
            $havaintokontrolleri->
                    toteuta_nayta_pinnalajit($palauteolio);
            $palaute = $palauteolio->get_sisalto();
            
            echo $palaute;
        }

        /*********************** havaintojen haku lajin mukaan *************/
        else if($kysymys === "nayta_lajihavainnot"){
            $havaintokontrolleri->
                            toteuta_hae_lajiluokan_havainnot($palauteolio);
            $palaute = $palauteolio->get_sisalto();
            echo $palaute;
        }

        /********************* Lajilistan näyttö: puolivuodet *************/
        else if($kysymys === "nayta_henkilon_bongauslajit"){

            $havaintokontrolleri->toteuta_hae_henkilon_lajilista($palauteolio);
            $palaute = $palauteolio->get_sisalto();

            echo $palaute;
        }
        
         /********************* Lajilistan näyttö: vuositaso ******************/
        else if($kysymys === "nayta_henkilon_pinnalajit"){

            $havaintokontrolleri->toteuta_hae_henkilon_vuosilajilista($palauteolio);
            $palaute = $palauteolio->get_sisalto();

            echo $palaute;
        }
        
        

        /*********************** havaintojen näyttö mukaan *************/
        else if($kysymys == "nayta_havainnot"){
            
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            echo $palauteolio->get_sisalto();
        }
        
        /*********************** Vakipaikkalomakkeen näyttö *************/
        else if($kysymys === "nayta_vakipaikkalomake"){
            
            $havaintokontrolleri->toteuta_nayta_vakipaikkalomake($palauteolio);
            $response = $palauteolio->get_ajax_response();
            echo $response;     // Ei xml tällä kertaa.
        }
        /*********************** Vakipaikkalomakkeen näyttö *************/
        else if($kysymys === "tallenna_vakipaikka"){
            
            $havaintokontrolleri->toteuta_tallenna_vakipaikka_uusivanha($palauteolio);
            $xml = $palauteolio->get_ajax_response();
            
            header('Content-type: text/xml');
            echo $xml;
        }
        /********************** Vakipaikan muutoksen aiheuttama toiminta ******/
        else if($kysymys === "aseta_paikka_ja_maa"){
            
            $xml = aseta_paikka_ja_maa($koodaus, $havaintokontrolleri);
            header('Content-type: text/xml');
            echo $xml;
        }
        


        /*=================================================================*/
        /*=================================================================*/
        /*=================== LAJILUOKKATOIMINNOT ALKAA ===================*/

        /********************* Lajiluokkalistan näyttö *********************/
        else if($kysymys == "nayta_lajiluokat"){

            $kontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);

            $kontrolleri->toteuta_nayta_lajiluokat($palauteolio);
            echo $palauteolio->get_sisalto();
        }
        else if($kysymys == "nayta_nimikuvauslomake"){
            $nimikuvausolio_id = isset($_REQUEST['nimikuvausolio_id']) ? 
                                    $_REQUEST['nimikuvausolio_id']: -1;
            
            if($nimikuvausolio_id == -1){
                $parametriolio->uusi_olio = true;
            }
            else{
                $parametriolio->uusi_olio = false;
                if($parametriolio->kieli_id == Kielet::$LATINA){
                    $parametriolio->id_lj = $nimikuvausolio_id;
                }
                else{
                    $parametriolio->id_kuv = $nimikuvausolio_id;
                }
            }

            $kontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);

            $kontrolleri->toteuta_nayta_nimikuvauslomake($palauteolio);
            echo $palauteolio->get_sisalto();

        }
        else if($kysymys == "tallenna_nimikuvaus"){
            $nimikuvausolio_id = isset($_REQUEST['nimikuvausolio_id']) ? 
                                    $_REQUEST['nimikuvausolio_id']: -1;

            $nimi = isset($_REQUEST['nimi']) ? $_REQUEST['nimi']: "tuntematon";
            $kuvaus = isset($_REQUEST['kuvaus']) ? $_REQUEST['kuvaus']: "tuntematon";


            // Ellei id ole määritelty, on kyseessä uuden olion luominen.
            // Tällöin on kyse aina Kuvaus-luokan oliosta, koska latina
            // on automaattisesti mukana Lajiluokka-luokan oliossa.
            if($nimikuvausolio_id == -1){
                $parametriolio->uusi_olio = true;
                $parametriolio->nimi_kuv = $nimi;
                $parametriolio->kuv_kuv = $kuvaus;

                $kontrolleri = new Kontrolleri_lj($tietokantaolio, 
                                                $parametriolio);

                $kontrolleri->toteuta_tallenna_uusi_kuvaus($palauteolio);

                // Haetaan uusi id:
                $nimikuvausolio_id = $palauteolio->get_muokatun_id();
            }
            else{
                $parametriolio->uusi_olio = false;
                if($parametriolio->kieli_id == Kielet::$LATINA){
                    $parametriolio->id_lj = $nimikuvausolio_id;
                    $parametriolio->nimi_latina_lj = $nimi;

                    $kontrolleri = new Kontrolleri_lj($tietokantaolio, 
                                                    $parametriolio);

                    
                    $kontrolleri->toteuta_tallenna_muokkaus_lajiluokka($palauteolio);
                }
                else{
                    $parametriolio->id_kuv = $nimikuvausolio_id;
                    $parametriolio->nimi_kuv = $nimi;
                    $parametriolio->kuv_kuv = $kuvaus;

                    $kontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);

                    $kontrolleri->toteuta_tallenna_muokkaus_kuvaus($palauteolio);
                }
            }

            // Html-tagit ovat myrkkyä xml:ssä!
            $ilmoitus = htmlspecialchars($palauteolio->get_ilmoitus(),ENT_QUOTES);
            $nimi = htmlspecialchars($nimi,ENT_QUOTES);
            
            // Onnistuminen arvoiksi 1 ja 0:
            if($palauteolio->get_onnistumispalaute() === 
                                                    Palaute::$OPERAATIO_ONNISTUI){
                $onnistuminen = 1;
            } else{
                $onnistuminen = 0;
            }

            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<taulukkosolun_id>'.$parametriolio->taulukkosolun_id.'</taulukkosolun_id>';
            echo '<nimi>'.$nimi.'</nimi>';
            echo '<onnistuminen>'.$onnistuminen.'</onnistuminen>';
            echo '<ilmoitus>'.$ilmoitus.'</ilmoitus>';
            echo '<ylaluokka_id>'.$parametriolio->ylaluokka_id_lj.'</ylaluokka_id>';
            echo '<kieli_id>'.$parametriolio->kieli_id.'</kieli_id>';
            echo '<olio_id>'.$nimikuvausolio_id.'</olio_id>';
            echo '<id_lj>'.$parametriolio->id_lj.'</id_lj>';
            echo '</tiedot>';
        }

        else if($kysymys === "vaihda_havjakso_lomake"){
            
            $id = $parametriolio->id_havjaks;
            $havjakso = new Havaintojakso($id, $tietokantaolio);
            $onUusi = 0;
            
            if($havjakso->olio_loytyi_tietokannasta){
                
                $nimi = $havjakso->get_nimi();
                $kommentti = $havjakso->get_kommentti();
                $alkuvuosi = $havjakso->get_alkuvuosi();
                $alkukk = $havjakso->get_alkukk();
                $alkupaiva = $havjakso->get_alkupaiva();
                $alkuh = $havjakso->get_alkutunti();
                $alkumin = $havjakso->get_alkumin();
                $kestovrk = $havjakso->get_keston_vrk();
                $kestoh = $havjakso->get_keston_h();
                $kestomin = $havjakso->get_keston_min();
            } else{
                $onUusi = 1;
                $nimi = "";
                $kommentti = "";
                $alkuvuosi = "";
                $alkukk = "";
                $alkupaiva = "";
                $alkuh = "";
                $alkumin = "";
                $kestovrk = "";
                $kestoh = "";
                $kestomin = "";
            }
            
            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<onUusi>'.$onUusi.'</onUusi>';
            echo '<id_nimi>'.Bongausasetuksia::$havjaksolomake_nimi_id.
                    '</id_nimi>';
            echo '<id_kommentti>'.Bongausasetuksia::$havjaksolomake_kommentti_id.
                    '</id_kommentti>';
            echo '<id_alkuh>'.Bongausasetuksia::$havjaksolomake_alkuh_id.
                    '</id_alkuh>';
            echo '<id_alkukk>'.Bongausasetuksia::$havjaksolomake_alkukk_id.
                    '</id_alkukk>';
            echo '<id_alkumin>'.Bongausasetuksia::$havjaksolomake_alkumin_id.
                    '</id_alkumin>';
            echo '<id_alkupaiva>'.Bongausasetuksia::$havjaksolomake_alkupäiva_id.
                    '</id_alkupaiva>';
            echo '<id_alkuvuosi>'.Bongausasetuksia::$havjaksolomake_alkuvuosi_id.
                    '</id_alkuvuosi>';
            echo '<id_kestoh>'.Bongausasetuksia::$havjaksolomake_kestoh_id.
                    '</id_kestoh>';
            echo '<id_kestomin>'.Bongausasetuksia::$havjaksolomake_kestomin_id.
                    '</id_kestomin>';
            echo '<id_kestovrk>'.Bongausasetuksia::$havjaksolomake_kestovrk_id.
                    '</id_kestovrk>';
            
            
            echo '<nimi>'.$nimi.'</nimi>';
            echo '<kommentti>'.$kommentti.'</kommentti>';
            echo '<alkuh>'.$alkuh.'</alkuh>';
            echo '<alkukk>'.$alkukk.'</alkukk>';
            echo '<alkumin>'.$alkumin.'</alkumin>';
            echo '<alkupaiva>'.$alkupaiva.'</alkupaiva>';
            echo '<alkuvuosi>'.$alkuvuosi.'</alkuvuosi>';
            echo '<kestoh>'.$kestoh.'</kestoh>';
            echo '<kestomin>'.$kestomin.'</kestomin>';
            echo '<kestovrk>'.$kestovrk.'</kestovrk>';
            echo '</tiedot>';
        }
        
        else if($kysymys == "nayta_siirtolomake"){


            $kontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);

            $kontrolleri->toteuta_nayta_havainto_ja_kuva_siirtolomake($palauteolio);

            // Html-tagit ovat myrkkyä xml:ssä!
            $lomakehtml = htmlspecialchars($palauteolio->get_sisalto(),ENT_QUOTES);

            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<lomakehtml>'.$lomakehtml.'</lomakehtml>';
            echo '<laatikko_id>'.Bongausasetuksia::$havaintokuvasiirtolaatikko_id.'</laatikko_id>';
            echo '</tiedot>';
        }

        else if($kysymys == "siirra_kuvat_ja_havainnot"){

            $kontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);

            $kontrolleri->toteuta_havaintojen_ja_kuvien_siirto($palauteolio);

            // Html-tagit ovat myrkkyä xml:ssä!
            $ilmoitus = htmlspecialchars($palauteolio->get_ilmoitus(),ENT_QUOTES);

            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<siirtolaatikko_id>'.Bongausasetuksia::$havaintokuvasiirtolaatikko_id.'</siirtolaatikko_id>';
            echo '<ilmoitus>'.$ilmoitus.'</ilmoitus>';
            echo '<ylaluokka_id>'.$parametriolio->ylaluokka_id_lj.'</ylaluokka_id>';
            echo '<siirtokohde_id>'.$parametriolio->siirtokohde_id_lj.'</siirtokohde_id>';
            echo '</tiedot>';
        }

        else if($kysymys == "poista_lajiluokka"){

            $kontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);

            $kontrolleri->toteuta_poista_lajiluokka($palauteolio);

            // Html-tagit ovat myrkkyä xml:ssä!
            $ilmoitus = htmlspecialchars($palauteolio->get_ilmoitus(),ENT_QUOTES);
            
            // Onnistuminen arvoiksi 1 ja 0:
            if($palauteolio->get_onnistumispalaute() === 
                                                    Palaute::$OPERAATIO_ONNISTUI){
                $onnistuminen = 1;
            } else{
                $onnistuminen = 0;
            }

            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<ilmoitus>'.$ilmoitus.'</ilmoitus>';
            echo '<ylaluokka_id>'.$parametriolio->ylaluokka_id_lj.'</ylaluokka_id>';
            echo '<onnistuminen>'.$onnistuminen.'</onnistuminen>';
            echo '</tiedot>';
        }



        /*=================================================================*/
        /*=================================================================*/
        /*=================== PIKAKOMMENTTITOIMINNOT ALKAA ================*/

        else if($kysymys == "hae_pikakommentit"){
            $pikakommenttikontrolleri->
                toteuta_nayta_pikakommentit(
                    $parametriolio->get_pk_kohdetyyppi(),
                    $parametriolio->get_pk_kohde_id(),
                    Pikakommenttikontrolleri::$kommenttien_max_nayttolkm,
                    $palauteolio);
            echo $palauteolio->get_sisalto();
        }
        
        else if($kysymys == "tallenna_uusi_pikakommentti"){
            $uusi = new Pikakommentti(Pikakommentti::$MUUTTUJAA_EI_MAARITELTY, 
                                    $tietokantaolio);
            
            $uusi->set_henkilo_id($omaid);
            $uusi->set_kohde_id($parametriolio->get_pk_kohde_id());
            $uusi->set_kohde_tyyppi($parametriolio->get_pk_kohdetyyppi());
            $uusi->set_kommentti($parametriolio->get_pk_kommenttiteksti());
            
            $pikakommenttikontrolleri->set_nykyinen_pk($uusi);
            
            $pikakommenttikontrolleri->toteuta_tallenna_uusi($palauteolio);
            
            $palaute = htmlspecialchars($palauteolio->get_ilmoitus(),ENT_QUOTES);

            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<kohde_id>'.$parametriolio->get_pk_kohde_id().'</kohde_id>';
            echo '<kohde_tyyppi>'.$parametriolio->get_pk_kohdetyyppi().'</kohde_tyyppi>';
            echo '<palaute>'.$palaute.'</palaute>';
            echo '</tiedot>';
        }
        else if($kysymys == "tallenna_pikakommentin_muutos"){
            $muokattava = new Pikakommentti($parametriolio->get_pk_id(), 
                                            $tietokantaolio);
            $muokattava->set_kohde_id($parametriolio->get_pk_kohde_id());
            $muokattava->set_kohde_tyyppi($parametriolio->get_pk_kohdetyyppi());
            $muokattava->set_kommentti($parametriolio->get_pk_kommenttiteksti());
            
            $pikakommenttikontrolleri->set_nykyinen_pk($muokattava);
            
            $pikakommenttikontrolleri->toteuta_tallenna_muokkaus($palauteolio);
            
            $palaute = htmlspecialchars($palauteolio->get_ilmoitus(),ENT_QUOTES);

            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<kohde_id>'.$parametriolio->get_pk_kohde_id().'</kohde_id>';
            echo '<kohde_tyyppi>'.$parametriolio->get_pk_kohdetyyppi().'</kohde_tyyppi>';
            echo '<palaute>'.$palaute.'</palaute>';
            echo '</tiedot>';
        }

        else if($kysymys == "nayta_poistovahvistus"){
            try{
                $pikakommenttikontrolleri->
                        toteuta_nayta_poistovarmistus($palauteolio);
                
                // Alla oikean palkin säiliötä hiukan lainaan..
                $perumiskoodi = 
                        htmlspecialchars($palauteolio->get_oikea_palkki(),
                                        ENT_QUOTES);

                // Tämä pitää tehdä, ettei xml romaha.
                $sisalto = htmlspecialchars($palauteolio->get_sisalto(),
                                            ENT_QUOTES);
            }
            catch (Exception $uups){
                $sisalto = "Kontrollerivirhe";
            }

            $kohde_id = "pk".$parametriolio->get_pk_id();

            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<kohde_id>'.$kohde_id.'</kohde_id>';
            echo '<sisalto>'.$sisalto.'</sisalto>';
            echo '<piiloon>'.$perumiskoodi.'</piiloon>';
            echo '<piilo_id>'.Pikakommenttikontrolleri::$piilovaraston_id.'</piilo_id>';
            echo '</tiedot>';
        }

        else if($kysymys == "toteuta_pikakommentin_poisto"){
            $sisalto = "Paapaa";
            try{
                $poistettavan_id = $parametriolio->get_pk_id();
                $pikakommenttikontrolleri->toteuta_poista($palauteolio);
                $sisalto = $palauteolio->get_ilmoitus();
                
                // Tämä pitää tehdä, ettei xml romaha.
                $ilmoitus = htmlspecialchars($sisalto,ENT_NOQUOTES);
            }
            catch (Exception $uups){
                $ilmoitus = "Kontrollerivirhe ilmeisesti";
            }

            

            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<poistettavan_id>'.$poistettavan_id.'</poistettavan_id>';
            echo '<kohde_id>'.$parametriolio->get_pk_kohde_id().'</kohde_id>';
            echo '<palaute>'.$ilmoitus.'</palaute>';
            echo '</tiedot>';
        }

        /*=================== PIKAKOMMENTTITOIMINNOT LOPPU ================*/
        /*=================================================================*/
        /*=================================================================*/

      
        
        
        /*=================== KUVATOIMINNOT ALKU ==========================*/
        /*=================================================================*/
        /*=================================================================*/

        
        else if($kysymys === "hae_kuva_ja_tiedot"){

            $kuvakontrolleri->toteuta_nayta_isokuva($palauteolio);
            $kuva_id = $parametriolio->id_kuva; 
            $html = htmlspecialchars($palauteolio->get_sisalto());
            
            
            // xml-muodossa saadaan muutkin tiedot mukaan:
            header('Content-type: text/xml');
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<tiedot>';
            echo '<kohde_tyyppi>'.Pikakommentti::$KOHDE_KUVA_BONGAUS.'</kohde_tyyppi>';
            echo '<kohde_id>'.$kuva_id.'</kohde_id>';
            echo '<html>'.$html.'</html>';
            echo '</tiedot>';
        }
        
        
        
        
        
        /*=================== KUVATOIMINNOT LOPPU ==========================*/
        /*=================================================================*/
        /*=================================================================*/
        
        
        $tietokantaolio->sulje_tietokanta($dbnimi); 
        
}// Raskaammat kyselyt loppuivat

/**
 * 
 * @param type $koodaus
 * @param Havaintokontrolleri $havKontr
 * @return string
 */
function aseta_paikka_ja_maa($koodaus, $havKontr){
    
    $tietokantaolio = $havKontr->get_tietokantaolio();
    $parametriolio = $havKontr->get_parametriolio();
    $kuvanakymat = new Kuvanakymat();
    
    $havaintonakymat = new Havaintonakymat($tietokantaolio, $parametriolio, $kuvanakymat);
    
    $maavalikko_id = 
        isset($_REQUEST['maavalikko_id']) ? $_REQUEST['maavalikko_id']: 
        Havaintopaikka::$ei_asetettu;
    $paikkakentta_id = 
        isset($_REQUEST['paikkakentta_id']) ? $_REQUEST['paikkakentta_id']: 
        "tuntematon";
    $muokkausnappispan_id = 
        isset($_REQUEST['muokkausnappispan_id']) ? $_REQUEST['muokkausnappispan_id']: 
        "tuntematon";
    
    
    $vakipaikka_id = $havKontr->get_parametriolio()->havaintopaikka_id;
    
    $vakipaikka = new Havaintopaikka($vakipaikka_id, $havKontr->get_tietokantaolio());

    $muokkausnappi = "";
    
    $paikka = "";
    $maa_id = -1;
    if($vakipaikka->olio_loytyi_tietokannasta){
        $paikka = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_NIMI);
        $maa_id = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_MAA_ID);
        $muokkausnappi = 
            htmlspecialchars(
                $havaintonakymat->luo_havaintopaikka_muokkauspainike($vakipaikka_id));
    }
    
    $safe_paikka = htmlspecialchars($paikka);


    // xml-muodossa saadaan muutkin tiedot mukaan:
    $xml ='<?xml version="1.0" encoding="'.$koodaus.'"?>'.
        '<tiedot>'.
        '<paikka>'.$safe_paikka.'</paikka>'.
        '<maa_id>'.$maa_id.'</maa_id>'.
        '<paikkakentta_id>'.$paikkakentta_id.'</paikkakentta_id>'.
        '<maavalikko_id>'.$maavalikko_id.'</maavalikko_id>'.
        '<muokkausnappispan_id>'.$muokkausnappispan_id.'</muokkausnappispan_id>'.
        '<muokkausnappi>'.$muokkausnappi.'</muokkausnappi>'.
        '</tiedot>';
    return $xml;
}

?>