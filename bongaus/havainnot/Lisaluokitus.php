<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Lisaluokitus: Luokka, joka yhdistää havaintoon jonkin
 * lisäluokituksen. Tietokantataulu:
 * create table havainnon_lisaluokitukset
(
  id                    int auto_increment not null,
  havainto_id           int default -1 not null,
  lisaluokka            int default -1 not null,
  primary key (id),
  index(havainto_id),
  index(lisaluokka),
  FOREIGN KEY (havainto_id) REFERENCES havainnot (id) ON DELETE CASCADE
) ENGINE=INNODB;
 * 
 * Luokkaan liittyy läheisesti asetusluokka Lisaluokitus_asetukset, jossa
 * määritellään lisäluokitusarvot ja niiden merkitykset. Tämä luokka hoitaa
 * lisäluokitusarvojen tietokantaoperaatiot.
 *
 * @author J-P
 */
class Lisaluokitus extends Malliluokkapohja{
    
    public static $SARAKENIMI_HAVAINTO_ID= "havainto_id";
    public static $SARAKENIMI_LISALUOKITUS= "lisaluokka";
    
    public static $taulunimi = "havainnon_lisaluokitukset";
    
    /**
     * @param Tietokantaolio $tietokantaolio
     * @param int $id olion id tietokannassa 
     */
    function __construct($id, $tietokantaolio){
        $tietokantasolut = 
            array(new Tietokantasolu(Lisaluokitus::$SARAKENIMI_ID, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Lisaluokitus::$SARAKENIMI_HAVAINTO_ID, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Lisaluokitus::$SARAKENIMI_LISALUOKITUS, Tietokantasolu::$luku_int));
        
        $taulunimi = Lisaluokitus::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
    } 
}

?>
