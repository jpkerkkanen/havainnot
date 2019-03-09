<?php

/**
 * Kuva-luokka sisältää kuvaan liittyviä metodeita. Kuva-oliota luodessa
 * käytetään seuraavan kuvauksen mukaista kuva-taulua tai sitten luodaan
 * tyhjä olio metodien käyttöä varten.
 *
 * <p>
 * Tämä tiedosto rakentuu sille ajatukselle, että kuvatiedostot tallennetaan
 * erilliseen paikkaan ja tietokannassa on pelkästään kuvien tiedot. Tämä
 * menetelmä tuntuu toimivan paremmin kuin tietokantaan tallennetut kuvat.
 * Syy saattaa toki olla osaamattomuudessa..
 * </p>
 *
 *
 * create table kuvat
(
  id                    int auto_increment not null,
  henkilo_id            int not null,
  kuvaotsikko		varchar(200),
  kuvaselitys		varchar(5000),
  vuosi                 smallint default 0,
 
  kk                    tinyint default 0,
  paiva                 tinyint default 0,
  src                   varchar(300),
  leveys                smallint not null,
  korkeus               smallint not null,
 
  tiedostokoko          int not null,
  tiedostotunnus        varchar(20) not null,
  tiedostonimi          varchar(100) not null,
  tallennusaika_sek     int default 0,
  muutosaika_sek        int default 0,
 
  primary key (id),
  index(henkilo_id),
  index(tallennusaika_sek),
  index(vuosi),
  index(kk),
  FOREIGN KEY (henkilo_id) REFERENCES henkilot (id)
                      ON DELETE CASCADE
)ENGINE=INNODB;
 */

class Kuva extends Malliluokkapohja{
    
    // Tietokannan sarakenimet (id tulee malliluokasta):
    public static $SARAKENIMI_HENKILO_ID = "henkilo_id";
    public static $SARAKENIMI_KUVAOTSIKKO = "kuvaotsikko";
    public static $SARAKENIMI_KUVASELITYS = "kuvaselitys";
    public static $SARAKENIMI_VUOSI = "vuosi";
    public static $SARAKENIMI_KK = "kk";
    
    public static $SARAKENIMI_PAIVA = "paiva";
    public static $SARAKENIMI_SRC = "src";
    public static $SARAKENIMI_LEVEYS = "leveys";
    public static $SARAKENIMI_KORKEUS = "korkeus";
    public static $SARAKENIMI_TIEDOSTOKOKO = "tiedostokoko";
    
    public static $SARAKENIMI_TIEDOSTOTUNNUS = "tiedostotunnus";
    public static $SARAKENIMI_TIEDOSTONIMI = "tiedostonimi";
    public static $SARAKENIMI_TALLENNUSHETKI_SEK = "tallennusaika_sek";
    public static $SARAKENIMI_MUUTOSHETKI_SEK = "muutosaika_sek";
    
    public static $taulunimi = "kuvat";
    
    /* Erikokoiset pikkukuvat tunnistetaan nimeen lisättävän alun perusteella: */
    public static $pikkukuva1_nimen_osa = "mini1_";    // Suurin versio
    public static $pikkukuva2_nimen_osa = "mini2_";
    public static $pikkukuva3_nimen_osa = "mini3_";
    public static $pikkukuva4_nimen_osa = "mini4_";
    public static $pikkukuva5_nimen_osa = "mini5_";
    public static $pikkukuva6_nimen_osa = "mini6_";
    public static $pikkukuva7_nimen_osa = "mini7_";
    public static $pikkukuva8_nimen_osa = "mini8_";      // Pienin versio
    
    public static $KOKOELMA_KUVAT_NORMAALI = 11;

    public static $MAX_FILE_SIZE = 10000000;

    public static $OLETUSMAKSIMILEVEYS = 800;    //Kuvan suurin leveys pikseleinä.
    public static $OLETUSMAKSIMIKORKEUS = 600;

    public static $KUVALAATIKON_OLETUSLEV = 1000; // Kuvalaatikon (<div>) leveys
    public static $IKKUNAN_MAXLEVEYS = 1400;    // Kuvat sis. ikkunan max-koko.

    public static $ESIKATSELUKUVIA_RIVILLA_LKM_OLETUS = 4;
    public static $ESIKATSELUKUVIA_RIVILLA_LKM_MAX = 16;

    /* Näitä käytetään lähinnä virhetilanteissa: */
    public static $MAKSIMIKORKEUS_ESIKATSELU = 150;
    public static $MAKSIMILEVEYS_ESIKATSELU = 200;

    // Tallennettavien pikkukuvien kokoja (tietokanta/tiedosto, px suurin mitta)
    public static $KUVATALLENNUS_PIENI8_MITTA = 50;
    public static $KUVATALLENNUS_PIENI7_MITTA = 100;
    public static $KUVATALLENNUS_PIENI6_MITTA = 200;
    public static $KUVATALLENNUS_PIENI5_MITTA = 300;
    public static $KUVATALLENNUS_PIENI4_MITTA = 400; //kuvakoko 15-30 kt
    public static $KUVATALLENNUS_PIENI3_MITTA = 600; //
    public static $KUVATALLENNUS_PIENI2_MITTA = 800; //
    public static $KUVATALLENNUS_PIENI1_MITTA = 1000; //

    // Jos ladatun kuvan koko on isompi, sitä pienennetään automaattisesti:
    public static $KUVALATAUS_RAJAKOKO = 300000;
    
    // Kuvan leveys (tai pystyssä olevan korkeus)
    public static $KUVATALLENNUS_PIENENNOSMITTA = 1600;
    
    // Viimeiset kuvat haetaan poppoosta välittämättä:
    public static $POPPOOLLA_EI_VALIA = -1;

    /**
     * Luokan muodostin:
     * 
     * @param type $id Mahdollisen tietokantaolio id, tai EI_MAARITELTY.
     * @param Tietokantaolio $tietokantaolio
     
     * @param type $ladattu_kuva Palvelimelle ladattu tiedosto.
     */
    function __construct($id, $tietokantaolio) {
        $tietokantasolut = 
            array(new Tietokantasolu(Kuva::$SARAKENIMI_ID, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_HENKILO_ID, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_KORKEUS, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_LEVEYS, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_KUVAOTSIKKO, Tietokantasolu::$mj_tyhja_ok,$tietokantaolio),
                
                new Tietokantasolu(Kuva::$SARAKENIMI_KUVASELITYS, Tietokantasolu::$mj_tyhja_ok,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_PAIVA, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_KK,Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_VUOSI,Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_SRC,Tietokantasolu::$mj_tyhja_EI_ok,$tietokantaolio),
                
                new Tietokantasolu(Kuva::$SARAKENIMI_TALLENNUSHETKI_SEK, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_TIEDOSTOKOKO, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_TIEDOSTONIMI, Tietokantasolu::$mj_tyhja_EI_ok,$tietokantaolio),
                new Tietokantasolu(Kuva::$SARAKENIMI_TIEDOSTOTUNNUS, Tietokantasolu::$mj_tyhja_EI_ok,$tietokantaolio));
        
        $taulunimi = Kuva::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
    }
    

    //Getterit ja setterit
    
    public function get_kuvaotsikko(){
        return $this->get_arvo(Kuva::$SARAKENIMI_KUVAOTSIKKO);
    }
    
    public function get_kuvaselitys(){
        return $this->get_arvo(Kuva::$SARAKENIMI_KUVASELITYS);
    }
    
    public function get_leveys(){
        return $this->get_arvo(Kuva::$SARAKENIMI_LEVEYS);
    }
    
    public function get_korkeus(){
        return $this->get_arvo(Kuva::$SARAKENIMI_KORKEUS);
    }
    
    /**
     * Palauttaa taulukossa minikuvien koot alkaen suurimmasta. Kokoja on
     * tällä hetkellä kahdeksan.
     */
    public static function get_minikuvakoot(){
        $koot = array(
            Kuva::$KUVATALLENNUS_PIENI1_MITTA,
            Kuva::$KUVATALLENNUS_PIENI2_MITTA,
            Kuva::$KUVATALLENNUS_PIENI3_MITTA,
            Kuva::$KUVATALLENNUS_PIENI4_MITTA,
            Kuva::$KUVATALLENNUS_PIENI5_MITTA,
            Kuva::$KUVATALLENNUS_PIENI6_MITTA,
            Kuva::$KUVATALLENNUS_PIENI7_MITTA,
            Kuva::$KUVATALLENNUS_PIENI8_MITTA
        );
        return $koot;
    }
    
    /**
     * Palauttaa taulukossa minikuvien koot alkaen pienimmästä. Kokoja on
     * tällä hetkellä kahdeksan.
     */
    public static function get_minikuvakoot_alkaen_pienimmasta(){
        
        $koot_alk_isoimmasta = Kuva::get_minikuvakoot();
        
        $koot_alk_pienin = array_reverse($koot_alk_isoimmasta);
        
        return $koot_alk_pienin;
    }
    
    /**
     * Palauttaa taulukossa minikuvien nimen etuliitteet alkaen suurimmasta. 
     * Minikuvia on tällä hetkellä kahdeksan.
     */
    public static function get_minikuvatied_os_miniosat(){
        $miniosat = array(
            Kuva::$pikkukuva1_nimen_osa,
            Kuva::$pikkukuva2_nimen_osa,
            Kuva::$pikkukuva3_nimen_osa,
            Kuva::$pikkukuva4_nimen_osa,
            Kuva::$pikkukuva5_nimen_osa,
            Kuva::$pikkukuva6_nimen_osa,
            Kuva::$pikkukuva7_nimen_osa,
            Kuva::$pikkukuva8_nimen_osa,
        );
        return $miniosat;
    }
    

    /**
     * Tarkistaa tiedostolatauksen tilan ja heittää virheen sattuessa
     * poikkeuksen, joka otetaan kiinni kutsuvassa ohjelmalohkossa.
     * 
     */
    public static function lataustarkistus_kuva($ladattu_kuva){
        
        if(is_array($ladattu_kuva)){
            $virheviesti = $ladattu_kuva['error'];
        } else{
            $virheviesti = $ladattu_kuva;
        }
        
        $viesti = "";

        // Koko rajoitettu oletuksena palvelimella php.inissa kuitenkin!
        $MAX_KOKO_TAVUINA = Kuva::$MAX_FILE_SIZE;

        if(!isset($ladattu_kuva) || $ladattu_kuva === ""){
            throw new Exception(Kuvatekstit::$kuvalomake_virheilm_tied_ei_havaittu);
        } else if ($virheviesti !== UPLOAD_ERR_OK) {

            // Php.inin määräämän koon ylitys:
            if ($virheviesti === UPLOAD_ERR_INI_SIZE) {
                $viesti = Kuvatekstit::$kuvalomake_virheilm_upload_err_ini_size;
            }

            // HTML-lomakkeessa määritellyn koon ylitys:
            else if ($virheviesti === UPLOAD_ERR_FORM_SIZE) {
                $viesti = Kuvatekstit::$kuvalomake_virheilm_upload_err_form_size1.
                    round(Kuvat::$MAX_FILE_KOKO/(1024))." ".
                    Kuvatekstit::$kuvalomake_virheilm_upload_err_form_size2.")!";
            }
            // Lataus keskeytyi (vain osa ladattu):
            else if ($virheviesti === UPLOAD_ERR_PARTIAL) {
                $viesti = Kuvatekstit::$kuvalomake_virheilm_upload_err_partial;
            }
            // Mitään tiedostoa ei ladattu:
            else if ($virheviesti === UPLOAD_ERR_NO_FILE) {
                $viesti = Kuvatekstit::$kuvalomake_virheilm_upload_err_ini_no_file;
            }

            // Tmp-kansio puuttuu palvelimelta:
            else if ($virheviesti === UPLOAD_ERR_NO_TMP_DIR) {
                $viesti = Kuvatekstit::$kuvalomake_virheilm_upload_err_ini_no_tmp_dir;
            }

            // Palvelimelle kirjoitus estetty:
            else if ($virheviesti === UPLOAD_ERR_CANT_WRITE) {
                $viesti = Kuvatekstit::$kuvalomake_virheilm_upload_err_ini_cant_write;
            }

            // ??
            else if ($virheviesti === UPLOAD_ERR_EXTENSION) {
                $viesti = Kuvatekstit::$kuvalomake_virheilm_upload_err_ini_extension;
            }

            else {
                $viesti = Kuvatekstit::$kuvalomake_virheilm_tuntematon;
            }

            throw new Exception($viesti);
        }

        // Jos tulee virhe, palauttaa falsen:
        else if (!getImageSize($ladattu_kuva['tmp_name'])) {
            throw new Exception(Kuvatekstit::$kuvalomake_virheilm_tied_ei_kuva1.
                                $ladattu_kuva['name'].
                                Kuvatekstit::$kuvalomake_virheilm_tied_ei_kuva2
                                );
        }

        // Tarkastaa, onko ladattu HTTP POST:n kautta:
        else if (!is_uploaded_file($ladattu_kuva['tmp_name'])) {
            throw new Exception();
        }

        else if (!(($ladattu_kuva["type"] === "image/gif") ||
                    ($ladattu_kuva["type"] === "image/jpeg") ||
                    ($ladattu_kuva["type"] === "image/pjpeg") || //IE!
                    ($ladattu_kuva["type"] === "image/jpg") ||
                    ($ladattu_kuva["type"] === "image/png"))){

            throw new Exception(Kuvatekstit::$kuvalomake_virheilm_tiedtunniste_vaara);
        }

        // Jos itse asetettu kokorajoitus menee yli (kilotavuiksi jako 1024:lla
        // hassua, mutta näin ilmeisesti ajatellaan!)
        else if($ladattu_kuva["size"] > $MAX_KOKO_TAVUINA){
            throw new Exception(Kuvatekstit::$kuvalomake_virheilm_tied_liian_iso1.
                                " (".round($ladattu_kuva["size"]/1024).
                                " ".Kuvatekstit::$kilotavulyhenne.") ".
                                Kuvatekstit::$kuvalomake_virheilm_tied_liian_iso2.
                                round($MAX_KOKO_TAVUINA/1024)." ".
                                Kuvatekstit::$kilotavulyhenne."!");
        }
    }
    

    /**
     * Tarkistaa kuvan tiedot. Tiedostolatauksen tilaa EI tarkisteta täällä,
     * koska kuvan siirtely ja pienennökset aiheuttavat ongelmia. Se on ehkä
     * parempi tehdä kuvakontrollerissa.
     * @param type $uusi
   
     * @return boolean
     */
    public function on_tallennuskelpoinen($uusi){
        $tarkistuksen_tulos = true;

        $tyhjatunnus = "";
        
        // Tarkistettavat arvot:
        $vuosi_kuva = $this->get_arvo(Kuva::$SARAKENIMI_VUOSI);
        $kk_kuva = $this->get_arvo(Kuva::$SARAKENIMI_KK);
        $paiva_kuva = $this->get_arvo(Kuva::$SARAKENIMI_PAIVA);
        
        // Seuraava tehdään vain uudelle. Muokkauksessa kuvaa ei pysty
        // toistaiseksi vaihtamaan, vaan vain kuvatietoja.
        if($uusi){

           
        }

        // Ellei virheitä havaittu, mennään tarkistuksessa eteenpäin:
        if($tarkistuksen_tulos){

            // Vuosiluku saa olla tyhjä tai muuten pitää olla 4 numeroa.
            if((preg_match('/^\d\d\d\d$/',$vuosi_kuva) == 0) &&
                    ($vuosi_kuva != $tyhjatunnus)){
                $this->lisaa_virheilmoitus(Kuvatekstit::$kuvalomake_virheilm_vuosi);
                $tarkistuksen_tulos = false;
            }
            // Kuukauden tarkistus:
            else if(((preg_match('/^\d\d$/',$kk_kuva) == 0)&&
                    (preg_match('/\d$/',$kk_kuva) == 0) &&
                    ($kk_kuva != $tyhjatunnus)) ||
                    ($kk_kuva < -1)|| ($kk_kuva == 0)||
                    ($kk_kuva > 12)){
                $this->lisaa_virheilmoitus(Kuvatekstit::$kuvalomake_virheilm_kk);
                $tarkistuksen_tulos = false;
            }

            // Päivän tarkistus:
            else if(((preg_match('/^\d\d$/',$paiva_kuva) == 0)&&
                    (preg_match('/\d$/',$paiva_kuva) == 0) &&
                    ($paiva_kuva != $tyhjatunnus)) ||
                    ($paiva_kuva < -1) || ($paiva_kuva == 0) ||
                    ($paiva_kuva > 31)){
                $this->lisaa_virheilmoitus(Kuvatekstit::$kuvalomake_virheilm_paiva);
                $tarkistuksen_tulos = false;
            }

            else{
                $tarkistuksen_tulos = true;
            }
        }

        return $tarkistuksen_tulos;
    }

    /**
     * Ylikirjoittaa Malliluokkapohjan metodin, jotta tarkempi tarkistus onnistuu.
     * Palauttaa Kuva::$OPERAATIO_ONNISTUI tai Kuva::VIRHE, kuten alkuperäinen
     * metodikin. Virheilmoitukset tulevat ilmoituksiin.
     */
    public function tallenna_uusi() {
        if($this->on_tallennuskelpoinen(true)){
            return parent::tallenna_uusi();
        } else{
            return Kuva::$VIRHE;
        }
    }
    
    
    /**
     * Ylikirjoittaa Malliluokkapohjan metodin, jotta tarkempi tarkistus onnistuu.
     * Palauttaa Kuva::$OPERAATIO_ONNISTUI tai Kuva::VIRHE, kuten alkuperäinen
     * metodikin. Virheilmoitukset tulevat ilmoituksiin.
     */
    public function tallenna_muutokset() {
        $uusi = false;
        if($this->on_tallennuskelpoinen($uusi)){
            return parent::tallenna_muutokset();
        } else{
            return Kuva::$VIRHE;
        }
    }
    
    /**
     * Lisää havainnon ja kuvan välillä linkin. Tarkistukset hoidetaan
     * Havaintokuvalinkki-luokassa.
     * 
     * Palauttaa normaalit palautteet VIRHE tai OPERAATIO_ONNISTUI.
     * 
     * Virhetapauksessa mahdolliset virheilmoitukset lisätään kuvaoliolle.
     * 
     * @param type $id_havainto
     */
    public function lisaa_havaintokuvalinkki($id_havainto){
        
        $id = Havaintokuvalinkki::$MUUTTUJAA_EI_MAARITELTY;
        $linkki = new Havaintokuvalinkki($id, $this->tietokantaolio);
        
        // Arvojen asettamiset:
        $linkki->set_arvo($id_havainto, 
                            Havaintokuvalinkki::$sarakenimi_havainto_id);
        
        // Järjestysluku. 
        $jarj_luku = time();    // Laitetaan järjestysluvuksi luomisaika.
        $linkki->set_arvo($jarj_luku, 
                        Havaintokuvalinkki::$sarakenimi_jarjestysluku);
        
        // Kuva_id
        $linkki->set_arvo($this->get_id(), 
                        Havaintokuvalinkki::$sarakenimi_kuva_id); 
        
        // Tallennus. Mahdolliset virheilmoitukset lisätään kuvaoliolle.
        $palaute = $linkki->tallenna_uusi();
        
        if($palaute === Havaintokuvalinkki::$VIRHE){
            $this->lisaa_virheilmoitus($linkki->tulosta_virheilmoitukset());
        }
        
        return $palaute;
    }
    
    /**=========================================================================
     * Lisää lajiluokan ja kuvan välillä linkin. Tarkistukset hoidetaan
     * Lajikuvalinkki-luokassa.
     * 
     * Palauttaa normaalit palautteet VIRHE tai OPERAATIO_ONNISTUI.
     * 
     * Virhetapauksessa mahdolliset virheilmoitukset lisätään kuvaoliolle.
     * 
     * @param type $id_lj
     */
    public function lisaa_lajikuvalinkki($id_lj){
        $id = Lajikuvalinkki::$MUUTTUJAA_EI_MAARITELTY;
        $linkki = new Lajikuvalinkki($id, $this->tietokantaolio);
        
        // Arvojen asettamiset:
        $linkki->set_arvo($id_lj, 
                            Lajikuvalinkki::$sarakenimi_lajiluokka_id);
        
        // Järjestysluku. 
        $jarj_luku = time();    // Laitetaan järjestysluvuksi luomisaika.
        $linkki->set_arvo($jarj_luku, 
                        Lajikuvalinkki::$sarakenimi_jarjestysluku);
        
        // Kuva_id
        $linkki->set_arvo($this->get_id(), 
                        Lajikuvalinkki::$sarakenimi_kuva_id); 
        
        // Tallennus. Mahdolliset virheilmoitukset lisätään kuvaoliolle.
        $palaute = $linkki->tallenna_uusi();
        
        if($palaute === Lajikuvalinkki::$VIRHE){
            $this->lisaa_virheilmoitus($linkki->tulosta_virheilmoitukset());
        }
        
        return $palaute;
    }

    /**
     * Muuttaa parametrina annetun kuvan (gif/jpg/png) koon niin, että sen
     * suurin mitta on parametrina annettu max_mitta. Kuvaa siis tarvittaessa
     * pienennetään tai suurennetaan.
     *
     * Tarvitsee GD-kirjaston, muttei ole riippuvainen Kuva-luokan muuttujista.
     *
     * Tallentaa muokatun kuvan samassa muodossa kuin lähdetiedosto
     * nimellä "$kohdetiedosto_osoite",
     * joka sisältää myös kohdekansion suhteellisen osoitteen
     * (esim. "../temp/pikkukuva.jpg").
     *
     * Virheen tai huonon parametrin sattuessa palauttaa FALSE, muuten TRUE.
     * 
     * @param <type> $ladattu_kuva Alkuperäinen kuva (usein juuri ladattu)
     * @param <type> $max_mitta
     * @param <type> $kohdetiedosto_osoite
     * @param <type> $laatuprosentti
     * @return <bool> onnistuminen: true, muuten false
     */
    public static function muuta_kuvan_koko($ladattu_kuva,
                                            $max_mitta,
                                            $kohdetiedosto_osoite,
                                            $laatuprosentti){
        $alkup_kuva = "";
        $uusi_kuva = "";    // Tämä palautetaan

        $uusi_lev = 0;
        $uusi_kork = 0;

        $palaute = false;

        /* Tarkistetaan vielä, että parametrit ovat ainakin määriteltyjä: */
        if(isset($ladattu_kuva) &&
            isset($max_mitta) &&
            isset($kohdetiedosto_osoite)){

            // Tarkistetaan laatuprosentti. Epäkelvon tullessa annetaan
            // oletusarvo 75:
            if(!isset($laatuprosentti) ||
                !is_numeric($laatuprosentti) ||
                ($laatuprosentti > 100) ||
                $laatuprosentti < 10){
                
                $laatuprosentti = 75;
            }

            $kuvatiedot = getimagesize($ladattu_kuva);
            $kuvan_tyyppi = $kuvatiedot[2];

            // Luodaan muokattava kuva:
            switch($kuvan_tyyppi)
            {
                case "1": $alkup_kuva = imagecreatefromgif($ladattu_kuva); break;
                case "2": $alkup_kuva = imagecreatefromjpeg($ladattu_kuva);break;
                case "3": $alkup_kuva = imagecreatefrompng($ladattu_kuva); break;
                default:  $alkup_kuva = imagecreatefromjpeg($ladattu_kuva);
            }

            // Haetaan alkuperäisen kuvan mitat:
            $alkup_lev = imageSX($alkup_kuva);
            $alkup_kork = imageSY($alkup_kuva);

            // Lasketaan korkeuden ja leveyden suhde:
            $kokosuhde = $alkup_kork/$alkup_lev;

            // Lasketaan uudet mitat: leveät kuvat:
            if($kokosuhde < 1){
                $uusi_lev = $max_mitta;
                $uusi_kork = round($max_mitta*$kokosuhde);
            }
            else{   // Korkeat kuvat:
                $uusi_kork = $max_mitta;
                $uusi_lev = round($max_mitta/$kokosuhde);
            }

            // Luodaan 'pohja' muokattavalle kuvalle:
            $uusi_kuva = imagecreatetruecolor($uusi_lev,$uusi_kork);

            // Sitten muokataan alkuperäistä kuvaa ja kopioidaan tulos uuteen:
            if(imagecopyresampled($uusi_kuva,
                                $alkup_kuva,
                                0, 0,   // uuden kuvan ylävasemmat koordinaatit
                                0, 0,   // alkup. kuvan ylävasemmat koordinaatit
                                $uusi_lev, $uusi_kork,
                                $alkup_lev, $alkup_kork)){

                // Tallennetaan kuva tiedostoon oikean tyyppisenä:
                switch($kuvan_tyyppi)
                {
                    case "1": $tallennus =  // Ilm laatua ei voi laittaa.
                        imagegif($uusi_kuva, $kohdetiedosto_osoite);
                    break;
                    case "2": $tallennus = 
                        imagejpeg($uusi_kuva,
                                    $kohdetiedosto_osoite,
                                    $laatuprosentti);
                    break;
                
                    // HUOM! laatunumeron pitää olla 0-9 nyt!!
                    case "3": $tallennus =
                        imagepng($uusi_kuva,
                                    $kohdetiedosto_osoite,
                                    (int)(floor($laatuprosentti/10)));
                    break;
                    default:  $tallennus =
                        imagejpeg($uusi_kuva,
                                    $kohdetiedosto_osoite,
                                    $laatuprosentti);
                }

                if($tallennus){
                    $palaute = true;
                }
            }
        }
        return $palaute;
    }
    /**
     * Kuvan kiertäminen. TOTEUTUS KESKEN.
     */
    public static function kierra_kuvaa(){
        imagerotate($image, $angle, $bgd_color);
    }
    
    /**
     * Palauttaa taulukossa max_lkm uusimman kuvan id:t, jotka liittyvät 
     * poppooseen, jonka id annettu parametrina. 
     * 
     * Jos poppoo_id-parametri on arvoltaan $POPPOOLLA_EI_VALIA, haetaan
     * kaikkien poppoiden kuvia.
     * 
     * Ellei poppoo_id ole kokonaisluku, ei haeta mitään, vaan palautetaan
     * tyhjä taulukko.
     * 
     * Jos max_lkm ei ole luku tai on negatiivinen, rajoitetaan kuvien määrä
     * oletuksena kymmeneen.
     * 
     * @param type $poppoo_id
     * @param Tietokantaolio----------------------------------------------------------------------- $tietokantaolio
     */
    public static function hae_uusien_kuvien_idt($poppoo_id, $max_lkm, $tietokantaolio){
        
        $palautus = array();
        
        // Tarkistetaan ensin max_lkm:
        if(!is_int($max_lkm) || ($max_lkm < 0)){
            $max_lkm = 10;
        }
        
        // Muotoillaan sitten poppoo_id-ehto:
        if(is_int($poppoo_id)){
            if($poppoo_id === Kuva::$POPPOOLLA_EI_VALIA){
                $poppooehto = "";
            } else{
                $poppooehto = " JOIN ".Henkilo::$taulunimi.
                                " ON ".Henkilo::$SARAKENIMI_ID."=".
                                    Kuva::$SARAKENIMI_HENKILO_ID.
                                " JOIN ".Poppoo::$taulunimi.
                                " ON ".Poppoo::$SARAKENIMI_ID."=".
                                    Henkilo::$sarakenimi_poppoo_id.
                                "WHERE ".Poppoo::$SARAKENIMI_ID."=".
                                    $poppoo_id;
            }
            
            $hakulause = "SELECT id FROM ".Kuva::$taulunimi.
                            $poppooehto.
                            " ORDER BY ".Kuva::$SARAKENIMI_TALLENNUSHETKI_SEK.
                            " DESC ".
                            " LIMIT ".$max_lkm;
        }
        
        return $palautus;
    }
}

?>