<?php
/**
 * Tämä luokka pitää huolta yhden kielen tiedoista. Alkoi vanha systeemi
 * järjestyksensäilyttämisvaatimuksineen tuntua elähtäneeltä. Näinhän asiat
 * hoidetaan paremmin! Tämä on git-testi!
 */
class Kieli{
    private $id, $nimi;
     
    function __construct($id, $nimi) {
        $this->id = $id;
        $this->nimi = $nimi;
    }
    
    public function get_id(){
        return $this->id;
    }
    public function get_nimi(){
        return $this->nimi;
    }
}

/**
 * Description of Kielet
 *
 * @author kerkjuk_admin
 */
class Kielet {

    public static $LATINA = 0;
    public static $SUOMI = 1;
    public static $RUOTSI = 2;
    public static $ENGLANTI = 3;
    
    // uudet:
    public static $NORJA = 4;
    public static $SAKSA = 5;
    public static $VENAJA = 6;
    public static $ARABIA = 7;
    public static $HEPREA = 8;
    public static $ITALIA = 9;
    public static $KIINA = 10;
    public static $RANSKA = 11;

    // Kieli_id on kielen nroarvoon viittaavan sessiomuuttujan nimi tai name-arvo.
    public static $name_kieli_id = "kieli_id";
    

    /**
     * @return <type> Palauttaa taulukon, joka sisältää kielioliot. Sisäiseen
     * käyttöön. KESKEN!!
     */
    private static function hae_kielitaulukko(){
        $kielet = array(new Kieli(0, "latina"),
                        new Kieli(1, "suomi"),
                        new Kieli(2, "ruotsi"),
                        new Kieli(3, "englanti"),
                        new Kieli(4, "norja"),
                        new Kieli(5, "saksa"),
                        new Kieli(6, "ven&auml;j&auml;"),
                        new Kieli(7, "arabia"),
                        new Kieli(8, "heprea"),
                        new Kieli(9, "italia"),
                        new Kieli(10, "kiina"),
                        new Kieli(11, "ranska"));
        
        //KORJAA sort($kielet, $sort_flags);
        
        return $kielet;
    }
    
    /**
     * @return <type> Palauttaa taulukon, joka sisältää kielten
     * numeroarvot
     */
    public static function hae_kielten_arvot(){
        $arvot = array();
        
        $kielet = Kielet::hae_kielitaulukko();
        
        foreach ($kielet as $kieli) {
            if($kieli instanceof Kieli){
                array_push($arvot, $kieli->get_id());
            }
        }
        
        return $arvot;
    }

    /**
     * Palauttaa kielten nimet taulukkona.
     * @return <type> Palauttaa taulukon, joka sisältää kielten nimet
     * merkkijonomuodossa.
     */
    public static function hae_kielten_nimet(){
        $nimet = array();
        
        $kielet = Kielet::hae_kielitaulukko();
        
        foreach ($kielet as $kieli) {
            if($kieli instanceof Kieli){
                array_push($nimet, $kieli->get_nimi());
            }
        }
        
        return $nimet;
    }
    
     /**
     * Palauttaa lukua eli kieli_indeksiä vastaavan kielen, tai
     * tekstin "Tuntematon", jos parametri on sopimaton.
     * @param <type> $arvo
     * @return <type>
     */
    public static function hae_kielen_nimi($arvo){
        $kuvaus = "Kieli tuntematon";

        // Jos parametri on ok, haetaan kielen nimi. Jos huomataan
        // jotakin outoa, palautetaan virheviesti.
        if(is_numeric($arvo)) {
            try{
                $kielet = Kielet::hae_kielitaulukko();

                foreach ($kielet as $kieli) {
                    if($kieli instanceof Kieli){
                        if($arvo == $kieli->get_id()){
                            $kuvaus = $kieli->get_nimi();
                            break;
                        }
                    }
                }
            }
            catch(Exception $poikkeus){
                $kuvaus = $poikkeus->getMessage();
            }
        }
        return $kuvaus;
    }


    /**
     * Kielen vaihtaminen (runko vain, ei toteutettu)
     * @param type $kieli 
     */
    public static function kaanna($kieli){
        if($kieli == Kielet::$RUOTSI){

        }
        else if($kieli == Kielet::$SUOMI){
        }
        else if($kieli == Kielet::$ENGLANTI_nimi){
        }
    }

    /**
    * Luo ja palauttaa kielivalikon html-koodin. Ei sisällä lomake- eli
    * form-tageja!
    * @param <type> $kieli
    * @param <type> $otsikko
    * @return <type>
    */
   public static function nayta_kielivalikko(&$kieli, $otsikko, $name_arvo){

       $kielivalikkohtml = "";

       try{
           $arvot = Kielet::hae_kielten_arvot();
           $nimet = Kielet::hae_kielten_nimet();
           $oletusvalinta_arvo = $kieli;
           $kielivalikkohtml.= Html::luo_pudotusvalikko($arvot,
                                                   $nimet,
                                                   $name_arvo,
                                                   $oletusvalinta_arvo,
                                                   $otsikko);
       }
       catch(Exception $poikkeus){
           $kielivalikkohtml = Bongaustekstit::$kielivalikko_virheilm." (".
                           $poikkeus->getMessage().")";
       }
       return $kielivalikkohtml;
   }
}


?>
