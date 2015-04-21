<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Ilmoitus sisältää erilaiset viestit, joita ohjelma voi lähettää 
 * käyttäjälle tai esimerkiksi testiohjelma testaajalle.
 *
 * @author J-P
 */
class Ilmoitus {
    private $viesti, $tyyppi;
    
    public static $TYYPPI_VIRHEILMOITUS = 1;
    public static $TYYPPI_ILMOITUS = 2;
    //public static $TYYPPI_3 = 3;
    
    public function __construct($viesti, $tyyppi) {
        
        // Annetaan jotkin arvot oletukseksi, ettei jää määrittelemättömiä.
        $this->viesti = "";
        $this->tyyppi = Ilmoitus::$TYYPPI_ILMOITUS;
        
        // Tarkistetaan ja sijoitetaan parametrit:
        if(isset($viesti)){
            $this->viesti = $viesti;
        }
        if($this->tyyppiarvo_ok($tyyppi)){
            $this->tyyppi = $tyyppi;
        }
    }
    
    public function get_viesti(){
        return $this->viesti;
    }
    public function get_tyyppi(){
        return $this->tyyppi;
    }
    public function set_viesti($viesti){
        if(isset($viesti)){
            $this->viesti = $viesti;
        }
    }
    public function set_tyyppi($tyyppi){
        if($this->tyyppiarvo_ok($tyyppi)){
            $this->tyyppi = $tyyppi;
        }
    }
    /**
     * Tutkii, onko $ehdokas-parametri oikean tyyppinen tyyppi-muuttujaksi.
     * Palauttaa TRUE, jos on, ja muuten FALSE.
     * @param type $ehdokas
     * @return boolean 
     */
    public function tyyppiarvo_ok($ehdokas){
        if(isset($ehdokas) && 
                (($ehdokas === Ilmoitus::$TYYPPI_ILMOITUS) || 
                ($ehdokas === Ilmoitus::$TYYPPI_VIRHEILMOITUS))){
            return true;
        } 
        else{
            return false;
        }
    }
    
    
    //==========================================================================
    // Alla staattisia metodeita, joilla hallitaan ilmoituksia. Parametrina
    // saatua taulukkoa muokataan (tarvittaessa) ja muutokset välitetään 
    // kutsujalle (&). Vaihtoehtoisesti palautetaan kysytty arvo.
    
    /**
     * Lisää uuden virheilmoituksen ilmoitukset-taulukkoon.
     * @param <type> $uusi
     */
    public static function lisaa_virheilmoitus($ilmoitus, &$ilmoitukset){
        array_push($ilmoitukset, 
                    new Ilmoitus($ilmoitus, Ilmoitus::$TYYPPI_VIRHEILMOITUS));
    }
    
    /**
     * Laskee virheilmoitusten lukumäärän. Palauttaa sen.
     */
    public static function virheilmoitusten_lkm($ilmoitukset){
        $lkm = 0;
        foreach ($ilmoitukset as $ilmoitus) {
            if($ilmoitus->get_tyyppi() === Ilmoitus::$TYYPPI_VIRHEILMOITUS){
                $lkm++;
            }
        }
        return $lkm;
    }

    /**
     * Tulostaa virheilmoitukset rivinvaihdolla erotettuina.
     */
    public static function tulosta_virheilmoitukset($ilmoitukset){
        $tulostus = " ";
        foreach ($ilmoitukset as $ilmoitus) {
            //if(!empty($ilmoitus->get_viesti()) && OUTO KUN EI HYVÄKSY
            if(($ilmoitus->get_viesti() != "") && 
            ($ilmoitus->get_tyyppi() == Ilmoitus::$TYYPPI_VIRHEILMOITUS)){
                $tulostus .= $ilmoitus->get_viesti()." <br />";
            }
        }
        return $tulostus;
    }
    
    /**
     * Tulostaa viimeksi tulleen virheilmoituksen (taulukon viimeisen).
     */
    public static function tulosta_viimeisin_virheilmoitus($ilmoitukset){
        $tulostus = " ";
        foreach ($ilmoitukset as $ilmoitus) {
            
            //if(!empty($ilmoitus->get_viesti()) && OUTO KUN EI HYVÄKSY
            if(($ilmoitus->get_viesti() != "") && 
            ($ilmoitus->get_tyyppi() == Ilmoitus::$TYYPPI_VIRHEILMOITUS)){
                $tulostus = $ilmoitus->get_viesti();
            }
        }
        return $tulostus;
    }
    

    /**
     * Poistaa (vain) virheilmoitukset ilmoitustaulukosta.
     */
    public static function tyhjenna_virheilmoitukset(&$ilmoitukset){
        $aputaulukko = array();
        
        // Ilmoituksen poisto vaikutti hankalalta, joten tässä tehdään uusi
        // taulukko, jossa ei virheilmoituksia ja asetetaan se $ilmoitukset
        // -taulukon arvoksi.
        foreach ($ilmoitukset as $ilmoitus) {
            if($ilmoitus->get_tyyppi() !== Ilmoitus::$TYYPPI_VIRHEILMOITUS){
                array_push($aputaulukko, $ilmoitus);
            }
        }
        $ilmoitukset = $aputaulukko;
    }

    public static function lisaa_ilmoitus($ilmoitus, $tyyppi,&$ilmoitukset) {
        if(is_string($ilmoitus) && (trim($ilmoitus) != "")){
            array_push($ilmoitukset, new Ilmoitus($ilmoitus, $tyyppi));
        }
    }
        

    public static function tulosta_kaikki_ilmoitukset($ilmoitukset) {
        $tulostus = "";
        foreach ($ilmoitukset as $ilmoitus) {
            if($ilmoitus->get_viesti() != ""){
                $tulostus .= $ilmoitus->get_viesti()."<br />";
            }
        }
        return $tulostus;
    }

    public static function tyhjenna_kaikki_ilmoitukset(&$ilmoitukset) {
        $ilmoitukset = array();
    }
    //==========================================================================
}

?>
