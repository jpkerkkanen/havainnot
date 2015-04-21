<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Käyttäjätunnusten, salasanojen ja vastaavien ominaisuudet ja tarkistukset.
 *
 * @author J-P
 */
class Tunnukset {
  
    public static $sallitut_merkit = "a-zA-Z0-9.:;-_*?!";
    public static $pituus_min_kayttajatunnus = 2;
    public static $pituus_max_kayttajatunnus = 30;
    public static $pituus_min_salasana = 7;
    public static $pituus_max_salasana = 30;
    
    // Poppoon käyttäjätunnus pitää olla pitempi:
    public static $pituus_min_kayttajatunnus_poppoo = 10;
    public static $pituus_max_kayttajatunnus_poppoo = 50;
    
    // Muokkaustapa:
    public static $ei_muokata = 1;
    public static $kumpikin = 2;
    public static $vain_salis = 3;
    public static $vain_kayttis = 4;
    
     /**
     * Tarkistaa, että annettu syöte sisältää vain tunnukselle sallittuja
     * merkkejä [a-zA-Z0-9.:;-_*?!] ja että syötteen pituus on annettujen
     * lukujen min_pit ja max_pit välissä.
     * 
     * Lisäksi tarkistaa, ettei metodit htmlspecialchars() eikä 
     * mysql_escape_string() muuta syötettä (muuten esim '<>' meni läpi!).
     * 
     * Palauttaa true, jos kaikki kunnossa, muuten false.
     * 
     * @param type $syote
     */
    static function tunnuksen_merkit_ja_pituus_ok($syote, $min_pit, $max_pit){
        
        // Jos merkit ok eikä html- tms tunnuksia sotkemassa.
        if((preg_match('/^['.Tunnukset::$sallitut_merkit.']{'.
                            $min_pit.','.$max_pit.'}$/',$syote) === 1) && 
            $syote === trim(htmlspecialchars(mysql_escape_string($syote)))){
            return true;
        } else{
            return false;
        }
    }
    
    static function email_merkit_ok($syote){
        if(preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/")===1){
            return true;
        } else{
            return false;
        }
    }
    
    /**
     * Tarkistaa käyttäjätunnuksen merkit ja pituuden. Ei tee tietokantahakuja.
     */
    static function kayttajatunnus_ok($ktunnus){
        if(Tunnukset::tunnuksen_merkit_ja_pituus_ok(
                                $ktunnus, 
                                Tunnukset::$pituus_min_kayttajatunnus, 
                                Tunnukset::$pituus_max_kayttajatunnus)){
            return true;
        } else{
            return false;
        }
    }
    /**
     * Tarkistaa salasana merkit ja pituuden. Ei tee tietokantahakuja.
     */
    static function salasana_ok($sala){
        if(Tunnukset::tunnuksen_merkit_ja_pituus_ok(
                                $sala, 
                                Tunnukset::$pituus_min_salasana, 
                                Tunnukset::$pituus_max_salasana)){
            return true;
        } else{
            return false;
        }
    }
}

?>
