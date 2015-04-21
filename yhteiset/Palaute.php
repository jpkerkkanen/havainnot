<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Palaute: Tämä luokka auttaa pitämään selvillä
 * toimintojen toteutusten palautusarvot. Toteutukset palauttavat tästä
 * lähtien aina Palaute-olion. Tänne tulevat erityisesti sivun sisällöt.
 * 
 * Muutos 30.5.2013: perii Pohja-luokan, jotta saa sieltä ilmoitusmekanismin.
 *
 * @author kerkjuk_admin
 */
class Palaute extends Pohja{
    
    /* 
     * $muokatun_id: viimeksi muokatun tietokanta- tai muun olion tunniste 
     * (oletus -1). Huonon parametrin tilanteessa setteri asettaa muokatun_id:ksi
     * arvon -1. Tämä voi liittyä esim. tilanteeseen, jolloin mysql_insert_id 
     * palauttaa arvon false tietokantayhteyden puuttumisen takia.
     */
    private $sisalto, $valikko_html, $muokatun_id, $kirjautumistiedot;
    private $kirjautuminen_ok; // boolean-arvo: true, jos käyttäjän kirjautuminen kunnossa.
    private $valikkonaytto; // bool; näytetäänkö valikot vai ei.
    private $vasen_palkki;    // Vasemman palkin sisältö
    private $oikea_palkki;    // Vasemman palkin sisältö
    private $alapalkki;    // Alapalkin sisältö
    private $painikkeet;    // Yleiset painikkeet
    private $nayttomoodi;   // Muokkaa muun muassa palstojen määrää.
    private $naytettava_oliotyyppi; // Kertoo näytettävien tyypin.
    private $js_url;    // Tämä viittaa urliin, jonne js-metodi vie.


    public static $EI_LINKKEJA = "";    // Tarkoittaa, että linkkihtml tyhjä.

    // Antaa luvun, joka ilmaisee toiminnon onnistumisen. Tätä hyödynnetään
    // testauksessa.
    private $onnistumispalaute;    

    public static $ONNISTUMISPALAUTE_KAIKKI_OK = -1;
    public static $ONNISTUMISPALAUTE_VIRHE_YLEINEN = 1;
    public static $ONNISTUMISPALAUTE_VIRHE_TALLENNUS_UUSI = 2;
    public static $ONNISTUMISPALAUTE_VIRHE_TALLENNUS_MUOKKAUS = 3;
    public static $ONNISTUMISPALAUTE_VIRHE_POISTO = 4;
    public static $ONNISTUMISPALAUTE_VIRHE_MUUTTUJA_EI_MAARITELTY = 5;

    

    /* Luokan rakentaja: */
    public function __construct() {
        parent::__construct();
        $this->onnistumispalaute = Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK;
        $this->sisalto = "";
        $this->valikko_html = "";
        $this->muokatun_id = -1;
        $this->vasen_palkki = Palaute::$EI_LINKKEJA;
        $this->oikea_palkki = "";
        $this->alapalkki = "";
        $this->naytettavat_oliot = Oliotyyppi::$PERUSNAKYMA;
        $this->js_url = "";
        $this->valikkonaytto = true;    // Oletus
        $this->kirjautumistiedot = "";
        $this->kirjautuminen_ok = false;
        $this->painikkeet = "";
        $this->nayttomoodi = Html_tulostus::$nayttomoodi_kaksipalkki_vasen_levea;
    }
    /**
     * Palauttaa muuttujille arvot, jotka niillä oli heti olion luomisen
     * jälkeen. Tämän jälkeen myös "käytössä"-muuttuja on arvoltaan "false".
     */
    public function nollaa_muuttujat(){
        $this->onnistumispalaute = Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK;
        $this->sisalto = "";
        $this->tyhjenna_kaikki_ilmoitukset();
        $this->valikko_html = "";
        $this->muokatun_id = -1;
        $this->vasen_palkki = Palaute::$EI_LINKKEJA;
        $this->oikea_palkki = "";
        $this->alapalkki = "";
        $this->naytettavat_oliot = Oliotyyppi::$PERUSNAKYMA;
        $this->js_url = "";
        $this->valikkonaytto = true;    // Oletus
        $this->kirjautumistiedot = "";
        $this->kirjautuminen_ok = false;
        $this->painikkeet = "";
        $this->nayttomoodi = Html_tulostus::$nayttomoodi_kaksipalkki_vasen_levea;
    }

    /* Setterit ja getterit */
    public function get_kirjautumistiedot(){
        return $this->kirjautumistiedot;
    }
    // Tiedot täytyy olla määritelty!
    public function set_kirjautumistiedot($uusi){
        if(isset($uusi)){
            $this->kirjautumistiedot = $uusi;
            
        }
    }
    
    public function get_kirjautuminen_ok(){
        return $this->kirjautuminen_ok;
    }
    // Tiedot täytyy olla määritelty!
    public function set_kirjautuminen_ok($uusi){
        if(is_bool($uusi)){
            $this->kirjautuminen_ok = $uusi;
        }
    }
    
    public function get_onnistumispalaute(){
        return $this->onnistumispalaute;
    }
    // Virhekoodin täytyy olla määritelty!
    public function set_onnistumispalaute($uusi){
        if(isset($uusi)){
            $this->onnistumispalaute = $uusi;
            
        }
    }

    public function get_valikkonaytto(){
        return $this->valikkonaytto;
    }
    // Virhekoodin täytyy olla määritelty!
    public function set_valikkonaytto($uusi){
        if(isset($uusi)){
            $this->valikkonaytto = $uusi;
            
        }
    }
    
    public function get_painikkeet(){
        return $this->painikkeet;
    }
    // Virhekoodin täytyy olla määritelty!
    public function set_painikkeet($uusi){
        if(isset($uusi)){
            $this->painikkeet = $uusi;
            
        }
    }

    public function get_js_url(){
        return $this->js_url;
    }
    // Virhekoodin täytyy olla määritelty!
    public function set_js_url($uusi){
        if(isset($uusi)){
            $this->js_url = $uusi;
          
        }
    }
    public function get_sisalto(){
        return $this->sisalto;
    }
    public function set_sisalto($uusi){
        $this->sisalto = $uusi;
        
    }
    
    /**
     * Yhteensopivuuden takia vanhat metodit ovat tallella, vaikka muokattuina.
     * 
     * Huomaa, että tämä hakee kaikki tallennetut ilmoitukset (vastaa metodia
     * 'tulosta_kaikki_ilmoitukset()').
     * @return type
     */
    public function get_ilmoitus(){
        return $this->tulosta_kaikki_ilmoitukset();
    }
    
    /**
     * Lisää ilmoituksen ilmoitustaulukkoon. Vastaa Ilmoitus-luokan metodia
     * 'lisaa_kommentti($uusi)'.
     * @param type $uusi
     */
    public function set_ilmoitus($uusi){
        $this->lisaa_kommentti($uusi);
        
    }
    public function get_valikko_html(){
        return $this->valikko_html;
    }
    public function set_valikko_html($uusi){
        $this->valikko_html = $uusi;
        
    }

    public function get_vasen_palkki(){
        return $this->vasen_palkki;
    }
    public function set_vasen_palkki($uusi){
        if(isset($uusi) && is_string($uusi)){
            $this->vasen_palkki = $uusi;
            
        }
    }
    public function get_oikea_palkki(){
        return $this->oikea_palkki;
    }
    public function set_oikea_palkki($uusi){
        if(isset($uusi) && is_string($uusi)){
            $this->oikea_palkki = $uusi;
            
        }
    }
    
    public function get_alapalkki(){
        return $this->alapalkki;
    }
    public function set_alapalkki($uusi){
        if(isset($uusi) && is_string($uusi)){
            $this->alapalkki = $uusi;
            
        }
    }
    
    public function get_nayttomoodi(){
        return $this->nayttomoodi;
    }
    public function set_nayttomoodi($uusi){
        if(isset($uusi)){
            $this->nayttomoodi = $uusi;
        }
    }

    

    public function get_muokatun_id(){
        return $this->muokatun_id;
    }
    public function set_muokatun_id($uusi){ 
        if(isset($uusi)){
            
            if($uusi === false){
                $this->muokatun_id = -1;
            }
            else if(is_numeric($uusi)){
                $this->muokatun_id = $uusi;
            }
        }
    }
    
    

    // Näytettävät oliot (liittyy css:n säätöön esim.):
    public function get_naytettava_oliotyyppi(){
        return $this->naytettava_oliotyyppi;
    }
    public function set_naytettava_oliotyyppi($uusi){
        $this->naytettava_oliotyyppi = $uusi;
        
    }
    /**
     * Asettaa onnistumispalaute-muuttujan arvon joko arvoon
     * Palaute::$OPERAATIO_ONNISTUI, jos $bool-parametrin arvo on true.
     * Muuten muuttujalle annetaan arvo Palaute::$VIRHE;
     * @param type $bool
     */
    public function set_operaatio_onnistui($bool){
        if($bool){
            $this->onnistumispalaute = Palaute::$OPERAATIO_ONNISTUI;
        } else{
            $this->onnistumispalaute = Palaute::$VIRHE;
        }
    }
}
?>
