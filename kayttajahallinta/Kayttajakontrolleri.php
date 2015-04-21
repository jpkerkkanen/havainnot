<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kayttajakontrolleri
 *
 * @author kerkjuk_admin
 */
class Kayttajakontrolleri extends Kontrolleripohja{
    
    // Asetuksia:
    public static $name_ktunnus = "kayttisehdokas"; // kirjautuminen
    public static $name_salis = "salasanaehdokas"; // kirjautuminen
    
    // Henkilötietojen name-arvot (esim. parametriolio). Tavallaan tässä
    // tuntuu olevan toistoa, mutta näin voidaan varmistaa, ettei tule ongelmaa
    // samojen name-arvojen takia (eri tietokantatauluissa voi olla saman nimisiä
    // sarakkeita, minkä takia suora sarakenimien käyttö voi johtaa ongelmiin).
    public static $name_henkilo_id = "henkilo_id";
    public static $name_etunimi = "henkilo_etunimi";
    public static $name_sukunimi = "henkilo_sukunimi";
    public static $name_lempinimi = "henkilo_lempinimi";
    public static $name_kommentti = "henkilo_kommentti";
    public static $name_eosoite = "henkilo_eosoite";
    public static $name_uusktunnus = "henkilo_uusikayttajatunnus";
    public static $name_uusisalasana = "henkilo_uusisalasana";
    public static $name_salasanavahvistus = "henkilo_salasanavahvistus";
    public static $name_osoite = "henkilo_osoite";
    public static $name_puhelin = "henkilo_puhelin";
    public static $name_online = "henkilo_online";
    public static $name_valtuudet = "henkilo_valtuudet";
    public static $name_asuinmaa = "henkilo_asuinmaa";
    public static $name_kieli = "henkilo_kieli";
   
    // Poppoo
    public static $name_poppootunnus = "poppootunnus"; // kirjautuminen
    public static $name_poppootunnusvahvistus = "poppootunnusvahvistus";
    public static $name_poppoonimi = "poppoonimi"; 
    public static $name_poppookommentti = "poppookommentti"; 
    public static $name_poppoomaxikoko = "poppoomaxikoko"; 
    public static $name_poppoon_id = "poppoo_id"; 
    
    // Tämä on siksi, ettei muutettava mene sekaisin adminin oman poppoon kanssa:
    public static $name_admin_henkilon_poppoo_id = "admin_poppoo_id"; 
    
    public $kayttajanakymat;
    
    
    function __construct($tietokantaolio, $parametriolio) {
        parent::__construct($tietokantaolio, $parametriolio);
        $this->kayttajanakymat = new kayttajanakymat();
        
        
        // Lisätään käyttäjä käyttöolioksi, jos se on tietokannassa:
        if($this->get_kayttaja()->olio_loytyi_tietokannasta){
            $this->set_olio($this->get_kayttaja());
        }
    }
    
    /** 
     * @param \Palaute $palauteolio
     */
    public function toteuta_nayta(&$palauteolio) {
        $this->lisaa_virheilmoitus("Toteutus kesken!");
        
    }

    /** 
     * @param \Palaute $palauteolio
     */
    public function toteuta_nayta_poistovarmistus(&$palauteolio) {
        $this->lisaa_virheilmoitus("Toteutus kesken!");
        
    }

    /** 
     * @param \Palaute $palauteolio
     */
    public function toteuta_poista(&$palauteolio) {
        $this->lisaa_virheilmoitus("Toteutus kesken!");
        
    }

    /** 
     * @param \Palaute $palauteolio
     */
    public function toteuta_tallenna_muokkaus(&$palauteolio) {
        // Määritetään sopivasti henkilön $tunnusten_muokkaus-muuttuja ja 
        // salavahvistus-muuttujat ja vastaavat arvot:
        $para = $this->get_parametriolio(); 
        $kayttaja = $this->get_olio();
        if($kayttaja instanceof Henkilo){
            if(($para->uusktunnus !== "") && ($para->uussalasana !== "")){
                $kayttaja->set_tunnusten_muokkaus(Tunnukset::$kumpikin);
                $kayttaja->set_salavahvistus($para->salavahvistus);
                $kayttaja->set_arvo($para->uusktunnus, 
                                        Henkilo::$sarakenimi_kayttajatunnus);
                $kayttaja->set_arvo($para->uussalasana, 
                                        Henkilo::$sarakenimi_salasana);
            } else if($para->uusktunnus !== ""){
                $kayttaja->set_tunnusten_muokkaus(Tunnukset::$vain_kayttis);
                $kayttaja->set_arvo($para->uusktunnus, 
                                        Henkilo::$sarakenimi_kayttajatunnus);
            } else if($para->uussalasana !== ""){
                $kayttaja->set_tunnusten_muokkaus(Tunnukset::$vain_salis);
                $kayttaja->set_salavahvistus($para->salavahvistus);
                $kayttaja->set_arvo($para->uussalasana, 
                                        Henkilo::$sarakenimi_salasana);
            } else{
                $kayttaja->set_tunnusten_muokkaus(Tunnukset::$ei_muokata);
            }
            
            // Sitten vain tehdään muutokset ja tallennetaan:
            $kayttaja->set_arvo($para->etun, Henkilo::$sarakenimi_etunimi);
            $kayttaja->set_arvo($para->sukun, Henkilo::$sarakenimi_sukunimi);
            $kayttaja->set_arvo($para->lempin, Henkilo::$sarakenimi_lempinimi);
            $kayttaja->set_arvo($para->komm, Henkilo::$sarakenimi_kommentti);
            $kayttaja->set_arvo($para->eosoite, Henkilo::$sarakenimi_eosoite);
            $kayttaja->set_arvo($para->osoite, Henkilo::$sarakenimi_osoite);
            $kayttaja->set_arvo($para->puhelin, Henkilo::$sarakenimi_puhelin);
            $kayttaja->set_arvo($para->asuinmaa, Henkilo::$sarakenimi_asuinmaa);
            $kayttaja->set_arvo($para->kieli_henkilo, Henkilo::$sarakenimi_kieli);
            
            $tallennus = $kayttaja->tallenna_muutokset();
            if($tallennus === Henkilo::$OPERAATIO_ONNISTUI){
                $palauteolio->lisaa_kommentti(
                        Kayttajatekstit::$ilmoitus_tietojen_muokkaustallennus_ok);
                
                // Päivitetään aktiivisuus:
                $kayttaja->paivita_aktiivisuus(
                                    Aktiivisuus::$HENKILON_TALLENNUS_MUOKKAUS);
                
                $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                $this->toteuta_kirjautunut_nakyma($palauteolio);
                
                // Otetaan kieli ylös:
                $_SESSION[Kielet::$name_kieli_id]=
                                $kayttaja->get_arvo(Henkilo::$sarakenimi_kieli);
                
            } else{
                $palauteolio->lisaa_virheilmoitus(
                    Kayttajatekstit::$virheilmoitus_tietojen_muokkaustallennus_ei_ok.
                    "<br/>".$kayttaja->tulosta_virheilmoitukset());
                
                $palauteolio->set_onnistumispalaute(Palaute::$VIRHE);
                
                $this->toteuta_kirjautunut_nakyma($palauteolio);
                
                // Näytetään muutosnäkymä:
                $this->toteuta_nayta_tietolomake_muokkaus($palauteolio);
            }
            
        } else{
            $this->lisaa_virheilmoitus(Kayttajatekstit::
                                            $virheilmoitus_henkiloa_ei_loytynyt);
        }
    }

    /** 
     * @param \Palaute $palauteolio
     */
    public function toteuta_tallenna_uusi(&$palauteolio) {
        $para = $this->get_parametriolio(); 
        
        $id = Henkilo::$MUUTTUJAA_EI_MAARITELTY;
        $kayttaja = new Henkilo($id, $this->get_tietokantaolio());

        // Sitten vain tehdään muutokset ja tallennetaan:
        $kayttaja->set_arvo($para->etun, Henkilo::$sarakenimi_etunimi);
        $kayttaja->set_arvo($para->sukun, Henkilo::$sarakenimi_sukunimi);
        $kayttaja->set_arvo($para->lempin, Henkilo::$sarakenimi_lempinimi);
        $kayttaja->set_arvo(0, Henkilo::$sarakenimi_online);    // Ei kirjaut.
        $kayttaja->set_arvo($para->komm, Henkilo::$sarakenimi_kommentti);
        
        $kayttaja->set_arvo($para->eosoite, Henkilo::$sarakenimi_eosoite);
        $kayttaja->set_arvo($para->osoite, Henkilo::$sarakenimi_osoite);
        $kayttaja->set_arvo($para->puhelin, Henkilo::$sarakenimi_puhelin);
        $kayttaja->set_arvo($para->online, Henkilo::$sarakenimi_online);
        $kayttaja->set_arvo($para->uusktunnus, Henkilo::$sarakenimi_kayttajatunnus);
        
        $kayttaja->set_arvo($para->uussalasana, Henkilo::$sarakenimi_salasana);
        $kayttaja->set_arvo($para->poppoon_id, Henkilo::$sarakenimi_poppoo_id);
        $kayttaja->set_arvo($para->asuinmaa, Henkilo::$sarakenimi_asuinmaa);
        $kayttaja->set_arvo($para->kieli_henkilo, Henkilo::$sarakenimi_kieli);
        
        // Annetaan uudelle normaalit valtuudet:
        $kayttaja->set_arvo(Valtuudet::$NORMAALI, Henkilo::$sarakenimi_valtuudet);
        
        $kayttaja->set_salavahvistus($para->salavahvistus);
        $tallennus = $kayttaja->tallenna_uusi();
        
        // Jos onnistui, kirjataan uusi henkilö sisään ja näytetään norminäkymä.
        if($tallennus === Henkilo::$OPERAATIO_ONNISTUI){
            $palauteolio->lisaa_kommentti(
                    Kayttajatekstit::$ilmoitus_uuden_henkilon_tallennus_ok);
            
            // Päivitetään aktiivisuus:
            $kayttaja->paivita_aktiivisuus(
                                    Aktiivisuus::$HENKILON_TALLENNUS_UUSI);
            
            // Kirjataan uusi käyttäjä sisään:
            
            // Tämä asettaa myös $_SESSIO[omaid]-arvon kohdalleen:
            $kayttaja->aseta_online(true);

            // Lisätään kirjautunut käytössä olevaksi kontrolleriin:
            $this->set_olio($kayttaja);

            // Samoin palautusolioon tieto kirjautumisesta:
            $palauteolio->set_kirjautuminen_ok(true);

            // Päivitetään aktiivisuus:
            $kayttaja->paivita_aktiivisuus(
                                    Aktiivisuus::$SISAANKIRJAUTUMINEN);

            // Testausta varten:
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            
            $this->toteuta_kirjautunut_nakyma($palauteolio);
            

        } else{
            $palauteolio->lisaa_virheilmoitus(
                Kayttajatekstit::$ilmoitus_uuden_henkilon_tallennus_ei_ok.
                "<br/>".$kayttaja->tulosta_virheilmoitukset());
            
            // Testausta varten:
            $palauteolio->set_onnistumispalaute(
                            Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_UUSI);
            
            // Näytetään lomakenäkymä:
            $this->toteuta_nayta_tietolomake_uusi($palauteolio);
        }
    }
    
    /** 
     * @param \Palaute $palauteolio
     */
    public function toteuta_palaa_takaisin(&$palauteolio){
        $palauteolio->get_ilmoitukset();
        $this->lisaa_virheilmoitus("Toteutus kesken!");
    }
    
    
    
    /** 
     * @param \Palaute $palauteolio
     */
    public function toteuta_vierailijanakyma(&$palauteolio){
        $palauteolio->set_sisalto("Et ole kirjautunut!");
        
        $ktunnus = $this->get_parametriolio()->get_kirjaudu_ktunnus();
        $palauteolio->set_kirjautumistiedot($this->kayttajanakymat->
                                        nayta_kirjautuminen_ajax($ktunnus));
        
        // Haetaan muutama kuva koristeeksi:
        $kuvat = "";
        
        
        $palauteolio->set_sisalto(
            Html::luo_div($kuvat, array(Maarite::classs("keskitys"))).
            Html::luo_table(
                Html::luo_tablerivi(
                    Html::luo_tablesolu($this->kayttajanakymat->
                                nayta_ilmoittautumispainike_kuvalla(), 
                        array(Maarite::style("padding-top:20px"))), 
                    array()),
                array(Maarite::align("center"),
                    Maarite::classs("keskitetty"))).
                
            Html::luo_div(Kayttajatekstit::$ilmoitus_yllapitajalta,   
                array(Maarite::align("center"),
                    Maarite::classs("keskitetty"))));
        
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
    }
    
    /** 
     * Asettaa palauteolion kirjautumistietoihin koodit uloskirjautumis-
     * painiketta ja omien tietojen muutospainiketta varten. Ei vaikuta
     * muuhun sisältöönm eli muu sisältö lisätään esimerkiksi 
     * index.php-tiedostossa. Näin saadaan vähennettyä riippuvuuksia 
     * pakettien (kuten bongaus ja kayttajahallinta) välillä.
     * @param \Palaute $palauteolio
     */
    public function toteuta_kirjautunut_nakyma(&$palauteolio){
        
        $kayttaja = $this->get_kayttaja();
        $nimi = "??";
        $maar_array = array();
        if($kayttaja instanceof Henkilo){
            $nimi = Html::luo_b($kayttaja->get_arvo(Henkilo::$sarakenimi_etunimi).
                        " ".$kayttaja->get_arvo(Henkilo::$sarakenimi_sukunimi), 
                    $maar_array);
            
            // Jos käyttäjä on ylläpitäjä, kerrotaan se:
            if($kayttaja->on_kuningas()){
                $nimi .= " (".Kayttajatekstit::$ilmoitus_yllapitaja.")";
            } else if($kayttaja->hae_valtuudet()+0 === 
                                                Valtuudet::$POPPOON_JOHTAJA){
                $nimi .= " (".Kayttajatekstit::$ilmoitus_poppoon_johtaja.")";
            }
            
            // Haetaan poppoon nimi:
            $poppoo = new Poppoo($this->get_parametriolio()->poppoon_id, 
                                $this->get_tietokantaolio());
            if($poppoo->olio_loytyi_tietokannasta){
                $nimi .= ". ".  Kayttajatekstit::$ilmoitus_Poppoo.": ".
                        Html::luo_b($poppoo->get_arvo(Poppoo::$sarakenimi_nimi),
                            $maar_array);
            }
        } 
        
        $mj = Kayttajatekstit::$ilmoitus_Kirjautunut.": ".$nimi;
        $painikkeet = 
                $this->kayttajanakymat->nayta_kotipainike_kuvalla().
                $this->kayttajanakymat->nayta_omat_tiedot_painike_kuvalla().
                $this->kayttajanakymat->nayta_poppootietopainike_kuvalla();
                
        
        //===================== Jos oikeuksia, näytetään admin-painike:
        if($kayttaja->on_kuningas()){
            $painikkeet .= 
                $this->kayttajanakymat->nayta_adminpainike_kuvalla();
        }
        //======================================================================
        
        $painikkeet .= $this->kayttajanakymat->
                                nayta_uloskirjautumispainike_kuvalla();
        
        $palauteolio->set_kirjautumistiedot($mj);
        $palauteolio->set_painikkeet($painikkeet);
        
        
        $palauteolio->set_nayttomoodi(
                Html_tulostus::$nayttomoodi_kaksipalkki_oikea_levea);
    }
    
    /** 
     * Toteuttaa uloskirjautumisen. Kutsuu Henkilo-luokan kirjaudu_ulos()-
     * metodia. Päivittää aktiivisuuden. Ja kutsuu metodia 
     * "toteuta_vierailijanakyma()".
     * @param \Palaute $palauteolio
     */
    function toteuta_uloskirjautuminen(&$palauteolio){

        $kayttaja = $this->get_kayttaja();
        if($kayttaja instanceof Henkilo){
            $etunimi = $kayttaja->get_arvo(Henkilo::$sarakenimi_etunimi);
            $uloskirjaus = $kayttaja->kirjaudu_ulos();
            if($uloskirjaus === Henkilo::$OPERAATIO_ONNISTUI){
                $palauteolio->lisaa_kommentti(
                            Kayttajatekstit::$ilmoitus_Hei_hei.
                            " ".$etunimi." ".
                            Kayttajatekstit::$ilmoitus_ja_tervetuloa_uudelleen);
                
                $palauteolio->set_painikkeet("");
                $palauteolio->set_sisalto("Ulkona ollaan");
                
                // Päivitetään aktiivisuus:
                $kayttaja->paivita_aktiivisuus(
                                        Aktiivisuus::$ULOSKIRJAUTUMINEN);
                
                // Asetetaan käyttöoliolle ei-määritelty arvo:
                $this->set_olio(Kayttajakontrolleri::$MUUTTUJAA_EI_MAARITELTY);
                
                $this->toteuta_vierailijanakyma($palauteolio);
                
            } else{
                $kayttaja->lisaa_virheilmoitus("Virhe: kirjaudu_ulos()".
                        " palautti virheviestin!");
                $palauteolio->lisaa_kommentti(
                    Kayttajatekstit::$virheilmoitus_uloskirjautuminen_epaonnistui);
            }
        } else{
            $palauteolio->lisaa_kommentti(
                            Kayttajatekstit::$ilmoitus_et_ole_kirjautunut);
        }
    }
    
    /** 
     * @param \Palaute $palauteolio
     */
    function toteuta_sisaankirjautuminen(&$palauteolio){

        // tyhjennetään vanhat ilmoitukset:
        $palauteolio->tyhjenna_kaikki_ilmoitukset();
        
        $parametriolio = $this->get_parametriolio();
        $tietokantaolio = $this->get_tietokantaolio();
        $ktunnus = $parametriolio->get_kirjaudu_ktunnus();
        $salis = $parametriolio->get_kirjaudu_salis();
        
        $testitulos = Henkilo::tarkista_kirjautuminen($ktunnus, $salis, 
                                                    $tietokantaolio);
        if($testitulos == Henkilo::$EI_LOYTYNYT_TIETOKANNASTA){
            $palaute = Kayttajatekstit::$ilmoitus_tunnukset_ei_kaytossa;
            $this->toteuta_vierailijanakyma($palauteolio);
        } else{
            $kirjautunut = new Henkilo($testitulos, $tietokantaolio);
            if($kirjautunut->olio_loytyi_tietokannasta){

                // Tämä asettaa myös $_SESSIO[omaid]-arvon kohdalleen:
                $kirjautunut->aseta_online(true);
                $palaute = Kayttajatekstit::$ilmoitus_tervetuloa." ".
                        $kirjautunut->get_arvo(Henkilo::$sarakenimi_etunimi).
                        "!";
                
                // Haetaan henkilön poppoo. Jos löytyy, lisätään sessiotieto.
                $id = $kirjautunut->get_arvo(Henkilo::$sarakenimi_poppoo_id);
                $poppoo = new Poppoo($id, $this->get_tietokantaolio());
                if($poppoo->olio_loytyi_tietokannasta){
                    $_SESSION[Sessio::$poppoon_id] = $id;
                    $this->get_parametriolio()->poppoon_id = $id;
                }
                
                // Lisätään kirjautunut käytössä olevaksi kontrolleriin:
                $this->set_olio($kirjautunut);
                
                // Samoin palautusolioon tieto kirjautumisesta:
                $palauteolio->set_kirjautuminen_ok(true);
                
                // Haetaan viimeinen uloskirjautumisaika ja tallennetaan
                // sessiotietoihin:
                $_SESSION[Sessio::$edellinen_uloskirjausaika_sek] =
                        $kirjautunut->hae_vika_ulosaika();
                
                // Päivitetään aktiivisuus:
                $kirjautunut->paivita_aktiivisuus(
                                        Aktiivisuus::$SISAANKIRJAUTUMINEN);
                
                $this->toteuta_kirjautunut_nakyma($palauteolio);
            }
        }

        $palauteolio->set_ilmoitus($palaute);
        
    }
    /** 
     * @param \Palaute $palauteolio
     */
    function toteuta_kirjautumistarkastus(&$palauteolio){
        
        $kirjautuminen_ok = false;  // Oletuksena näin.

        // Tarkistetaan, onko käyttäjä ihan varmasti kirjautunut ja ettei sessio
        // ole vanhentunut:
        if(isset($_SESSION[Sessio::$tunnistus]) && 
            $_SESSION[Sessio::$tunnistus] === Sessio::$tunnistus_ok){
            
            $tietokantaolio = $this->get_tietokantaolio();
            
            // Jos sessiomuuttuja ok, tarkistetaan vielä tietokannasta, ettei
            // kyseessä ole paluupainikkeella tms saatu sessioarvo:
            $omaid = $this->get_parametriolio()->get_omaid();
            $kayttaja = $this->get_kayttaja();
            if($kayttaja->olio_loytyi_tietokannasta && 
                $kayttaja->online($omaid, $tietokantaolio)){
                
                // Tarkistetaan istunnon laiskan ajan kesto ja kirjataan laiska ulos:
                if(isset($_SESSION[Sessio::$viim_aktiivisuus]) &&
                    ((time()-$_SESSION[Sessio::$viim_aktiivisuus]) > 
                                                Aikarajat::$MAXILAISKA_AIKA)){
                    $palauteolio->lisaa_kommentti(
                                Kayttajatekstit::$ilmoitus_sessio_vanhentunut);
                } else{
                    $kirjautuminen_ok = true;
                }
                
                // Otetaan kieli ylös:
                $_SESSION[Kielet::$name_kieli_id]=
                                $kayttaja->get_arvo(Henkilo::$sarakenimi_kieli);
                
            } else{
                $this->lisaa_virheilmoitus("Ongelma: online=".
                                    $kayttaja->online($omaid, $tietokantaolio));
            }
        } else{
            if(!isset($_SESSION[Sessio::$tunnistus])){
                $arvo = "ei-määritelty";
            } else{
                $arvo = $_SESSION[Sessio::$tunnistus];
            }
            $this->lisaa_virheilmoitus("Ongelma: sessio-tunnistus=".$arvo);
        }
        // Kummassakin tapauksessa päivitetään palauteolion kirjautuminen_ok-
        // muuttuja.
        if($kirjautuminen_ok){
            $palauteolio->set_kirjautuminen_ok(true);
        } else{
            $palauteolio->set_kirjautuminen_ok(false);
            
            // Sessiotiedot kuntoon.
            $_SESSION[Sessio::$tunnistus] = Sessio::$tunnistus_ei_ok;
            $_SESSION[Sessio::$omaid] = Henkilo::$MUUTTUJAA_EI_MAARITELTY;
            $_SESSION[Sessio::$viim_aktiivisuus] = 0;
        }
    }
    
    /** 
     * @param \Palaute $palauteolio
     */
    function toteuta_poistu_valtuuksien_muutoksista(&$palauteolio){
        $parametriolio = $this->get_parametriolio();
        $ilmoitus = Tekstit::$ilm_valtuusmuokkaus_peruttu;
        $sisalto = nayta_valtuuslomake($parametriolio->get_tietokantaolio());

        $palauteolio->set_sisalto($sisalto);
        $palauteolio->set_ilmoitus($ilmoitus);
        
    }
    /** 
     * @param \Palaute $palauteolio
     */
    function toteuta_tallenna_valtuuksien_muutokset(&$palauteolio){
        $parametriolio = $this->get_parametriolio();
        $sisalto = "";
        $ilmoitus = "";

        $tulos = tallenna_valtuusmuutos($parametriolio->henkilo_id,
                                        $parametriolio->uudet_valtuudet,
                                        $parametriolio->get_tietokantaolio());

        if($tulos == "onnistui"){
            $ilmoitus = Tekstit::$ilm_valtuusmuutokset_tallennettu;
            $sisalto =
                    nayta_valtuuslomake($parametriolio->get_tietokantaolio());
        }

        // Ellei onnistu, annetaan ilmoitus:
        else{
            $ilmoitus = $tulos;
            $sisalto =
                    nayta_valtuuslomake($parametriolio->get_tietokantaolio());
        }

        $palauteolio->set_sisalto($sisalto);
        $palauteolio->set_ilmoitus($ilmoitus);
    }

    /** 
     * @param \Palaute $palauteolio
     */
    function toteuta_nayta_valtuusmuutoslomake(&$palauteolio){
        $parametriolio = $this->get_parametriolio();
        $ilmoitus = Tekstit::$ilm_muokkaa_valtuudet;
        $sisalto = nayta_valtuusmuutoslomake($parametriolio->get_tietokantaolio(),
                                            $parametriolio->henkilo_id);
        $palauteolio->set_sisalto($sisalto);
        $palauteolio->set_ilmoitus($ilmoitus);
        
    }
    /** 
     * @param \Palaute $palauteolio
     */
    function toteuta_nayta_valtuustaulukko(&$palauteolio){
        $parametriolio = $this->get_parametriolio();
        $ilmoitus = Tekstit::$ilm_nayta_valtuudet;
        $sisalto = nayta_valtuuslomake($parametriolio->get_tietokantaolio());
        $palauteolio->set_sisalto($sisalto);
        $palauteolio->set_ilmoitus($ilmoitus);
        
    }
   /** 
     * @param \Palaute $palauteolio
     */
   function toteuta_nayta_tietolomake_muokkaus(&$palauteolio){
       $parametriolio = $this->get_parametriolio();
       
       $kayttaja = $this->get_kayttaja();
       
       if($kayttaja instanceof Henkilo){
           // Asetetaan tiedot parametriolioon:
           if($parametriolio->etun === ""){
               $parametriolio->etun = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_etunimi);
           }
           if($parametriolio->sukun === ""){
               $parametriolio->sukun = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_sukunimi);
           }
           if($parametriolio->lempin === ""){
               $parametriolio->lempin = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_lempinimi);
           }
           if($parametriolio->komm === ""){
               $parametriolio->komm = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_kommentti);
           }
           if($parametriolio->eosoite === ""){
               $parametriolio->eosoite = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_eosoite);
           }
           if($parametriolio->uusktunnus === ""){
               $parametriolio->uusktunnus = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_kayttajatunnus);
           }
           
           if($parametriolio->osoite === ""){
               $parametriolio->osoite = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_osoite);
           }
           if($parametriolio->puhelin === ""){
               $parametriolio->puhelin = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_puhelin);
           }
           
           $parametriolio->asuinmaa = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_asuinmaa);
           $parametriolio->kieli_henkilo = 
                            $kayttaja->get_arvo(Henkilo::$sarakenimi_kieli);
       }
       
       $sisalto = $this->kayttajanakymat->
                                nayta_henkilotietolomake($parametriolio, 
                                                        false);
       $palauteolio->set_sisalto($sisalto);
       
       // Haetaan jäsenet, nykyinen poppoo:
       $poppoo = new Poppoo($this->get_parametriolio()->poppoon_id, 
                            $this->get_tietokantaolio());
       if($poppoo->olio_loytyi_tietokannasta){
            $poppoonimi = $poppoo->get_arvo(Poppoo::$sarakenimi_nimi);
            $tietojen_katselu = false;
            $poppoon_jasenet = $poppoo->hae_poppoon_jasenet();
            
            $admintoiminto = false;
            $jasentaulukko = $this->kayttajanakymat->
                                        nayta_poppoon_jasenet($poppoonimi, 
                                                        $poppoon_jasenet, 
                                                        $tietojen_katselu,
                                                        $admintoiminto);

            $palauteolio->set_oikea_palkki($jasentaulukko);
       }
       
       $palauteolio->set_nayttomoodi(
                    Html_tulostus::$nayttomoodi_kaksipalkki_oikea_levea);
   }
   
   /** 
     * @param \Palaute $palauteolio
     */
    function toteuta_nayta_tietolomake_uusi(&$palauteolio){
        $parametriolio = $this->get_parametriolio();
        
        $parametriolio->henkiloilmoitus = $palauteolio->tulosta_virheilmoitukset();
       
        $uusi = true;
        $sisalto = $this->kayttajanakymat->
                nayta_henkilotietolomake($parametriolio, $uusi);
        
        // Haetaan jäsenet, nykyinen poppoo. Poppoon_id on paras ottaa
        // sessiomuuttujasta, jotta ajax-kutsun kauttakin saadaa poppoo selville.
        $poppoo = new Poppoo($_SESSION[Sessio::$poppoon_id], 
                            $this->get_tietokantaolio());
        if($poppoo->olio_loytyi_tietokannasta){
            $poppoonimi = $poppoo->get_arvo(Poppoo::$sarakenimi_nimi);
            $tietojen_katselu = false;
            $poppoon_jasenet = $poppoo->hae_poppoon_jasenet();
            $admintoiminto = false;
            
            $jasentaulukko = $this->kayttajanakymat->
                                        nayta_poppoon_jasenet($poppoonimi, 
                                                        $poppoon_jasenet, 
                                                        $tietojen_katselu,
                                                        $admintoiminto);

            $palauteolio->set_oikea_palkki($jasentaulukko);
        } else{
            $palauteolio->set_oikea_palkki(
                                Kayttajatekstit::$poppooilmoitus_ei_loytynyt);
        }
        
        $palauteolio->set_sisalto($sisalto);
        
        $palauteolio->set_kirjautumistiedot("");
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_kolmipalkki);
    }
    
    /** 
     * @param \Palaute $palauteolio
     */
    function toteuta_nayta_poppootiedot(&$palauteolio){
        
        // Haetaan jäsenet, nykyinen poppoo:
        $poppoo = new Poppoo($this->get_parametriolio()->poppoon_id, 
                            $this->get_tietokantaolio());
        if($poppoo->olio_loytyi_tietokannasta){
            $poppoonimi = $poppoo->get_arvo(Poppoo::$sarakenimi_nimi);
            $tietojen_katselu = true;
            $poppoon_jasenet = $poppoo->hae_poppoon_jasenet();
            $admintoiminto = false;
            
            $jasentaulukko = $this->kayttajanakymat->
                                        nayta_poppoon_jasenet($poppoonimi, 
                                                        $poppoon_jasenet, 
                                                        $tietojen_katselu,
                                                        $admintoiminto);

            $palauteolio->set_vasen_palkki($jasentaulukko);
            
            if(empty($poppoon_jasenet)){
                $palauteolio->set_sisalto(Kayttajatekstit::
                                            $poppooilmoitus_tyhja_poppoo);
            } else{
                $eka_henkilo = $poppoon_jasenet[0];
                
                // Ei muokkausoikeutta!
                $muokkausoikeus = false;
                
                $ekan_tiedot = $this->kayttajanakymat->
                                            nayta_henkilotiedot($eka_henkilo,
                                                        $muokkausoikeus);
                $palauteolio->set_sisalto($ekan_tiedot);
            }
        } else{
            $palauteolio->set_vasen_palkki(
                                Kayttajatekstit::$poppooilmoitus_ei_loytynyt);
        }
        $palauteolio->set_nayttomoodi(
                        Html_tulostus::$nayttomoodi_kaksipalkki_vasen_levea);
        
    }
    
    /** 
     * Hakee henkilön ne tiedot, jotka voidaan näyttää muille poppoon jäsenille.
     * ASettaa tietotaulukon palauteolion sisalto-elementin sisällöksi.
     * 
     * Ellei parametriolion henkilo_id ole aktiivinen, 
     * kirjoittaa palautteen asiasta.
     * 
     * Onnistumisesta laitetaan onnistumispalaute testausta varten palauteolioon.
     * 
     * @param \Palaute $palauteolio
     */
    function toteuta_nayta_henkilotiedot_poppoolle(&$palauteolio){
        
        // Haetaan jäsenet, nykyinen poppoo:
        $henkilo = new Henkilo($this->get_parametriolio()->henkilo_id, 
                            $this->get_tietokantaolio());
        
        // False viittaa siihen, ettei
        if($henkilo->olio_loytyi_tietokannasta){
            $tiedot = $this->kayttajanakymat->nayta_henkilotiedot($henkilo, false); 
            $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        } else{
            $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
            $tiedot = Kayttajatekstit::$virheilmoitus_henkiloa_ei_loytynyt;
        }
        $palauteolio->set_sisalto($tiedot);
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
    }
    
    /** 
     * @param \Palaute $palauteolio
     */
   function toteuta_nayta_poppookirjautuminen(&$palauteolio){
       $parametriolio = $this->get_parametriolio();
       
       $sisalto = $this->kayttajanakymat->
               nayta_poppookirjautuminen_ajax(
                                    $parametriolio->poppoo_kayttajatunnus);
       $palauteolio->set_sisalto($sisalto);
       
       // Estetään tavallisen kirjautumisen näkyminen samaan aikaan:
       $palauteolio->set_kirjautumistiedot("");
       
       // Huom: oikeaa palkkia tarvitaan joka tapauksessa!
       $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_kolmipalkki);
   }
   
   
   
   
   /**
    * Luo painikkeen Html-koodin ja palauttaa sen.
    */
   function poppoo_luo_uusi_painike(){
       
       $value = Kayttajatekstit::$nappi_poppoo_luo_uusi_value;
       $name = Toimintonimet::$yllapitotoiminto;
              
       $maar_array_form = array();
       $maar_array_input = array(Maarite::value($value), Maarite::name($name));
       $html = Html::luo_forminput_painike($maar_array_form, $maar_array_input);
       return $html;
   }
   /**
    * Luo painikkeen Html-koodin ja palauttaa sen.
    */
   function poppoo_muokkaa_painike(){
       
       $value = Kayttajatekstit::$nappi_poppoo_muokkaa_value;
       $name = Toimintonimet::$yllapitotoiminto;
              
       $maar_array_form = array();
       $maar_array_input = array(Maarite::value($value), Maarite::name($name));
       $html = Html::luo_forminput_painike($maar_array_form, $maar_array_input);
       return $html;
   }
   /**
    * Luo painikkeen Html-koodin ja palauttaa sen.
    */
   function poppoo_poista_painike(){
       
       $value = Kayttajatekstit::$nappi_poppoo_poista_value;
       $title = Kayttajatekstit::$nappi_poppoo_poista_title;
       $name = Toimintonimet::$yllapitotoiminto;
              
       $maar_array_form = array();
       $maar_array_input = array(Maarite::value($value), 
                                Maarite::name($name),
                                Maarite::title($title));
       
       $html = Html::luo_forminput_painike($maar_array_form, 
                                            $maar_array_input);
       return $html;
   }
   
   
   
   //========================= ADMIN-HOMMELIT ==================================
   /** 
    * @param \Palaute $palauteolio
    */
   function toteutad_nayta_adminnakyma(&$palauteolio){
       
       $poppoot = Poppoo::hae_kaikki_poppoot($this->get_tietokantaolio());
       
       // Haetaan valtuudet:
       $admin = new Henkilo($this->get_parametriolio()->get_omaid(), 
                            $this->get_tietokantaolio());
       
       $valtuudet = $admin->hae_valtuudet();
       
       $sisalto = $this->poppoo_luo_uusi_painike();
       $sisalto .= $this->kayttajanakymat->
                            naytad_poppoot($poppoot, 
                                            $this->get_parametriolio(),
                                            $valtuudet);
       $palauteolio->set_nayttomoodi(Html_tulostus::
                                $nayttomoodi_kaksipalkki_vasen_levea);
       $palauteolio->set_sisalto($sisalto);
   }
   
   /** 
     * @param \Palaute $palauteolio
     */
    function toteutad_nayta_poppootiedot(&$palauteolio){
        
        // Haetaan jäsenet, nykyinen poppoo:
        $poppoo = new Poppoo($this->get_parametriolio()->poppoon_id, 
                            $this->get_tietokantaolio());
        if($poppoo->olio_loytyi_tietokannasta){
            $poppoonimi = $poppoo->get_arvo(Poppoo::$sarakenimi_nimi);
            $tietojen_katselu = true;
            $poppoon_jasenet = $poppoo->hae_poppoon_jasenet();
            $admintoiminto = true;
            
            $jasentaulukko = $this->kayttajanakymat->
                                        nayta_poppoon_jasenet($poppoonimi, 
                                                        $poppoon_jasenet, 
                                                        $tietojen_katselu,
                                                        $admintoiminto);

            $palauteolio->set_vasen_palkki($jasentaulukko);
            
            if(empty($poppoon_jasenet)){
                $palauteolio->set_sisalto(Kayttajatekstit::
                                            $poppooilmoitus_tyhja_poppoo);
            } else{
                $eka_henkilo = $poppoon_jasenet[0];
                
                // Tarkistetaan muokkausoikeus:
                $muokkausoikeus = false;
               
                
                if($this->get_kayttaja()->hae_valtuudet() === Valtuudet::$HALLINTA){
                    
                    $muokkausoikeus = true;
                }

                $ekan_tiedot = $this->kayttajanakymat->
                                            nayta_henkilotiedot($eka_henkilo,
                                                        $muokkausoikeus);
                $palauteolio->set_sisalto($ekan_tiedot);
            }
        } else{
            $palauteolio->set_vasen_palkki(
                                Kayttajatekstit::$poppooilmoitus_ei_loytynyt);
        }
        $palauteolio->set_nayttomoodi(
                        Html_tulostus::$nayttomoodi_kaksipalkki_vasen_levea);
        
    }
   
   /** 
     * Hakee henkilön ne tiedot, jotka voidaan näyttää muille poppoon jäsenille.
     * Lisäksi luodaan muokkaus painike, josta tietoja päästään muokkaamaan. 
     * ASettaa tietotaulukon palauteolion sisalto-elementin sisällöksi.
     * 
     * Ellei parametriolion henkilo_id ole aktiivinen, 
     * kirjoittaa palautteen asiasta.
     * 
     * Onnistumisesta laitetaan onnistumispalaute testausta varten palauteolioon.
     * 
     * @param \Palaute $palauteolio
     */
    function toteutad_nayta_henkilotiedot(&$palauteolio){
        
        // Haetaan jäsenet, nykyinen poppoo:
        $henkilo = new Henkilo($this->get_parametriolio()->henkilo_id, 
                            $this->get_tietokantaolio());
        
        // Jatketaan, jos henkilö löytyi.
        if($henkilo->olio_loytyi_tietokannasta){
            
            $muokkausoikeus = true;
            $tiedot = $this->kayttajanakymat->nayta_henkilotiedot($henkilo, 
                                                                $muokkausoikeus); 
            $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        } else{
            $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
            $tiedot = Kayttajatekstit::$virheilmoitus_henkiloa_ei_loytynyt;
        }
        $palauteolio->set_sisalto($tiedot);
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
    }
    
    /** 
     * Toteuttaa henkilötietomuutoslomakkeen näyttämisen adminille. Lomakkeella
     * voi muokata tunnuksia ja poppoota.
     * 
     * Ellei parametriolion henkilo_id ole aktiivinen, 
     * kirjoittaa palautteen asiasta.
     * 
     * Onnistumisesta laitetaan onnistumispalaute testausta varten palauteolioon.
     * 
     * @param \Palaute $palauteolio
     */
    function toteutad_nayta_henkilon_tietolomake(&$palauteolio){
        
        // Haetaan muokattavan henkilön tiedot:
        $henkilo = new Henkilo($this->get_parametriolio()->henkilo_id, 
                            $this->get_tietokantaolio());
        
        $poppoot = Poppoo::hae_kaikki_poppoot($this->get_tietokantaolio());
        
        // Ellei löytynyt, ei jatketa. Muuten näytetään lomake.
        if($henkilo->olio_loytyi_tietokannasta){
            
            $parametriolio = $this->get_parametriolio();
            $henkilon_poppoo_id = $henkilo->get_arvo(Henkilo::$sarakenimi_poppoo_id);
            
            $tiedot = $this->kayttajanakymat->
                        naytad_henkilotietolomake(
                            $parametriolio, 
                            $poppoot, 
                            $henkilo);
                    
            $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        } else{
            $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
            $tiedot = Kayttajatekstit::$virheilmoitus_henkiloa_ei_loytynyt;
        }
        $palauteolio->set_sisalto($tiedot);
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
    }
   
   /** 
    * @param \Palaute $palauteolio
    */
   function toteutad_nayta_poppoolomake_uusi(&$palauteolio){
        $parametriolio = $this->get_parametriolio();
        
        $parametriolio->poppooilmoitus = $palauteolio->tulosta_virheilmoitukset();
       
        // Poppoota ei tarvita.
        $poppoo = false;
        $uusi = true;
        $sisalto = $this->kayttajanakymat->
                nayta_poppootietolomake($parametriolio, $uusi, $poppoo);
        
        $palauteolio->set_sisalto($sisalto);
        
        $palauteolio->set_kirjautumistiedot("");
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
   }
   /** 
    * @param \Palaute $palauteolio
    */
   function toteutad_nayta_poppoolomake_muokkaus(&$palauteolio){
       $parametriolio = $this->get_parametriolio();
        
        $parametriolio->poppooilmoitus = $palauteolio->tulosta_virheilmoitukset();
       
        $poppoo_id = $this->get_parametriolio()->poppoon_id;
        $poppoo = new Poppoo($poppoo_id, $this->get_tietokantaolio());
        
        $uusi = false;
        $sisalto = $this->kayttajanakymat->
                nayta_poppootietolomake($parametriolio, $uusi, $poppoo);
        
        $palauteolio->set_sisalto($sisalto);
        
        $palauteolio->set_kirjautumistiedot("");
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
   }
    /** 
     * Toteuttaa
     * @param \Palaute $palauteolio
     */
    function toteutad_tallenna_poppoo_uusi(&$palauteolio){
        $para = $this->get_parametriolio(); 
        
        $tallentaja = $this->get_olio();
       
        $id = Poppoo::$MUUTTUJAA_EI_MAARITELTY;
        $poppoo = new Poppoo($id, $this->get_tietokantaolio());

        // Sitten vain tehdään muutokset ja tallennetaan. Luomispvm asetetaan
        // automaattisesti luomisen yhteydesså.
        $poppoo->set_arvo($para->poppoo_nimi, Poppoo::$sarakenimi_nimi);
        $poppoo->set_arvo($para->poppoo_kommentti, Poppoo::$sarakenimi_kommentti);
        $poppoo->set_arvo($para->poppoo_kayttajatunnus, Poppoo::$sarakenimi_kayttajatunnus);
        $poppoo->set_arvo($para->poppoo_maksimikoko, Poppoo::$sarakenimi_maksimikoko);   
        
        // Tunnuksen varmistus:
        $poppoo->set_kayttajatunnusvahvistus($para->get_poppootunnusvahvistus());
        $tallennus = $poppoo->tallenna_uusi();
        
        // Jos onnistui, näytetään ylläpidon perusnäkymä.
        if($tallennus === Poppoo::$OPERAATIO_ONNISTUI){
            $palauteolio->lisaa_kommentti(
                    Kayttajatekstit::$poppooilmoitus_uuden_poppoon_tallennus_ok);      
            

            // Päivitetään aktiivisuus:
            if($tallentaja instanceof Henkilo){
                $tallentaja->paivita_aktiivisuus(
                                    Aktiivisuus::$POPPOOTALLENNUS_UUSI);
            }
                                    

            // Testausta varten:
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            
            $this->toteuta_kirjautunut_nakyma($palauteolio);
            $this->toteutad_nayta_adminnakyma($palauteolio);
            

        } else{
            $palauteolio->lisaa_virheilmoitus(
                Kayttajatekstit::$poppoovirheilmoitus_uuden_henkilon_tallennus_ei_ok.
                "<br/>".$poppoo->tulosta_virheilmoitukset());
            
            // Testausta varten:
            $palauteolio->set_onnistumispalaute(
                            Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_UUSI);
            
            // Näytetään muutosnäkymä:
            $this->toteutad_nayta_poppoolomake_uusi($palauteolio);
        }
   }
   /** 
    * @param \Palaute $palauteolio
    */
   function toteutad_tallenna_poppoomuokkaus(&$palauteolio){
        $para = $this->get_parametriolio(); 
        $tallentaja = $this->get_olio();
       
        $id = $para->poppoon_id;
        $poppoo = new Poppoo($id, $this->get_tietokantaolio());

        // Sitten vain tehdään muutokset ja tallennetaan. Luomispvm asetetaan
        // automaattisesti luomisen yhteydesså.
        $poppoo->set_arvo($para->poppoo_nimi, Poppoo::$sarakenimi_nimi);
        $poppoo->set_arvo($para->poppoo_kommentti, Poppoo::$sarakenimi_kommentti);
        $poppoo->set_arvo($para->poppoo_maksimikoko, Poppoo::$sarakenimi_maksimikoko);  
        
        // Tunnusta muokataan vain, jos ei tyhjä:
        if($para->poppoo_kayttajatunnus != ""){
            $poppoo->set_arvo($para->poppoo_kayttajatunnus, Poppoo::$sarakenimi_kayttajatunnus);
            
            $poppoo->set_tunnuksen_muokkaus(true);
            
            // Tunnuksen varmistus:
            $poppoo->set_kayttajatunnusvahvistus($para->get_poppootunnusvahvistus());
        }
        
        $tallennus = $poppoo->tallenna_muutokset();
        
        // Jos onnistui, näytetään ylläpidon perusnäkymä.
        if($tallennus === Poppoo::$OPERAATIO_ONNISTUI){
            $palauteolio->lisaa_kommentti(
                    Kayttajatekstit::$poppooilmoitus_muokkauksen_tallennus_ok);      
            
            // Päivitetään aktiivisuus:
            if($tallentaja instanceof Henkilo){
                $tallentaja->paivita_aktiivisuus(
                                    Aktiivisuus::$POPPOOTALLENNUS_MUOKKAUS);
            }
                                    
            // Testausta varten:
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            
            $this->toteuta_kirjautunut_nakyma($palauteolio);
            $this->toteutad_nayta_adminnakyma($palauteolio);
            

        } else{
            $palauteolio->lisaa_virheilmoitus(
                Kayttajatekstit::$poppoovirheilmoitus_muokkauksen_tallennus_ei_ok.
                "<br/>".$poppoo->tulosta_virheilmoitukset());
            
            // Testausta varten:
            $palauteolio->set_onnistumispalaute(
                            Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_MUOKKAUS);
            
            // Näytetään muutosnäkymä:
            $this->toteuta_kirjautunut_nakyma($palauteolio);
            $this->toteutad_nayta_poppoolomake_muokkaus($palauteolio);
        }
   }

   /** 
     * Toteuttaa ylläpitäjän toimesta henkilömuokkauksen.
    * 
    * Huomaa: toteuttaa myös jatkonäkymän, joten siitä ei pidä huolehtia!
     * 
     * @param \Palaute $palauteolio
     */
    public function toteutad_tallenna_henkilomuokkaus(&$palauteolio) {
        
        // Määritetään sopivasti henkilön $tunnusten_muokkaus-muuttuja ja 
        // salavahvistus-muuttujat ja vastaavat arvot:
        $para = $this->get_parametriolio(); 
        
        $muokattava = new Henkilo($para->henkilo_id, $this->get_tietokantaolio());
        
        if($muokattava->olio_loytyi_tietokannasta){
            if(($para->uusktunnus !== "") && ($para->uussalasana !== "")){
                $muokattava->set_tunnusten_muokkaus(Tunnukset::$kumpikin);
                $muokattava->set_salavahvistus($para->salavahvistus);
                $muokattava->set_arvo($para->uusktunnus, 
                                        Henkilo::$sarakenimi_kayttajatunnus);
                $muokattava->set_arvo($para->uussalasana, 
                                        Henkilo::$sarakenimi_salasana);
            } else if($para->uusktunnus !== ""){
                $muokattava->set_tunnusten_muokkaus(Tunnukset::$vain_kayttis);
                $muokattava->set_arvo($para->uusktunnus, 
                                        Henkilo::$sarakenimi_kayttajatunnus);
            } else if($para->uussalasana !== ""){
                $muokattava->set_tunnusten_muokkaus(Tunnukset::$vain_salis);
                $muokattava->set_salavahvistus($para->salavahvistus);
                $muokattava->set_arvo($para->uussalasana, 
                                        Henkilo::$sarakenimi_salasana);
            } else{
                $muokattava->set_tunnusten_muokkaus(Tunnukset::$ei_muokata);
            }
            
            // Sitten vain tehdään muutokset ja tallennetaan:
            $muokattava->set_arvo($para->get_poppoon_id_muokkaus(), 
                                    Henkilo::$sarakenimi_poppoo_id);
            
            $tallennus = $muokattava->tallenna_muutokset();
            
            if($tallennus === Henkilo::$OPERAATIO_ONNISTUI){
                $palauteolio->lisaa_kommentti(
                        Kayttajatekstit::$ilmoitus_tietojen_muokkaustallennus_ok);
                
                $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                
                // Päivitetään aktiivisuus:
                $this->get_kayttaja()->paivita_aktiivisuus(
                                    Aktiivisuus::$HENKILON_TALLENNUS_MUOKKAUS);
                
                $this->toteutad_nayta_poppootiedot($palauteolio);
                
            } else{
                $palauteolio->lisaa_virheilmoitus(
                    Kayttajatekstit::$virheilmoitus_tietojen_muokkaustallennus_ei_ok.
                    "<br/>".$muokattava->tulosta_virheilmoitukset());
                
                $palauteolio->set_onnistumispalaute(
                        Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_MUOKKAUS);
                
                // Näytetään muutosnäkymä:
                $this->toteutad_nayta_henkilon_tietolomake($palauteolio);
            }
            
        } else{
            $this->lisaa_virheilmoitus(Kayttajatekstit::
                                            $virheilmoitus_henkiloa_ei_loytynyt);
            $palauteolio->lisaa_virheilmoitus(Kayttajatekstit::
                                            $virheilmoitus_henkiloa_ei_loytynyt);
            $palauteolio->set_onnistumispalaute(
                        Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_MUOKKAUS);
        }
    }
   //====================== Admin-hommat loppu =================================
}

?>
