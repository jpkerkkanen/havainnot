<?php

/**
 * Description of Havaintojakso: Havaintojaksolla tarkoitetaan havaintotapahtumaa, 
 * joka voi olla vaikkapa viiden minuutin tarkkailu, tornien taisto tai viikon matka. 

   Periaatteessa kaikki havainnot voisivat liittyä havaintojaksoon tai siis 
 * johonkin tapahtumaan, mutta ei
   pakoteta tai miksikäs ei voisi pakottaakin, koska tällöin voisi havainnot
 * näyttää tapahtumaperusteisesti ilman, että havaintoja jää pois (siis tästä
 * eteenpäin). Havainto voi liittyä useampaan jaksoon: vaikkapa tunnin bongaus
 * viikon Hondurasin matkalla.
 * 
create table havaintojaksot
(
  id                    int auto_increment not null,
  henkilo_id            int default -1 not null,
  lajiluokka_id         int default -1 not null,
  alkuaika_sek          int not null,
  kesto_min             int not null,
  nimi                  varchar(50),
  kommentti             varchar(3000),
  nakyvyys              smallint default -1 not null,
  primary key (id),
  index(henkilo_id),
  index(lajiluokka_id),
  index (alkuaika_sek),
  index (kesto_min),
  FOREIGN KEY (lajiluokka_id) REFERENCES lajiluokat (id)
                      ON DELETE CASCADE
) ENGINE=INNODB;
 * @author J-P
 */
class Havaintojakso extends Malliluokkapohja {
    
    public static $SARAKENIMI_HENKILO_ID= "henkilo_id";
    public static $SARAKENIMI_LAJILUOKKA_ID= "lajiluokka_id";
    public static $SARAKENIMI_ALKUAIKA_SEK= "alkuaika_sek";
    public static $SARAKENIMI_KESTO_MIN= "kesto_min";
    public static $SARAKENIMI_NIMI= "nimi";
    public static $SARAKENIMI_KOMMENTTI= "kommentti";
    public static $SARAKENIMI_NAKYVYYS= "nakyvyys";
    
    public static $taulunimi = "havaintojaksot";
    /**
     * @param Tietokantaolio $tietokantaolio
     * @param int $id olion id tietokannassa
     */
    function __construct($id, $tietokantaolio){
        $tietokantasolut = 
            array(new Tietokantasolu(Havaintojakso::$SARAKENIMI_ID, 
                                                    Tietokantasolu::$luku_int),  
                new Tietokantasolu(Havaintojakso::$SARAKENIMI_HENKILO_ID, 
                                                    Tietokantasolu::$luku_int), 
                new Tietokantasolu(Havaintojakso::$SARAKENIMI_LAJILUOKKA_ID, 
                                                    Tietokantasolu::$luku_int), 
                new Tietokantasolu(Havaintojakso::$SARAKENIMI_ALKUAIKA_SEK, 
                                                    Tietokantasolu::$luku_int), 
                new Tietokantasolu(Havaintojakso::$SARAKENIMI_KESTO_MIN,
                                                    Tietokantasolu::$luku_int), 
                new Tietokantasolu(Havaintojakso::$SARAKENIMI_NIMI, 
                                                    Tietokantasolu::$mj_tyhja_EI_ok), 
                new Tietokantasolu(Havaintojakso::$SARAKENIMI_KOMMENTTI, 
                                                    Tietokantasolu::$mj_tyhja_ok), 
                new Tietokantasolu(Havaintojakso::$SARAKENIMI_NAKYVYYS, 
                                                    Tietokantasolu::$luku_int));
        
        $taulunimi = Havaintojakso::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
        
        $this->poistetut_pikakommentit_lkm = 0;
    }
}
