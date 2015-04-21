<?php

/**
 * Description of Lajiluokka:
 * Käärii sisäänsä tietokannan lajiluokat-taulun yhden rivin ja huolehtii
 * vuorovaikutuksesta tietokannan kanssa (uuden tallennus, muokkaus ja poisto).
 * 
 * Huom! Tuplalajeja ei saisi tulla. Ehkä paras keino tämän välttämiseen on
 * käyttää latinalaista nimeä, koska se on yleismaailmallinen ja lisäksi kuuluu 
 * samaan tietokantatauluun.
 * Hätätapauksessa sen paikalle voinee tallentaa minkä tahansa merkkijonon,
 * ellei oikeaa saa selville ja korjata se myöhemmin.
 * 
 * 27.5.2012 toteutus on sellainen, että ei_tyhjä latina vaaditaan uusissa
 * ja latinan muutoksissa ja latinan yksilöllisyys tarkistetaan. Kahta samaa
 * lajia (latinaksi) ei enää pysty tallentamaan.

* Lajiluokat eli sekä lajit että erilaiset luokat:*
  create table lajiluokat
(
  id                    int auto_increment not null,
  ylaluokka_id          int default -1 not null,
  nimi_latina           varchar(128) not null,
  primary key (id),
  index(ylaluokka_id),
  unique index(nimi_latina),
) ENGINE=INNODB;
 *
 * @author J-P
 */
class Lajiluokka extends Malliluokkapohja {

    private $jnro;  // Auttaa järjestysluvun näytössä. Ei tallenneta tietokantaan!
    
    public static $SARAKENIMI_YLALUOKKA_ID= "ylaluokka_id";
    public static $SARAKENIMI_NIMI_LATINA= "nimi_latina";

    public static $taulunimi = "lajiluokat";
    /**
     * @param Tietokantaolio $tietokantaolio
     * @param int $id olion id tietokannassa
     * 
     * HUOM! Alla parametrit vanhalla tavalla. Nykyisin suositus on
     * laittaa id ensin ja sitten tietokantaolio.
     */
    function __construct($tietokantaolio, $id){
        
        $this->jnro = 1;
        $tietokantasolut = 
            array(new Tietokantasolu(Lajiluokka::$SARAKENIMI_ID, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Lajiluokka::$SARAKENIMI_YLALUOKKA_ID, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Lajiluokka::$SARAKENIMI_NIMI_LATINA, Tietokantasolu::$mj_tyhja_EI_ok)
               );
        
        $taulunimi = Lajiluokka::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
    }
    
    // Getterit ja setterit:
    public function get_jnro(){
        return $this->jnro;
    }
    public function set_jnro($uusi){
        if(isset($uusi)){
            $this->jnro = $uusi;
        }
    }
    
    
    public function get_ylaluokka_id(){
        return $this->get_arvo(Lajiluokka::$SARAKENIMI_YLALUOKKA_ID);
    }
    
    /**
     * Asettaa ylaluokka_id:n arvon ja palauttaa onnistuessa arvon TRUE ja 
     * muussa tapauksessa FALSE. 
     * 
     * Huom! Lisäsin 30.5.2012 ominaisuuden, ettei
     * asetusta voi tehdä muuten, kuin että entinen arvo on
     * Lajiluokka::$MUUTTUJAA_EI_MAARITELTY. Tämä auttaa sen valvomisessa, ettei
     * ylaluokka_id:n arvoa muuteta, kun se on kerran annettu. Se kun helposti
     * aiheuttaisi hankalia ongelmia ja toisaalta siihen ei pitäisi olla 
     * tarvetta. Voihan tuota löysentää tarvittaessa.
     * 
     * Ellei muutos onnistu, lisätään virheilmoituksiin viesti asiasta.
     * 
     * @param type $uusi 
     * @return bool $onnistui
     */
    public function set_ylaluokka_id($uusi){
        $onnistui = false;
        if($this->get_ylaluokka_id() === Lajiluokka::$MUUTTUJAA_EI_MAARITELTY){
            $pal = parent::set_arvo($uusi, Lajiluokka::$SARAKENIMI_YLALUOKKA_ID);
            if($pal === Lajiluokka::$OPERAATIO_ONNISTUI){
                $onnistui = true;
            } else{
                $this->lisaa_virheilmoitus("Virhe ylaluokka_id-muuttujan arvon".
                        " muuttamisessa!");
            }
        }
        return $onnistui;
    }
    public function get_nimi_latina(){
        return $this->get_arvo(Lajiluokka::$SARAKENIMI_NIMI_LATINA);
    }
    
    public function get_nimi_latina_html_encoded(){
        return $this->get_html_encoded_arvo(Lajiluokka::$SARAKENIMI_NIMI_LATINA);
    }
    /**
     * Pakotetaan ylaluokka_id:n ja nimi_latina:n asetukset menemään erillisten
     * metodeitten kautta yleistä setter-metodia käytettäessä.
     * @param type $uusi
     * @param type $sarakenimi
     */
    public function set_arvo($uusi, $sarakenimi) {
        $palaute = Lajiluokka::$VIRHE;
        
        // Yläluokka_id ja latina_nimi pakotetaan eri kautta.
        if($sarakenimi === Lajiluokka::$SARAKENIMI_YLALUOKKA_ID){
            if($this->set_nimi_latina($uusi)){
                $palaute = Lajiluokka::$OPERAATIO_ONNISTUI;
            }
        } else if($sarakenimi === Lajiluokka::$SARAKENIMI_NIMI_LATINA){
            if($this->set_nimi_latina($uusi)){
                $palaute = Lajiluokka::$OPERAATIO_ONNISTUI;
            }
        } else{ // Muuten setterit normaalisti.
            $palaute = parent::set_arvo($uusi, $sarakenimi);
        }
        return $palaute;
    }
    
    /**
     * Asettaa nimi_latina-muuttujan arvon ja palauttaa onnistuessa arvon TRUE ja 
     * muussa tapauksessa FALSE. Viimeksi mainitussa tapauksessa lisätään
     * virheilmoitus ilmoituksiin.
     * 
     * Huom! Metodi
     * $this->set_arvo($uusi, Lajiluokka::$SARAKENIMI_NIMI_LATINA) kutsuu
     * automaattisesti tätä metodia, joten sen toiminta on identtinen
     * palautusarvoa lukuunottamatta.
     * 
     * @param type $uusi
     */
    public function set_nimi_latina($uusi){
        // Tarkistetaan (käyttäjän syöte)
        $uusi = mysql_real_escape_string(stripslashes(trim($uusi)));
        
        if($this->get_ylaluokka_id() != -1){
                $uusi = Yleismetodit::eka_kirjain_pieneksi($uusi);
        }
        else{
            $uusi = Yleismetodit::eka_kirjain_isoksi($uusi);
        }
        
        $pal = parent::set_arvo($uusi, Lajiluokka::$SARAKENIMI_NIMI_LATINA);
            if($pal === Lajiluokka::$OPERAATIO_ONNISTUI){
                $onnistui = true;
            } else{
                $this->lisaa_virheilmoitus("Virhe nimi_latina-muuttujan arvon".
                        " muuttamisessa!");
            }
    }
    
    /**
     * Palauttaa totuusarvolla tiedon siitä, onko olio valmis tallennukseen, eli
     * onko muuttujat asianmukaisia. Parametrilla annetaan funktiolle tieto siitä,
     * onko kysymyksessä uuden tallennus vai vanhan muokkaus.
     *
     * Vanhan muokkauksessa täällä tarkistetaan, onko
     * tietoja muutettu. Ellei tietoja ole muutettu, ei tietokantarivi muuttuisi,
     * minkä syy voi olla hämärä (myös virheellinen syöte tms). Siksi
     * tallentamaan ei päästetä, ellei jotakin muutosta tullut.
     * 
     * Myös se tarkistetaan, ettei lajia ole jo olemassa (latinaksi). Jos on,
     * ei päästetä tallentamaan.
     *
     * Huom! Palauttaa aina totuusarvon, mutta virhetapauksissa lisää
     * oliolle virheilmoituksen, joka kuvaa virheen tarkemmin.
     *
     * Itse asiassa yläluokka_id:n muuttaminen kannattaa estää. Teen sen
     * setteritasolla.
     * 
     * @param <type> $uusi
     * @return boolean 
     */
    function on_tallennuskelpoinen($uusi){
        $tila = false;
        $latin = $this->get_nimi_latina();
        $ylaluokka_id = $this->get_ylaluokka_id();
        
        // Täällä voi olla tuplatarkastuksia, mutta en lähde muuttamaan.
        if($uusi){
            $putsaa = true;
            $tyhja_ok = true;
            
            
           if($this->lukumuotoinen_muuttuja_ok($ylaluokka_id, true,
                                    Bongaustekstit::$lajiluokkalomake_ylaluokka)&&
            $this->mjmuotoinen_muuttuja_ok($latin, $putsaa, $tyhja_ok,
                                    Bongaustekstit::$lajiluokkalomake_nimi_latina)){
               
                // Tarkistetaan, ettei nimi_latina ole tyhjä:
                if(!empty($latin)){
                    
                    // Tarkistetaan lajin yksinäisyys:
                    if(!$this->laji_on_jo_olemassa()){
                        
                        // Muuttujat valmiina tallennukseen:
                        $tila = true;
                    }
                    else{
                        $ilmoitus = 
                        Bongaustekstit::$lajiluokka_virheilmoitus_on_jo_olemassa_latina;
                        $this->lisaa_virheilmoitus($ilmoitus);
                    }      
                }
                else{
                    $ilmoitus = 
                    Bongaustekstit::$lajiluokka_virheilmoitus_latina_tyhja;
                    $this->lisaa_virheilmoitus($ilmoitus);
                }
            }
            else{
                $this->lisaa_virheilmoitus(
                Bongaustekstit::$lajiluokka_virheilmoitus_tiedoissa_virheita.
                        "; ylaluokka_id=".$this->get_ylaluokka_id()." ja ".
                        "nimi_latina=".$this->get_nimi_latina());
            }
        }

        // Vanhan muokkaus. Se, onko mitään muutettu, tarkistetaan
        // Malliluokkapohjassa.
        else{
            
            $tila = true;   // Helpompi näin tässä.
            $putsaa = true;
            $tyhja_ok = false; // Ei saa olla tyhjä.

            // Latinan tarkistus.
            if(!$this->mjmuotoinen_muuttuja_ok($latin, 
                                $putsaa, $tyhja_ok,
                                Bongaustekstit::$lajiluokkalomake_nimi_latina)){
                $tila = false;
                $this->lisaa_virheilmoitus(
                    Bongaustekstit::$lajiluokka_virheilmoitus_viallinen_nimi_latina);
            } else{ // kun nimi on ok itsessään:

                // Tarkistetaan lajin yksinäisyys:
                $laji_on_jo = $this->laji_on_jo_olemassa();
                if($laji_on_jo){
                    $ilmoitus = 
                    Bongaustekstit::$lajiluokka_virheilmoitus_on_jo_olemassa_latina;
                    $this->lisaa_virheilmoitus($ilmoitus);
                    $tila = false;
                }      
            }
            
            // Yläluokka_id:n tarkistus:
            if(!$this->lukumuotoinen_muuttuja_ok($ylaluokka_id, true,
                                Bongaustekstit::$lajiluokkalomake_ylaluokka)){
                $tila = false;
                $this->lisaa_virheilmoitus(
                    Bongaustekstit::$lajiluokka_virheilmoitus_viallinen_ylaluokka_id);
            }
        }
        return $tila;
    }
    
    /**
     * Tallentaa tiedot tietokantaan. 
     * HUOM! Tietojen tarkistus tehdään kutsumalla on_tallennuskelpoinen-metodia. 
     *
     * Palauttaa arvon Lajiluokka::$OPERAATIO_ONNISTUI,
     * jos tallennus onnistuu, muuten virheilmoituksen.
     * 
     * <p>Oikea id tallennetaan olioon.</p>
     */
    public function tallenna_uusi(){
        $taulu = $this->tk_taulunimi;
      
        if(!$this->tietokantaolio instanceof Tietokantaolio){
            $palaute = Bongaustekstit::$virheilmoitus_tietokantaolio_ei_maaritelty;
        }
        else{
            // Tietojen tarkistus:
            $uusi = true;
            if($this->on_tallennuskelpoinen($uusi)){
                $palaute = parent::tallenna_uusi();
            }
            else{
                $palaute = $this->tulosta_virheilmoitukset();
            }
        }

        return $palaute;
    }

    /**
     * Tallentaa muuttuneet tiedot tietokantaan.
     * HUOM! 
     *
     * Muutoksia voi tehdä vain kommenttitekstiin. Muokkaushetki tosin
     * päivitetään samalla nykyhetkeen.
     *
     * Palauttaa arvon Lajiluokka::$OPERAATIO_ONNISTUI,
     * jos tallennus onnistuu, muuten virheilmoituksen.
     */
    public function tallenna_muutokset(){
        $taulu = $this->tk_taulunimi;

        if(!$this->tietokantaolio instanceof Tietokantaolio){
            $palaute = Bongaustekstit::$virheilmoitus_tietokantaolio_ei_maaritelty;
        }
        else{
            // Tietojen tarkistus:
            $uusi = false;
            if($this->on_tallennuskelpoinen($uusi)){
                $palaute = parent::tallenna_muutokset();
            }
            else{
                $palaute = $this->tulosta_virheilmoitukset();
            }
        }
        return $palaute;
    }

    /**
     * Poistaa id-arvoa vastaavan kuvauksen tietokannasta. Palauttaa joko
     * arvon Lajiluokka::$OPERAATIO_ONNISTUI tai Lajiluokka::$VIRHE. Virhe-
     * tapauksissa lisätään virheilmoitus oliolle.
     * 
     * Lajiluokkaan viittaavat kuvaukset poistuvat automaattisesti
     * tietokantatason cascade-toiminnan avulla.
     * 
     * Ennen poistoa tarkistetaan ettei lajilla ole aliluokkia, lajiin kohdistu 
     * havaintoja eikä kuvia. Jos yksikin näistä havaitaan, ei poistoa sallita.
     *
     * @return 
     */
    public function poista(){ 
        $palaute = Lajiluokka::$VIRHE;
        if((!$this->tietokantaolio instanceof Tietokantaolio) ||
            ($this->get_id() == Lajiluokka::$MUUTTUJAA_EI_MAARITELTY)){
            
            $this->lisaa_virheilmoitus(Bongaustekstit::
                    $virheilmoitus_tietokantaolio_tai_id_ei_maaritelty);
        }
        else{
            
            // Tarkistetaan, ettei lajiluokalla ole aliluokkia. Tällöin näet
            // ei poisto ole luvallinen.
            // TArkistetaan vielä tietokanta:
            $hakuarvo = $this->get_id();
            $lkm = $this->tietokantaolio->hae_osumien_lkm(Lajiluokka::$taulunimi, 
                                            Lajiluokka::$SARAKENIMI_YLALUOKKA_ID, 
                                            $hakuarvo);
            if($lkm == 0){
                
                // TArkistetaan, ettei lajiluokalla ole havaintoja, jolloin
                // poisto ei myöskään ole luvallinen:
                if($this->lajiin_kohdistuu_havaintoja()){
                    $this->lisaa_virheilmoitus(Bongaustekstit::
                            $lajiluokka_virheilm_poisto_eiok_havaintoja_loytyi);
                }
                // TArkistetaan, ettei lajiluokalla ole kuvia, jolloin
                // poisto ei myöskään ole luvallinen:
                else if($this->lajiin_kohdistuu_kuvia()){
                    $this->lisaa_virheilmoitus(Bongaustekstit::
                            $lajiluokka_virheilm_poisto_eiok_kuvia_loytyi);
                }
                else{
                    $palaute = parent::poista();

                    if($palaute === Lajiluokka::$VIRHE){
                        $this->lisaa_virheilmoitus(ongaustekstit::
                                        $lajiluokka_virheilmoitus_poisto_eiok);
                    }
                }
            }
            else{  
                $this->lisaa_virheilmoitus(Bongaustekstit::
                            $lajiluokka_virheilmoitus_poisto_eiok_aliluokkia);
            }
        }
        return $palaute;
    }
    /**
     * Tarkistaa tietokannasta, onko laji (nimi_latina) jo olemassa. Jos on, 
     * palauttaa arvon TRUE, muuten FALSE. 
     */
    public function laji_on_jo_olemassa(){
        $taulunimi = $this->tk_taulunimi;   // blajiluokat
        $sarakenimi = Lajiluokka::$SARAKENIMI_NIMI_LATINA;
        $hakuarvo = $this->get_nimi_latina();
        $tk_lajiluokkaolio =
                $this->tietokantaolio->hae_eka_osuma_oliona($taulunimi,
                                                        $sarakenimi,
                                                            $hakuarvo);

        if($tk_lajiluokkaolio !== Tietokantaolio::$HAKU_PALAUTTI_TYHJAN){
            return true;    // Laji löytyi latinaksi
        }
        else{
            return false;
        }
    }
    /**
     * Hakee tähän lajiluokkaan liittyvät kuvaukset ja palauttaa ne taulukossa
     * Kuvaus-luokan olioina kieli_id:n mukaan järjestettynä.
     * Ellei mitään löydy, palauttaa tyhjän taulukon. 
     */
    public function hae_kuvaukset(){
        $palautustaulukko = array();
        
        $taulunimi = Kuvaus::$taulunimi;
        $sarakenimi = Kuvaus::$SARAKENIMI_LAJILUOKKA_ID;
        $hakuarvo = $this->get_id();
        
        // Haetaan vain id:t, koska muut haetaan automaattisesti myöhemmin:
        $hakulause = "SELECT ".Kuvaus::$SARAKENIMI_ID.
                        " FROM ".$taulunimi.
                        " WHERE ".$sarakenimi."=".$hakuarvo.
                        " ORDER BY ".Kuvaus::$SARAKENIMI_KIELI;
        
        $tk_oliot = $this->tietokantaolio->
                        tee_OMAhaku_oliotaulukkopalautteella($hakulause);

        if(!empty($tk_oliot)){
            foreach ($tk_oliot as $tk_kuvaus) {
                //$id = $tk_kuvaus."->".$sarakenimi; // Voisko toimia?
                $id = $tk_kuvaus->id;
                $kuvaus = new Kuvaus($this->tietokantaolio, $id);
                array_push($palautustaulukko, $kuvaus);
            }
        }
        
        return $palautustaulukko;
    }
    
    
    
    /**
     * Hakee tähän lajiluokkaan liittyvää kuvausta annetulla kielellä ja 
     * palauttaa Kuvaus-luokan olion tai arvon 
     * Lajiluokka::MUUTTUJAA_EI_MAARITELTY, ellei kuvausta löydy. 
     * 
     * Muutos 7.5.2014: Ellei eka yrittämällä löydy, yrittää etsiä kuvausta
     * suomen kielellä. Ellei sitäkään löydy, palauttaa arvon 
     * Lajiluokka::MUUTTUJAA_EI_MAARITELTY
     */
    public function hae_kuvaus($kieli_id){
        $palautus = Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;
        
        $taulunimi = Kuvaus::$taulunimi;
        $sarakenimi1 = Kuvaus::$SARAKENIMI_LAJILUOKKA_ID;
        $hakuarvo1 = $this->get_id();
        $sarakenimi2 = Kuvaus::$SARAKENIMI_KIELI;
        $hakuarvo2 = $kieli_id;
        
        // Haetaan vain id, koska muut tiedot haetaan automaattisesti myöhemmin:
        $hakulause = "SELECT ".Kuvaus::$SARAKENIMI_ID.
                        " FROM ".$taulunimi.
                        " WHERE ".$sarakenimi1."=".$hakuarvo1.
                        " AND ".$sarakenimi2."=".$hakuarvo2;
        
        $tk_oliot = $this->tietokantaolio->
                        tee_OMAhaku_oliotaulukkopalautteella($hakulause);

        if(!empty($tk_oliot)){
            $id = $tk_oliot[0]->id;
            $palautus = new Kuvaus($this->tietokantaolio, $id);
            
        } else{
            
            // Etsitään suomenkielistä kuvausta (joka nyt pitäisi aina olla).
            $hakuarvo2 = Kielet::$SUOMI;
            
            // Haetaan vain id, koska muut tiedot haetaan automaattisesti myöhemmin:
            $hakulause = "SELECT ".Kuvaus::$SARAKENIMI_ID.
                        " FROM ".$taulunimi.
                        " WHERE ".$sarakenimi1."=".$hakuarvo1.
                        " AND ".$sarakenimi2."=".$hakuarvo2;
        
            $tk_oliot = $this->tietokantaolio->
                        tee_OMAhaku_oliotaulukkopalautteella($hakulause);
            if(!empty($tk_oliot)){
                $id = $tk_oliot[0]->id;
                $palautus = new Kuvaus($this->tietokantaolio, $id);
            }
        }
        
        return $palautus;
    }
    
    /**
     * Tämä tarkistaa, onko lajiluokalle jo nimi tietyllä kielellä ja 
     * palauttaa FALSE, ELLEI sellaista löydy ja muute TRUE. 
     */
    public function lajinimilatinax_on_jo_olemassa(){
        $palaute = true;
        
        // HUOM! Hakulauseessa avainsanojen ympärille tarvitaan välit!
        $hakulause = "SELECT id FROM ".$this->tk_taulunimi.
                     " WHERE ".Lajiluokka::$SARAKENIMI_NIMI_LATINA."='".
                            $this->get_nimi_latina()."'";
        
        $osumat = 
            $this->tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);
        
        if(empty($osumat)){
            $palaute = false;
        }
        
        return $palaute;
    }
    
    /**
     * Tämä tarkistaa, onko lajiluokalle olemassa havaintoja ja  
     * palauttaa FALSE, ELLEI sellaisia löydy ja muute TRUE. 
     */
    public function lajiin_kohdistuu_havaintoja(){
        $palaute = true;
        
        // HUOM! Hakulauseessa avainsanojen ympärille tarvitaan välit!
        $hakulause = "SELECT id FROM ".Havainto::$taulunimi.
                     " WHERE lajiluokka_id =".$this->get_id();
        
        $osumat = 
            $this->tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);
        
        if(empty($osumat)){
            $palaute = false;
        }
        
        return $palaute;
    }
    
    /**
     * Tämä tarkistaa, onko lajiluokalle olemassa kuvia ja  
     * palauttaa FALSE, ELLEI sellaisia löydy ja muute TRUE. 
     */
    public function lajiin_kohdistuu_kuvia(){
        $palaute = true;
        
        // HUOM! Hakulauseessa avainsanojen ympärille tarvitaan välit!
        $hakulause = "SELECT id FROM ".Lajikuvalinkki::$taulunimi.
                     " WHERE lajiluokka_id =".$this->get_id();
        
        $osumat = 
            $this->tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);
        
        if(empty($osumat)){
            $palaute = false;
        }
        
        return $palaute;
    }
    
    /**
     * Tämä tarkistaa, onko lajiluokalle olemassa aliluokkia ja  
     * palauttaa FALSE, ELLEI sellaisia löydy ja muute TRUE. 
     */
    public function lajilla_on_aliluokkia(){
        $palaute = true;
        
        // HUOM! Hakulauseessa avainsanojen ympärille tarvitaan välit!
        $hakulause = "SELECT id FROM ".Lajiluokka::$taulunimi.
                     " WHERE ".Lajiluokka::$SARAKENIMI_YLALUOKKA_ID."=".
                    $this->get_id();
        
        $osumat = 
            $this->tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);
        
        if(empty($osumat)){
            $palaute = false;
        }
        
        return $palaute;
    }
    /**
     * Tämä siirtää kaikki tähän lajiin kohdistuneet havainnot parametrina
     * annettavaan lajiin.
     * 
     * HUOM! Virheen sattuessa palauttaa arvon Tietokantaolio::HAKUVIRHE
     *
     * @param int $kohdelajiluokan_id kohteena olevan lajiluokan tunniste.
     * @return int Palauttaa luvun, joka ilmaisee siirrettyjen havaintojen lkm:n.
     */
    public function siirra_havainnot_toiseen_lajiin($kohdelajiluokan_id){
 
        // Luodaan uusi Havainto-olio, jotta päästään sen metodeihin:
        $havainto = new Havainto(Havainto::$MUUTTUJAA_EI_MAARITELTY, 
                                $this->tietokantaolio);
        
        /*EI NÄIN: $tietokantasoluehto = 
                $havainto->get_tietokantasolu(Havainto::$SARAKENIMI_LAJILUOKKA_ID);*/
        // Vaan näin!
        $tietokantasoluehto = 
                new Tietokantasolu(Havainto::$SARAKENIMI_LAJILUOKKA_ID, true, 
                                    Tietokantasolu::$TYHJA_EI_OK);
        $tietokantasoluehto->set_arvo($this->get_id());
        
        // Muutettavat tiedot: HUOM! Tässä oli kavala virhe: muuttaa myös
        // tietokantasoluehdon arvon, koska se viittaa samaan olioon! Täten
        // tietokantasoluehto pitää määritellä kokonaan uutena soluna!
        $havainto->set_lajiluokka_id($kohdelajiluokan_id);
        
        $tietokantarivi = $havainto->get_tietokantarivi();
        $max_muutosrivilkm = Tietokantaolio::$EI_RAJOITETTU;
        
        $palaute = $this->tietokantaolio->update_rivi($tietokantarivi, 
                                                $tietokantasoluehto, 
                                                $max_muutosrivilkm);
        
        if($palaute !== Tietokantaolio::$HAKU_ONNISTUI){
            $this->lisaa_virheilmoitus(Bongaustekstit::
                    $lajiluokan_havaintosiirtovirhe);
        } else{
            // Muokattujen lkm edellisessä MySQL-kutsussa. Huomaa, ettei mukana
            // ole sellaiset tietokantarivit, joiden tiedoissa ei havaittu muutoksia.
            // Vain todelliset muutokset lasketaan. Palauttaa -1, jos tapahtui jokin
            // virhe.
            $palaute = mysql_affected_rows();
        }
        
        return $palaute;
    }
    
    /**
     * KESKEN! Tämä siirtää kaikki tähän lajiin kohdistuneet kuvat parametrina
     * annettavaan lajiin.
     * 
     * HUOM! Virheen sattuessa palauttaa arvon Tietokantaolio::HAKUVIRHE
     *
     * @param int $kohdelajiluokan_id kohteena olevan lajiluokan tunniste.
     * @return Palauttaa luvun, joka ilmaisee siirrettyjen kuvien lkm:n.
     */
    public function siirra_kuvat_toiseen_lajiin($kohdelajiluokan_id){
        
        return 0;
    }
    
    /**
     * Palauttaa taulukossa kaikki tallennetut lajiluokat Lajiluokka-luokan
     * olioina, jotka kuuluvat parametrina annettavan yläluokan alle.
     * 
     * Oliot ovat kielen mukaisessa aakkosjärjestyksessä. Kuvausta ei palauteta,
     * mutta nimen voi myöhemmin helposti hakea lajiluokka-olion metodilla.
     * 
     * HUOM! Jos kielellä ei ole kuvausta, rajoittaa se tässä. Pitäisi ottaa
     * JOIN LEFT vai mikä se olikaan, mutta kunhan katsotaan. Suomeksi toiminee.
     * 
     * @param type $kieli_id
     * @param type $ylaluokka_id
     * @param Tietokantaolio $tietokantaolio
     * @return array
     */
    public static function hae_kaikki_lajiluokat($kieli_id, $ylaluokka_id, $tietokantaolio){
        $ehtolause = "WHERE (".Kuvaus::$taulunimi.".kieli= $kieli_id
                    AND ".Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id)";
        
        
        // Luultavasti tässä turha, koska tuplia ei pitäisi olla..
        $hakulause = "SELECT ".Lajiluokka::$taulunimi.".id AS lj_id, ".Kuvaus::$taulunimi.".nimi AS nimi
                    FROM ".Lajiluokka::$taulunimi."
                    JOIN ".Kuvaus::$taulunimi."
                    ON ".Kuvaus::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                    $ehtolause
                    ORDER BY ".Kuvaus::$taulunimi.".nimi ASC
                ";

        $osumat = $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);

        // Luodaan taulukot tyhjine vaihtoehtoineen (jolloin ei yläkokonaisuutta):
        $lajit = array();

        // Viedään otsikot ja vastaavat arvot taulukoihin:
        if(sizeof($osumat) != 0){
            foreach ($osumat as $lajiluokka) {
                array_push($lajit, 
                        new Lajiluokka($tietokantaolio, $lajiluokka->lj_id));
            }
        }
        return $lajit;
    }
    
    /** 
     * Palauttaa ne lajiluokat taulukossa, joiden yläluokka on sama kuin tällä.
     * Lajiluokat järjestetään annetun kielen mukaisesti 
     * (Huomaa, että lajiluokista tulevat mukaan vain ne, joilla kyseisen 
     * kielen mukainen kuvaus on olemassa!)
     * @param <type> $kieli_id
     * @param bool $itse_mukana jos this-lajiluokka otetaan mukaan, niin TRUE,
     * muuten FALSE.
     */
    public function hae_sisarlajiluokat($kieli_id, $itse_mukana){
        $ehtolause = "WHERE (".Kuvaus::$taulunimi.".kieli= $kieli_id
                    AND ".Lajiluokka::$taulunimi.".ylaluokka_id = ".$this->get_ylaluokka_id().")";
        if(!$itse_mukana){
            $ehtolause = "WHERE (".Kuvaus::$taulunimi.".kieli= ".$kieli_id.
                        " AND ".Lajiluokka::$taulunimi.".ylaluokka_id = ".$this->get_ylaluokka_id().
                        " AND ".Lajiluokka::$taulunimi.".id <> ".$this->get_id().")";
        }
        
        $hakulause = "SELECT DISTINCT ".Lajiluokka::$taulunimi.".id AS lj_id, ".
                                    Kuvaus::$taulunimi.".nimi AS nimi
                    FROM ".Lajiluokka::$taulunimi."
                    JOIN ".Kuvaus::$taulunimi."
                    ON ".Kuvaus::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                    $ehtolause
                    ORDER BY ".Kuvaus::$taulunimi.".nimi ASC
                ";


        $osumat = $this->tietokantaolio->
                                tee_omahaku_oliotaulukkopalautteella($hakulause);


        // Luodaan taulukot tyhjine vaihtoehtoineen (jolloin ei yläkokonaisuutta):
        $sisarukset = array();

        // Viedään otsikot ja vastaavat arvot taulukoihin:
        if(sizeof($osumat) != 0){
            foreach ($osumat as $lajiluokka) {
                array_push($sisarukset, 
                        new Lajiluokka($this->tietokantaolio, $lajiluokka->lj_id));
            }
        }
        return $sisarukset;
    }
    /**
     * Palauttaa lajiluokan nimen halutulla kielellä, nimen suomeksi tai arvon
     * Bongaustekstit::$tuntematon. 
     * 
     * Nimi html-koodataan, jotta heittomerkit yms toimivat hyvin.
     * 
     * @param type $kieli_id 
     */
    public function hae_lajiluokan_nimi($kieli_id){
        $nimi = Bongaustekstit::$tuntematon;
        
        if($this->olio_loytyi_tietokannasta){
            
            // Latina löytyy suoraan lajiluokasta!
            if($kieli_id == Kielet::$LATINA){
                $nimi = $this->get_html_encoded_arvo(
                                        Lajiluokka::$SARAKENIMI_NIMI_LATINA);
            } else{
                $kuvaus = $this->hae_kuvaus($kieli_id);
                if($kuvaus->olio_loytyi_tietokannasta){
                    $nimi = $kuvaus->get_html_encoded_arvo(
                                        Kuvaus::$SARAKENIMI_NIMI);
                }
            }
        }
        return $nimi;
    } 
    
   /**
    * Palauttaa yläluokkavalikon html:n. Tässä vaiheessa käytän sellaista
    * yksinkertaistusta, että luokkia on vain kahta tasoa: yläluokkia ja
    * alaluokkia. Myöhemmin luokkien määrää voi lisätä ja lajiluokat voi sitten
    * panna oikeaan yläluokkaansa tarkemmin. Jos nyt huvittaa.
    *
    * @param bool/string $nayta_tyhja jos false, niin ei näytä määrittelemätöntä
    * vaihtoehtoa. Muussa tapauksessa näyttää valikossa $nayta_tyhja-muuttujan arvon.
    * @param Tietokantaolio $tietokantaolio
    * @param <type> $ylaluokka_id_lj
    * @param <type> $kieli_id
    * @param <type> $param_otsikko
    * @param <type> $js_metodinimi
    * @param <type> $js_param_array
    * @return <type>
    */
   static function nayta_ylaluokkavalikko($nayta_tyhja,
                                   $tietokantaolio,
                                   &$ylaluokka_id_lj,
                                   $kieli_id,
                                   $param_otsikko,
                                   $js_metodinimi,
                                   $js_param_array){

       // Haetaan lajiluokkien ja niihin liittyvien kuvausten tiedot.
       // HUOM! Tässä luotan siihen, ettei samalla kielellä ole kuin yksi
       // kuvausrivi yhtä lajiluokkaa kohti. Tämä pitää varmistaa!
       $hakulause = "SELECT DISTINCT ".Kuvaus::$taulunimi.".nimi AS nimi, ".
                                        Lajiluokka::$taulunimi.".id AS lj_id
                   FROM ".Lajiluokka::$taulunimi."
                   JOIN ".Kuvaus::$taulunimi."
                   ON ".Kuvaus::$taulunimi.".lajiluokka_id = ".
                            Lajiluokka::$taulunimi.".id
                   WHERE ".Kuvaus::$taulunimi.".kieli= $kieli_id
                   AND ".Lajiluokka::$taulunimi.".ylaluokka_id = -1
                  ";


       $osumat = $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
        

       // Luodaan taulukot tyhjine vaihtoehtoineen (jolloin ei yläkokonaisuutta):
       $arvot = array();
       $nimet = array();

       if($nayta_tyhja){
           $arvot[0] = -1;
           $nimet[0] = $nayta_tyhja;
       }


       // Viedään otsikot ja vastaavat arvot taulukoihin:
       if(sizeof($osumat) != 0){
           foreach ($osumat as $lajiluokka) {
               array_push($arvot, $lajiluokka->lj_id);
               array_push($nimet, $lajiluokka->nimi);
           }
       }

       $valikkohtml = "";

       try{
           $name_arvo = "ylaluokka_id_lj";
           $id_arvo = "";
           $class_arvo = "";
           $oletusvalinta_arvo = $ylaluokka_id_lj;
           $otsikko = $param_otsikko;
           $onchange_metodinimi = $js_metodinimi;
           $onchange_metodiparametrit_array = $js_param_array;

           $valikkohtml.= Html::luo_pudotusvalikko_onChange($arvot,
                                                           $nimet,
                                                           $name_arvo,
                                                           $id_arvo,
                                                           $class_arvo,
                                                           $oletusvalinta_arvo,
                                                           $otsikko,
                                                           $onchange_metodinimi,
                                               $onchange_metodiparametrit_array);

           
       }
       catch(Exception $poikkeus){
           $valikkohtml = 
               Bongaustekstit::$lajiluokkalomake_virheilm_ylaluokkavalikko." (".
                           $poikkeus->getMessage().")";
       }
       return $valikkohtml;
   }

   /**
    * Palauttaa lajivalikon html-koodin. Pohjautuu tässä vaiheessa vain
    * kaksikerroksiseen lajiluokkahierarkkiaan.
    * @param <type> $lajiluokka_id_hav
    * @param Tietokantaolio $tietokantaolio
    * @param <type> $ylaluokka_id_lj
    * @param <type> $kieli_kuv
    * @param <type> $otsikko
    */
   static function nayta_lajivalikko(&$lajiluokka_id_hav,
                               $tietokantaolio,
                               $ylaluokka_id_lj,
                               $kieli_kuv,
                               $otsikko){
       // Haetaan lajiluokkien ja niihin liittyvien kuvausten tiedot.
       // HUOM! Tässä luotan siihen, ettei samalla kielellä ole kuin yksi
       // kuvausrivi yhtä lajiluokkaa kohti.
       $hakulause = "SELECT DISTINCT ".Lajiluokka::$taulunimi.".id AS lj_id, ".
                                    Kuvaus::$taulunimi.".nimi AS nimi
                   FROM ".Lajiluokka::$taulunimi."
                   JOIN ".Kuvaus::$taulunimi."
                   ON ".Kuvaus::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   WHERE (".Kuvaus::$taulunimi.".kieli= $kieli_kuv
                   AND ".Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id_lj)
                   ORDER BY ".Kuvaus::$taulunimi.".nimi ASC
                  ";


       $osumat = $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
       
       // Luodaan taulukot tyhjine vaihtoehtoineen (jolloin ei yläkokonaisuutta):
       $arvot = array();
       $nimet = array();

       // Viedään otsikot ja vastaavat arvot taulukoihin:
       if(sizeof($osumat) != 0){
           foreach ($osumat as $lajiluokka) {
               array_push($arvot, $lajiluokka->lj_id);
               array_push($nimet, $lajiluokka->nimi);
           }
       }

       $valikkohtml = "";

       try{
           $name_arvo = "lajiluokka_id_hav";
           $oletusvalinta_arvo = $lajiluokka_id_hav;
           $valikkohtml.= Html::luo_pudotusvalikko($arvot,
                                                   $nimet,
                                                   $name_arvo,
                                                   $oletusvalinta_arvo,
                                                   $otsikko);
       }
       catch(Exception $poikkeus){
           $valikkohtml =
               Bongaustekstit::$havaintolomake_virheilm_lajivalikko." (".
                           $poikkeus->getMessage().")";
       }
       return $valikkohtml;
   }
}
?>
