<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Aktiivisuus:
create table aktiivisuus
(
  id                    int auto_increment not null,
  henkilo_id            int not null,
  viimeksi_aktiivi	int not null default 0, /* aika sekunteina (timestamp)*
  aktiivisuuslaji       smallint default 0,  
  primary key (id),
  index(henkilo_id),
  index(viimeksi_aktiivi),
  FOREIGN KEY (henkilo_id) REFERENCES henkilot (id)
                      ON DELETE CASCADE
)ENGINE=INNODB;
 * @author J-P
 */
class Aktiivisuus extends Malliluokkapohja{
    public static $sarakenimi_henkilo_id = "henkilo_id";
    public static $sarakenimi_aika = "aika";
    public static $sarakenimi_aktiivisuuslaji = "aktiivisuuslaji";
    
    public static $taulunimi = "aktiivisuus";

    public static $SISAANKIRJAUTUMINEN = 0; //
    public static $ULOSKIRJAUTUMINEN = 1; //
    public static $MUU_AKTIIVISUUS = 2; //
    
    public static $HAVAINTOTALLENNUS_UUSI = 4;
    public static $KOMMENTTITALLENNUS_UUSI = 5;
    
    public static $KUVATALLENNUS_MUOKKAUS = 6;
    public static $KUVA_POISTO = 19;
    public static $KUVATALLENNUS_UUSI = 3;
    
    public static $HAVAINTOTALLENNUS_MUOKKAUS = 7;
    public static $KOMMENTTITALLENNUS_MUOKKAUS = 8;
    public static $LAJILUOKKATALLENNUS_UUSI = 9;
    public static $LAJILUOKKATALLENNUS_MUOKKAUS = 10;
    public static $KUVAUSTALLENNUS_UUSI = 11;
    public static $KUVAUSTALLENNUS_MUOKKAUS = 12;
    
    public static $POPPOOTALLENNUS_UUSI = 13;
    public static $POPPOOTALLENNUS_MUOKKAUS = 14;
    public static $POPPOON_POISTO = 15;
    
    public static $HENKILON_TALLENNUS_UUSI = 16;
    public static $HENKILON_TALLENNUS_MUOKKAUS = 17;
    public static $HENKILON_POISTO = 18;
    
    
    
    function __construct($id, $tietokantaolio) {
        
        $tietokantasolut = 
            array(new Tietokantasolu(Aktiivisuus::$SARAKENIMI_ID, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Aktiivisuus::$sarakenimi_henkilo_id, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Aktiivisuus::$sarakenimi_aika, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Aktiivisuus::$sarakenimi_aktiivisuuslaji, Tietokantasolu::$luku_int));
        
        $taulunimi = Aktiivisuus::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
    }
}

?>
