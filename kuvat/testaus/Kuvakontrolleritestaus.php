<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kuvakontrolleritestaus
 *
 * @author J-P
 */
class Kuvakontrolleritestaus extends Testiapu_kuvat{
    //put your code here
    
     /**
     * @param Tietokantaolio $tietokantaolio
     */
    function __construct($tietokantaolio, $parametriolio){
        parent::__construct($tietokantaolio, $parametriolio, "Kuvakontrolleri");
        
        
    }
      /**
     * Kutsuu kaikkia kuviin liittyviä testejä ja palauttaa Testipalaute 
     * -luokan olion.
     * @param Tietokantaolio $tietokantaolio
     * @return Testipalaute $testipalaute
     */
    function toteuta_testit() {
        
        $ots = "Testataan Kuvakontrolleri-luokkaa.";
        
        $this->lisaa_virheilmoitus("Kuvakontrolleritestit toteuttamatta!");
        //=====================================================================
        //$this->testaa_kuvan_luominen();
        //$this->testaa_kuvan_muokkaus();
        //$this->testaa_kuvan_poisto();

        //$this->siivoa_jaljet();
        //=====================================================================

        $virheilm = $this->tulosta_virheilmoitukset();
        $sis = $this->tulosta_kaikki_ilmoitukset();
        $virheilm_lkm = $this->virheilmoitusten_lkm();
        
        
        $palaute = new Testipalaute($ots, $virheilm, $sis, $virheilm_lkm);
        return $palaute;
        //==========================================================================
    }
}

?>
