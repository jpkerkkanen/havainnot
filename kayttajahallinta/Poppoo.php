<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Poppoo
 * SQL:
create table poppoot
(
  id                    int auto_increment not null,
  nimi                  varchar(50) not null,
  luomispvm             date not null default CURDATE(),
  kommentti		varchar(1000),
  kayttajatunnus        varchar(50) unique not null,
  maksimikoko           int not null default 10,
  primary key (id),
  index(nimi),
  index(kayttajatunnus),
) ENGINE=InnoDB;
 * @author kerkjuk_admin
 */
class Poppoo extends Malliluokkapohja{
    
    private $kayttajatunnusvahvistus;
    private $tunnuksen_muokkaus;  // Muokataanko tunnusta vai ei.
    
    public static $sarakenimi_nimi = "nimi";
    public static $sarakenimi_luomispvm = "luomispvm";
    public static $sarakenimi_kommentti = "kommentti";
    public static $sarakenimi_kayttajatunnus = "kayttajatunnus";
    public static $sarakenimi_maksimikoko = "maksimikoko";
    
    public static $taulunimi = "poppoot";
    
    // Yleinen poppoon koon maksimiarvo:
    public static $maksimikoko = 100;


    // Rakentaja:
    function __construct($id, $tietokantaolio) {
        
        $tietokantasolut = 
            array(new Tietokantasolu(Poppoo::$SARAKENIMI_ID, Tietokantasolu::$luku_int, $tietokantaolio),
                new Tietokantasolu(Poppoo::$sarakenimi_nimi, Tietokantasolu::$mj_tyhja_EI_ok, $tietokantaolio),
                new Tietokantasolu(Poppoo::$sarakenimi_luomispvm, Tietokantasolu::$mj_tyhja_EI_ok, $tietokantaolio),
                new Tietokantasolu(Poppoo::$sarakenimi_kommentti, Tietokantasolu::$mj_tyhja_ok, $tietokantaolio),
                new Tietokantasolu(Poppoo::$sarakenimi_kayttajatunnus, Tietokantasolu::$mj_tyhja_EI_ok, $tietokantaolio),
                new Tietokantasolu(Poppoo::$sarakenimi_maksimikoko, Tietokantasolu::$luku_int, $tietokantaolio));
        
        $taulunimi = Poppoo::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
    }   
    
    public function get_kayttajatunnusvahvistus(){
        return $this->kayttajatunnusvahvistus;
    }
    public function set_kayttajatunnusvahvistus($uusi){
        $this->kayttajatunnusvahvistus = $uusi;
    }
    
    public function get_tunnuksen_muokkaus(){
        return $this->tunnuksen_muokkaus;
    }
    public function set_tunnuksen_muokkaus($boolean){
        $this->tunnuksen_muokkaus = $boolean;
    }
    
    /**
     * Hakee kaikki poppooseen kuuluvat henkilöt ja palauttaa taulukon, joka
     * sisältää kaikki kyseiset Henkilö-luokan oliot. Taulukko on tyhjä, ellei
     * poppooseen kuulu ketään.
     */
    public function hae_poppoon_jasenet(){
        $jasenet = array();
        
        if($this->olio_loytyi_tietokannasta){
            $poppoo_id = $this->get_id();
            
            $taulunimi = Henkilo::$taulunimi;
            $ehtosolu1 = new Tietokantasolu(Henkilo::$sarakenimi_poppoo_id,  
                                            Tietokantasolu::$luku_int, $this->tietokantaolio);
            $ehtosolu1->set_arvo_kevyt($poppoo_id);
            $ehtotietokantasolut = array($ehtosolu1);

            $osumat = $this->tietokantaolio->hae_tk_oliot($taulunimi, 
                                                        $ehtotietokantasolut);
            foreach ($osumat as $tk_olio) {
                $henkilo = new Henkilo($tk_olio->id, $this->tietokantaolio);
                if($henkilo->olio_loytyi_tietokannasta){
                    array_push($jasenet, $henkilo);
                }
            }
        }
        
        return $jasenet;
    }
    
    /*********************FUNCTION ETSI_POPPOOTUNNUS **************************/
    /**
     * Tarkistaa, löytyyko käyttäjätunnusta vastaavaa riviä
     * tietokannasta. Ennen hakua
     * syötteet puhdistetaan kaikista merkeistä, joilla on erikoismerkitys
     * mySQL:ssä. 
     * 
     * Palauttaa luvun, joka on poppoon id-tunniste, jos tunnus täsmää ja 
     * muuten arvon Poppoo::$EI_LOYTYNYT_TIETOKANNASTA.
     * 
     * Asettaa löytyneen id:n $_SESSION[Sessio::$poppoon_id]-muuttujan arvoksi.
     *
     * @param type $tunnus
     * @param Tietokantaolio $tietokantaolio
     */
    static function etsi_poppootunnus($tunnus, $tietokantaolio){
        $tunnuss = mysql_real_escape_string($tunnus);
        
        $palaute = Poppoo::$EI_LOYTYNYT_TIETOKANNASTA;
        
        $taulunimi = Poppoo::$taulunimi;
        $ehtosolu1 = new Tietokantasolu(Poppoo::$sarakenimi_kayttajatunnus,
                                        Tietokantasolu::$mj_tyhja_EI_ok, $tietokantaolio);
        $ehtosolu1->set_arvo_kevyt($tunnuss);   // Puhdistettu!
        $ehtotietokantasolut = array($ehtosolu1);
                                
        $osumat = $tietokantaolio->hae_tk_oliot($taulunimi, $ehtotietokantasolut);
        
        // osumia pitäisi olla nolla tai yksi. Useampi viittaa ilkeään virheeseen.
        if(sizeof($osumat) === 1){
            $palaute = $osumat[0]->id;
            
            // Lisätään poppoon id sessio-muuttujaan, jotta tiedetään tämän
            // olevan käytössä oleva poppoo.
            $_SESSION[Sessio::$poppoon_id] = $palaute;
            
        } else if(sizeof($osumat) > 1){
            $this->lisaa_virheilmoitus("Virhe metodissa 'etsi_poppootunnus':".
                    " Osumia l&ouml;ytyi ".sizeof($osumat)." kpl");
        }
        
        return $palaute;
    }
    
    /**
     * Lisää tarkistuksia esimerkiksi liittyen tunnuksiin. Palauttaa samat
     * arvot kuin Malliluokkapohjan metodikin. Virheen sattuessa ilmoitukset
     * virheilmoituksissa.
     * 
     * luomispvm lisätään
     * automaattisesti, ettei siitä tarvitse huolehtia. [Tämä on turha, 
     * jos asiasta huolehditaan tietokantatasolla:
     * luomispvm date not null default CURDATE()]
     * 
     */
    public function tallenna_uusi() {
        
        // Tarkistetaan ennen tallennuksen yrittämistä (salasana selkomuodossa).
        $uusi_olio = true;
        if($this->on_tallennuskelpoinen($uusi_olio)){
            $sql_pvm_now = date("Y-m-d");   // Ilm järjestys pitää olla noin...
            $this->set_arvo($sql_pvm_now, Poppoo::$sarakenimi_luomispvm);
            $tallennuspalaute = parent::tallenna_uusi();
        } else{
            $tallennuspalaute = Poppoo::$VIRHE;
        }
        return $tallennuspalaute;
    }
    /**
     * Lisää tarkistuksia esimerkiksi liittyen tunnuksiin. Palauttaa samat
     * arvot kuin Malliluokkapohjan metodikin. Virheen sattuessa ilmoitukset
     * virheilmoituksissa.
     * 
     */
    public function tallenna_muutokset() {
        
        // Tarkistetaan ennen tallennuksen yrittämistä.
        $uusi_olio = false;
        if($this->on_tallennuskelpoinen($uusi_olio)){
            
            $tallennuspalaute = parent::tallenna_muutokset();
        } else{
            $tallennuspalaute = Poppoo::$VIRHE;
        }
        return $tallennuspalaute;
    }
    
    /**
     * Tarkistaa muun muassa sen, ettei käyttäjätunnus ole jo käytössä.
     * Käyttäjätunnus myös tarkistetaan hiukan tarkemmin, kuin
     * mitä tapahtuisi Malliluokkapohjan yleisessä tarkistuksessa. Samoin
     * tehdään muutamia muita tarkistuksia.
     * 
     * Palauttaa totuusarvon ja false-tapauksessa tallentaa virheilmoituksen 
     * oliolle.
     * 
     * Parametrina annetaan salasanaa muutettaessa vahvistussalasana. Jos
     * sillä on arvo false, 
     * 
     * @param type $uusi ilmoittaa, onko uusi olio, vai muokkaus. Totuusarvo
     * true viittaa uuteen olioon.
     * @param type $tunnusten_muokkaus Muokataanko tunnuksia ja miten?
     * @return boolean
     */
    protected function on_tallennuskelpoinen($uusi){
        
        $palaute = true;
        $ilmoitus = "";
       
        $nimi = $this->get_arvo(Poppoo::$sarakenimi_nimi);
        $kommentti = $this->get_arvo(Poppoo::$sarakenimi_kommentti);
        $koko = $this->get_arvo(Poppoo::$sarakenimi_maksimikoko);
        $ktunnus = $this->get_arvo(Poppoo::$sarakenimi_kayttajatunnus);

        
        if(trim($nimi) == '' || trim($kommentti) == '' || trim($ktunnus) == '')
        {
            $ilmoitus = Kayttajatekstit::
                            $henkilolomake_jokin_pakollisista_kentista_tyhja;
            $palaute = false;
        }

        // Tarkistetaan, ettei maksimikoko ole einumero tai tyhjä:
        // Tässä käytetään Perlin tyyppistä reg. exiä.
        else if((preg_match('/^\d+$/',$koko) === 0) || ($koko < 1) || 
                                            ($koko > Poppoo::$maksimikoko))
        {
            $ilmoitus = Kayttajatekstit::$poppoolomake_koko_ei_ole_oikein." ";
            $ilmoitus .= Kayttajatekstit::$poppoolomake_koko_on_luku_valilta.
                    "1-".Poppoo::$maksimikoko;
            $palaute = false;
        }
        
        // Tarkistetaan tunnukset. Virheistä tulee virheilmoitukset.
        else if(!$this->tunnus_ok($uusi)){
            $palaute = false;
        }
        
        $this->lisaa_virheilmoitus($ilmoitus);

        return $palaute;
    }
    
    /*********************FUNCTION TARKISTA_TUNNUS *****************************/
    /* Tarkistaa käyttäjätunnuksen n. Tätä enne  syöte puhdistetaan kaikista 
     * merkeistä, joilla on erikoismerkitys html:ssä tai mySQL:ssä. 
     * 
     * Tarkistukset: merkit sekä tunnuksen ja vahvistuksen samuus. Lisäksi
     * tarkistetaan käyttäjätunnuksen olemassaolo (pitää olla unique!).
     * 
     * Palauttaa totuusarvon funktion nimen mukaisesti eli true, jos
     * tunnus näyttää olevan ok.
     */
    function tunnus_ok($uusi_olio){
        $ok = true;
        $tunnusta_muokataan = $this->get_tunnuksen_muokkaus();
        $vahvistus = $this->get_kayttajatunnusvahvistus();
        
        $ktunnus = $this->get_arvo(Poppoo::$sarakenimi_kayttajatunnus);

        // Sisempään if-lauseeseen joutuminen merkitsee virhettä.
        
        // Käyttäjätunnus jo käytössä? Muokatessa tarkistetaan vain, jos
        // sitä on tarkoitus muuttaa.
        if($uusi_olio || $tunnusta_muokataan){
            
            $taulunimi = Poppoo::$taulunimi;
            $sarakenimi = Poppoo::$sarakenimi_kayttajatunnus;

            if(Yleismetodit::arvo_jo_kaytossa($taulunimi, 
                                                $sarakenimi, 
                                                $ktunnus, 
                                                $this->tietokantaolio)){
                $this->lisaa_virheilmoitus(
                                Kayttajatekstit::$tunnus_poppoon_jo_kaytossa);
                $ok = false;
            } 
            
            // Tunnus ja vahvistus täsmäävät?
            if($vahvistus !== $ktunnus){
                $this->lisaa_virheilmoitus(
                                Kayttajatekstit::
                                $poppoovirheilmoitus_tunnus_vahvistus_ei_tasmaa);
                $ok = false;

            } 
            
            // Käyttäjätunnuksen merkit ja pituus:
            if(!Tunnukset::tunnuksen_merkit_ja_pituus_ok($ktunnus, 
                                    Tunnukset::$pituus_min_kayttajatunnus_poppoo, 
                                    Tunnukset::$pituus_max_kayttajatunnus_poppoo,
                                    $this->tietokantaolio)) {
                $this->lisaa_virheilmoitus(
                    Kayttajatekstit::$tunnus_poppoo_pituus_tai_merkkivirhe.
                    "<br/>".
                    Kayttajatekstit::$tunnus_poppootunnuksen_min_pituus_on." ".
                    Tunnukset::$pituus_min_kayttajatunnus_poppoo.". ".
                    Kayttajatekstit::$tunnus_poppootunnuksen_max_pituus_on." ".
                    Tunnukset::$pituus_max_kayttajatunnus_poppoo.".<br/>".
                    Kayttajatekstit::$tunnus_vain_seuraavat_merkit_sopivat.": ".
                    Tunnukset::$sallitut_merkit);
                $ok = false;

            } 
        }
       
        return $ok;
    }
    /**
     * Palauttaa poppoon kaikkien havaintojen määrän.
     * @param array $jasenet taulukko, joka sisältää poppoon jäsenet
     * Henkilo-luokan olioina.
     */
    public function hae_poppoon_havaintomaara($jasenet){
        $lkm = 0;
        
        foreach ($jasenet as $henk) {
            if($henk instanceof Henkilo){
                
                $taulu = Havainto::$taulunimi; 
                $taulun_sarake = Havainto::$SARAKENIMI_HENKILO_ID; 
                $hakuarvo = $henk->get_id();
                $lkm += $this->tietokantaolio->hae_osumien_lkm($taulu, 
                                                                $taulun_sarake, 
                                                                $hakuarvo);
            }
        }
        return $lkm;
    }
    
    /**
     * Hakee kaikki poppoot tietokannasta ja palauttaa ne taulukossa
     * Poppoo-luokan olioina. Voi palauttaa tyhjän taulukon.
     * 
     * @param Tietokantaolio $tietokantaolio
     * @return array
     */
    static function hae_kaikki_poppoot($tietokantaolio){
        $poppoot = array();
        
        $hakulause = "SELECT id FROM ".  Poppoo::$taulunimi;
        $osumat = $tietokantaolio->
                            tee_omahaku_oliotaulukkopalautteella($hakulause);
        
        foreach ($osumat as $tk_olio) {
            $poppoo = new Poppoo($tk_olio->id, $tietokantaolio);
            if($poppoo->olio_loytyi_tietokannasta){
                array_push($poppoot, $poppoo);
            }
        }
        
        return $poppoot;
    }
    
    
}

?>
