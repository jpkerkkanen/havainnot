<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Tämä luokka auttaa tietokantataulun tietojen siirtämisessä Malliluokkapohjalle, 
 * joka huolehtii CRUD-toiminnoista. Täällä oletetaan, että tietokantaan
 * tallennettavat yksittäiset arvot ("solun" arvo) ovat joko lukuja tai
 * merkkijonoja. Myöhemmin voi olla tarpeen laajentaa tyyppivalikoimaa.
 *
 * <p>Tämä luokka liittyy kiinteästi luokkiin Malliluokkapohja, Tietokantarivi
 * ja Tietokantaolio.</p>
 * 
 * <p>Arvon voi syöttää vain metodien set_arvo() ja set_arvo_kevyt() kautta, 
 * jolloin syötteiden tarkistus on helpompi hallita. Luokan $tiedot_ok-muuttuja 
 * on true vain, jos tiedot ovat läpäisseet tarkistuksen, eli myös $arvo on
 * määritelty ja oikean muotoinen.
 * Muussa tapauksessa, kuten myös uuden olion muodostamisvaiheessa 
 * muuttujalle $sarakenimi asetetaan arvoksi
 * Pohja::$MUUTTUJAA_EI_MAARITELTY. </p>
 * 
 * MUUTOS 11.1.2014: täällä tutkitaan myös syötteen tyhjyys 
 * lisäämällä yhden parametrin $tyhja_ok. Tosin huomasin, että tämä on järkevä 
 * lähinnä merkkijonoilla, koska numeric-arvoilla esim. 0, 0.0 tai "0" antaa
 * true metodille empty()!
 * 
 * @author Jukka-Pekka Kerkkänen, 17.10.2012
 */
class Tietokantasolu {
    
    
    //put your code here
    private $sarakenimi, $arvo, $arvon_tyyppi, $tiedot_ok;
    private $tietokantaolio;    //  Tarvitaan funktiota escape_real_string varten.
    
    private $on_muokattu;   // Tämän avulla tiedetään, onko solua muutettu.
    
    // Arvotyypit;
    public static $luku_int = 1;
    public static $mj_tyhja_ok = 2;
    public static $mj_tyhja_EI_ok = 3;
            
    function __construct($sarakenimi, $arvon_tyyppi, $tietokantaolio){
        
        $this->arvo = Pohja::$MUUTTUJAA_EI_MAARITELTY;
        $this->sarakenimi = Pohja::$MUUTTUJAA_EI_MAARITELTY;
        $this->arvon_tyyppi = Pohja::$MUUTTUJAA_EI_MAARITELTY;
        $this->tiedot_ok = false;
        $this->on_muokattu = false;
        $this->tietokantaolio = $tietokantaolio;
        
        // Sarakenimen pitää olla määritelty ja epätyhjä merkkijono:
        if(is_string($sarakenimi) && !empty($sarakenimi)){   
            $this->sarakenimi = $sarakenimi;
            
            if(($arvon_tyyppi === Tietokantasolu::$luku_int) ||
                ($arvon_tyyppi === Tietokantasolu::$mj_tyhja_EI_ok) ||
                ($arvon_tyyppi === Tietokantasolu::$mj_tyhja_ok)){
                
                $this->arvon_tyyppi = $arvon_tyyppi;
            } else{
                $this->arvon_tyyppi = Tietokantasolu::$luku_int;
            }
            
            
        }
        $this->tietokantaolio = $tietokantaolio;
    }
    
    /**
     * Tätä kautta tulevat kaikki arvot tietokannasta. 
     * Arvoille ei tehdä mitään, vaan se palautetaan sellaisenaan!
     * 
     * Huom: htmlspecialchars()-metodin käyttö täällä aiheuttaa helposti sen,
     * että merkit koodataan useaan kertaan, ennen kuin ne päätyvät näytettäväksi.
     * Päädyin siis tekemään erillisen metodin, jossa koodi menee
     * htmlspecialchars-muokkauksen läpi.
     * 
     * Huom2: stripslashes($this->arvo);   // TURVALLISUUS?!
        // HUOMAA yllä olevan mahdollinen ongelma: jos tietokannasta haetaan
        // mysql_real_escape_string()-koodattu arvo, niin sen kenoviivat
        // häviävät, jolloin arvoa ei pidä käyttää suoraan SQL-hakuihin (en
     * varma siis ole, mutta voipi olla porsaanreikiä.
     * 
     * @return type
     */
    public function get_arvo(){
        
        return $this->arvo;
        
    }
    
    /**
     * Tätä kautta tulevat arvot ovat siinä muodossa, että ne näkyvät 
     * järkevästi ja turvallisesti html-sivulla. Arvot menevät siis
     * htmlspecialchars-muokkauksen läpi.
     * 
     * Huom2: stripslashes($this->arvo);   // TURVALLISUUS?!
        // HUOMAA yllä olevan mahdollinen ongelma: jos tietokannasta haetaan
        // mysql_real_escape_string()-koodattu arvo, niin sen kenoviivat
        // häviävät, jolloin arvoa ei pidä käyttää suoraan SQL-hakuihin (en
     * varma siis ole, mutta voipi olla porsaanreikiä.
     * 
     * VARMUUDEN VUOKSI tätä suositellaan käytettäväksi vain TULOSTETTAESSA
     * tekstiä käyttäjän luettavaksi. Muuten kannattaa käyttää get_arvo-metodia.
     * 
     * @return type
     */
    public function get_html_encoded_arvo(){
        
        // html-koodaus kannattaa
        // tehdä, jos arvoa käytetään html:n seassa. Tällöin nimittäin
        // esimerkiksi heittomerkit sisällössä eivät sekoita html-koodiin
        // kuuluvia heittomerkkejä (esim input-elementin value-arvo).
        // ENT_QUOTES tarkoittaa, että sekä yksin- että kaksinkertaiset
        // heittomerkit koodataan.
        return (htmlspecialchars(stripslashes($this->arvo),
                                    ENT_QUOTES,
                                    "UTF-8",
                                    true));
    }
    
    /**
     * Tämän avulla voidaan asettaa TIETOKANTAAN MENEVÄ arvo. Arvon pitää olla
     * määritelty ja se puhdistetaan vaarallisista merkeistä
     * mysql_real_escape_string-metodin avulla. Se trimmataan (trim) myös eli tyhjät
     * pois päistä. Tämän jälkeen perityissä metodeissa ei ole niin tiukkaa
     * tarkistusten kanssa, koska täällä tehdään tärkein kuitenkin.
     * 
     * <p>
     * <b>HUOM! Kaikki tietokantaan menevä tieto kulkee tämän metodin kautta!</b>
     * </p>
     * 
     * Ellei uusi ole määritelty, 
     * annetaan arvoksi Pohja::$MUUTTUJAA_EI_MAARITELTY.
     * 
     * Jos uusi arvo on tyhjä silloin, kun tyhjä kielletty, 
     * annetaan arvoksi Pohja::$ARVO_TYHJA. HUOMAA: tässä vain merkkijonoja
     * lienee järkevää tarkistaa, koska muuten esim. arvo 0 ei mene läpi
     * luku-muodossa. 
     * 
     * Ellei uusi arvo ole oikean tyyppiä, annetaan arvoksi
     * Pohja::$ARVO_VAARANTYYPPINEN.
     * 
     * Muuttuja tiedot_ok saa arvon TRUE, jos arvo on määritelty ja muuten
     * arvon FALSE.
     * 
     * [Jos vinoviivat ovat ongelma, voi ne poistaa (stripslashes) täällä ENNEN 
     * mysql_real_escape_string-metodin käyttöä.]
     * 
     * @param type $uusi 
     */
    public function set_arvo($uusi){
        if(isset($uusi) && ($uusi != Pohja::$MUUTTUJAA_EI_MAARITELTY)){
            
            // Huomaa, ettei tyhjyyttä tarkisteta luvuille, joten
            // rakentajan parametri on niille turha..
            if($this->arvon_tyyppi === Tietokantasolu::$luku_int){
                if(is_numeric($uusi)){
                    $this->arvo = 
                        $this->tietokantaolio->real_escape_string(trim($uusi));
                    $this->tiedot_ok = true;
                }
                else{
                    $this->arvo = Pohja::$ARVO_VAARANTYYPPINEN;
                    $this->tiedot_ok = false;
                }
            }
            else{
                if(($this->arvon_tyyppi === Tietokantasolu::$mj_tyhja_EI_ok) ||
                    ($this->arvon_tyyppi === Tietokantasolu::$mj_tyhja_ok)){
                    
                    if(is_string($uusi)){

                        $this->arvo = 
                            $this->tietokantaolio->real_escape_string(trim($uusi));


                        // Tarkistetaan tyhjyys tarvittaessa:
                        if(($this->arvon_tyyppi === Tietokantasolu::$mj_tyhja_EI_ok) && 
                            (empty($this->arvo))){
                            
                            $this->arvo = Pohja::$ARVO_TYHJA;
                            $this->tiedot_ok = false;
                        } else{
                            $this->tiedot_ok = true;
                        }
                    }
                    else{
                        $this->arvo = Pohja::$ARVO_VAARANTYYPPINEN;
                        $this->tiedot_ok = false;
                    }
                } else{
                    $this->arvo = Pohja::$MUUTTUJAA_EI_MAARITELTY;
                    $this->tiedot_ok = false;
                }
            }
        }
        else{
            $this->arvo = Pohja::$MUUTTUJAA_EI_MAARITELTY;
            $this->tiedot_ok = false;
        }
    }
    
    /**
     * Tämän metodin avulla asetetaan TIETOKANNASTA haetut arvot oliolle,
     * jolloin putsausta ja tarkistusta ei tarvita samalla tavalla, kuin
     * arvon tullessa käyttäjältä.
     * 
     * <p>
     * HUOM! Kaikki tietokantaan menevä käyttäjän syöttämä tieto pitää
     * ehdottomasti asettaa set_arvo()-metodin avulla, jotta se menee
     * mysql_real_escape_string()-koodauksen läpi!
     * </p>
     * 
     * Voiko olla turvallisuusriski? Jos tietokannasta haettaessa koodia 
     * muokataan, niin siitä voi ehkä tulla riski..
     * 
     * <p>
     * Ellei uusi ole määritelty, annetaan arvoksi Pohja::$MUUTTUJAA_EI_MAARITELTY.
     * </p>
     * 
     * Muuttuja tiedot_ok saa arvon TRUE, jos arvo on määritelty ja muuten
     * arvon FALSE. 
     * 
     * Metodi ei palauta mitään arvoa.
     * 
     * @param type $uusi 
     */
    public function set_arvo_kevyt($uusi){
        if(isset($uusi) && ($uusi != Pohja::$MUUTTUJAA_EI_MAARITELTY)){
            $this->arvo = $uusi;
            $this->tiedot_ok = true;
        }
        else{
            $this->arvo = Pohja::$MUUTTUJAA_EI_MAARITELTY;
            $this->tiedot_ok = false;
        }
    }
    
    public function get_sarakenimi(){
        return $this->sarakenimi;
    }
    public function get_arvon_tyyppi(){
        return $this->arvon_tyyppi;
    }
    
    public function on_muokattu(){
        return $this->on_muokattu;
    }
    public function set_on_muokattu($uusi){
        $this->on_muokattu = $uusi;
    }
    
    /**
     * Palauttaa TRUE, jos sarakenimi on epätyhjä merkkijono ja arvo
     * on jokin muu kuin Pohja::$MUUTTUJAA_EI_MAARITELTY. Muuten palauttaa
     * FALSE.
     * @return type 
     */
    public function tiedot_ok(){
        return $this->tiedot_ok; 
    }
}

?>
