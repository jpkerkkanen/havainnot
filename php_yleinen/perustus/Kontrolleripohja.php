<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kontrolleripohja
 *
 * @author kerkjuk_admin
 */
abstract class Kontrolleripohja extends Pohja{
    //put your code here
    private $tietokantaolio, $parametriolio, $olio, $oliot;
    
    /**
     * @var Henkilo $kayttaja Henkilo-luokan olio, joka luotu
     * $parametriolio->omaid:n pohjalta.
     */
    private $kayttaja; // Sovelluksen käyttäjä.
    
    
    function __construct($tietokantaolio, $parametriolio){
        parent::__construct();
        if($tietokantaolio instanceof Tietokantaolio){
            $this->tietokantaolio = $tietokantaolio;
        }
        else{
            $this->tietokantaolio = Kontrolleripohja::$MUUTTUJAA_EI_MAARITELTY;
        }
        
        if($parametriolio instanceof Parametrit){
            $this->parametriolio = $parametriolio;
        }
        else{
            $this->parametriolio = Kontrolleripohja::$MUUTTUJAA_EI_MAARITELTY;
        }
        
        // luodaan käyttäjä. Tiedot yritetään hakea tietokannasta. Ellei onnistu,
        // jää jäljelle tyhjä Henkilo-luokan olio (liekö parempi näin? Ainakin
        // tällöin metodit toimivat.:
        $this->kayttaja = 
                new Henkilo($this->parametriolio->get_omaid(), 
                            $this->tietokantaolio);
        
        $this->olio = Kontrolleripohja::$MUUTTUJAA_EI_MAARITELTY;
        $this->oliot = array();
    }
    
    /**
     * Palauttaa aina Henkilo-luokan olion, joka voi olla "uusi", ellei
     * kunnollista omaid-arvoa löytynyt. Yleensä pitäisi olla tietokannassa
     * oleva henkilö kuitenkin.
     * @return \Henkilo $kayttaja
     */
    public function get_kayttaja(){
        return $this->kayttaja;
    }
    
    
    /**
     * @return \Palaute $palauteolio
     */
    public function get_palauteolio(){
        return $this->palauteolio;
    }
    
    /**
     * Palauttaa tietokantaolio-muuttujan arvon. Hmm haittaako, jos arvo onkin
     * ei määritelty?
     * @return \Tietokantaolio $tietokantaolio
     */
    public function get_tietokantaolio(){
        return $this->tietokantaolio;
    }
    
    /**
     * Palauttaa parametriolio-muuttujan arvon. Hmm haittaako, jos arvo onkin
     * ei määritelty?
     * @return \Parametrit $parametriolio
     */
    public function get_parametriolio(){
        return $this->parametriolio;
    }
    
    /** Käytössä oleva olio:*/    
    public function get_olio(){
        return $this->olio;
    }
    public function set_olio($uusi){
        $this->olio = $uusi;
    }
    
    /** Oliot taulukossa */
    public function get_oliot(){
        return $this->oliot;
    }
    
    /**
     * Asettaa parametritaulukon oliot-muuttujan arvoksi, jos parametri on
     * taulukko. Muuten ei tee mitään.
     * @param type $oliotaulukko
     */
    public function set_oliot($oliotaulukko){
        if(is_array($oliotaulukko)){
            $this->oliot = $oliotaulukko;
        }
    }

    // Pakkototeutettavat metodit perittäessä.
    /**
     * Tallentaa uuden olion tietokantaan ja tallentaa aktiivisuudet ja 
     * tekee jatkotoimenpiteet onnistumisten perusteella.
     * 
     * Palauteolio on parametrina, jotta samaa oliota voidaan käyttää koko ajan.
     * Helpottaa esim. viestien lähetystä eri toiminnoista. Muokkaukset
     * palautuvat kutsujalle.
     * 
     * @param \Palaute $palauteolio
     */
    public abstract function toteuta_tallenna_uusi(&$palauteolio);
    
    /**
     * @param \Palaute $palauteolio
     */
    public abstract function toteuta_tallenna_muokkaus(&$palauteolio);
    
    /**
     * @param \Palaute $palauteolio
     */
    public abstract function toteuta_poista(&$palauteolio);
    
    /**
     * @param \Palaute $palauteolio
     */
    public abstract function toteuta_nayta_poistovarmistus(&$palauteolio);
    
    /**
     * @param \Palaute $palauteolio
     */
    public abstract function toteuta_nayta(&$palauteolio);
    
    
}

?>
