<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Täältä ohjataan toimintojen kokonaistoteutukset ja esimerkiksi
 * tallennetaan aktiivisuusmerkinnät.
 *
 * @author kerkjuk_admin
 */
class Havaintokontrolleri extends Kontrolleripohja{
    
    private $valittujen_idt, $kuvanakymat;

    // Name-arvot liittyen havaintoihin:
    public static $name_id_hav = "id_hav";
    public static $name_henkilo_id_hav = "henkilo_id_hav";
    public static $name_lajiluokka_id_hav = "lajiluokka_id_hav";
    public static $name_vuosi_hav = "vuosi_hav";
    public static $name_kk_hav = "kk_hav";

    public static $name_paiva_hav = "paiva_hav";
    public static $name_paikka_hav = "paikka_hav";
    public static $name_kommentti_hav = "kommentti_hav"; 
    public static $name_maa_hav = "maa_hav"; 
    public static $name_varmuus_hav = "varmuus_hav";

    public static $name_sukupuoli_hav = "sukupuoli_hav";
    public static $name_lkm_hav = "lkm_hav";
    public static $name_lisaluokitusvalinnat_hav = "lk_valinnat_hav";

    public static $name_nayttomoodi_hav = "nayttomoodi_hav"; 
    public static $name_nayttovuosi_hav = "nayttovuosi_hav"; 
    public static $name_havaintoalue_hav = "havaintoalue_hav"; 
    public static $name_lajivalinnat_hav = "lajivalinnat_hav"; // UUdet havainnot
    public static $name_havaintovalinnat_hav = "havaintovalinnat_hav"; // vanhat
    public static $name_puolivuotiskauden_nro_hav ="puolivuotkaudnum_hav";
    
    public static $name_lisaluokitusehto_hav = "lisaluokitusehto";
    
    public static $name_max_lkm_hav = "max_lkm_hav";    // Näin monta havaintoa näytetään kerralla.
    
    public static $name_aukaise_havainnot_hav = "aukaise_hav"; // boolean, havainnon tallennuksen jälkitoiminto
    public static $name_on_kopio_hav = "on_kopio_hav";  // boolean, onko kysymys havainnon kopioinnista.
    public static $name_uusi_hav = "uusi_hav";  // boolean: uusi havainto vai vanhan muokkaus
    public static $name_naytettavan_id_hav = "naytettavan_id_hav";   
    
    // Name-arvot liittyen havaintojaksoihin:
    public static $name_id_havjaks= "id_havjaks";
    public static $name_henkilo_id_havjaks= "henkilo_id_havjaks";
    public static $name_lajiluokka_id_havjaks= "lajiluokka_id_havjaks";
    public static $name_alkuaika_sek_havjaks= "alkuaika_sek_havjaks";
    public static $name_kesto_min_havjaks= "kesto_min_havjaks";
    public static $name_nimi_havjaks= "nimi_havjaks";
    public static $name_kommentti_havjaks= "kommentti_havjaks";
    public static $name_nakyvyys_havjaks= "nakyvyys_havjaks";
    
    // Name-arvot liittyen havaintojaksolinkkeihin:
    public static $name_id_havjakslink= "id_havjakslink";
    public static $name_havainto_id_havjakslink= "havainto_id_havjakslink";
    public static $name_havaintojakso_id_havjakslink= "havaintojakso_id_havjakslink";
    
    //=========================================================================
    
    /**
     * @param \Parametrit $parametriolio
     * @param \Tietokantaolio $tietokantaolio
     */
    public function __construct($tietokantaolio, $parametriolio) {
        
        parent::__construct($tietokantaolio, $parametriolio);
        
        // Haetaan valinnat:
        $this->valittujen_idt = $this->get_parametriolio()->havaintovalinnat_hav;
        $this->kuvanakymat = new Kuvanakymat();
    }

    /**
     * Palauttaa Tietokantaolio-luokan olion. Alla oleva merkintä auttaa
     * käytössä niin, että editori löytää tietokantaolion metodit myös. Tällöin
     * ei sen takia tarvitse asettaa muuttujaa julkiseksi.
     * 
     * @return \Tietokantaolio 
     */
    public function tietokantaolio(){
         return $this->get_tietokantaolio();
    }
    
    /**
     * Ylikirjoitetaan metodi, jotta luokka saadaan tietoon:
     * @return \Havainto
     */
    public function get_olio() {
        return $this->get_olio();
    }
    
    /**
     * Toteuttaa tilaston puolivuotisnäkymän näyttämisen.
     * @param type $palauteolio
     */
    public function toteuta_nayta_tilasto_puolivuotisnakyma(&$palauteolio){
        
        $otsikko = Html::luo_p(Bongaustekstit::$otsikko1_tilastot_puolivuotis, 
                        array(Maarite::style("font-weight:bold")));
        $sisalto = $otsikko;
        
        // Haetaan jakson nro ja havaintomäärät:
        $nyk_vuosi = Aika::anna_nyk_vuoden_nro();
        $nyk_kk = Aika::anna_nyk_kk_nro();

        $nyk_puolivuotiskauden_nro = ($nyk_vuosi-2009)*2;
        if($nyk_kk < 7){
            $nyk_puolivuotiskauden_nro--;
        }
        
        $sisalto .= Havainto::hae_havaintomaarat(
                        $this->get_parametriolio()->ylaluokka_id_lj, 
                        $this->get_tietokantaolio(), 
                        $nyk_puolivuotiskauden_nro,
                        $this->get_parametriolio()->poppoon_id);
        $palauteolio->set_sisalto($sisalto);
        
        // Tämä nyt enempi kosmeettinen, ennenkuin muokkaan hiukan:
        if(!empty($sisalto)){
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }else{
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
        }
    }
    

    public function toteuta_hae_henkilon_havainnot(&$palauteolio){
        
        $sisalto = Havainto::hae_henkilon_havainnot($this->get_parametriolio());
        $palauteolio->set_sisalto($sisalto);
        
        // Tämä nyt enempi kosmeettinen, ennenkuin muokkaan hiukan:
        if(!empty($sisalto)){
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }else{
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
        }
        
        
    }
    
    public function toteuta_hae_lajiluokan_havainnot(&$palauteolio){
        
        $sisalto = Havainto::hae_lajiluokan_havainnot($this->get_parametriolio());
        $palauteolio->set_sisalto($sisalto);
        
        // Tämä nyt enempi kosmeettinen, ennenkuin muokkaan hiukan:
        if(!empty($sisalto)){
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }else{
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
        }
        
        
    }
    
    public function toteuta_hae_henkilon_lajilista(&$palauteolio){
        
        $sisalto = 
            Havainto::hae_henkilon_bongatut_lajit($this->get_parametriolio(),
                        $this->get_parametriolio()->puolivuotiskauden_nro_hav);
        $palauteolio->set_sisalto($sisalto);
        
        // Tämä nyt enempi kosmeettinen, ennenkuin muokkaan hiukan:
        if(!empty($sisalto)){
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }else{
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
        }
    }
    
    public function toteuta_hae_henkilon_vuosilajilista(&$palauteolio){
        
        $sisalto = 
            Havainto::hae_henkilon_pinnalajit($this->get_parametriolio(),
                        $this->get_parametriolio()->vuosi_hav,
                        $this->get_parametriolio()->lisaluokitusehto_hav);
        
        $palauteolio->set_sisalto($sisalto);
        
        // Tämä nyt enempi kosmeettinen, ennenkuin kehitän paremmaksi:
        if(!empty($sisalto)){
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }else{
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
        }
        
        
        
    }
    
    
    /**
     * Toteuttaa kelvollisten valintojen poiston, sekä poistettaviin liittyvien
     * pikakommenttien poiston.
     * 
     * HOIDA: muuta bkuvalinkit-riveistä mahdolliset poistettuihin viittaavat
     * havainto_id:t arvoon -1.
     */
    public function toteuta_poista(&$palauteolio) {
        $valitut = $this->valittujen_idt; 
        $poistetut = array();
        $virheilmot = array();
        
        // Ne poistettavat, jotka täyttävät valtuusehdot:
        $poistettavat = $this->poimi_valituista_mahdolliset($valitut);
        
        if(empty($valitut)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            $this->toteuta_nayta($palauteolio);
        }
        else if(empty($poistettavat)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_kelvollisia_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            $this->toteuta_nayta($palauteolio);
        }
        else{
            
            $lkm_pk = 0;   //Poistetut pikakommentit.
            
            foreach ($poistettavat as $poistettava) {
                if($poistettava instanceof Havainto){
                    $palaute = $poistettava->poista();
                    
                    // tallennetaan virheilmoitukset
                    if($palaute == Havainto::$VIRHE){
                        array_push($virheilmot, $poistettava->tulosta_virheilmoitukset());
                    }
                    else{   
                        array_push($poistetut, $poistettava->get_id());
                        
                        // Otetaan muutosten lukumäärät talteen:
                        $lkm_pk += $poistettava->get_poistetut_pikakommentit_lkm();
                        
                    }
                }
            }
            
            if(sizeof($poistetut) == sizeof($poistettavat)){
                $palauteolio->set_ilmoitus(
                        sizeof($poistetut)." ".
                        Bongaustekstit::$ilm_havaintojen_poisto_ok.
                        Html::luo_br().
                        "(".$lkm_pk." ".Bongaustekstit::$ilm_pikak_kpl_poistettu.
                        ")");

                $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            }
            else{
                $viestit = sizeof($poistettavat)-sizeof($poistetut)." ".
                        Bongaustekstit::$ilm_havaintojen_lisays_eiok.
                        Html::luo_br().
                        "(".$lkm_pk." ".Bongaustekstit::$ilm_pikak_kpl_poistettu.
                        ")";
            
                foreach ($virheilmot as $ilm) {
                    $viestit .= $ilm.Html::luo_br();
                }

                $palauteolio->set_ilmoitus($viestit);
                $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_VIRHE_POISTO);
            }
            $this->toteuta_nayta($palauteolio);
        }
        
    }
    /**
     * Lisää havainnot, joiden kaikkien tiedot on kopioitu valituista. 
     * Kopioida saa kaikkien havaintoja, myös omia, jos siihen näkee tarvetta. 
     * 
     * <p>Antaa ilmoitukset ja ilmoituksen onnistumisesta $palauteolion kautta.
     * Palauttaa käyttäjän havaintojen näyttötilaan.</p>
     * 
     * 
     * 
     * @return \Palaute Palauttaa Palaute-luokan olion.
     */
    public function toteuta_kopioi_itselle(&$palauteolio) {
        $valitut = $this->valittujen_idt;
        $omaid = $this->get_parametriolio()->get_omaid();
        $tallennetut = array();
        $virheilmot = array();
        
        if(empty($valitut)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_valintoja);
            $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
        }
        else{
            foreach ($valitut as $id) {
                $kopioitava = new Havainto($id, $this->get_tietokantaolio());
                if($kopioitava->olio_loytyi_tietokannasta){
                    $uusi = new Havainto(Havainto::$MUUTTUJAA_EI_MAARITELTY,
                                        $this->get_tietokantaolio());
                    $uusi->set_lajiluokka_id($kopioitava->get_lajiluokka_id());
                    $uusi->set_henkilo_id($omaid);
                    $uusi->set_kk($kopioitava->get_kk());
                    $uusi->set_paiva($kopioitava->get_paiva());
                    $uusi->set_vuosi($kopioitava->get_vuosi());
                    $uusi->set_maa($kopioitava->get_maa());
                    $uusi->set_paikka($kopioitava->get_paikka());
                    $uusi->set_varmuus($kopioitava->get_varmuus());
                    $uusi->set_kommentti("");   // Kommentiksi tyhjä.
                    $uusi->set_arvo($kopioitava->get_sukupuoli(), 
                                    Havainto::$SARAKENIMI_SUKUPUOLI);
                    $uusi->set_arvo($kopioitava->get_lkm(), 
                                    Havainto::$SARAKENIMI_LKM);

                    if($uusi->tallenna_uusi() === Havainto::$OPERAATIO_ONNISTUI){
                        array_push($tallennetut, $uusi);

                    }
                    else{   // tallennetaan virheilmoitukset
                        array_push($virheilmot, $uusi->tulosta_virheilmoitukset());
                    }
                }
            }
            if(sizeof($tallennetut) == sizeof($valitut)){
                $palauteolio->set_ilmoitus(
                        sizeof($tallennetut)." ".
                        Bongaustekstit::$ilm_havaintojen_lisays_ok);

                $palauteolio->set_onnistumispalaute(
                                        Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            }
            else{
                $viestit = sizeof($valitut)-sizeof($tallennetut)." ".
                        Bongaustekstit::$ilm_havaintojen_lisays_eiok;
                
                foreach ($virheilmot as $ilm) {
                    $viestit .= $ilm.Html::luo_br();
                }
                
                $palauteolio->set_ilmoitus($viestit);
                $palauteolio->set_onnistumispalaute(
                                    Palaute::$ONNISTUMISPALAUTE_VIRHE_YLEINEN);
            }
        }

        // Näytetään havainnot:
        $this->toteuta_nayta($palauteolio);
        
    }

    /**
     * Toteuttaa valittujen havaintojen muokkauksen tallentamisen. Täällä
     * varmistetaan myös valtuudet. Omia saa muokata ja kunkku saa muokata
     * kaikkia. 
     * 
     * <p>HUOM! Yhtä muokattaessa on enemmän muutosmahdollisuuksia kuin 
     * montaa. Montaa muokattaessa voi muuttaa ainoastaan aikaa, paikkaa,
     * maata, varmuutta ja kommenttia. Yhtä muokattaessa myös lajiluokkaa
     * voi muuttaa. Tulee siis vähän erilaiset lomakkeet.</p>
     * 
     * Ylläoleva määräytyy sellaisen lukumäärän mukaan, joka saadaan valituista,
     * kun niistä vähennetään sellaiset, joiden muuttamiseen käyttäjälle ei
     * ole valtuuksia.
     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_tallenna_muokkaus(&$palauteolio) {
        $valitut = $this->valittujen_idt;
        $omaid = $this->get_parametriolio()->get_omaid();
        $tallennetut = array();
        $virheilmot = array();
        
        $muokkaaja = new Henkilo($omaid, $this->get_tietokantaolio());
        
        $kuvalinkkimuutos = "";
        //======================== SECURITY ====================================
        // Ne muokattavat, jotka täyttävät valtuusehdot:
        /* @var $muokattavat array */
        $muokattavat = $this->poimi_valituista_mahdolliset($valitut);
        //======================================================================
        
        if(empty($valitut)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            
            // Näytetään havainnot:
            $this->toteuta_nayta($palauteolio);
        }
        else if(empty($muokattavat)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_kelvollisia_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            
            // Näytetään havainnot:
            $this->toteuta_nayta($palauteolio);
        }
        else{
            
            // Tapaus, jossa vain yhtä havaintoa muokataan, jolloin mahdolli-
            // suuksia on enemmän.
            if(sizeof($muokattavat) === 1){
                
                // Seuraavan avulla erotetaan tapaus, jossa vain lisäluokitukset
                // päivitetään (muita tietoja ei muutettu).
                $lkm_ei_muutoksia_havaittu = 0;
                
                $muokattava = $muokattavat[0];
                if($muokattava instanceof Havainto){
                    
                    // Jos lkm on tyhjä, annetaan arvo ei-määritelty.
                    if(empty($this->get_parametriolio()->lkm_hav)){
                        $this->get_parametriolio()->lkm_hav =
                                    Parametrit::$EI_MAARITELTY;
                    }
                    
                    // Jos yhden muokkaus tulee monimuokkaussivulta, ei
                    // lajiluokkaa, kommenttia, lukumäärää tai sukupuolta ole
                    // määritelty. Testataan nämä ja jätetään alkuperäisiksi, '
                    // ellei määritelty.
                    
                    // Jos toisaalta lajiluokkaa on muutettu, pitää silloin
                    // päivittää myös lajikuvalinkit tämän havainnon kuville!
                    if($this->get_parametriolio()->lajiluokka_id_hav !==
                        Parametrit::$EI_MAARITELTY){
                        
                        // Otetaan talteen entinen lajiluokka, jotta muutos 
                        // havaitaan. Tämän perusteella linkkejä tajutaan alkaa
                        // korjaamaan.
                        $muokattava->set_vanha_lajiluokka_id(
                                        $muokattava->get_lajiluokka_id());
                        
                        // Ja nyt voidaan asettaa uusi lajiluokka:
                        $muokattava->set_lajiluokka_id($this->
                                get_parametriolio()->lajiluokka_id_hav);
                        
                    }
                    
                    
                    $muokattava->set_henkilo_id($omaid);
                    $muokattava->set_kk($this->
                                    get_parametriolio()->kk_hav);
                    $muokattava->set_paiva($this->
                                    get_parametriolio()->paiva_hav);
                    $muokattava->set_vuosi($this->
                                    get_parametriolio()->vuosi_hav);
                    $muokattava->set_maa($this->
                                    get_parametriolio()->maa_hav);
                    $muokattava->set_paikka($this->
                                    get_parametriolio()->paikka_hav);
                    $muokattava->set_varmuus($this->
                                    get_parametriolio()->varmuus_hav);
                    
                    if($this->get_parametriolio()->kommentti_hav !==
                        Parametrit::$EI_MAARITELTY){ 
                        $muokattava->set_kommentti($this->
                                    get_parametriolio()->kommentti_hav);  
                    }
                    
                    // Uudet ominaisuudet:
                    if($this->get_parametriolio()->sukupuoli_hav !==
                        Parametrit::$EI_MAARITELTY){ 
                        $muokattava->set_arvo($this->get_parametriolio()->sukupuoli_hav, 
                                    Havainto::$SARAKENIMI_SUKUPUOLI); 
                    }
                    if($this->get_parametriolio()->lkm_hav !==
                        Parametrit::$EI_MAARITELTY){ 
                        $muokattava->set_arvo($this->get_parametriolio()->lkm_hav, 
                                    Havainto::$SARAKENIMI_LKM); 
                    }  
                    
                    if($muokattava->tallenna_muutokset() === 
                                            Havainto::$OPERAATIO_ONNISTUI){
                        array_push($tallennetut, $muokattava);

                        // Lajia voi muuttaa vain yksitellen, joten tämä vain täällä.
                        $kuvalinkkimuutos = 
                            $muokattava->
                                korjaa_lajikuvalinkit_lajimuokkauksen_jalkeen();
                        
                        if($kuvalinkkimuutos !== Havainto::$OPERAATIO_ONNISTUI){
                            array_push($virheilmot, 
                                        $muokattava->tulosta_virheilmoitukset());
                        }
                        //======================================================
                        // Päivitetään lisäluokitukset:
                        $this->paivita_muokatun_lisaluokitukset($muokattava, 
                                                            $this, 
                                                            $palauteolio);
                        //======================================================

                    }
                    else{   // tallennetaan virheilmoitukset, jos aihetta
                        if($muokattava->tulosta_viimeisin_virheilmoitus() ==
                            Perustustekstit::$ilm_tiedoissa_ei_muutoksia){
                            
                            //======================================================
                            // Päivitetään lisäluokitukset, vaikka muita 
                            // muutoksia ei ole havaittu:
                            $this->paivita_muokatun_lisaluokitukset($muokattava, 
                                                                $this, 
                                                                $palauteolio);
                            //======================================================
                            $lkm_ei_muutoksia_havaittu++;
                            
                        } else{
                            array_push($virheilmot, $muokattava->tulosta_virheilmoitukset());
                        }
                    }
                }
            }
            else{   // Kun laillisia valintoja on enemmän kuin yksi.
                
                // Kaikkia ei välttämättä ole tarvetta muokata, mutta se on
                // ihan ok, eikä tällöin pidä lähettää virheilmoitusta.
                $lkm_ei_muutoksia_havaittu = 0;
                
                foreach ($muokattavat as $muokattava) {  
                    
                    $muokattava->set_henkilo_id($omaid);
                    $muokattava->set_kk($this->
                                    get_parametriolio()->kk_hav);
                    $muokattava->set_paiva($this->
                                    get_parametriolio()->paiva_hav);
                    $muokattava->set_vuosi($this->
                                    get_parametriolio()->vuosi_hav);
                    $muokattava->set_maa($this->
                                    get_parametriolio()->maa_hav);
                    $muokattava->set_paikka($this->
                                    get_parametriolio()->paikka_hav);
                    $muokattava->set_varmuus($this->
                                    get_parametriolio()->varmuus_hav);
                    /*$muokattava->set_kommentti($this->
                                    get_parametriolio()->kommentti_hav);   */      
                    
                    if($muokattava->tallenna_muutokset() === Havainto::$OPERAATIO_ONNISTUI){
                        array_push($tallennetut, $muokattava);
                        //======================================================
                        // Päivitetään lisäluokitukset:
                        $this->paivita_muokatun_lisaluokitukset($muokattava, 
                                                            $this, 
                                                            $palauteolio);
                        //======================================================

                    }
                    else{   // tallennetaan virheilmoitukset, jos aihetta.
                        if($muokattava->tulosta_viimeisin_virheilmoitus() ==
                            Perustustekstit::$ilm_tiedoissa_ei_muutoksia){
                            
                            //======================================================
                            // Päivitetään lisäluokitukset, vaikka muita 
                            // muutoksia ei ole havaittu:
                            $this->paivita_muokatun_lisaluokitukset($muokattava, 
                                                                $this, 
                                                                $palauteolio);
                            //======================================================
                            $lkm_ei_muutoksia_havaittu++;
                            
                        } else{
                            array_push($virheilmot, $muokattava->tulosta_virheilmoitukset());
                        }
                        
                    }
                }
            }
            // Varmistuksessa otetaan mukaan ne, joiden tiedot ovat jo olleet
            // samat, eli joita ei ole tarvinnut muuttaa:
            if((sizeof($tallennetut)+$lkm_ei_muutoksia_havaittu) == sizeof($muokattavat)){
                $palauteolio->set_ilmoitus(
                        sizeof($muokattavat)." ".
                        Bongaustekstit::$ilm_havainnon_monimuokkaustallennus_ok);

                $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                
                // Näytetään havainnot:
                $this->toteuta_nayta($palauteolio);
            } else{
                $viestit = sizeof($muokattavat)-
                            (sizeof($tallennetut)+$lkm_ei_muutoksia_havaittu)." ".
                        Bongaustekstit::$ilm_havainnon_monimuokkaustallennus_EI_ok;
                
                foreach ($virheilmot as $ilm) {
                    $viestit .= $ilm.Html::luo_br();
                }
                
                $palauteolio->set_ilmoitus($viestit);
                $palauteolio->set_onnistumispalaute(
                        Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_MUOKKAUS);
                // Näytetään havainnot:
                $this->toteuta_nayta_monimuokkauslomake($palauteolio);
            }
            
            if($kuvalinkkimuutos === Havainto::$OPERAATIO_ONNISTUI){
                $kuvalinkkimuutosilmoitus = 
                    "(".Bongaustekstit::
                            $ilm_havainnon_muokkaus_kuvalinkkilj_muutettu.")";
                
                // Lisätään palautteeseen ilmoitus:
                $palauteolio->lisaa_kommentti($kuvalinkkimuutosilmoitus);
            } 
            
            // Aktiivisuusmerkintä:
            $muokkaaja->paivita_aktiivisuus(Aktiivisuus::$HAVAINTOTALLENNUS_MUOKKAUS);
        }
    }
    /**
     * 
     * @param Palaute $palauteolio
     */
    public function toteuta_nayta_yksi_uusi_lomake(&$palauteolio){
        
        // Paikka ja kommentti muutetaan tyhjiksi, jos ovat epämääriteltyjä:
        if($this->get_parametriolio()->paikka_hav == Parametrit::$EI_MAARITELTY){
            $this->get_parametriolio()->paikka_hav = "";
        }
        if($this->get_parametriolio()->kommentti_hav == Parametrit::$EI_MAARITELTY){
            $this->get_parametriolio()->kommentti_hav = "";
        }
        
        // Asuinmaaksi haetaan käyttäjän senhetkinen asuinmaa:
        $omaid = $this->get_parametriolio()->get_omaid();
        $tallentaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                    $this->get_tietokantaolio());
        $asuinmaa = $tallentaja->get_arvo(Henkilo::$sarakenimi_asuinmaa);
        $this->get_parametriolio()->maa_hav = $asuinmaa;
        
        
        $havaintonakymat = new Havaintonakymat($this->get_tietokantaolio(), 
                                                    $this->get_parametriolio(),
                                                    $this->kuvanakymat);

            
        $palauteolio->set_sisalto($havaintonakymat->nayta_uusi_havaintolomake());
        
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
    }
    
    /**
     * Palauttaa palauteolion, joka sisältää kuvalomakkeen html:n
     * 
     * @param Palaute $palauteolio
     * @param Kuvakontrolleri $kuvakontrolleri
     * @param type
     */
    public function toteuta_nayta_kuvalomake_havaintoihin(&$palauteolio, 
                                                        $kuvakontrolleri){
        $valitut = $this->valittujen_idt;
        $omaid = $this->get_parametriolio()->get_omaid();
        $this->get_parametriolio()->uusi_kuva = true;
        
        //======================== SECURITY ====================================
        // Ne muokattavat, jotka täyttävät valtuusehdot:
        /* @var $muokattavat array */
        $muokattavat = $this->poimi_valituista_mahdolliset($valitut);
        //======================================================================
        
        if(empty($valitut)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            $this->toteuta_nayta($palauteolio);
        }
        else if(empty($muokattavat)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_kelvollisia_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            $this->toteuta_nayta($palauteolio);
        }
        else{
            
            // Haetaan ekan havainnon tiedot kuvaa varten:
            $hav = $muokattavat[0];
            
            if($hav instanceof Havainto){
                $this->get_parametriolio()->id_hav = $hav->get_id();
                $this->get_parametriolio()->kuvaotsikko_kuva = 
                        Lajiluokka::hae_lajiluokan_nimi(
                                $hav->get_lajiluokka_id(), 
                                $this->tietokantaolio(), 
                                $this->get_parametriolio()->kieli_id); 
                $this->get_parametriolio()->kuvaselitys_kuva = 
                                $hav->get_paikka().": ".$hav->get_kommentti();
                $this->get_parametriolio()->vuosi_kuva = $hav->get_vuosi(); 
                $this->get_parametriolio()->kk_kuva = $hav->get_kk(); 
                $this->get_parametriolio()->paiva_kuva = $hav->get_paiva();
            }
            
            
            
            $havaintonakymat = new Havaintonakymat($this->get_tietokantaolio(), 
                                                    $this->get_parametriolio(),
                                                    $this->kuvanakymat);

            $paraolio = $this->get_parametriolio();
            $lomakekoodi = $kuvakontrolleri->get_kuvanakymat()->
                        nayta_kuvalomake_ilman_formia($paraolio);
            $palauteolio->
                set_sisalto($havaintonakymat->luo_kuvalomake(
                                $muokattavat,
                                $lomakekoodi));
        }
    }
    
    public function toteuta_nayta_monimuokkauslomake(&$palauteolio) {
        $valitut = $this->valittujen_idt;
        
        // Jos valitut on tyhjä, tarkistetaan vielä havainto_id, joka
        // korvaa valitut toisinaan:
        if(empty($valitut)){
            if($this->get_parametriolio()->id_hav > 0){
                array_push($valitut, $this->get_parametriolio()->id_hav);
            }
        }
        
        // Asuinmaaksi haetaan käyttäjän senhetkinen asuinmaa:
        $omaid = $this->get_parametriolio()->get_omaid();
        $tallentaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                    $this->get_tietokantaolio());
        $asuinmaa = $tallentaja->get_arvo(Henkilo::$sarakenimi_asuinmaa);
        $this->get_parametriolio()->maa_hav = $asuinmaa;
        
        $tallennetut = array();
        $virheilmot = array();
        
        //======================== SECURITY ====================================
        // Ne muokattavat, jotka täyttävät valtuusehdot:
        /* @var $muokattavat array */
        $muokattavat = $this->poimi_valituista_mahdolliset($valitut);
        //======================================================================
        
        if(empty($valitut)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            $this->toteuta_nayta($palauteolio);
        }
        else if(empty($muokattavat)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_kelvollisia_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            $this->toteuta_nayta($palauteolio);
        }
        else{
            // Paikka ja kommentti muutetaan tyhjiksi, jos ovat epämääriteltyjä:
            if($this->get_parametriolio()->paikka_hav == Parametrit::$EI_MAARITELTY){
                $this->get_parametriolio()->paikka_hav = "";
            }
            if($this->get_parametriolio()->kommentti_hav == Parametrit::$EI_MAARITELTY){
                $this->get_parametriolio()->kommentti_hav = "";
            }
            
            $havaintonakymat = new Havaintonakymat($this->get_tietokantaolio(), 
                                                    $this->get_parametriolio(),
                                                    $this->kuvanakymat);

            
            $palauteolio->set_sisalto($havaintonakymat->
                            luo_monimuokkauslomake($muokattavat));
        }
    }
    
    
    public function toteuta_nayta_moniuusitallennuslomake(&$palauteolio){
        
        // Paikka ja kommentti muutetaan tyhjiksi, jos ovat epämääriteltyjä:
        if($this->get_parametriolio()->paikka_hav == Parametrit::$EI_MAARITELTY){
            $this->get_parametriolio()->paikka_hav = "";
        }
        if($this->get_parametriolio()->kommentti_hav == Parametrit::$EI_MAARITELTY){
            $this->get_parametriolio()->kommentti_hav = "";
        }
        
        // Asuinmaaksi haetaan käyttäjän senhetkinen asuinmaa:
        $omaid = $this->get_parametriolio()->get_omaid();
        $tallentaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                    $this->get_tietokantaolio());
        $asuinmaa = $tallentaja->get_arvo(Henkilo::$sarakenimi_asuinmaa);
        $this->get_parametriolio()->maa_hav = $asuinmaa;
        
        $this->get_parametriolio()->set_kieli_id(Kielet::$SUOMI);
        
        $havaintonakymat = new Havaintonakymat($this->get_tietokantaolio(), 
                                                    $this->get_parametriolio(),
                                                    $this->kuvanakymat);
        
        $palauteolio->set_sisalto($havaintonakymat->nayta_uusi_monen_havainnon_lomake());
        $palauteolio->set_nayttomoodi(
                Html_tulostus::$nayttomoodi_yksipalkki);
    }
    
    /**
     * Toteuttaa yksittäisen uuden havainnon tallennuksen.
     * @param Palaute $palauteolio
     */
    public function toteuta_tallenna_uusi(&$palauteolio) {
        
        $omaid = $this->get_parametriolio()->get_omaid();
        
        $uusi = new Havainto(Havainto::$MUUTTUJAA_EI_MAARITELTY,
                                $this->get_tietokantaolio());
        
        $tallentaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                    $this->get_tietokantaolio());
        
        // Jos lkm on tyhjä, annetaan arvo ei-määritelty.
        if(empty($this->get_parametriolio()->lkm_hav)){
            $this->get_parametriolio()->lkm_hav =
                        Parametrit::$EI_MAARITELTY;
        }
        
        $uusi->set_henkilo_id($this->get_parametriolio()->get_omaid());
        $uusi->set_lajiluokka_id($this->get_parametriolio()->lajiluokka_id_hav);
        $uusi->set_paiva($this->get_parametriolio()->paiva_hav);
        $uusi->set_kk($this->get_parametriolio()->kk_hav);
        $uusi->set_vuosi($this->get_parametriolio()->vuosi_hav);
        $uusi->set_paikka($this->get_parametriolio()->paikka_hav);
        $uusi->set_kommentti($this->get_parametriolio()->kommentti_hav);
        $uusi->set_maa($this->get_parametriolio()->maa_hav);
        $uusi->set_varmuus($this->get_parametriolio()->varmuus_hav);
        
        // Uudet ominaisuudet:
        $uusi->set_arvo($this->get_parametriolio()->sukupuoli_hav, 
                        Havainto::$SARAKENIMI_SUKUPUOLI);
        $uusi->set_arvo($this->get_parametriolio()->lkm_hav, 
                        Havainto::$SARAKENIMI_LKM);
        
        if($uusi->tallenna_uusi() === Havainto::$OPERAATIO_ONNISTUI){
            
            // Tallennetaan havainnon lisäluokitukset:==========================
            $valitut = $this->get_parametriolio()->lisaluokitusvalinnat_hav;
            foreach ($valitut as $lisaluokitusarvo) {
                $palaute = $uusi->tallenna_uusi_lisaluokitus($lisaluokitusarvo);
                if($palaute === Havainto::$VIRHE){
                    $palauteolio->lisaa_virheilmoitus(
                            Bongaustekstit::$ilm_havainnon_lisaluokan_tallennus_eiok.
                            " ".$uusi->tulosta_virheilmoitukset());
                }
            }
            //==================================================================
            
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_havainnon_lisays_ok);
            $this->toteuta_nayta($palauteolio);
            $palauteolio->set_muokatun_id($uusi->get_id());
            
            // Aktiivisuusmerkintä:
            $tallentaja->paivita_aktiivisuus(Aktiivisuus::$HAVAINTOTALLENNUS_UUSI);
            
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                    
                    
            
        }
        else{
            $palauteolio->set_onnistumispalaute(
                            Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_UUSI);
            
            $palaute = Bongaustekstit::$ilm_havainnon_lisays_eiok.
                    Html::luo_br().
                    $uusi->tulosta_virheilmoitukset();
            
            // Parametriolion kautta saadaan lomakkeeseen palaute myös.
            $this->get_parametriolio()->set_tallennuspalaute($palaute);
            $palauteolio->set_ilmoitus($palaute);
            
            // Asetetaan valituksi uusi: 
            $this->valittujen_idt = array($uusi->get_id());
            $this->toteuta_nayta_yksi_uusi_lomake($palauteolio);
        }
    }
    /**
     * Toteuttaa monen uuden havainnon tallennuksen.
     * @return \Palaute
     */
    public function toteuta_tallenna_monta_uutta(&$palauteolio){
        
        $tietokantaolio = $this->get_tietokantaolio();

        // Sisältää valittujen lajiluokkien id:t.
        $valinnat = $this->get_parametriolio()->lajivalinnat_hav; 

        $laskuri = 0;
        $tallennusten_lkm = 0;
        $virheiden_lkm = 0;
        $virheilmot = "";
        $tallennetut_lajit = "";    // Nimet kerätään tähän.
        
        $tallentaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                $this->get_tietokantaolio());

        foreach ($valinnat as $id_lj) {
                
            $uusi = new Havainto(Havainto::$MUUTTUJAA_EI_MAARITELTY,
                                $this->tietokantaolio());
            $uusi->set_henkilo_id($this->get_parametriolio()->get_omaid());
            $uusi->set_lajiluokka_id($id_lj);
            $uusi->set_paiva($this->get_parametriolio()->paiva_hav);
            $uusi->set_kk($this->get_parametriolio()->kk_hav);
            $uusi->set_vuosi($this->get_parametriolio()->vuosi_hav);
            $uusi->set_paikka($this->get_parametriolio()->paikka_hav);
            $uusi->set_kommentti($this->get_parametriolio()->kommentti_hav);
            $uusi->set_maa($this->get_parametriolio()->maa_hav);
            $uusi->set_varmuus($this->get_parametriolio()->varmuus_hav);

             // Uudet ominaisuudet:
            $uusi->set_arvo($this->get_parametriolio()->sukupuoli_hav, 
                            Havainto::$SARAKENIMI_SUKUPUOLI);
            $uusi->set_arvo($this->get_parametriolio()->lkm_hav, 
                            Havainto::$SARAKENIMI_LKM);
            
            if($uusi->tallenna_uusi() === Havainto::$OPERAATIO_ONNISTUI){
                $tallennusten_lkm++;
                
                // Tallennetaan havainnon lisäluokitukset:==========================
                $valitut = $this->get_parametriolio()->lisaluokitusvalinnat_hav;
                foreach ($valitut as $lisaluokitusarvo) {
                    $palaute = $uusi->tallenna_uusi_lisaluokitus($lisaluokitusarvo);
                    if($palaute === Havainto::$VIRHE){
                        $palauteolio->lisaa_virheilmoitus(
                            Bongaustekstit::$ilm_havainnon_lisaluokan_tallennus_eiok.
                            " ".$uusi->tulosta_virheilmoitukset());
                    }
                }
                
                // Haetaan nimi tallennetulle:
                $nimi = Bongaustekstit::$nimi_tuntematon;
                $lajiluokka = new Lajiluokka($tietokantaolio, $id_lj);
                if($lajiluokka->olio_loytyi_tietokannasta){
                    $kuvaus = $lajiluokka->
                                hae_kuvaus($this->get_parametriolio()->kieli_id);
                    if($kuvaus instanceof Kuvaus){
                        if($laskuri == 0){
                            $nimi = $kuvaus->get_nimi();

                        }
                        else{
                            $nimi = ", ".$kuvaus->get_nimi();

                        }
                    }
                }
                
                $tallennetut_lajit.= $nimi;
            }
            else{
                $virheiden_lkm++;
                $virheilmot .= $uusi->tulosta_virheilmoitukset()."<br />";
            }
            
            $laskuri++;
        }
        
        // Aktiivisuusmerkintä (vain kerran):
        $tallentaja->paivita_aktiivisuus(Aktiivisuus::$HAVAINTOTALLENNUS_UUSI);

        // Palautteet:
        if($tallennusten_lkm == sizeof($valinnat)){
            $kommentti = $tallennusten_lkm." ".
                            Bongaustekstit::$ilm_havaintojen_lisays_ok.
                            " (".$tallennetut_lajit.")";
        }
        else{
            $kommentti = $virheiden_lkm." ".
                        Bongaustekstit::$ilm_havaintojen_lisays_eiok."<br/>".
                        $virheilmot;
        }
        
        $palauteolio->set_ilmoitus($kommentti);

        // Avataan havainnot.
        $this->toteuta_nayta($palauteolio);
    }

    /**
     * Hakee havainnot nätisti muotoiltuna ja asettaa ne palauteolion
     * sisällöksi. Ei koske ilmoitukseen.
     * @return \Palaute $palauteolio
     */
    public function toteuta_nayta(&$palauteolio) {
        
        $parametriolio = $this->get_parametriolio();
        
        // Huomaa: alla +0 tarpeen!
        if($parametriolio->get_havaintojen_nayttovuosi()+0 ===
                    Bongausasetuksia::$nayta_oletushavainnot){
            $nayttomoodi = Havaintojen_nayttomoodi::$nayta_uusimmat;
        }
        else{
            $nayttomoodi = Havaintojen_nayttomoodi::$nayta_vuoden_mukaan;
        }

        $parametriolio->set_havaintojen_nayttomoodi($nayttomoodi);
        
        $havainnot = Havainto::hae_soveliaat($this->get_tietokantaolio(), 
                                            $this->get_parametriolio());
        
        // Asetetaan kieli, joka lähinnä vaikuttaa lajien nimiin. Muuten
        // kiele vaihtelee turhan hallitsemattomasti, jos esim. lajinimiä
        // lisätään. Käyttäjä voi valita näyttökielen.
        $kieli = $this->get_kayttaja()->get_arvo(Henkilo::$sarakenimi_kieli);
        $this->get_parametriolio()->set_kieli_id($kieli);
        
        // Havaintotaulukossa näytettävien kuvien koko:
        $this->get_parametriolio()->max_nayttokork_kuva = 75;
        $this->get_parametriolio()->max_nayttolev_kuva = 75*2;
                                    //Kuva::$KUVATALLENNUS_PIENI8_MITTA; 
        
        $havaintonakymat = new Havaintonakymat($this->get_tietokantaolio(), 
                                                $this->get_parametriolio(),
                                                $this->kuvanakymat);
        
        // Tehdään aikavalikon koodi täällä:
        $valinta = $this->get_parametriolio()->nayttovuosi_hav;
        $havaintonakymat->aikavalikko = $this->nayta_aikavalikko($valinta);
        
        $palauteolio->set_sisalto($havaintonakymat->nayta($havainnot));
        
        $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_yksipalkki);
        
    }

    public function toteuta_nayta_poistovarmistus(&$palauteolio) {
    
        $valitut_havainnot = $this->get_parametriolio()->havaintovalinnat_hav; 
        
        // Jos valitut on tyhjä, tarkistetaan vielä havainto_id, joka
        // korvaa valitut toisinaan:
        if(empty($valitut_havainnot)){
            if($this->get_parametriolio()->id_hav > 0){
                array_push($valitut_havainnot, $this->get_parametriolio()->id_hav);
            }
        }
        
         // Ne poistettavat, jotka täyttävät valtuusehdot:
        $poistettavat = $this->poimi_valituista_mahdolliset($valitut_havainnot);
    
        if(empty($valitut_havainnot)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            $this->toteuta_nayta($palauteolio);
        }
        else if(empty($poistettavat)){
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_ei_kelvollisia_valintoja);
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            $this->toteuta_nayta($palauteolio);
        }
        else{
            $havaintonakymat = new Havaintonakymat($this->get_tietokantaolio(), 
                                                    $this->get_parametriolio(),
                                                    $this->kuvanakymat);

            
            $palauteolio->set_sisalto($havaintonakymat->
                                    luo_poistovahvistuslomake($poistettavat));
        }
        
        
    }
    
    /**
     * Päivittää muokatun havainnon lisäluokitukset valintojen mukaisiksi.
     * 
     * Ei palauta arvoa (voi muuttaa tarvittaessa).
     * 
     * @param Havainto $muokattu
     * @param Havaintokontrolleri $havaintokontrolleri
     * @param Palaute $palauteolio
     */
    public function paivita_muokatun_lisaluokitukset($muokattu, 
                                                $havaintokontrolleri,
                                                &$palauteolio){
        
        // Päivitetään havainnon lisäluokitukset:===============
        $lisaluokitusasetukset = new Lisaluokitus_asetukset();
        $asetukset = $lisaluokitusasetukset->get_asetukset(); 

        $valitut = $havaintokontrolleri->get_parametriolio()->
                                        lisaluokitusvalinnat_hav;

        $poistettavat = array();    // Nää poistetaan.

        // Kopioidaan $kaikki_arvot-taulukosta $poistettavat-
        // taulukkoon ne arvot, joita ei olla valittu, eli joiden
        // mahdolliset tietokantarivit pitää poistaa.
        foreach ($asetukset as $asetus) {
            $arvo = $asetus->get_arvo();
            $on_valittu = false;
            foreach ($valitut as $valittu) {
                if($arvo == $valittu){
                    $on_valittu = true;
                }
            }
            if(!$on_valittu){
                array_push($poistettavat, $arvo);
            }
        }

        // Tallennetaan valitut (ellei ole ennestään):
        $tallennetut_lkm = 0;
        foreach ($valitut as $valittu_lisaluokitus) {
            $palaute = $muokattu->tallenna_uusi_lisaluokitus(
                                            $valittu_lisaluokitus);
            if($palaute === Havainto::$VIRHE){
                $palauteolio->lisaa_virheilmoitus(
                        Bongaustekstit::
                        $ilm_havainnon_lisaluokan_tallennus_eiok.
                        " ".$muokattu->tulosta_virheilmoitukset());
            } else{
                $tallennetut_lkm++;
            }
        }
        /*$palauteolio->lisaa_kommentti($tallennetut_lkm." ".
                Bongaustekstit::$ilm_havainnon_lisaluokkaa_tallennettu);*/

        // Poistetaan ei-valitut tietokannasta:
        $poistetut_lkm = 0;
        foreach ($poistettavat as $poistettava) {
            $palaute = $muokattu->poista_lisaluokitus($poistettava);
            $poistetut_lkm += $palaute;
        }
        /*$palauteolio->lisaa_kommentti($poistetut_lkm." ".
                Bongaustekstit::$ilm_havainnon_lisaluokkaa_poistettu);*/
        //======================================================
    }
    
    /**
     * ======================== SECURITY ====================================
     * Noukitaan valituista poistettaviin/muokattaviin sellaiset havainnot
     * (Havainto-luokan oliot), joihin käyttäjällä on oikeudet. Palauttaa oliot 
     * taulukossa, joka voi olla tyhjä.
     */
    private function poimi_valituista_mahdolliset($valitut){
        
        $mahdolliset = array();
        $omaid = $this->get_parametriolio()->get_omaid();
        $kayttaja = new Henkilo($omaid, $this->get_tietokantaolio()); 
                
        foreach ($valitut as $id_hav) {
            $ehdokas = new Havainto($id_hav, $this->get_tietokantaolio());
            
            // Ok, jos käyttäjä on kuningas tai havainto oma:
            if($ehdokas->olio_loytyi_tietokannasta){
                if($kayttaja->on_kuningas() ||
                    ($ehdokas->get_henkilo_id() === $omaid)){
                    
                    // Lisätään muokattaviin:
                    array_push($mahdolliset, $ehdokas);
                }
            }
        }
        return $mahdolliset;
    }
    
    /**
     *  * ======================== SECURITY ====================================
     * Noukitaan valituista poistettaviin/muokattaviin sellaiset havainnot
     * (Havainto-luokan oliot), joihin käyttäjällä on oikeudet. Palauttaa oliot 
     * taulukossa, joka voi olla tyhjä.
     * 
     * @param type $valitut
     * @param type $omaid
     * @param type $tietokantaolio
     * @return array
     */
    public static function poimi_valituista_havainnoista_mahdolliset(
                                                                $valitut, 
                                                                $omaid, 
                                                                $tietokantaolio){
        
        $mahdolliset = array();
        $kayttaja = new Henkilo($omaid, $tietokantaolio); 
                
        foreach ($valitut as $id_hav) {
            $ehdokas = new Havainto($id_hav, $tietokantaolio);
            
            // Ok, jos käyttäjä on kuningas tai havainto oma:
            if($ehdokas->olio_loytyi_tietokannasta){
                if($kayttaja->on_kuningas() ||
                    ($ehdokas->get_henkilo_id() === $omaid)){
                    
                    // Lisätään muokattaviin:
                    array_push($mahdolliset, $ehdokas);
                }
            }
        }
        return $mahdolliset;
    }
    //======================================================================
   
    /**
    * Toteuttaa bongauskuva-albumeiden näyttämisen.
    * @param Parametrit $parametriolio
    * @return Palaute $palauteolio
    */
   static function toteuta_bongausalbumeiden_naytto(&$palauteolio){
       $parametriolio = $this->get_parametriolio();

       $sisalto = bongaus_hae_albumit($parametriolio->omaid,
                                           $parametriolio->tietokantaolio);
       $palauteolio->set_sisalto($sisalto);
       $palauteolio->set_ilmoitus("");

       
   }
   
   /**
    * Palauttaa aikavalikon, joka näyttää ja josta voidaan valita se, kuinka
    * paljon tai miltä aikaväliltä merkinnät näytetään.
    *
    * @param Tietokantaolio $tietokantaolio
    * @param <type> $valinta
    * @param <type> $kieli_id
    * @param <type> $param_otsikko
    * @param <type> $js_metodinimi
    * @param <type> $js_param_array
    * @return <type>
    */
   function nayta_aikavalikko(&$valinta){

       $tietokantaolio = $this->get_tietokantaolio();
       $parametriolio = $this->get_parametriolio();
       $kieli_id = $this->get_parametriolio()->get_kieli_id();
       $otsikko = "";
       $js_metodinimi = "hae_havainnot";
       $js_param_array = // Alla ylempi ei tunnu toimivan!
            //array(Havaintokontrolleri::$name_nayttovuosi_hav,"this.value");
              array("this.value");
       
       $alkuvuosi = 2010;   // Ensimmäinen vuosi, joka näytetään.
       
       // Haetaan valittu:
       /*if($parametriolio->get_havaintojen_nayttomoodi() == 
                                    Havaintojen_nayttomoodi::$nayta_uusimmat){
            $valinta = Bongausasetuksia::$nayta_oletushavainnot;
            
       } else if($parametriolio->get_havaintojen_nayttomoodi() ==
                            Havaintojen_nayttomoodi::$nayta_vuoden_mukaan){
            $valinta = $parametriolio->nayttovuosi_hav;
            
       } else{ // Voishan tänne lisätä vaikka kuukausittain tms.
            $valinta = Bongausasetuksia::$nayta_oletushavainnot;
            
       }*/
       $valinta = $parametriolio->nayttovuosi_hav; 
       
       $eka_valinta = $parametriolio->
                    max_lkm_hav.Bongaustekstit::$max_nayttoilm_bongaussivu1;
        $arvot = array(Bongausasetuksia::$nayta_oletushavainnot);
        $nimet = array($eka_valinta);
       
       // Lisätään sitten vuosiluvut nykyisestä alaspäin:
       $nyk_vuosi = Aika::anna_nyk_vuoden_nro();
       $vuosi = $nyk_vuosi;
       
       while ($vuosi >= $alkuvuosi){
           array_push($arvot, $vuosi);
           array_push($nimet, $vuosi);
           $vuosi--;
       }

       $valikkohtml = "";

       try{
           $name_arvo = "name_aikavalinta";
           $id_arvo = "";
           $class_arvo = "";
           $oletusvalinta_arvo = $valinta;
           $onchange_metodinimi = $js_metodinimi;
           $onchange_metodiparametrit_array = $js_param_array;

           $valikkohtml.= Html::luo_pudotusvalikko_onChange($arvot,
                                                           $nimet,
                                                           $name_arvo,
                                                           $id_arvo,
                                                           $class_arvo,
                                                           $oletusvalinta_arvo,
                                                           $otsikko,
                                                           $onchange_metodinimi,
                                               $onchange_metodiparametrit_array);

           
       }
       catch(Exception $poikkeus){
           $valikkohtml = 
               Bongaustekstit::$havainnot_aikavalikko_virheilm." (".
                           $poikkeus->getMessage().")";
       }
       return $valikkohtml;
   }
}
?>