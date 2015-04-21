<?php

/**
 * Tämän luokan tarkoituksena on testata tietokantaluokkien (luokat, jotka
 * mallintavat tietokantariviä ja hoitavat tietokantaoperaatiot) kantaluokkana
 * toimivaa Mallipohjaluokkaa. Testauksessa hyödynnetään luokkaa Testiolio, joka
 * perii Malliluokkapohjan ja toimii kuten todelliset tietokantaluokat
 *
 * @author J-P
 */
class Malliluokkapohjatesti extends Testialusta {
    
    /** @var Testiolio */
    private $testiolio1, $testiolio2, $testiolio3;
    
    public static $muutoshetki = 1234;
    
    public static $luomishetki = 123;   // Ei muuteta tätä, niin siivous helppo.
    
    public function __construct($tietokantaolio, $parametriolio){
        parent::__construct($tietokantaolio, $parametriolio, "Malliluokkapohja");
        $this->tietokantaolio = $tietokantaolio;
    }
    
    /**
     * Tyhjentää testiolio-taulun tietokannasta kuten mahdolliset muut luodut
     * oliot.
     */
    public function tee_loppusiivous(){
        $this->lisaa_kommentti("======================== SIIVOUS ALKAA =======================");
        $poisto_lkm = 
            $this->tietokantaolio->poista_kaikki_rivit(
                                        "testiolio", 
                                        Testiolio::$SARAKENIMI_LUOMISHETKI_SEK, 
                                        Malliluokkapohjatesti::$luomishetki);
        
        $this->lisaa_lihava_kommentti("Siivous suoritettu. Poistettu ".
                                        $poisto_lkm." testioliota");
        $this->lisaa_kommentti("======================== SIIVOUS TEHTY ========================");
    }
    
    public function testaa_luominen_ja_arvon_asetus(){
        $this->lisaa_kommentti("================ LUOMINEN JA ARVON GETSET-METODIT: =============");
        $this->lisaa_lihava_kommentti("Testataan testiolion luominen ja arvogetterit ja -setterit");
        
        $id = Pohja::$MUUTTUJAA_EI_MAARITELTY;
        
        $this->testiolio1 = new Testiolio($this->tietokantaolio, $id);
        
        if($this->testiolio1 instanceof Testiolio){
            $this->lisaa_ilmoitus("Testiolion luonti onnistui!",
                                Ilmoitus::$TYYPPI_ILMOITUS);
        }
        else{
            $this->lisaa_virheilmoitus("Testiolion luonti ei onnistunut!");
        }
        
        //======================================================================
        $this->lisaa_lihava_kommentti("Kokeillaan testiolion kommentti-muuttujan hakua:");
        $kommentti = $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI);
        
        if($kommentti == Testiolio::$MUUTTUJAA_EI_MAARITELTY){
            $this->lisaa_ilmoitus("Kommentti on arvoltaan Testiolio::".
                                "MUUTTUJAA_EI_MAARITELTY, kuten pitääkin!",
                                Ilmoitus::$TYYPPI_ILMOITUS);
        }
        else{
            $this->lisaa_virheilmoitus("Virhe: kommentti = ".$kommentti);
        }
        //======================================================================
        $this->lisaa_lihava_kommentti("Asetetaan arvot oliolle ja haetaan ne kaikki.");
        
        // Asetettavat arvot (luomishetki on yleinen vakio, jota ei muuteta,
        // jotta siivous on helppoa):
        $id= 111;
        $kommentti = "Piipiipaa";
        
        $this->testiolio1->set_id($id);
        $palaute0 = $this->testiolio1->
                            set_arvo(Malliluokkapohjatesti::$luomishetki, 
                                        Testiolio::$SARAKENIMI_LUOMISHETKI_SEK);
        $palaute1 = $this->testiolio1->set_arvo_kevyt(Malliluokkapohjatesti::$muutoshetki, 
                                        Testiolio::$SARAKENIMI_ED_MUUTOS_SEK);
        $palaute2 = $this->testiolio1->set_arvo($kommentti, 
                                        Testiolio::$SARAKENIMI_KOMMENTTI);
        
        // Testataan sekä palautteet että haetaan arvot:
        if($palaute0 === Pohja::$OPERAATIO_ONNISTUI && 
            $palaute1 === Pohja::$OPERAATIO_ONNISTUI &&
            $palaute2 === Pohja::$OPERAATIO_ONNISTUI){
            
            // Huomaa, että alla jostakin syystä luvut ei toimi "==="-merkin kanssa!
            // Selitys: "==="-operaattori vertaa myös tyyppejä, "==" yrittää ensin
            // muuttaa samaksi tyypiksi. Jotenkin ovat eri tyyppejä ilmeisesti.
            if(($this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI)===$kommentti)&&
                ($this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK)==
                                        Malliluokkapohjatesti::$muutoshetki)&&
                ($this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_LUOMISHETKI_SEK)==
                                        Malliluokkapohjatesti::$luomishetki)&&
                ($this->testiolio1->get_id() == $id)){
                
                $this->lisaa_kommentti("Arvot ovat oikein!");
            }
            else{
                $this->lisaa_virheilmoitus("Arvoissa vikaa:
                    id=".$this->testiolio1->get_id().", kommentti=".
                        $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI).
                        ", luomishetki=".
                        $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_LUOMISHETKI_SEK).
                        " ja muutoshetki=".
                        $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK));
            }
        }
        else{
            // Tuo implode on kätevä taulukon tulostamiseen!
            $sarakenimet = implode(", ", $this->testiolio1->get_tietokantarivi()->get_sarakenimet_paitsi_id());
            $this->lisaa_virheilmoitus("Virhe set_arvo-metodissa! <br/>".
                    "Arvot: id=".$this->testiolio1->get_id().", kommentti=".
                        $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI).
                        ", luomishetki=".
                        $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_LUOMISHETKI_SEK).
                        " ja muutoshetki=".
                        $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK).
                        " ja sarakenimet=".$sarakenimet);
        }
        
        //======================================================================
        
        $this->lisaa_lihava_kommentti("Kokeillaan viel&auml; muuttaa arvo".
                " v&auml;&auml;r&auml;n tyyppiseksi, esim muutosaika merkkijonoksi 'uups':");
                    
        $palaute4 = $this->testiolio1->set_arvo("uups", 
                                            Testiolio::$SARAKENIMI_ED_MUUTOS_SEK);
        if($palaute4 === Pohja::$VIRHE && 
            $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK)===
                Pohja::$ARVO_VAARANTYYPPINEN){
            
            // Palautetaan turvallinen arvo:
            $this->testiolio1->set_arvo(Malliluokkapohjatesti::$muutoshetki, 
                                        Testiolio::$SARAKENIMI_ED_MUUTOS_SEK);
            $this->lisaa_kommentti("Kaikki hyvin! Uups ei mennyt l&auml;pi!".
                    " Vanha turvallinen arvo '".
                    Malliluokkapohjatesti::$muutoshetki."' palautettu.");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe: muutosarvo=".
                   $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK));
        }
        
        $this->lisaa_ilmoitus("===================== LUOMISTESTIN LOPPU =========================<br/>",
                                Ilmoitus::$TYYPPI_ILMOITUS);
    }
    
    private function testaa_tallennus_tietokantaan(){
        $this->lisaa_kommentti("====================== TALLENNUSTESTI ALKAA =======================");
        $this->lisaa_lihava_kommentti("Yritetään eka tallentaa puutteellista".
                " oliota, eli jonka yksi muuttuja on epämääritelty");
        
        $this->lisaa_kommentti("Muutetaan ed_muutos_sek epämääräiseksi");
        $this->testiolio1->set_arvo_kevyt(
                    Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY,
                    Testiolio::$SARAKENIMI_ED_MUUTOS_SEK);
        
        if($this->testiolio1->tallenna_uusi() == 
                                        Malliluokkapohja::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Tallennus meni läpi, vaikkei ".
                    "kaikki arvot määriteltyjä!");
        }
        else{
            $this->lisaa_kommentti("Ei onnistunut, eli oikein! Testiolio ".
                    "sai seuraavan virheilmoituksen: ".
                    $this->testiolio1->tulosta_virheilmoitukset());
            // Tyhjennetään olion virheilmoitukset
            $this->testiolio1->tyhjenna_kaikki_ilmoitukset();
        }
        
        
        $this->lisaa_lihava_kommentti("Yritetään tallentaa kunnon testiolio 
                                    (kaikki arvot määriteltyjä) tietokantaan");
        
        $this->testiolio1->set_arvo_kevyt(Malliluokkapohjatesti::$muutoshetki, 
                                        Testiolio::$SARAKENIMI_ED_MUUTOS_SEK);
        
        $pal = $this->testiolio1->tallenna_uusi();
        if($pal === Testiolio::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Tallennus tietokantaan onnistui!");
            $this->lisaa_lihava_kommentti("Haetaan tallennettu testiolio tietokannasta:");
            
            $haettu = new Testiolio($this->tietokantaolio, $this->testiolio1->get_id()); 
            if($haettu->olio_loytyi_tietokannasta){
                $this->lisaa_kommentti("Haku tietokannasta onnistui! Haettiin".
                        " muun muassa kommentti=".$haettu->
                            get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI).
                        " ja id=".$haettu->get_id().
                        " sekä id_tietokanta=".$haettu->get_id_tietokanta());
            }
            else{
                $this->lisaa_virheilmoitus("Testiolion haku tietokannasta ei ".
                        "onnistunut! Esimerkiksi luodun olion kommentti=".$haettu->
                                    get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI).
                        " ja id=".$haettu->get_id());
            }
           
        }else{
            $this->lisaa_virheilmoitus("Testiolion tallennus tietokantaan ei onnistunut!");
        }
        
        
        
        $this->lisaa_kommentti("====================== TALLENNUSTESTIN LOPPU =======================<br/>");
    }
    
    public function testaa_muokkaustallennus(){
        $this->lisaa_kommentti("================= MUOKKAUSTALLENNUSTESTI ALKAA ==================");
        $this->lisaa_lihava_kommentti("Yritetään tallentaa olemattomat muutokset tietokantaan");
        
        $pal = $this->testiolio1->tallenna_muutokset();
        if($pal === Testiolio::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Muuttamattomien tallennus meni läpi!");
        }
        else{
            $this->lisaa_kommentti("Ei onnistunut (OIKEIN). Ilmoituksiin".
                    " tuli seuraava viesti: <br/>".
                    $this->testiolio1->tulosta_virheilmoitukset());
            
            // Tyhjennetään olion virheilmoitukset
            $this->testiolio1->tyhjenna_kaikki_ilmoitukset();
        }
        
        $this->lisaa_kommentti("================================================================");
        $this->lisaa_lihava_kommentti("Yritetään tallentaa vaillinaista oliota".
                " oliota, eli jonka yksi muuttuja on epämääritelty
                    ja yhtä muutettu kunnolla.");
        
        $this->lisaa_kommentti("Muutetaan ed_muutos_sek epämääräiseksi.
            Tällöin sen vanhan arvon pitäisi pysyä voimassa.");
        $this->testiolio1->set_arvo_kevyt(
                    Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY,
                    Testiolio::$SARAKENIMI_ED_MUUTOS_SEK);
        
        $this->lisaa_kommentti("Kommentille annetaan kunnon muutettu arvo");
        
        // Tämä on kunnon muutettu arvo:
        $uusi_kommentti = "Taa on muokattu kommentti";
        $this->testiolio1->set_arvo($uusi_kommentti, 
                                        Testiolio::$SARAKENIMI_KOMMENTTI);
        
        $tallennuspalaute = $this->testiolio1->tallenna_muutokset();
        if($tallennuspalaute === Malliluokkapohja::$OPERAATIO_ONNISTUI &&
            $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI) == 
                                        $uusi_kommentti &&
            $this->testiolio1->get_arvo(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK) == 
                                        Malliluokkapohjatesti::$muutoshetki){
            
            $this->lisaa_kommentti("Oikein! Kommenttia muutettiin, 
                            ed_muutos_sek-muuttujaa ei!");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe: yhden muutoksen muokkaustallennus 
                    ei onnistunut. Testiolio ".
                    "sai seuraavan virheilmoituksen: ".
                    $this->testiolio1->tulosta_virheilmoitukset());
            
            // Tyhjennetään olion virheilmoitukset
            $this->testiolio1->tyhjenna_kaikki_ilmoitukset();
        }
        
        $this->lisaa_kommentti("================================================================");
        
        
        $this->lisaa_lihava_kommentti("Tehdään oikeita muutoksia ja ".
                "yritetään tallentaa muutokset tietokantaan");
        
        // Muutetaan testiolio1:n id, muutoshetki ja kommentti. Näistä
        // id:n muutos EI saa tallentua tietokantaan ja muiden muutokset kyllä.
        $uusi_id = 12343456;    // Tämä ei saa aiheuttaa muutoksia!
        $uusi_muutoshetki = time();
        
        // Arvot oliolle:
        $this->testiolio1->set_id($uusi_id);   
        $this->testiolio1->set_arvo($uusi_muutoshetki, 
                                        Testiolio::$SARAKENIMI_ED_MUUTOS_SEK);
        
        
        // Muutosten tallennus tietokantaan:
        $pal = $this->testiolio1->tallenna_muutokset();
        
        if($pal === Testiolio::$OPERAATIO_ONNISTUI){
            
            $this->lisaa_kommentti("Muutosten tallennus tietokantaan onnistui!");
            $this->lisaa_lihava_kommentti("Haetaan muutetut arvot tietokannasta:");
            
            $haettu = new Testiolio($this->tietokantaolio, 
                                    $this->testiolio1->get_id_tietokanta()); 
            if($haettu->olio_loytyi_tietokannasta && 
                $haettu->get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI) === 
                                                    "Taa on muokattu kommentti"){
                
                $this->lisaa_kommentti("Haku tietokannasta onnistui! Arvot: ".
                        " id=".$haettu->get_id().
                        ", luomishetki=".
                        $haettu->get_arvo(Testiolio::$SARAKENIMI_LUOMISHETKI_SEK).
                        ", muutoshetki=".
                        $haettu->get_arvo(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK).
                        " ja kommentti=".$haettu->
                            get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI));
            }
            else{
                $this->lisaa_virheilmoitus("Testiolion haku tietokannasta ".
                        " tai muutosten tallennus ei ".
                        "onnistunut! Arvot: ".
                        " id=".$haettu->get_id().
                        ", luomishetki=".
                        $haettu->get_arvo(Testiolio::$SARAKENIMI_LUOMISHETKI_SEK).
                        ", muutoshetki=".
                        $haettu->get_arvo(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK).
                        " ja kommentti=".$haettu->
                            get_arvo(Testiolio::$SARAKENIMI_KOMMENTTI));
            }
           
        }else{
            $this->lisaa_virheilmoitus("Testiolion muutosten tallennus ".
                    "tietokantaan ei onnistunut! Ilmoitukset: <br/>".
                    $this->testiolio1->tulosta_virheilmoitukset());
        }
        
        
        
        $this->lisaa_kommentti("================ MUOKKAUSTALLENNUSTESTIN LOPPU =================");
    }
    
    public function testaa_poisto(){
        $this->lisaa_kommentti("<br/>===================== POISTOTESTIN ALKU ======================");
        $this->lisaa_lihava_kommentti("Poistetaan luotu testiolio tietokannasta");
        $poistettava = $this->testiolio1;
        
        if($poistettava->poista() === Testiolio::$OPERAATIO_ONNISTUI){
            
            // Varmistetaan vielä:
            $olematon = new Testiolio($this->tietokantaolio, 
                                        $poistettava->get_id_tietokanta());
            if(!$olematon->olio_loytyi_tietokannasta){
                $this->lisaa_kommentti("Tietojen poisto tietokannasta onnistui!");
            }
            else{
                $this->lisaa_virheilmoitus("Tietojen poisto tietokannasta ei onnistunut!".
                                " Poistettu olio löytyi kuitenkin!!");
            }
        }else{
            $this->lisaa_virheilmoitus("Tietojen poisto tietokannasta ei onnistunut!".
                                $poistettava->tulosta_virheilmoitukset());
        }
        
        $this->lisaa_kommentti("===================== POISTOTESTIN LOPPU ======================<br/>");
    }
    
    public function toteuta_malliluokkapohjatestit(){
        
        $ots = "Malliluokkapohjaa testataan Testiolio-luokan avulla";
        
        //=====================================================================
        $this->testaa_luominen_ja_arvon_asetus();
        $this->testaa_tallennus_tietokantaan();
        $this->testaa_muokkaustallennus();
        $this->testaa_poisto();
        $this->tee_loppusiivous();
        //=====================================================================
        $virheilm = $this->tulosta_virheilmoitukset();
        
        $virheilm_lkm = $this->virheilmoitusten_lkm();
        
        $sis = $this->tulosta_kaikki_ilmoitukset();
        
        $palaute = new Testipalaute($ots, $virheilm, $sis, $virheilm_lkm);
        
        return $palaute;
    }
}

?>
