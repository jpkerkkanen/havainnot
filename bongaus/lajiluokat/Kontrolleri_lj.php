<?php
/**
 * Description of Kontrolleri_lj
 * Sisältää metodit, jotka toteuttavat (delegoivat) nettisivulle tulevat pyynnöt,
 * esimerkiksi liittyen lajiluokkien ja niiden kuvausten näyttöön, tallennukseen, 
 * muokkaukseen ja poistoon (CRUD). Luomisen, muokkauksen ja poiston tarkka toteutus on
 * kuitenkin Lajiluokka- tai Kuvaus-luokassa. 
 * Täällä toteutetaan näytettävien etsinnät ja toimintojen paketointi.
 *
 * Tyypillisesti toiminnan paketoiva metodin nimi alkaa sanalla 'toteuta' ja
 * se palauttaa Palaute-luokan olion.
 *
 * Tietokantaoperaatiot
 * @author J-P
 */
class Kontrolleri_lj extends Kontrolleripohja{

    //put your code here
    public static $TOIMINTANIMI_PIKAKOMMENTIT = "Lajiluokkatoiminta";


    // Käsiteltävät oliot:
    /**
     * @var Lajiluokka
     */
    private $nyk_lajiluokkaolio;
    /**
     * @var Kuvaus
     */
    private $nyk_kuvausolio;
    
    private $havaintokontrolleri;   // näkymiä varten..

    // Name-arvot liittyen lajiluokkiin ja kuvauksiin.
    public static $name_id_lj = "id_lj";
    public static $name_ylaluokka_id_lj = "ylaluokka_id_lj";
    public static $name_nimi_latina_lj = "nimi_latina_lj";
    public static $name_siirtokohde_id_lj = "siirtokohde_id_lj";
    
    public static $name_id_kuv = "id_kuv"; 
    public static $name_lajiluokka_id_kuv = "lajiluokka_id_kuv"; 
    public static $name_nimi_kuv = "nimi_kuv";  
    public static $name_kuv_kuv = "kuv_kuv"; 
    public static $name_kieli_kuv = "kieli_kuv"; 
    
    public static $name_taulukkosolun_id = "taulukkosolun_id"; // Lajiluokkien muokkaus.

    /**
     * Luokan rakentaja:
     * @param <type> $tietokantaolio
     * @param <type> $parametriolio
     */
    public function __construct($tietokantaolio, $parametriolio) {
        parent::__construct($tietokantaolio, $parametriolio);
        
        $this->havaintokontrolleri = new Havaintokontrolleri($tietokantaolio, 
                                                            $parametriolio);
    }
    
    public function get_nyk_lajiluokkaolio(){      
        return $this->nyk_lajiluokkaolio;    
    }
    
    public function set_nyk_lajiluokkaolio($lj_olio){
        if($lj_olio instanceof Lajiluokka){
            $this->nyk_lajiluokkaolio = $lj_olio;
        }
    }
    
    public function get_nyk_kuvausolio(){      
        return $this->nyk_kuvausolio;    
    }
    
    public function set_nyk_kuvausolio($kuv_olio){
        if($kuv_olio instanceof Kuvaus){
            $this->nyk_kuvausolio = $kuv_olio;
        }
    }
    
    public function toteuta_nayta_nimikuvauslomake(&$palauteolio){
        /** @var Parametrit */
        $param = $this->get_parametriolio();
        $olio_id = -1;
        
        // Haetaan ensin muokattavan tiedot, jos kyseessä vanha:
        if(!$param->uusi_olio){
            if($param->kieli_id == Kielet::$LATINA){
                $kuvaus = "";   // Tällä ei merkitystä
                $olio = new Lajiluokka($this->get_tietokantaolio(), 
                                        $this->get_parametriolio()->id_lj);
                if($olio instanceof Lajiluokka){
                    $nimi = $olio->get_nimi_latina_html_encoded();
                    $olio_id = $this->get_parametriolio()->id_lj;
                }
                else{
                    $nimi = "??";
                }
            }
            // Muissa kielissä tiedot ovat Kuvaus-luokan oliossa:
            else{
                $olio = new Kuvaus($this->get_tietokantaolio(), 
                                    $this->get_parametriolio()->id_kuv);
                if($olio instanceof Kuvaus){
                    $nimi = $olio->get_nimi_html_encoded();
                    $kuvaus = $olio->get_kuvaus();
                    $olio_id = $this->get_parametriolio()->id_kuv;
                }
                else{
                    $nimi = "??";
                    $kuvaus = "????";
                }
            }
        }
        else{
            $nimi = "";
            $kuvaus = "";
        }
        
        $html = Nakymat_lj::nayta_nimi_kuvaus_lomake( 
                                                    $nimi, 
                                                    $kuvaus, 
                                                    $param->kieli_id, 
                                                    $olio_id,
                                                    $param->taulukkosolun_id,
                                                    $param->id_lj);
                
        $palauteolio->set_sisalto($html);

    }

    /**
     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_nayta_lajiluokkalomake(&$palauteolio){
        
        // Ei näytetä -1:stä kentissä.
        if($this->get_parametriolio()->nimi_kuv === Parametrit::$EI_MAARITELTY){
            $this->get_parametriolio()->nimi_kuv = "";
        }
        if($this->get_parametriolio()->kuv_kuv === Parametrit::$EI_MAARITELTY){
            $this->get_parametriolio()->kuv_kuv = "";
        }
        
        $html = Nakymat_lj::nayta_lajiluokkalomake(
                        $this->get_parametriolio()->ylaluokka_id_lj, 
                                            $this->get_parametriolio()->nimi_latina_lj, 
                                            $this->get_parametriolio()->nimi_kuv, 
                                            $this->get_parametriolio()->kuv_kuv, 
                                            $this->get_parametriolio()->kieli_kuv, 
                                            true, 
                                            $this->get_tietokantaolio());
        
        $palauteolio->set_sisalto($html);
        
        // Palaute onnistumisesta:
        if(empty($html)){
            $palauteolio->set_onnistumispalaute(
                                    Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
        }
        else{
            $palauteolio->set_onnistumispalaute(
                                    Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }
        
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
        
        return $palauteolio;
    }
    
    /**
     * Toteuttaa nimenmukaisen toiminnon. Palauttaa palauteolion, mutta
     * huomaa, että se ei sisällä kokonaista html-sivua, vaan vain
     * lajiluokkalaatikon (div).
     * @param Palauteolio $palauteolio
     */
    public function toteuta_nayta_lajiluokat(&$palauteolio){

        $html = ""; // Palautettava.

        $ylaluokka_id = $this->get_parametriolio()->ylaluokka_id_lj;
        
        $lajiluokat = array();
        
        // Haetaan tietokannasta ylaluokkaan kuuluvat lajiluokat. Jotta
        // järjestys saadaan suomen mukaan, pitää yhdistää kuvaukset-tauluun.
        $hakulause = "SELECT ".Lajiluokka::$taulunimi.".".Lajiluokka::$SARAKENIMI_ID.
                        " FROM ".Lajiluokka::$taulunimi.
                        " JOIN ".Kuvaus::$taulunimi.
                        " WHERE ".Lajiluokka::$taulunimi.".".Lajiluokka::$SARAKENIMI_YLALUOKKA_ID."=".
                            $ylaluokka_id.
                        " AND ".Kuvaus::$taulunimi.".".Kuvaus::$SARAKENIMI_LAJILUOKKA_ID."=".
                            Lajiluokka::$taulunimi.".".Lajiluokka::$SARAKENIMI_ID.
                        " AND ".Kuvaus::$taulunimi.".".Kuvaus::$SARAKENIMI_KIELI."=".
                            Kielet::$SUOMI.
                        " ORDER BY ".Kuvaus::$taulunimi.".".Kuvaus::$SARAKENIMI_NIMI;
        $osumat =
            $this->get_tietokantaolio()->tee_OMAhaku_oliotaulukkopalautteella($hakulause);


        if(!empty ($osumat)){
            foreach ($osumat as $lj) {
                $olio = new Lajiluokka($this->get_tietokantaolio(), $lj->id);
                array_push($lajiluokat, $olio);
            }
        }
        else{   // Ellei lajiluokkia löytynyt:
            $ilm = Bongaustekstit::$lajiluokka_virheilmoitus_yhtaan_lajiluokkaa_ei_loytynyt;
            $html .= $ilm;
            $palauteolio->set_ilmoitus($ilm);
        }

        // Paketoidaan yksittäiset yhteen taulukkoon. Tarvitaan myös
        // painike poistumiseen.
        $painikkeet =
                    "<button type='button' onclick=".
                    "'sulje_ruutu2(\"lajiluokkalaatikko\")' title='".
                    Bongauspainikkeet::$LAJILUOKAT_SULJE_NAKYMA_TITLE."'>".
                    Bongauspainikkeet::$LAJILUOKAT_SULJE_NAKYMA_VALUE.
                    "</button>";
        
        // TArkistetaan admin-oikeudet:
        $on_admin = false;
        $kayttaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                $this->get_tietokantaolio());
        
        if($kayttaja->on_kuningas()){
            $on_admin = true;
        }
        
        $html = Nakymat_lj::nayta_lajiluokat($lajiluokat, $painikkeet, $on_admin);

        $palauteolio->set_sisalto($html);

        
    }

    /**
     * Toteuttaa poistovahvistuskyselyn tulostuksen.
     *
     * @param Palauteolio $palauteolio
     */
    public function toteuta_nayta_poistovahvistuskysely(&$palauteolio){

        $pk = $this->nyk_pikakommentti;

        $html = $pk->nayta_poistovahvistuskysely();

        $palauteolio->set_sisalto($html);

        
    }

    /**
     * Toteuttaa nimenmukaisen toiminnon, johon sisältyy tarkistukset. Palauttaa
     * Palauteolio-luokan olion, joka sisältää palautteen onnistumisesta ja 
     * virhetapauksessa virheilmoituksen.
     * 
     * @param Palauteolio $palauteolio
     */
    public function toteuta_tallenna_uusi_kuvaus(&$palauteolio){

        $id = Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        $uusi = new Kuvaus($this->get_tietokantaolio(), $id);
        $uusi->set_lajiluokka($this->get_parametriolio()->id_lj);
        $uusi->set_kieli($this->get_parametriolio()->kieli_id);
        $uusi->set_nimi($this->get_parametriolio()->nimi_kuv);
        $uusi->set_kuvaus($this->get_parametriolio()->kuv_kuv);
        
        $palaute = $uusi->tallenna_uusi();
        $palauteolio->set_muokatun_id(mysql_insert_id());
        
        if($palaute == Kuvaus::$OPERAATIO_ONNISTUI){
            $palaute = Bongaustekstit::$kuvaus_tallennus_uusi_ok;
            $palauteolio->set_operaatio_onnistui(true);
            
        }
        else{
            $palauteolio->set_operaatio_onnistui(false);
            $palaute = Bongaustekstit::$kuvaus_virheilmoitus_tiedoissa_virheita." ".
                        $uusi->tulosta_virheilmoitukset();
        }
        
        $palauteolio->set_ilmoitus($palaute);
    }
    
    /**
     * Toteuttaa nimenmukaisen toiminnon, johon sisältyy tarkistukset. Palauttaa
     * Palauteolio-luokan olion, joka sisältää palautteen onnistumisesta ja 
     * virhetapauksessa virheilmoituksen.
     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_tallenna_muokkaus_kuvaus(&$palauteolio){

        $id = $this->get_parametriolio()->id_kuv;
        $muokattava = new Kuvaus($this->get_tietokantaolio(), $id);
        $muokattava->set_nimi($this->get_parametriolio()->nimi_kuv);
        $muokattava->set_kuvaus($this->get_parametriolio()->kuv_kuv);
        
        $palaute = $muokattava->tallenna_muutokset();
        
        if($palaute == Kuvaus::$OPERAATIO_ONNISTUI){
            $palaute = Bongaustekstit::$kuvaus_tallennus_muokkaus_ok;
            $palauteolio->set_onnistumispalaute(Palaute::$OPERAATIO_ONNISTUI);
        }
        else{
            $palauteolio->set_onnistumispalaute(Palaute::$VIRHE);
            $palaute = Bongaustekstit::$kuvaus_virheilmoitus_tiedoissa_virheita." ".
                        $muokattava->tulosta_virheilmoitukset();
        }
        
        $palauteolio->set_ilmoitus($palaute);
    }
    
    /**
     * Toteuttaa nimenmukaisen toiminnon, johon sisältyy tarkistukset. Palauttaa
     * Palauteolio-luokan olion, joka sisältää palautteen onnistumisesta ja 
     * virhetapauksessa virheilmoituksen.
     * 
     * @param Palauteolio $palauteolio
     */
    public function toteuta_tallenna_muokkaus_lajiluokka(&$palauteolio){

        $id = $this->get_parametriolio()->id_lj;
        $muokattava = new Lajiluokka($this->get_tietokantaolio(), $id);
        $muokattava->set_nimi_latina($this->get_parametriolio()->nimi_latina_lj);
        
        $palaute = $muokattava->tallenna_muutokset();
        
        if($palaute == Lajiluokka::$OPERAATIO_ONNISTUI){
            $palaute = Bongaustekstit::$lajiluokka_muutostallennus_ok;
            $palauteolio->set_operaatio_onnistui(true);
        }
        else{
            $palauteolio->set_operaatio_onnistui(false);
        }
        
        $palauteolio->set_ilmoitus($palaute);

        
    }
    
    /**
     * Toteuttaa nimenmukaisen toiminnon. 
     * @param Palauteolio $palauteolio
     */
    public function toteuta_havaintojen_ja_kuvien_siirto(&$palauteolio){
        
        $lajiluokkaolio = new Lajiluokka($this->get_tietokantaolio(), 
                                        $this->get_parametriolio()->id_lj);
        
        $kohdelajiluokan_id = $this->get_parametriolio()->siirtokohde_id_lj;
        
        $hav_lkm = 
            $lajiluokkaolio->siirra_havainnot_toiseen_lajiin($kohdelajiluokan_id);
        
        $kuvalinkit_lkm = 
            $lajiluokkaolio->siirra_kuvat_toiseen_lajiin($kohdelajiluokan_id);
        
        if($hav_lkm === Tietokantaolio::$HAKUVIRHE){
            $palaute = Bongaustekstit::$lajiluokan_havaintosiirtovirhe.
                    $hav_lkm.Bongaustekstit::$siirtopalaute1.
                    $kuvalinkit_lkm.Bongaustekstit::$siirtopalaute2;
            $palauteolio->set_operaatio_onnistui(false);
        }
        else if($kuvalinkit_lkm === Tietokantaolio::$HAKUVIRHE){
            $palaute = Bongaustekstit::$lajiluokan_kuvasiirtovirhe.
                    $hav_lkm.Bongaustekstit::$siirtopalaute1.
                    $kuvalinkit_lkm.Bongaustekstit::$siirtopalaute2;
            $palauteolio->set_operaatio_onnistui(false);
        }
        else{
            $palaute = $hav_lkm.Bongaustekstit::$siirtopalaute1.
                    $kuvalinkit_lkm.Bongaustekstit::$siirtopalaute2;
            $palauteolio->set_operaatio_onnistui(true);
        }
        
        $palauteolio->set_ilmoitus($palaute);
                    
        
    }
    /**
     * Toteuttaa nimenmukaisen toiminnon. 
     * @param Palauteolio $palauteolio
     */
    public function toteuta_nayta_havainto_ja_kuva_siirtolomake(&$palauteolio){
        
        $lajiluokkaolio = new Lajiluokka($this->get_tietokantaolio(), $this->get_parametriolio()->id_lj);
        
        $sisaret = array(); // Muut lajiluokat.
        $this->get_parametriolio()->kieli_id = Kielet::$SUOMI;
        
        // Haetaan suomenkielinen nimi, tai latina, ellei suomeksi löydy.
        // Tarkistetaan ensin, että lajiluokkaolio kunnossa:
        if($lajiluokkaolio->olio_loytyi_tietokannasta){
            $kuvaus = $lajiluokkaolio->hae_kuvaus(Kielet::$SUOMI);
            if($kuvaus === Lajiluokka::$MUUTTUJAA_EI_MAARITELTY){
                $this->get_parametriolio()->nimi_kuv = $lajiluokkaolio->get_nimi_latina();
            }
            else{
                $this->get_parametriolio()->nimi_kuv = $kuvaus->get_nimi();
            }
            
            // Haetaan sisarlajit lajivalikkoa varten. Itseä ei mukaan!
            $kieli_id = $this->get_parametriolio()->kieli_id;
            $itse_mukana = FALSE;
            $sisaret = $lajiluokkaolio->hae_sisarlajiluokat($kieli_id, 
                                                           $itse_mukana);
        }
        else{
            $this->get_parametriolio()->nimi_kuv = "tuntematon";
        }
        
        
        $oletus_id_lj = $lajiluokkaolio->get_id();
        $sisarlajiluokat = $sisaret;
        $otsikko = "";
        $kieli_id = $this->get_parametriolio()->kieli_id;
        $name_arvo = Bongausasetuksia::$havaintokuvasiirtolomakevalikko_name;
        $lajivalikko = Nakymat_lj::nayta_lajivalikko($oletus_id_lj, 
                                                    $sisarlajiluokat, 
                                                    $otsikko, 
                                                    $kieli_id, 
                                                    $name_arvo);
        
        
        $palauteolio->set_sisalto( 
            Nakymat_lj::nayta_havaintojen_ja_kuvien_siirtolomake(
                                                            $this->get_parametriolio(),
                                                            $lajivalikko));
        
    }
    
    /**
     * Toteuttaa nimenmukaisen toiminnon.
     * @param Palauteolio $palauteolio
     */
    public function toteuta_poista_lajiluokka(&$palauteolio){
        
        $poistettava = new Lajiluokka($this->get_tietokantaolio(), 
                                    $this->get_parametriolio()->id_lj);
       
        if($poistettava instanceof Lajiluokka){
            if($poistettava->olio_loytyi_tietokannasta){
                $palaute = $poistettava->poista();

                if($palaute === Lajiluokka::$OPERAATIO_ONNISTUI){
                    $ilmoitus = Bongaustekstit::$lajiluokan_poisto_ok;
                    $palauteolio->set_operaatio_onnistui(true);
                }
                else{
                    $ilmoitus = 
                    Bongaustekstit::$lajiluokka_virheilmoitus_poisto_eiok.
                            " ".$palaute;
                    $palauteolio->set_operaatio_onnistui(false);
                }
            }
            else{
                $ilmoitus = Bongaustekstit::$lajiluokka_virheilmoitus_poisto_eiok.
                        " ".$poistettava->tulosta_virheilmoitukset();
                $palauteolio->set_operaatio_onnistui(false);
            }
        }
        else{
            $ilmoitus = 
                Bongaustekstit::$lajiluokka_virheilmoitus_poisto_eiok_lajiluokkaa_ei_loyt;
            $palauteolio->set_operaatio_onnistui(false);
        }

        $palauteolio->set_ilmoitus($ilmoitus);

        
    }

    /**
     * Tämä metodi poistaa isäntäolion poiston yhteydessä isäntäolioon
     * linkitetyt pikakommentit ja palauttaa poistettujen olioiden lukumäärän.
     * @param Tietokantaolio $tietokantaolio (täällä, koska static!)
     * @param <type> $kohde_tyyppi
     * @param <type> $kohde_id 
     */
    public static function poista_pikakommentit($tietokantaolio,
                                                $kohde_tyyppi,$kohde_id){
        $poistettujen_lkm = 0;

        // Haetaan ensin kyseiset rivit ja poistetaan ne sitten
        $hakulause = "SELECT id
                        FROM pikakommentit
                        WHERE kohde_id=$kohde_id
                        AND kohde_tyyppi=$kohde_tyyppi";
        $osumat =
            $tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);

        foreach ($osumat as $pk_olio) {
            
            $taulu = "pikakommentit";
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

        
        $vikaulosaika = hae_vika_ulosaika($omaid, $tietokantaolio);

        $hakulause = "SELECT id FROM pikakommentit
                    WHERE (tallennushetki_sek > $vikaulosaika
                    AND kohde_tyyppi = $kohde
                    AND henkilo_id <> $omaid)";

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
     * Palauttaa muotoillun vastauksen uusien pikakommenttien lukumäärästä,
     * ovat tulleet tietyn henkilön suorituksiin.
     *
     * @param <type> $henkilo_id
     * @param <type> $omaid
     * @param Tietokantaolio $tietokantaolio
     * @return string
     */
    public static function hae_henkilon_uusien_suorituspikakomm_lkm(
                                                    $omaid,
                                                    $tietokantaolio,
                                                    $henkilo_id){

        $kohde = Pikakommentti::$KOHDE_LIIKUNTASUORITUS;

        $vikaulosaika = hae_vika_ulosaika($omaid, $tietokantaolio);

        $hakulause = "SELECT pikakommentit.id FROM pikakommentit
                    JOIN suoritukset
                    ON suoritukset.id = pikakommentit.kohde_id
                    WHERE (pikakommentit.tallennushetki_sek > $vikaulosaika
                    AND pikakommentit.kohde_tyyppi = $kohde
                    AND suoritukset.henkilo_id = $henkilo_id
                    AND pikakommentit.henkilo_id <> $omaid)";

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

    public function toteuta_nayta(&$palauteolio) {
        $this->toteuta_nayta_lajiluokat($palauteolio);
    }

    /**
     * @param \Palaute $palauteolio
     * @return \Palaute $palauteolio
     */
    public function toteuta_nayta_poistovarmistus(&$palauteolio) {
        $this->toteuta_nayta_poistovahvistuskysely($palauteolio);
    }

    public function toteuta_poista(&$palauteolio) {
        $this->toteuta_poista_lajiluokka($palauteolio);
    }

    /**
     * Lajiluokan tallennus!
     * @return Palaute
     */
    public function toteuta_tallenna_muokkaus(&$palauteolio) {
        $this->toteuta_tallenna_muokkaus_lajiluokka($palauteolio);
    }
    /**
     * Uuden lajiluokan tallennus!
     * @param Palaute $palauteolio
     */
    public function toteuta_tallenna_uusi(&$palauteolio) {
        
        $uusi = new Lajiluokka($this->get_tietokantaolio(), 
                                        Lajiluokka::$MUUTTUJAA_EI_MAARITELTY);
        
        $tallentaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                $this->get_tietokantaolio());
        
        // Lajiluokka:
        $ylaluokka_id = $this->get_parametriolio()->ylaluokka_id_lj;
        $nimi_latina = $this->get_parametriolio()->nimi_latina_lj;
        
        // Kuvaus:
        // Nykyään kieli on tuossa aina suomi. Voi muuttaa tarvittaessa.
        $kieli_kuv = Kielet::$SUOMI;
        $kuv_kuv = $this->get_parametriolio()->kuv_kuv;
        $nimi_kuv = $this->get_parametriolio()->nimi_kuv;
        
        // Asetetaan arvot kohdalleen:
        $uusi->set_ylaluokka_id($ylaluokka_id);
        $uusi->set_nimi_latina($nimi_latina);
        
        // Kokeillaan tallentaa:
        $tallennus = $uusi->tallenna_uusi();
        
        // Jos kaikki hyvin, tallennetaan myös kuvaus lajiluokalle:
        if($tallennus === Lajiluokka::$OPERAATIO_ONNISTUI){
            
            // tallennetaan aktiivisuus:
            $tallentaja->paivita_aktiivisuus(Aktiivisuus::$LAJILUOKKATALLENNUS_UUSI);            
            
            // Uuden kuvauksen tallennus:
            $uusi_kuv = new Kuvaus($this->get_tietokantaolio(), 
                                            Kuvaus::$MUUTTUJAA_EI_MAARITELTY);
            
            // Asetetaan arvot:
            $uusi_kuv->set_kieli($kieli_kuv);
            $uusi_kuv->set_kuvaus($kuv_kuv);
            $uusi_kuv->set_lajiluokka($uusi->get_id());
            $uusi_kuv->set_nimi($nimi_kuv);
            
            // Kokeillaan tallentaa:
            $tallennus_kuv = $uusi_kuv->tallenna_uusi();
        
            // Jos kaikki hyvin:
            if($tallennus_kuv === Kuvaus::$OPERAATIO_ONNISTUI){
                
                // tallennetaan aktiivisuus:
                $tallentaja->paivita_aktiivisuus(Aktiivisuus::$KUVAUSTALLENNUS_UUSI);
                
                $palauteolio->set_onnistumispalaute(
                            Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                
                $palauteolio->set_ilmoitus(Bongaustekstit::
                                            $lajiluokka_ja_kuvaus_tallennus_ok);
                
                // Asetetaan kuitenkin palautusolioon luodun lajiluokan id:
                $palauteolio->set_muokatun_id($uusi->get_id());
                
                //$this->toteuta_nayta($palauteolio);
                $this->havaintokontrolleri->get_parametriolio()->
                                            lajiluokka_id_hav = $uusi->get_id();
                $this->havaintokontrolleri->
                                    toteuta_nayta_yksi_uusi_lomake($palauteolio);
                
            }
            else{
                $palauteolio->set_onnistumispalaute(
                            Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_UUSI);
                $palauteolio->set_ilmoitus(Bongaustekstit::
                                            $kuvaus_virheilm_tallennus_eiok.
                    Html::luo_br().$uusi_kuv->tulosta_virheilmoitukset());

                $this->toteuta_nayta_lajiluokkalomake($palauteolio);
            }
        }
        else{
            $palauteolio->set_onnistumispalaute(Palaute::
                                        $ONNISTUMISPALAUTE_VIRHE_TALLENNUS_UUSI);
            $palauteolio->set_ilmoitus(Bongaustekstit::
                                        $lajiluokka_virheilm_tallennus_eiok.
                Html::luo_br().$uusi->tulosta_virheilmoitukset());
            $this->toteuta_nayta_lajiluokkalomake($palauteolio);
        }
    }
    
    /**
    * Apumetodi usein (?) tarvittuun havaintojen näyttämiseen. Palauttaa
    * näytettävien havaintojen html:n: 
    *
    private function nayta_havainnot($palauteolio){
        $havaintokontrolleri = new Havaintokontrolleri($this->get_tietokantaolio(), 
                                                        $this->get_parametriolio());
        
        return $havaintokontrolleri->toteuta_nayta($palauteolio)->get_sisalto();
    }*/
    
    ///=========================================================================
    ///=========================================================================
    ///========================= Tietokannan säätöä ============================
    /**
     * Räätälöity metodi, jonka avulla kopioidaan toisesta tietokantataulusta
     * lajiluokka- ja kuvaustiedot tänne.
     * 
     * HUOMAA! Tämä toimii vain, kun uusi taulu on tyhjä, jolloin kaikki id-
     * arvot ovat käytössä. Muuten tulee ongelmia. Alla kohdetaulut:
     * 
     * create table lajiluokat
        (
          id                    int auto_increment not null,
          ylaluokka_id          int default -1 not null,
          nimi_latina           varchar(128) not null,
          primary key (id),
          index(ylaluokka_id),
          unique index(nimi_latina)
        ) ENGINE=INNODB;

        /* Lajiluokkien nimet ja kuvaukset eri kielillä. *
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

        ) ENGINE=INNODB;    /* Vaaditaan, jotta viite-eheys-rajoitteet toimivat *

     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_kopioi_lajiluokat_ja_kuvaukset(&$palauteolio){
        
        // Haetaan alkuperäiset tiedot tietokannasta.
        $hakulause = "SELECT * FROM blajiluokat";
        $osumat =
            $this->get_tietokantaolio()->tee_OMAhaku_oliotaulukkopalautteella($hakulause);

        $laskuri = 0;   // Laskee onnistumiset.
        
        if(!empty ($osumat)){
            foreach ($osumat as $lj) {
                
                // Uudessa taulussa nimi_latina ei saa olla tyhjä, joten
                // varmistetaan asia:
                if(empty($lj->nimi_latina)){
                    // Arvotaan luku väliltä 100000-1000000 ja lisätään vihje.
                    $lj->nimi_latina = rand(100000, 1000000)."_korjaa";
                }
                
                $kyselylause = "INSERT INTO lajiluokat VALUES (".
                                $lj->id.",".
                                $lj->ylaluokka_id.",'".
                                $lj->nimi_latina."')";
                
                $kyselyn_tila = mysql_query($kyselylause);

                if($kyselyn_tila && (mysql_affected_rows() == 1)){
                    $laskuri++;
                } 
            }
            
            $ilm = "Lajiluokkia kopioitu ".$laskuri." kpl. Kysely: ".$kyselylause."<br/>";
            
            // Sitten tehdään sama kuvauksille:
            // Haetaan alkuperäiset tiedot tietokannasta.
            $hakulause = "SELECT * FROM bkuvaukset";
            $osumat =
                $this->get_tietokantaolio()->tee_OMAhaku_oliotaulukkopalautteella($hakulause);

            $laskuri_kuv = 0;   // Laskee onnistumiset.
            
            if(!empty ($osumat)){
                foreach ($osumat as $kuv) {


                    $kyselylause = "INSERT INTO kuvaukset VALUES (".
                                    $kuv->id.",".
                                    $kuv->lajiluokka_id.",'".
                                    $kuv->nimi."','".
                                    $kuv->kuvaus."',".
                                    $kuv->kieli.")";

                    $kyselyn_tila = mysql_query($kyselylause);

                    if($kyselyn_tila && (mysql_affected_rows() == 1)){
                        $laskuri_kuv++;
                    } 
                }

                $ilm .= "Kuvauksia kopioitu ".$laskuri_kuv." kpl. Kysely: ".$kyselylause."<br/>";
            } else{
                $ilm .= "Virhe kuvausten kopioinnissa: yhtaan kuvausta ei loytynyt";
            }

        }else{  
            // Ellei lajiluokkia löytynyt:
            $ilm .= "Virhe kopioinnissa: yhtaan lajiluokkaa ei loytynyt!";
            
        }
        $palauteolio->set_ilmoitus($ilm);
    }
    
    /**
     * Räätälöity metodi, jonka avulla kopioidaan toisesta tietokantataulusta
     * havaintotiedot tänne.
     * 
     * HUOMAA! Tämä toimii vain, kun uusi taulu on tyhjä, jolloin kaikki id-
     * arvot ovat käytössä. Muuten tulee ongelmia. Alla kohdetaulut:
     * 
     * 
        create table havainnot
        (
          id                    int auto_increment not null,
          henkilo_id            int default -1 not null,
          lajiluokka_id         int default -1 not null,
          vuosi                 smallint default -1,
          kk                    tinyint default -1,
          paiva                 tinyint default -1,
          paikka                varchar(300),
          kommentti             varchar(3000),
          maa                   smallint default 1,
          varmuus               smallint default 100,
          sukupuoli             tinyint default -1,
          lkm                   int default -1,
          lisaluokitus1         smallint default -1,
          lisaluokitus2         smallint default -1,
          lisaluokitus3         smallint default -1,
          primary key (id),
          index(henkilo_id),
          index(vuosi),
          index(kk),
          index(paiva),
          index(paikka),
          index(maa),
          index(lajiluokka_id),
          index(lisaluokitus1),
          index(lisaluokitus2),
          index(lisaluokitus3),
          FOREIGN KEY (lajiluokka_id) REFERENCES lajiluokat (id)
                              ON DELETE CASCADE,
          FOREIGN KEY (henkilo_id) REFERENCES henkilot (id)
                              ON DELETE CASCADE

        ) ENGINE=INNODB; 
     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_kopioi_havainnot(&$palauteolio){
        
        // Haetaan alkuperäiset tiedot tietokannasta.
        $hakulause = "SELECT * FROM bhavainnot";
        $osumat =
            $this->get_tietokantaolio()->tee_OMAhaku_oliotaulukkopalautteella($hakulause);

        $laskuri = 0;   // Laskee onnistumiset.
        
        if(!empty ($osumat)){
            foreach ($osumat as $lj) {
                
                // Otetaan mukaan vain jp:n, Viivin ja Kimmon havainnot:
                if($lj->henkilo_id == 1 || $lj->henkilo_id == 2 || $lj->henkilo_id == 4){
                    /*create table bhavainnot
                    (
                      id                    mediumint auto_increment not null,
                      henkilo_id            int default -1 not null,
                      lajiluokka_id         mediumint default -1 not null,
                      vuosi                 smallint default -1,
                      kk                    tinyint default -1,
                      paiva                 tinyint default -1,
                      paikka                varchar(300),
                      kommentti             varchar(3000),
                      maa                   smallint DEFAULT 1,
                      varmuus               smallint DEFAULT 100,*/

                    // Uusia sarakkeita on viimeiset viisi, joihin asetetaan arvot käsin:
                    $kyselylause = "INSERT INTO havainnot VALUES (".
                                    $lj->id.",".
                                    $lj->henkilo_id.",".
                                    $lj->lajiluokka_id.",".
                                    $lj->vuosi.",".
                                    $lj->kk.",".
                                    $lj->paiva.",'".
                                    $lj->paikka."','".
                                    $lj->kommentti."',".
                                    $lj->maa.",".
                                    $lj->varmuus.",".   
                                    Sukupuoli::$ei_maaritelty.",". //sukupuoli
                                    Parametrit::$EI_MAARITELTY.",". //lkm
                                    Lisaluokitus::$ei_maaritelty.",". //ll1
                                    Lisaluokitus::$ei_maaritelty.",". //ll2
                                    Lisaluokitus::$ei_maaritelty.")"; //ll3

                    $kyselyn_tila = mysql_query($kyselylause);

                    if($kyselyn_tila && (mysql_affected_rows() == 1)){
                        $laskuri++;
                    } 
                }
            }
            
            $ilm = "Havaintoja kopioitu ".$laskuri." kpl. Kysely: ".$kyselylause."<br/>";

        }else{  
            // Ellei havaintoja löytynyt:
            $ilm = "Virhe kopioinnissa: yhtaan havaintoa ei loytynyt!";
            
        }
        $palauteolio->set_ilmoitus($ilm);
    }
    
    ///=========================================================================
    ///=========================================================================
    ///========================= Tietokannan säätö loppui ======================
}
?>
