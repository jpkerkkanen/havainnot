<?php
/**
 * Description of Kayttajakontrolleritestaus: Käyttäjään liittyvät
 * integraatiotestit (liittyvät "toteuta-"-alkuisiin metodeihin).
 *
 * @author J-P
 */
class Kayttajakontrolleritestaus extends Testialusta{
    
    /** @var Henkilo */
    private $testihenkilo1, $testihenkilo2;
    
    /** @var Kayttajakontrolleri */
    private $kt;
    
    /** @var Poppoo */
    private $testipoppoo;
    private $testipoppoon_id;
    
    public static $testipoppoonimi = "Testi-poppoo";
    
    function __construct($tietokantaolio, $parametriolio){
        parent::__construct($tietokantaolio, $parametriolio, "Kayttajakontrolleri");
        $this->kt = new Kayttajakontrolleri($tietokantaolio, $parametriolio);
                
    }
    
    function toteuta_testit(){
        
        $ots = "Testataan luokkaa 'Kayttajakontrolleri'";
        //===================================================================
        $this->tee_alkusiivous();
        $this->luo_testipoppoo();
        $this->testaa_toteuta_tallenna_uusi();
        
        $this->tee_loppusiivous();
        //===================================================================
        
        $virheilm = $this->tulosta_virheilmoitukset();
        $sis = $this->tulosta_kaikki_ilmoitukset();
        $virheilm_lkm = $this->virheilmoitusten_lkm();
        
        
        $palaute = new Testipalaute($ots, $virheilm, $sis, $virheilm_lkm);
        
        return $palaute;
    }
    /**
     * Testaa uuden henkilön tallennuksen.
     */
    public function testaa_toteuta_tallenna_uusi(){
        $this->lisaa_kommentti("==================================================");
        $this->lisaa_lihava_kommentti("Testataan toteuta_tallenna_uusi-metodia");
        
        $this->kt->get_parametriolio()->etun = "Kalle";
        $this->kt->get_parametriolio()->sukun = "Kekkänen";
        $this->kt->get_parametriolio()->lempin = "Ketku";
        $this->kt->get_parametriolio()->komm = "Katala tyyppi";
        
        $this->kt->get_parametriolio()->eosoite = Kayttajatestaus::$testi_email;
        $this->kt->get_parametriolio()->osoite = "Ketkutie 23, Hölölä";
        $this->kt->get_parametriolio()->puhelin = "+534 899 7878";
        $this->kt->get_parametriolio()->online = 1;
        
        $this->kt->get_parametriolio()->uusktunnus = "testikalle";
        $this->kt->get_parametriolio()->uussalasana = "kallekalle";
        $this->kt->get_parametriolio()->salavahvistus = "kallekalle";
        $this->kt->get_parametriolio()->poppoon_id = -1; // Testipoppoota ei ole.
        
        $this->kt->get_parametriolio()->asuinmaa = Maat::$islanti; 
        
        // kokeillaan tallentaa uusi:
        $palauteolio = new Palaute();
        $this->kt->toteuta_tallenna_uusi($palauteolio);
        if($palauteolio->get_onnistumispalaute() === 
                Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK){
            $this->lisaa_kommentti("Uuden tallennus hyvillä tiedoilla ok!");
        } else{
            $this->lisaa_virheilmoitus("Virhe uuden tallennuksessa!".
                    $palauteolio->tulosta_virheilmoitukset());
        }
        
        
        $this->lisaa_kommentti("Metodin toteuta_tallenna_uusi testaus loppu.");
        $this->lisaa_kommentti("==================================================");
    }
    
    /**
     * Tyhjentää tietokannasta testihenkilöt. 
     */
    public function tee_loppusiivous(){
        $this->lisaa_kommentti("======================== SIIVOUS ALKAA =======================");
        
        $poisto_lkm = 
            $this->tietokantaolio->poista_kaikki_rivit(
                                        "henkilot", 
                                        Henkilo::$sarakenimi_eosoite, 
                                        Kayttajatestaus::$testi_email);
        
        
        if($poisto_lkm > 0){
            $this->lisaa_kommentti("Henkil&ouml;iden poisto ok. 
                                Poistettu ".$poisto_lkm." henkil&ouml;&auml;");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe siivouksessa! 
                                Poistettu ".$poisto_lkm." henkil&ouml;&auml;");
        }
        
        // Poistetaan testipoppoot:
        $this->poista_testipoppoot(1);
        
        $this->lisaa_kommentti("======================== SIIVOUS TEHTY ========================");
    }
    /**
     * Tyhjentää tietokannasta mahdolliset testioliot. 
     */
    public function tee_alkusiivous(){
        $this->lisaa_kommentti("===================== ALKUSIIVOUS ALKAA ======================");
        
        // Poistetaan testipoppoot:
        $this->poista_testipoppoot(-1);
        
        $poisto_lkm = 
            $this->tietokantaolio->poista_kaikki_rivit(
                                        "henkilot", 
                                        Henkilo::$sarakenimi_eosoite, 
                                        Kayttajatestaus::$testi_email);
        
        
        $this->lisaa_kommentti("Alkusiivous suoritettu onnistuneesti. 
                                Poistettu ".$poisto_lkm." henkil&ouml;&auml;");
        
        
        
        $this->lisaa_kommentti("====================== ALKUSIIVOUS TEHTY ======================");
    }

    /**
     * Luo testipoppoon ja tallentaa sen. Nimi on 
     * Kayttajakontrolleritestaus::$testipoppoonimi ja myös testipoppoo_id saa
     * arvon täällä.
     */
    public function luo_testipoppoo() {
        $this->testipoppoo = new Poppoo(Poppoo::$MUUTTUJAA_EI_MAARITELTY, 
                                        $this->tietokantaolio);
        $this->testipoppoo->set_arvo(Kayttajakontrolleritestaus::$testipoppoonimi, 
                                    Poppoo::$sarakenimi_nimi);
        $this->testipoppoo->set_arvo("testikayttis", 
                                    Poppoo::$sarakenimi_kayttajatunnus);
        $this->testipoppoo->set_arvo("Testikommentti", 
                                    Poppoo::$sarakenimi_kommentti);
        $this->testipoppoo->set_arvo(10, 
                                    Poppoo::$sarakenimi_maksimikoko);
        
        // Tunnuksen vahvistus tarvitaan:
        $this->testipoppoo->set_kayttajatunnusvahvistus("testikayttis");
        
        $palaute = $this->testipoppoo->tallenna_uusi();
        
        if($palaute == Poppoo::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Testipoppoon tallennus onnistui!");
            $this->testipoppoon_id = $this->testipoppoo->get_id();
            $_SESSION[Sessio::$poppoon_id] = $this->testipoppoon_id;
        } else{
            $this->lisaa_virheilmoitus("Virhe testipoppoon tallennuksessa! ".
                $this->testipoppoo->tulosta_virheilmoitukset());
                
            $this->testipoppoon_id = Poppoo::$MUUTTUJAA_EI_MAARITELTY;
        }    
    }
    /**
     * Poistaa testipoppoot. Jos onnistuu eli poistettuja $hyva_lkm-parametrin
     * verran, palauttaa viestin onnistumisesta, muuten virheilmoituksen.
     * Jos $hyva_lkm == -1, ei palauteta missään tapauksessa virheilmoitusta, 
     * vaan pelkkä neutraali ilmoitus poistettujen lukumäärästä.
     * @param type $hyva_lkm
     */
    public function poista_testipoppoot($hyva_lkm) {
        $poisto_lkm = 
            $this->tietokantaolio->poista_kaikki_rivit(
                                        "poppoot", 
                                        Poppoo::$sarakenimi_nimi, 
                                        Kayttajakontrolleritestaus::$testipoppoonimi);
        
        if($poisto_lkm == $hyva_lkm || $hyva_lkm == -1){
            $this->lisaa_kommentti("Siivous suoritettu onnistuneesti. 
                                Poistettu ".$poisto_lkm." poppoota");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe siivouksessa! 
                                Poistettu ".$poisto_lkm." poppoota");
        }
    }
}

?>
