<?php

/*
 *create table paikat
(
  id                    int auto_increment not null,
  henkilo_id            int default -1 not null,
  nimi                  varchar(30) not null,
  selitys               varchar(200) not null,
  maa                   int not null,
  primary key (id),
  index(henkilo_id),
  index(maa),
  FOREIGN KEY (henkilo_id) REFERENCES henkilot (id)
                      ON DELETE CASCADE
) ENGINE=INNODB;
 */

/**
 * Tämä huolehtii omien paikkojen tallentamisesta. Näin havainto voidaan
 * yhdistää paikan id:hen, mikä helpottaa paikkojen hallinnointia ja 
 * mahdollistaa esimerkiksi havaintojen haun paikan mukaan. Esimerkiksi
 * lisäluokitusten ongelma on se, että vaikkapa kotipiha voi muuttua, eikä
 * tieto siitä jää talteen. Omien paikkojen avulla päästään eroon tästä
 * ongelmasta.
 * 
 * Henkilö_id
 *
 * @author J-P
 */
class Havaintopaikka extends Malliluokkapohja{
    public static $SARAKENIMI_HENKILO_ID= "henkilo_id";
    public static $SARAKENIMI_NIMI= "paikannimi";
    public static $SARAKENIMI_SELITYS= "selitys";
    public static $SARAKENIMI_MAA_ID= "maa_id";
    
    public static $taulunimi = "havaintopaikat";
    
    // Tallennetaan tietokantaan vakipaikka_id:n kohdalle silloin, kun 
    // vakipaikkaa ei ole määritelty. Huomaa, että Pohja::muuttujaa_ei_maaritelty
    //-arvoa ei voi tallentaa tietokantaan (ei mene läpi tarkistuksista).
    public static $ei_asetettu = -1;
    /**
     * @param Tietokantaolio $tietokantaolio
     * @param int $id olion id tietokannassa
     */
    function __construct($id, $tietokantaolio){
        $tietokantasolut = 
            array(new Tietokantasolu(Havaintopaikka::$SARAKENIMI_ID, 
                                    Tietokantasolu::$luku_int, 
                                    $tietokantaolio),  
                new Tietokantasolu(Havaintopaikka::$SARAKENIMI_HENKILO_ID, 
                                    Tietokantasolu::$luku_int, 
                                    $tietokantaolio),  
                new Tietokantasolu(Havaintopaikka::$SARAKENIMI_NIMI, 
                                    Tietokantasolu::$mj_tyhja_EI_ok, 
                                    $tietokantaolio), 
                new Tietokantasolu(Havaintopaikka::$SARAKENIMI_SELITYS, 
                                    Tietokantasolu::$mj_tyhja_ok, 
                                    $tietokantaolio),
                new Tietokantasolu(Havaintopaikka::$SARAKENIMI_MAA_ID, 
                                    Tietokantasolu::$luku_int, 
                                    $tietokantaolio));

        
        $taulunimi = Havaintopaikka::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
       
    }
    
    /**
     * Hakee tietokannasta kyseisen käyttäjän havaintopaikat ja palauttaa ne
     * Havaintopaikka-luokan olioina taulukossa (array).
     * 
     * @param Tietokantaolio $tietokantaolio
     * @param int $id_henk_para Käyttäjän id
     */
    public static function hae_omat_paikat($tietokantaolio, $id_henk_para){

        $id_henk = $id_henk_para+0;  // Muutos integeriksi varmuuden vuoksi.
        
        $oliot = array();   // Tämä sisältää Havaintopaikka-luokan oliot.
        
        if(is_int($id_henk) && $id_henk > 0){
            
            $hakulause = "SELECT ".Havaintopaikka::$SARAKENIMI_ID.
                    " FROM ". Havaintopaikka::$taulunimi.
                    " WHERE ".Havaintopaikka::$SARAKENIMI_HENKILO_ID." = '".$id_henk.
                    "' ORDER BY ". Havaintopaikka::$SARAKENIMI_NIMI;
        
            $tulostaulu = 
                $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);

            if(!empty($tulostaulu)){
                foreach ($tulostaulu as $tk_olio) {

                    $id = $tk_olio->id;
                    $olio = new Havaintopaikka($id, $tietokantaolio);
                    if($olio->olio_loytyi_tietokannasta){
                        array_push($oliot, $olio);
                    }
                }
            }
        }
        
        return $oliot;
    }
    
    /**
     * Palauttaa parametrina saatujen Havaintopaikka-luokan olioiden id-arvot
     * taulukossa.
     * @param array $paikat Havaintopaikka-luokan oliot taulukossa.
     */
    public static function hae_paikkojen_idt($paikat){
        
        $idtaulu = array();   // Tämä sisältää idt.
        
        foreach ($paikat as $paikka) {
            if($paikka instanceof Havaintopaikka){
                array_push($idtaulu, $paikka->get_id());
            }
        }
        return $idtaulu;
    }
    
    /**
     * Palauttaa parametrina saatujen Havaintopaikka-luokan olioiden 
     * valikkoon sopivat nimitykset taulukossa.
     */
    public static function hae_paikkojen_valikkonimet($paikat){
        
        $nimitaulu = array();   // Tämä sisältää nimet.
        
        foreach ($paikat as $paikka) {
            if($paikka instanceof Havaintopaikka){

                $nimi = $paikka->get_arvo(Havaintopaikka::$SARAKENIMI_NIMI);
                
                array_push($nimitaulu, $nimi);
            }
        }
        return $nimitaulu;
    }
}
