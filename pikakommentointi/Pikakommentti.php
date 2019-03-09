<?php

/**
 * Description of Pikakommentti
 * Käärii sisäänsä tietokannan pikakommentit-taulun yhden rivin ja huolehtii
 * vuorovaikutuksesta tietokannan kanssa (uuden tallennus, muokkaus ja poisto).
 *
 create table pikakommentit
(
  id                    int auto_increment not null,
  henkilo_id		int not null,
  tallennushetki_sek    int not null,
  muokkaushetki_sek     int default -1,
  kohde_id              int not null,
  kohde_tyyppi          smallint not null,
  kommentti		varchar(1000) not null,
  primary key (id),
  index(henkilo_id),
  index(tallennushetki_sek),
  index(kohde_id),
  index(kohde_tyyppi),
  FOREIGN KEY (henkilo_id) REFERENCES henkilot (id) ON DELETE CASCADE
) ENGINE=InnoDB; 
 * @author J-P
 */
class Pikakommentti extends Malliluokkapohja{

    // Kommentoinnin kohteet: 
    public static $KOHDE_BONGAUS = 1;
    public static $KOHDE_KUVA_BONGAUS = 2;

    // Tietokannan sarake- ja muut nimet:
    public static $SARAKENIMI_HENKILO_ID= "henkilo_id";
    public static $SARAKENIMI_TALLENNUSHETKI= "tallennushetki_sek";
    public static $SARAKENIMI_MUOKKAUSHETKI= "muokkaushetki_sek";
    public static $SARAKENIMI_KOHDE_ID= "kohde_id";
    public static $SARAKENIMI_KOHDE_TYYPPI= "kohde_tyyppi";
    public static $SARAKENIMI_KOMMENTTI= "kommentti";


    public static $TALLENNUSPAINIKKEEN_ID = "tallennuspainike";

    public static $taulunimi = "pikakommentit";
    
    /**
     * Konstruktorin "overloading" eli eri konstruktorit eri parametreille
     * ei ole tuettu PHP:ssä. Kierrän tämän antamalla parametreille, joita
     * ei käytetä, vakioarvon, joka tarkoittaa, ettei parametri käytössä.
     *
     * @param Tietokantaolio $tietokantaolio
     * @param <type> $tk_pikakommenttiolio Tietokantahausta saatava yksi rivi
     * oliona.
     */
    function __construct($id, $tietokantaolio) {
        
        $tietokantasolut = 
            array(new Tietokantasolu(Pikakommentti::$SARAKENIMI_ID, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Pikakommentti::$SARAKENIMI_HENKILO_ID, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Pikakommentti::$SARAKENIMI_KOHDE_ID, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Pikakommentti::$SARAKENIMI_KOHDE_TYYPPI, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Pikakommentti::$SARAKENIMI_KOMMENTTI, Tietokantasolu::$mj_tyhja_EI_ok,$tietokantaolio),
                new Tietokantasolu(Pikakommentti::$SARAKENIMI_MUOKKAUSHETKI, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Pikakommentti::$SARAKENIMI_TALLENNUSHETKI, Tietokantasolu::$luku_int,$tietokantaolio));
        
        $taulunimi = Pikakommentti::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
        
   
    }   
    // Getterit ja setterit (nääkin kyllä pystynee dynaamisesti tekemään):
    // Id:tä ei aseteta täältä käsin, vaan tietokanta luo sen automaattisesti.
    public function get_henkilo_id(){
        return $this->get_arvo(Pikakommentti::$SARAKENIMI_HENKILO_ID);
    }
    public function set_henkilo_id($uusi){
        $this->set_arvo($uusi, Pikakommentti::$SARAKENIMI_HENKILO_ID);
    }
    public function get_tallennushetki_sek(){
        return $this->get_arvo(Pikakommentti::$SARAKENIMI_TALLENNUSHETKI);
    }
    public function set_tallennushetki_sek($uusi){
        $this->set_arvo($uusi, Pikakommentti::$SARAKENIMI_TALLENNUSHETKI);
    }
    public function get_muokkaushetki_sek(){
        return $this->get_arvo(Pikakommentti::$SARAKENIMI_MUOKKAUSHETKI);
    }
    public function set_muokkaushetki_sek($uusi){
        $this->set_arvo($uusi, Pikakommentti::$SARAKENIMI_MUOKKAUSHETKI);
    }
    public function get_kohde_id(){
        return $this->get_arvo(Pikakommentti::$SARAKENIMI_KOHDE_ID);
    }
    public function set_kohde_id($uusi){
        $this->set_arvo($uusi, Pikakommentti::$SARAKENIMI_KOHDE_ID);
    }
    public function get_kohde_tyyppi(){
        return $this->get_arvo(Pikakommentti::$SARAKENIMI_KOHDE_TYYPPI);
    }
    public function set_kohde_tyyppi($uusi){
        $this->set_arvo($uusi, Pikakommentti::$SARAKENIMI_KOHDE_TYYPPI);
    }
    // Poistetaan mahdolliset vinoviivat:
    public function get_kommentti(){
        return $this->get_arvo(Pikakommentti::$SARAKENIMI_KOMMENTTI);
    }
    public function set_kommentti($uusi){
        // Tämä tulee käyttäjältä, joten tarkistus päälle!
        $this->set_arvo($uusi, Pikakommentti::$SARAKENIMI_KOMMENTTI);
    }

    
    /**
     * Palauttaa pikakommentin html-koodin.
     */
    public function nayta_pikakommentti($omaid, $kayttajan_valtuudet){

        $html="";

        if($this->olio_loytyi_tietokannasta){
            $aika = Aika::anna_pvm_ja_aika($this->get_tallennushetki_sek());

            
            $lahettaja = new Henkilo($this->get_henkilo_id(),
                                      $this->tietokantaolio);

            $sisalto = $this->get_kommentti();

            // Luodaan muokkaus/tuhouspainikkeet, jos kysymyksessä
            // oma tai kuningas:
            if(($omaid == $this->get_henkilo_id()) ||
                ($kayttajan_valtuudet== Valtuudet::$HALLINTA)){
                
                $painikkeet =
                "<button type='button' onclick=".
                  "'pk_muokkaa(\"".$this->get_kommentti()."\",".
                                $this->get_kohde_tyyppi().",".
                                $this->get_kohde_id().",".
                                $this->get_id().",\"".
                                Pikakommenttikontrolleri::$name_kohdetyyppi."\",\"".
                                Pikakommenttikontrolleri::$name_kohde_id."\",\"".
                                Pikakommenttikontrolleri::$name_kommentti."\",\"".
                                Pikakommenttikontrolleri::$name_id."\")'".
                "title='".
                Pikakommenttitekstit::$muokkaa_pikakommentti_title."'>".
                Pikakommenttitekstit::$muokkaa_pikakommentti_value.
                "</button>".
                "<button type='button' onclick=".
                "'esita_pikakommentin_poistovarmistus(".
                        $this->get_id().",\"".
                        Pikakommenttikontrolleri::$name_id."\"".
                        ")'title='".
                Pikakommenttitekstit::$poista_pikakommentti_title."'>".
                Pikakommenttitekstit::$poista_pikakommentti_value.
                "</button>";
            }
            else{
                $painikkeet = "";
            }


            $html= Pikakommenttinakymat::nayta_pikakommentti(
                                    $aika,
                                    $lahettaja->get_arvo(Henkilo::$sarakenimi_etunimi),
                                    $sisalto,
                                    $painikkeet,
                                    $this->get_id());
        }
        else{
             $html=Pikakommenttitekstit::$ilmoitus_pikakommentteja_ei_loytynyt;
        }
        return $html;
    }
    
    /**
     * Palauttaa poistovahvistus-html:n, joka sisältää pikakommentin tiedot
     * ja poiston vahvistus- ja perumispainikkeet.
     * 
     * @return type
     */
    public function nayta_poistovahvistuskysely(){

        $html= "";

        //
        if($this->olio_loytyi_tietokannasta){
            $aika = Aika::anna_pvm_ja_aika($this->get_tallennushetki_sek());

            $lahettaja = new Henkilo($this->get_henkilo_id(),
                                      $this->tietokantaolio);

            $sisalto = $this->get_kommentti();

            $vie_elem_id = "pk".$this->get_id();
            $tuo_elem_id = Pikakommenttikontrolleri::$piilovaraston_id;
            
            // Luodaan vahvistus- ja perumispainikkeet
            $painikkeet =
            "<button type='button' onclick=".
            "'peru_poisto(\"".$tuo_elem_id."\",\"".$vie_elem_id."\")'title='".
                Pikakommenttitekstit::$peruminen_pikakommentti_title."'>".
                Pikakommenttitekstit::$peru_poisto_pikakommentti_value.
            "</button>".
            "<button type='button' onclick=".
              "'pk_poista(".$this->get_id().",".
                            $this->get_kohde_id().",\"".
                            Pikakommenttikontrolleri::$name_id."\",\"".
                            Pikakommenttikontrolleri::$name_kohde_id."\")'".
            "title='".
                Pikakommenttitekstit::$poistovahvistus_pikakommentti_title."'>".
                Pikakommenttitekstit::$poistovahvistus_pikakommentti_value.
            "</button>";

            $html= Pikakommenttinakymat::nayta_poistovahvistus(
                                    $aika,
                                    $lahettaja->get_arvo(Henkilo::$sarakenimi_etunimi),
                                    $sisalto,
                                    $painikkeet,
                                    $this->get_id());
        }
        else{
            $html = Pikakommenttitekstit::$virheilmoitus_ei_tallennuskelpoinen;
        }
        return $html;
    }
    /**
     * Staattinen metodi, jonka avulla voidaan hakea kaikki tiettyyn
     * kohteeseen (parametrina annetaan kohdetyyppi ja -id) liittyvät
     * pikakommentit ja palautetaan ne Pikakommentti-luokan olioina taulukossa.
     * 
     * Palauttaa taulukon myös, vaikka mitään ei löydy. Tällöin taulukko on tyhjä.
     * 
     * Jonkin verran raskas operaatio, koska yleisen haun jälkeen jokaisen
     * osuman kohdalla suoritetaan erillinen haku (Pikakommentti-olion luonti).
     * Voisi ehkä rajoittaa lukumäärää, jos tarvis.
     * 
     * @param type $kohde_tyyppi
     * @param type $kohde_id
     * @param Tietokantaolio $tietokantaolio
     */
    public static function hae_pikakommentit($kohde_tyyppi, 
                                            $kohde_id, 
                                            $tietokantaolio){
        $palautustaulukko = array();
        
        // Haetaan pikakommenttien lkm ja tarkastetaan samalla, onko uusia.
        $hakulause = "SELECT id
                    FROM ".Pikakommentti::$taulunimi."
                    WHERE ".Pikakommentti::$SARAKENIMI_KOHDE_TYYPPI."=".$kohde_tyyppi.
                    " AND ".Pikakommentti::$SARAKENIMI_KOHDE_ID."=".$kohde_id.
                    " ORDER BY ".Pikakommentti::$SARAKENIMI_TALLENNUSHETKI;

        $osumat =
            $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
        
        foreach ($osumat as $tk_pk) {
            $pk = new Pikakommentti($tk_pk->id,$tietokantaolio);
            
            // Jos kaikki kunnossa, lisätään taulukkoon:
            if($pk->olio_loytyi_tietokannasta){
                array_push($palautustaulukko, $pk);
            }
        }
        return $palautustaulukko;
    }
    
    /**
     * Staattinen metodi, jonka avulla voidaan hakea kaikki tiettyyn
     * kohteeseen (parametrina annetaan kohdetyyppi ja -id) liittyvät
     * pikakommentit ja palautetaan ne Pikakommentti-luokan olioina taulukossa.
     * 
     * Lisäehtona on, että pikakommentin kirjoittaja kuuluu nimettyyn poppooseen,
     * eli käyttäjäryhmään. Tässä oletetaan, että käyttäjät kuuluvat aina 
     * tiettyyn ryhmään, jolla on oma id-tunnus. Tämän metodin käyttö muissa
     * sovelluksissa saattaa vaatia muokkausta tarkemman rakennevaatimuksen takia.
     * Lisäksi tämä on raskaampi haku kuin pelkkien pikakommenttien hakeminen,
     * koska vaatii liitokset Poppoo- ja Henkilo-tauluihin.
     * 
     * Palauttaa taulukon myös, vaikka mitään ei löydy. Tällöin taulukko on tyhjä.
     * 
     * Jonkin verran raskas operaatio, koska yleisen haun jälkeen jokaisen
     * osuman kohdalla suoritetaan erillinen haku (Pikakommentti-olion luonti).
     * Voisi ehkä rajoittaa lukumäärää, jos tarvis.
     * 
     * @param type $kohde_tyyppi
     * @param type $kohde_id
     * @param Tietokantaolio $tietokantaolio
     */
    public static function hae_pikakommentit_poppoorajoituksella(
                                                    $kohde_tyyppi, 
                                                    $kohde_id, 
                                                    $poppoo_id,
                                                    $tietokantaolio){
        $palautustaulukko = array();
        
        // Haetaan pikakommenttien lkm ja tarkastetaan samalla, onko uusia.
        $hakulause = "SELECT ".Pikakommentti::$taulunimi.".".
                                Pikakommentti::$SARAKENIMI_ID.
                    " FROM ".Pikakommentti::$taulunimi.
                    " JOIN ".Henkilo::$taulunimi.
                    " ON ".Pikakommentti::$taulunimi.".".
                            Pikakommentti::$SARAKENIMI_HENKILO_ID.
                            "=".Henkilo::$taulunimi.".".  Henkilo::$SARAKENIMI_ID.
                    " WHERE ".Pikakommentti::$taulunimi.".".
                            Pikakommentti::$SARAKENIMI_KOHDE_TYYPPI."=".$kohde_tyyppi.
                    " AND ".Pikakommentti::$taulunimi.".".
                            Pikakommentti::$SARAKENIMI_KOHDE_ID."=".$kohde_id.
                    " AND ".Henkilo::$taulunimi.".".Henkilo::$sarakenimi_poppoo_id.
                            "=".$poppoo_id.
                    " ORDER BY ".Pikakommentti::$taulunimi.".".
                            Pikakommentti::$SARAKENIMI_TALLENNUSHETKI;

        $osumat =
            $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
        
        foreach ($osumat as $tk_pk) {
            $pk = new Pikakommentti($tk_pk->id,$tietokantaolio);
            
            // Jos kaikki kunnossa, lisätään taulukkoon:
            if($pk->olio_loytyi_tietokannasta){
                array_push($palautustaulukko, $pk);
            }
        }
        return $palautustaulukko;
    }
}
?>
