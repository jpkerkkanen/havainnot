<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kuvakontrolleri
 *
 * @author J-P
 */
class Kuvakontrolleri extends Kontrolleripohja {

    private $kuvanakymat;
    // Lomakkeen name-muuttujan arvo kuvatoiminnoille.
    public static $kuvatoimintonimi = "kuvatoiminto";
    
    /* Kuvakansion osoite (tällä kansiotasolla), kun kaikkien poppoiden kuvat
     * menevät samaan kansioon.
     */
    public static $kuvakansion_osoite = "kuvat/lataukset";
    public static $kansion_osoite_testikuvat = 
                "kuvat/testaus/testikuvatiedostot/testilataukset";
    
    /** Kuvamuuttujien name-arvot:*/
    public static $name_ladattu_kuva = "ladattu_kuva";
    public static $name_uusi_kuva = "uusi_kuva";
    public static $name_ilmoitus_kuva = "ilmoitus_kuva";
    public static $name_otsikko_kuva = "otsikko_kuva";
    public static $name_selitys_kuva = "selitys_kuva";
    public static $name_vuosi_kuva = "vuosi_kuva";
    public static $name_kk_kuva = "kk_kuva";
    public static $name_paiva_kuva = "paiva_kuva";
    public static $name_src_kuva = "src_kuva";
    public static $name_tiedostonimi_kuva = "tiedostonimi_kuva";
    public static $name_id_kuva = "id_kuva";
    
    // Ikkunan korkeus ja leveys voivat liittyä muuhunkin kuin kuviin:
    public static $name_ikkunan_kork = "ikkunan_kork";
    public static $name_ikkunan_lev = "ikkunan_lev";

    
    public function __construct($tietokantaolio, $parametriolio) {
        parent::__construct($tietokantaolio, $parametriolio);
        
        $this->kuvanakymat = new Kuvanakymat();
    }
    
    /**
     * 
     * @return Kuvanakymat
     */
    public function get_kuvanakymat(){
        return $this->kuvanakymat;
    }
    
    /**
     * Toteuttaa mahdollisimman ison kuvan näyttämisen. Kuva sujautetaan
     * divin sisälle, jossa yläreunassa on kuvaan liittyviä painikkeita.
     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_nayta_isokuva(&$palauteolio){
        
        $poisto_oikeus = false;
        
        $para = $this->get_parametriolio();
         
        // Leveydestä vähennetään pikakommenttisarake
        // 214 + 4px (reunaa vähän jätetään näkyviin).
        $sivupalkit = 218;
        $painikepalkki_kork = 30;
            
        $para->max_nayttolev_kuva = $para->ikkunan_leveys-$sivupalkit;   
        $para->max_nayttokork_kuva = $para->ikkunan_korkeus-$painikepalkki_kork;

        // Sitten vain toteutetaan kuvannäyttö, joka jo toteutettu:
        $this->toteuta_nayta_pelkka_kuva($palauteolio);
        $kuvahtml = $palauteolio->get_sisalto();
        
        // Tarkistetaan oikeudet kuvan poistoon ja kenties muokkaukseen:
        $id_kuva = $this->get_parametriolio()->id_kuva;
        $kuva = new Kuva($id_kuva, $this->get_tietokantaolio());
        
        // Jos käyttäjällä on admin-oikeudet tai jos käyttäjä on kuvan tallentaja:
        if(($this->get_kayttaja()->hae_valtuudet() === Valtuudet::$HALLINTA) || 
            ($this->get_kayttaja()->get_id() === 
                            $kuva->get_arvo(Kuva::$SARAKENIMI_HENKILO_ID))){
            
            $poisto_oikeus = true;
        }
        
        $html = $this->get_kuvanakymat()->
                            nayta_kuvakehys_iso($kuvahtml, 
                                            $id_kuva,
                                            $this->get_parametriolio()->id_hav,
                                            $poisto_oikeus);
        
        $palauteolio->set_sisalto($html);
    }
    
    /**
    * 
    * @param Palaute $palauteolio
    */
    public function toteuta_nayta_poistovarmistus(&$palauteolio) {
        
        $id_kuva = $this->get_parametriolio()->id_kuva;
        $id_hav = $this->get_parametriolio()->id_hav;
        
        $kuva = new Kuva($id_kuva, $this->get_tietokantaolio());
        $havainto = new Havainto($id_hav, $this->get_tietokantaolio());
        
        $palauteolio->set_sisalto($this->kuvanakymat->
                                    nayta_poistovarmistus_kuva($kuva, $havainto));
    }
    
    /**
     * Toteuttaa havaintokuvalinkin poiston tietokannasta (kuvatiedostot
     * ja mahdolliset muut linkit säilyvät ennallaan).
     * 
     * Palauteolioon syötetään vain ilmoituksia. Muu näyttö pitää hoitaa
     * muualla.
     * 
     * Onnistumisesta annetaan vakiopalaute palauteolioon.
     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_poista_havaintokuvalinkki(&$palauteolio) {

        $param = $this->get_parametriolio();
        $id_kuva = $param->id_kuva;
        $kuva = new Kuva($id_kuva, $this->get_tietokantaolio());
        
        $id_hav = $param->id_hav;
        $havainto = new Havainto($id_hav, $this->get_tietokantaolio());
        $onnistui = true;
        
        if($kuva->olio_loytyi_tietokannasta && $havainto->olio_loytyi_tietokannasta){
            $havaintokuvalinkit = $havainto->hae_havaintokuvalinkit();
        
            // Haetaan kuvan ja havainnon välinen linkki (pitäisi olla yksi):
            $varalaskuri = 0;
            foreach ($havaintokuvalinkit as $havkuvlinkki) {
                if($havkuvlinkki instanceof Havaintokuvalinkki){
                    if($havkuvlinkki->
                            get_arvo(Havaintokuvalinkki::$sarakenimi_kuva_id)===
                        $id_kuva){

                        if($havkuvlinkki->poista() === 
                                        Havaintokuvalinkki::$OPERAATIO_ONNISTUI){
                            $varalaskuri++;
                        } else{
                            $this->lisaa_virheilmoitus(Kuvatekstit::
                                $ilm_kuva_virhe_havaintokuvalinkin_poistossa);
                            $onnistui = false;
                        }
                    }
                }
            }
            
            $this->lisaa_kommentti($varalaskuri." ".
                        Kuvatekstit::$ilm_kuva_havaintokuvalinkkia_poistettu);
            
            if($onnistui){
                $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            } else{
                $palauteolio->set_onnistumispalaute(Palaute::$VIRHE);
            }
            
        } else{
            $this->lisaa_virheilmoitus(Kuvatekstit::
                        $ilm_kuva_virhe_havaintokuvalinkin_poistossa_id_huono);
            $palauteolio->set_onnistumispalaute(Palaute::$VIRHE);
        }
        
        $palauteolio->set_ilmoitus($this->tulosta_kaikki_ilmoitukset());
    }
    
    /**
     * Toteuttaa kuvan poiston tietokannasta sekä kuvatiedostojen poiston
     * kuvakansiosta (sekä varsinainen kuva, että kaikki minitiedostot).
     * 
     * Palauteolioon syötetään vain ilmoituksia. Muu näyttö pitää hoitaa
     * muualla.
     * 
     * Onnistumisesta annetaan vakiopalaute palauteolioon.
     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_poista(&$palauteolio) {

        $param = $this->get_parametriolio();
        $poistettavan_id = $param->id_kuva;
        //$poppoon_id = $param->poppoon_id;
        $latauskansio = Kuvakontrolleri::$kuvakansion_osoite;
        
        $kuva = new Kuva($poistettavan_id, $this->get_tietokantaolio());
        $tiedostonimi = $kuva->get_arvo(Kuva::$SARAKENIMI_TIEDOSTONIMI);
        $src = $kuva->get_arvo(Kuva::$SARAKENIMI_SRC);
        
        // Otetaan id talteen:
        $kuva_id = $kuva->get_id();
        
        // Poistoyritys:
        $palaute = $kuva->poista();
        
        // Jos kuvantietojen poisto tietokannasta onnistui, poistetaan
        // myös orvoksi jäänyt kuvatiedosto ja sen kaikki pienennökset:
        if($palaute === Kuva::$OPERAATIO_ONNISTUI){

            // Tämä tuhoaa kuvatiedoston lopullisesti:
            $poistolaskuri = 0;
            
            // Varmistetaan ensin, että tiedosto on olemassa:
            if(file_exists($src)){
                $todellinen_poisto = unlink($src);

                // Testataan poiston onnistuminen:
                if(!$todellinen_poisto){
                    $this->lisaa_virheilmoitus(
                            Kuvatekstit::$ilm_kuva_virhe_kuvatiedoston_poistossa);
                    $palaute = Kuva::$VIRHE;
                } else{
                    $poistolaskuri++;
                }
            } else{
                $this->lisaa_virheilmoitus(
                        Kuvatekstit::$ilm_kuva_virhe_tied_osoite_ei_kelpaa);
                $palaute = Kuva::$VIRHE;
            }
            
            // Haetaan poistettavien minikuvatiedostojen osoitteet:
            $kohdeos_minikuvat = 
                    Kuvakontrolleri::hae_minikuvien_tied_osoitteet($latauskansio, 
                                                                $tiedostonimi);
            
            // Poistetaan pienennetyt kuvatiedostot:
            foreach ($kohdeos_minikuvat as $minikohdeosoite) {
                if(file_exists($minikohdeosoite)){
                    if(unlink($minikohdeosoite)){
                        $poistolaskuri++;
                    } else{
                        $this->lisaa_virheilmoitus(
                            Kuvatekstit::$ilm_kuva_virhe_kuvatiedoston_poistossa);
                        $palaute = Kuva::$VIRHE;
                    }
                } else{
                    $this->lisaa_virheilmoitus(
                            Kuvatekstit::$ilm_kuva_virhe_tied_osoite_ei_kelpaa);
                    $palaute = Kuva::$VIRHE;
                }
            }
            
            // Tarkistetaan vielä, että kaikki tiedostot on poistettu:
            if($poistolaskuri === (sizeof($kohdeos_minikuvat)+1)){
                $this->lisaa_kommentti(Kuvatekstit::$ilm_kuva_poisto_ok);
                $palauteolio->
                        get_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                
            } else{
                $palaute = Kuva::$VIRHE;
                $this->lisaa_virheilmoitus(
                        Kuvatekstit::$ilm_kuva_tied_poisto_ei_ok_poistettu_lkm.
                        " ".$poistolaskuri);
            }
            
            //==================================================================
            // Poistetaan lopuksi kaikki kuvaan liittyvät pikakommentit:
            $poistetut_pikakommentit_lkm = 
                Pikakommenttikontrolleri::
                            poista_pikakommentit($this->get_tietokantaolio(), 
                                                Pikakommentti::$KOHDE_KUVA_BONGAUS, 
                                                $kuva_id);
                $this->lisaa_kommentti($poistetut_pikakommentit_lkm." ".
                        Pikakommenttitekstit::$ilmoitus_pikakommenttia_poistettu.".");
            //==================================================================
            
        } else{
            $this->lisaa_virheilmoitus(Kuvatekstit::$ilm_kuva_poisto_ei_ok);
            $palaute = Kuva::$VIRHE;
        }
        
        // Jos jossakin tullut virhe, annetaan palautetta palauteoliolle.
        if($palaute === Kuva::$VIRHE){
            $palauteolio->get_onnistumispalaute(
                                    Palaute::$ONNISTUMISPALAUTE_VIRHE_POISTO);
        }
        
        // Tallennetaan vielä ilmoitukset palauteolioon:
        $palauteolio->lisaa_kommentti($this->tulosta_kaikki_ilmoitukset());
        
        // Aktiivisuusmerkintä:
        $aktiivisuuslaji = Aktiivisuus::$KUVA_POISTO;
        $this->get_kayttaja()->paivita_aktiivisuus($aktiivisuuslaji);
    }

    /**
    * Kuvan muokkauksen tallennus. Huom: lajiluokka ei ole suoraan kuvan
    * ominaisuus, mutta jos sitä voi muuttaa, pitää samalla muuttaa myös samaan
    * kuvaan viittaavien havaintojen lajiluokat! Vähän monimutkaista kyllä, joten
    * ehkä tässä vaiheessa lajiluokkaa ei voi muuttaa.
    * @param Palaute $palauteolio
    */
    public function toteuta_tallenna_muokkaus(&$palauteolio) {
        
        // Aktiivisuusmerkintä:
        $aktiivisuuslaji = Aktiivisuus::$KUVAN_;
        $this->kayttaja->paivita_aktiivisuus($aktiivisuuslaji);
    }

    /**
    * 
    * @param Palaute $palauteolio
    */
    public function toteuta_tallenna_uusi(&$palauteolio) {
        
        $valitut = $this->get_parametriolio()->havaintovalinnat_hav;
        $omaid = $this->get_parametriolio()->get_omaid();
        $tallentaja = new Henkilo($omaid, $this->get_tietokantaolio());
        
        //======================== SECURITY ====================================
        // Ne havainnot, jotka täyttävät valtuusehdot:
        /* @var $turvavalinnat array */
        $turvavalinnat = 
            Havaintokontrolleri::
                poimi_valituista_havainnoista_mahdolliset($valitut, 
                                                    $omaid, 
                                                    $this->get_tietokantaolio());
        //======================================================================
        
        if(empty($valitut)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }
        else if(empty($turvavalinnat )){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_kelvollisia_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }
        else{
           
            // Antaa sen kansion suhteellisen tiedostopolun,
            // jonne ladattu tiedosto on tarkoitus siirtää pysyvää sijoitusta varten.
            $latauskansio = Kuvakontrolleri::$kuvakansion_osoite;

            // Lyhennysmerkintä:
            $para = $this->get_parametriolio();

            // Haetaan ladattu kuvatiedosto:
            $ladattu_kuva = $this->get_parametriolio()->ladattu_kuva;

            $uusi = new Kuva(Kuva::$MUUTTUJAA_EI_MAARITELTY, 
                                $this->get_tietokantaolio(), 
                                $ladattu_kuva);
            
            // Tarkistetaan ladattu tiedosto ennen kuin lähdetään pienentelemään:
            $tarkistus_ok = true;
            try{
                Kuva::lataustarkistus_kuva($ladattu_kuva);
            }
            catch (Exception $poikkeus){
                $tarkistus_ok = false;
                $this->lisaa_virheilmoitus($poikkeus->getMessage());
            }

            // Edetään vain, jos lataustarkistus on kunnossa.
            if($tarkistus_ok){

                // Haetaan tiedot, jotka voimassa, ellei kuvaa tarvitse
                // kutistaa.
                $kuvatietotaulukko = getImageSize($ladattu_kuva['tmp_name']);
                $leveys = $kuvatietotaulukko[0];
                $kork = $kuvatietotaulukko[1];

                // Tiedostotunnisteet:
                $tunniste = $kuvatietotaulukko[2];  //gif = 1/jpg = 2/png = 3/...
                if($tunniste === IMAGETYPE_GIF){
                    $tunniste = "gif";
                } else if($tunniste === IMAGETYPE_JPEG){
                    $tunniste = "jpg";
                } else if($tunniste === IMAGETYPE_PNG){
                    $tunniste = "png";
                } else{
                    $this->lisaa_virheilmoitus(
                    Kuvatekstit::$kuvalomake_virheilm_tiedtunniste_vaara);
                }
                
                
                //$html_lev_ja_kork = $kuvatietotaulukko[3];  // "height='333' width='455'"*/
                $koko = $ladattu_kuva['size'];  // Koko tavuina (ilm.)
                
                $poppoon_id = $this->get_parametriolio()->poppoon_id;
                
                // Kuvan nimeen lisätään p+poppoon_id ja tallennusaika sekunteina, 
                // jottei duplikaatteja syntyisi. Samannimisiä kuvia voi kuitenkin tulla.
                // Kuvan tiedostonimeä ei muuteta.
                $tiedostonimi = "p".$poppoon_id."_".time()."_".$ladattu_kuva['name'];

                

                // Tiedostopolut minikuviin: taulukko auttaa käsittelyä:
                $kohdeos_minikuvat = 
                        $this->hae_minikuvien_tied_osoitteet($latauskansio, 
                                                            $tiedostonimi);

                $minikuvien_maxmitat = Kuva::get_minikuvakoot();

                // Lopuksi lisätään kansion osoite mukaan:
                $kohdeosoite = $latauskansio."/".$tiedostonimi;

                // IDEA: PIENENNYS 1600 -> 1000 -> 800 ->600 -> 400 -> 300 -> 200 -> 100 -> 50 
                // NIIN MENNEE NOPSEMMIN. KUVALAATU SÄILYY IHAN OK!
                /******************************************************************/
                /******************************************************************/

                /* TÄSSÄ KUVAN KOKO TARKISTETAAN JA KUVA PIENENNETÄÄN, ETTEI
                 * ÄLYTTÖMIÄ TULE. YLI 300 KT:N KUVAT PIENENNETÄÄN 1600 PIKSELIN
                 * KOKOON, JOLLOIN MYÖS TIEDOSTOKOKO YLEENSÄ PIENENTYY. */
                if($koko > Kuva::$KUVALATAUS_RAJAKOKO){
                    $max_mitta = Kuva::$KUVATALLENNUS_PIENENNOSMITTA;
                    if(Kuva::muuta_kuvan_koko($ladattu_kuva['tmp_name'],$max_mitta,
                                                                    $kohdeosoite,
                                                                    75)){

                        // Haetaan pienennetyn kuvan uudet tiedot:
                        $kuvatietotaulukko = getImageSize($kohdeosoite);
                        $leveys = $kuvatietotaulukko[0];
                        $kork = $kuvatietotaulukko[1];

                        // Tiedostotunniste:
                        $tunniste = $kuvatietotaulukko[2];  //gif = 1/jpg = 2/png = 3/...
                        if($tunniste === IMAGETYPE_GIF){
                            $tunniste = "gif";
                        } else if($tunniste === IMAGETYPE_JPEG){
                            $tunniste = "jpg";
                        } else if($tunniste === IMAGETYPE_PNG){
                            $tunniste = "png";
                        } else{
                            $this->lisaa_virheilmoitus(
                            Kuvatekstit::$kuvalomake_virheilm_tiedtunniste_vaara);
                        }
                        
                        $koko = filesize($kohdeosoite);  //
                    }
                    else{   // Ellei onnistu, tallennetaan alkuperäinen:
                        $kuvatiedosto = $ladattu_kuva['tmp_name'];
                        move_uploaded_file($kuvatiedosto,$kohdeosoite);
                    }
                }
                else{   // Ellei pienennöstarvetta ole.
                    $kuvatiedosto = $ladattu_kuva['tmp_name'];
                    move_uploaded_file($kuvatiedosto,$kohdeosoite);
                }

                // Tehdään sitten pienennökset, aina edellisestä pienennöksestä:
                $index = 0;
                $virhelaskuri = 0;
                $aloitusosoite = $kohdeosoite; // Tästä pienennettävä haetaan.

                foreach ($kohdeos_minikuvat as $minikohdeosoite) {
                    $max_mitta = $minikuvien_maxmitat[$index];

                    if(!Kuva::muuta_kuvan_koko($aloitusosoite,$max_mitta,
                                                $minikohdeosoite, 75)){

                        $virhelaskuri++;
                    }

                    // Tehdään seuraava pienennös jo pienennetystä:
                    $aloitusosoite = $kohdeos_minikuvat[$index];
                    $index++;
                }

                // Virheistä lisätään virheilmoitus:
                if($virhelaskuri > 0){
                    $this->lisaa_virheilmoitus(
                        Kuvatekstit::$ilm_kuva_uusi_minikuvatallennus_virh_lkm.
                        $virhelaskuri);
                }

                // Asetetaan arvot oliolle:
                $uusi->set_arvo($leveys, Kuva::$SARAKENIMI_LEVEYS);
                $uusi->set_arvo($kork, Kuva::$SARAKENIMI_KORKEUS);
                $uusi->set_arvo($tunniste, Kuva::$SARAKENIMI_TIEDOSTOTUNNUS);
                $uusi->set_arvo($koko, Kuva::$SARAKENIMI_TIEDOSTOKOKO);
                
                $uusi->set_arvo($para->get_omaid(), Kuva::$SARAKENIMI_HENKILO_ID);
                $uusi->set_arvo($para->kk_kuva, Kuva::$SARAKENIMI_KK);
                $uusi->set_arvo($para->paiva_kuva, Kuva::$SARAKENIMI_PAIVA);
                $uusi->set_arvo($para->vuosi_kuva, Kuva::$SARAKENIMI_VUOSI);
                $uusi->set_arvo($para->kuvaotsikko_kuva, Kuva::$SARAKENIMI_KUVAOTSIKKO);

                $uusi->set_arvo($para->kuvaselitys_kuva, Kuva::$SARAKENIMI_KUVASELITYS);
                $uusi->set_arvo(Parametrit::$EI_MAARITELTY, Kuva::$SARAKENIMI_MUUTOSHETKI_SEK);
                $uusi->set_arvo(time(), Kuva::$SARAKENIMI_TALLENNUSHETKI_SEK);
                $uusi->set_arvo($kohdeosoite, Kuva::$SARAKENIMI_SRC);
                $uusi->set_arvo($tiedostonimi, Kuva::$SARAKENIMI_TIEDOSTONIMI);

                $tallennuspalaute = $uusi->tallenna_uusi();

                if($tallennuspalaute === Kuva::$OPERAATIO_ONNISTUI){

                    // Jos onnistui, lisätään linkit havaintojen ja kuvan välille, 
                    // sekä lajin (mahdollisesti lajien) ja kuvan välille.
                    // Samassa kuvassahan voi olla useampi laji.
                    $havkuvalinkkilaskuri = 0;
                    $lajikuvalinkkilaskuri = 0;
                    $virheilm = "";
                    foreach ($turvavalinnat as $havainto) {
                        if($havainto instanceof Havainto){
                            $id_havainto = $havainto->get_id();
                            $id_lj = $havainto->get_arvo(
                                        Havainto::$SARAKENIMI_LAJILUOKKA_ID);
                            
                            if($uusi->lisaa_havaintokuvalinkki($id_havainto) ===
                                Havaintokuvalinkki::$OPERAATIO_ONNISTUI){
                                $havkuvalinkkilaskuri++;
                            } else{
                                $virheilm .= $uusi->tulosta_virheilmoitukset();
                            }
                            if($uusi->lisaa_lajikuvalinkki($id_lj) === 
                                Lajikuvalinkki::$OPERAATIO_ONNISTUI){
                                $lajikuvalinkkilaskuri++;
                            } else{
                                $virheilm .= $uusi->tulosta_virheilmoitukset();
                            }
                        }
                    }
                   
                    $lkm = sizeof($turvavalinnat);
                    if(($havkuvalinkkilaskuri === $lkm) &&
                        ($lajikuvalinkkilaskuri <= $lkm)){
                        
                        $palauteolio->set_ilmoitus(
                                        Kuvatekstit::$ilm_kuva_uusi_tallennus_ok);
                    } else{
                        $palauteolio->set_ilmoitus(
                                    Kuvatekstit::$ilm_kuvalinkit_tallennus_eiok.
                                    " ".$lajikuvalinkkilaskuri."/".$lkm.", ".
                                    $havkuvalinkkilaskuri."/".$lkm.
                                    "<br/>".
                                    $virheilm);
                    }
                    
                    // Aktiivisuusmerkintä:
                    $aktiivisuuslaji = Aktiivisuus::$KUVATALLENNUS_UUSI;
                    $tallentaja->paivita_aktiivisuus($aktiivisuuslaji);
                    
                }
                else{   // Ellei kuvan tallennus onnistunut:
                    $this->lisaa_virheilmoitus(
                            Kuvatekstit::$ilm_kuva_uusi_tallennus_eiok.
                            "<br />".$uusi->tulosta_virheilmoitukset());
                    
                    // Poistetaan ladatut tiedostot, koska tallennus ei ok:
                    foreach ($kohdeos_minikuvat as $minikohdeosoite) {
                        unlink($minikohdeosoite);
                    }
                    // Ja vielä iso kuva:
                    unlink($kohdeosoite);
                    
                    $palauteolio->set_ilmoitus($this->tulosta_virheilmoitukset());
                }
                
                // Poistetaan entiset kummittelemasta.
                unset($_FILES[Kuvakontrolleri::$name_ladattu_kuva]); 
            } else{
                $palauteolio->set_ilmoitus($this->tulosta_virheilmoitukset());
            }
        }
    }
    
    /**
     * Palauttaa nimikuvien lopulliset tiedosto-osoitteet taulukossa.
     * @param type $latauskansio Kansio-osoite.
     * @param type $tiedostonimi Alkuperäisen kuvan tiedostonimi.
     */
    public static function hae_minikuvien_tied_osoitteet($latauskansio,
                                                    $tiedostonimi){
        $nimien_miniosat = Kuva::get_minikuvatied_os_miniosat();
        
        $kohdeos_minikuvat = array();
        
        foreach ($nimien_miniosat as $miniosa) {
            $os = $latauskansio."/".$miniosa.$tiedostonimi;
            array_push($kohdeos_minikuvat, $os);
        }
        
        return $kohdeos_minikuvat;
    }
    
    /**
     * Hakee ja palauttaa optimaalisen kokoisen kuvan tiedosto-osoitteen.
     * @param type $tiedostokansio Kuvakansion osoite.
     * @param type $tiedostonimi    Kuvan tiedostonimi tietokannassa.
     * @param type $nayttomitta     Kuvan haluttu näyttökoko.
     * @param type $src Isoimman tallennetun kuvan tiedosto-osoite.
     */
    public static function hae_sopivan_kok_kuvan_tied_os(
                                                        $tiedostokansio,
                                                        $tiedostonimi,
                                                        $nayttomitta, 
                                                        $src){
        // Haku:
        $minikoot = Kuva::get_minikuvakoot_alkaen_pienimmasta();
        $minitied_osoitteet_alk_pien = 
                array_reverse(Kuvakontrolleri::hae_minikuvien_tied_osoitteet(
                                                            $tiedostokansio, 
                                                            $tiedostonimi));
        
        $index = 0;
        $osoite = false;
        foreach ($minikoot as $minikuvan_koko) {
            if($nayttomitta <= $minikuvan_koko){
                $osoite = $minitied_osoitteet_alk_pien[$index];
                break;
            } else{
                $index++;
            }
        }
        
        // Jos näyttömitta on suurempi kuin minikuvista suurin, käytetään
        // varsinaista kuvaa:
        if(!$osoite){
            $osoite = $src;
        }
        
        return $osoite;
    }
    
    
    /**
    * 
    * @param Palaute $palauteolio
    */
    public function toteuta_nayta(&$palauteolio) {
        
    }
    /**
     * Laskee ja palauttaa yhden näytettävän kuvan todellisen leveyden ja korkeuden.
     * Mitat palautetaan taulukossa edellä mainitussa järjestyksessä.
     * 
     * @param type $maxlev näyttökuvan maksimileveys
     * @param type $maxkork näyttökuvan maksimikorkeus
     * @param type $lev Kuvan todellinen leveys.
     * @param type $kork Kuvan todellinen korkeus.
     * @return array 
     */
    public static function laske_kuvan_nayttomitat($maxlev, $maxkork, $lev, $kork){
        
        $kuvasuhde = $lev/$kork;        // Todellisen kuvan mittojen suhde.
        
        // 1) Käsitellään tapaus "LEVEÄ kuva":
        if($kuvasuhde >= 1){
            
            // Jos leveys on yli maksimin, pitää pienentää kuvaa joka tapauksessa.
            // Huom: pienentäminen leveyden maksimiarvoon ei välttämättä riitä
            // korkeuden puolesta!
            if($lev > $maxlev){
                
                $nayttolev = $maxlev;
                
                // Lasketaan alustava korkeus, jonka sopiminen pitää testata.
                $nayttokork = round($nayttolev/$kuvasuhde);
                        
                // Jos näyttökorkeus on vieläkin yli maksimin, pitää ottaa 
                // uusiksi, eli pienentää korkeus ensin ja sen perusteella leveys:
                if($nayttokork > $maxkork){
                    $nayttokork = $maxkork;
                    $nayttolev = round($kuvasuhde*$nayttokork);
                }
            } 
            
            // Jos leveys sopii, pitää korkeus tarkistaa ja tarvittaessa pienentää molempia.
            else if($kork > $maxkork){
                $nayttokork = $maxkork;
                $nayttolev = round($kuvasuhde*$nayttokork);
                
            } 
            
            // Muuten kuva sopii sellaisenaan:
            else{
                $nayttolev = $lev;
                $nayttokork = $kork;
            }
        } 
        
        // 2) Tapaus "KORKEA kuva" eli $kuvasuhde < 1:
        else{ 
            // Jos korkeus on yli maksimin, pitää pienentää kuvaa joka tapauksessa.
            // Huom: pienentäminen korkeuden maksimiarvoon ei välttämättä riitä
            // leveyden puolesta!
            if($kork > $maxkork){
                
                $nayttokork = $maxkork;
                
                // Lasketaan alustava leveys, jonka sopiminen pitää testata.
                $nayttolev = round($nayttokork * $kuvasuhde);
                        
                // Jos näyttöleveys on yli maksimin, pitää ottaa uusiksi, eli
                // pienentää leveys ensin ja sen perusteella korkeus:
                if($nayttolev > $maxlev){
                    $nayttolev = $maxlev;
                    $nayttokork = round($nayttolev/$kuvasuhde); // kork > lev
                }
            } 
            
            // Jos korkeus sopii, pitää leveys tarkistaa ja tarvittaessa 
            // pienentää molempia.
            else if($lev > $maxlev){
                $nayttolev = $maxlev;
                $nayttokork = round($nayttolev/$kuvasuhde);
            }
            
            // Muuten kuva sopii sellaisenaan:
            else{
                $nayttolev = $lev;
                $nayttokork = $kork;
            }
        }
            
        return array($nayttolev, $nayttokork);
    }
    
    /**
    * 
    * @param Palaute $palauteolio
    */
    public function toteuta_nayta_pelkka_kuva(&$palauteolio) {
        
        $kuvakansion_osoite = Kuvakontrolleri::$kuvakansion_osoite;

        $paraolio = $this->get_parametriolio();
        
        $kuva_id = $paraolio->id_kuva;
        $kuva = new Kuva($kuva_id, $this->get_tietokantaolio());
        $tiedostonimi = $kuva->get_arvo(Kuva::$SARAKENIMI_TIEDOSTONIMI);
        $src = $kuva->get_arvo(Kuva::$SARAKENIMI_SRC);
        $kuva_selitys = $kuva->get_arvo(Kuva::$SARAKENIMI_KUVASELITYS);
        $kork = $kuva->get_korkeus();
        $lev = $kuva->get_leveys();

        $maxkork = $paraolio->max_nayttokork_kuva;
        $maxlev = $paraolio->max_nayttolev_kuva;
        $mitat = Kuvakontrolleri::laske_kuvan_nayttomitat($maxlev, $maxkork, 
                                                            $lev, $kork);
        $nayttolev = $mitat[0];
        $nayttokork = $mitat[1];
        
        $nayttomitta = max(Array($nayttokork, $nayttolev));

        // Haku:
        $optimi_src = Kuvakontrolleri::hae_sopivan_kok_kuvan_tied_os(
                                                        $kuvakansion_osoite, 
                                                        $tiedostonimi, 
                                                        $nayttomitta, 
                                                        $src);
        
        
        $kuva_html = $this->kuvanakymat->nayta_pelkka_kuva($kuva_id, 
                                                            $kuva_selitys, 
                                                            $optimi_src, 
                                                            $nayttolev, 
                                                            $nayttokork);
        $palauteolio->set_sisalto($kuva_html);
    }
    
    public function nayta_rivi_kuvia($lkm, $maxkork){
        
    }
}
?>