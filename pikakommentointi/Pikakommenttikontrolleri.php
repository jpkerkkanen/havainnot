<?php
/**
 * Description of Kontrolleri_pikakommentit
 * Sisältää metodit, jotka toteuttavat (delegoivat) nettisivulle tulevat pyynnöt,
 * esimerkiksi liittyen kommenttien näyttöön, tallennukseen, muokkaukseen ja
 * poistoon (CRUD). Luomisen, muokkauksen ja poiston tarkka toteutus on
 * kuitenkin Pikakommentti-luokassa. Täällä toteutetaan näytettävien etsinnät
 * ja toimintojen paketointi.
 *
 * Tyypillisesti toiminnan paketoiva metodin nimi alkaa sanalla 'toteuta' ja
 * se palauttaa Palaute-luokan olion.
 *
 * Tietokantaoperaatiot
 * @author J-P
 */
class Pikakommenttikontrolleri extends Kontrolleripohja{

    //put your code here
    // Seuraavaa ei taidet tarvita, koska kaikki toiminnot ajaxin kautta:
    public static $TOIMINTANIMI_PIKAKOMMENTIT = "Pikakommenttitoiminta";
    
    // Pikakommenttimuuttujien name-arvot (esim. post-haku):
    public static $name_kommentti = "pk_kommenttiteksti";
    public static $name_kohdetyyppi = "pk_kohdetyyppi";
    public static $name_kohde_id = "pk_kohde_id";
    public static $name_id = "pk_id";
    
    // Tänne voidaan piilottaa tietoa. Tarvitaan esim poiston perumisessa.
    public static $piilovaraston_id = "piilovarasto";
    
    public static $kommenttien_max_nayttolkm = 1000;

    /**
     * @var Tietokantaolio 
     */
    private $tietokantaolio;
    /**
     * @var Palaute
     */
    private $palauteolio;
    /**
     * @var Parametrit
     */
    private $parametriolio;

    /**
     * @var Pikakommentti
     */
    private $nyk_pikakommentti;


    /**
     * Luokan rakentaja:
     * @param <type> $tietokantaolio
     * @param <type> $parametriolio
     * @param <type> $PK Käsiteltävä pikakommenttiolio
     */
    public function __construct($tietokantaolio, $parametriolio, $PK) {
        $this->tietokantaolio = $tietokantaolio;
        $this->parametriolio = $parametriolio;
        $this->palauteolio = new Palaute();
        $this->nyk_pikakommentti = $PK;
    }
    /**
     * Toteuttaa nimenmukaisen toiminnon. Palauttaa palauteolion, mutta
     * huomaa, että se ei sisällä kokonaista html-sivua, vaan vain
     * pikakommenttilaatikon (div).
     * 
     * HUOM: max_lkm ei käytössä!
     * 
     * Näyttää vain nykyisen poppoon jäsenten kirjoittamat pikakommentit!
     *
     * @param <type> $max_lkm
     * @param <type> $kohde_id
     * @param <type> $max_lkm
     * @param \Palaute $palauteolio
     *
     */
    public function toteuta_nayta_pikakommentit($kohde_tyyppi, 
                                                $kohde_id, 
                                                $max_lkm, 
                                                &$palauteolio){

        $html = ""; // Palautettava.

        
        $poppoo_id = $this->parametriolio->poppoon_id;
        $pikakommentit = Pikakommentti::hae_pikakommentit_poppoorajoituksella(
                                    $kohde_tyyppi, 
                                    $kohde_id, 
                                    $poppoo_id, 
                                    $this->tietokantaolio);
        
        $kayttaja = new Henkilo($this->parametriolio->get_omaid(), 
                                $this->tietokantaolio);
        
        if(!empty ($pikakommentit)){
            foreach ($pikakommentit as $pk) {
                $html .= $pk->nayta_pikakommentti($this->parametriolio->get_omaid(),
                                $kayttaja->hae_valtuudet());
            }
        }
        else{   // Ellei kommentteja löytynyt:
            $html .= Pikakommenttitekstit::$ilmoitus_pikakommentteja_ei_loytynyt;
        }
        
        // Lisätään vielä tallennuspainike ja tekstinsyöttökenttä. Huomaa alla
        // piti olla tuplaheittomerkit, jotta yleensä alkoi metodikutsu toimimaan.
        $tallennuspainike =
            "<button type='button' id=".Pikakommentti::$TALLENNUSPAINIKKEEN_ID.
            " onclick=".
            "'tallenna_uusi_pikakommentti(".
                                $kohde_tyyppi.",".
                                $kohde_id.",\"".
                                Pikakommenttikontrolleri::$name_kohdetyyppi."\",\"".
                                Pikakommenttikontrolleri::$name_kohde_id."\",\"".
                                Pikakommenttikontrolleri::$name_kommentti."\")'".
            "title='".
            Pikakommenttitekstit::$tallenna_uusi_pikakommentti_title."'>".
            Pikakommenttitekstit::$tallenna_uusi_pikakommentti_value.
            "</button>";

        $html .= Pikakommenttinakymat::nayta_pikakommenttilomake($tallennuspainike);


        // Paketoidaan yksittäiset yhteen taulukkoon. Tarvitaan myös
        // painike poistumiseen.
        $painikkeet =
                    "<button type='button' onclick=".
                    "'piilota_pikakommentit()'title='".
                    Pikakommenttitekstit::$pikakommentit_sulje_title."'>".
                    Pikakommenttitekstit::$pikakommentit_sulje_value.
                    "</button>";
        $html = Pikakommenttinakymat::nayta_pikakommentit($html, $painikkeet);

        $palauteolio->set_sisalto($html);

    }

    
    /**
     * Toteuttaa nimenmukaisen toiminnon.
     * @param \Palaute $palauteolio
     */
    public function toteuta_tallenna_muokkaus(&$palauteolio){
        $this->nyk_pikakommentti->set_muokkaushetki_sek(time());
        
        $palaute = $this->nyk_pikakommentti->tallenna_muutokset();
        
        if($palaute == Pikakommentti::$OPERAATIO_ONNISTUI)
            $palaute = 
                Pikakommenttitekstit::$ilmoitus_pikakommentin_muokkaustallennus_ok;
        else{
            $palaute = Pikakommenttitekstit::
                        $virheilmoitus_pikakommentin_muokkaustallennus_eiok.": ".
                        $this->nyk_pikakommentti->tulosta_virheilmoitukset();
        }

        $palauteolio->set_ilmoitus($palaute);
    }
   

    /**
     * Tämä metodi poistaa isäntäolion poiston yhteydessä isäntäolioon
     * linkitetyt pikakommentit ja palauttaa poistettujen olioiden lukumäärän.
     * @param Tietokantaolio $tietokantaolio (täällä, koska static!)
     * @param <type> $kohde_tyyppi Sen olion tyyppi, johon pikakommentti on liitetty.
     * @param <type> $kohde_id Sen olion id, johon pikakommentti on liitetty.
     */
    public static function poista_pikakommentit($tietokantaolio,
                                                $kohde_tyyppi,$kohde_id){
        $poistettujen_lkm = 0;

        // Haetaan ensin kyseiset rivit ja poistetaan ne sitten
        $hakulause = "SELECT id
                        FROM ".Pikakommentti::$taulunimi."
                        WHERE ".Pikakommentti::$SARAKENIMI_KOHDE_ID."=".
                                $kohde_id.
                        " AND ".Pikakommentti::$SARAKENIMI_KOHDE_TYYPPI.
                                "=".$kohde_tyyppi;
        $osumat =
            $tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);

        foreach ($osumat as $pk_olio) {
            
            $taulu = Pikakommentti::$taulunimi;
            $taulun_sarake = "id";
            $hakuarvo = $pk_olio->id;
            $palaute =
                $tietokantaolio->poista_rivi($taulu, $taulun_sarake, $hakuarvo);

            if($palaute == Tietokantaolio::$HAKU_ONNISTUI){
                $poistettujen_lkm++;
            }
        }

        return $poistettujen_lkm;
    }

    /**
     * Palauttaa muotoillun vastauksen uusien pikakommenttien lukumäärästä.
     *
     * @param <type> $kohde
     * @param <type> $omaid
     * @param Tietokantaolio $tietokantaolio
     * @return string
     */
    public static function hae_uusien_pikakomm_lkm($kohde,
                                                    $omaid,
                                                    $tietokantaolio){

        $henkilo = new Henkilo($omaid, $tietokantaolio);
        
        $vikaulosaika = $henkilo->hae_vika_ulosaika();

        $hakulause = "SELECT id FROM pikakommentit
                    WHERE (tallennushetki_sek > $vikaulosaika
                    AND ".Pikakommentti::$SARAKENIMI_KOHDE_TYYPPI." = ".$kohde.
                    " AND henkilo_id <> ".$omaid.")";

        $hakutulos =
            $tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);

        $lkm = sizeof($hakutulos);
        $palaute = "";
        if($lkm > 0){
            $title = "Uusien pikakommenttien lkm (edellisen ".
                    "uloskirjautumisen j&auml;lkeen tulleet)";
            $palaute = "<span title='".$title."'>(".$lkm."*)</span>";
        }
        return $palaute;
    }
    /**
     * EI TOTEUTETTU! Katso sen sijaan toteuta_nayta_pikakommentit().
     * @param type $palauteolio
     */
    public function toteuta_nayta(&$palauteolio) {
        
    }
    /**
     * @param Palaute $palauteolio
     */
    public function toteuta_nayta_poistovarmistus(&$palauteolio) {
        //$pk = $this->nyk_pikakommentti; // Tässä id=-2! Miksi ihmeessä?
        $perumiskoodi = "";
        $pk = new Pikakommentti($this->parametriolio->get_pk_id(), 
                                $this->tietokantaolio);
        if($pk->olio_loytyi_tietokannasta){
            $html = $pk->nayta_poistovahvistuskysely(
                                    $this->parametriolio->get_omaid());
            
            // Tämä perumista varten (ENT_QUOTES->muuttaa sekä yksöis- että
            // kaksoislainausmerkit). Eka parametri varmistaa, että painikkeet
            // tulevat näkyviin perumisen jälkeenkin. Sikälihän poistamispainikekin
            // näkyy vain, jos käyttäjällä on oikeus poistoon.
            $perumiskoodi =
                $sisalto_html = $pk->nayta_pikakommentti(
                                            $this->parametriolio->get_omaid(), 
                                            Valtuudet::$NORMAALI);
            
        } else if($pk instanceof Pikakommentti){
            $html = Pikakommenttitekstit::
                    $virheilmoitus_pikakommenttia_ei_loytynyt_poistettavaksi.
                    " Pk_id=".$pk->get_id();
        } else {
            $html = Pikakommenttitekstit::
                    $virheilmoitus_pikakommentti_nyk_pikakommentti_ei_maaritelty;
        }
        
        // Huom! Alla lainaan oikean palkin paikkaa peruutustiedon varastoksi.
        $palauteolio->set_oikea_palkki($perumiskoodi);
        $palauteolio->set_sisalto($html);
    }

    public function toteuta_poista(&$palauteolio) {
        
        // Jostakin ihmeen syystä nykyinen_pk pitää erikseen asettaa. Miksiköhän??
        $this->set_nykyinen_pk(
                new Pikakommentti($this->parametriolio->get_pk_id(), 
                                $this->tietokantaolio));
        $palaute = $this->nyk_pikakommentti->poista();

        if($palaute == Pikakommentti::$OPERAATIO_ONNISTUI){
            $ilmoitus = Pikakommenttitekstit::$ilmoitus_pikakommentin_poisto_ok;
        }
        else{
            $ilmoitus = Pikakommenttitekstit::$virheilmoitus_pikakommentin_poisto_eiok;
            $ilmoitus .= $this->nyk_pikakommentti->tulosta_virheilmoitukset();
        }

        $palauteolio->set_ilmoitus($ilmoitus);
    }

    /**
     * Toteuttaa nimenmukaisen toiminnon. Palauttaa ilmoituksen
     * tallennuksen onnistumisesta, tai virheilmoituksen.
     * @param \Palaute $palauteolio
     */
    public function toteuta_tallenna_uusi(&$palauteolio) {
        $this->nyk_pikakommentti->set_tallennushetki_sek(time());
        $this->nyk_pikakommentti->set_muokkaushetki_sek(0);
        
        $palaute = $this->nyk_pikakommentti->tallenna_uusi();
        
        if($palaute === Pikakommentti::$OPERAATIO_ONNISTUI){
            $palaute = Pikakommenttitekstit::
                        $ilmoitus_uuden_pikakommentin_tallennus_ok;
        } else{
            $palaute = Pikakommenttitekstit::
                        $virheilmoitus_pikakommentin_tallennus_eiok.": ".
                        $this->nyk_pikakommentti->tulosta_virheilmoitukset();
        }
        
        $palauteolio->set_ilmoitus($palaute);
    }
    
    public function get_nykyinen_pk(){
        return $this->nyk_pikakommentti;
    }
    public function set_nykyinen_pk($uusi){
        if($uusi instanceof Pikakommentti){
            $this->nyk_pikakommentti = $uusi;
        }
    }
}
?>