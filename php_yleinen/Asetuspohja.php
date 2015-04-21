<?php
/**
 * Monissa asetuksissa tietty luku vastaa tiettyä tilaa, jota kuvataan
 * merkkijonolla (nimi). Vastaava luku on kuitenkin helpompi käsitellä esim.
 * tietokannassa. Asetukseen on usein mukava liittää myös selitys tms.
 * Näiden asetusten käsittelyn avuksi on seuraava abstrakti luokka:
 */
abstract class Asetuspohja{
    
    private $parit; // nimi-arvo -parit
    /**
     * Luokan rakentajaan annetaan parametrina taulukossa käsiteltävät
     * Asetus-luokan oliot.
     * @param type $nimiarvoparit
     */
    function __construct($asetukset) {
        
        $this->parit = array();
        
        // Tarkistetaan parametritaulukko:
        foreach ($asetukset as $luokka) {
            if($luokka instanceof Asetus){
                array_push($this->parit, $luokka);
            }
        }
    }
    
    /**
     * Palauttaa taulukon, joka sisältää Asetus-luokan oliot.
     */
    public function get_asetukset(){
        return $this->parit;
    } 
    
    /**
     * @return <type> Palauttaa taulukon, joka sisältää vaihtoehtojen numeroarvot
     */
    public function hae_arvot(){
        $arvot = array();
        $luokat = $this->parit;
        
        foreach ($luokat as $luokka) {
            if($luokka instanceof Asetus){
                array_push($arvot, $luokka->get_arvo());
            }
        }
        return $arvot;
    }

    /**
     * Palauttaa arvoja vastaavat (samassa järjestyksessä)
     * nimet taulukkona. 
     * @return <type> Palauttaa taulukon, joka sisältää nimet.
     */
    public function hae_nimet(){
        $nimet = array();
        $luokat = $this->parit;
        
        foreach ($luokat as $luokka) {
            if($luokka instanceof Asetus){
                array_push($nimet, $luokka->get_nimi());
            }
        }

        return $nimet;
    }
    /**
     * Palauttaa lukua eli indeksiä vastaavan nimiarvopari-nimen, tai
     * tekstin "Tuntematon", jos parametri on sopimaton.
     * @param <type> $arvo
     * @return <type>
     */
    public function hae_nimi($arvo){
        $nimi = "Tuntematon";
        
        foreach ($this->parit as $luokka) {
            if($luokka instanceof Asetus){
                if($luokka->get_arvo() == $arvo){
                    $nimi = $luokka->get_nimi();
                    break;
                }
            }
        }
        return $nimi;
    }
}

/**
 * Tämä luokka pitää huolta yhden asetuksen (tilan) tiedoista.
 */
class Asetus{
    private $nimi, $arvo, $selitys;     // selitys sopii esim. title-arvoksi.

    /**/
    public function __construct($nimi, $arvo, $selitys) {
        $this->arvo = $arvo;
        $this->nimi = $nimi;
        $this->selitys = $selitys;
    }
    
    public function get_nimi(){
        return $this->nimi;
    } 
    public function get_arvo(){
        return $this->arvo;
    } 
    public function get_selitys(){
        return $this->selitys;
    } 
}
?>
