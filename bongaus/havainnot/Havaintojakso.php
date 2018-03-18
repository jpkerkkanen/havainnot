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
 * Havaintojaksoon liittyy luontevasti havaintoja eri ihmisiltä ja eri lajeista.
 * Tällöin esimerkiksi Kimmon havaitsema lehmä ja Viivin pikku-uikku tornien
 * taistossa menevät saman havaintojakson alle.
 * 
 * Muokkauksessa pitää vain ottaa huomioon, että vain omia havaintoja voi 
 * muokata. Havaintojakson tietoja voi ehkä muokata vain luoja, ellei sitten
 * anna oikeutta myös muille. Tätä ehtii miettiä myöhemmin.
 * 
create table havaintojaksot
(
  id                    int auto_increment not null,
  henkilo_id            int default -1 not null,
  alkuaika_sek          int not null,
  kesto_min             int not null,
  nimi                  varchar(50),
  kommentti             varchar(3000),
  nakyvyys              smallint default -1 not null,
  primary key (id),
  index(henkilo_id),
  index (alkuaika_sek),
  index (kesto_min),
  index (nimi),
  FOREIGN KEY (lajiluokka_id) REFERENCES lajiluokat (id)
                      ON DELETE CASCADE
) ENGINE=INNODB;
 * @author J-P
 */
class Havaintojakso extends Malliluokkapohja {
    
    public static $SARAKENIMI_HENKILO_ID= "henkilo_id";
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
    /**
     * Palauttaa havaintojakson kokonaiskeston minuutteina.
     */
    public function get_kestomin_total(){
        return $this->get_arvo(Havaintojakso::$SARAKENIMI_KESTO_MIN);
    }
    
    /**
     * Palauttaa lasketun keston vuorokausiosan.
     * @return type
     */
    public function get_keston_vrk(){
        $kestomin = intval($this->get_kestomin_total());
        $vrk = floor($kestomin / (60*24));
        return $vrk;
    }
    
    /**
     * Palauttaa lasketun keston tuntiosan.
     * @return type
     */
    public function get_keston_h(){
        $kestomin = intval($this->get_kestomin_total());
        $vrk = $this->get_keston_vrk();
        $hours = floor(($kestomin-$vrk*60*24)/60);
        return $hours;
    }
    
    /**
     * Palauttaa lasketun keston minuuttiosan.
     * @return type
     */
    public function get_keston_min(){
        $kestomin = intval($this->get_kestomin_total());
        $vrk = intval($this->get_keston_vrk());
        $hours = intval($this->get_keston_h());
        $min = $kestomin-$vrk*60*24-$hours*60;
        return $min;
    }
    
    public function get_nimi(){
        return $this->get_arvo(Havaintojakso::$SARAKENIMI_NIMI);
    }
    public function get_kommentti(){
        return $this->get_arvo(Havaintojakso::$SARAKENIMI_KOMMENTTI);
    }
    /**
     * Palauttaa alkuajan unix time stampin eli sekunteina vuodesta 1970.
     * @return type
     */
    public function get_alkutime(){
        return $this->get_arvo(Havaintojakso::$SARAKENIMI_ALKUAIKA_SEK);
    }
    
    /**
     * Palauttaa alkuajan vuoden.
     * @return type
     */
    public function get_alkuvuosi(){
        $datetime = new Aika($this->get_alkutime());
        return $datetime->getYear4digits();
    }
    public function get_alkukk(){
        $datetime = new Aika($this->get_alkutime());
        return $datetime->getMonth();
    }
    public function get_alkupaiva(){
        $datetime = new Aika($this->get_alkutime());
        return $datetime->getDay();
    }
    public function get_alkutunti(){
        $datetime = new Aika($this->get_alkutime());
        return $datetime->getHour();
    }
    public function get_alkumin(){
        $datetime = new Aika($this->get_alkutime());
        return $datetime->getMinutes();
    }
    public function get_henkilo_id(){
        return $this->get_arvo(Havaintojakso::$SARAKENIMI_HENKILO_ID);
    }
    public function get_nakyvyys(){
        return $this->get_arvo(Havaintojakso::$SARAKENIMI_NAKYVYYS);
    }
    
    
    /**
     * Hakee tietokannasta max uusinta havaintojakso-oliota ja palauttaa ne
     * Havaintojakso-luokan olioina taulukossa (array).
     * 
     * Jos $max < 1, haetaan kaikki ilman rajaa!
     * 
     * @param Tietokantaolio $tietokantaolio
     * @param int $max Palauttaa korkeintaan näin monta uusinta!
     */
    public static function hae_uusimmat($tietokantaolio, $max){
        
        $rajoitus = "";
        $max = $max+0;  // Muutos integeriksi varmuuden vuoksi.
        
        if(is_int($max) && $max > 0){
            $rajoitus = " LIMIT ".$max;
        }
        
        $hakulause = "SELECT ".Havaintojakso::$SARAKENIMI_ID.
                    " FROM ". Havaintojakso::$taulunimi.
                    " ORDER BY ". Havaintojakso::$SARAKENIMI_ALKUAIKA_SEK." DESC".
                    $rajoitus;
        
        $tulostaulu = 
                $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
        
        $oliot = array();   // Tämä sisältää Havaintojakso-luokan oliot.
        
        if(!empty($tulostaulu)){
            foreach ($tulostaulu as $tk_olio) {
                
                $id = $tk_olio->id;
                $olio = new Havaintojakso($id, $tietokantaolio);
                if($olio->olio_loytyi_tietokannasta){
                    array_push($oliot, $olio);
                }
            }
        }
        return $oliot;
    }
    
    /**
     * Palauttaa parametrina saatujen Havaintojakso-luokan olioiden id-arvot
     * taulukossa.
     * @param array $jaksot
     */
    public static function hae_jaksojen_idt($jaksot){
        
        $idtaulu = array();   // Tämä sisältää idt.
        
        foreach ($jaksot as $jakso) {
            if($jakso instanceof Havaintojakso){
                array_push($idtaulu, $jakso->get_id());
            }
        }
        return $idtaulu;
    }
    
    /**
     * Palauttaa parametrina saatujen Havaintojakso-luokan olioiden 
     * valikkoon sopivat nimitykset taulukossa.
     * Otetaan mukaan nimi ja aloitusaika ainakin, jotta jaksot eli tapahtumat
     * erottuvat toisistaan. Ajan mukaan on myös helppo järjestää.
     */
    public static function hae_jaksojen_valikkonimet($jaksot){
        
        $nimitaulu = array();   // Tämä sisältää idt.
        
        foreach ($jaksot as $jakso) {
            if($jakso instanceof Havaintojakso){
                
                $time = $jakso->get_arvo(Havaintojakso::$SARAKENIMI_ALKUAIKA_SEK);
                $date = date("m.d.Y", $time);
                
                $nimi = $date." ".$jakso->
                        get_arvo(Havaintojakso::$SARAKENIMI_NIMI);
                
                array_push($nimitaulu, $nimi);
            }
        }
        return $nimitaulu;
    }
    /**
     * Luo Havintojaksolinkki-luokan
     * Hyödyntää Havainto-luokan metodia, jottei samaa tehdä moneen kertaa.
     * Varmistaa ensin, ettei samaa havaintoa jo merkitty tapahtumaan. 
     * 
     * Huomaa, että saman lajin voi toki havaita monta kertaa esimerkiksi
     * matkan aikana eri päivinä.
     * 
     * Palauttaa arvon $OPERAATIO_ONNISTUI, jos linkki lisätään.
     * Muuten palauttaa lisää selittävän kommentin tai virheilmoituksen 
     * (Malliluokkapohja-oliolta). Tämähän ei ole yleensä 
     * virhetoiminto, koska todennäköisesti törmäyksiä tulee aika usein, 
     * vaan rutiinitarkastus.
     * 
     * @param type $id_hav
     * @param bool $tarkista Jos true, tarkistetaan linkin olemassa olo, muuten ei.
     */
    function lisaa_havainto($id_hav, $tarkista){
        $hav = new Havainto($id_hav, $this->tietokantaolio);
        
        if($hav->olio_loytyi_tietokannasta){
            $palaute = $hav->lisaa_havaintojaksoon($this->get_id(), $tarkista);
        } else {
            $palaute = Malliluokkapohja::$VIRHE;
            $this->lisaa_virheilmoitus(Bongaustekstit::$ilm_havaintoa_ei_loytynyt);
        }
        return $palaute;
    }
    
}
