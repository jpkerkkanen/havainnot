<?php

/**
 * Description of Kuvaus
 * Käärii sisäänsä tietokannan kuvaukset-taulun yhden rivin ja huolehtii
 * vuorovaikutuksesta tietokannan kanssa (uuden tallennus, muokkaus ja poisto).
 *  
 * Kuvaus liittyy aina yhteen lajiluokkaan ja sen avulla voidaan lajille tai
 * luokalle antaa nimet ja kuvaukset eri kielillä.
 */

/* Lajiluokkien nimet ja kuvaukset eri kielillä. Tietokantataulu: *
create table kuvaukset
(
  id                    int auto_increment not null,
  lajiluokka_id         int default -1 not null,
  nimi                  varchar(128) not null,
  kuvaus                varchar(5000) not null,
  kieli                 smallint not null,
  primary key (id),
  index(lajiluokka_id),
  FOREIGN KEY (lajiluokka_id) REFERENCES lajiluokat (id)
                      ON DELETE CASCADE

) ENGINE=INNODB;
 *
 * @author J-P
 */
class Kuvaus extends Malliluokkapohja{

    // Tietokannan sarakenimet:
    public static $SARAKENIMI_ID= "id";
    public static $SARAKENIMI_LAJILUOKKA_ID= "lajiluokka_id";
    public static $SARAKENIMI_NIMI= "nimi";
    public static $SARAKENIMI_KUVAUS= "kuvaus";
    public static $SARAKENIMI_KIELI= "kieli";
    
    public static $taulunimi = "kuvaukset";

    /**
     * Konstruktorin "overloading" eli eri konstruktorit eri parametreille
     * ei ole tuettu PHP:ssä. Kierrän tämän antamalla parametreille, joita
     * ei käytetä, vakioarvon, joka tarkoittaa, ettei parametri käytössä.
     *
     * @param Tietokantaolio $tietokantaolio
     * @param <type> $tk_kuvausolio Tietokantahausta saatava yksi rivi
     * oliona.
     * 
     * HUOM Parametrien suositusjärjestys on ensin id, sitten tietokantaolio.
     * En jaksanut tähän muuttaa.
     */
    function __construct($tietokantaolio, $id){
        
        $tietokantasolut = 
            array(new Tietokantasolu(Kuvaus::$SARAKENIMI_ID, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Kuvaus::$SARAKENIMI_LAJILUOKKA_ID, Tietokantasolu::$luku_int), 
                new Tietokantasolu(Kuvaus::$SARAKENIMI_NIMI, Tietokantasolu::$mj_tyhja_EI_ok), 
                new Tietokantasolu(Kuvaus::$SARAKENIMI_KUVAUS, Tietokantasolu::$mj_tyhja_ok), 
                new Tietokantasolu(Kuvaus::$SARAKENIMI_KIELI, Tietokantasolu::$luku_int)
               );
        
        $taulunimi = Kuvaus::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
    }
        
    // Getterit ja setterit:
    public function get_lajiluokka_id(){
        return $this->get_arvo(Kuvaus::$SARAKENIMI_LAJILUOKKA_ID);
    }
    public function set_lajiluokka($uusi){
        return $this->set_arvo($uusi, Kuvaus::$SARAKENIMI_LAJILUOKKA_ID);
    }
    public function get_nimi(){
        return $this->get_arvo(Kuvaus::$SARAKENIMI_NIMI);
    }
    
    public function get_nimi_html_encoded(){
        return $this->get_html_encoded_arvo(Kuvaus::$SARAKENIMI_NIMI);
    }
    
    public function set_nimi($uusi){
        // Tarkistetaan (käyttäjän syöte).
        $uusi = mysql_real_escape_string(stripslashes(trim($uusi)));
        
        // ekat kirjaimet sopivan suuriksi:
        if($this->hae_lajiluokka()->get_ylaluokka_id() != -1){
                $uusi = Yleismetodit::eka_kirjain_pieneksi($uusi);
        }
        else{
            $uusi = Yleismetodit::eka_kirjain_isoksi($uusi);
        }
        // Täällä vakiotarkistus tehdään taas, mutta eipä tuo haitanne.
        return $this->set_arvo($uusi, Kuvaus::$SARAKENIMI_NIMI);
    }
    public function get_kuvaus(){
        return $this->get_arvo(Kuvaus::$SARAKENIMI_KUVAUS);
    }
    public function set_kuvaus($uusi){
        return $this->set_arvo($uusi, Kuvaus::$SARAKENIMI_KUVAUS);
    }
    public function get_kieli(){
        return $this->get_arvo(Kuvaus::$SARAKENIMI_KIELI);
    }
    public function set_kieli($uusi){
        return $this->set_arvo($uusi, Kuvaus::$SARAKENIMI_KIELI);
    }
    
    /**
     * Palauttaa totuusarvolla tiedon siitä, onko olio valmis tallennukseen, eli
     * onko muuttujat asianmukaisia (määriteltyjä, lukumuotoisia, muttei
     * $this->MUUTTUJAA_EI_MAARITELTY-arvoisia). Parametrilla annetaan funktiolle 
     * tieto siitä, onko kysymyksessä uuden tallennus vai vanhan muokkaus.
     *
     * Uuden tallennuksessa täällä tarkistetaan, ettei samalla kielellä ole jo kuvausta
     * kyseiselle lajiluokalle. Vain yksi kuvaus/kieli/lajiluokka sallitaan!
     * 
     * HUOM! Pitäisikö saman arvon lisääminen eri lajiluokille estää täällä? 
     * Nyt EI ole tätä estetty.
     * 
     * Vanhan muokkauksessa täällä tarkistetaan, onko
     * tietoja muutettu. Ellei tietoja ole muutettu, ei tietokantarivi muuttuisi,
     * minkä syy voi olla hämärä (myös virheellinen syöte tms). Siksi
     * tallentamaan ei päästetä, ellei jotakin muutosta tullut.
     * 
     * Huom! Palauttaa aina totuusarvon, mutta virhetapauksissa lisää
     * kuvausoliolle virheilmoituksen, joka kuvaa virheen tarkemmin.
     *
     * @param <type> $uusi
     * @return boolean 
     */
    function on_tallennuskelpoinen($uusi){
        $tila = false;
        $nimi = $this->get_nimi();
        $kieli = $this->get_kieli();
        $kuvaus = $this->get_kuvaus();
        $lajiluokka_id = $this->get_lajiluokka_id();
        
        if($uusi){
            
           // Seuraavat tarkistukset ehkä turhia, mutta en jaksa muuttaa.
           // Parempi tarkistaa monta kertaa, kuin unohtaa. Ja toimivan
           // muuttaminen ei aina ole tarpeen.
           if($this->lukumuotoinen_muuttuja_ok($lajiluokka_id, true,
                                    Bongaustekstit::$kuvauslomake_lajiluokka)&&
            $this->lukumuotoinen_muuttuja_ok($kieli, true,
                                    Bongaustekstit::$kuvauslomake_kieli)&&
            $this->mjmuotoinen_muuttuja_ok($nimi, true, true,
                                    Bongaustekstit::$kuvauslomake_nimi)&&
            $this->mjmuotoinen_muuttuja_ok($kuvaus, true, true,
                                    Bongaustekstit::$kuvauslomake_kuvaus)){

                // Tarkistetaan, ettei kuvausta ole ennestään tietokannassa:
                if($this->kuvausta_lj_kieli_parille_ei_ole()){
                    
                    // TArkistetaan vielä, ettei nimi ole tyhjä:
                    if(empty($nimi)){
                        $this->lisaa_virheilmoitus(
                                Bongaustekstit::$kuvaus_virheilmoitus_nimi_tyhja);
                    } else{
        
                        //TArkistetaan, ettei nimi jo käytössä:
                        if($this->lajinimi_on_jo_talla_kielella()){
                            $this->lisaa_virheilmoitus(
                                Bongaustekstit::
                                $kuvaus_virheilmoitus_nimi_jo_kaytossa);
                        } else{
                            // Muuttujat valmiina tallennukseen:
                            $tila = true;
                        }
                    }
                } else{
                    $this->lisaa_virheilmoitus(
                    Bongaustekstit::
                            $kuvaus_virheilmoitus_kuvaus_lj_kieli_parille_on_jo);
                }
            } else{
                $this->lisaa_virheilmoitus(
                Bongaustekstit::$kuvaus_virheilmoitus_tiedoissa_virheita);
            }
        }

        // Vanhan muokkaus.
        else{
            
            $tila = true;   // Helpompi näin tässä.
            $putsaa = true;
            $tyhja_ok = true;
            
            if(!$this->mjmuotoinen_muuttuja_ok($nimi, $putsaa, 
                        $tyhja_ok, Bongaustekstit::$kuvauslomake_nimi)){
                $tila = false;
                $this->lisaa_virheilmoitus(
                    Bongaustekstit::$kuvaus_virheilmoitus_viallinen_nimi);
            }
            else if(empty($nimi)){
                $tila = false;
                $this->lisaa_virheilmoitus(
                        Bongaustekstit::$kuvaus_virheilmoitus_nimi_tyhja);
            }
            //TArkistetaan, ettei nimi jo käytössä:
            else if($this->lajinimi_on_jo_talla_kielella()){ 
                $tila = false;
                $this->lisaa_virheilmoitus(
                    Bongaustekstit::
                    $kuvaus_virheilmoitus_nimi_jo_kaytossa);
            }

            if(!$this->lukumuotoinen_muuttuja_ok($kieli, true,
                            Bongaustekstit::$kuvauslomake_kieli)){
                $tila = false;
                $this->lisaa_virheilmoitus(
                    Bongaustekstit::$kuvaus_virheilmoitus_viallinen_kieli);
            }
            else{
                // Jos kunnossa, tarkistetaan, ettei vain kuvausta jo ole:
                // Tarkistetaan, ettei kuvausta ole ennestään tietokannassa:
                if(!$this->kuvausta_lj_kieli_parille_ei_ole()){

                    // Kuvausta ei voida sallia:
                    $tila = false;
                    $this->lisaa_virheilmoitus(
                    Bongaustekstit::$kuvaus_virheilmoitus_kuvaus_lj_kieli_parille_on_jo);
                }
            }

            if(!$this->mjmuotoinen_muuttuja_ok($kuvaus, $putsaa, $tyhja_ok,
                            Bongaustekstit::$kuvauslomake_kuvaus)){
                $tila = false;
                $this->lisaa_virheilmoitus(
                    Bongaustekstit::$kuvaus_virheilmoitus_viallinen_kuvaus);
            }
        }
        return $tila;
    }
    
    /**
     * Tallentaa tiedot tietokantaan. HUOM! Tietojen tarkistus tehdään
     * kutsumalla on_tallennuskelpoinen-metodia. Siellä tehdään myös se tärkeä
     * tarkistus, ettei samalla kielellä ole samalle lajiluokalle jo ennestään
     * kuvausta. Vain yksi kuvaus/kieli/lajiluokka!!
     *
     * Tallennus vaatii sen, että kaikkiin neljään tietokantamuuttujaan
     * (poislukien id) on asetettu jokin arvo. Muuten tallennuksessa ei ole
     * järkeä.
     * 
     * Palauttaa arvon Kuvaus::$OPERAATIO_ONNISTUI, muuten arvon Kuvaus::$VIRHE.
     * Virheilmoitukset ovat olion ilmoituksissa saatavilla.
     */
    public function tallenna_uusi(){
        $taulu = $this->tk_taulunimi;
        $palaute = Kuvaus::$VIRHE;
        
        if(!$this->tietokantaolio instanceof Tietokantaolio){
            $palaute = Bongaustekstit::$virheilmoitus_tietokantaolio_ei_maaritelty;
        }
        else{
            // Tietojen tarkistus:
            $uusi = true;
            if($this->on_tallennuskelpoinen($uusi)){
                $palaute = parent::tallenna_uusi();
            }
        }
        return $palaute;
    }

    /**
     * Tallentaa muuttuneet tiedot tietokantaan.
     * Tiedot tarkistetaan täällä automaattisesti. Tietojen pitää olla
     * määriteltyjä ja lukujen positiivisia.
     *
     * Muutoksia voi tehdä vain kieleen, nimeen ja kuvaukseen. 
     * HUOM! Isäntälajiluokkaa
     * ei voi vaihtaa, koska se voisi aiheuttaa ikäviä sotkuja. Muuttuu vielä
     * joutsenet ankoiksi havainnoissa..
     *
     * Palauttaa arvon Kuvaus::$OPERAATIO_ONNISTUI, muuten arvon Kuvaus::$VIRHE.
     * Virheilmoitukset ovat olion ilmoituksissa saatavilla.
     */
    public function tallenna_muutokset(){
        $taulu = $this->tk_taulunimi;
        $palaute = Kuvaus::$VIRHE;

        if(!$this->tietokantaolio instanceof Tietokantaolio){
            $palaute = Bongaustekstit::$virheilmoitus_tietokantaolio_ei_maaritelty;
        }
        else{
            // Tietojen tarkistus:
            $uusi = false;
            if($this->on_tallennuskelpoinen($uusi)){
                $palaute = parent::tallenna_muutokset();
            } else{
                $this->lisaa_kommentti("Ei ole tallennuskelpoinen!");
            }
        }

        return $palaute;
    }

    /**
     * Poistaa id-arvoa vastaavan kuvauksen tietokannasta. Palauttaa joko
     * arvon Kuvaus::$OPERAATIO_ONNISTUI tai Kuvaus::$VIRHE. 
     * Virheistä annetaan kuvaukset virheilmoituksiin.
     * 
     * Virheilmoitukset lisätään myös olion virheilmoituksiin, josta ne kaikki
     * saadaan esiin haluttaessa.
     * 
     * Tätä metodia ei ehkä tarvita, koska poisto tapahtuu aina lajiluokan poiston
     * yhteydessä, koska tällöin kuvaus poistetaan tietokannan cascade-toiminnon
     * puolesta automaattisesti. Tälle on lähinnä käyttöä, jos jollakin hassulla
     * kielellä oleva halutaan poistaa (eikä muuttaa). Samalla pitänee varmistaa, ettei
     * suomenkielinen kuvaus häviä, koska sen olemassaolo oletetaan.
     *
     * @return 
     */
    public function poista(){
        $palaute = Kuvaus::$VIRHE;
        
        if(!$this->tietokantaolio instanceof Tietokantaolio){
            $this->lisaa_virheilmoitus(Bongaustekstit::
                                $virheilmoitus_tietokantaolio_ei_maaritelty);
        }
        // Tämä on tärkeä!
        else if($this->get_kieli() == Kielet::$SUOMI){
            $this->lisaa_virheilmoitus(Bongaustekstit::
                        $kuvaus_virheilmoitus_suomenkielista_ei_saa_poistaa);
        }
        else{
           $palaute = parent::poista();

            if($palaute != Kuvaus::$OPERAATIO_ONNISTUI){
                $this->lisaa_virheilmoitus(Bongaustekstit::
                                            $kuvaus_virheilmoitus_poisto_eiok);
            }
        }
        
        return $palaute;
    }
    
    /**
     * Tämä tarkistaa, onko lajiluokalle jo kuvaus tietyllä kielellä ja 
     * palauttaa TRUE, ELLEI sellaista löydy ja muute FALSE. 
     * 
     * Huomaa, että tutkinnassa ei oteta huomioon muokattavaa oliota itseään, 
     * koska muuten muokkaus ei mene läpi, ellei kieltä muuteta! Uuden luomisessa
     * ei ole ongelmaa, koska silloin olion id on ei_maaritelty (ei löyty
     * tietokannasta).
     */
    public function kuvausta_lj_kieli_parille_ei_ole(){
        $palaute = false;
        
        // HUOM! Hakulauseessa avainsanojen ympärille tarvitaan välit!
        $hakulause = "SELECT * FROM ".$this->tk_taulunimi.
                     " WHERE ".Kuvaus::$SARAKENIMI_LAJILUOKKA_ID."=".
                            $this->get_lajiluokka_id().
                    " AND ".Kuvaus::$SARAKENIMI_KIELI."=".$this->get_kieli().
                    " AND ".Kuvaus::$SARAKENIMI_ID."<>'".$this->get_id()."'";
        
        $osumat = 
            $this->tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);
        
        if(empty($osumat)){
            $palaute = true;
        }
        
        return $palaute;
    }
    
    /**
     * Tämä tarkistaa, onko lajiluokalle jo nimi tietyllä kielellä ja 
     * palauttaa FALSE, ELLEI sellaista löydy ja muute TRUE. 
     * 
     * Huomaa, että tutkinnassa ei oteta huomioon muokattavaa oliota itseään, 
     * koska muuten muokkaus ei mene läpi, ellei nimeä muuteta! Uuden luomisessa
     * ei ole ongelmaa, koska silloin olion id on ei_maaritelty (ei löyty
     * tietokannasta).
     */
    public function lajinimi_on_jo_talla_kielella(){
        $palaute = true;
        
        // HUOM! Hakulauseessa avainsanojen ympärille tarvitaan välit!
        $hakulause = "SELECT id FROM ".$this->tk_taulunimi.
                     " WHERE ".Kuvaus::$SARAKENIMI_KIELI."=".
                            $this->get_kieli().
                    " AND ".Kuvaus::$SARAKENIMI_NIMI."='".$this->get_nimi()."'".
                    " AND ".Kuvaus::$SARAKENIMI_ID."<>'".$this->get_id()."'";
        
        $osumat = 
            $this->tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);
        
        if(empty($osumat)){
            $palaute = false;
        }
        
        return $palaute;
    }
    
    /**
     * Palauttaa kuvauksen isäntälajiluokan Lajiluokka-luokan oliona tai
     * ellei löydy, arvon Kuvaus::MUUTTUJAA_EI_MAARITELTY
     * @return \Lajiluokka 
     */
    public function hae_lajiluokka(){
        $taulunimi = Lajiluokka::$taulunimi;
        $sarakenimi = Lajiluokka::$SARAKENIMI_ID;
        $hakuarvo = $this->get_lajiluokka_id();
        $lajiluokka_tk = 
            $this->tietokantaolio->hae_eka_osuma_oliona($taulunimi, 
                                                        $sarakenimi, 
                                                        $hakuarvo);
        if($lajiluokka_tk == Tietokantaolio::$HAKU_PALAUTTI_TYHJAN){
            return Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        }
        else{
            return new Lajiluokka($this->tietokantaolio, $lajiluokka_tk->id);
        }
    }
}
?>
