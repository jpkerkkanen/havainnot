<?php

/**
 * Description of Parametrit
 *
 * HUOM! Suuri osa muuttujista piti laittaa julkisiksi, jotta viittaus-
 * parametrit toimivat (vai mitä ne &$-alkuiset muuttujat olivat).
 *
 * Huomautus: Olisi parempi laittaa luokitustunniste muuttujan nimen alkuun,
 * esim hav_id, hav_paikka jne. Tällöin yhteen tunnisteeseen liittyvät 
 * muuttujat olisi helpompi löytää. Osa täällä on vain sen verran käytettyjä,
 * etten taida lähteä muuttamaan kaikkia.
 * 
 * @author kerkjuk_admin
 */
class Parametrit {

    // Olion käyttöön liittyvät (ei-parametrit):
    //---------------------------------------------------------------------
    // Toimintatyypit:
    private $havaintotoiminto,
            $lajiluokkatoiminto,
            $yllapitotoiminto,
            $kayttajatoiminto,
            $kuvatoiminto; 

    // Yleiset:
    private $omaid, // Käyttäjän id
            $tietokantaolio,
            $tallennuspalaute;  // Palaute tallennuksesta.

    public  $henkilo_id,    // Voi olla muukin kuin käyttäjä (vrt. omaid)     
            $kieli_id,
            $ikkunan_korkeus,
            $ikkunan_leveys;
            

    /********************* kuvatiedot alku ************************************/
    public 
            $ladattu_kuva,
            $uusi_kuva,
            $ilmoitus_kuva,
            $kuvaotsikko_kuva,
            $kuvaselitys_kuva,
            $vuosi_kuva,
            $kk_kuva,
            $paiva_kuva,
            $src_kuva,
            $tiedostonimi_kuva,
            $id_kuva,
            $max_nayttokork_kuva,   // näytettävän kuvan maksimikork.
            $max_nayttolev_kuva;   // näytettävän kuvan maksimilev.

    /********************* kuvatiedot loppu ************************************/

    /********************* henkilötiedot alku *********************************/
    public  $etun,
            $sukun,
            $lempin,
            $komm,
            $uusktunnus,
            $uussalasana,
            $salavahvistus,
            $eosoite,
            $puhelin,
            $online,
            $osoite,
            $uudet_valtuudet,
            $asuinmaa,
            $kieli_henkilo,
            $henkiloilmoitus;   // Esim henkilötietolomakkeeseen.
    
    /********************* henkilötiedot loppu ********************************/
    //=========================== Poppoo =======================================
    public  $poppoon_id,    // Käytössä/tarkasteltavana oleva poppoo.
            $poppoo_nimi,
            $poppoo_kommentti,
            $poppoo_kayttajatunnus,
            $poppoo_maksimikoko;
    
    private $poppoon_id_muokkaus;
    
    public $poppooilmoitus; // Tallentamisen virheilmoitukset esimerkiksi
    private $poppootunnusvahvistus;
    //==========================================================================
    
    
    private $kirjaudu_ktunnus, $kirjaudu_salis;
    
    //================ Pikakommenttimuuttujat alku =============================
    private $pk_kommenttiteksti, $pk_kohdetyyppi, $pk_kohde_id, $pk_id;
                        
    //================ Pikakommenttimuuttujat loppu =============================
    
    
    //=========================== Lajiluokkamuuttujat ==========================
    public $id_lj, 
            $ylaluokka_id_lj, 
            $nimi_latina_lj, 
            $siirtokohde_id_lj;
    //==========================================================================
    //=========================== Kuvausmuuttujat ==============================
    public $id_kuv, 
            $lajiluokka_id_kuv, 
            $nimi_kuv, 
            $kuv_kuv, 
            $kieli_kuv;
    //==========================================================================
    //=========================== Havaintomuuttujat ============================
    public $id_hav,
            $henkilo_id_hav,
            $lajiluokka_id_hav,
            $vuosi_hav,
            $kk_hav,
            
            $paiva_hav,
            $paikka_hav,
            $kommentti_hav, 
            $maa_hav, 
            $varmuus_hav,
            
            $sukupuoli_hav,
            $lkm_hav,
            $lisaluokitusvalinnat_hav;
    
    public
            $nayttomoodi_hav, 
            $nayttovuosi_hav, 
            $havaintoalue_hav, 
            $lajivalinnat_hav,
            $havaintovalinnat_hav, 
            $puolivuotiskauden_nro_hav,
            $lisaluokitusehto_hav;
    
    public $max_lkm_hav;    // Näin monta havaintoa näytetään kerralla.
    public $taulukkosolun_id; // Lajiluokkamuokkaustaulukon solun id.
    
    private $aukaise_havainnot_hav; // boolean, havainnon tallennuksen jälkitoiminto
    private $on_kopio_hav;  // boolean, onko kysymys havainnon kopioinnista.
    private $uusi_hav;  // boolean: uusi havainto vai vanhan muokkaus
    private $naytettavan_id_hav;    // Sen havainnon id, joka halutaan näyttää.
    
    //==========================================================================
    // Havaintojaksomuuttujat:
    
    // havaintojaksoihin liittyvät (jatkossa: havjaks eteen, niin löytäminen helpottuu):
    public $id_havjaks;
    public $henkilo_id_havjaks;
    public $lajiluokka_id_havjaks;
    public $alkuaika_sek_havjaks;
    public $alkuaika_min_havjaks;
    public $alkuaika_h_havjaks;
    public $alkuaika_paiva_havjaks;
    public $alkuaika_kk_havjaks;
    public $alkuaika_vuosi_havjaks;
    public $kesto_vrk_havjaks;
    public $kesto_h_havjaks;
    public $kesto_min_havjaks;
    public $nimi_havjaks;
    public $kommentti_havjaks;
    public $nakyvyys_havjaks;
    public $uusi_havjaks;
    //==========================================================================
    // Havaintojaksolinkkimuuttujat:
    // Name-arvot liittyen havaintojaksolinkkeihin:
    public $id_havjakslink;
    public $havainto_id_havjakslink;
    public $havaintojakso_id_havjakslink;
    
    
    /** 
     * Huom! Vältä tämän käyttöä entiteettiluokan id-tunnisteen kohdalla.
     * Käytä niissä Luokannimi::MUUTTUJAA_EI_MAARITELTY-arvoa, ettei tule
     * sekaannusta kun entiteettiä luodaan (periaatteessa otettu kyllä huomioon,
     * mutta kuitenkin noin loogisempi).
     * 
     * @var type 
     */
    public static $EI_MAARITELTY = -1;
    
    
    /* Luokan rakentaja: */
    public function __construct($tietokantaolio) {

        $this->tietokantaolio = $tietokantaolio;
        
        $this->tallennuspalaute = "";
        
        // Ikkunan leveys.
        $this->ikkunan_leveys = 
                    isset($_REQUEST[Kuvakontrolleri::$name_ikkunan_lev]) ?
                    $_REQUEST[Kuvakontrolleri::$name_ikkunan_lev]: 
                    Kuva::$OLETUSMAKSIMILEVEYS;

        // Ikkunan leveys:
        $this->ikkunan_korkeus = 
                    isset($_REQUEST[Kuvakontrolleri::$name_ikkunan_kork]) ?
                    $_REQUEST[Kuvakontrolleri::$name_ikkunan_kork]: 
                    Kuva::$OLETUSMAKSIMIKORKEUS;
        
        // Toimintoryhmät: =====================================================
        $this->havaintotoiminto = isset($_REQUEST[Toimintonimet::$havaintotoiminto]) ?
                                $_REQUEST[Toimintonimet::$havaintotoiminto]: 
                                Parametrit::$EI_MAARITELTY;
        $this->lajiluokkatoiminto
                = isset($_REQUEST[Toimintonimet::$lajiluokkatoiminto]) ?
                                $_REQUEST[Toimintonimet::$lajiluokkatoiminto]: 
                                Parametrit::$EI_MAARITELTY;
        
        // Ylläpito:
        $this->yllapitotoiminto = isset($_REQUEST[Toimintonimet::$yllapitotoiminto]) ?
                                $_REQUEST[Toimintonimet::$yllapitotoiminto]: 
                                Parametrit::$EI_MAARITELTY;

        $this->kayttajatoiminto = isset($_REQUEST[Toimintonimet::$kayttajatoiminto]) ?
                                $_REQUEST[Toimintonimet::$kayttajatoiminto]:
                                Parametrit::$EI_MAARITELTY;

        $this->kuvatoiminto = isset($_REQUEST[Toimintonimet::$kuvatoiminto]) ?
                                $_REQUEST[Toimintonimet::$kuvatoiminto]: 
                                Parametrit::$EI_MAARITELTY;

        //================== Pikakommenttimuuttujat=========================
        $this->pk_kommenttiteksti = 
                    isset($_REQUEST[Pikakommenttikontrolleri::$name_kommentti]) ?
                         $_REQUEST[Pikakommenttikontrolleri::$name_kommentti]:
                                Parametrit::$EI_MAARITELTY;

        $this->pk_kohdetyyppi = 
                    isset($_REQUEST[Pikakommenttikontrolleri::$name_kohdetyyppi]) ?
                        $_REQUEST[Pikakommenttikontrolleri::$name_kohdetyyppi]:
                                Parametrit::$EI_MAARITELTY;
        
        $this->pk_kohde_id = isset($_REQUEST[Pikakommenttikontrolleri::$name_kohde_id]) ?
                        $_REQUEST[Pikakommenttikontrolleri::$name_kohde_id]:
                                Parametrit::$EI_MAARITELTY;
        
        $this->pk_id = isset($_REQUEST[Pikakommenttikontrolleri::$name_id]) ?
                            $_REQUEST[Pikakommenttikontrolleri::$name_id]:
                                Parametrit::$EI_MAARITELTY;
        //======================================================================

        // Yleisiä muuttujia:
        $this->omaid = Henkilo::$MUUTTUJAA_EI_MAARITELTY;   // Oletus (tunnistautumaton käyttäjä)

        // Haetaan omaid sessiomuuttujasta, jos tunnistus on kunnossa.
        if(isset($_SESSION[Sessio::$tunnistus])) {
            if ($_SESSION[Sessio::$tunnistus]  === Sessio::$tunnistus_ok){
                $this->omaid = $_SESSION[Sessio::$omaid];
            }
        }

        // Tämä voi olla kuka tahansa henkilö, ei välttämättä käyttäjä:
        // Huom: Varo, ettei sama ei-määritelty -arvo omaid-muuttujalle
        // avaa porsaanreikiä valtuusrajoituksiin.
        $this->henkilo_id = 
                isset($_REQUEST[Kayttajakontrolleri::$name_henkilo_id]) ? 
                        $_REQUEST[Kayttajakontrolleri::$name_henkilo_id]:
                        Henkilo::$MUUTTUJAA_EI_MAARITELTY;


        // Kieli saadaan $_REQUEST['kieli_id']-muuttujasta. Ellei se ole
        // määritelty, katsotaan session muistama ja ellei siellä mitään-> suomi:
        if(isset($_REQUEST[Kielet::$name_kieli_id])){
            $_SESSION[Kielet::$name_kieli_id] = $_REQUEST[Kielet::$name_kieli_id];
            $this->kieli_id = $_REQUEST[Kielet::$name_kieli_id];
        }
        else if (isset($_SESSION[Kielet::$name_kieli_id])){
            $this->kieli_id = $_SESSION[Kielet::$name_kieli_id];
        }
        else{
            $this->kieli_id = Kielet::$SUOMI;
        }
        
        
        /********************* kuvatiedot alku ************************************/
        $this->max_nayttokork_kuva = Kuva::$KUVATALLENNUS_PIENI8_MITTA; // Oletus.
        $this->max_nayttolev_kuva = Kuva::$KUVATALLENNUS_PIENI8_MITTA; // Oletus.
        $this->uusi_kuva = isset($_REQUEST[Kuvakontrolleri::$name_uusi_kuva]) ? 
                                $_REQUEST[Kuvakontrolleri::$name_uusi_kuva]: true;
        $this->ilmoitus_kuva = isset($_REQUEST[Kuvakontrolleri::$name_ilmoitus_kuva])?
                                $_REQUEST[Kuvakontrolleri::$name_ilmoitus_kuva]: "";
        $this->kuvaotsikko_kuva = isset($_REQUEST[Kuvakontrolleri::$name_otsikko_kuva])?
                                $_REQUEST[Kuvakontrolleri::$name_otsikko_kuva]: 
                                Parametrit::$EI_MAARITELTY;
        $this->kuvaselitys_kuva = isset($_REQUEST[Kuvakontrolleri::$name_selitys_kuva])?
                                $_REQUEST[Kuvakontrolleri::$name_selitys_kuva]: 
                                Parametrit::$EI_MAARITELTY;
        $this->vuosi_kuva = isset($_REQUEST[Kuvakontrolleri::$name_vuosi_kuva])? 
                                $_REQUEST[Kuvakontrolleri::$name_vuosi_kuva]: 
                                Parametrit::$EI_MAARITELTY;
        $this->kk_kuva = isset($_REQUEST[Kuvakontrolleri::$name_kk_kuva])? 
                                $_REQUEST[Kuvakontrolleri::$name_kk_kuva]: 
                                Parametrit::$EI_MAARITELTY;
        $this->paiva_kuva = isset($_REQUEST[Kuvakontrolleri::$name_paiva_kuva])? 
                            $_REQUEST[Kuvakontrolleri::$name_paiva_kuva]: 
                                Parametrit::$EI_MAARITELTY;
        $this->src_kuva = isset($_REQUEST[Kuvakontrolleri::$name_src_kuva])? 
                            $_REQUEST[Kuvakontrolleri::$name_src_kuva]: 
                                Parametrit::$EI_MAARITELTY;
        
        $this->tiedostonimi_kuva = isset($_REQUEST[Kuvakontrolleri::$name_tiedostonimi_kuva])? 
                            $_REQUEST[Kuvakontrolleri::$name_tiedostonimi_kuva]: 
                                Parametrit::$EI_MAARITELTY;
        
        $this->id_kuva = isset($_REQUEST[Kuvakontrolleri::$name_id_kuva])?
                            $_REQUEST[Kuvakontrolleri::$name_id_kuva]: 
                            Kuva::$MUUTTUJAA_EI_MAARITELTY;

        /* Haetaan mahdollisesti ladattu kuva: */
        $this->ladattu_kuva = isset($_FILES[Kuvakontrolleri::$name_ladattu_kuva]) ?
                                    $_FILES[Kuvakontrolleri::$name_ladattu_kuva]: 
                                    Parametrit::$EI_MAARITELTY;

        /********************* kuvatiedot loppu ************************************/
       

        /********************* henkilötiedot alku *********************************/
        // Lisätään ihmisen tiedot:
        $this->etun = isset($_REQUEST[Kayttajakontrolleri::$name_etunimi]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_etunimi]: "";
        $this->sukun = isset($_REQUEST[Kayttajakontrolleri::$name_sukunimi]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_sukunimi]: "";
        $this->lempin = isset($_REQUEST[Kayttajakontrolleri::$name_lempinimi]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_lempinimi]: "";
        $this->komm = isset($_REQUEST[Kayttajakontrolleri::$name_kommentti]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_kommentti]: "";
        $this->uusktunnus = isset($_REQUEST[Kayttajakontrolleri::$name_uusktunnus]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_uusktunnus]: "";
        $this->uussalasana = isset($_REQUEST[Kayttajakontrolleri::$name_uusisalasana]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_uusisalasana]: "";
        $this->salavahvistus = isset(
                            $_REQUEST[Kayttajakontrolleri::$name_salasanavahvistus]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_salasanavahvistus]: "";
        $this->eosoite = isset($_REQUEST[Kayttajakontrolleri::$name_eosoite]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_eosoite]: "";
        $this->osoite = isset($_REQUEST[Kayttajakontrolleri::$name_osoite]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_osoite]: "";
        $this->puhelin = isset($_REQUEST[Kayttajakontrolleri::$name_puhelin]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_puhelin]: "";
        $this->online = isset($_REQUEST[Kayttajakontrolleri::$name_online]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_online]: 0;
        $this->asuinmaa = isset($_REQUEST[Kayttajakontrolleri::$name_asuinmaa]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_asuinmaa]: 
                            Maat::$suomi;
        
        $this->kieli_henkilo = isset($_REQUEST[Kayttajakontrolleri::$name_kieli]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_kieli]: 
                            Kielet::$SUOMI;
        
        $this->uudet_valtuudet =
                    isset($_REQUEST[Kayttajakontrolleri::$name_valtuudet]) ? 
                            $_REQUEST[Kayttajakontrolleri::$name_valtuudet]:
                            Valtuudet::$PANNASSA;
        
        $this->henkiloilmoitus = "";
        /********************* henkilötiedot loppu ****************************/

        //====================== kirjautuminen =================================
        $this->kirjaudu_ktunnus = 
                        isset($_POST[Kayttajakontrolleri::$name_ktunnus]) ? 
                        $_POST[Kayttajakontrolleri::$name_ktunnus]: 
                        Parametrit::$EI_MAARITELTY;
        $this->kirjaudu_salis = 
                        isset($_POST[Kayttajakontrolleri::$name_salis]) ? 
                        $_POST[Kayttajakontrolleri::$name_salis]: 
                        Parametrit::$EI_MAARITELTY;
        //======================================================================
       
        //======================= poppoo alku ==================================
        $this->poppoo_nimi = 
                isset($_REQUEST[Kayttajakontrolleri::$name_poppoonimi]) ? 
                        $_REQUEST[Kayttajakontrolleri::$name_poppoonimi]: "";
        $this->poppoo_kayttajatunnus = 
                isset($_REQUEST[Kayttajakontrolleri::$name_poppootunnus]) ? 
                    $_REQUEST[Kayttajakontrolleri::$name_poppootunnus]: "";
        $this->poppoo_kommentti = 
                isset($_REQUEST[Kayttajakontrolleri::$name_poppookommentti]) ? 
                    $_REQUEST[Kayttajakontrolleri::$name_poppookommentti]: "";
        $this->poppoo_maksimikoko = 
                isset($_REQUEST[Kayttajakontrolleri::$name_poppoomaxikoko]) ? 
                        $_REQUEST[Kayttajakontrolleri::$name_poppoomaxikoko]: 
                        Parametrit::$EI_MAARITELTY;
        
        $this->poppootunnusvahvistus = 
                isset($_REQUEST[Kayttajakontrolleri::$name_poppootunnusvahvistus]) ? 
                        $_REQUEST[Kayttajakontrolleri::$name_poppootunnusvahvistus]: 
                        Parametrit::$EI_MAARITELTY;
        
        // id haetaan monipuolisesti:
        if(isset($_REQUEST[Kayttajakontrolleri::$name_poppoon_id])){
            $_SESSION[Sessio::$poppoon_id] = 
                            $_REQUEST[Kayttajakontrolleri::$name_poppoon_id];
            $this->poppoon_id = $_REQUEST[Kayttajakontrolleri::$name_poppoon_id];
        }
        else if (isset($_SESSION[Sessio::$poppoon_id])){
            $this->poppoon_id = $_SESSION[Sessio::$poppoon_id];
        }
        else{
            $this->poppoon_id = Poppoo::$MUUTTUJAA_EI_MAARITELTY;
        }
        
        $this->poppooilmoitus = ""; // Lomakeilmoitus tms.
        
        // Admin: henkilön siirto poppoosta toiseen:
        $this->poppoon_id_muokkaus = 
                isset($_REQUEST[Kayttajakontrolleri::$name_admin_henkilon_poppoo_id]) ? 
                        $_REQUEST[Kayttajakontrolleri::$name_admin_henkilon_poppoo_id]: 
                        Parametrit::$EI_MAARITELTY;
        //======================= poppoo loppu =================================
        
        //======================= Lajiluokka alku ==============================
        $this->id_lj = isset($_REQUEST[Kontrolleri_lj::$name_id_lj]) ? 
                            $_REQUEST[Kontrolleri_lj::$name_id_lj]: 
                            Lajiluokka::$MUUTTUJAA_EI_MAARITELTY; 
        $this->siirtokohde_id_lj = 
                    isset($_REQUEST[Kontrolleri_lj::$name_siirtokohde_id_lj]) ? 
                            $_REQUEST[Kontrolleri_lj::$name_siirtokohde_id_lj]: 
                            Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;

        // Yläluokka_id otetaan sessiomuuttujaan talteen:
        if(isset($_REQUEST[Kontrolleri_lj::$name_ylaluokka_id_lj])){
            $_SESSION[Kontrolleri_lj::$name_ylaluokka_id_lj] = 
                    $_REQUEST[Kontrolleri_lj::$name_ylaluokka_id_lj];
            $this->ylaluokka_id_lj = 
                    $_REQUEST[Kontrolleri_lj::$name_ylaluokka_id_lj];
        }
        else if (isset($_SESSION[Kontrolleri_lj::$name_ylaluokka_id_lj])){
            $this->ylaluokka_id_lj = 
                        $_SESSION[Kontrolleri_lj::$name_ylaluokka_id_lj];
        }
        else{
            $this->ylaluokka_id_lj = -1;   // LINNUT? (-1 -> kaikki)
        }

        $this->nimi_latina_lj = 
                isset($_REQUEST[Kontrolleri_lj::$name_nimi_latina_lj]) ?
                        $_REQUEST[Kontrolleri_lj::$name_nimi_latina_lj]: "";
        
        $this->taulukkosolun_id = 
            isset($_REQUEST[Kontrolleri_lj::$name_taulukkosolun_id]) ? 
                    $_REQUEST[Kontrolleri_lj::$name_taulukkosolun_id]: 
                    Parametrit::$EI_MAARITELTY;

        //======================= Lajiluokka loppu =============================
        
        //======================= Kuvaukset alku ===============================
        
        // Kuvausmuuttujat:
        $this->id_kuv = 
                    isset($_REQUEST[Kontrolleri_lj::$name_id_kuv]) ? 
                                $_REQUEST[Kontrolleri_lj::$name_id_kuv]: 
                                Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        $this->lajiluokka_id_kuv = 
                    isset($_REQUEST[Kontrolleri_lj::$name_lajiluokka_id_kuv]) ? 
                            $_REQUEST[Kontrolleri_lj::$name_lajiluokka_id_kuv]: 
                            Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;
        $this->nimi_kuv = 
                    isset($_REQUEST[Kontrolleri_lj::$name_nimi_kuv]) ? 
                            $_REQUEST[Kontrolleri_lj::$name_nimi_kuv]: 
                            Parametrit::$EI_MAARITELTY;
        $this->kuv_kuv = 
                    isset($_REQUEST[Kontrolleri_lj::$name_kuv_kuv]) ? 
                            $_REQUEST[Kontrolleri_lj::$name_kuv_kuv]: 
                            Parametrit::$EI_MAARITELTY;
        $this->kieli_kuv = 
                    isset($_REQUEST[Kontrolleri_lj::$name_kieli_kuv]) ? 
                            $_REQUEST[Kontrolleri_lj::$name_kieli_kuv]: 
                            Kielet::$SUOMI;
        
        

        //======================= Kuvausmuuttujat loppu ========================
        
        //======================= Havaintomuuttujat alku =======================
        $this->id_hav = isset($_REQUEST[Havaintokontrolleri::$name_id_hav]) ? 
                        $_REQUEST[Havaintokontrolleri::$name_id_hav]: 
                        Parametrit::$EI_MAARITELTY;
        $this->henkilo_id_hav = 
                isset($_REQUEST[Havaintokontrolleri::$name_henkilo_id_hav]) ? 
                $_REQUEST[Havaintokontrolleri::$name_henkilo_id_hav]: 
                Parametrit::$EI_MAARITELTY;
        $this->lajiluokka_id_hav = 
                isset($_REQUEST[Havaintokontrolleri::$name_lajiluokka_id_hav]) ? 
                $_REQUEST[Havaintokontrolleri::$name_lajiluokka_id_hav]: 
                Parametrit::$EI_MAARITELTY;
        $this->vuosi_hav = 
                isset($_REQUEST[Havaintokontrolleri::$name_vuosi_hav]) ? 
                $_REQUEST[Havaintokontrolleri::$name_vuosi_hav]: 
                Aika::anna_nyk_vuoden_nro();
        $this->kk_hav = 
                isset($_REQUEST[Havaintokontrolleri::$name_kk_hav]) ? 
                $_REQUEST[Havaintokontrolleri::$name_kk_hav]: 
                Aika::anna_nyk_kk_nro();
        $this->paiva_hav = 
                isset($_REQUEST[Havaintokontrolleri::$name_paiva_hav]) ? 
                $_REQUEST[Havaintokontrolleri::$name_paiva_hav]: 
                Aika::anna_nyk_paivan_nro();
        $this->paikka_hav = 
                    isset($_REQUEST[Havaintokontrolleri::$name_paikka_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_paikka_hav]: 
                    Parametrit::$EI_MAARITELTY;
        $this->kommentti_hav = 
                    isset($_REQUEST[Havaintokontrolleri::$name_kommentti_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_kommentti_hav]: 
                    Parametrit::$EI_MAARITELTY;
        $this->uusi_hav = 
                    isset($_REQUEST[Havaintokontrolleri::$name_uusi_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_uusi_hav]: 
                    Parametrit::$EI_MAARITELTY;
        $this->max_lkm_hav = 
                    isset($_REQUEST[Havaintokontrolleri::$name_max_lkm_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_max_lkm_hav]: 
                    Havaintojen_nayttomoodi::$havaintojen_max_lkm;
        $this->varmuus_hav = 
                    isset($_REQUEST[Havaintokontrolleri::$name_varmuus_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_varmuus_hav]: 
                    Varmuus::$varma;
        $this->maa_hav =  
                    isset($_REQUEST[Havaintokontrolleri::$name_maa_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_maa_hav]: 
                    Maat::$suomi;
        
        $this->sukupuoli_hav = 
                    isset($_REQUEST[Havaintokontrolleri::$name_sukupuoli_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_sukupuoli_hav]: 
                    Parametrit::$EI_MAARITELTY;
        $this->lkm_hav = 
                    isset($_REQUEST[Havaintokontrolleri::$name_lkm_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_lkm_hav]: 
                    Parametrit::$EI_MAARITELTY;
        
        $this->lisaluokitusvalinnat_hav = // Kaikki lisäluokitukset taulukossa.
                    isset($_REQUEST[Havaintokontrolleri::$name_lisaluokitusvalinnat_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_lisaluokitusvalinnat_hav]: 
                    array();
        
        $this->havaintoalue_hav =  
                isset($_REQUEST[Havaintokontrolleri::$name_havaintoalue_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_havaintoalue_hav]:
                    "Kaikkivaa";
        
        // Monen lajin valintojen tiedot ovat taulukossa:
        $this->lajivalinnat_hav =  
                isset($_REQUEST[Havaintokontrolleri::$name_lajivalinnat_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_lajivalinnat_hav]:
                    array();
        
        // Havaintovalintojen tiedot ovat taulukossa:
        $this->havaintovalinnat_hav =  
                isset($_REQUEST[Havaintokontrolleri::$name_havaintovalinnat_hav]) ?
                    $_REQUEST[Havaintokontrolleri::$name_havaintovalinnat_hav]:
                    array();
        
        $this->puolivuotiskauden_nro_hav =
        isset($_REQUEST[Havaintokontrolleri::$name_puolivuotiskauden_nro_hav]) ? 
            $_REQUEST[Havaintokontrolleri::$name_puolivuotiskauden_nro_hav]: 
            -1000;
        
        $this->lisaluokitusehto_hav =
        isset($_REQUEST[Havaintokontrolleri::$name_lisaluokitusehto_hav]) ? 
            $_REQUEST[Havaintokontrolleri::$name_lisaluokitusehto_hav]: 
            Parametrit::$EI_MAARITELTY;

        
        // Näytettävän_havainnon id haetaan ensisijaisesti sessiomuuttujasta:
        if(isset($_REQUEST[Havaintokontrolleri::$name_naytettavan_id_hav])){
            $_SESSION[Havaintokontrolleri::$name_naytettavan_id_hav] = 
                    $_REQUEST[Havaintokontrolleri::$name_naytettavan_id_hav];
            
            $this->naytettavan_id_hav = 
                    $_REQUEST[Havaintokontrolleri::$name_naytettavan_id_hav];
        }
        else if (isset($_SESSION[Havaintokontrolleri::$name_naytettavan_id_hav])){
            $this->naytettavan_id_hav = 
                    $_SESSION[Havaintokontrolleri::$name_naytettavan_id_hav];
        }
        else{
            $this->naytettavan_id_hav = Havainto::$MUUTTUJAA_EI_MAARITELTY; 
        }

        // Havaintojen näyttömoodi haetaan ensisijaisesti sessiomuuttujasta:
        if(isset($_REQUEST[Havaintokontrolleri::$name_nayttomoodi_hav])){
            $_SESSION[Havaintokontrolleri::$name_nayttomoodi_hav] =
                    $_REQUEST[Havaintokontrolleri::$name_nayttomoodi_hav];
            $this->havaintojen_nayttomoodi =
                    $_REQUEST[Havaintokontrolleri::$name_nayttomoodi_hav];
        }
        else if (isset($_SESSION[Havaintokontrolleri::$name_nayttomoodi_hav])){
            $this->havaintojen_nayttomoodi = 
                    $_SESSION[Havaintokontrolleri::$name_nayttomoodi_hav];
        }
        else{
            $this->havaintojen_nayttomoodi = 
                    Havaintojen_nayttomoodi::$nayta_uusimmat;
        }

        // Havaintojen näyttövuosi haetaan ensisijaisesti sessiomuuttujasta:
        if(isset($_REQUEST[Havaintokontrolleri::$name_nayttovuosi_hav])){
            $_SESSION[Havaintokontrolleri::$name_nayttovuosi_hav] =
                    $_REQUEST[Havaintokontrolleri::$name_nayttovuosi_hav];
            $this->nayttovuosi_hav =
                    $_REQUEST[Havaintokontrolleri::$name_nayttovuosi_hav];
        }
        else if (isset($_SESSION[Havaintokontrolleri::$name_nayttovuosi_hav])){
            $this->nayttovuosi_hav = 
                    $_SESSION[Havaintokontrolleri::$name_nayttovuosi_hav];
        }
        else{   // Perusnäkymä, uusimmat.
            $this->nayttovuosi_hav = Bongausasetuksia::$nayta_oletushavainnot; 
        }
        //======================= Havaintomuuttujat loppu ======================
        //======================= Havaintojaksomuuttujat alku ==================
        $this->id_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_id_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_id_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->henkilo_id_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_henkilo_id_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_henkilo_id_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->alkuaika_sek_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_alkuaika_sek_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_alkuaika_sek_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->alkuaika_min_havjaks_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_alkuaika_min_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_alkuaika_min_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->alkuaika_h_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_alkuaika_h_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_alkuaika_h_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->alkuaika_paiva_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_alkuaika_paiva_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_alkuaika_paiva_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->alkuaika_kk_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_alkuaika_kk_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_alkuaika_kk_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->alkuaika_vuosi_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_alkuaika_vuosi_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_alkuaika_vuosi_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        /*$this->alkuaika_date_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_alkuaika_date_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_alkuaika_date_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->alkuaika_time_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_alkuaika_time_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_alkuaika_time_havjaks]: 
                Parametrit::$EI_MAARITELTY;*/
        $this->kesto_min_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_kesto_min_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_kesto_min_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->kesto_h_havjaks_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_kesto_h_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_kesto_h_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->kesto_vrk_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_kesto_vrk_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_kesto_vrk_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->nimi_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_nimi_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_nimi_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->kommentti_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_kommentti_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_kommentti_havjaks]: 
                Parametrit::$EI_MAARITELTY;
        $this->nakyvyys_havjaks =
            isset($_REQUEST[Havaintokontrolleri::$name_nakyvyys_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_nakyvyys_havjaks]: 
                Nakyvyys::$JULKINEN;
        $this->uusi_havjaks = 
            isset($_REQUEST[Havaintokontrolleri::$name_uusi_havjaks]) ? 
                $_REQUEST[Havaintokontrolleri::$name_uusi_havjaks]: 
                Parametrit::$EI_MAARITELTY;
   
        //================== Havaintojaksomuuttujat loppu ======================
        //================= Havaintojaksolinkkimuuttujat alku ==================
        $this->id_havjakslink =
            isset($_REQUEST[Havaintokontrolleri::$name_id_havjakslink]) ? 
                $_REQUEST[Havaintokontrolleri::$name_id_havjakslink]: 
                Parametrit::$EI_MAARITELTY;
        $this->havainto_id_havjakslink =
            isset($_REQUEST[Havaintokontrolleri::$name_havainto_id_havjakslink]) ? 
                $_REQUEST[Havaintokontrolleri::$name_havainto_id_havjakslink]: 
                Parametrit::$EI_MAARITELTY;
        $this->havaintojakso_id_havjakslink =
            isset($_REQUEST[Havaintokontrolleri::$name_havaintojakso_id_havjakslink]) ? 
                $_REQUEST[Havaintokontrolleri::$name_havaintojakso_id_havjakslink]: 
                Parametrit::$EI_MAARITELTY;
        //================ Havaintojaksolinkkimuuttujat loppu ==================
        
    }
    // Setterit ja getterit (osa vähän turhia..):
    function get_tietokantaolio(){
        return $this->tietokantaolio;
    }

    function get_max_nayttokoko_kuva(){
        return $this->max_nayttokoko_kuva;
    }
    function set_max_nayttokoko_kuva($koko_px){
        $this->max_nayttokoko_kuva = $koko_px;
    }
   
    function get_omaid(){
        return $this->omaid;
    }
    //========================================================================
    function get_kirjaudu_ktunnus(){
        return $this->kirjaudu_ktunnus;
    }
    function get_kirjaudu_salis(){
        return $this->kirjaudu_salis;
    }
    //=========================================================================
    function get_pk_kommenttiteksti(){
        return $this->pk_kommenttiteksti;
    }
    function get_pk_kohdetyyppi(){
        return $this->pk_kohdetyyppi;
    }
    function get_pk_kohde_id(){
        return $this->pk_kohde_id;
    }
    function get_pk_id(){
        return $this->pk_id;
    }
    
    //=============== päätoiminnot =============================================
    function get_havaintotoiminto(){
        return $this->havaintotoiminto;
    }
    
    function get_lajiluokkatoiminto(){
        return $this->lajiluokkatoiminto;
    }
    
    function get_yllapitotoiminto(){
        return $this->yllapitotoiminto;
    }
    
    function get_kayttajatoiminto(){
        return $this->kayttajatoiminto;
    }
    function get_kuvatoiminto(){
        return $this->kuvatoiminto;
    }
   
    

    function get_henkilo_id(){
        return $this->henkilo_id;
    }
    function set_henkilo_id($uusi){
        if(isset($uusi)){
            $this->henkilo_id=$uusi;
            
        }
    }
    function get_kieli_id(){
        return $this->kieli_id;
    }
    /**
     * Asettaa kielen arvon, myös sessiomuuttujaan!
     * @param type $uusi
     */
    function set_kieli_id($uusi){
        if(isset($uusi)){
            $this->kieli_id=$uusi;
            $_SESSION[Kielet::$name_kieli_id] = $uusi;
        }
    }
  
    function get_havaintojen_nayttomoodi(){
        return $this->nayttomoodi_hav;
    }
    function set_havaintojen_nayttomoodi($uusi){
        if(isset($uusi)){
            $this->nayttomoodi_hav=$uusi;
            
        }
    }
    
    function get_havaintojen_nayttovuosi(){
        return $this->nayttovuosi_hav;
    }
    function set_havaintojen_nayttovuosi($uusi){
        if(isset($uusi)){
            $this->nayttovuosi_hav=$uusi;
            
        }
    }
    
    function get_naytettavan_id_hav(){
        return $this->naytettavan_id_hav;
    }
    function set_naytettavan_id_hav($uusi){
        if(isset($uusi)){
            $this->naytettavan_id_hav=$uusi;
        }
    }
    
    /*function get_latauskansio_os(){
        return $this->latauskansio_os;
    }
    function set_latauskansio_os($uusi){
        if(isset($uusi)){
            $this->latauskansio_os=$uusi;
            
        }
    }*/
    function get_uusi_kuva(){
        return $this->uusi_kuva;
    }
    function set_uusi_kuva($uusi){
        if(isset($uusi)){
            $this->uusi_kuva=$uusi;
            
        }
    }
    function get_ilmoitus_kuva(){
        return $this->ilmoitus_kuva;
    }
    function set_ilmoitus_kuva($uusi){
        if(isset($uusi)){
            $this->ilmoitus_kuva=$uusi;
            
        }
    }
    function get_kuvaotsikko_kuva(){
        return $this->kuvaotsikko_kuva;
    }
    function set_kuvaotsikko_kuva($uusi){
        if(isset($uusi)){
            $this->kuvaotsikko_kuva=$uusi;
            
        }
    }
    
    
    
    function get_kuvaselitys_kuva(){
        return $this->kuvaselitys_kuva;
    }
    function set_kuvaselitys_kuva($uusi){
        if(isset($uusi)){
            $this->kuvaselitys_kuva=$uusi;
            
        }
    }
    function get_vuosi_kuva(){
        return $this->vuosi_kuva;
    }
    function set_vuosi_kuva($uusi){
        if(isset($uusi)){
            $this->vuosi_kuva=$uusi;
            
        }
    }
    function get_kk_kuva(){
        return $this->kk_kuva;
    }
    function set_kk_kuva($uusi){
        if(isset($uusi)){
            $this->kk_kuva=$uusi;
            
        }
    }
    function get_paiva_kuva(){
        return $this->paiva_kuva;
    }
    function set_paiva_kuva($uusi){
        if(isset($uusi)){
            $this->paiva_kuva=$uusi;
            
        }
    }
    function get_id_kuva(){
        return $this->id_kuva;
    }
    function set_id_kuva($uusi){
        if(isset($uusi)){
            $this->id_kuva=$uusi;
            
        }
    }
    function get_kohde_kuva(){
        return $this->kohde_kuva;
    }
    function set_kohde_kuva($uusi){
        if(isset($uusi)){
            $this->kohde_kuva=$uusi;
            
        }
    }

    function get_id_kohde_kuva(){
        return $this->id_kohde_kuva;
    }
    function set_id_kohde_kuva($uusi){
        if(isset($uusi)){
            $this->id_kohde_kuva=$uusi;
            
        }
    }
  
    function get_etun(){
        return $this->etun;
    }
    function set_etun($uusi){
        if(isset($uusi)){
            $this->etun=$uusi;
            
        }
    }
    function get_sukun(){
        return $this->sukun;
    }
    function set_sukun($uusi){
        if(isset($uusi)){
            $this->sukun=$uusi;
            
        }
    }
    function get_lempin(){
        return $this->lempin;
    }
    function set_lempin($uusi){
        if(isset($uusi)){
            $this->lempin=$uusi;
            
        }
    }
    function get_svuosi(){
        return $this->svuosi;
    }
    function set_svuosi($uusi){
        if(isset($uusi)){
            $this->svuosi=$uusi;
            
        }
    }
    function get_skk(){
        return $this->skk;
    }
    function set_skk($uusi){
        if(isset($uusi)){
            $this->skk=$uusi;
            
        }
    }
    function get_spaiva(){
        return $this->spaiva;
    }
    function set_spaiva($uusi){
        if(isset($uusi)){
            $this->spaiva=$uusi;
            
        }
    }
    function get_komm(){
        return $this->komm;
    }
    function set_komm($uusi){
        if(isset($uusi)){
            $this->komm=$uusi;
            
        }
    }
    function get_uusktunnus(){
        return $this->uusktunnus;
    }
    function set_uusktunnus($uusi){
        if(isset($uusi)){
            $this->uusktunnus=$uusi;
            
        }
    }
    function get_uussalasana(){
        return $this->uussalasana;
    }
    function set_uussalasana($uusi){
        if(isset($uusi)){
            $this->uussalasana=$uusi;
            
        }
    }
    function get_salavahvistus(){
        return $this->salavahvistus;
    }
    function set_salavahvistus($uusi){
        if(isset($uusi)){
            $this->salavahvistus=$uusi;
            
        }
    }
    function get_eosoite(){
        return $this->eosoite;
    }
    function set_eosoite($uusi){
        if(isset($uusi)){
            $this->eosoite=$uusi;
            
        }
    }
    /*function get_valtuudet(){
        return $this->valtuudet;
    }
    function set_valtuudet($uusi){
        if(isset($uusi)){
            $this->valtuudet=$uusi;
            
        }
    }*/
    function get_tallennuspalaute(){
        return $this->tallennuspalaute;
    }
    function set_tallennuspalaute($uusi){
        if(isset($uusi)){
            $this->tallennuspalaute=$uusi;
            
        }
    }
    
    function get_poppootunnusvahvistus(){
        return $this->poppootunnusvahvistus;
    }
    function set_poppootunnusvahvistus($uusi){
        if(isset($uusi)){
            $this->poppootunnusvahvistus=$uusi;
            
        }
    }
    
    function get_poppoon_id_muokkaus(){
        return $this->poppoon_id_muokkaus;
    }
    function set_poppoon_id_muokkaus($uusi){
        $this->poppoon_id_muokkaus = $uusi;
    }
}
?>