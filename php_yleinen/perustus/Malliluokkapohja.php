<?php
/**
 * Description of malliluokkapohja
 * Tämä luokka toimii perittävänä pohjana ns. entiteettiluokille, eli
 * luokille, jotka vastaavat muuttujien tietokantaoperaatioista. Yksi luokka
 * vastaa yleensä yhtä relaatiotietokannan taulua (poikkeuksiakin voi olla).
 * 
 * Toteuttaa Ilmoitusrajapinnan, joka määrittelee ilmoitus-/viestimetodien rajapinnat.
 * Versionumero antaa sen päivämäärän, jolloin luokkaan on tehty muutoksia.
 * 
 * @author J-P 2012
 * @version 20150316
 */
abstract class Malliluokkapohja extends Pohja{
    
    /** @var Tietokantaolio */
    protected $tietokantaolio;

    // Tämä asetetaan tallennettaessa tietokantaan, eikä sen koommin muuteta!
    private $id_tietokanta; 
    
    /**
     * Tämän arvona on joko tietokantahaun tuottaman tieto-olio
     * tahi arvo $MUUTTUJAA_EI_MAARITELTY
     */
    //protected $tk_olio;   // Ei tarpeen.

    protected $tk_taulunimi;
    
    // Tietokantarivi-luokan olio, joka sisältää kaikki rivin tiedot 
    // Tietokantasolu-luokan olioiden muodossa.
    protected $tietokantarivi;
    
            
    // Tämä kertoo sen, onko olion tiedot haettu onnistuneesti tietokannasta.
    // Tätä ei aina voida tehdä, eikä ole tarkoituskaan.
    public $olio_loytyi_tietokannasta;
    
    // Kätevä tietokantahauissa joskus:
    public static $EI_LOYTYNYT_TIETOKANNASTA = -3;
    
    // Tietokannan sarakenimet:
    public static $SARAKENIMI_ID = "id";    // Tämä näin aina!
   
    
    /**
     * Konstruktorin "overloading" eli eri konstruktorit eri parametreille
     * ei ole tuettu PHP:ssä. Kierrän tämän antamalla parametreille, joita
     * ei käytetä, vakioarvon, joka tarkoittaa, ettei parametri käytössä.
     *
     * @param Tietokantaolio $tietokantaolio
     * @param int $id olion tunniste
     * @param string $taulunimi tietokantataulun nimi
     * @param type $tietokantasolut taulukko Tietokantasolu-luokan olioita. 
     * Huomaa, että jokaista tietokantataulukon saraketta kohti pitää olla
     * tasan yksi solu! Muuten ei operaatiot toimi.
     */
    function __construct($tietokantaolio, $id, $taulunimi, $tietokantasolut){
        parent::__construct();
        
        // Ks. metodi "nouki_arvot_tk_osumataulukosta":
        $this->id_tietokanta = Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY;
        $this->tk_taulunimi = $taulunimi;
        $this->olio_loytyi_tietokannasta = false;
        
        // Tämä on tärkeä! Tietokantasolut tuovat tiedot tietokannan soluista.
        $this->tietokantarivi = new Tietokantarivi($taulunimi, $tietokantasolut);
        
        if($tietokantaolio instanceof Tietokantaolio){
            $this->tietokantaolio = $tietokantaolio;
        }
        else{
            $this->tietokantaolio = Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY;
        }
        
        // Asetetaan id. Jos määritelty, haetaan myös tiedot tietokannasta.
        if(is_numeric($id) && ($id > 0) && 
            ($id != Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY)){
            
            $this->set_id($id);
            
            $tiedot = $this->tietokantaolio->
                    hae_eka_osuma_taulukkona($this->tk_taulunimi, 
                                            Malliluokkapohja::$SARAKENIMI_ID, 
                                            $id);
            
            // Tämä asettaa $olio_loytyi_tietokannasta-muuttujan arvon
            // onnistumisen mukaan. 
            $this->nouki_arvot_tk_osumataulukosta($tiedot);
        } else{
            
            // Asetetaan id ei-määritellyksi. Tällä on merkitystä, jos
            // id on ollut ei-numero tai muu negatiivinen luku.
            $this->set_id(Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY);
        }
        
        //$this->tk_olio = Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY;
    }
    
    public function get_id_tietokanta(){
        return $this->id_tietokanta;
    }
    
    /**
     * Käytä tätä vain uuden olion tietokantaan tallennuksen yhteydessä! Voidaan
     * tehdä vain, kun muuttujan arvo on Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY.
     * @param type $uusi
     */
    protected function set_id_tietokanta($uusi){
        if(isset($uusi) && 
            $this->id_tietokanta === Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY){
            $this->id_tietokanta = $uusi;
        }
    }


    // Getterit ja setterit:
    /** Id:n tietokanta luo automaattisesti. Setteriä tarvitaan kuitenkin,
     * kun tiedot haetaan tietokannasta.
     */
    public function get_id(){
        return $this->tietokantarivi->get_arvo(Malliluokkapohja::$SARAKENIMI_ID);
    }
    
    /**
     * Asettaa id:n arvon tietokantasoluun. Tämä ei tule koskaan käyttäjältä,
     * joten voidaan käyttää kevyttä asetusmetodia:
     * @param type $uusi 
     */
    public function set_id($uusi){
        $this->set_arvo_kevyt($uusi, Malliluokkapohja::$SARAKENIMI_ID);
    }
    
    public function get_tk_taulunimi(){
        return $this->tk_taulunimi;
    }
    
    /** 
     * Palauttaa sen Tietokantasolu-olion arvon, jonka sarakenimi-muuttujan
     * arvo täsmää parametrina annettuun. Ellei löydy, palauttaa arvon
     * Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY.
     */
    public function get_arvo($sarakenimi){
        return $this->tietokantarivi->get_arvo($sarakenimi);
    }
    
    /** 
     * Palauttaa sen Tietokantasolu-olion arvon, jonka sarakenimi-muuttujan
     * arvo täsmää parametrina annettuun. Ellei löydy, palauttaa arvon
     * Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY.
     * 
     * Arvo on html-encoded (htmlspecialchars) ja mahdollisesti muutenkin muotoiltu, 
     * jotta se näkyy hyvin html-sivulla häiritsemättä koodia.
     * 
     * Vinoviivat on poistettu myös, mikä saattaa mahdollisesti aiheuttaa
     * turvallisuusaukon, jos arvoa käytetään suoraan esim. tietokantahakuun.
     * 
     * VARMUUDEN VUOKSI tätä suositellaan käytettäväksi vain TULOSTETTAESSA
     * tekstiä käyttäjän luettavaksi / muokattavaksi esim. input-elementtiin. 
     * Muuten kannattaa käyttää get_arvo-metodia.
     */
    public function get_html_encoded_arvo($sarakenimi){
        
        $muotoiltu = $this->tietokantarivi->get_html_encoded_arvo($sarakenimi);
        
        return $muotoiltu;
    }
    
    /** 
     * Palauttaa sen Tietokantasolu-olion arvon, jonka sarakenimi-muuttujan
     * arvo täsmää parametrina annettuun. Ellei löydy, palauttaa arvon
     * Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY.
     * 
     * Huom: Html-erikoismerkit koodataan htmlspecialchars()-metodin avulla.
     * Sekä yksin- että kaksinkertaiset heittomerkit koodataan (ENT_QUOTES).
     */
    /*public function get_arvo_htmlspecialchars($sarakenimi){
        return htmlspecialchars($this->tietokantarivi->get_arvo($sarakenimi),
                                ENT_QUOTES);
    }*/
    
    /**
     * Käyttäjältä tietokantaan:
     * Asettaa uuden arvon ja palauttaa onnistumisesta joko Pohja::$VIRHE tai 
     * Pohja::$OPERAATIO_ONNISTUI. Tarkistaa ja puhdistaa syötteen (ks. 
     * Tietokantasolu).
     * 
     * Huom! Ohjelmoija käyttää aina tätä set_arvo()-metodia, joka sitten kutsuu
     * Tietokantarivin ja Tietokantasolun samannimisiä metodeita.
     */
    public function set_arvo($uusi, $sarakenimi){
        return $this->tietokantarivi->set_arvo($uusi, $sarakenimi);
    }
    
    /**
     * Tietokannasta valmiit arvot oliolle:
     * Asettaa uuden arvon ja palauttaa onnistumisesta joko Pohja::$VIRHE tai 
     * Pohja::$OPERAATIO_ONNISTUI. Ei tee tarkistuksia!
     */
    public function set_arvo_kevyt($uusi, $sarakenimi){
        return $this->tietokantarivi->set_arvo_kevyt($uusi, $sarakenimi);
    }
    
    /**
     * Palauttaa Tietokantarivi-luokan olion.
     * @return Tietokantarivi
     */
    public function get_tietokantarivi(){
        return $this->tietokantarivi;
    }

    /**
     * Palauttaa sen Tietokantasolu-luokan olion, jonka sarakenimi vastaa
     * parametria, tai arvon Pohja::$MUUTTUJA_EI_MAARITELTY, ellei solua löydy.
     * 
     * Tämä nopeuttaa vähän koodaamista, koska ei tarvitse mennä 
     * tietokantarivin kautta.
     * 
     * @param type $sarakenimi
     * @return Tietokantasolu
     */
    public function get_tietokantasolu($sarakenimi){
        return $this->tietokantarivi->get_tietokantasolu($sarakenimi);
    }
    
    /**
     * Palauttaa aina taulukon, joka sisältää tietokantasolut tai sitten on tyhjä.
     * Nopeutusmetodi vain.
     * @return array
     */
    public function get_tietokantasolut(){
        return $this->tietokantarivi->get_tietokantasolut();
    }


    /**
     * Huom: tallennettaessa suoritetaan yleensä samat tarkistukset
     * (ks. set_arvo()-metodi Tietokantasolu-luokassa), jolloin tämä
     * tarpeeton. Tällä on lähinnä merkitystä, jos ennen tallennusyritystä
     * halutaan tarkistus & putsaus tehdä.
     * 
     * Tarkistaa lukumuotoisen muuttujan (on määritelty ja oikean sorttinen)
     * Tässä ei oikein kovin tarkasti määrätä, koska esimerkiksi joskus
     * negatiivinen arvo halutaan sallia.
     * 
     * @param type $arvo
     * @param type $putsaa true, jos halutaan putsata vaaralliset merkit.
     * Samalla trimmataan tyhjät päistä pois (tarpeen?)
     * 
     * @param string $kentan_nimi Jos epätyhjä, lisätään virheilmoitukseen, jotta
     * käyttäjä tietää, mistä virhe tuli. 
     */
    function lukumuotoinen_muuttuja_ok(&$arvo, $putsaa, $kentan_nimi){
        $palaute = false;
    
        // HUOM! Alla is_int ei toimi sovelluksessa, koska ilm. arvot eivät
        // HTTP-kutsun kautta tule int-tyyppisinä.
        if(isset($arvo)&& is_numeric($arvo) && 
            ($arvo != Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY)){
            
            if($putsaa){
                $arvo = $this->tietokantaolio->real_escape_string(trim($arvo));
            }

            $palaute = true;
        }

        // Virhetapauksessa lisätään ilmoitus:
        if(!$palaute){
            $ilmoitus = $kentan_nimi.
                Perustustekstit::$syotteen_tarkistusvirhe." ".$arvo;
            $this->lisaa_ilmoitus($ilmoitus, Ilmoitus::$TYYPPI_VIRHEILMOITUS);
        }

        return $palaute;
    }
    /**
     * Huom: tallennettaessa suoritetaan yleensä samat tarkistukset
     * (ks. set_arvo()-metodi Tietokantasolu-luokassa), jolloin tämä ei tarpeen. 
     * Tällä on lähinnä merkitystä, jos ennen tallennusyritystä
     * halutaan tarkistus & putsaus tehdä. Merkkijonon tyhjyys saadaan tällä
     * kiinni.
     * 
     * Tarkistaa merkkijonomuotoisen muuttujan (on määritelty ja oikean sorttinen).
     * Halutessa putsataan vaaralliset merkit (2. parametri) ja kolmas parametri
     * ratkaisee sen, hyväksytäänkö tyhjä merkkijono. Neljäs selkeyttää
     * virheilmoitusta.
     * 
     * Puhdistetaan myös tyhjät ja vaaralliset merkit (html sallitaan)
     *
     * @param type &$arvo Tarkistettava arvo, jonka muutokset heitetään eteenpäin.
     * @param type $putsaa true, jos halutaan putsata vaaralliset merkit.
     * Samalla trimmataan ja poistetaan vinoviivat (tarpeen?)
     * @param type $tyhja_ok true, jos tyhjä merkkijono halutaan hyväksyä, muuten
     * false.
     * @param string $kentan_nimi Jos epätyhjä, lisätään virheilmoitukseen, jotta
     * käyttäjä tietää, mistä virhe tuli. Esimerkiksi lomakkeen kohtaan viittava
     * nimi.
     * @return boolean 
     */
    function mjmuotoinen_muuttuja_ok(&$arvo, $putsaa, $tyhja_ok, $kentan_nimi){
        $palaute = false;
        
        if(isset($arvo)&&
            is_string($arvo)&&
            ($arvo != Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY)){
            
            // Putsaus:
            if($putsaa){
               $arvo = 
                $this->tietokantaolio->real_escape_string(stripslashes(trim($arvo)));
            }

            // Tarkistetaan tarvittaessa, onko arvo tyhjä ja tehdään tarvittavat.
            if($tyhja_ok){
                $palaute = true;
            }
            else{
                if(!empty ($arvo)){
                    $palaute = true;
                }
                else{
                    $palaute = false;
                }
            }
        }

        // Virhetapauksessa lisätään ilmoitus:
        if(!$palaute){
            if(empty($arvo)){
                $arvo = Perustustekstit::$tyhja_merkkijono;
            }
            $ilmoitus = $kentan_nimi.
                        Perustustekstit::$syotteen_tarkistusvirhe." (".$arvo.")";
            $this->lisaa_ilmoitus($ilmoitus, Ilmoitus::$TYYPPI_VIRHEILMOITUS);
        }

        return $palaute;
    }
    /**
     * Asettaa olion muuttujille samat arvot kuin ilman tietokantatietoja
     * luodessa:
     */
    function nollaa_muuttujat(){
        $this->id = Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY;
        $this->tk_olio = Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY;
        $this->virheilmot = array();
    }
    
    
    
    /** 
     * CRUD toteutetaan jo täällä. Toteutukset voi toki kirjoittaa
     * asianomaisessa luokassa uusiksi tarvittaessa.
     * 
     * Huomaa, ettei tietojen tarkistusta tehdä täällä varsinaisesti, mutta
     * standarditarkastus tehdään Tietokantasolu-luokassa (ks. Tietokantasolun 
     * set_arvo()).
     * Toteuta tarvittaessa tarkempi tarkistus perityssä luokassa!
     * 
     * Palauttaa onnistumisen mukaan joko Malliluokkapohja::$OPERAATIO_ONNISTUI
     * tai Malliluokkapohja::$VIRHE.
     * 
     * Korjaus 16.3.2015: nyt metodi asettaa onnistuneen tallennuksen jälkeen
     * oliolle kentän $this->olio_loytyi_tietokannasta arvoksi true. Oli 
     * unohtunut näemmä.
     * 
     * @return type 
     */
    public function tallenna_uusi(){
        $palaute = Malliluokkapohja::$VIRHE;
        
        // Jos olio on jo tietokannassa, ei tallenneta uutena:
        if($this->olio_loytyi_tietokannasta){
            $this->lisaa_virheilmoitus("Olio on jo tietokannassa!");
            return $palaute;
        }
        
        // Muussa tapauksessa jatketaan:
        $sarakenimet = $this->tietokantarivi->get_sarakenimet_paitsi_id();
        $arvot = $this->tietokantarivi->get_arvot_paitsi_id();
        
        // Käydään ei-id-solut läpi ja ilmoitetaan, jos jollakin eivät tiedot 
        // ole kunnossa. Kunnollisia pitää olla sizeof($sarakenimet) kpl.
        $kunnolliset = 0;
        foreach ($this->get_tietokantasolut() as $solu) {
            if($solu instanceof Tietokantasolu){
                if($solu->get_sarakenimi() != Malliluokkapohja::$SARAKENIMI_ID){
                    if(!$solu->tiedot_ok()){
                        if($solu->get_arvo() === Pohja::$MUUTTUJAA_EI_MAARITELTY){
                            $this->lisaa_virheilmoitus("'".$solu->get_sarakenimi().
                            "'".Perustustekstit::$syotteen_tarkistusvirhe);
                        } else if($solu->get_arvo() === Pohja::$ARVO_TYHJA){
                            $this->lisaa_virheilmoitus("'".$solu->get_sarakenimi().
                            "'".Perustustekstit::$muuttujan_arvo_tyhja);
                        } else{
                            $this->lisaa_virheilmoitus("'".$solu->get_sarakenimi().
                            "'".Perustustekstit::$muuttujan_arvo_vaarantyyppinen);
                        }
                    }
                    else{
                        $kunnolliset++;
                    }
                }
            }
        }
        
        // Katsotaan, jotta sarakenimien ja arvojen lkm täsmää.
        // Plus 1 tulee siitä, että id-saraketta ei ole täällä mukana.
        // Tässä jäädään kiinni myös, ellei jokin arvo ole määritelty, koska
        // silloin sen sarakenimeä ei palauteta.
        if(($this->tietokantarivi->get_sarakkeiden_lkm() === 
                                                    (sizeof($sarakenimet)+1)) &&
             (sizeof($sarakenimet) === sizeof($arvot) &&
              ($kunnolliset == sizeof($sarakenimet)))){

            $tallennuspalaute =
                    $this->tietokantaolio->tallenna_rivi($this->tietokantarivi);

            // Jos onnistui:
            if($tallennuspalaute === Tietokantaolio::$HAKU_ONNISTUI){

                // Otetaan ylös tallennetun id:
                $uuden_id = $this->tietokantaolio->get_insert_id();
                $this->set_id($uuden_id);
                $this->set_id_tietokanta($uuden_id);
                $this->olio_loytyi_tietokannasta = true;
                $palaute = Malliluokkapohja::$OPERAATIO_ONNISTUI;
            }
            else{
                $this->lisaa_virheilmoitus("Tietokantavirhe uuden tallennuksessa.".
                    " Arvot oikein? ". 
                    " Tietokantakysely: ".$tallennuspalaute); // Testausta
            }
        }
        else{
            /* Tämä voi tulla lähinnä testaajalle, joten viesti voi auttaa.*/
            $this->lisaa_virheilmoitus(
                                Perustustekstit::$virhe_arvo_vaarantyyppinen);
            
        }
        return $palaute;
    }
    
    /**
     * Tallentaa arvot tietokantaan eli muuttaa jo olemassaolevaa tietokantariviä.
     * Se, onko arvoja muutettu, tarkistetaan ensin, eikä tallennukseen mennä,
     * ellei vähintään yhtä arvoa ole muutettu (id:tä ei lasketa). Samalla 
     * tarkistetaan myös, että olio ylipäätään on tietokannassa.
     * 
     * Virhetilanteessa lähetetään ilmoituksiin kuvaus, joka on suoraan
     * näytettävissä käyttäjälle.
     * 
     * Palauttaa onnistumisen
     * mukaan joko ::$OPERAATIO_ONNISTUI tai ::$VIRHE. Viimeksi
     * mainitussa tapauksessa kannattaa tarkistaa tarkemmat kommentit olion
     * virheilmoituksista.
     * 
     * @return type 
     */
    public function tallenna_muutokset(){
        $palaute = Malliluokkapohja::$VIRHE;
        $max_muutosrivilkm = 1;
        
        // Luodaan uusi, koska id:llä voi periaatteessa olla muutettu arvo!
        $tietokantasoluehto = 
            new Tietokantasolu(Malliluokkapohja::$SARAKENIMI_ID, 
                                        Tietokantasolu::$mj_tyhja_EI_ok,
                                        $this->tietokantaolio);
        
        // Otetaan varmasti oikea id mukaan:
        $tietokantasoluehto->set_arvo_kevyt($this->id_tietokanta);
        
        if($tietokantasoluehto instanceof Tietokantasolu){
            
            // Haetaan vertailuolio (Tietokantarivi) tietokannasta, johon 
            // tämän olion tietokantarivin arvoja voidaan
            // verrata ja muutokset huomata. Huomaa, että seuraava toimii vain,
            // ellei alkuperäinen id ole muuttunut! Siksi erillinen muuttuja
            // "id_tietokanta", jota ei voi muuttaa!
            $on_muutoksia = false;
            
            $alkup_tietokantarivi = 
                $this->hae_tietokantarivi_tietokannasta($this->id_tietokanta);
            
            $arvot_kunnossa = true;     // Vain true päästää tallentamaan.
            
            // Ellei tietoja löydy tai niitä ei ole muokattu (id:tä ei lasketa),
            // tai jos muokattu on ei-määritelty,
            // ei yritetä tallentaa, vaan heitetään virheilmoitus.
            if($alkup_tietokantarivi instanceof Tietokantarivi){
                foreach ($alkup_tietokantarivi->get_tietokantasolut() as $solu) {
                    if($solu instanceof Tietokantasolu && 
                        $solu->get_sarakenimi() != Malliluokkapohja::$SARAKENIMI_ID){
                        
                        // Haetaan tietokantasolu, jota on voitu muokata:
                        $mahd_muokattu_solu = 
                            $this->get_tietokantasolu($solu->get_sarakenimi());
                        
                        // Jos havaitaan kunnollinen muokkaus, palautetaan true.
                        if($mahd_muokattu_solu->tiedot_ok() &&
                            $solu->get_arvo() != $mahd_muokattu_solu->get_arvo()){

                            $on_muutoksia = true;
                            $mahd_muokattu_solu->set_on_muokattu(true);
                            
                        } else if(!$mahd_muokattu_solu->tiedot_ok()){
                            
                            // Arvo saa olla määrittelemätön, muttei väärä!
                            if($mahd_muokattu_solu->get_arvo() == 
                                            Pohja::$ARVO_VAARANTYYPPINEN){
                                $arvot_kunnossa = false;
                                $this->lisaa_virheilmoitus(
                                    $mahd_muokattu_solu->get_sarakenimi().
                                    Perustustekstit::
                                    $muuttujan_arvo_vaarantyyppinen);
                                
                            // Tyhjä silloin kun ei sallittu:    
                            } else if($mahd_muokattu_solu->get_arvo() == 
                                            Pohja::$ARVO_TYHJA){
                                $arvot_kunnossa = false;
                                $this->lisaa_virheilmoitus(
                                    $mahd_muokattu_solu->get_sarakenimi().
                                    Perustustekstit::
                                    $muuttujan_arvo_tyhja);
                            }
                        } else{
                            // Arvoa ei muutettu, eli ei tarvitse sitä tallentaa.
                            $mahd_muokattu_solu->set_on_muokattu(false);
                        }
                    }
                }
                
                if($on_muutoksia && $arvot_kunnossa){
                    $tallennuspalaute = $this->tietokantaolio->update_rivi(
                                                            $this->tietokantarivi, 
                                                            $tietokantasoluehto, 
                                                            $max_muutosrivilkm);
                    // Jos onnistui:
                    if($tallennuspalaute === Tietokantaolio::$HAKU_ONNISTUI){
                        $palaute = Malliluokkapohja::$OPERAATIO_ONNISTUI;
                        
                        // Tämä on tärkeä, jotta olio vastaa todellisuutta:
                        $this->paivita_olion_tiedot_tietokannasta();
                        
                        //========= testausta ==================================
                        $this->lisaa_kommentti(
                            $this->tietokantaolio->tulosta_kaikki_ilmoitukset());
                        //========= testausta ==================================
                    }
                    else{
                        $this->lisaa_virheilmoitus(
                            Perustustekstit::
                                $malliluokkapohja_virheilm_muutostallennuksen_tietokantavirhe.
                            //$this->tietokantaolio->tulosta_virheilmoitukset().
                            $this->tietokantarivi->toString());
echo $this->tietokantaolio->tulosta_virheilmoitukset();
                    }
                }
                else{
                    if(!$arvot_kunnossa){
                         $this->lisaa_virheilmoitus(
                                    Perustustekstit::$Tietoja_ei_tallennettu);
                    } else{
                        // Käyttäjälle asti tuleva ilmoitus -> käännösmahdollisuus:
                        $this->lisaa_virheilmoitus(
                                    Perustustekstit::$ilm_tiedoissa_ei_muutoksia);
                    }
                  
                    // Varmuuden vuoksi palautetaan id:n arvo oikeaksi,
                    // jos joku törppö on yrittänyt muuttaa (muut arvot ovat
                    // vanhoja):
                    $this->set_id($this->get_id_tietokanta());
                }
            }
            else{
                $this->lisaa_virheilmoitus(
                                Perustustekstit::$ilm_tietoja_ei_tietokannassa);
            }
        }
        else{
            $this->lisaa_virheilmoitus("Virhe muokkaustallennuksessa: ".
                    "id-tietokantasolua ei l&ouml;ytynyt!");
            
        }
        return $palaute;
    }
    
     /**
     * Poistaa tämän tietokantarivin. Palauttaa onnistumisen mukaan joko arvon
     * Malliluokkapohja::$VIRHE tai Malliluokkapohja::$OPERAATIO_ONNISTUI.
     * Virhetilanteessa voi lähettää ilmoituksiin viestin.
     * @return type 
     */
    public function poista(){
        $palaute = Malliluokkapohja::$VIRHE;
        $taulu = $this->tk_taulunimi;
        
        $tietokantasoluehto = 
            $this->get_tietokantasolu(Malliluokkapohja::$SARAKENIMI_ID);
        
        if($tietokantasoluehto instanceof Tietokantasolu){
            $taulun_sarake = $tietokantasoluehto->get_sarakenimi();
            $hakuarvo = $tietokantasoluehto->get_arvo();

            $tallennuspalaute = $this->tietokantaolio->poista_rivi($taulu, 
                                                                    $taulun_sarake, 
                                                                    $hakuarvo);
            // Jos onnistui:
            if($tallennuspalaute === Tietokantaolio::$HAKU_ONNISTUI){
                $palaute = Malliluokkapohja::$OPERAATIO_ONNISTUI;
            }
        }
        else{
            // Testaajalle kommmentti:
            $this->lisaa_virheilmoitus("Virhe poistossa: ".
                    "id-tietokantasolua ei l&ouml;ytynyt!");
        }
        return $palaute;
    }
    
    /**
     * Hakee tietokannasta haetusta taulukosta Malliluokkapohjasta perityn 
     * olion muuttujien arvot. 
     * 
     * Onnistuessaan asettaa muuttujan $olio_loytyi_tietokannasta arvoksi TRUE,
     * muuten FALSE.
     * 
     * Tämä vähentää tietokantahakuja esimerkiksi silloin, kun tietokannasta
     * on haettu kerralla monta riviä, jolloin uuden olion luominen rivistä
     * id:n avulla edellyttää turhaa tietokantahakua. Tämä metodi ei lainkaan
     * tee tietokantahakua, vaan käyttää jo haettuja valmiita tietoja.
     * 
     * @param type $osumataulukko_tk Tietokantahaun tulos - assosiatiivinen!
     */
    public function nouki_arvot_tk_osumataulukosta($osumataulukko_tk){
        $onnistui = false;
        if(!empty($osumataulukko_tk)){
            
            // Kerätään sarakenimet:
            $sarakenimet = $this->tietokantarivi->get_sarakenimet_kaikki();
            
            // Lisätään kaikki arvot paikoilleen (ilman tarkastusta, koska
            // tarkastus on tehty ennen tallentamista):
            $laskuri = 0;
            foreach ($sarakenimet as $sarakenimi) {
                $haettu_arvo = $osumataulukko_tk[$sarakenimi];
                
                if($this->set_arvo_kevyt($haettu_arvo, $sarakenimi) === 
                                            POHJA::$OPERAATIO_ONNISTUI){
                
                    $laskuri++;
                } else{
                    echo "Virhe tk-arvon asetuksessa oliolle: sarakenimi-arvo=".
                            $sarakenimi."-".$haettu_arvo;
                }
            }
             
            if($laskuri == $this->tietokantarivi->get_sarakkeiden_lkm()){
                
                // Asetetaan vielä olion tunniste. Tämän jälkeen tätä ei voi muuttaa.
                $this->set_id_tietokanta(
                        $osumataulukko_tk[Malliluokkapohja::$SARAKENIMI_ID]);  
                $onnistui = true;
            }
            else{
                $this->lisaa_virheilmoitus("Virhe metodissa ".
                    "'nouki_arvot_tk_osumataulukosta()'. ".
                    "Asetusten lukumäärä (".$laskuri."/".
                    $this->tietokantarivi->get_sarakkeiden_lkm().") ei täsmää.");
            }
        }
        else{
            $this->lisaa_virheilmoitus("Virhe metodissa ".
                    "'nouki_arvot_tk_osumataulukosta()'. ".
                    "Osumataulukko tyhjä!");
        }
        if($onnistui){
            $this->olio_loytyi_tietokannasta = true;
        }
        else {
            $this->olio_loytyi_tietokannasta = false;
        }
    }
    
    
    /**
     * Hakee olion tiedot tietokannasta ja päivittää tietokantasolujen
     * arvot tietokannan mukaisiksi. 
     */
    public function paivita_olion_tiedot_tietokannasta(){
        if($this->id_tietokanta != Malliluokkapohja::$MUUTTUJAA_EI_MAARITELTY){
            $tiedot = $this->tietokantaolio->
                    hae_eka_osuma_taulukkona($this->tk_taulunimi, 
                                            Malliluokkapohja::$SARAKENIMI_ID, 
                                            $this->id_tietokanta);
            
            // Tämä asettaa $olio_loytyi_tietokannasta-muuttujan arvon
            // onnistumisen mukaan. 
            $this->nouki_arvot_tk_osumataulukosta($tiedot);
        }
        else{
            $this->lisaa_virheilmoitus("Päivitys ei onnistunut: ".
                    "id_tietokanta ei määritelty!");
        }
    }
    
    /**
     * Tämä palauttaa onnistuessaa uuden Tietokantarivi-luokan olion, jossa on 
     * samanlaiset solut kuin tämän luokan tietokantarivillä ja jonka 
     * id-tunnisteen arvo on $id.
     * Solujen arvoiksi syötetään tietokannan arvot. Metodi luo siis uuden 
     * tietokantarivin, <i>eikä tee muutoksia this->tietokantarivi-olioon</i>.
     * 
     * <p>
     * Tätä voidaan hyödyntää esimerkiksi ennen muutosten tallennusta niin, että
     * tämän avulla voidaan tarkistaa, onko yhtään tietoa muutettu.
     * </p>
     * 
     * Ellei tunnistetta vastaavaa riviä löydy tietokannasta, palautetaan arvo
     * Malliluokkapohja::$EI_LOYTYNYT_TIETOKANNASTA. 
     * Palautteen arvioinnissa kannattaa käyttää instanceof-metodia.
     * 
     * @param type $id 
     */
    public function hae_tietokantarivi_tietokannasta($id){
        $palaute = Malliluokkapohja::$EI_LOYTYNYT_TIETOKANNASTA;
        
        $tietokantasolut_array = array();
        
        foreach ($this->get_tietokantasolut() as $solu) {
            if($solu instanceof Tietokantasolu){
                array_push($tietokantasolut_array, 
                            new Tietokantasolu($solu->get_sarakenimi(), 
                                                $solu->get_arvon_tyyppi(),
                                                $this->tietokantaolio));
            }
        }
        
        // Haetaan arvot tietokannasta:
        $osumataulukko = $this->tietokantaolio->
                            hae_eka_osuma_taulukkona(
                                    $this->tk_taulunimi, 
                                    Malliluokkapohja::$SARAKENIMI_ID, 
                                    $id);
        
        // Huom! Alla osumataulukko sisältää kaikki tiedot kahteen kertaan 
        // (indeksin ja sarakenimen avulla haettaviksi)!
        if(!empty($osumataulukko) && 
            sizeof($osumataulukko) == 2*sizeof($tietokantasolut_array)){
                    
            foreach ($tietokantasolut_array as $solu) {
                $solu->set_arvo_kevyt($osumataulukko[$solu->get_sarakenimi()]);
            }
            
            $palaute = new Tietokantarivi($this->tk_taulunimi, $tietokantasolut_array);
        }
        else{
            $this->lisaa_virheilmoitus("Virhe metodissa 'hae_tietokantarivi".
                    "_tietokannasta'. Osumataulukon koko =".
                    sizeof($osumataulukko)." ja tietokantasolut_arrayn koko=".
                    sizeof($tietokantasolut_array));
        }
        
        return $palaute;
    }
    
    //public abstract static function hae($id);
    //public abstract function on_tallennuskelpoinen($uusi);
    /**
     * Palauttaa true, jos tietokannasta löytyy yksi useampi sellainen rivi,
     * joka vastaa kaikkia parametrina annettuja sarakenimiä vastaavia
     * tietokantasoluja. Ellei yhtään sellaista riviä löydy, palauttaa arvon
     * false.
     * 
     * Virhetapauksessa, esimerkiksi jos jokin tietokantasoluarvoista on
     * virheellinen, metodi palauttaa arvon true.
     * 
     * Tästä on hyötyä, kun ennen uuden olion tai muutoksen tallennusta
     * halutaan tarkistaa, onko tietyn ehdot täyttävä olio jo tallennettu
     * tietokantaan.
     */
    public function on_jo_olemassa($taulunimi, $sarakenimet){
        $palaute = true;
        $ehtotietokantasolut = array();
        foreach ($sarakenimet as $snimi) {
            array_push($ehtotietokantasolut, 
                    $this->get_tietokantasolu($snimi));
        }
       
        $osumat = $this->tietokantaolio->hae_tk_oliot($taulunimi, 
                                                        $ehtotietokantasolut);
        // Ellei osumia löydy, ei linkkiä ilmeisesti ole vielä luotu.
        if(empty($osumat)){
            $palaute = false;
        }
        return $palaute;
    }
}
?>