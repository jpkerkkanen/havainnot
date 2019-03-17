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
    
    private $valittujen_idt, $kuvanakymat, $havaintonakymat;

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
    public static $name_alkuaika_sek_havjaks= "alkuaika_sek_havjaks";
    public static $name_alkuaika_min_havjaks= "alkuaika_min_havjaks";
    public static $name_alkuaika_paiva_havjaks= "alkuaika_paiva_havjaks";
    public static $name_alkuaika_kk_havjaks= "alkuaika_kk_havjaks";
    public static $name_alkuaika_vuosi_havjaks= "alkuaika_vuosi_havjaks";
    public static $name_alkuaika_h_havjaks= "alkuaika_h_havjaks";
    public static $name_kesto_min_havjaks= "kesto_min_havjaks";
    public static $name_kesto_h_havjaks= "kesto_h_havjaks";
    public static $name_kesto_vrk_havjaks= "kesto_vrk_havjaks";
    public static $name_nimi_havjaks= "nimi_havjaks";
    public static $name_kommentti_havjaks= "kommentti_havjaks";
    public static $name_nakyvyys_havjaks= "nakyvyys_havjaks";
    public static $name_uusi_havjaks= "uusi_havjaks";
    
    // Name-arvot liittyen havaintojaksolinkkeihin:
    public static $name_id_havjakslink= "id_havjakslink";
    public static $name_havainto_id_havjakslink= "havainto_id_havjakslink";
    public static $name_havaintojakso_id_havjakslink= "havaintojakso_id_havjakslink";
    
    // Name-arvot liittyen havaintopaikkoihin:
    public static $name_havaintopaikka_id = "id_havpaikka";
    public static $name_havaintopaikka_henkilo_id = "id_henkilo_havpaikka";
    public static $name_havaintopaikka_paikannimi = "nimi_havpaikka";
    public static $name_havaintopaikka_selitys = "selitys_havpaikka";
    public static $name_havaintopaikka_maa = "maa_havpaikka";
    
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
        $this->havaintonakymat = 
            new Havaintonakymat($tietokantaolio, $parametriolio, $this->kuvanakymat);
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
    
    //==========================================================================
    /**
     * Palauttaa yhdessä paikassa havaitut havainnot taulukkoon muotoiltuna.
     * 
     * Näkymäkoodi voisi olla erillään kyllä.. Ja pari vastaavaa on jopa
     * Havainto-luokan sisällä, mikä nyt ei ihan optimaalista ole. Mutta hyvä,
     * jotta on jotakin kehitettävää..
     * 
     * @return <type> /
     */
    function hae_paikan_havainnot(){
        $parametriolio = $this->get_parametriolio();

        $tietokantaolio = $parametriolio->get_tietokantaolio();

        $vakipaikka_id = $parametriolio->havaintopaikka_id;
        $henkilo_id = $parametriolio->henkilo_id;

        // Painikkeita:
        $muokkausnappi = "";    // Määritellään myöhemmin
        $poistonappi = "";      // Määritellään myöhemmin
        $sulkemisnappi = "<button type='button' onclick='sulje_ruutu(\"".
                        Bongausasetuksia::$havaintotietotaulun_id."\")'>".
                        Bongauspainikkeet::$HAVAINNOT_SULJE_HENKILON_HAVAINNOT_VALUE.
                        "</button>";


        $tulos = "";

        // Muotoillaan yläluokan lause:
        $ylaluokka_id = $parametriolio->ylaluokka_id_lj;

        if(isset($ylaluokka_id) && is_numeric($ylaluokka_id) && $ylaluokka_id > 0){
            $ylaluokkaehto = "AND ".Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id";
        }
        else{
            $ylaluokkaehto = "AND ".Lajiluokka::$taulunimi.".ylaluokka_id <> -1";
        }

        // Tässäpä lausetta kerrakseen.
        $hakulause = "SELECT ".Lajiluokka::$taulunimi.".id AS laji_id,
                            ".Havainto::$taulunimi.".id AS hav_id,
                            ".Havainto::$taulunimi.".maa AS maa,
                            ".Havainto::$taulunimi.".varmuus AS varmuus,
                            henkilot.etunimi AS nimi,
                            henkilot.id AS henk_id,
                            henkilot.valtuudet AS henk_valtuudet,
                            ".Kuvaus::$taulunimi.".nimi AS laji,
                            ".Havainto::$taulunimi.".paikka AS paikka,
                            ".Havainto::$taulunimi.".kommentti AS kommentti,
                            ".Havainto::$taulunimi.".vuosi AS vuosi,
                            ".Havainto::$taulunimi.".kk AS kk,
                            ".Havainto::$taulunimi.".paiva AS paiva,
                            ".Havaintopaikka::$taulunimi.".".Havaintopaikka::$SARAKENIMI_NIMI." AS vakipaikka
                    FROM ".Lajiluokka::$taulunimi."
                    JOIN ".Kuvaus::$taulunimi."
                    ON ".Kuvaus::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                    JOIN ".Havainto::$taulunimi."
                    ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                    JOIN henkilot
                    ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                    JOIN ".Havaintopaikka::$taulunimi.
                    " ON ".Havainto::$taulunimi.".". Havainto::$SARAKENIMI_VAKIPAIKKA."=".
                        Havaintopaikka::$taulunimi.".". Havaintopaikka::$SARAKENIMI_ID.
                    
                    " WHERE (".Kuvaus::$taulunimi.".kieli= ".$parametriolio->kieli_kuv.
                    " ".$ylaluokkaehto."
                    AND ".Havaintopaikka::$taulunimi.".".Havaintopaikka::$SARAKENIMI_ID.
                        "=".$vakipaikka_id.")
                    ORDER by vuosi DESC, kk DESC, paiva DESC, laji;
                   ";

        $havaintotaulu = 
                $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);

        if(empty($havaintotaulu)){
            $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                    $sulkemisnappi."</div>";
            $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
                    <tr>
                    <th>".Bongaustekstit::$ilm_ei_havaintoja."</th></tr></table>";
                    //$hakulause;
        }
        else{ // Muotoillaan tiedot nätisti:
            
            $havainto = $havaintotaulu[0];
        
            // Maa :
            $maa = ", ".Maat::hae_maan_nimi($havainto->maa);
            $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                    "Havainnot (".$havainto->vakipaikka.
                    $maa.")".$sulkemisnappi."</div>";

            $omaid = $parametriolio->get_omaid();
            $toiminto_otsikko = "";
            if(($henkilo_id == $omaid) || ($omaid == Valtuudet::$HALLINTA)){
                $toiminto_otsikko = "<th>Toiminnot</th>";
            }

            $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
                <tr>
                    <th>Nro</th>
                    <th>Laji</th>
                    <th>Aika</th>
                    <th>Henkilö</th>
                    $toiminto_otsikko
                </tr>";


            $laskuri = 1; // Auttaa joka toisen rivin eri väriseksi.

            foreach ($havaintotaulu as $havainto) {

                // Määritellään omille havainnoille muokkaus- ja poistopainikkeet:
                // painike, josta saa näkyviin havaintolomakkeen:
                $muokkausnappi = ""; // Nollataan, ettei kummittele!
                $poistonappi = "";
                $toimintopainikkeet = "";   // Omistajalle ja hallitsijalle ei-tyhjä.

                // Omiin havaintoihin ja hallitsijan oikeuksilla saa muokata ja
                // poistaa
                if(($henkilo_id == $omaid) || ($omaid == Valtuudet::$HALLINTA)){

                    $class = "rinnakkain";
                    $id = "";
                    $action = "index.php?id_hav=".$havainto->hav_id.
                                "&id_lj=".$havainto->laji_id;
                    $name = Bongaustoimintonimet::$havaintotoiminto;
                    $value = Bongauspainikkeet::$MUOKKAA_HAVAINTO_VALUE;
                    $muokkausnappi =
                        Html::luo_painikelomake($class, $id, $action, $name, $value);

                    $name = Bongaustoimintonimet::$havaintotoiminto;
                    $value = Bongauspainikkeet::$POISTA_HAVAINTO_VALUE;
                    $poistonappi =
                        Html::luo_painikelomake($class, $id, $action, $name, $value);


                    $toimintopainikkeet = "<td>".$muokkausnappi.$poistonappi."</td>";
                }

                

                // Vain epävarmuus näytetään
                $varmuus = "";
                if($havainto->varmuus == Varmuus::$epavarma){
                    $varmuus = " (?)";
                }

                // Muokataan aika:
                $aika = Aika::anna_viikonp_suomeksi($havainto->paiva,
                                                $havainto->kk,
                                                $havainto->vuosi,
                                                true)." ".
                                                $havainto->paiva.".".
                                                $havainto->kk.".".
                                                $havainto->vuosi;

                $henk_tiedot = $havainto->nimi;
                if($laskuri % 2 == 0)
                {
                    $tulos .= "<tr class =".
                        Bongausasetuksia::$tietotaulu_parillinenrivi_class.">";
                }
                else
                {
                    $tulos .= "<tr>";
                }

                $tulos .= "<td>".$laskuri."</td>";
                $tulos .= "<td>".$havainto->laji.$varmuus."</td>";
                $tulos .= "<td>".$aika."</td>";
                $tulos .= "<td>".$henk_tiedot."</td>";
                $tulos .= $toimintopainikkeet;
                $tulos .= "</tr>";

                $laskuri++;
            }

            $tulos .= "</table>";
        }

        return $tulos;
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
    
    public function toteuta_hae_vakipaikan_havainnot(&$palauteolio){
        
        $sisalto = $this->hae_paikan_havainnot();
        $palauteolio->set_sisalto($sisalto);
        
        // Tämä nyt enempi kosmeettinen, ennenkuin keksin jotakin. Nyt aina ok..
        $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
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
     * Muodostaa vakipaikkalomakkeen ja asettaa sen sekä sisältö- että 
     * ajax-response-muuttujan arvoksi.
     * @param Palaute $palauteolio
     */
    public function toteuta_nayta_vakipaikkalomake(&$palauteolio){
        
        $omaid = $this->get_parametriolio()->get_omaid();
        $mie = new Henkilo($oma_id, $this->get_tietokantaolio());
        if($mie->olio_loytyi_tietokannasta){
            $asuinmaa_id = $mie->get_arvo(Henkilo::$sarakenimi_asuinmaa);
        }
        
        $vakipaikka_id = $this->get_parametriolio()->havaintopaikka_id;
        $vakipaikka = new Havaintopaikka($vakipaikka_id, $this->get_tietokantaolio());
        
        if($vakipaikka->olio_loytyi_tietokannasta){
            $paikka = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_NIMI);
            $selitys = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_SELITYS);
            $maa_id = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_MAA_ID);
        } else{
            $paikka = "";
            $selitys = "";
            $maa_id = $asuinmaa_id;  
        }
        
        $lomake = $this->havaintonakymat->nayta_vakipaikkalomake(
                                $vakipaikka_id, $paikka, $selitys, $maa_id);
        
        $palauteolio->set_ajax_response($lomake);
        $palauteolio->set_sisalto($lomake);
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
     * maata, varmuutta ja kommenttia ja vakipaikkaa. Yhtä muokattaessa myös 
     * lajiluokkaa voi muuttaa. Tulee siis vähän erilaiset lomakkeet.</p>
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
            // Siepataan maa ja paikka vakipaikalta, jos sellainen määritelty:
            $maa = $this->get_parametriolio()->maa_hav;
            $paikka = $this->get_parametriolio()->paikka_hav;

            // Jos vakipaikka määritelty, haetaan maa ja paikka(?) siltä:
            $vakipaikka_id = $this->get_parametriolio()->havaintopaikka_id;
            if($vakipaikka_id != Havaintopaikka::$ei_asetettu){
                $vakipaikka = 
                    new Havaintopaikka($vakipaikka_id, $this->get_tietokantaolio());
                if($vakipaikka->olio_loytyi_tietokannasta){
                    $maa = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_MAA_ID);
                    $paikka = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_NIMI);
                }
            }
            
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
                    $muokattava->set_maa($maa);
                    $muokattava->set_paikka($paikka);
                    $muokattava->set_varmuus($this->
                                    get_parametriolio()->varmuus_hav);
                    
                    $muokattava->set_vakipaikka(
                            $this->get_parametriolio()->havaintopaikka_id);
                    
                    if($this->get_parametriolio()->kommentti_hav !==
                        Parametrit::$EI_MAARITELTY){ 
                        $muokattava->set_kommentti($this->
                                    get_parametriolio()->kommentti_hav);  
                    }
                   
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
                    $muokattava->set_maa($maa);
                    $muokattava->set_paikka($paikka);
                    $muokattava->set_varmuus($this->
                                    get_parametriolio()->varmuus_hav);
                    $muokattava->set_vakipaikka(
                            $this->get_parametriolio()->havaintopaikka_id);
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
    
    /**
     * Toteuttaa monen havainnon tallennuslomakkeen näyttämisen.
     * @param type $palauteolio
     */
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
        
        $palauteolio->set_sisalto(
            $havaintonakymat->
                nayta_uusi_monen_havainnon_lomake($this->get_parametriolio()));
        $palauteolio->set_nayttomoodi(
                Html_tulostus::$nayttomoodi_yksipalkki);
    }
    
    /**
     * Toteuttaa yksittäisen uuden havainnon tallennuksen.
     * @param Palaute $palauteolio
     */
    public function toteuta_tallenna_uusi(&$palauteolio) {
        
        $id_lj = $this->get_parametriolio()->lajiluokka_id_hav;
        $uusi = $this->luo_aseta_tallenna_havainto($this->get_parametriolio(), $id_lj);
        
        if($uusi instanceof Havainto){

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
            $tallentaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                    $this->get_tietokantaolio());
            $tallentaja->paivita_aktiivisuus(Aktiivisuus::$HAVAINTOTALLENNUS_UUSI);
            
            $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                    
                    
            
        }
        else{
            $palauteolio->set_onnistumispalaute(
                            Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_UUSI);
            
            $palaute = Bongaustekstit::$ilm_havainnon_lisays_eiok.
                    Html::luo_br().
                    $this->tulosta_virheilmoitukset();
            
            // Parametriolion kautta saadaan lomakkeeseen palaute myös.
            $this->get_parametriolio()->set_tallennuspalaute($palaute);
            $palauteolio->set_ilmoitus($palaute);
            
            // Asetetaan valituksi uusi: ???
            //$this->valittujen_idt = array($uusi->get_id());
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
        $havjakslinkit_lkm = 0;
        $tallennusten_lkm = 0;
        $virheiden_lkm = 0;
        $virheilmot = "";
        $tallennetut_lajit = "";    // Nimet kerätään tähän.
        
        $tallentaja = new Henkilo($this->get_parametriolio()->get_omaid(), 
                                $this->get_tietokantaolio());
        
        $param = $this->get_parametriolio();
        //======================================================================
        // Tallennetaan uusi tapahtuma (Havaintojakso), ellei valittu jo
        // tallennettua.
        $havjakstallennus = $this->luo_havaintojakso_olio($param, $tietokantaolio);
        $havjaks = new Havaintojakso($param->id_havjaks, $tietokantaolio);
        
        // Jatketaan vain, jos havaintojakso tietokannassa:
        if($havjaks->olio_loytyi_tietokannasta){
            foreach ($valinnat as $id_lj) {

                $havainto = $this->luo_aseta_tallenna_havainto($param, $id_lj);
                
                if($havainto->olio_loytyi_tietokannasta){
                    
                    $tallennusten_lkm++;

                    // Tallennetaan havainnon lisäluokitukset:==========================
                    $valitut = $this->get_parametriolio()->lisaluokitusvalinnat_hav;
                    foreach ($valitut as $lisaluokitusarvo) {
                        $palaute = $havainto->tallenna_uusi_lisaluokitus($lisaluokitusarvo);
                        if($palaute === Havainto::$VIRHE){
                            $palauteolio->lisaa_virheilmoitus(
                                Bongaustekstit::$ilm_havainnon_lisaluokan_tallennus_eiok.
                                " ".$havainto->tulosta_virheilmoitukset());
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
                    
                    // Tallennetaan vielä linkki havaintojaksoon:
                    $tarkista = !$param->uusi_havjaks;
                    $lisays = $havjaks->lisaa_havainto($havainto->get_id(), 
                                                        $tarkista);
                    // Lasketaan lisäykset:
                    if($lisays === Havainto::$OPERAATIO_ONNISTUI){
                        $havjakslinkit_lkm ++;
                    } else{
                        //$this->lisaa_virheilmoitus($lisays);    //Testiä varten
                    }
                }
                else{
                    $virheiden_lkm++;
                    $virheilmot .= $this->tulosta_virheilmoitukset()."<br />";
                }

                $laskuri++;
            }
            // Aktiivisuusmerkintä (vain kerran):
            $tallentaja->paivita_aktiivisuus(Aktiivisuus::$HAVAINTOTALLENNUS_UUSI);

            // Palautteet:
            if($tallennusten_lkm == sizeof($valinnat)){
                $kommentti = $tallennusten_lkm." ".
                                Bongaustekstit::$ilm_havaintojen_lisays_ok.
                                " (".$tallennetut_lajit.") ".
                                Bongaustekstit::$ja." ".
                                $havjakslinkit_lkm." ".
                                Bongaustekstit::$ilm_havaintojaksolinkkeja_luotu_kpl;
            }
            else{
                $kommentti = $virheiden_lkm." ".
                            Bongaustekstit::$ilm_havaintojen_lisays_eiok."<br/>".
                            $virheilmot;
            }

            $palauteolio->set_ilmoitus($kommentti);

            // Avataan havainnot.
            $this->toteuta_nayta($palauteolio);
            
        } else{ // Kun havaintojakson tallennus ei onnistunut.
            $kommentti = Bongaustekstit::$havaintojakso_virheilm_tallennus_eiok.
                        " ".$this->tulosta_kaikki_ilmoitukset();
            $palauteolio->set_ilmoitus($kommentti);
            $this->toteuta_nayta_moniuusitallennuslomake($palauteolio);
        }
    }
    
    
    /**
     * Toteuttaa uuden havaintopaikan tai vanhan muutosten tallennuksen 
     * tietokantaan. Asettaa ajax-kutsua varten xml-koodin ja muuten normaalisti
     * sisällöt ja ilmoitukset.
     * 
     * @param Palaute $palauteolio
     */
    function toteuta_tallenna_vakipaikka_uusivanha(&$palauteolio){
        $omaid = $this->get_parametriolio()->get_omaid();
        
        $uuden_tallennus = true;
        $tallennus_ok = false;
        
        $id = $this->get_parametriolio()->havaintopaikka_id;
        $paikannimi = $this->get_parametriolio()->havaintopaikka_nimi;
        $selitys = $this->get_parametriolio()->havaintopaikka_selitys;
        $maa = $this->get_parametriolio()->havaintopaikka_maa;
        
        $vpaikka = new Havaintopaikka($id, $this->get_tietokantaolio());

        if($vpaikka->olio_loytyi_tietokannasta){
            $uuden_tallennus = false;
        }
        
        $vpaikka->set_arvo($omaid, Havaintopaikka::$SARAKENIMI_HENKILO_ID);
        $vpaikka->set_arvo($paikannimi, Havaintopaikka::$SARAKENIMI_NIMI);
        $vpaikka->set_arvo($selitys, Havaintopaikka::$SARAKENIMI_SELITYS);
        $vpaikka->set_arvo($maa, Havaintopaikka::$SARAKENIMI_MAA_ID);
        
        // Painike on jo näkyvissä muokkauksess, mutta uuden luomisen yhteydessä
        // lisätään muokkausnappi.
        $muokkausnappispan_id = "";
        $muokkausnappi = "";
        
        if($uuden_tallennus){
            if($vpaikka->tallenna_uusi() === Havainto::$OPERAATIO_ONNISTUI){

                $this->toteuta_nayta_yksi_uusi_lomake($palauteolio);
                $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_havaintopaikan_lisays_ok);
                $palauteolio->set_muokatun_id($vpaikka->get_id());

                $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                $tallennus_ok = true;
                
                $muokkausnappispan_id = Havaintonakymat::$muokkausnappispan_id;
                $muokkausnappi = htmlspecialchars($this->havaintonakymat->
                    luo_havaintopaikka_muokkauspainike($vpaikka->get_id()));
                
            }
            else{
                $palauteolio->set_onnistumispalaute(
                                Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_UUSI);

                $palaute = Bongaustekstit::$virheilm_havaintopaikan_lisays_eiok.
                        Html::luo_br().
                        $vpaikka->tulosta_virheilmoitukset();

                // Parametriolion kautta saadaan lomakkeeseen palaute myös.
                $this->get_parametriolio()->set_tallennuspalaute($palaute);
                $palauteolio->set_ilmoitus($palaute);

                // Asetetaan valituksi uusi: 
                $palauteolio->set_sisalto(
                    $this->havaintonakymat->nayta_vakipaikkalomake(
                        Havaintopaikka::$MUUTTUJAA_EI_MAARITELTY, 
                        $paikannimi, $selitys, $maa));
            }

        } else{ // vanhan muokkaus
            
            
            if($vpaikka->tallenna_muutokset() === Havainto::$OPERAATIO_ONNISTUI){

                $this->toteuta_nayta_yksi_uusi_lomake($palauteolio);
                $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_havaintopaikan_muutos_ok);
                $palauteolio->set_muokatun_id($vpaikka->get_id());
                $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
                $tallennus_ok = true;
            }
            else{
                $palauteolio->set_onnistumispalaute(
                                Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_MUOKKAUS);

                $palaute = Bongaustekstit::$virheilm_havaintopaikan_muutos_eiok.
                        Html::luo_br().
                        $vpaikka->tulosta_virheilmoitukset();

                // Parametriolion kautta saadaan lomakkeeseen palaute myös.
                $this->get_parametriolio()->set_tallennuspalaute($palaute);
                $palauteolio->set_ilmoitus($palaute);

                // Asetetaan valituksi uusi: 
                $palauteolio->set_sisalto(
                    $this->havaintonakymat->nayta_vakipaikkalomake(
                                            $id,$paikannimi, $selitys, $maa));
            }
        }
        
        
        //============================= ajax-koodin muodostaminen
        $koodaus = Yleisasetuksia::$koodaus;
        $havaintonakymat = $this->havaintonakymat;
        $vakipaikkavalikon_id = Havaintonakymat::$vakipaikkavalikon_id;

        $success = 0;
        if($tallennus_ok){
            $success = 1;
            $safe_paikka = htmlspecialchars($paikannimi);
        } else{
            $maa = -1;
            $safe_paikka = "";
        }
        $kommentti = htmlspecialchars($palauteolio->tulosta_kaikki_ilmoitukset());

        $vakipaikkavalikko = 
            $havaintonakymat->luo_havaintopaikkavalikko(
                $vpaikka->get_id(), 
                $this->get_parametriolio()->get_omaid());

        $html = htmlspecialchars($vakipaikkavalikko);

        $paikkakentta_id = Havaintonakymat::$havaintopaikkakentta_id;
        $maavalikko_id = Havaintonakymat::$havaintomaavalikko_id;

        // xml-muodossa saadaan muutkin tiedot mukaan:
        $xml ='<?xml version="1.0" encoding="'.$koodaus.'"?>'.
            '<tiedot>'.
            '<success>'.$success.'</success>'.
            '<kommentti>'.$kommentti.'</kommentti>'.
            '<dropdown>'.$html.'</dropdown>'.
            '<dropdown_id>'.$vakipaikkavalikon_id.'</dropdown_id>'.
            '<paikka>'.$safe_paikka.'</paikka>'.
            '<maa_id>'.$maa.'</maa_id>'.
            '<paikkakentta_id>'.$paikkakentta_id.'</paikkakentta_id>'.
            '<maavalikko_id>'.$maavalikko_id.'</maavalikko_id>'.
            '<muokkausnappispan_id>'.$muokkausnappispan_id.'</muokkausnappispan_id>'.
            '<muokkausnappi>'.$muokkausnappi.'</muokkausnappi>'.
        '</tiedot>';
        
        $palauteolio->set_ajax_response($xml);
    }
    /**
     * Toteuttaa havaintopaikan muutosten tallennuksen tietokantaan.
     *
    function toteuta_tallenna_vakipaikkamuutokset(&$palauteolio){
        
        $vakipaikka_id = $this->get_parametriolio()->havaintopaikka_id;
        $paikannimi = $this->get_parametriolio()->havaintopaikka_nimi;
        $selitys = $this->get_parametriolio()->havaintopaikka_nimi;
        $maa = $this->get_parametriolio()->havaintopaikka_maa;
        
        $uusi = new Havaintopaikka($vakipaikka_id, $this->get_tietokantaolio());
        
        if($uusi->olio_loytyi_tietokannasta){
           
            $uusi->set_arvo($paikannimi, Havaintopaikka::$SARAKENIMI_NIMI);
            $uusi->set_arvo($selitys, Havaintopaikka::$SARAKENIMI_SELITYS);
            $uusi->set_arvo($maa, Havaintopaikka::$SARAKENIMI_MAA_ID);

            if($uusi->tallenna_muutokset() === Havainto::$OPERAATIO_ONNISTUI){

                $this->toteuta_nayta_yksi_uusi_lomake($palauteolio);
                $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_havaintopaikan_muutos_ok);
                $palauteolio->set_muokatun_id($uusi->get_id());
                $palauteolio->set_onnistumispalaute(Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK);
            }
            else{
                $palauteolio->set_onnistumispalaute(
                                Palaute::$ONNISTUMISPALAUTE_VIRHE_TALLENNUS_MUOKKAUS);

                $palaute = Bongaustekstit::$virheilm_havaintopaikan_muutos_eiok.
                        Html::luo_br().
                        $uusi->tulosta_virheilmoitukset();

                // Parametriolion kautta saadaan lomakkeeseen palaute myös.
                $this->get_parametriolio()->set_tallennuspalaute($palaute);
                $palauteolio->set_ilmoitus($palaute);

                // Asetetaan valituksi uusi: 
                $palauteolio->set_sisalto(
                    $this->havaintonakymat->nayta_vakipaikkalomake(
                        $vakipaikka_id,$paikannimi, $selitys, $maa));
            }
        } else{
            $this->toteuta_nayta_yksi_uusi_lomake($palauteolio);
            $palauteolio->set_ilmoitus(Bongaustekstit::$ilm_havaintopaikkaa_ei_loytynyt);
        }
    }*/
    
    /**
     * Luo uuden Havainto-luokan olion, hakee tiedot parametrioliosta ja 
     * tallentaa tietokantaan. 
     * Onnistuesssaan palauttaa Havainto-luokan olion. Muussa tapauksessa 
     * palauttaa arvon Havainto::$VIRHE ja jättää tarvittaessa ilmoituksen 
     * Havaintokontrollerioliolle.
     * @param Parametrit $parametriolio
     * @param int $id_lj Valitun lajin id.
     * @return type
     */
    private function luo_aseta_tallenna_havainto(&$parametriolio, $id_lj){
        
        $palautusarvo = Havainto::$VIRHE;
        $tietokantaolio = $parametriolio->get_tietokantaolio();
       
        $hav = new Havainto(Havainto::$MUUTTUJAA_EI_MAARITELTY, $tietokantaolio);
        
        // Ellei kommenttia määritelty, tallennetaan tyhjä merkkijono:
        if($parametriolio->kommentti_hav === Parametrit::$EI_MAARITELTY){
            $parametriolio->kommentti_hav = "";
        }
         
        $maa = $this->get_parametriolio()->maa_hav;
        $paikka = $this->get_parametriolio()->paikka_hav;
        
        // Jos vakipaikka määritelty, haetaan maa ja paikka(?) siltä:
        $vakipaikka_id = $this->get_parametriolio()->havaintopaikka_id;
        
        if($vakipaikka_id != Havaintopaikka::$ei_asetettu){
            $vakipaikka = 
                new Havaintopaikka($vakipaikka_id, $this->get_tietokantaolio());
            if($vakipaikka->olio_loytyi_tietokannasta){
                $maa = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_MAA_ID);
                $paikka = $vakipaikka->get_arvo(Havaintopaikka::$SARAKENIMI_NIMI);
            }
        }
        
        // Jos lkm on tyhjä, annetaan arvo ei-määritelty.
        if(empty($this->get_parametriolio()->lkm_hav)){
            $this->get_parametriolio()->lkm_hav =
                        Parametrit::$EI_MAARITELTY;
        }
        
        $hav->set_henkilo_id($this->get_parametriolio()->get_omaid());
        $hav->set_lajiluokka_id($id_lj);
        $hav->set_paiva($this->get_parametriolio()->paiva_hav);
        $hav->set_kk($this->get_parametriolio()->kk_hav);
        $hav->set_vuosi($this->get_parametriolio()->vuosi_hav);
        $hav->set_paikka($paikka);
        $hav->set_kommentti($this->get_parametriolio()->kommentti_hav);
        $hav->set_maa($maa);
        $hav->set_varmuus($this->get_parametriolio()->varmuus_hav);
        $hav->set_vakipaikka($vakipaikka_id);
        $hav->set_arvo($this->get_parametriolio()->sukupuoli_hav, 
                        Havainto::$SARAKENIMI_SUKUPUOLI);
        $hav->set_arvo($this->get_parametriolio()->lkm_hav, 
                        Havainto::$SARAKENIMI_LKM);
        
        $palaute = $hav->tallenna_uusi();
        if($palaute === Malliluokkapohja::$OPERAATIO_ONNISTUI){
            $parametriolio->id_hav = $hav->get_id();
            $palautusarvo = $hav;
            echo "vakipaikka_id=".$hav->get_vakipaikka();
        } else{
            $this->lisaa_virheilmoitus($hav->tulosta_virheilmoitukset());
            echo "virhe - vakipaikka_id=".$hav->get_arvo(Havainto::$SARAKENIMI_VAKIPAIKKA)." ja onnistuminen=".$palaute;
        }
        return $palautusarvo;
    }

    /**
     * Luo uuden Havaintojakso-luokan olion ja tallentaa sen tietokantaan.
     * Onnistuesssaan, tai kun havaintojakso jo olemassa, palauttaa arvon 
     * OPERAATIO_ONNISTUI. Muussa tapauksessa palauttaa arvon VIRHE
     * ja jättää tarvittaessa ilmoituksen Havaintokontrollerioliolle.
     * @param Parametrit $parametriolio
     * @param Tietokantaolio $tietokantaolio
     */
    private function luo_havaintojakso_olio(&$parametriolio, $tietokantaolio){
        // Tallennetaan uusi tapahtuma (Havaintojakso), ellei valittu jo
        // tallennettua.
        $palautusarvo = Havaintojakso::$OPERAATIO_ONNISTUI;
        
        $param = $parametriolio;    // Lyhempi vain..
 
        if($param->id_havjaks+0 === Parametrit::$EI_MAARITELTY){
            $param->uusi_havjaks = true;
            
            $uusi = new Havaintojakso(Havaintojakso::$MUUTTUJAA_EI_MAARITELTY, 
                                        $tietokantaolio);
            
            // Haetaan ja muotoillaan alkuaika (unix time stamp) ja kesto (min):
            $vuosi = $param->alkuaika_vuosi_havjaks;
            $kk = $param->alkuaika_kk_havjaks;
            $paiva = $param->alkuaika_paiva_havjaks;
            $h = $param->alkuaika_h_havjaks;
            if($h === "" || $h < 0){ $h = 0;}    // Jos jätetty tyhjäksi.
            $min = $param->alkuaika_min_havjaks;
            if($min == "" ||$min < 0){ $min = 0;}
            
            $sek = 0;   // Sekunteja ei tallenneta.
            
            /*$alkuaika = new DateTime($vuosi."-".$kk."-".$paiva." ".
                                        $h.":".$min.":01");

            $alkuaika_sek = $alkuaika->getTimestamp();*/ // Ei parsinta onnannu.
            
            $alkuaika_sek = mktime($h, $min, $sek, $kk, $paiva, $vuosi);

            $param->alkuaika_sek_havjaks = $alkuaika_sek;
            
            // Haetaan kesto minuutteina:
            $kestovrk = $param->kesto_vrk_havjaks;
            $kestoh = $param->kesto_h_havjaks;
            $kestomin = $param->kesto_min_havjaks;
            
            // Ellei määritelty, pistetään nollaksi:
            if($kestovrk < 1){
                $kestovrk = 0;
            }
            if($kestoh < 1){
                $kestoh = 0;
            }
            if($kestomin < 1){
                $kestomin = 0;
            }
            
            $kestomintotal = $kestovrk * 24 * 60 + $kestoh * 60 + $kestomin; 

            $uusi->set_arvo($param->alkuaika_sek_havjaks, 
                    Havaintojakso::$SARAKENIMI_ALKUAIKA_SEK);
            
            $uusi->set_arvo($kestomintotal, 
                    Havaintojakso::$SARAKENIMI_KESTO_MIN);
            
            $uusi->set_arvo($parametriolio->get_omaid(), 
                    Havaintojakso::$SARAKENIMI_HENKILO_ID);
            
            $uusi->set_arvo($param->nimi_havjaks, 
                    Havaintojakso::$SARAKENIMI_NIMI);
            
            $uusi->set_arvo($param->kommentti_havjaks, 
                    Havaintojakso::$SARAKENIMI_KOMMENTTI);
            
            $uusi->set_arvo($param->nakyvyys_havjaks, 
                    Havaintojakso::$SARAKENIMI_NAKYVYYS);
            
            $palaute = $uusi->tallenna_uusi();
            
            if($palaute === Havaintojakso::$OPERAATIO_ONNISTUI){
                $param->id_havjaks = $uusi->get_id();

            } else{
                $palautusarvo = Havaintojakso::$VIRHE;
                $this->lisaa_virheilmoitus($uusi->tulosta_kaikki_ilmoitukset());
            }
            
        } else{ 
            $param->uusi_havjaks = false;
        }
        
        return $palautusarvo;
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