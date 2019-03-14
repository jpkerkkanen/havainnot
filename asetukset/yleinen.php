<?php
/**
 * Tänne tulee yleisiä asetuksia liittyen sivuston sisältööön yms.
 * Asetusarvot on kääritty kuvaavasti nimetyn luokan sisään ja määritelty
 * static-määreellä, jolloin niitä voidaan käyttää helposti myös luokan
 * ulkopuolelta.
 */




/**
 * Tämä luokka sisältää arvot muuttujalle, joka määrää, mikä olio
 * käyttäjälle näytetään. 
 */
class Oliotyyppi{
    public static $PERUSNAKYMA = 0; //
    public static $VALTUUSLOMAKE = 1; //
    public static $HENKILOTIETOLOMAKE = 3; //
    public static $HAVAINTOLOMAKE = 4; //
    public static $KUVA = 6; //
    public static $MUU = 50; //
}

/**
 * Tämä luokka sisältää arvot muuttujalle, joka määrää, miten olio
 * käyttäjälle näytetään. 
 */
class Nakyvyys{
    public static $JULKINEN = 10; //
    public static $YKSITYINEN = 5; //
}



/**
 * Aikarajojen arvot ja erinäisiä metodeita.
 * Näihin static-muuttujiin pääsee käsiksi muualtakin suoraan luokan nimen avulla
 * ilman olion luomista.
 */
class Aikarajat{
    //Aika sekunteina, jota pitempi laiska aika aiheuttaa uloskirjauksen.
    public static $MAXILAISKA_AIKA =7200;
}

/**
 * Sisältää toimintoryhmien nimet eli lomakkeiden name-arvot (kun yhtä arvoa
 * kohti useita eri value-arvoa). Nämä eivät näy käyttäjälle missään, eikä
 * niitä tarvitse kääntää eri kielille.
 */
class Toimintonimet{
    public static $kayttajatoiminto = "kayttajatoiminto";
    public static $yllapitotoiminto = "yllapitotoiminto";
    public static $kuvatoiminto = "kuvatoiminto";
    public static $havaintotoiminto = "havaintotoiminto";
    public static $lajiluokkatoiminto = "lajiluokkatoiminto";
}

/**
 * Sisältää sivulla näkyvät yleiset kiinteät tekstit lukuunottamatta 
 * painikkeita (ovat erikseen)
 */
class Tekstit{
    public static $otsikko1_etusivu = "";   // Oli: "Aihe: "

    public static $otsikko_selitys = "Lis&auml;tietoja";

    // Ilmoitukset:
    public static $ilm_toimintoa_ei_toteutettu =
        "Toimintoa ei ole valitettavasti toteutettu!";
    
    public static $virhe_maavalikon_luomisessa = "Virhe maavalikon luomisessa";

    //======================= kayttäjä-tekstit==================================
}

/**
 * Sisältää sovellukselle yhteisiä tietoja.
 */
class Yleisasetuksia{
    public static $etusivutiedostonimi = "index.php";  

    //======================= kayttäjä-tekstit==================================
}


/* sisältää sessioon liittyviä merkkijonotunnisteita ja arvoja, joita käytetään
 * yleisesti halki sovelluksen.
 */
class Sessio{
    // Sessiomuuttujien nimet:
    public static $tunnistus = "tunnistus";
    public static $viim_aktiivisuus = "viim_aktiivisuus";
    public static $omaid = "omaid";    // Käyttäjän id-tunniste.
    public static $poppoon_id = "poppoon_id";    // Poppoon id-tunniste.
    public static $edellinen_uloskirjausaika_sek = "edellinen_uloskirjausaika_sek";
    
    // Sessiomuuttujien arvot:
    public static $tunnistus_ok = "tunnistus_ok";
    public static $tunnistus_ei_ok = "tunnistus_ei_ok";
    
}
/**
 * Yleisiä käyttöliittymäelementtien id-arvoja.
 */
class Id{
    public static $palkki_oikea = "palkki_oikea";
    public static $palkki_vasen = "palkki_vasen";
    public static $sisalto = "sisalto";
}

/**
 * Yleisiä käyttöliittymäelementtien class-arvoja.
 */
class Class_arvo{
    public static $keskitys = "keskitys";
    public static $yllapitoilmoitus = "yllapitoilmoitus";
    public static $rinnakkain = "rinnakkain";
    public static $korostus = "korostus";
}

/**
 * Tämä luokka pitää huolta yhden Maan tiedoista. 
 */
class Maa{
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
class Maat {

    public static $valikko_otsikko = "Maa";

    // Aakkosjärjestys:
    public static $jordania = 4; //
    public static $madeira = 2; //
    public static $norja = 3; //
    public static $ruotsi = 7; //
    public static $skotlanti = 6; //
    
    public static $suomi = 1; //    // OLETUS!
    public static $syyria = 5; //
    public static $unkari = 8; //
    public static $muumaa = 9; //
    public static $belgia = 10; 
    
    public static $libanon = 11;
    public static $hollanti = 12;
    public static $ranska = 13;
    public static $saksa = 14;
    public static $tanska = 15;
    
    public static $islanti = 16;
    public static $israel = 17;
    public static $isobritannia = 18;
    
    
    /**
     * @return <type> Palauttaa taulukon, joka sisältää maaoliot. Sisäiseen
     * käyttöön. KESKEN!! Huomaa, että maan nimet suomeksi kiinteästi. Pitäisi
     * jotenkin mahdollistaa muuttaminen, kun kerkiää.
     */
    private static function hae_maataulukko(){
        $maat = array(new Maa(Maat::$belgia, "Belgia"),
                        new Maa(Maat::$hollanti, "Hollanti"),
                        new Maa(Maat::$islanti, "Islanti"),
                        new Maa(Maat::$isobritannia, "Iso-Britannia"),
                         new Maa(Maat::$israel, "Israel"),
                        new Maa(Maat::$jordania, "Jordania"),
                        new Maa(Maat::$libanon, "Libanon"),
            
                        new Maa(Maat::$madeira, "Madeira"),
                        new Maa(Maat::$norja, "Norja"),
                        new Maa(Maat::$ranska, "Ranska"),
                        new Maa(Maat::$ruotsi, "Ruotsi"),
                        new Maa(Maat::$saksa, "Saksa"),
            
                        new Maa(Maat::$skotlanti, "Skotlanti"),
                        new Maa(Maat::$suomi, "Suomi"),
                        new Maa(Maat::$syyria, "Syyria"),
                        new Maa(Maat::$tanska, "Tanska"),
                        new Maa(Maat::$unkari, "Unkari"),
            
                        new Maa(Maat::$muumaa, "Muu maa")
                        );
        return $maat;
    }
    
    /**
     * @return <type> Palauttaa taulukon, joka sisältää maiden
     * numeroarvot
     */
    public static function hae_maiden_arvot(){
        $arvot = array();
        
        $maat = Maat::hae_maataulukko();
        
        foreach ($maat as $maa) {
            if($maa instanceof Maa){
                array_push($arvot, $maa->get_id());
            }
        }
        
        return $arvot;
    }

    /**
     * Palauttaa maiden nimet taulukkona.
     * @return <type> Palauttaa taulukon, joka sisältää maiden nimet
     * merkkijonomuodossa.
     */
    public static function hae_maiden_nimet(){
        $nimet = array();
        
        $maat = Maat::hae_maataulukko();
        
        foreach ($maat as $maa) {
            if($maa instanceof Maa){
                array_push($nimet, $maa->get_nimi());
            }
        }
        
        return $nimet;
    }
    
     /**
     * Palauttaa lukua eli maa_indeksiä vastaavan kielen, tai
     * tekstin "Tuntematon", jos parametri on sopimaton.
     * @param <type> $arvo
     * @return <type>
     */
    public static function hae_maan_nimi($arvo){
        $kuvaus = "Maa tuntematon";

        // Jos parametri on ok, haetaan maan nimi. Jos huomataan
        // jotakin outoa, palautetaan virheviesti.
        if(is_numeric($arvo)) {
            try{
                $maat = Maat::hae_maataulukko();

                foreach ($maat as $maa) {
                    if($maa instanceof Maa){
                        if($arvo == $maa->get_id()){
                            $kuvaus = $maa->get_nimi();
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
    * Luo ja palauttaa maavalikon html-koodin. Ei sisällä lomake- eli
    * form-tageja!
    * @param <type> $maa
    * @param <type> $otsikko
    * @return <type>
     * @param type $name_arvo
     * @return string
     */
   public static function nayta_maavalikko(&$maaindeksi, $otsikko, $name_arvo){

       $maavalikkohtml = "";
lisää id
       try{
           $arvot = Maat::hae_maiden_arvot();
           $nimet = Maat::hae_maiden_nimet();
           $oletusvalinta_arvo = $maaindeksi;
           $maavalikkohtml.= Html::luo_pudotusvalikko($arvot,
                                                   $nimet,
                                                   $name_arvo,
                                                   $oletusvalinta_arvo,
                                                   $otsikko);
       }
       catch(Exception $poikkeus){
           $maavalikkohtml = Tekstit::$virhe_maavalikon_luomisessa." (".
                           $poikkeus->getMessage().")";
       }
       return $maavalikkohtml;
   }
   
   /**
    * Luo ja palauttaa maavalikon html-koodin. Ei sisällä lomake- eli
    * form-tageja!
    * @param <type> $maa
    * @param <type> $otsikko
    * @return <type>
     * @param type $name_arvo
     * @return string
     */
   public static function nayta_maavalikko_vakipaikka(&$maaindeksi, $otsikko, $name_arvo){

       $maavalikkohtml = "";

       try{
           $arvot = Maat::hae_maiden_arvot();
           $nimet = Maat::hae_maiden_nimet();
           $oletusvalinta_arvo = $maaindeksi;
           $maavalikkohtml.= Html::luo_pudotusvalikko($arvot,
                                                   $nimet,
                                                   $name_arvo,
                                                   $oletusvalinta_arvo,
                                                   $otsikko);
       }
       catch(Exception $poikkeus){
           $maavalikkohtml = Tekstit::$virhe_maavalikon_luomisessa." (".
                           $poikkeus->getMessage().")";
       }
       return $maavalikkohtml;
   }
}





?>
