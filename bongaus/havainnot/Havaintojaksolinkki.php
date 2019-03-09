<?php

/**
 * Description of Havaintojaksolinkit: 
 * Havaintojaksolinkit yhdistävät havaintojaksot havaintoihin, jolloin
   yksi havainto voi kuulua useampaan jaksoon ja luonnollisesti toisin päin. 
 * Jos havaintojakso hävitetään, häviää havaintojaksolinkitkin, jotka liittyvät 
 * kyseiseen havaintojaksoon.
 *
create table havaintojaksolinkit
(
  id                    int auto_increment not null,
  havainto_id           int default -1 not null,
  havaintojakso_id      int default -1 not null,
  primary key (id),
  index(havaintojakso_id),
  index(havainto_id),
  FOREIGN KEY (havaintojakso_id) REFERENCES havaintojaksot (id)
                      ON DELETE CASCADE
) ENGINE=INNODB;
 * 
 * @author J-P
 */
class Havaintojaksolinkki extends Malliluokkapohja {
    
    public static $SARAKENIMI_HAVAINTO_ID= "havainto_id";
    public static $SARAKENIMI_HAVAINTOJAKSO_ID= "havaintojakso_id";
    
    public static $taulunimi = "havaintojaksolinkit";
    /**
     * @param Tietokantaolio $tietokantaolio
     * @param int $id olion id tietokannassa
     */
    function __construct($id, $tietokantaolio){
        $tietokantasolut = 
            array(new Tietokantasolu(Havaintojaksolinkki::$SARAKENIMI_ID, 
                                                    Tietokantasolu::$luku_int, $tietokantaolio),  
                new Tietokantasolu(Havaintojaksolinkki::$SARAKENIMI_HAVAINTO_ID, 
                                                    Tietokantasolu::$luku_int, $tietokantaolio), 
                new Tietokantasolu(Havaintojaksolinkki::$SARAKENIMI_HAVAINTOJAKSO_ID, 
                                                    Tietokantasolu::$luku_int, $tietokantaolio));
        
        $taulunimi = Havaintojaksolinkki::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
    }
}
