<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Tämä on testausta varten kehitetty olio, jolla on vastine tietokannassa.
 * Tämän avulla testataan erityisesti Malliluokkapohjaa (Malliluokkapohjatesti).
 * Tietokantataulu on seuraavanlainen:
 * create table testiolio
   ( 
     id                    mediumint auto_increment not null,
     luomishetki_sek       int not null,
     ed_muutos_sek         int not null,
     kommentti		   varchar(1000) not null,
     primary key (id)   
   ) ENGINE=INNODB;
 * 
 * @author J-P
 */
class Testiolio extends Malliluokkapohja{
    
    // Näiden pitää ehdottomasti olla samat kuin tietokantasarakkeiden nimet!
    // CRUD-toiminnot perustuvat näihin.
    public static $SARAKENIMI_LUOMISHETKI_SEK = "luomishetki_sek";
    public static $SARAKENIMI_ED_MUUTOS_SEK = "ed_muutos_sek";
    public static $SARAKENIMI_KOMMENTTI = "kommentti";
    
    function __construct($tietokantaolio, $id){
        
        // Määritellään tietokantatiedot. True, jos on luku, muuten false.
        $tietokantasolut = 
            array(new Tietokantasolu(Testiolio::$SARAKENIMI_ID, Tietokantasolu::$luku_int),
                new Tietokantasolu(Testiolio::$SARAKENIMI_LUOMISHETKI_SEK, Tietokantasolu::$luku_int),
                new Tietokantasolu(Testiolio::$SARAKENIMI_ED_MUUTOS_SEK, Tietokantasolu::$luku_int),
                new Tietokantasolu(Testiolio::$SARAKENIMI_KOMMENTTI, Tietokantasolu::$mj_tyhja_EI_ok));
        
        $taulunimi = "testiolio";
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
    }
    
    public function on_tallennuskelpoinen($uusi) {
        
    }

    public function hae($id) {
        
    }
}

?>
