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
    private $result; // Result of a query

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

    public function get_yhteys(){
        return $this->yhteys;
    }
    public function get_result(){
        return $this->result;
    }
    
    /**
     * Yhdistää tietokantaan.
     * @param <type> $dbnimi Tietokannan nimi, johon yhdistetään.
     *
     * Huom. Yksi olio voisi hallita useita eri yhteyksiä, jos yhteys-
     * muuttuja olisi esim vektori.
     **/
    function yhdista_tietokantaan($dbnimi)
    {
        if ($this->dbtyyppi === "mysql")
        {
            $this->yhteys = new mysqli($this->dbhost,
                                        $this->dbuser,
                                        $this->dbsalis,
                                        $dbnimi);
            //$this->yhteys->set_charset("utf8");
            if($this->yhteys->connect_errno){
                echo "Tietokannan valinta ep&auml;onnistui: " .
                    $this->yhteys->connect_errno;
            }
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
            mysqli_close($this->yhteys)
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

                $this->result = $this->yhteys->query($hakulause);
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

                $this->result = $this->yhteys->query($hakulause);  //FALSE on failure, true muutoin.

                if($this->result){
                    $tulos = Tietokantaolio::$HAKU_ONNISTUI;
                    //$this->lisaa_kommentti("<br/>".$hakulause."<br/>");
                } else{
                    //$this->lisaa_virheilmoitus("<br/>".$hakulause."<br/>");
                    $tulos = false;
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
        if($this->dbtyyppi === 'mysql')
        {
            $hakulause = "SELECT * FROM $taulu
                            WHERE $taulun_sarake='$hakuarvo'";
            $this->result = $this->yhteys->query($hakulause); //FALSE on failure
            return $this->result;
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
            $this->result = $this->yhteys->query($hakulause); //FALSE on failure
            if($this->result != false){
                $palaute = $this->hae_osumarivit_olioina($this->result);
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
            $this->result = $this->yhteys->query($hakulause); //FALSE on failure
            if($this->result != false){
                $palaute = $this->hae_osumarivit_taulukoina($this->result);
            }
        }

        return $palaute;
    }


    /**
     * Escapes the string used in an SQL-query. Takes into account the
     * charset uset in the connection.
     * @param type $string

     * @return the escaped string

     */
    function real_escape_string($string){
        return $this->yhteys->real_escape_string($string);
    }
    
    /**
     * @param <type> $hakutulos function "mysqli_query()" palautusarvosta
     * @return int palauttaa aina luvun, joka on nolla myös, jos $hakutulos
     * ei ole määritelty tai on arvoltaan false. Muuten palauttaa osumarivien
     * lukumäärän.
     * Tämä toimii myös muokkausten tai poistojen yhteydessä.
     */
    function get_number_of_affected_rows()
    {
        $palaute = 0;
        if($this->dbtyyppi == "mysql")
        {
            if (isset($this->result) && ($this->result != false))
            {
                $palaute = $this->yhteys->affected_rows;
            }
        }
        return $palaute;
    }
    
    /**
     * Hakee lisätyn rivin id-arvon.
     */
    function get_insert_id()
    {
        $palaute = 0;
        if($this->dbtyyppi == "mysql")
        {
            if (isset($this->yhteys))
            {
                $palaute = $this->yhteys->insert_id;
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
        if($this->dbtyyppi === 'mysql')
        {
            $hakulause = "SELECT id
                            FROM $taulu
                            WHERE $taulun_sarake='$hakuarvo'";
            $hakutulos = $this->yhteys->query($hakulause); //FALSE on failure

            if (isset($hakutulos) && ($hakutulos != false))
            {
                $palaute = $hakutulos->num_rows;
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
        if($this->dbtyyppi === "mysql")
        {
            if(isset($hakutulos) && $hakutulos != false)
            {
                // fetch palauttaa lopuksi falsen.
                while ($rivi = $hakutulos->fetch_object())
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
        if($this->dbtyyppi == 'mysql'){
            if(isset($hakutulos) && $hakutulos != false){
                
                // fetch palauttaa lopuksi falsen.
                while (($rivi = $hakutulos->fetch_array(MYSQLI_BOTH)) != false)
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

        $this->result = $hakutulos;
        
        if($this->dbtyyppi == 'mysql')
        {
            
            // Seuraavasta tietoa voi hakea sekä sarakenimillä että 
            // sarakkeen numeroindeksillä. Palauttaa FALSEn, ellei
            // mitään saada irti:
            $rivi = $hakutulos->fetch_array(MYSQLI_BOTH);
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
            $hakutulos = $this->yhteys->query($hakulause);  //FALSE on failure
            $this->result = $hakutulos;
           
            if($hakutulos)
            {
                do{
                    // Seuraavasta tietoa voi hakea sekä sarakenimillä että 
                    // sarakkeen numeroindeksillä. Palauttaa FALSEn, ellei
                    // mitään saada irti:
                    $rivi = $hakutulos->fetch_array(MYSQLI_BOTH);
                    if($rivi){
                        array_push($palaute, $rivi);
                    }
                }
                while($rivi);
            }
        }
        return $palaute;
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
                $value_koodi = " VALUES ";
               
                $kyselylause = $insert_koodi.$sarakenimet.
                                $value_koodi.$arvot;

                $this->result = $this->yhteys->query($kyselylause);

                if($this->result && ($this->get_number_of_affected_rows() === 1)){
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

            $this->result = $this->yhteys->query($poistolause);

            if($this->result)
            {
                if($this->get_number_of_affected_rows() === 0){
                    $palaute = Tietokantaolio::$HAKU_PALAUTTI_TYHJAN;
                }
                else if($this->get_number_of_affected_rows() === 1){
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
                $tulos = $this->yhteys->query($poistolause);
                $this->result = $tulos;

                if($tulos)
                {
                    if($this->get_number_of_affected_rows() > 0){
                        $poistettujen_lkm = 
                            $this->get_number_of_affected_rows();
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

                $this->result = $this->yhteys->query($kyselylause);
                
                if($this->result === false){
                    $onnistu = $virheilmoitus.
                                        " (Tietokantaolio.tallenna_uusi_rivi)".
                                        "<br/> Kysely: <br/>".$kyselylause;
                }

                else if($this->get_number_of_affected_rows() === 1)
                {
                    $onnistu = Tietokantaolio::$HAKU_ONNISTUI;
                }
            }
        }
        return $onnistu;
    }
}
?>