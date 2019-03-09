<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tietokantaolio:
 * Luokka sisältää metodeita tietokantaan yhdistämiseen ja yhteyden
 * katkaisuun, erilaisten hakujen tekemiseen jne,
 *
 * TURVALLISUUSHUOMAUTUS: Ilmeisesti olisi parempi täällä ja vasta täällä
 * mysql_real_escape_string()-koodata tietokantahauissa käytetyt käyttäjän
 * syötteet (silloinkin omahaku-juttu pitäisi ottaa tarkasti). 
 * Nyt tarkistus tehdään Tietokantasolu-luokan set_arvo()-metodissa, mikä
 * on ihan ok, kun vain muistaa puhdistaa mahdolliset muualta tulevat
 * hakusanat yms ennen tietokantaan lähettämistä! Pitää muistaa, että myös
 * hakuehdot pitää putsata, eikä vain tallennettavat!! 
 * 
 * @author Jukka-Pekka
 */
class Tietokantaolio extends Pohja
{
    private $dbtyyppi;
    private $dbhost;
    private $dbuser;
    private $dbsalis;
    private $yhteys;

    /**
     * Tämä merkkijono palautetaan aina sellaisten kyselyjen onnistuessa,
     * joiden ei ole tarkoitus palauttaa suoraan viestiä käyttäjälle.
     * (esim. poistot, tallennukset, muokkaukset).
     * @var string
     */
    public static $HAKU_ONNISTUI = "onnistui";

    /* Jos tietyt haut eivät löydä mitään, palautetaan tämä merkkijono.
     * Katso kuitenkin aina kunkin metodin API.
     */
    public static $HAKU_PALAUTTI_TYHJAN = "tuntematon";

    /* Jotkut metodit palauttavat tämän, jos kysely palauttaa falsen: */
    public static $HAKUVIRHE = "Hakuvirhe";
    
    /* Esim jos update-metodissa muutosten lukumäärän halutaan olevan
     * rajaton: */
    public static $EI_RAJOITETTU = -123;

    /* Update: tiedoissa ei muutoksia: */
    public static $update_tiedoissa_ei_havaittu_muutoksia =
        "Tiedoissa ei havaittu muutoksia!";

    /**
     * Luokan muodostin:
     *
     * @param <type> $dbtyyppi tietokannan tyyppi (esim 'mysql')
     * @param <type> $dbhost tietokantapalvelimen nimi
     * @param <type> $dbuser käyttäjätunnus tietokantaan
     * @param <type> $dbsalis salasana tietokantaan. Tämä ei sitten saa
     * joutua vieraisiin käsiin!!
     */
    public function __construct($dbtyyppi, $dbhost, $dbuser, $dbsalis)
    {
        parent::__construct();
        $this->dbtyyppi = $dbtyyppi;
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbsalis = $dbsalis;
    }

    /**
     * Yhdistää tietokantaan.
     * @param <type> $dbnimi Tietokannan nimi, johon yhdistetään.
     *
     * Huom. Yksi olio voisi hallita useita eri yhteyksiä, jos yhteys-
     * muuttuja olisi esim vektori.
     */
    function yhdista_tietokantaan($dbnimi)
    {
        if ($this->dbtyyppi === "mysql")
        {
            // Yhdistetään tietokantapalvelimeen
            $this->yhteys = mysql_connect($this->dbhost,
                                            $this->dbuser,
                                            $this->dbsalis)
            or die('Yhdist&auml;minen tietokantaan ei onnistunut!
                Tarkista tietokannan nimi ja salasana!');

            // Valitaan tietokanta:
            mysql_select_db($dbnimi, $this->yhteys)
            or die("Tietokannan valinta ep&auml;onnistui!");
        }
    }

    /**
     * Sulkee yhteyden tietokantaan.
     * @param <type> $dbnimi Tietokannan nimi, joka suljetaan.
     */
    function sulje_tietokanta()
    {
        if ($this->dbtyyppi === "mysql")
        {
            mysql_close($this->yhteys)
            or die('Tietokannan sulkeminen ei onnistunut!');
        }
    }

    /************************DEPRECATED *************************/
    /************************FUNCTION TEE_UPDATEHAKU *************************/
    /**
     * DEPRECATED: use instead method update_rivi()!
     * Tämä metodi suorittaa haun, jossa muutetaan olemassaolevan
     * tietokantataulun tietoja
     * yhden ehtolauseen avulla. Uudet tiedot ja vastaavat sarakenimet
     * syötetään parametritaulukoina.
     *
     * @param <type> $taulu taulun nimi
     * @param <type> $muutossarakkeet taulukko, jonka sisältää muutettavien
     * sarakkeiden nimet.
     * @param <type> $muutosarvot taulukko, jonka sisältää muutettavien
     * sarakkeiden uudet arvot.
     * @param <type> $ehtosarake sarakenimi, jonka arvo on ehtona muutosten
     * tekemiselle kyseisellä rivillä.
     * @param <type> $ehtoarvo arvo, joka ehtosarakekohdassa täytyy olla
     * kyseisellä rivillä
     * @param $max_muutosrivilkm luonnollinen luku, joka ilmoittaa määrän, jota
     * useampaa riviä ei muokata.
     * @return <type> palauttaa haun tuloksen eli function "mysql_query()"-
     * palautusarvon (true tai false).
     */
    public function tee_UPDATEhaku($taulu,
                                    $muutossarakkeet,
                                    $muutosarvot,
                                    $ehtosarake, 
                                    $ehtoarvo,
                                    $max_muutosrivilkm)
    {
        $tulos = false;

        if($this->dbtyyppi == 'mysql')
        {
            if(isset($taulu) && isset($muutossarakkeet) && isset($muutosarvot)&&
                isset($ehtosarake) && isset($ehtoarvo) &&
                sizeof($muutossarakkeet) == sizeof($muutosarvot))
            {
                $set_koodi ="SET ";
                for($i =0; $i < sizeof($muutossarakkeet); $i++)
                {
                    // Pilkut kohdalleen if-lauseen avulla.
                    if($i == 0){
                        $set_koodi .=
                                $muutossarakkeet[$i]."='".$muutosarvot[$i]."'";
                    }
                    else{
                        $set_koodi .=
                            ",".$muutossarakkeet[$i]."='".$muutosarvot[$i]."'";
                    }
                    
                }
                $hakulause = "UPDATE $taulu
                            $set_koodi
                            WHERE $ehtosarake='$ehtoarvo'
                            LIMIT $max_muutosrivilkm";

                $tulos = mysql_query($hakulause) or //FALSE on failure, true muutoin.
                          "Tietojen muokkaaminen ei onnistunut!";
            }         
        }
        return $tulos;
    }
    
    /************************FUNCTION hae_tk_oliot *************************/
    /**
     * Tämä metodi suorittaa haun, jossa haetaan kaikki tietokannan rivit, jotka
     * toteuttavat kaikki ehtotietokantasolut, jotka annetaan taulukossa
     * parametrina. Toisena parametrina annetaan tietokantataulun nimi.
     *
     * Palauttaa aina taulukon, joka voi olla tyhjä (virheen ilmetessä tyhjä).
     * 
     * @author Jukka-Pekka Kerkkänen, 14.5.2013
     */
    public function hae_tk_oliot($taulunimi, $ehtotietokantasolut){
        $palaute = array();
        $ei_ongelmia = true;    // Yksikin ongelma kääntää arvoon false.
        
        $hakulause = "SELECT * FROM ".$taulunimi." WHERE ";
        
        $laskuri = 0;
        foreach ($ehtotietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu && $solu->tiedot_ok()){
                
                // Lisätään alkuun AND, ellei eka kerta.
                if($laskuri > 0){
                    $hakulause .= " AND ";
                }
                
                if($solu->get_arvon_tyyppi() === Tietokantasolu::$luku_int){
                    $hakulause .= $solu->get_sarakenimi()."=".$solu->get_arvo();
                } else{
                    $hakulause .= $solu->get_sarakenimi()."=".
                                    "'".$solu->get_arvo()."'";
                }
            } else{
                $ei_ongelmia = false;
            }
            $laskuri++;
        }
        
        // Jos kaikki kunnossa, tehdään haku:
        if($ei_ongelmia){
            $palaute = $this->tee_omahaku_oliotaulukkopalautteella($hakulause);
        }
        
        return $palaute;
    }
    /************************FUNCTION UPDATE *************************/
    /**
     * Tämä metodi suorittaa haun, jossa muutetaan olemassaolevan
     * tietokantataulun tietoja (paitsi id:tä ei muuteta koskaan). 
     * Parametrina annetaan muutostiedot
     * Tietokantarivi-luokan oliona, ehtotiedot
     * Tietokantasolu-luokan oliona sekä muutettavien rivien maksimilukumäärä.
     *
     * HUOM! Tietokantasoluehto pitää olla uusi olio [new Tietokantasolu].
     * Valmiin solun haku muutettavasta oliosta voi aiheuttaa hankalasti
     * huomattavia yllätyksiä, jos ehto ja muutos viittaavat samaan 
     * tietokantasarakkeeseen!!
     * 
     * <p>
     * Palauttaa joko arvon Tietokantaolio::$HAKU_ONNISTUI tai 
     * Tietokantaolio::$HAKUVIRHE toiminnon onnistumisen mukaan.
     * </p>
     * 
     * 
     * @param Tietokantarivi $tietokantarivi tallennettavat tiedot. Huomaa, että
     * vain määritellyt ja muutetut tiedot tallennetaan. 
     * @param Tietokantasolu $tietokantasoluehto mihin tallennetaan
     * @param int $max_muutosrivilkm Huom! Arvo Tietokantaolio::$EI_RAJOITETTU
     * antaa mahdollisuuden muuttaa mielivaltaisen määrän rivejä.
     * @return type 
     * @author Jukka-Pekka Kerkkänen 22.10.2012
     */
    public function update_rivi($tietokantarivi, 
                                $tietokantasoluehto, 
                                $max_muutosrivilkm)
    {
        $tulos = Tietokantaolio::$HAKUVIRHE;
        
        if($tietokantarivi instanceof Tietokantarivi && 
            $tietokantasoluehto instanceof Tietokantasolu &&
            $tietokantasoluehto->tiedot_ok()){
            
            $taulu = $tietokantarivi->get_taulunimi();
            $ehtosarake = $tietokantasoluehto->get_sarakenimi();
            $ehtoarvo = $tietokantasoluehto->get_arvo();
            
            if($this->dbtyyppi == 'mysql')
            {
                $laskuri = 0;
                $set_koodi ="SET ";
                $tietokantasolut = $tietokantarivi->get_tietokantasolut();
                
                // Tallennetaan kaikki määritellyt ja muutetut solut paitsi id-sarake:
                foreach ($tietokantasolut as $solu) {
                    if($solu instanceof Tietokantasolu && 
                        $solu->tiedot_ok() &&
                        $solu->on_muokattu() &&
                        $solu->get_sarakenimi() != Malliluokkapohja::$SARAKENIMI_ID){
                        
                        // Pilkut kohdalleen if-lauseen avulla.
                        if($laskuri > 0){
                            $set_koodi .= ",";
                        }
                        if($solu->get_arvon_tyyppi() === Tietokantasolu::$luku_int){
                            $set_koodi .=
                                $solu->get_sarakenimi()."=".$solu->get_arvo();
                        }
                        else{
                            $set_koodi .=
                                $solu->get_sarakenimi()."='".$solu->get_arvo()."'";
                        }

                        $laskuri++;
                    }
                }
                
                if($max_muutosrivilkm === Tietokantaolio::$EI_RAJOITETTU){
                    $hakulause = "UPDATE $taulu
                                $set_koodi
                                WHERE $ehtosarake='$ehtoarvo'";
                }
                else{
                    $hakulause = "UPDATE $taulu
                                $set_koodi
                                WHERE $ehtosarake='$ehtoarvo'
                                LIMIT $max_muutosrivilkm";
                }
                $tulos = mysql_query($hakulause);  //FALSE on failure, true muutoin.

                if($tulos){
                    $tulos = Tietokantaolio::$HAKU_ONNISTUI;
                    //$this->lisaa_kommentti("<br/>".$hakulause."<br/>");
                } else{
                    //$this->lisaa_virheilmoitus("<br/>".$hakulause."<br/>");
                }
            }
        }
        
        return $tulos;
    }

    /************************FUNCTION TEE_WHEREHAKU_1 *************************/
    /**
     * Tämä metodi suorittaa haun, jossa haetaan tietokantataulun koko rivejä
     * yhden ehtolauseen avulla.
     *
     * @param <type> $taulu taulun nimi
     * @param <type> $taulun_sarake sarakkeen nimi
     * @param <type> $hakuarvo sarakkeesta haettava arvo
     * @return <type> palauttaa haun tuloksen eli function "mysql_query()"-
     * palautusarvon, joka voi olla false!
     */
    private function tee_WHEREhaku_1($taulu, $taulun_sarake, $hakuarvo)
    {
        if($this->dbtyyppi == 'mysql')
        {
            $hakulause = "SELECT * FROM $taulu
                            WHERE $taulun_sarake='$hakuarvo'";
            $tulos = mysql_query($hakulause); //FALSE on failure
            return $tulos;
        }
    }

    /************************FUNCTION TEE_WHEREHAKU_1_(ylhäältä_alas) *********/
    /**
     * Tämä metodi suorittaa haun, jossa haetaan tietokantataulun koko rivejä
     * yhden ehtolauseen avulla. Rivit järjestetään halutun sarakkeen mukaan
     * ylhäältä alaspäin.
     *
     * @param <type> $taulu taulun nimi
     * @param <type> $taulun_sarake sarakkeen nimi
     * @param <type> $hakuarvo sarakkeesta haettava arvo
     * @param <type> $jarjestyssarake sarake, jonka mukaan rivit järjestetään.
     * @return <type> palauttaa haun tuloksen eli function "mysql_query()"-
     * palautusarvon, joka on tulosrivitaulukko tai false.
     *
    public function tee_WHEREhaku_1_jarjestaen($taulu,
                                                $taulun_sarake,
                                                $hakuarvo,
                                                $jarjestyssarake)
    {
        if($this->dbtyyppi == 'mysql')
        {
            $hakulause = "SELECT * FROM $taulu
                            WHERE $taulun_sarake='$hakuarvo'
                            ORDER BY $jarjestyssarake DESC";
            $tulos = mysql_query($hakulause); //FALSE on failure
            return $tulos;
        }
    }

    /************hae_SELECT_WHERE_1ehto($taulu, $taulun_sarake, $hakuarvo) *****/
    /**
     * Tämä metodi etsii taulunimen, sarakenimen ja -arvon (parametrit)
     * mukaisen haun. Kuten tee_WHEREhaku_1($taulu, $taulun_sarake, $hakuarvo),
     * mutta hakee osumat olioina ja palauttaa oliotaulukon, joka voi olla tyhjä.
     *
     * @param <type> $taulu
     * @param <type> $taulun_sarake
     * @param <type> $hakuarvo
     * @return <type> palauttaa aina taulukon, joka on tyhjä, jos
     * jokin menee pieleen. Muuten taulukko sisältää tietokantaosumat olioina.
     *
    function hae_SELECT_WHERE_1ehto($taulu, $taulun_sarake, $hakuarvo)
    {
        $palaute = array();

        if($this->dbtyyppi == 'mysql')
        {
            //FALSE on failure:
            $tulos = $this->tee_WHEREhaku_1($taulu, $taulun_sarake, $hakuarvo);
            
            if($tulos != false){
                $palaute = $this->hae_osumarivit_olioina($tulos);
            }
        }

        return $palaute;
    }

    /************************FUNCTION TEE_WHEREHAKU_2 ********************************/
    /**
     * Tämä metodi suorittaa haun, jossa haetaan tietokantataulun koko rivejä
     * kahden ehtolauseen avulla.
     *
     * @param <type> $taulu taulun nimi
     * @param <type> $sarake1 ekan sarakkeen nimi
     * @param <type> $sarake1 tokan sarakkeen nimi
     * @param <type> $arvo1 1. sarakkeesta haettava arvo
     * @param <type> $arvo2 2. sarakkeesta haettava arvo
     * @return <type> palauttaa haun tuloksen eli function "mysql_query()"-
     * palautusarvon.
     *
    function tee_WHEREhaku_2($taulu, $sarake1, $sarake2, $arvo1, $arvo2)
    {
        if($this->dbtyyppi == 'mysql')
        {
            $hakulause = "SELECT * FROM $taulu
                            WHERE $sarake1='$arvo1'
                            AND $sarake2='$arvo2'";
            $tulos = mysql_query($hakulause); //FALSE on failure
            return $tulos;
        }
    }

    /************************FUNCTION TEE_OMAhaku ********************************/
    /**
     * Tämä metodi suorittaa parametrina saatavan hakulauseen mukaisen haun.
     * kahden ehtolauseen avulla.
     *
     * @param <type> $hakulause mysql-lause, jolla haku tehdään.
     * @return <type> palauttaa haun tuloksen eli function "mysql_query()"-
     * palautusarvon, tai 'false', jos jokin menee pieleen.
     *
    function tee_OMAhaku($hakulause)
    {
        if($this->dbtyyppi == 'mysql')
        {
            $tulos = mysql_query($hakulause); //FALSE on failure
            return $tulos;
        }
    }

    /************************FUNCTION TEE_OMAhaku_oliotaulukkopalautteella *****/
    /**
     * Tämä metodi suorittaa parametrina saatavan hakulauseen mukaisen haun.
     *
     * @param <type> $hakulause mysql-lause, jolla haku tehdään.
     * @return <type> palauttaa aina taulukon, joka on tyhjä, jos
     * jokin menee pieleen. Muuten taulukko sisältää tietokantaosumat olioina.
     */
    function tee_omahaku_oliotaulukkopalautteella($hakulause)
    {
        $palaute = array();

        if($this->dbtyyppi === 'mysql')
        {
            $tulos = mysql_query($hakulause, $this->yhteys); //FALSE on failure
            
            if($tulos != false){
                $palaute = $this->hae_osumarivit_olioina($tulos);   
            } else{
                echo "Virheviesti (tee_oma_haku_oliotaulukkopalautteella...): ".
                        mysql_error();
            }
        }

        return $palaute;
    }
    
    /************************FUNCTION TEE_OMAhaku_oliotaulukkopalautteella *****/
    /**
     * Tämä metodi suorittaa parametrina saatavan hakulauseen mukaisen haun.
     * 
     * Tulos palautetaan yhdessä isossa 
     * taulukossa kunkin rivin tiedot omana taulukkona, josta tietoja
     * voi hakea sekä sarakkeen nimellä, että sarakeindeksillä.
     *
     * @param <type> $hakulause mysql-lause, jolla haku tehdään.
     * @return <type> palauttaa aina taulukon, joka on tyhjä, jos
     * jokin menee pieleen. palauttaa tiedot taulukkona, josta tietoja
     * voi hakea sekä sarakkeen nimellä, että sarakeindeksillä.
     */
    function tee_omahaku_taulukkopalautteella($hakulause)
    {
        $palaute = array();

        if($this->dbtyyppi === 'mysql')
        {
            $tulos = mysql_query($hakulause, $this->yhteys); //FALSE on failure
            
            if($tulos != false){
                $palaute = $this->hae_osumarivit_taulukoina($tulos);   
            } else{
                echo "Virheviesti (tee_oma_haku_taulukkopalautteella...): ".
                        mysql_error();
            }
        }

        return $palaute;
    }

    /**
     * Hakee haun tuloksesta eli function "mysql_query()" palautusarvosta sen
     * sisältämät rivit.
     * @param <type> $hakutulos function "mysql_query()" palautusarvosta
     * @return int palauttaa aina luvun, joka on nolla myös, jos $hakutulos
     * ei ole määritelty tai on arvoltaan false. Muuten palauttaa osumarivien
     * lukumäärän.
     *
    function hae_osumarivien_lkm($hakutulos)
    {
        $palaute = 0;
        if($this->dbtyyppi == 'mysql')
        {
            if (isset($hakutulos) || $hakutulos != false)
            {
                $palaute = mysql_num_rows($hakutulos);
                if ($palaute == false)
                {
                    $palaute = 0;
                }
            }
        }
        return $palaute;
    }

    /**
     * Hakee taulusta yhden ehdon haulla saatujen rivien lkm:n.
     * @param <type> $taulu
     * @param <type> $taulun_sarake ehtosarakkeen nimi
     * @param <type> $hakuarvo
     * @return int palauttaa aina luvun, joka on nolla myös, jos hakutulos
     * ei ole määritelty tai on arvoltaan false. Muuten palauttaa osumarivien
     * lukumäärän.
     */
    function hae_osumien_lkm($taulu, $taulun_sarake, $hakuarvo)
    {
        $palaute = 0;
        if($this->dbtyyppi == 'mysql')
        {
            $hakulause = "SELECT COUNT(*) AS lkm
                            FROM $taulu
                            WHERE $taulun_sarake='$hakuarvo'";
            $hakutulos = mysql_query($hakulause); //FALSE on failure

            // Haetaan ainut 'osumarivi':
            $osumaolio = mysql_fetch_object($hakutulos);

            // Haetaan lkm, jos haku mennyt putkeen:
            if($osumaolio != false){
                $palaute = $osumaolio->lkm;
            }
        }
        return $palaute;
    }

    /**
     * Hakee haun tuloksesta eli function "mysql_query()" palautusarvosta sen
     * sisältämät rivit olioina ja palauttaa ne taulukkona.
     * @param <type> $hakutulos eli function "mysql_query()" palautusarvo
     * @return array palauttaa aina taulukon, joka on tyhjä osumien puuttumisen
     * lisäksi jos $hakutulos ei ole määritelty tai on arvoltaan false.
     * Muuten palauttaa taulukkona osumaoliot.
     */
    private function hae_osumarivit_olioina($hakutulos)
    {
        $oliot = array();
        $ind = 0;   //taulukon indeksi.
        if($this->dbtyyppi == 'mysql')
        {
            if(isset($hakutulos) && $hakutulos != false)
            {
                // fetch palauttaa lopuksi falsen.
                while (($rivi = mysql_fetch_object($hakutulos)) !== false)
                {
                    $oliot[$ind] = $rivi;
                    $ind++;
                }
            }
        }
        return $oliot;
    }
    
    /**
     * Hakee haun tuloksesta eli function "mysql_query()" palautusarvosta sen
     * sisältämät rivit isossa taulukossa kunkin rivin tiedot omana taulukkona, 
     * josta tietoja voi hakea sekä sarakkeen nimellä, että sarakeindeksillä..
     * @param <type> $hakutulos eli function "mysql_query()" palautusarvo
     * @return array palauttaa aina taulukon, joka on tyhjä osumien puuttumisen
     * lisäksi jos $hakutulos ei ole määritelty tai on arvoltaan false.
     * Muuten palauttaa taulukkona osumat.
     */
    private function hae_osumarivit_taulukoina($hakutulos)
    {
        $oliot = array();
        $ind = 0;   //taulukon indeksi.
        if($this->dbtyyppi == 'mysql')
        {
            if(isset($hakutulos) && $hakutulos != false)
            {
                // fetch palauttaa lopuksi falsen.
                while (($rivi = mysql_fetch_array($hakutulos, MYSQL_BOTH)) !== false)
                {
                    $oliot[$ind] = $rivi;
                    $ind++;
                }
            }
        }
        return $oliot;
    }
    
    /**
     * Hakee haun tuloksesta eli function "mysql_query()" palautusarvosta sen
     * sisältämän rivin (ekan) ja palauttaa tiedot taulukkona, josta tietoja
     * voi hakea sekä sarakkeen nimellä, että sarakeindeksillä.
     * 
     * @param type $taulunimi
     * @param type $sarakenimi
     * @param type $hakuarvo
     * @return type palauttaa Palauttaa aina taulukon, joka on tyhjä, ellei
     * mitään löydy.
     */
    function hae_eka_osuma_taulukkona($taulunimi, $sarakenimi, $hakuarvo){
        
        // Palautetaan taulukko aina, tai muuten pitää tarkistusta muuttaa
        // moneen paikkaan.
        $palaute = array();

        $hakutulos = $this->tee_WHEREhaku_1($taulunimi, $sarakenimi, $hakuarvo);
        
        if($this->dbtyyppi == 'mysql')
        {
            
            // Seuraavasta tietoa voi hakea sekä sarakenimillä että 
            // sarakkeen numeroindeksillä. Palauttaa FALSEn, ellei
            // mitään saada irti:
            $rivi = mysql_fetch_array($hakutulos, MYSQL_BOTH);
            if($rivi){
                $palaute = $rivi;
            }
        }
        // Haetaan lähettäjän tiedot:
        /*$osumataulu = $this->hae_osumarivit_taulukoina(
                $this->tee_WHEREhaku_1($taulunimi, $sarakenimi, $hakuarvo));

        // Taulu on tyhjä, ellei mitään löytynyt.
        if(sizeof($osumataulu) != 0){
            $palaute = $osumataulu[0];
        }*/
        return $palaute;
    }

    /*********************** HAE EKA OSUMA OLIONA *****************************/
    /**
     * Hakee tietokannasta parametria "$taulunimi" vastaavan taulukon parametreja
     * "$sarakenimi" ja "$hakuarvo" vastaavan tietueen. Palauttaa
     * tekstin Tietokantaolio::$HAKU_PALAUTTI_TYHJAN, ellei tietuetta löydy,
     * muuten ensimmäisen (vain!) löytyneen tietueen tiedot oliona.
     *
     * <p>Tällä on kätevä tehdä usein toistuvia yksiselitteisiä hakuja, esim.
     * indeksin perusteella. Tuloksia on aina korkeintaan yksi.</p>
     * @param string $taulunimi Etsittävän taulun nimi
     * @param string $sarakenimi Etsittävän sarakkeen nimi
     * @param $hakuarvo Etsittävä arvo. 
     */
    function hae_eka_osuma_oliona($taulunimi, $sarakenimi, $hakuarvo){
        $palaute = Tietokantaolio::$HAKU_PALAUTTI_TYHJAN;

        // Haetaan lähettäjän tiedot:
        $osumataulu = $this->hae_osumarivit_olioina(
                $this->tee_WHEREhaku_1($taulunimi, $sarakenimi, $hakuarvo));

        // Taulu on tyhjä, ellei mitään löytynyt.
        if(sizeof($osumataulu) != 0){
            $palaute = $osumataulu[0];
        }
        return $palaute;
    }

    /**
     * Hakee tietokantataulun kaikki rivit ja palauttaa <b>yhdessä</b> isossa 
     * taulukossa kunkin rivin tiedot omana taulukkona, josta tietoja
     * voi hakea sekä sarakkeen nimellä, että sarakeindeksillä.
     * 
     * @param type $taulunimi
     * @return array palauttaa aina taulukon, joka on tyhjä osumien puuttumisen
     * lisäksi jos $hakutulos on arvoltaan false.
     * Muuten palauttaa taulukkona osumatietotaulukot (kussakin rivin tiedot).
     */
    function hae_kaikki_rivit_taulukoina($taulunimi){
        
        $palaute = array();

        if($this->dbtyyppi == 'mysql')
        {
            $hakulause = "SELECT * FROM ".$taulunimi;
            $hakutulos = mysql_query($hakulause); //FALSE on failure
           
            if($hakutulos)
            {
                do{
                    // Seuraavasta tietoa voi hakea sekä sarakenimillä että 
                    // sarakkeen numeroindeksillä. Palauttaa FALSEn, ellei
                    // mitään saada irti:
                    $rivi = mysql_fetch_array($hakutulos, MYSQL_BOTH);
                    if($rivi){
                        array_push($palaute, $rivi);
                    }
                }
                while($rivi);
            }
        }
        return $palaute;
    }
    
    /***************FUNCTION TALLENNA_UUSI_rivi *************************************/

    // Metodi, joka tallentaa uuden olion tiedot MySQL:llään: Palauttaa true, jos
    // tallennus onnistuu.
    /**
     * DEPRECATED!
     * @param <type> $taulu tietokantataulun nimi
     * @param <type> $sarakenimitaulukko Taulukko, joka sisältää sarakenimet.
     * @param <type> $arvotaulukko Taulukko, joka sisältää vastaavat arvot.
     * HUOM! Taulukoissa pitää olla yhtä monta alkiota, muuten tulee palautetta!
     * Muutenkin käyttäjän pitää huolehtia, että sarakenimet vastaavat
     * olemassaolevaa tietokantataulua, ja että arvot ovat samassa järjestyksessä.
     *
     * Lisäksi oletetaan, että taulukon pääindeksi tuotetaan automaattisesti
     * (auto-increment), ellei sitä anneta erikseen.
     *
     * @param string $virheilmoitus mysql_query-lauseen palauttaessa falsen.
     * @return string Palauttaa arvon Tietokantaolio::$HAKU_ONNISTUI,
     * jos tallennus ok, muuten false.
     *
    function tallenna_uusi_rivi($taulu, $sarakenimitaulukko, $arvotaulukko,
                                $virheilmoitus)
    {
        $onnistu = false;
        $sarakeLkm = sizeof($sarakenimitaulukko);
        $arvoLkm = sizeof($arvotaulukko);

        if(!isset($sarakenimitaulukko) || !isset($arvotaulukko) ||
            ($sarakeLkm != $arvoLkm) || ($sarakeLkm == 0))
        {
            // Virheilmoitus kehittäjälle (ei käyttäjälle yl. näy):
            $onnistu = "Virhe tallennuksessa: Sarakenimi- ja arvotaulukot".
            " eivät t&auml;sm&auml;&auml;, ole m&auml;&auml;riteltyj&auml;
                tai ovat tyhji&auml;";
        }
        else
        {
            $saraketeksti = '';
            $arvoteksti = '';
            
            for($i = 0; $i < $sarakeLkm; $i++)
            {
                /* Ajatus: arvojen int-vertailu, jolloin lukujen kohdalla
                 * turhat ja hidastavat hipsut '' voidaan välttää. 
                 * Form-input on kuulemma aina string. Olennaista?
                 *
                if($i == 0) // Pilkkujen viilausta:
                {
                    $saraketeksti = $sarakenimitaulukko[$i];
                    $arvoteksti = "'".$arvotaulukko[$i]."'";
                }
                else
                {
                    $saraketeksti .= ", ".$sarakenimitaulukko[$i];
                    $arvoteksti .= ", '".$arvotaulukko[$i]."'";                 
                }
            }
            // MYSQL:
            if($this->dbtyyppi == 'mysql')
            {
                $kyselylause = "INSERT INTO $taulu ($saraketeksti)
							 VALUE ($arvoteksti)";

                $kyselyn_tila = mysql_query($kyselylause);
                
                if($kyselyn_tila == false){
                    $onnistu = $virheilmoitus.
                                        " (Tietokantaolio.tallenna_uusi_rivi)";
                }

                else if(mysql_affected_rows() == 1)
                {
                    $onnistu = Tietokantaolio::$HAKU_ONNISTUI;
                }
            }
        }
        return $onnistu;
    }
    
    /**
     * Tallentaa Mallipohjaluokasta perityn olion tietokantaan. Tämä on nykyään
     * ensisijainen tapa tallentaa tietokantaan.
     * 
     * @param Tietokantarivi $tietokantarivi Tietokantarivi-luokan olio 
     * @return string Palauttaa arvon Tietokantaolio::$HAKU_ONNISTUI,
     * jos tallennus ok, muuten arvon 
     * Tietokantaolio::$HAKUVIRHE+tietokantakyselyn (tarkoitettu vain testikäyttöön).
     * @author JP (9.5.2013)
     */
    public function tallenna_rivi($tietokantarivi)
    {
        $onnistu = Tietokantaolio::$HAKUVIRHE;
        
        if($tietokantarivi instanceof Tietokantarivi){
            
            $taulu = $tietokantarivi->get_taulunimi();
            
            if($this->dbtyyppi == 'mysql')
            {
                $laskuri = 0;
                $tietokantasolut = $tietokantarivi->get_tietokantasolut();
                
                $sarakenimet = "(";
                $arvot = "(";
                
                // Id-saraketta ei oteta mukaan, koska se on aina automaattinen:
                foreach ($tietokantasolut as $solu) {
                    if($solu instanceof Tietokantasolu && 
                        $solu->tiedot_ok() &&
                        $solu->get_sarakenimi() != Malliluokkapohja::$SARAKENIMI_ID){
                        
                        // Pilkut kohdalleen if-lauseen avulla.
                        if($laskuri > 0){
                            $arvot .= ",";
                            $sarakenimet .= ",";
                        }
                        
                        $sarakenimet .= $solu->get_sarakenimi();

                        // Arvoissa laitetaan hipsut vain ei-lukuihin (liekö
                        // merkitystä?)
                        if($solu->get_arvon_tyyppi() == Tietokantasolu::$luku_int){
                            $arvot .= $solu->get_arvo();
                        }
                        else{
                            $arvot .= "'".$solu->get_arvo()."'";
                        }
                        $laskuri++;
                    }
                }
                
                // Sulut kiinni:
                $sarakenimet .= ")";
                $arvot .= ")";
                
                $insert_koodi ="INSERT INTO ".$taulu." ";
                $value_koodi = " VALUE ";
               
                $kyselylause = $insert_koodi.$sarakenimet.
                                $value_koodi.$arvot;

                $kyselyn_tila = mysql_query($kyselylause);

                if($kyselyn_tila && (mysql_affected_rows() == 1)){
                    $onnistu = Tietokantaolio::$HAKU_ONNISTUI;
                } else{
                    $onnistu = Tietokantaolio::$HAKUVIRHE."<br/>".
                                $kyselylause;
                }
            }
        }
        return $onnistu;
    }
    /*********** Poistaa tietokantataulusta rivin, 
     * jonka tunnukset saadaan parametrina. Korkeintaan yksi rivi poistetaan, tai
     * ei yhtään, ellei löydy.
     * Palauttaa Tietokantaolio::$HAKU_ONNISTUI, jos onnistui ja rivi
     * poistettiin. Jos haku onnistui, muttei löytänyt poistettavaa, palautetaan
     * Tietokantaolio::$HAKU_PALAUTTI_TYHJAN. Jos tapahtuu virhe, palautetaan
     * arvo Tietokantaolio::$HAKUVIRHE;.
     */
    function poista_rivi($taulu, $taulun_sarake, $hakuarvo)
    {
        $palaute = false;
        if($this->dbtyyppi == 'mysql')
        {
            $poistolause = "DELETE FROM $taulu
                            WHERE $taulun_sarake ='$hakuarvo'
                            LIMIT 1";

            $tulos = mysql_query($poistolause);

            if($tulos)
            {
                if(mysql_affected_rows() == 0){
                    $palaute = Tietokantaolio::$HAKU_PALAUTTI_TYHJAN;
                }
                else if(mysql_affected_rows() == 1){
                    $palaute = Tietokantaolio::$HAKU_ONNISTUI;
                }
                else{
                    $palaute = Tietokantaolio::$HAKUVIRHE;
                }
            }
            else
            {
                $palaute = Tietokantaolio::$HAKUVIRHE;
            }
        }
        return $palaute;
    }

    /*********** Poistaa tietokantataulusta KAIKKI rivit,
     * jotka sopivat parametreina annettuihin ehtoihin. Käytä varovasti!
     * Palauttaa luvun, joka ilmoittaa poistettujen lkm:n. Virheen sattuessa
     * palautetaan myös nolla.
     */
    function poista_kaikki_rivit($taulu, $taulun_sarake, $hakuarvo)
    {
        $poistettujen_lkm = 0;

        // Varmistetaan, että hakuarvo on järkevä:
        if((is_string($hakuarvo) || is_numeric($hakuarvo)) && 
                (!empty($hakuarvo)) ){
            
            if($this->dbtyyppi == 'mysql')
            {
                $poistolause = "DELETE FROM $taulu
                                WHERE $taulun_sarake ='$hakuarvo'";

                // Palauttaa FALSE, jos tapahtuu virhe.
                $tulos = mysql_query($poistolause);

                if($tulos)
                {
                    if(mysql_affected_rows() > 0){
                        $poistettujen_lkm = mysql_affected_rows();
                    }
                }
                else
                {
                    $poistettujen_lkm = Tietokantaolio::$HAKUVIRHE;
                }
            }
            
        }
        
        
        return $poistettujen_lkm;
    }
    /******************FUNCTION TARKISTA_SYÖTTEET ***************************/

    /** Tarkistaa syätteet palvelimen päässä: tähän voisi yhdistää toiminnon
    * poista_rumat_sanat
    * Trim-funktio poistaa välierottimet, mikä voi joskus olla hyvä.
    * Muuten tarkistetaan, ettei syätteissä
    * ole merkkejä, joilla on erityismerkitys MySQL:ssä tai HTML:ssä.
    * Palauttaa puhdistetun merkkijonon.
    *
    function tarkista_syote($syote)
    {
        $syote = mysql_real_escape_string(htmlspecialchars(trim($syote)));
        return $syote;
    }*/
    
    //==========================================================================
    //==========================================================================
    /**=========================== Vanhentuneita, mutta vielä tarpeen, ennen
     * kuin tehtävät, ratkaisut ja kuvat on muutettu entiteettiolioiksi:
     * 
     */
    /***************FUNCTION TALLENNA_UUSI_rivi *************************************/

    // Metodi, joka tallentaa uuden olion tiedot MySQL:llään: Palauttaa true, jos
    // tallennus onnistuu.
    /**
     *
     * @param <type> $taulu tietokantataulun nimi
     * @param <type> $sarakenimitaulukko Taulukko, joka sisältää sarakenimet.
     * @param <type> $arvotaulukko Taulukko, joka sisältää vastaavat arvot.
     * HUOM! Taulukoissa pitää olla yhtä monta alkiota, muuten tulee palautetta!
     * Muutenkin käyttäjän pitää huolehtia, että sarakenimet vastaavat
     * olemassaolevaa tietokantataulua, ja että arvot ovat samassa järjestyksessä.
     *
     * Lisäksi oletetaan, että taulukon pääindeksi tuotetaan automaattisesti
     * (auto-increment), ellei sitä anneta erikseen.
     *
     * @param string $virheilmoitus Ulkoapäin muotoiltava virheilmoitus, jos
     * mysql_query-lause palauttaa falsen. Lisäksi palautetaan kyselylause, joka
     * helpottaa virhekohdan selvittämistä. Liekö vähän riskialtis, mutta
     * tallentajien määrä on ainakin tässä vaiheessa rajallinen. 
     * @return string Palauttaa arvon Tietokantaolio::$HAKU_ONNISTUI,
     * jos tallennus ok, muuten false.
     */
    function tallenna_uusi_rivi($taulu, $sarakenimitaulukko, $arvotaulukko,
                                $virheilmoitus)
    {
        $onnistu = false;
        $sarakeLkm = sizeof($sarakenimitaulukko);
        $arvoLkm = sizeof($arvotaulukko);

        if(!isset($sarakenimitaulukko) || !isset($arvotaulukko) ||
            ($sarakeLkm != $arvoLkm) || ($sarakeLkm == 0))
        {
            // Virheilmoitus kehittäjälle (ei käyttäjälle yl. näy):
            $onnistu = "Virhe tallennuksessa: Sarakenimi- ja arvotaulukot".
            " eivät t&auml;sm&auml;&auml;, ole m&auml;&auml;riteltyj&auml;
                tai ovat tyhji&auml;";
        }
        else
        {
            $saraketeksti = '';
            $arvoteksti = '';
            
            for($i = 0; $i < $sarakeLkm; $i++)
            {
                /* Ajatus: arvojen int-vertailu, jolloin lukujen kohdalla
                 * turhat ja hidastavat hipsut '' voidaan välttää. 
                 * Form-input on kuulemma aina string. Olennaista?
                 */
                if($i == 0) // Pilkkujen viilausta:
                {
                    $saraketeksti = $sarakenimitaulukko[$i];
                    $arvoteksti = "'".$arvotaulukko[$i]."'";
                }
                else
                {
                    $saraketeksti .= ", ".$sarakenimitaulukko[$i];
                    $arvoteksti .= ", '".$arvotaulukko[$i]."'";                 
                }
            }
            // MYSQL:
            if($this->dbtyyppi == 'mysql')
            {
                $kyselylause = "INSERT INTO $taulu ($saraketeksti)
							 VALUE ($arvoteksti)";

                $kyselyn_tila = mysql_query($kyselylause);
                
                if($kyselyn_tila == false){
                    $onnistu = $virheilmoitus.
                                        " (Tietokantaolio.tallenna_uusi_rivi)".
                                        "<br/> Kysely: <br/>".$kyselylause;
                }

                else if(mysql_affected_rows() == 1)
                {
                    $onnistu = Tietokantaolio::$HAKU_ONNISTUI;
                }
            }
        }
        return $onnistu;
    }
}
?>
