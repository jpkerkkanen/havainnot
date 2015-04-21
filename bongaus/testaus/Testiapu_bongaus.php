<?php
/**
 * Description of Testiapu
 * Toimii bongaustestien alustana ja perii yleisen Testialusta-luokan
 * "php_yleinen"-kansiosta.
 * @author J-P
 */
class Testiapu_bongaus extends Testialusta{


    public $lajiluokat;  // taulukko
    public $kuvaukset;  // taulukko
    public $havainnot;  // taulukko

    //put your code here
    public function  __construct($tietokantaolio, $parametriolio, $luokkanimi) {
        parent::__construct($tietokantaolio, $parametriolio, $luokkanimi);
        $this->lajiluokat = array();
        $this->kuvaukset = array();
        $this->havainnot = array();
    }

    /**
     * Lisää uuden lajiluokan lajiluokat-kokoelmaan.
     * @param <type> $uusi
     */
    public function lisaa_lajiluokka($uusi){
        array_push($this->lajiluokat, $uusi);
    }
    /**
     * Lisää uuden lajiluokan kuvaukset-kokoelmaan.
     * @param <type> $uusi
     */
    public function lisaa_kuvaus($uusi){
        array_push($this->kuvaukset, $uusi);
    }

    /**
     * Lisää uuden havainnon havainnot-kokoelmaan.
     * @param <type> $uusi
     */
    public function lisaa_havainto($uusi){
        array_push($this->havainnot, $uusi);
    }
    
    /**
     * Luo uuden lajiluokan annetuilla arvoilla, tallentaa sen tietokantaan
     * ja palauttaa tallennetun id:n tai arvon
     * Lajiluokka::$MUUTTUJAA_EI_MAARITELTY, jos jokin menee vikaan.
     *
     * @param type $ylaluokka_id
     * @param type $nimi_latina
     * @return type 
     */
    public function luo_ja_tallenna_lajiluokka($ylaluokka_id, $nimi_latina){
        $tallennetun_id = Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;
        $id = Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;
        $lajiluokka = new Lajiluokka($this->tietokantaolio, $id);

        $this->lisaa_ilmoitus("Uusi tyhja lajiluokka luotu!",false);

        // Ei pitäisi olla tallennuskelpoinen:
        if($lajiluokka->on_tallennuskelpoinen(true)){
            $this->lisaa_virheilmoitus("Ei pitaisi olla tallennuskelpoinen!");
        }
        else{
            /*$this->lisaa_ilmoitus("Muuttujia ei ole asetettu, joten".
                    " saatiin aivan oikein seuraava palaute: ".
                    $lajiluokka->tulosta_virheilmoitukset(),false);*/
        }
        
        /*$this->lisaa_ilmoitus("Asetetaan lajiluokan ylaluokka_id,
                        ja nimi_latina. Testataan
                        uudelleen, onko nyt tallennuskelpoinen:",false);*/

        $lajiluokka->set_ylaluokka_id($ylaluokka_id);
        $lajiluokka->set_nimi_latina($nimi_latina);
        
        
        // Tässä vaiheessa ongelmaksi voi tulla myös se, että nimi_latina on
        // jo olemassa. Siitä annetaan erillinen ilmoitus.
        if($lajiluokka->on_tallennuskelpoinen(true)){
            $this->lisaa_ilmoitus("Lajiluokka on tallennuskelpoinen",false);
            $onnistuminen = $lajiluokka->tallenna_uusi();

            if($onnistuminen === Lajiluokka::$OPERAATIO_ONNISTUI){
                $tallennetun_id = mysql_insert_id();
                $this->lisaa_ilmoitus("Lajiluokan tallennus onnistui!", false);
            }
            else{
                $this->lisaa_ilmoitus($onnistuminen." Virhe tallennuksessa (luo_
                    ja_tallenna_lajiluokka())!".
                    " Arvot: ylaluokka_id=".$lajiluokka->get_ylaluokka_id().
                    ", nimi_latina=".$lajiluokka->get_nimi_latina(), true);
            }
        }
        else{
            // Tämä ei aina ole vastoin toivomuksia!
            $this->lisaa_ilmoitus($lajiluokka->tulosta_virheilmoitukset(), false);
        }
        return $tallennetun_id;
    }
    /**
     * Luo uuden kuvauksen annetuilla arvoilla, tallentaa sen tietokantaan
     * ja palauttaa tallennetun id:n tai arvon
     * Kuvaus::$MUUTTUJAA_EI_MAARITELTY, jos jokin menee vikaan.
     * @param int $lajiluokka_id Viittaus yläluokkaan
     * @param string $nimi Lajin nimi $kieli-muuttujan määrittämällä kielellä.
     * @param string $kuvaus Lajin kuvaus $kieli-muuttujan määrittämällä kielellä.
     * @param int $kieli kielen indeksi
     * @return int Palauttaa kokonaisluvun
     */
    public function luo_ja_tallenna_kuvaus($lajiluokka_id, $nimi, $kuvaus, $kieli){
        $tallennetun_id = Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        $id = Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        $kuvausolio = new Kuvaus($this->tietokantaolio, $id);
        
        $this->lisaa_ilmoitus("Uusi tyhja kuvausolio luotu!",false);

        // Ei pitäisi olla tallennuskelpoinen:
        if($kuvausolio->on_tallennuskelpoinen(true)){
            $this->lisaa_virheilmoitus("Ei pitaisi olla tallennuskelpoinen!");
        }
        /*else{
            $this->lisaa_ilmoitus("Muuttujia ei ole asetettu, joten".
                    " saatiin aivan oikein seuraava palaute: ".
                    $kuvausolio->tulosta_virheilmoitukset(),false);
        }
        
        $this->lisaa_ilmoitus("Asetetaan lajiluokka_id, nimi, kuvaus ja
                kieli. Testataan uudelleen, onko nyt tallennuskelpoinen:",false);*/

        $kuvausolio->set_lajiluokka($lajiluokka_id);
        $kuvausolio->set_nimi($nimi);
        $kuvausolio->set_kuvaus($kuvaus);
        $kuvausolio->set_kieli($kieli);

        $this->lisaa_ilmoitus("Muuttujien arvot asetettu!",false);
        
        if($kuvausolio->on_tallennuskelpoinen(true)){
            $this->lisaa_ilmoitus("Kuvausolio on tallennuskelpoinen!",false);
            $onnistuminen = $kuvausolio->tallenna_uusi();

            if($onnistuminen === Kuvaus::$OPERAATIO_ONNISTUI){
                $tallennetun_id = mysql_insert_id();
            }
            else{
                $this->lisaa_ilmoitus($onnistuminen." Virhe tallennuksessa (luo_
                    ja_tallenna_kuvaus())!".
                    " Arvot: lajiluokka_id=".$kuvausolio->get_lajiluokka_id().
                    ", nimi=".$kuvausolio->get_nimi().
                    ", kuvaus=".$kuvausolio->get_kuvaus().
                    ", kieli=".$kuvausolio->get_kieli(), true);
            }
        }
        else{
            // Joskus testissä tämä on toivottava toiminto, joten ei aina virhe!
            $this->lisaa_ilmoitus($kuvausolio->tulosta_virheilmoitukset(), false);
        }
        return $tallennetun_id;
    }
    
     
    /**
     * Tallentaa havainnon ja palauttaa olion, jonka tiedot haettu tietokannasta.
     * Virheen sattuessa palauttaa arvon Malliluokkapohja::VIRHE. 
     * @param <type> $henkilo_id
     * @param <type> $lajiluokka_id
     * @param <type> $vuosi
     * @param <type> $kk
     * @param <type> $paiva
     * @param <type> $paikka
     * @param <type> $kommentti
     * @param <type> $maa
     * @param <type> $varmuus
     */
    public function luo_ja_tallenna_havainto(
                                            $henkilo_id,
                                            $lajiluokka_id,
                                            $vuosi,
                                            $kk,
                                            $paiva,
                                            $paikka,
                                            $kommentti,
                                            $maa,
                                            $varmuus){

        $id = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        $hav = new Havainto($id, $this->tietokantaolio);

        $pal = $hav->set_henkilo_id($henkilo_id);
        
        if($pal == Havainto::$VIRHE){
            $this->lisaa_virheilmoitus("Virhe henkilo_id:n asetuksessa! ".
                    $hav->tulosta_virheilmoitukset());
        }
        
        $hav->set_lajiluokka_id($lajiluokka_id);
        $hav->set_vuosi($vuosi);
        $hav->set_kk($kk);
        $hav->set_paiva($paiva);
        $hav->set_paikka($paikka);
        $hav->set_kommentti($kommentti);
        $hav->set_maa($maa);
        $hav->set_varmuus($varmuus);
        $hav->set_arvo(1, Havainto::$SARAKENIMI_SUKUPUOLI);
        $hav->set_arvo(2, Havainto::$SARAKENIMI_LKM);

        $palaute = $hav->tallenna_uusi();

        // Haetaan tiedot:
        if($palaute === Havainto::$OPERAATIO_ONNISTUI){
            //return new Havainto($hav->get_id(), $this->tietokantaolio);
            return $hav;
        }
        else{
            // Otetaan virheilmoitukset talteen ja nollataan olio:
            $this->lisaa_virheilmoitus($hav->tulosta_virheilmoitukset());
            $hav->nollaa_muuttujat();
            return Malliluokkapohja::$VIRHE;
        }
    }
}

/**
 * public static $SARAKENIMI_HENKILO_ID= "henkilo_id";
    public static $SARAKENIMI_LAJILUOKKA_ID= "lajiluokka_id";
    public static $SARAKENIMI_VUOSI= "vuosi";
    public static $SARAKENIMI_KK= "kk";
    public static $SARAKENIMI_PAIVA= "paiva";
    
    public static $SARAKENIMI_PAIKKA= "paikka";
    public static $SARAKENIMI_KOMMENTTI= "kommentti";
    public static $SARAKENIMI_MAA= "maa";
    public static $SARAKENIMI_VARMUUS= "varmuus";
    public static $SARAKENIMI_SUKUPUOLI= "sukupuoli";
    
    public static $SARAKENIMI_LKM= "lkm";
    public static $SARAKENIMI_LISALUOKITUS1= "lisaluokitus1";
    public static $SARAKENIMI_LISALUOKITUS2= "lisaluokitus2";
    public static $SARAKENIMI_LISALUOKITUS3= "lisaluokitus3";
 */
?>
