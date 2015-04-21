<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Täällä on kaikki Henkilo-luokkaan liittyvät yksikkötestit. Integraatiotestit
 * ovat Kayttajakontrolleritestaus-luokassa.
 *
 * @author kerkjuk_admin
 */
class Kayttajatestaus extends Testialusta{
    
    /** @var Henkilo */
    private $testihenkilo, $testiomistaja;
    
    /** @var Henkilo $testiapuhenkilo */
    private $testiapuhenkilo;
    
    
    
    public static $testi_email = "testiemailiosoite_vaan";    // Ei muuteta tätä!
    
    function __construct($tietokantaolio, $parametriolio){
        parent::__construct($tietokantaolio, $parametriolio, "Henkilo-luokka ja Aktiivisuus");
    }
    
    public function testaa_henkilon_luominen_ja_tallentaminen(){
        $this->lisaa_lihava_kommentti("Luodaan testihenkilö:");
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $this->testihenkilo = 
                Kayttajatestaus::luo_testihenkilo("Matti", "Tuupo", 
                                            "kayttis", "s03HyYXzs_;*:4?!8.",
                                            $this->tietokantaolio);
        if($this->testihenkilo instanceof Henkilo){
            $this->lisaa_kommentti("Henkil&ouml;n luonti onnistui.");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe henkil&ouml;n luomisessa!");
        }
        
        // Katsotaan vielä, onko arvojen asetuksessa ollut ongelmia. Ellei,
        // tallennetaan henkilö tietokantaan:
        if($this->testihenkilo->virheilmoitusten_lkm() == 0){
            $this->lisaa_kommentti("Henkil&ouml;n arvojen asetus onnistui.");
            
            // Tallennus tietokantaan:
            $this->lisaa_lihava_kommentti("Yritetään tallentaa testihenkilöt:");
            if($this->testihenkilo->tallenna_uusi() === Malliluokkapohja::$OPERAATIO_ONNISTUI){
                $this->lisaa_kommentti("Testihenkilön tallennus tietokantaan onnistui!");
            }
            else{
                $this->lisaa_virheilmoitus("Virhe 1. testihenkilön tallennuksessa!!".
                    "<br/> Ilmoitukset: ".$this->testihenkilo->tulosta_virheilmoitukset());
            }
        }
        else{
            $this->lisaa_virheilmoitus("Virhe arvojen asetuksessa: <br/>".
                            $this->testihenkilo->tulosta_virheilmoitukset());
        }
        
        // Omistajahenkilön luominen samalla käyttäjätunnuksella:
        $this->lisaa_kommentti("Yritetään luoda 2. henkil&ouml; samalla
                        käyttäjätunnuksella:");
        $this->testiomistaja = 
                Kayttajatestaus::luo_testihenkilo("Pomo", "Ilkeinen", "kayttis", 
                                                    "salis33",
                                                    $this->tietokantaolio);
        if($this->testiomistaja->tallenna_uusi() === Malliluokkapohja::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: tallennus onnistui, vaikka".
                    " käyttäjätunnus jo käytössä!! Tarkista unique-ominaisuus!".
                    " Onnistuiko 1. henkilon tallennus oikeasti?");
        }
        else{
            
            $this->lisaa_kommentti("Oikein: samalla käyttäjätunnuksella ei ".
                    "voi tallentaa toista henkilöä. ".
                    "<br/> Ilmoitukset: ".$this->testiomistaja->
                                        tulosta_virheilmoitukset());
        }
        
        
        // Omistajahenkilön luominen:
        $this->lisaa_kommentti("Luodaan sitten testiomistaja (siis 2. henkil&ouml;)");
        $this->testiomistaja = 
                Kayttajatestaus::luo_testihenkilo("Pomo", "Ilkeinen", "kayttis2", 
                                                "salis33",$this->tietokantaolio);
        if($this->testiomistaja->tallenna_uusi() === Malliluokkapohja::$OPERAATIO_ONNISTUI){
                $this->lisaa_kommentti("Testiomistajan tallennus tietokantaan onnistui!");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe testiomistajan tallennuksessa!!".
                "<br/> Ilmoitukset: ".$this->testiomistaja->tulosta_virheilmoitukset());
        }
        
        // Apuhenkilön luominen:
        $this->lisaa_kommentti("Luodaan sitten apuhenkilö vielä (siis 3. henkil&ouml;)");
        $this->testiapuhenkilo = 
                Kayttajatestaus::luo_testihenkilo("Kalle", "Kakola", "kayttis3", 
                                                "salis33",$this->tietokantaolio);
        if($this->testiapuhenkilo->tallenna_uusi() === Malliluokkapohja::$OPERAATIO_ONNISTUI){
                $this->lisaa_kommentti("Testiapuhenkilön tallennus tietokantaan onnistui!");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe testiapuhenkilön tallennuksessa!!".
                "<br/> Ilmoitukset: ".$this->testiapuhenkilo->tulosta_virheilmoitukset());
        }
    }
    
    public function testaa_tarkista_kirjautuminen(){
        $this->lisaa_lihava_kommentti("Kirjautumisen tarkistus: löytyykö".
            " tunnukset tietokannasta?");
        //======================================================================
        $this->lisaa_kommentti("Luodaan ensin testihenkilö:");
        $s = "piipii44";
        $k = "kayttis3456";
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $testih = Kayttajatestaus::luo_testihenkilo("Matti", "Tuupo", $k, $s,
                                                    $this->tietokantaolio);
        $testi = $testih->tallenna_uusi();
        if($testi == Henkilo::$OPERAATIO_ONNISTUI){
            $pal = Henkilo::tarkista_kirjautuminen(
                                $k, $s, $this->tietokantaolio);
            
            if($pal == Henkilo::$EI_LOYTYNYT_TIETOKANNASTA){
                $this->lisaa_virheilmoitus("Virhe metodissa ".
                        "'tarkista_kirjautuminen()'! Henkilöä ei löytynyt!");
            }  else{
                
                // Varmistetaan, että palautusarvo on oikean ihmisen id:
                $varmistus = new Henkilo($pal, $this->tietokantaolio);
                if($varmistus->olio_loytyi_tietokannasta &&
                    $varmistus->get_id() == $testih->get_id()){
                    
                    $this->lisaa_kommentti("Metodi 'tarkista_kirjautuminen()'".
                            " toimii oikein!");
                    
                } else{
                    $this->lisaa_virheilmoitus("Virhe metodissa ".
                            "'tarkista_kirjautuminen()'! Varmistus viiraa.");
                }
            }
        }
        else{
            $this->lisaa_virheilmoitus("Virhe henkil&ouml;n luomisessa!");
        }
    }
   
     public function testaa_henkilon_tunnusten_tarkistus(){
        $this->lisaa_lihava_kommentti("Tunnusten tarkistus: yritetään luoda".
                " henkiloita huonoilla tunnuksilla");
        //======================================================================
        $this->lisaa_kommentti("Liian lyhyt salasana:");
        $s = "piipii";
        $k = "kayttis345";
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $testih = Kayttajatestaus::luo_testihenkilo("Matti", "Tuupo", $k, $s,
                                                    $this->tietokantaolio);
        $testi = $testih->tallenna_uusi();
        if($testi == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: henkil&ouml;n luonti onnistui".
                    " salasanalla '".$s."'");
        }
        else{
            $this->lisaa_kommentti("Oikein: ei onnistunut salasanalla '".$s."'".
                            "! Ilmoitukset: ".$testih->tulosta_virheilmoitukset());
        }
        //======================================================================
        $this->lisaa_kommentti("Liian pitk&auml; salasana:");
        $s = "piiippiiippiiippiiippiiippiiippiiippiiippiiippiiipi"; // 51 kirj.
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $testih = Kayttajatestaus::luo_testihenkilo("Matti", "Tuupo", $k, $s,
                                                    $this->tietokantaolio);
        $testi = $testih->tallenna_uusi();
        if($testi == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: henkil&ouml;n luonti onnistui".
                    " salasanalla '".$s."'");
        }
        else{
            $this->lisaa_kommentti("Oikein: ei onnistunut salasanalla '".$s."'".
                            "! Ilmoitukset: ".$testih->tulosta_virheilmoitukset());
        }
        //======================================================================
        $this->lisaa_kommentti("&Auml;&auml;kk&ouml;si&auml; salasanassa:");
        $s = "äitioioi";
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $testih = Kayttajatestaus::luo_testihenkilo("Matti", "Tuupo", $k, $s,
                                                    $this->tietokantaolio);
        $testi = $testih->tallenna_uusi();
        if($testi == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: henkil&ouml;n luonti onnistui".
                    " salasanalla '".$s."'");
        }
        else{
            $this->lisaa_kommentti("Oikein: ei onnistunut salasanalla '".$s."'".
                            "! Ilmoitukset: ".$testih->tulosta_virheilmoitukset());
        }
        //======================================================================
        $this->lisaa_kommentti("Pahoja merkkeja salasanassa 1:");
        $s = "scripti]'tioioi";
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $testih = Kayttajatestaus::luo_testihenkilo("Matti", "Tuupo", $k, $s,
                                                    $this->tietokantaolio);
        $testi = $testih->tallenna_uusi();
        if($testi == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: henkil&ouml;n luonti onnistui".
                    " salasanalla '".htmlspecialchars($s)."'");
        }
        else{
            $this->lisaa_kommentti("Oikein: ei onnistunut salasanalla '".htmlspecialchars($s)."'".
                            "! Ilmoitukset: ".$testih->tulosta_virheilmoitukset());
        }
        //======================================================================
        $this->lisaa_kommentti("Pahoja merkkeja salasanassa 2:");
        $s = "scrip<btitioioi>";
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $testih = Kayttajatestaus::luo_testihenkilo("Matti", "Tuupo", $k, $s,
                                                    $this->tietokantaolio);
        $s_olion = $testih->get_arvo(Henkilo::$sarakenimi_salasana);
        $testi = $testih->tallenna_uusi();
        if($testi == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: henkil&ouml;n luonti onnistui".
                    " salasanalla '".htmlspecialchars($s)."'. Salasana".
                    " oliossa on '".$s_olion."'");
        }
        else{
            $this->lisaa_kommentti("Oikein: ei onnistunut salasanalla '".htmlspecialchars($s)."'".
                            "! Ilmoitukset: ".$testih->tulosta_virheilmoitukset());
        }
        //======================================================================
        //======================================================================
        $this->lisaa_kommentti("Välejä salasanassa:");
        $s = "select id";
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $testih = Kayttajatestaus::luo_testihenkilo("Matti", "Tuupo", $k, $s,
                                                    $this->tietokantaolio);
        $s_olion = $testih->get_arvo(Henkilo::$sarakenimi_salasana);
        $testi = $testih->tallenna_uusi();
        if($testi == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: henkil&ouml;n luonti onnistui".
                    " salasanalla '".htmlspecialchars($s)."'. Salasana".
                    " oliossa on '".$s_olion."'");
        }
        else{
            $this->lisaa_kommentti("Oikein: ei onnistunut salasanalla '".htmlspecialchars($s)."'".
                            "! Ilmoitukset: ".$testih->tulosta_virheilmoitukset());
        }
        //======================================================================
     }
    
    public function testaa_paivita_aktiivisuus_ja_hae_vika_ulosaika(){
        $this->lisaa_kommentti("======================== AKTIIVISUUS ALKAA =======================");
        $this->lisaa_lihava_kommentti("Metodi 'paivita_aktiivisuus':");
        $this->lisaa_kommentti("Lisätään henkilölle uloskirjautumismerkintä.");
        $this->testihenkilo->paivita_aktiivisuus(Aktiivisuus::$ULOSKIRJAUTUMINEN);
        $this->lisaa_lihava_kommentti("Metodi 'hae_vika_ulosaika':");
        $this->lisaa_kommentti("Haetaan saman henkilon vika uloskirjautumisaika".
                " ja tarkistetaan, onko aika järkevä:");
        
        $aika = time();
        $vikaulosaika = $this->testihenkilo->hae_vika_ulosaika();
        
        if(abs($vikaulosaika-$aika < 2)){
            $this->lisaa_kommentti("Aktiivisuustoiminnat ok!");
        } else{
            $this->lisaa_virheilmoitus("Aktiivisuustoiminnoissa vikaa!".
                    " Vikaulosaika=".$vikaulosaika);
        }
        $this->lisaa_kommentti("======================== AKTIIVISUUS LOPPUI =======================");
        
    }
    
   
    /**
     * Luo testiä varten henkilön tietokantaan. Huomaa, että syntymävuosi
     * pidetään aina samana, jotta sen perusteella saadaan siivous tehtyä!
     * 
     * <p>Palauttaa onnistuessaan Henkilo-luokan olion ja muuten
     * Pohja::$VIRHE-arvon.</p>
     */
    public static function luo_testihenkilo($etun, $sukun, $ktunnus, $salis,
                                            $tietokantaolio){
        
        $palaute = Pohja::$VIRHE;
        
        $lempin="Sepi";
        
        $komm="Ei hassumpi kaveri";
        //$sala= md5($salis);
        $eosoite=  Kayttajatestaus::$testi_email;
        $valtuudet= Valtuudet::$NORMAALI;
        $online = 0;
        $asuinmaa = Maat::$belgia;
        
        $id = Henkilo::$MUUTTUJAA_EI_MAARITELTY;
        $poppoo_id = 1;
        $os = "Sepänkatu";
        $puh = "+11234678987";
        
        $henki = new Henkilo($id, $tietokantaolio);
        
        $henki->set_arvo_kevyt($etun, Henkilo::$sarakenimi_etunimi);
        $henki->set_arvo_kevyt($sukun, Henkilo::$sarakenimi_sukunimi);
        $henki->set_arvo_kevyt($lempin, Henkilo::$sarakenimi_lempinimi);
        $henki->set_arvo_kevyt($komm, Henkilo::$sarakenimi_kommentti);
        $henki->set_arvo_kevyt($ktunnus, Henkilo::$sarakenimi_kayttajatunnus);
        $henki->set_arvo_kevyt($salis, Henkilo::$sarakenimi_salasana);
        $henki->set_arvo_kevyt($eosoite, Henkilo::$sarakenimi_eosoite);
        $henki->set_arvo_kevyt($os, Henkilo::$sarakenimi_osoite);
        $henki->set_arvo_kevyt($puh, Henkilo::$sarakenimi_puhelin);
        $henki->set_arvo_kevyt($online, Henkilo::$sarakenimi_online);
        $henki->set_arvo_kevyt($valtuudet, Henkilo::$sarakenimi_valtuudet);
        $henki->set_arvo_kevyt($poppoo_id, Henkilo::$sarakenimi_poppoo_id);
        $henki->set_arvo_kevyt($asuinmaa, Henkilo::$sarakenimi_asuinmaa);
        
        // Salasanan vahvistus tarvitaan myös:
        $henki->set_salavahvistus($salis);

        if($henki instanceof Henkilo && !$henki->olio_loytyi_tietokannasta){
            $palaute = $henki;
        }
        
        return $palaute;
    }
    
    public function testaa_henkilon_muokkaus(){
        $this->lisaa_kommentti("===================== HENKILON MUOKKAUS ALKAA ======================");
        $this->lisaa_lihava_kommentti("Muokataan testihenkilöä:");
        $henk = $this->testiapuhenkilo;
        
        //=================================================================
        $henk->set_tunnusten_muokkaus(Tunnukset::$ei_muokata);
        
        // Yritetään tallentaa ilman muutoksia:
        $this->lisaa_lihava_kommentti("Yritetään tallentaa ilman muutoksia");
        $ilm = $henk->tallenna_muutokset();
        if($ilm == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: henkil&ouml;n muokkaus onnistui, ".
                    "vaikka muutoksia ei ollut! Kommentit: ".
                    $henk->tulosta_kaikki_ilmoitukset() );
                    $henk->tyhjenna_virheilmoitukset();
        }
        else{
            $this->lisaa_kommentti("Oikein! Henkil&ouml;n muokkausta ei tehty ilman muutoksia!".
                    " Ilmoitukset: ".$henk->tulosta_virheilmoitukset());
        }
        //=================================================================
        
        // Tehdään muutoksia ja yritetään uudelleen:
        $henk->set_tunnusten_muokkaus(Tunnukset::$vain_kayttis);
        $henk->set_arvo("Sikakeijo", Henkilo::$sarakenimi_etunimi);
        $henk->set_arvo("Huh", Henkilo::$sarakenimi_kayttajatunnus);
        $henk->set_arvo(Maat::$hollanti, Henkilo::$sarakenimi_asuinmaa);
        $this->lisaa_lihava_kommentti("Tehdään muutoksia etunimeen ja 
            käyttäjätunnukseen ja asuinmaahan ja yritetään uudelleen:");
        
        $ilm = $henk->tallenna_muutokset();
        if($ilm == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Henkil&ouml;n muokkaus onnistui.");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe henkil&ouml;n muokkauksessa!".
                    " Ilmoitukset: ".$henk->tulosta_virheilmoitukset());
        }
        
        //=================================================================
        
        // Tehdään muutoksia (salasana) ja yritetään uudelleen:
        $uusinimi = "Sikareijo";
        $uussala = "pipipipi";
        $this->lisaa_lihava_kommentti("Kokeillaan muuttaa salasanaa:");
        $henk->set_tunnusten_muokkaus(Tunnukset::$vain_salis);
        $henk->set_arvo($uusinimi, Henkilo::$sarakenimi_etunimi);
        $henk->set_arvo($uussala, Henkilo::$sarakenimi_salasana);
        $henk->set_salavahvistus($uussala);
        
        $ilm = $henk->tallenna_muutokset();
        if($ilm == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Henkil&ouml;n muokkaus onnistui.");
            $this->lisaa_kommentti("Haetaan viel arvot tietokannasta:");
            $uusi = new Henkilo($henk->get_id(), $this->tietokantaolio);
            if($uusi->get_arvo(Henkilo::$sarakenimi_etunimi === "Sikareijo") &&
                $uusi->get_arvo(Henkilo::$sarakenimi_salasana)=== md5($uussala)){
                $this->lisaa_kommentti("Oikein! Tietokannassa etunimi on '".
                        $uusi->get_arvo(Henkilo::$sarakenimi_etunimi)."' ja ".
                        "salasana t&auml;sm&auml;&auml;!");
            }
        }
        else{
            $this->lisaa_virheilmoitus("Virhe henkil&ouml;n muokkauksessa!".
                    " Ilmoitukset: ".$henk->tulosta_virheilmoitukset());
        }
        
        //=================================================================
        // Tehdään muutoksia (salasana) ja yritetään uudelleen:
        $uusinimi = "Sikareijo";
        $uussala = "pipipipi";
        $this->lisaa_lihava_kommentti("Kokeillaan muuttaa salasanaa niin, että".
                " vahvistus ei täsmää:");
        $henk->set_tunnusten_muokkaus(Tunnukset::$vain_salis);
        $henk->set_arvo($uusinimi, Henkilo::$sarakenimi_etunimi);
        $henk->set_arvo($uussala, Henkilo::$sarakenimi_salasana);
        $henk->set_salavahvistus($uussala."6");
        
        $ilm = $henk->tallenna_muutokset();
        if($ilm === Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe henkil&ouml;n muokkauksessa!".
                    " Salasana meni läpi väärällä vahvistuksella!");
        }
        else{
            $this->lisaa_kommentti("Oikein! Salasanavirhe huomattu".
                    " tarkistuksessa! Ilmoitukset: ".
                    $henk->tulosta_virheilmoitukset());
                    $henk->tyhjenna_virheilmoitukset();
        }
        //=================================================================
        // Tehdään muutoksia muihin ja yritetään uudelleen:
        $henk->set_tunnusten_muokkaus(Tunnukset::$ei_muokata);
        $henk->set_arvo("Hanhiaivo", Henkilo::$sarakenimi_sukunimi);
        $this->lisaa_lihava_kommentti("Tehdään muutoksia vain sukunimeen
                            ja yritetään uudelleen:");
        
        $ilm = $henk->tallenna_muutokset();
        if($ilm == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Henkil&ouml;n muokkaus onnistui.");
            $this->lisaa_kommentti("Varmistetaan, että tunnukset ovat samoja.");
            
            $uusi = new Henkilo($henk->get_id(), $this->tietokantaolio);
            if($uusi->get_arvo(Henkilo::$sarakenimi_kayttajatunnus === 
                                "Huh") &&
                $uusi->get_arvo(Henkilo::$sarakenimi_salasana)=== md5($uussala)){
                $this->lisaa_kommentti("Oikein! Tietokannassa kayttajatunnus on '".
                        $uusi->get_arvo(Henkilo::$sarakenimi_kayttajatunnus).
                        "' ja salasana t&auml;sm&auml;&auml;!");
            } else{
                $this->lisaa_virheilmoitus("Virhe henkil&ouml;n muokkauksessa!".
                    " Käyttäjätunnus = ".
                        $uusi->get_arvo(Henkilo::$sarakenimi_kayttajatunnus).
                        " ja salasana=".
                        $uusi->get_arvo(Henkilo::$sarakenimi_salasana));
            }
            
        }
        else{
            $this->lisaa_virheilmoitus("Virhe henkil&ouml;n muokkauksessa!".
                    " Ilmoitukset: ".$henk->tulosta_virheilmoitukset());
        }
        $this->lisaa_kommentti("===================== HENKILON MUOKKAUS LOPPU ======================");
    }
    
    public function testaa_henkilon_poistaminen(){
        $this->lisaa_kommentti("===================== POISTO ALKAA ======================");
        $this->lisaa_lihava_kommentti("Poistetaan testihenkilö:");
        
        // Luodaan testihenkilö ja asetetaan arvot paikalleen:
        $ilm = $this->testiapuhenkilo->poista();
        if($ilm == Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Henkil&ouml;n poisto onnistui.");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe henkil&ouml;n poistossa!");
        }
        $this->lisaa_kommentti("===================== POISTON LOPPU ======================");
    }
    
    /**
     * Tyhjentää tietokannasta testihenkilöt ja lisävaltuudet (jotka 
     * poistuvat cascade-ominaisuuden vuoksi testihenkilön mukana tietokannasta). 
     */
    public function tee_loppusiivous(){
        $this->lisaa_kommentti("======================== SIIVOUS ALKAA =======================");
        
        
        
        $poisto_lkm = 
            $this->tietokantaolio->poista_kaikki_rivit(
                                        "henkilot", 
                                        Henkilo::$sarakenimi_eosoite, 
                                        Kayttajatestaus::$testi_email);
        
        
        if($poisto_lkm > 0){
            $this->lisaa_kommentti("Siivous suoritettu onnistuneesti. 
                                Poistettu ".$poisto_lkm." henkil&ouml;&auml;");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe siivouksessa! 
                                Poistettu ".$poisto_lkm." henkil&ouml;&auml;");
        }
        
        
        $this->lisaa_kommentti("======================== SIIVOUS TEHTY ========================");
    }
    /**
     * Tyhjentää tietokannasta mahdolliset testioliot. 
     */
    public function tee_alkusiivous(){
        $this->lisaa_kommentti("===================== ALKUSIIVOUS ALKAA ======================");
        
        
        
        $poisto_lkm = 
            $this->tietokantaolio->poista_kaikki_rivit(
                                        "henkilot", 
                                        Henkilo::$sarakenimi_eosoite, 
                                        Kayttajatestaus::$testi_email);
        
        
        $this->lisaa_kommentti("Alkusiivous suoritettu onnistuneesti. 
                                Poistettu ".$poisto_lkm." henkil&ouml;&auml;");
        
        $this->lisaa_kommentti("====================== ALKUSIIVOUS TEHTY ======================");
    }
    
    function toteuta_kayttajatestit(){
        
        $ots = "Testataan luokkia 'Henkilo', 'Aktiivisuus' yms";
        //===================================================================
        $this->tee_alkusiivous();
        $this->testaa_henkilon_luominen_ja_tallentaminen();
        $this->testaa_henkilon_tunnusten_tarkistus();
        $this->testaa_tarkista_kirjautuminen();
        $this->testaa_paivita_aktiivisuus_ja_hae_vika_ulosaika();
        $this->testaa_henkilon_muokkaus();
        $this->testaa_henkilon_poistaminen();
        $this->tee_loppusiivous();
        //===================================================================
        
        $virheilm = $this->tulosta_virheilmoitukset();
        $sis = $this->tulosta_kaikki_ilmoitukset();
        $virheilm_lkm = $this->virheilmoitusten_lkm();
        
        
        $palaute = new Testipalaute($ots, $virheilm, $sis, $virheilm_lkm);
        
        return $palaute;
    }
    
    
}

?>
