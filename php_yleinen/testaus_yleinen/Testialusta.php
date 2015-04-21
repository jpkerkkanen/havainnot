<?php
/**
 * Description of Testialusta
 * Tämä luokka toimii pohjana testiluokille. Täällä on yleisiä metodeita
 * esimerkiksi ilmoitusten tekemiseen testin kulusta jne.
 *
 * @author J-P (10.12.2011)
 */
abstract class Testialusta extends Pohja{
    public $id_testihenkilo1, $id_testihenkilo2;
    public static $etun_testihenkilo1 = "Testi-Seppo";
    public static $etun_testihenkilo2 = "Testi-Kalle";
    public static $sukun_testihenkilo1 = "Testinen";

    public $luokkanimi;
    /**
     * @var Tietokantaolio 
     */
    public $tietokantaolio;    // Tietokantaolio-luokan olio;
    
    /**
     * @var Parametrit
     */
    public $parametriolio;    // Parametrit-luokan olio;

    //put your code here
    public function  __construct($tietokantaolio, $parametriolio, $luokkanimi) {
        parent::__construct();
        $this->tietokantaolio = $tietokantaolio;
        $this->parametriolio = $parametriolio;
        $this->luokkanimi = $luokkanimi;
    }

    
     /**
     * Luo testiä varten henkilön tietokantaan. Hyödyntää Kayttajahallinnan
     * metodeita.
     */
    public function luo_testihenkilo1(){
        $etun= Testialusta::$etun_testihenkilo1;
        $sukun=   Testialusta::$sukun_testihenkilo1;
        $lempin="Sepi";
        $komm="Ei hassumpi kaveri";
        $ktunnus="testi345";
        $salis= "testitesti";
        $eosoite="Enpä jaksa syöttää";
        $valtuudet=Valtuudet::$NORMAALI;
        $poppoo_id = 1;
        $asuinmaa = Maat::$madeira;

        $kaveri = Kayttajatestaus::luo_testihenkilo($etun, $sukun, 
                                                    $ktunnus, $salis,
                                                    $this->tietokantaolio);
        $kaveri->set_arvo_kevyt($lempin, Henkilo::$sarakenimi_lempinimi);
        $kaveri->set_arvo_kevyt($komm, Henkilo::$sarakenimi_kommentti);
        $kaveri->set_arvo_kevyt($eosoite, Henkilo::$sarakenimi_eosoite);
        $kaveri->set_arvo_kevyt($valtuudet, Henkilo::$sarakenimi_valtuudet);
        $kaveri->set_arvo_kevyt($poppoo_id, Henkilo::$sarakenimi_poppoo_id);
        $kaveri->set_arvo_kevyt($asuinmaa, Henkilo::$sarakenimi_asuinmaa);
        
        $palaute = $kaveri->tallenna_uusi();
        
        if($palaute === Henkilo::$OPERAATIO_ONNISTUI){
            $this->lisaa_ilmoitus("Henkil&ouml;n luonti onnistui",  
                    Ilmoitus::$TYYPPI_ILMOITUS);
            $this->id_testihenkilo1 = $kaveri->get_id();
        }
        else{
            $this->lisaa_virheilmoitus("Virhe henkil&ouml;n luomisessa
                 (metodi Testialusta.php->luo_testihenkilo1())! Ilmoitukset: ". 
                 $kaveri->tulosta_virheilmoitukset());
        }
    }
    
    /** Palauttaa tietokannasta ensimmäisen henkilön id:n */
    public function hae_henkilon_id(){

        $hakulause = "select id from henkilot";

        $tk_henkilo_oliot =
            $this->tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);

        if(!empty ($tk_henkilo_oliot)){
            return $tk_henkilo_oliot[0]->id;
        }
    }
    /**
     * Hakee tietokannasta henkilön nimen ja palauttaa sen. Ellei löydy,
     * palauttaa merkkijonon "tuntematon".
     * @param <type> $id
     */
    public function hae_henkilon_nimi($id){
        $nimi = "Tuntematon";
        $tk_henkilo_olio =
            $this->tietokantaolio->hae_eka_osuma_oliona("henkilot", "id", $id);

        if($tk_henkilo_olio !== Tietokantaolio::$HAKU_PALAUTTI_TYHJAN){
            $nimi = $tk_henkilo_olio->etunimi." ".$tk_henkilo_olio->sukunimi;
            $this->lisaa_kommentti(
                    "Henkil&ouml;n nimi tietokannassa: ".$nimi);
        }
        else{
            $this->lisaa_kommentti(
                    "Henkil&ouml;n nimen haku tietokannasta ei onnistunut!");
            $this->lisaa_virheilmoitus(
                    "Henkil&ouml;n nimen haku tietokannasta ei onnistunut!");
        }
        return $nimi;
    }
    
    
    
    //====================== Ilmoitusmetodit ===================================
    /**
     * Tulostaa virheilmoitukset rivinvaihdolla erotettuina kivasti muotoiltuna.
     * Huom! Tämä yliajaa Pohjassa määritellyn metodin.
     */
    public function tulosta_virheilmoitukset(){     
        if($this->virheilmoitusten_lkm() == 0){
             $tulostus = "<div style='color:green'>
                            <b>".$this->luokkanimi."-luokka: Virheit&auml;
                            ei havaittu!</b></div>";
        }
        else{
            $tulostus = "<div style='color:red'>".
                        $this->luokkanimi."-luokka: Virheit&auml; tuli " .
                        $this->virheilmoitusten_lkm().
                        " kpl. Alla ilmoitukset:<br/>";
            
            foreach ($this->ilmoitukset as $ilmoitus) {
                if(($ilmoitus->get_viesti() != "") && 
                    ($ilmoitus->get_tyyppi() == Ilmoitus::$TYYPPI_VIRHEILMOITUS)){
                    $tulostus .= $ilmoitus->get_viesti()."<br />";
                }
            }
            $tulostus .= "</div>";
        }

        return $tulostus;
    }
    
    /**
     * Tulostaa kaikki testauksen ilmoitukset rivinvaihdolla erotettuina.
     */
    public function tulosta_testikommentit(){
        $tulostus = "<h2>".$this->luokkanimi."testien tarkempi sis&auml;lt&ouml;</h2>";
        $tulostus .= "<h3>Testaus alkaa, j&auml;nnitys kihi&auml;&auml;!</h3>";
        $tulostus .= $this->tulosta_kaikki_ilmoitukset();
        return $tulostus;
    }
}
?>
