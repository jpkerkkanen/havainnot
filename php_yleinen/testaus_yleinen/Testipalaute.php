<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Tämän luokan avulla eri testien tulokset voidaan koota kätevästi yhteen.
 * Tarkoitus on, että kunkin moduulin toteuta_-alkuiset testikoonnit
 * palauttavat tämän luokan olion.
 *
 * @author J-P
 */
class Testipalaute {
    // Seuraavat ovat kaikki merkkijonoja.
    private $otsikko, $sisalto, $virheilmoitukset, $virheilm_lkm;
    
    // Luokan rakentaja:
    public function __construct($ots, $virheilm, $sis, $virheilm_lkm) {
        $this->otsikko = $ots;
        $this->virheilmoitukset = $virheilm;
        $this->sisalto = $sis;
        $this->virheilm_lkm = $virheilm_lkm;
    }
    
    public function get_otsikko(){
        return $this->otsikko;
    }
    public function get_sisalto(){
        return $this->sisalto;
    }
    public function get_virheilmoitukset(){
        return $this->virheilmoitukset;
    }
    public function get_virheilmoitusten_lkm(){
        return $this->virheilm_lkm;
    }
}

?>
