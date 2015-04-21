<?php

/**
 * Henkilo on entiteettiluokka, joka huolehtii yhteydenpidosta tietokannan
 * henkilot-taulun kanssa.
 * MUUTOS 7.5.2014: lisätty sarake "kieli" oletuskieltä varten.
 *
 * create table henkilot
(
  id                    int auto_increment not null,
  etunimi		varchar(50) not null,
  sukunimi		varchar(50) not null,
  lempinimi		varchar(50),
  kommentti		varchar(1000),
 
  kayttajatunnus        varchar(30) unique not null,
  salasana              varchar(50) not null,
  eosoite               varchar(50) not null,
  osoite                varchar(200) default '',
  puhelin               varchar(100) default '',
  
  online                boolean,
  valtuudet             smallint default -1,
  poppoo_id             int not null default -1,
  asuinmaa              smallint default 1,
  kieli                 smallint default 1,         Suomi oletuksena
 
  primary key (id),
  index(kayttajatunnus),
  index(salasana),
  index(online),
  index(poppoo_id)
) ENGINE=InnoDB;
 * 
 * @author Jukka-Pekka Kerkkänen, 30.10.2012 (testivaiheessa)
 */
class Henkilo extends Malliluokkapohja{
    private $salavahvistus; // Paikallinen muuttuja.
    private $tunnusten_muokkaus;  // Muokataanko tunnuksia?
    
    
    public static $sarakenimi_etunimi = "etunimi";
    public static $sarakenimi_sukunimi = "sukunimi";
    public static $sarakenimi_lempinimi = "lempinimi";
    public static $sarakenimi_kommentti = "kommentti";
    public static $sarakenimi_kayttajatunnus = "kayttajatunnus";
    
    public static $sarakenimi_salasana = "salasana";
    public static $sarakenimi_eosoite = "eosoite";
    public static $sarakenimi_osoite = "osoite";
    public static $sarakenimi_puhelin = "puhelin";
    public static $sarakenimi_online = "online";
    
    public static $sarakenimi_valtuudet = "valtuudet";
    public static $sarakenimi_poppoo_id = "poppoo_id";
    public static $sarakenimi_asuinmaa = "asuinmaa";
    public static $sarakenimi_kieli="kieli";
    
    public static $taulunimi = "henkilot";
    
    // Kertoo, että kyseessä on uuden olion tallennus tietokantaan.
    public static $uusi_olio = -529;
    
    function __construct($id, $tietokantaolio) {
        
        $tietokantasolut = 
            array(new Tietokantasolu(Henkilo::$SARAKENIMI_ID, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Henkilo::$sarakenimi_etunimi, Tietokantasolu::$mj_tyhja_EI_ok), 
                new Tietokantasolu(Henkilo::$sarakenimi_sukunimi, Tietokantasolu::$mj_tyhja_EI_ok), 
                new Tietokantasolu(Henkilo::$sarakenimi_lempinimi, Tietokantasolu::$mj_tyhja_ok), 
                new Tietokantasolu(Henkilo::$sarakenimi_kommentti, Tietokantasolu::$mj_tyhja_ok), 
                
                new Tietokantasolu(Henkilo::$sarakenimi_kayttajatunnus, Tietokantasolu::$mj_tyhja_EI_ok), 
                new Tietokantasolu(Henkilo::$sarakenimi_salasana, Tietokantasolu::$mj_tyhja_EI_ok),       
                new Tietokantasolu(Henkilo::$sarakenimi_eosoite, Tietokantasolu::$mj_tyhja_EI_ok), 
                new Tietokantasolu(Henkilo::$sarakenimi_osoite, Tietokantasolu::$mj_tyhja_ok), 
                new Tietokantasolu(Henkilo::$sarakenimi_puhelin, Tietokantasolu::$mj_tyhja_ok), 
                
                new Tietokantasolu(Henkilo::$sarakenimi_online, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Henkilo::$sarakenimi_valtuudet, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Henkilo::$sarakenimi_poppoo_id, Tietokantasolu::$luku_int),
                new Tietokantasolu(Henkilo::$sarakenimi_asuinmaa, Tietokantasolu::$luku_int),
                new Tietokantasolu(Henkilo::$sarakenimi_kieli, Tietokantasolu::$luku_int));
        
        $taulunimi = Henkilo::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
        
        if(!$this->olio_loytyi_tietokannasta){
            $this->salavahvistus = Henkilo::$uusi_olio;
        } else{
            $this->salavahvistus = "";
        }
        
        $this->tunnusten_muokkaus = Tunnukset::$ei_muokata; // oletus.
    }   

    /**
     * Lisää tarkistuksia esimerkiksi liittyen tunnuksiin. Palauttaa samat
     * arvot kuin Malliluokkapohjan metodikin. Virheen sattuessa ilmoitukset
     * virheilmoituksissa.
     * 
     * Huomaa, että salasanan koodaus tehdään vasta tarkistuksen jälkeen, jotta
     * tarkistus on mahdollinen.
     */
    public function tallenna_uusi() {
        
        // Tarkistetaan ennen tallennuksen yrittämistä (salasana selkomuodossa).
        $uusi_olio = true;
        if($this->on_tallennuskelpoinen($uusi_olio)){
            
            // Jos kaikki kunnossa, koodataan salasana ja tallennetaan:
            $koodattu = md5($this->get_arvo(Henkilo::$sarakenimi_salasana));
            $this->set_arvo($koodattu, Henkilo::$sarakenimi_salasana);
            $tallennuspalaute = parent::tallenna_uusi();
        } else{
            $tallennuspalaute = Henkilo::$VIRHE;
            // Virheen sattuessakin koodataan salasana (ks tallenna_muutokset()):
            $koodattu = md5($this->get_arvo(Henkilo::$sarakenimi_salasana));
            $this->set_arvo($koodattu, Henkilo::$sarakenimi_salasana);
        }
        return $tallennuspalaute;
    }
    /**
     * Lisää tarkistuksia esimerkiksi liittyen tunnuksiin. Palauttaa samat
     * arvot kuin Malliluokkapohjan metodikin. Virheen sattuessa ilmoitukset
     * virheilmoituksissa.
     * 
     * Huomaa, että salasana koodaus tehdään vasta tarkistuksen jälkeen, jotta
     * tarkistus on mahdollinen. Tämä tosin aiheutti melkein aika ilkeän
     * virheen, jonka onneksi testiohjelma huomasi...
     */
    public function tallenna_muutokset() {
        
        // Tarkistetaan ennen tallennuksen yrittämistä.
        $uusi_olio = false;
        if($this->on_tallennuskelpoinen($uusi_olio)){
            
            // Jos kaikki kunnossa, koodataan tarvittaessa salasana ja tallennetaan:
            if(($this->tunnusten_muokkaus === Tunnukset::$kumpikin) ||
                $this->tunnusten_muokkaus === Tunnukset::$vain_salis){
                
                $koodattu = md5($this->get_arvo(Henkilo::$sarakenimi_salasana));
                $this->set_arvo($koodattu, Henkilo::$sarakenimi_salasana);
            }
            $tallennuspalaute = parent::tallenna_muutokset();
        } else{
            $tallennuspalaute = Henkilo::$VIRHE;
            
            // Tämä oli ilkeä: virheellinen arvo aiheutti sen, että
            // salasana jäi koodaamattomaan muotoon, ja jos heti perään
            // muokattiin olion jotakin muuta kuin tunnusta, livahti
            // tietokantaan salasana selväkielisenä! Kannatti testata...
            // Pitää siis koodata täällä myös!
            if(($this->tunnusten_muokkaus === Tunnukset::$kumpikin) ||
                $this->tunnusten_muokkaus === Tunnukset::$vain_salis){
                
                $koodattu = md5($this->get_arvo(Henkilo::$sarakenimi_salasana));
                $this->set_arvo($koodattu, Henkilo::$sarakenimi_salasana);
            }
        }
        return $tallennuspalaute;
    }
    
    /**
     * Tarkistaa muun muassa sen, ettei käyttäjätunnus ole jo käytössä.
     * Käyttäjätunnus ja salasana myös tarkistetaan hiukan tarkemmin, kuin
     * mitä tapahtuisi Malliluokkapohjan yleisessä tarkistuksessa. Samoin
     * tehdään muutamia muita tarkistuksia.
     * 
     * Palauttaa totuusarvon ja false-tapauksessa tallentaa virheilmoituksen 
     * oliolle.
     * 
     * Parametrina annetaan salasanaa muutettaessa vahvistussalasana. Jos
     * sillä on arvo false, 
     * 
     * @param type $uusi ilmoittaa, onko uusi olio, vai muokkaus. Totuusarvo
     * true viittaa uuteen olioon.
     * @param type $tunnusten_muokkaus Muokataanko tunnuksia ja miten?
     * @return boolean
     */
    protected function on_tallennuskelpoinen($uusi){
        
        $salavahvistus = $this->get_salavahvistus();
        $tunnusten_muokkaus = $this->get_tunnusten_muokkaus();
        
        $palaute = true;
        $ilmoitus = "";
       
        $etun = $this->get_arvo(Henkilo::$sarakenimi_etunimi);
        $sukun = $this->get_arvo(Henkilo::$sarakenimi_sukunimi);
        $lempin = $this->get_arvo(Henkilo::$sarakenimi_lempinimi);
        $komm = $this->get_arvo(Henkilo::$sarakenimi_kommentti);
        $ktunnus = $this->get_arvo(Henkilo::$sarakenimi_kayttajatunnus);
        $sala = $this->get_arvo(Henkilo::$sarakenimi_salasana);
        $eosoite = $this->get_arvo(Henkilo::$sarakenimi_eosoite);


        // Tarkistetaan tunnukset. Virheistä tulee virheilmoitukset.
        if(!$this->tunnukset_ok($uusi)){
            $palaute = false;
        }
        
        else if(trim($eosoite) == '' || trim($etun) == '' || trim($sukun) == '')
        {
            $ilmoitus = Kayttajatekstit::
                            $henkilolomake_jokin_pakollisista_kentista_tyhja;
            $palaute = false;
        }
        
        $this->lisaa_virheilmoitus($ilmoitus);

        return $palaute;
    }
    
    /**
     * Palauttaa taulukossa Henkilo-luokan olioina kaikki käyttäjät, 
     * joiden valtuusarvo on vähintään $minimivaltuus.
     * @param type $minimivaltuus
     * @param type $taulunimi
     * @param type $tietokantaolio 
     */
    public static function hae_valtuutetut_henkilot($minimivaltuus, 
                                                    $taulunimi, 
                                                    $tietokantaolio){
        
        $henkilot = Henkilo::hae_kaikki_henkilot($taulunimi, $tietokantaolio);
        $valtuutetut = array();
        
        foreach ($henkilot as $ehdokas) {
            if($ehdokas instanceof Henkilo){
                if($ehdokas->get_arvo(Henkilo::$sarakenimi_valtuudet) >=
                    $minimivaltuus){
                    array_push($valtuutetut, $ehdokas);
                }
            }
        }
        return $valtuutetut;
    }
    /**
     * Palauttaa taulukossa Henkilo-luokan olioina kaikki tietokannassa olevat
     * ihmiset. Haku on optimoitu niin, että tietokantaan tehdään vain yksi haku.
     * @param type $taulunimi
     * @param type $tietokantaolio
     * @return array 
     */
    public static function hae_kaikki_henkilot($taulunimi, $tietokantaolio){
        $henkilot = array();
        
        $henkilotaulukot = 
                $tietokantaolio->hae_kaikki_rivit_taulukoina($taulunimi);
        
        foreach ($henkilotaulukot as $henkilotaulukko) {
            $henkilo = new Henkilo(Henkilo::$MUUTTUJAA_EI_MAARITELTY, 
                                    $tietokantaolio);
            $henkilo->nouki_arvot_tk_osumataulukosta($henkilotaulukko);
            if($henkilo->olio_loytyi_tietokannasta){
                array_push($henkilot, $henkilo);
            }
        }
        
        return $henkilot;
    }
    
    
    /**
     * Palauttaa henkilön valtuusarvon tai arvon $EI_LOYTYNYT_TIETOKANNASTA,
     * ellei mitään löydy (ellei henkilöä tietokannassa esimerkiksi).
     * @param type $id
     * @param Tietokantaolio $tietokantaolio 
     */
    public function hae_valtuudet(){
        $palaute = Henkilo::$EI_LOYTYNYT_TIETOKANNASTA;
        
        // Välillä tuli muodon kanssa ongelmia, joten lisäsi (int)-jutun.
        if($this->olio_loytyi_tietokannasta){
            $palaute = (int)$this->get_arvo(Henkilo::$sarakenimi_valtuudet);
        }
        
        return $palaute;
    }
    
    
    
   /**
    * Tekee muutoksen henkilön online-kenttään sekä oliomuuttujaan että
    * tietokantaan. Palauttaa onnistumisen
    * mukaan joko Henkilo::$OPERAATIO_ONNISTUI tai Henkilo::$VIRHE. Viimeksi
    * mainitussa tapauksessa kannattaa tarkistaa tarkemmat kommentit olion
    * virheilmoituksista.
    * 
    * Huom! Onnistuessaan asettaa sessio::omaid-arvoksi henkilon id:n,
    * jos tullaan sisään, tai arvon Henkilo::MUUTTUJAA_EI_MAARITELTY, jos 
    * tullaan ulos.
    * 
    * HUOM! Aktiivisuutta ei päivitetä, joten se pitää tehdä muualla!
    * 
    * Asettaa sessio-tunnistus-muuttujan ookooksi: 
    * $_SESSION[Sessio::$tunnistus] = Sessio::$tunnistus_ok
    * 
    * Jos henkilö on jo kirjautunut, palautetaan
    * onnistumismerkintä.
    * 
    * @param type $sisaan true, jos kirjaudutaan sisään, false muuten.
    */
    function aseta_online($sisaan){
        
        $jo_kirjautunut = false;
        
        if($sisaan){
            $online = 1;
            
            // Jos henkilö on jo kirjautunut:
            if($this->online()){
                $jo_kirjautunut = true;
            }
        }
        else{
            $online = 0;
        }
        
        $palaute = Henkilo::$VIRHE;
        
        $asetus = $this->set_arvo($online, Henkilo::$sarakenimi_online);
        if($asetus == Henkilo::$OPERAATIO_ONNISTUI){
            $tallennus = $this->tallenna_muutokset();
            if($tallennus == Henkilo::$OPERAATIO_ONNISTUI || $jo_kirjautunut){
                $palaute = Henkilo::$OPERAATIO_ONNISTUI;
                
                // Session omaid-arvon muokkaus:
                if($sisaan){
                    $_SESSION[Sessio::$omaid]=$this->get_id();
                    
                    // Sessio-tunnistus on nyt myös kunnossa:
                    $_SESSION[Sessio::$tunnistus] = Sessio::$tunnistus_ok;
                } else{
                    $_SESSION[Sessio::$omaid]= Henkilo::$MUUTTUJAA_EI_MAARITELTY;
                    
                    // Sessio-tunnistus ei päälle:
                    $_SESSION[Sessio::$tunnistus] = Sessio::$tunnistus_ei_ok;
                }
            } else{
                $this->lisaa_virheilmoitus("Metodi aseta_online: muutosten".
                        " tallennus ei onnistunut!");
            }
        } 
        return $palaute;
   }
   
   /**
    * Tallentaa valtuusmuutoksen tietokantaan (ja olioon).
    * Palauttaa onnistumisen
    * mukaan joko Henkilo::$OPERAATIO_ONNISTUI tai Henkilo::$VIRHE. Viimeksi
    * mainitussa tapauksessa kannattaa tarkistaa tarkemmat kommentit olion
    * virheilmoituksista.
    * 
    * @param int $valtuudet
    */
    function tallenna_valtuusmuutos($valtuudet){
        $palaute = Henkilo::$VIRHE;
        $asetus = $this->set_arvo($valtuudet, Henkilo::$sarakenimi_valtuudet);
        if($asetus == Henkilo::$OPERAATIO_ONNISTUI){
            $tallennus = $this->tallenna_muutokset();
            if($tallennus == Henkilo::$OPERAATIO_ONNISTUI){
                $palaute = Henkilo::$OPERAATIO_ONNISTUI;
            }
        } 
        return $palaute;
    }

    /**
    * Kirjaa käyttäjän ulos eli päivittää tietokantaan online-muuttujan ja lisää
    * aktiivisuusmerkinnän ja sitten vielä lopettaa session ja 
    * poistaa tiedot sesssiomuisteista.
    * 
    * Palauttaa onnistumisen
    * mukaan joko Henkilo::$OPERAATIO_ONNISTUI tai Henkilo::$VIRHE. 
    */
    function kirjaudu_ulos(){
        $pal = Henkilo::$VIRHE;
        if($this->online()){
            $this->aseta_online(false);

            // Aktiivisuusmerkintä:
            $aktiivisuuslaji = Aktiivisuus::$ULOSKIRJAUTUMINEN;
            $this->paivita_aktiivisuus($aktiivisuuslaji);

            // Nämä ovat tärkeitä. Tiedot tahtovat jäädä muuten jonnekin muistin
            // perukoille kummittelemaan.
            unset($_SESSION[Sessio::$omaid]); // Vapauttaa muuttujan
            unset($_SESSION[Sessio::$tunnistus]); // Vapauttaa muuttujan
            unset($_SESSION[Sessio::$viim_aktiivisuus]); // Vapauttaa muuttujan
            unset($_SESSION[Sessio::$poppoon_id]); // Vapauttaa muuttujan
            $pal = Henkilo::$OPERAATIO_ONNISTUI;
        }
        return $pal;
    }

   /**
    * Päivittää aktiivisuudeksi parametrina annetun aktiivisuuden, jonka ajaksi
    * tallennetaan nykyhetki. Lisää aina uuden rivin taulukkoon, eli taulukosta
    * tulee ajan mittaan melkoisen iso. Toinen vaihtoehto olisi päivittää samaa 
    * riviä, ellei historialla ole niin väliä.
    * 
    * Palauttaa onnistumisen
    * mukaan joko Henkilo::$OPERAATIO_ONNISTUI tai Henkilo::$VIRHE. 
    * Virhetapauksessa virheilmoitukset löytyvät $henkilo-olion ilmoituksista.
    * 
    * $_SESSION[Sessio::$viim_aktiivisuus]-muuttujalle annetaan arvo time()
    * onnistumistapauksessa.
    *
    * @param <type> $aktiivisuuslaji Viimeisen aktiivisuuden symboliluku.
    */
    function paivita_aktiivisuus($aktiivisuuslaji){
        $palaute = Henkilo::$VIRHE;
        
        // Luodaan uusi aktiivisuusolio:
        $id = Aktiivisuus::$MUUTTUJAA_EI_MAARITELTY;
        $aktiivisuus = new Aktiivisuus($id, $this->tietokantaolio);
        
        // Asetetaan uudet arvot (ei käyttäjältä tulevia -> luotettavia):
        $aktiivisuus->set_arvo_kevyt($aktiivisuuslaji, 
                                    Aktiivisuus::$sarakenimi_aktiivisuuslaji);
        $aktiivisuus->set_arvo_kevyt($this->get_id(), 
                                    Aktiivisuus::$sarakenimi_henkilo_id);
        $aktiivisuus->set_arvo_kevyt(time(), 
                                    Aktiivisuus::$sarakenimi_aika);
        
        // Tallennetaan aktiivisuusmerkintä:
        $tallennus = $aktiivisuus->tallenna_uusi();
        
        // Palautteet ja virheet:
        if($tallennus == Aktiivisuus::$OPERAATIO_ONNISTUI){
            $palaute = Henkilo::$OPERAATIO_ONNISTUI;
            $_SESSION[Sessio::$viim_aktiivisuus] = time();
        } else{
            $this->lisaa_virheilmoitus($aktiivisuus->tulosta_virheilmoitukset());
        }
        
        return $palaute;
    }
   /**
    * Tutkii, onko käyttäjä linjoilla hakemalla olion online-muuttujan arvon. 
    * 
    * Ellei olion tietoja ole haettu tietokannasta, palauttaa arvon false.
    * 
    * Tätä tarvitaan esimerkiksi sen tarkistamiseen, onko 
    * aikakatkaisu poistanut käyttäjän. Palauttaa true, jos käyttäjä on 
    * linjoilla, muuten false.
    */
    function online(){
       $online = false; // käyttäjä ei ole linjoilla.
       if($this->olio_loytyi_tietokannasta){
           $online = $this->get_arvo(Henkilo::$sarakenimi_online);
       }
       return $online;
    }
    
    
    



    /******************** FUNCTION PÄIVITÄ SESSIOTIEDOT ***************************/
    /**
     * Hakee ja päivittää henkilön tiedot, jotka säilytetään sessio-muuttujassa.
     * Tämä on tarpeen silloin, kun tietoihin tehdään muutoksia.
     * @param <type> $omaid
     * @param <type> $tietokantaolio
     *
    function paivita_sessiohenkilotiedot($omaid, $tietokantaolio){

        $taulunimi = "henkilot";
        $sarakenimi = "id";
        $hakuarvo = $omaid;

        $henk_olio = $tietokantaolio->
                            hae_eka_osuma_oliona($taulunimi, $sarakenimi, $hakuarvo);
        if($henk_olio != "tuntematon"){
            $_SESSION['tiedot'] = $henk_olio;
            $_SESSION['tiedot']->salasana = "Hups vaan";    // VArmuuden vuoksi.
        }
    }*/


    /*********************FUNCTION HAE VIIMEINEN ULOSKIRJAUTUMISAIKA ***************/
    /* Hakee käyttäjän viimeisen uloskirjautumisen. Ellei tällaista löydy,
     * palauttaa arvon 0.
     * 
     * Tämän avulla voidaan määritellä
     *
     */
    function hae_vika_ulosaika()
    {
        $aika = 0;
        $hakulause = "SELECT MAX(aika) AS vika FROM ".Aktiivisuus::$taulunimi."
                            WHERE ".Aktiivisuus::$sarakenimi_henkilo_id.
                                    " = ".$this->get_id()."
                            AND ".Aktiivisuus::$sarakenimi_aktiivisuuslaji.
                                    " = ".Aktiivisuus::$ULOSKIRJAUTUMINEN;
        $tulostaulu = $this->tietokantaolio->
                            tee_omahaku_oliotaulukkopalautteella($hakulause);

        if(sizeof($tulostaulu) > 0){
            if(isset($tulostaulu[0]->vika)){
                $aika = $tulostaulu[0]->vika;
            }
        }
        return $aika;
    }

    /**
     * Tutkii, onko henkilöllä kuninkaan eli adminin valtuudet. 
     * Palauttaa true, jos kyseessä on kuningas, muuten false.
     *
     * @param <type> $omaid
     * @param <type> $tietokantaolio
     */
    function on_kuningas(){
        $kuningas = false; // käyttäjä ei ole verkon hallitsija.

        if($this->olio_loytyi_tietokannasta){
            $valtuudet = $this->get_arvo(Henkilo::$sarakenimi_valtuudet);
            
            // Alla "===" ei toiminut oikein ilman nollan lisäämistä. Nollan
            // lisäys saa php:n tulkitsemaan luvun lukuna.
            if(is_numeric($valtuudet) && ($valtuudet > 0) && 
                $valtuudet+0 === Valtuudet::$HALLINTA){
                $kuningas = true;
            }
        }
        
        return $kuningas;
    }
    
    /*********************FUNCTION TARKISTA_KIRJAUTUMINEN *****************************/
    /**
     * Tarkistaa, löytyyko käyttäjätunnusta ja salasanaa vastaavaa riviä
     * tietokannasta. Ennen hakua
     * syötteet puhdistetaan kaikista merkeistä, joilla on erikoismerkitys
     * mySQL:ssä. 
     * 
     * Palauttaa luvun, joka on henkilön id-tunniste, jos tunnukset täsmäämät ja 
     * muuten arvon Henkilo::$EI_LOYTYNYT_TIETOKANNASTA.
     *
     * @param type $ktunnus
     * @param type $salis
     * @param Tietokantaolio $tietokantaolio
     */
    static function tarkista_kirjautuminen($ktunnus, $salis, $tietokantaolio){
        $ktunnuss = mysql_real_escape_string($ktunnus);
        $saliss = mysql_real_escape_string($salis);
        
        $palaute = Henkilo::$EI_LOYTYNYT_TIETOKANNASTA;
        
        // Tietokannassa salasana on aina koodatussa muodossa:
        $saliss_koodattu = md5($saliss);
        
        $taulunimi = Henkilo::$taulunimi;
        $ehtosolu1 = new Tietokantasolu(Henkilo::$sarakenimi_kayttajatunnus, 
                                            Tietokantasolu::$mj_tyhja_EI_ok); 
        $ehtosolu1->set_arvo_kevyt($ktunnuss);
        $ehtosolu2 = new Tietokantasolu(Henkilo::$sarakenimi_salasana, 
                                            Tietokantasolu::$mj_tyhja_EI_ok); 
        $ehtosolu2->set_arvo_kevyt($saliss_koodattu);
        $ehtotietokantasolut = array($ehtosolu1,$ehtosolu2);
                                
        $osumat = $tietokantaolio->hae_tk_oliot($taulunimi, $ehtotietokantasolut);
        
        // osumia pitäisi olla nolla tai yksi. Useampi viittaa ilkeään virheeseen.
        if(sizeof($osumat) === 1){
            $palaute = $osumat[0]->id;
        } 
        
        return $palaute;
    }
    
    /*********************FUNCTION TARKISTA_TUNNUKSET *****************************/
    /* Tarkistaa käyttäjätunnuksen ja salasanan oikeellisuuden. Tätä ennen
     * syötteet puhdistetaan kaikista merkeistä, joilla on erikoismerkitys html:ssä
     * tai mySQL:ssä. 
     * 
     * Tarkistukset: merkit sekä salasanan ja vahvistuksen samuus. Lisäksi
     * tarkistetaan käyttäjätunnuksen olemassaolo (unique!).
     * 
     * Palauttaa totuusarvon funktion nimen mukaisesti eli true, jos
     * tunnukset näyttävät olevan ok.
     */
    function tunnukset_ok($uusi_olio){
        $ok = true;
        $muokkaustapa = $this->get_tunnusten_muokkaus();
        $salavahvistus = $this->get_salavahvistus();
        
        $sala = $this->get_arvo(Henkilo::$sarakenimi_salasana);

        // Mihin tahansa if-lauseeseen joutuminen merkitsee virhettä.
        
        // Käyttäjätunnus jo käytössä? Muokatessa tarkistetaan vain, jos
        // sitä on tarkoitus muuttaa.
        if($uusi_olio ||
            ($muokkaustapa === Tunnukset::$kumpikin)||
            ($muokkaustapa === Tunnukset::$vain_kayttis)){
            
            $taulunimi = Henkilo::$taulunimi;
            $sarakenimi = Henkilo::$sarakenimi_kayttajatunnus;
            $ktunnus = $this->get_arvo(Henkilo::$sarakenimi_kayttajatunnus);

            if(Yleismetodit::arvo_jo_kaytossa($taulunimi, 
                                                $sarakenimi, 
                                                $ktunnus, 
                                                $this->tietokantaolio)){
                $this->lisaa_virheilmoitus(Kayttajatekstit::$tunnus_jo_kaytossa);
                $ok = false;
            } 
            
            // Käyttäjätunnuksen merkit ja pituus:
            if(!Tunnukset::kayttajatunnus_ok($ktunnus)) {
                $this->lisaa_virheilmoitus(
                    Kayttajatekstit::$tunnus_kayttajatunnus_pituus_tai_merkkivirhe.
                    "<br/>".
                    Kayttajatekstit::$tunnus_kayttajatunnuksen_min_pituus_on." ".
                    Tunnukset::$pituus_min_kayttajatunnus.". ".
                    Kayttajatekstit::$tunnus_kayttajatunnuksen_max_pituus_on." ".
                    Tunnukset::$pituus_max_kayttajatunnus.".<br/>".
                    Kayttajatekstit::$tunnus_vain_seuraavat_merkit_sopivat.": ".
                    Tunnukset::$sallitut_merkit);
                $ok = false;

            } 
        }
        
        // Salasana tarkistetaan vain uudelle oliolle tai muokattaessa:
        if($uusi_olio ||
            ($muokkaustapa === Tunnukset::$kumpikin)||
            ($muokkaustapa === Tunnukset::$vain_salis)){
         
            // Salasana ja vahvistus täsmäävät?
            if($salavahvistus !== $sala){
                $this->lisaa_virheilmoitus(
                                    Kayttajatekstit::$tunnus_vahvistus_ei_tasmaa);
                $ok = false;

            } 

            // Salasanan merkit ja pituus:
            if(!Tunnukset::salasana_ok($sala)) {
                $this->lisaa_virheilmoitus(
                    Kayttajatekstit::$tunnus_salasana_pituus_tai_merkkivirhe."<br/>".
                    Kayttajatekstit::$tunnus_salasanan_min_pituus_on." ".
                    Tunnukset::$pituus_min_salasana.". ".
                    Kayttajatekstit::$tunnus_salasanan_max_pituus_on." ".
                    Tunnukset::$pituus_max_salasana.".<br/>".
                    Kayttajatekstit::$tunnus_vain_seuraavat_merkit_sopivat.": ".
                    Tunnukset::$sallitut_merkit);
                $ok = false;
            } 
        }
        return $ok;
    }
    
    // Getterit ja setterit:
    public function get_salavahvistus(){
        return $this->salavahvistus;
    }
    public function set_salavahvistus($vahvistus){
        $this->salavahvistus = $vahvistus;
    }
    public function get_tunnusten_muokkaus(){
        return $this->tunnusten_muokkaus;
    }
    public function set_tunnusten_muokkaus($tapa){
        $this->tunnusten_muokkaus = $tapa;
    }
    
}
?>