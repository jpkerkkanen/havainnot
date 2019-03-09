<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Havainto: Pitää sisällään tietokantataulun tiedot.
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
 
  primary key (id),
  index(henkilo_id),
  index(vuosi),
  index(kk),
  index(paiva),
  index(paikka),
  index(maa),
  index(lajiluokka_id),
  FOREIGN KEY (lajiluokka_id) REFERENCES lajiluokat (id)
                      ON DELETE CASCADE,
  FOREIGN KEY (henkilo_id) REFERENCES henkilot (id)
                      ON DELETE CASCADE

) ENGINE=INNODB;    
 * @author J-P
 * 
 * Sidoksia: Kontrolleri_pikakommentit
 */
class Havainto extends Malliluokkapohja {
    
    // Liittyy muokkaukseen: jos lajiluokka muuttuu, pitää myös havaintoon
    // liittyvien kuvien linkit muokata.
    private $vanha_lajiluokka_id;   
    
    // Tiedonvälityksen avuksi:
    private $poistetut_pikakommentit_lkm, $muutetut_lajikuvalinkit_lkm;

    public static $SARAKENIMI_HENKILO_ID= "henkilo_id";
    public static $SARAKENIMI_LAJILUOKKA_ID= "lajiluokka_id";
    public static $SARAKENIMI_VUOSI= "vuosi";
    public static $SARAKENIMI_KK= "kk";
    public static $SARAKENIMI_PAIVA= "paiva";
    
    public static $SARAKENIMI_PAIKKA= "paikka";
    public static $SARAKENIMI_KOMMENTTI= "kommentti";
    public static $SARAKENIMI_MAA= "maa";
    public static $SARAKENIMI_VARMUUS= "varmuus";
    public static $SARAKENIMI_SUKUPUOLI= "sukupuoli";
    
    public static $SARAKENIMI_LKM= "lkm";
    
    public static $taulunimi = "havainnot";
    /**
     * @param Tietokantaolio $tietokantaolio
     * @param int $id olion id tietokannassa
     */
    function __construct($id, $tietokantaolio){
        $tietokantasolut = 
            array(new Tietokantasolu(Havainto::$SARAKENIMI_ID, Tietokantasolu::$luku_int,$tietokantaolio),  
                new Tietokantasolu(Havainto::$SARAKENIMI_HENKILO_ID, Tietokantasolu::$luku_int,$tietokantaolio), 
                new Tietokantasolu(Havainto::$SARAKENIMI_LAJILUOKKA_ID, Tietokantasolu::$luku_int,$tietokantaolio), 
                new Tietokantasolu(Havainto::$SARAKENIMI_VUOSI, Tietokantasolu::$luku_int,$tietokantaolio), 
                new Tietokantasolu(Havainto::$SARAKENIMI_KK,Tietokantasolu::$luku_int,$tietokantaolio), 
                
                new Tietokantasolu(Havainto::$SARAKENIMI_PAIVA, Tietokantasolu::$luku_int,$tietokantaolio), 
                new Tietokantasolu(Havainto::$SARAKENIMI_PAIKKA, Tietokantasolu::$mj_tyhja_EI_ok,$tietokantaolio), 
                new Tietokantasolu(Havainto::$SARAKENIMI_KOMMENTTI, Tietokantasolu::$mj_tyhja_ok,$tietokantaolio), 
                new Tietokantasolu(Havainto::$SARAKENIMI_MAA, Tietokantasolu::$luku_int,$tietokantaolio), 
                new Tietokantasolu(Havainto::$SARAKENIMI_VARMUUS, Tietokantasolu::$luku_int,$tietokantaolio), 
                
                new Tietokantasolu(Havainto::$SARAKENIMI_SUKUPUOLI, Tietokantasolu::$luku_int,$tietokantaolio), 
                new Tietokantasolu(Havainto::$SARAKENIMI_LKM, Tietokantasolu::$luku_int,$tietokantaolio));
        
        $taulunimi = Havainto::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
        
        $this->poistetut_pikakommentit_lkm = 0;
        $this->muutetut_lajikuvalinkit_lkm = 0;
        
        $this->vanha_lajiluokka_id = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        
    }
    // Getterit ja setterit:
    public function get_henkilo_id(){
        return $this->get_arvo(Havainto::$SARAKENIMI_HENKILO_ID);
    }
    public function set_henkilo_id($uusi){
        return $this->set_arvo($uusi, Havainto::$SARAKENIMI_HENKILO_ID);
    }
    public function get_lajiluokka_id(){
        return $this->get_arvo(Havainto::$SARAKENIMI_LAJILUOKKA_ID);
    }
    public function set_lajiluokka_id($uusi){
        return $this->set_arvo($uusi, Havainto::$SARAKENIMI_LAJILUOKKA_ID);
    }
    public function get_kommentti(){
        return $this->get_arvo(Havainto::$SARAKENIMI_KOMMENTTI);
    }
    
    // Kommentti ja paikka tarkistetaan heti täällä, ettei vahingollista tule
    // tietokantaan.
    public function set_kommentti($uusi){
        return $this->set_arvo($uusi, Havainto::$SARAKENIMI_KOMMENTTI);
    }
    public function get_paikka(){
        return $this->get_arvo(Havainto::$SARAKENIMI_PAIKKA);
    }
    public function set_paikka($uusi){
        return $this->set_arvo($uusi, Havainto::$SARAKENIMI_PAIKKA);
    }
    public function get_vuosi(){
        return $this->get_arvo(Havainto::$SARAKENIMI_VUOSI);
    }
    public function set_vuosi($uusi){
        return $this->set_arvo($uusi, Havainto::$SARAKENIMI_VUOSI);
    }
    public function get_kk(){
        return $this->get_arvo(Havainto::$SARAKENIMI_KK);
    }
    public function set_kk($uusi){
        return $this->set_arvo($uusi, Havainto::$SARAKENIMI_KK);
    }
    public function get_paiva(){
        return $this->get_arvo(Havainto::$SARAKENIMI_PAIVA);
    }
    public function set_paiva($uusi){
       return $this->set_arvo($uusi, Havainto::$SARAKENIMI_PAIVA);
    }
    public function get_maa(){
        return $this->get_arvo(Havainto::$SARAKENIMI_MAA);
    }
    public function set_maa($uusi){
        return $this->set_arvo($uusi, Havainto::$SARAKENIMI_MAA);
    }
    public function get_varmuus(){
        return $this->get_arvo(Havainto::$SARAKENIMI_VARMUUS);
    }
    public function set_varmuus($uusi){
        return $this->set_arvo($uusi, Havainto::$SARAKENIMI_VARMUUS);
    }
    
    public function get_sukupuoli(){
        return $this->get_arvo(Havainto::$SARAKENIMI_SUKUPUOLI);
    }
    public function get_lkm(){
        return $this->get_arvo(Havainto::$SARAKENIMI_LKM);
    }
    
    public function get_poistetut_pikakommentit_lkm(){
        return $this->poistetut_pikakommentit_lkm;
    }
    public function get_muutetut_lajikuvalinkit_lkm(){
        return $this->muutetut_lajikuvalinkit_lkm;
    }
    
    public function get_vanha_lajiluokka_id(){
        return $this->vanha_lajiluokka_id;
    }
    public function set_vanha_lajiluokka_id($uusi){
        return $this->vanha_lajiluokka_id = $uusi;
    }
    
    /**
     * Palauttaa nätisti muotoillun (suomalaiseen tyyliin) viikonpäivän
     * ja päivämäärän. Kansainvälinen jakelu vaatii hiomista..
     * @return string
     */
    public function hae_pvm(){
        // Muokataan aika:
        $pvm = Aika::anna_viikonp_suomeksi($this->get_paiva(),
                                        $this->get_kk(),
                                        $this->get_vuosi(),
                                        true)." ".
                                        $this->get_paiva().".".
                                        $this->get_kk().".".
                                        $this->get_vuosi();
        return $pvm;
    }
    
    

    /**
     * Poistaa tämän olion tiedot tietokannasta ja palauttaa arvon
     * Havainto::OPERAATIO_ONNISTUI, jos kaikki menee hyvin. Muuten palautetaan
     * arvo Havainto::VIRHE. Virheilmoitukset tallennetaan ilmoitustaulukkoon.
     * 
     * Poiston jälkeen pitää siivota tietokanta
     * siivoa_tietokanta_poiston_jalkeen-metodin avulla. Poistettujen
     * pikakommenttien lkm otetaan talteen $this->poistetut_pikakommentit_lkm-
     * muuttujaan.
     */
    public function poista() {
        $palaute = parent::poista();
        
        if($palaute === Havainto::$OPERAATIO_ONNISTUI){
            $this->siivoa_tietokanta_poiston_jalkeen();
        }
        else{
            $ilmoitus = Bongaustekstit::$ilm_havainnon_poisto_eiok;
            $this->lisaa_virheilmoitus($ilmoitus);
        }
        return $palaute;
    }
    
    /**
     * Poistaa poistettuun havaintoon osoittavat pikakommentit.
     * 
     */
    private function siivoa_tietokanta_poiston_jalkeen(){
        $this->poistetut_pikakommentit_lkm = 
                Pikakommenttikontrolleri::
                            poista_pikakommentit($this->tietokantaolio, 
                                                Pikakommentti::$KOHDE_BONGAUS, 
                                                $this->get_id());
        
    }
    
    /**
     * Korjaa lajikuvalinkit silloin, kun havainnon lajiluokkaa muutetaan. 
     * Havaintokuvalinkki ei ole ongelma, mutta kyseisen kuvan lajikuvalinkki
     * osoittaa muutoksen jälkeen eri lajiin kuin havainto! Tämä on kyllä vähän
     * virhealtis homma. Hmm. KANNATTAISI TESTATA!!!..
     * 
     * Menettely on seuraava: haetaan tähän havaintoon viittaavat 
     * havaintokuvalinkit (kuvia voi olla useita) ja niiden avulla vastaavat 
     * lajikuvalinkit (aina olemassa yksi). Sitten vain muutetaan
     * lajikuvalinkkien lajiluokka_id-muuttujan arvo nykyiseksi.
     * 
     * HUOM! ENTÄ JOS LAJIKUVALINKKIÄ MUUTETAAN SUORAAN JA KUVA KUULUU
     * HAVAINTOON? TÄLLÖIN PITÄISI HAVAINNON LAJILUOKKA MUUTTAA? TAI ESTÄÄ
     * MUOKKAUS SUORAAN KUVASTA? ESTETÄÄN TÄSSÄ SOVELLUKSESSA SUORA MUOKKAUS 
     * TOISTAISEKSI.
     * 
     * Palauttaa joko Havainto::$VIRHE tai Havainto::$OPERAATIO_ONNISTUI.
     * Pitää lukuja korjatuista linkeistä ja tallentaa virheilmoituksen, jos
     * jokin mättää.
     * 
     * Tässä on edelleen sotkun mahdollisuus, jos sama kuva asetetaan osoittamaan
     * kahteen erilliseen saman lajin havaintoon ja sitten mennään muuttamaan
     * toista näistä havainnoista (sen lajiluokkaa siis). Toki tällaista ei
     * pitäisi tulla vastaan, koska kuva liittynee aina yhteen havaintoon, eikä
     * siis moneen saman lajin havaintoon..
     * 
     * @return int $muutosten_lkm nolla tai suurempi. 
     */
    public function korjaa_lajikuvalinkit_lajimuokkauksen_jalkeen(){
        
        $pal = Havainto::$VIRHE;
        
        $onnistumisten_lkm = 0;
        
        // Muutetaan tarvittaessa kuvalinkit: ============================
        if(($this->vanha_lajiluokka_id != $this->get_lajiluokka_id()) &&
            $this->vanha_lajiluokka_id !== Havainto::$MUUTTUJAA_EI_MAARITELTY){

            $havaintokuvalinkit = $this->hae_havaintokuvalinkit();

            // Käydään läpi kaikki linkit ja korjataan vastaavat
            // lajikuvalinkit.
            foreach ($havaintokuvalinkit as $havaintokuvalinkki) {
                if($havaintokuvalinkki instanceof Havaintokuvalinkki){
                    
                    // Etsitään id_kuvaa ja VANHAA id_lajiluokkaa vastaava 
                    // lajikuvalinkki, joita pitäisi olla vain yksi. Muista, 
                    // että samasta kuvasta voi kuitenkin olla lajikuvalinkkejä 
                    // eri lajeihin!
                    $taulunimi = Lajikuvalinkki::$taulunimi;
                    
                    // Haetaan kuvan ja lajiluokan idt:
                    $id_kuva = $havaintokuvalinkki->
                                get_arvo(Havaintokuvalinkki::$sarakenimi_kuva_id);
                    
                    // Laji pitää ottaa hakuehtoon mukaan, ettei sivullisia muuteta.!
                    $id_lj_vanha = $this->get_vanha_lajiluokka_id();
                    
                    // Luodaan linkki, jotta päästään sen tietoihin:
                    $lajikuvalinkki = new Lajikuvalinkki(
                                        Lajikuvalinkki::$MUUTTUJAA_EI_MAARITELTY, 
                                        $this->tietokantaolio);
                    
                    $ehtosolu1 = $lajikuvalinkki->
                        get_tietokantasolu(Lajikuvalinkki::$sarakenimi_kuva_id);
                    $ehtosolu1->set_arvo($id_kuva);
                    
                    $ehtosolu2 = $lajikuvalinkki->
                        get_tietokantasolu(Lajikuvalinkki::$sarakenimi_lajiluokka_id);
                    $ehtosolu2->set_arvo($id_lj_vanha);
                    
                    $ehtotietokantasolut = array();
                    
                    array_push($ehtotietokantasolut, $ehtosolu1); 
                    array_push($ehtotietokantasolut, $ehtosolu2); 
                             
                    $osumat = $this->tietokantaolio->hae_tk_oliot($taulunimi, 
                                                        $ehtotietokantasolut);
       
                    // Osumia pitäisi olla yksi vain.
                    if(!empty($osumat)){
                        $lajikuvalinkki_id = $osumat[0]->id;
                        $korjattava = new Lajikuvalinkki($lajikuvalinkki_id, 
                                                        $this->tietokantaolio);
                        
                        if($korjattava->olio_loytyi_tietokannasta){
                            // Korjataan lajikuvalinkki osoittamaan oikeaan 
                            // lajiluokkaan:
                            $korjattava->set_arvo($this->get_lajiluokka_id(), 
                                        Lajikuvalinkki::$sarakenimi_lajiluokka_id);

                            $muutos = $korjattava->tallenna_muutokset();
 
                            if($muutos === Tietokantaolio::$HAKU_ONNISTUI){
                                $onnistumisten_lkm++;
                            } 
                        } else{
                            $this->lisaa_virheilmoitus(Bongaustekstit::
                                    $lajiluokka_virheilm_lajikuvalinkkia_ei_loytynyt.
                                    $korjattava->tulosta_kaikki_ilmoitukset());
                        }
                    }
                }
            }
            
            // Tämä vähän hassu, mutta se on ohjelman kannalta ok, etta sama
            // kuva yhdistetään saman lajin kahteen havaintoon, jolloin vain
            // yksi lajikuvalinkki lisätään.
            if($onnistumisten_lkm === sizeof($havaintokuvalinkit)){
                $pal = Havainto::$OPERAATIO_ONNISTUI;
                $this->lisaa_kommentti(
                    Bongaustekstit::
                        $lajikuvalinkki_ilmoitus_lj_muutokset_ok.
                        $onnistumisten_lkm."/".sizeof($havaintokuvalinkit));
            } else{
                $pal = Havainto::$VIRHE;
                $this->lisaa_virheilmoitus(
                    Bongaustekstit::
                        $lajikuvalinkki_virheilmoitus_lj_muutokset_ei_ok.
                        $onnistumisten_lkm."/".sizeof($havaintokuvalinkit));
            }
            
            
        } else{
            $pal = Havainto::$VIRHE;
            $this->lisaa_virheilmoitus(
                    Bongaustekstit::
                        $lajiluokka_virheilm_vanha_id_lj_eiok);
        }
       
        return $pal;
            //==================================================================
    }


    /**
     * Tarkistaa, onko olio tallennuskelpoinen. Lähinnä tarkistetaan, onko
     * muuttujien arvot asetettu ja määriteltyjä.
     *
     * Huom! Tämä aiheuttaa jonkin verran työtä, jos merkkijonot putsataan, joten
     * ei kannata pelkissä hauissa käyttää.
     *
     * @param type $uusi Parametrilla ei tässä merkitystä.
     */
    public function on_tallennuskelpoinen($uusi) {
        $palaute = false;

        $putsaa = true;
        $tyhja_ok1 = true;
        $tyhja_ok2 = false; // Ei saa olla tyhjä!

        if($this->lukumuotoinen_muuttuja_ok($this->henkilo_id, $putsaa, 
                                    Bongaustekstit::$havaintolomake_henkilo_id) &&
            $this->lukumuotoinen_muuttuja_ok($this->lajiluokka_id, $putsaa, 
                                    Bongaustekstit::$havaintolomake_laji) &&
            $this->mjmuotoinen_muuttuja_ok($this->kommentti, $putsaa, $tyhja_ok1, 
                                    Bongaustekstit::$havaintolomake_kommentti) &&
            $this->mjmuotoinen_muuttuja_ok($this->paikka, $putsaa, $tyhja_ok2, 
                                    Bongaustekstit::$havaintolomake_paikka) &&
            $this->lukumuotoinen_muuttuja_ok($this->vuosi, $putsaa, 
                                    Bongaustekstit::$havaintolomake_vuosi) &&
            $this->lukumuotoinen_muuttuja_ok($this->kk, $putsaa, 
                                    Bongaustekstit::$havaintolomake_kk) &&
            $this->lukumuotoinen_muuttuja_ok($this->paiva, $putsaa, 
                                    Bongaustekstit::$havaintolomake_paiva) &&
            $this->lukumuotoinen_muuttuja_ok($this->maa, $putsaa, 
                                    Bongaustekstit::$havaintolomake_maa) &&
            $this->lukumuotoinen_muuttuja_ok($this->varmuus, $putsaa, 
                                    Bongaustekstit::$havaintolomake_varmuus)
            ){

            $palaute = true;
        }
        return $palaute;
    }
    
    /**
     * Etsii tietokannasta parametri-id:n mukaista havaintoa. HUOM! Palauttaa
     * aina Havainto-luokan olion, joka saattaa olla tyhjä, ellei tietokannasta
     * löytynyt. Tämän asian voi varmistaa $olio_loytyi_tietokannasta-muuttujan
     * avulla (true - löytyi).
     * 
     * @param type $id
     * @param type $tietokantaolio
     * @return \Havainto
     */
    public static function etsi($id, $tietokantaolio){
        return new Havainto($id, $tietokantaolio);
    }
    
    /**
     * Hakee havaintoon liittyvät kuvat ja palauttaa ne taulukossa
     * Kuva-luokan olioina. Ellei
     * mitään löydy, palauttaa tyhjän taulukon.
     * 
     */
    public function hae_kuvat(){
        
        $kuvataulukko = array();
        
        /* Haetaan havaintoon liittyvät havaintokuvalinkit */
        $linkit = $this->hae_havaintokuvalinkit();
        
        foreach ($linkit as $linkki) {
            if($linkki instanceof Havaintokuvalinkki){
                $kuva_id = $linkki->get_arvo(Havaintokuvalinkki::$sarakenimi_kuva_id);
                $kuva = new Kuva($kuva_id, $this->tietokantaolio);
                
                // Otetaan mukaan, jos vielä olion tiedot löytyivät tietokannasta:
                if($kuva->olio_loytyi_tietokannasta){
                    array_push($kuvataulukko, $kuva);
                }
            }
        }
        
        return $kuvataulukko;
    }
    /**
     * Hakee tietyn poppoon tietyn kauden havainnot tietokannasta ja palauttaa ne 
     * (Havainto-luokan oliot) taulukossa.
     * Yläluokka ja kieli ja näyttötapa saadaan parametrioliosta.
     * 
     * <p>Vaativuus: Yleisen haun jälkeen jokaisen havainnon luonnin yhteydessä
     * tehdään tietokantahaku. Jos havaintoja on esim. tuhansia, voi tämä
     * viedä resurssia hiukan. Tämä ei oikeastaan olisi tarpeellista, koska
     * havainto-olion tiedot on jo kertaalleen haettu. Pitäisikö havainnon
     * luomiseen tehdä vaihtoehto "älä koske tietokantaan?". Ainakin jos alkaa
     * tökkimään. 
     * 
     * HUOM! Muista, että luodessa epämääritelty id aiheuttaa sen,
     * ettei tietokantahakua tehdä. Sen jälkeen voi noukkia tiedot jo haetusta
     * tk_oliosta.</p>
     * 
     * @param \Tietokantaolio $tietokantaolio 
     * @param \Parametrit $parametriolio 
     */
    public static function hae_soveliaat($tietokantaolio, $parametriolio){
        // Muotoillaan yläluokan lause:
        $ylaluokka_id = $parametriolio->ylaluokka_id_lj;

        if(isset($ylaluokka_id) && is_numeric($ylaluokka_id) && $ylaluokka_id > 0){
            $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id";
        }
        else{
            $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id <> -1";
        }
        
        // Haetaan poppoon id:
        $poppoo_id = $parametriolio->poppoon_id;
        $poppooehto = Poppoo::$taulunimi.".".Poppoo::$SARAKENIMI_ID."=".$poppoo_id;

        // Tarkistetaan, haetaanko vuoden, määrän tai jonkin muun ehdon mukaan:
        $max_lkm = $parametriolio->max_lkm_hav;
//echo "Havaintojen_nayttomoodi: ".$parametriolio->get_havaintojen_nayttomoodi();
        if($parametriolio->get_havaintojen_nayttomoodi() ==
                    Havaintojen_nayttomoodi::$nayta_uusimmat){
            $ehtolause = "WHERE (".Kuvaus::$taulunimi.".kieli= ".Kielet::$SUOMI."
                        AND $ylaluokkaehto
                        AND $poppooehto)
                        ORDER by vuosi DESC, kk DESC, paiva DESC, laji
                        LIMIT ".$max_lkm;
            $nayttoilmoitus =
                $max_lkm.Bongaustekstit::$max_nayttoilm_bongaussivu1;
        }
        else if($parametriolio->get_havaintojen_nayttomoodi() == 
                            Havaintojen_nayttomoodi::$nayta_vuoden_mukaan){

            $ehtolause = "WHERE (".Kuvaus::$taulunimi.".kieli= ".Kielet::$SUOMI."
                        AND $ylaluokkaehto
                        AND $poppooehto
                        AND vuosi = $parametriolio->nayttovuosi_hav)
                        ORDER by vuosi DESC, kk DESC, paiva DESC, laji";
            $nayttoilmoitus = $parametriolio->nayttovuosi_hav;
        }
        else{
            $ehtolause = "WHERE (".Kuvaus::$taulunimi.".kieli= ".Kielet::$SUOMI."
                        AND $ylaluokkaehto
                        AND $poppooehto)
                        ORDER by vuosi DESC, kk DESC, paiva DESC, laji
                        LIMIT 10";
            $nayttoilmoitus = "???";
        }

        $hakulause = "SELECT 
                        ".Havainto::$taulunimi.".id AS hav_id,
                        ".Kuvaus::$taulunimi.".nimi AS laji,
                        ".Havainto::$taulunimi.".vuosi AS vuosi,
                        ".Havainto::$taulunimi.".kk AS kk,
                        ".Havainto::$taulunimi.".paiva AS paiva
                FROM ".Lajiluokka::$taulunimi."
                JOIN ".Kuvaus::$taulunimi."
                ON ".Kuvaus::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                JOIN ".Havainto::$taulunimi."
                ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                JOIN ".Henkilo::$taulunimi."
                ON ".Henkilo::$taulunimi.".".Henkilo::$SARAKENIMI_ID."=".
                    Havainto::$taulunimi.".".Havainto::$SARAKENIMI_HENKILO_ID."
                JOIN ".Poppoo::$taulunimi."
                ON ".Poppoo::$taulunimi.".".Poppoo::$SARAKENIMI_ID."=".
                    Henkilo::$taulunimi.".".Henkilo::$sarakenimi_poppoo_id."
                $ehtolause
               ";

        $havaintotaulu = 
                $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);

        $oliotaulu = array();   // Tämä sisältää Havainto-oliot.
        
        if(!empty($havaintotaulu)){
            foreach ($havaintotaulu as $tk_hav) {
                
                $uusi = new Havainto($tk_hav->hav_id, $tietokantaolio);
                if($uusi->olio_loytyi_tietokannasta){
                    array_push($oliotaulu, $uusi);
                }
            }
        }
        return $oliotaulu;
    }
    
    /**
    * Hakee käyttäjien havaintomäärät kyseisestä luokasta.
    * @param <type> $ylaluokka_id luku, joka määrää sen, mistä yläluokasta
    * havaintoja haetaan. Jos parametri on alle 1 tai epäluku, etsitään kaikista
    * luokista.
    * @param Tietokantaolio $tietokantaolio
    * @param <type> $nyk_puolivuotiskauden_nro: 1->kevät 2010, 2-> syksy2010, 3->kevät2011 jne.
     * @param type $poppoo_id: vain nykyisestä poppoosta!
     * @return string
     */
   static function hae_havaintomaarat($ylaluokka_id, 
                                    $tietokantaolio,
                                    $nyk_puolivuotiskauden_nro,
                                    $poppoo_id){

       $palaute = "";
       $ylaluokkaehto = "";

       // Muotoillaan yläluokan lause:
       if(isset($ylaluokka_id) && is_numeric($ylaluokka_id) && $ylaluokka_id > 0){
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id";
       }
       else{
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id <> -1";
       }
       //=======================================================================
       // Haetaan ensin kaikki havainnot:
       $hakulause = "SELECT DISTINCT
                               henkilot.etunimi AS nimi,
                               henkilot.id AS henk_id
                   FROM ".Lajiluokka::$taulunimi."
                   JOIN ".Havainto::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".
                        Lajiluokka::$taulunimi.".id
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   WHERE $ylaluokkaehto
                   AND ".Henkilo::$sarakenimi_poppoo_id."=".$poppoo_id;

       $havaintotaulu = $tietokantaolio->
                            tee_omahaku_oliotaulukkopalautteella($hakulause);

       if(!empty ($havaintotaulu)){
           // Muotoillaan jakso-otsikko:
           $palaute .= "<div id='bongaukset_kaikki'><b>Kaikki</b><br />";
           $havaintotulostaulu = array();
           foreach ($havaintotaulu as $henkilo) {
               $lkm = Havainto::laske_henkilon_bongausten_lkm($tietokantaolio,
                                                   $henkilo->henk_id,
                                                   $ylaluokka_id,
                                                   "Ehi vuan ihan kaikki!");

               $tulosmj = "<span class='huomio2' title='".
                               Bongauspainikkeet::$HAVAINNOT_NAYTA_HENKILON_LAJIT_KAIKKI_TITLE.
                               "' onclick='hae_henkilon_bongauslajit(".
                                   $henkilo->henk_id.",\"kaikki_jaksot\",".
                                       "\"nayta_kaikki\")'>".
                                       $henkilo->nimi.": ".$lkm[0]."</span>".

                               "<span class='huomio2' title='".
                               Bongauspainikkeet::$HAVAINNOT_NAYTA_HENKILON_LAJIT_SUOMI_TITLE.
                               "' onclick='hae_henkilon_bongauslajit(".
                                   $henkilo->henk_id.",\"kaikki_jaksot\",\"".
                                       Bongausasetuksia::$nayta_vain_suomessa_havaitut."\")'>".
                                       " (FIN: ".$lkm[1].")".
                               "</span><br />";

               array_push($havaintotulostaulu, array($lkm[0], $tulosmj));
           }

           usort($havaintotulostaulu, "Havainto::vertaa_havaintomaarat");
           foreach ($havaintotulostaulu as $tulos) {
               $palaute .= $tulos[1];
           }

           // Kaikkien loppu:
           $palaute .= "</div>";
       }
       //=======================================================================
       // Haetaan sitten puolivuotiskausittain:
       if(isset($nyk_puolivuotiskauden_nro) &&
               is_numeric($nyk_puolivuotiskauden_nro) &&
               $nyk_puolivuotiskauden_nro > 0){

           /* Käydään läpi kaikki puolivuotiskaudet, joilla havaintoja tehty. */
           for($i=0; $i<$nyk_puolivuotiskauden_nro; $i++){

               $puolivuotiskauden_nro = $nyk_puolivuotiskauden_nro-$i;

               // Muotoillaan puolivuotiskauden valinta:
               if(is_numeric($puolivuotiskauden_nro)){

                   $vuosi = floor(2010+($puolivuotiskauden_nro-1)/2);
                   $kk_alaraja = 0;
                   $kk_ylaraja = 7;
                   if($puolivuotiskauden_nro % 2 == 0){
                       $kk_alaraja = 6;
                       $kk_ylaraja = 13;
                   }

                   $jaksoaikaehto = "".Havainto::$taulunimi.".vuosi = $vuosi
                                   AND ".Havainto::$taulunimi.".kk > $kk_alaraja
                                   AND ".Havainto::$taulunimi.".kk < $kk_ylaraja";
               }
               else{
                   $jaksoaikaehto = "";    /* Haetaan kaikki! */
               }

               $hakulause = "SELECT DISTINCT
                                   henkilot.etunimi AS nimi,
                                   henkilot.id AS henk_id
                           FROM ".Lajiluokka::$taulunimi."
                           JOIN ".Havainto::$taulunimi."
                           ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                           JOIN henkilot
                           ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                           WHERE $ylaluokkaehto
                           AND $jaksoaikaehto
                           AND ".Henkilo::$sarakenimi_poppoo_id."=".$poppoo_id;



               $havaintotaulu = $tietokantaolio->
                            tee_omahaku_oliotaulukkopalautteella($hakulause);

               if(!empty ($havaintotaulu)){
                   // Muotoillaan jakso-otsikko:
                   $palaute .= "<div class='bongaukset_jakso'><b>";
                   if($puolivuotiskauden_nro % 2 == 0){
                       $palaute .= "Syksy ";
                   }
                   else{
                       $palaute .= "Kev&auml;t ";
                   }
                   $palaute .= floor(2010+($puolivuotiskauden_nro-1)/2)."</b><br />";

                   // Haluan nimet havaintomääräjärjestyksessä, joten teen
                   // uuden taulukon, jossa järjestys oikea. Hmm.
                   // Uuteen taulukkoon tulee jokaisesta henkilöstä tämän
                   // tulosviesti ja lkm pikkutaulukossa.
                   $havaintotulostaulu = array();
                   foreach ($havaintotaulu as $henkilo) {
                       $lkm = Havainto::laske_henkilon_bongausten_lkm($tietokantaolio,
                                                           $henkilo->henk_id,
                                                           $ylaluokka_id,
                                                           $puolivuotiskauden_nro);

                       // Tulostetaan lajimäärät ja lisätään mahdollisuus
                       // klikkaamalla nähdä havaitut lajit.
                       $tulosmj = "<span class='huomio2' title='".
                               Bongauspainikkeet::$HAVAINNOT_NAYTA_HENKILON_LAJIT_KAIKKI_TITLE.
                               "' onclick='hae_henkilon_bongauslajit(".
                                   $henkilo->henk_id.",".$puolivuotiskauden_nro.",".
                                       "\"nayta_kaikki\")'>".
                                       $henkilo->nimi.": ".$lkm[0]."</span>".

                               "<span class='huomio2' title='".
                               Bongauspainikkeet::$HAVAINNOT_NAYTA_HENKILON_LAJIT_SUOMI_TITLE.
                               "' onclick='hae_henkilon_bongauslajit(".
                                   $henkilo->henk_id.",".$puolivuotiskauden_nro.",\"".
                                       Bongausasetuksia::$nayta_vain_suomessa_havaitut."\")'>".
                                       " (FIN: ".$lkm[1].")".
                               "</span><br />";

                       array_push($havaintotulostaulu, array($lkm[0], $tulosmj));
                   }

                   usort($havaintotulostaulu, "Havainto::vertaa_havaintomaarat");
                   foreach ($havaintotulostaulu as $tulos) {
                       $palaute .= $tulos[1];
                   }
                   
                   $palaute .= "</div>";
               }
           }
       }
       

       return $palaute;
   }
   
   /* Tämä liittyy hae_havaintomäärät-metodissa kutsuttavaan usort-metodiin.
    * Parametrit ovat taulukoita, joiden ensimmäinen alkio on luku. */
   static function vertaa_havaintomaarat($a, $b)
   {
       if ($a[0] == $b[0]) {
           return 0;
       }
       else if($a[0] > $b[0]){
           return -1;
       }
       else{
           return 1;
       }
   }
   
   /**
    * Palauttaa luvun, joka ilmoittaa, kuinka monta annetun yläluokan lajia
    * annettu henkilö on havainnut (melkoisen varmasti) annetun vuoden aikana.
    * 
    * Jos vuosi-parametri ei ole numeric, haetaan kuluvan vuoden havainnot.
    * 
    * @param <type> $tietokantaolio
    * @param <type> $henkilo_id
    * @param <type> $lajiluokka_id
    * @param <type> $vuosi
    *
    * Palauttaa taulukon, jossa kaksi lkm-alkiota: 1. kaikki ja 2. Suomessa havaitut
    */
   static function laske_henkilon_vuodarit($tietokantaolio,
                                           $henkilo_id,
                                           $ylaluokka_id,
                                           $vuosi){
       $lkm = array();

       // Muotoillaan yläluokan lause:
       if(isset($ylaluokka_id) && is_numeric($ylaluokka_id) && $ylaluokka_id > 0){
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id";
       }
       else{
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id <> -1";
       }

       // Muotoillaan vuoden valinta:
       if(is_numeric($vuosi)){
           $aikaehto = "AND ".Havainto::$taulunimi.".vuosi = ".$vuosi;
       }
       else{
           $aikaehto = " AND ".Havainto::$taulunimi.".vuosi = ".
                                                Aika::anna_nyk_vuoden_nro();
       }

       // Muotoillaan varmuusehto:
       $varmuusehto = "AND ".Havainto::$taulunimi.".varmuus >= ".Varmuus::$melkoisen_varma;

       $hakulause = 
                   "SELECT ".Havainto::$taulunimi.".lajiluokka_id AS laji_id
                   FROM ".Havainto::$taulunimi."
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   JOIN ".Lajiluokka::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   WHERE henkilot.id = $henkilo_id
                   AND $ylaluokkaehto
                   $aikaehto
                   $varmuusehto
                   GROUP BY laji_id;
                  ";

       $hakutulos = 
                $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);


       // Palauttaa luvun 0 myös jos parametri paha.
       $lkm[0] = sizeof($hakutulos);

       // Haetaan sitten vain Suomessa nähtyjen lkm:
       $suomi = Maat::$suomi;
       $hakulause =
                   "SELECT ".Havainto::$taulunimi.".lajiluokka_id AS laji_id
                   FROM ".Havainto::$taulunimi."
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   JOIN ".Lajiluokka::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   WHERE henkilot.id = $henkilo_id
                   AND $ylaluokkaehto
                   $aikaehto
                   $varmuusehto
                   AND ".Havainto::$taulunimi.".maa = $suomi
                   GROUP BY laji_id;
                  ";

       $hakutulos = $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);


       // Palauttaa luvun 0 myös jos parametri paha.
       $lkm[1] = sizeof($hakutulos);
       
       //======================================================================
       //======================= Ekovuodarien haku =============================
       // Täällä yhdistetään vielä tauluun "havainnon_lisaluokitukset":
       
       $hakulause = 
                   "SELECT ".Havainto::$taulunimi.".lajiluokka_id AS laji_id
                   FROM ".Havainto::$taulunimi."
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   JOIN ".Lajiluokka::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   JOIN ".Lisaluokitus::$taulunimi."
                   ON ".Havainto::$taulunimi.".id=".
                        Lisaluokitus::$SARAKENIMI_HAVAINTO_ID." 
                   WHERE henkilot.id = $henkilo_id
                   AND $ylaluokkaehto
                   $aikaehto
                   $varmuusehto
                   AND ".Lisaluokitus::$taulunimi.".".
                                Lisaluokitus::$SARAKENIMI_LISALUOKITUS."=".  
                        Lisaluokitus_asetukset::$ekopinna."
                   GROUP BY laji_id;
                  ";
       $hakutulos = 
                $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);


       // Palauttaa luvun 0 myös jos parametri paha.
       $lkm[2] = sizeof($hakutulos);
       
       
       
       //======================================================================

       return $lkm;
   }
   
   /**
    * Palauttaa luvun, joka ilmoittaa, kuinka monta annetun yläluokan lajia
    * annettu henkilö on havainnut.
    * @param <type> $tietokantaolio
    * @param <type> $henkilo_id
    * @param <type> $lajiluokka_id
    * @param <type> $puolivuotiskauden_nro: 1->kevät 2010, 2-> syksy2010, 3->kevät2011 jne.
    * Jos tämä on ei-numeerinen, haetaan kaikki.
    *
    * Palauttaa taulukon, jossa kaksi lkm-alkiota: 1. kaikki ja 2. Suomessa havaitut
    *
    * KORJAA MENEMÄÄN HAVAINTOJAKSOJEN KAUTTA KUN KERKIÄT
    */
   static function laske_henkilon_bongausten_lkm($tietokantaolio,
                                           $henkilo_id,
                                           $ylaluokka_id,
                                           $puolivuotiskauden_nro){
       $lkm = array();

       // Muotoillaan yläluokan lause:
       if(isset($ylaluokka_id) && is_numeric($ylaluokka_id) && $ylaluokka_id > 0){
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id";
       }
       else{
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id <> -1";
       }

       // Muotoillaan puolivuotiskauden valinta:
       if(is_numeric($puolivuotiskauden_nro)){

           $vuosi = floor(2010+($puolivuotiskauden_nro-1)/2);
           $kk_alaraja = 0;
           $kk_ylaraja = 7;
           if($puolivuotiskauden_nro % 2 == 0){
               $kk_alaraja = 6;
               $kk_ylaraja = 13;
           }

           $jaksoaikaehto = "AND ".Havainto::$taulunimi.".vuosi = $vuosi
                           AND ".Havainto::$taulunimi.".kk > $kk_alaraja
                           AND ".Havainto::$taulunimi.".kk < $kk_ylaraja";
       }
       else{
           $jaksoaikaehto = "";    /* Haetaan kaikki! */
       }

       // Muotoillaan varmuusehto:
       $varmuusehto = "AND ".Havainto::$taulunimi.".varmuus >= ".Varmuus::$melkoisen_varma;


       $hakulause = 
                   "SELECT ".Havainto::$taulunimi.".lajiluokka_id AS laji_id
                   FROM ".Havainto::$taulunimi."
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   JOIN ".Lajiluokka::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   WHERE henkilot.id = $henkilo_id
                   AND $ylaluokkaehto
                   $jaksoaikaehto
                   $varmuusehto
                   GROUP BY laji_id;
                  ";

       $hakutulos = 
                $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);


       // Palauttaa luvun 0 myös jos parametri paha.
       $lkm[0] = sizeof($hakutulos);

       // Haetaan sitten vain Suomessa nähtyjen lkm:
       $suomi = Maat::$suomi;
       $hakulause =
                   "SELECT ".Havainto::$taulunimi.".lajiluokka_id AS laji_id
                   FROM ".Havainto::$taulunimi."
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   JOIN ".Lajiluokka::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   WHERE henkilot.id = $henkilo_id
                   AND $ylaluokkaehto
                   $jaksoaikaehto
                   $varmuusehto
                   AND ".Havainto::$taulunimi.".maa = $suomi
                   GROUP BY laji_id;
                  ";

       $hakutulos = $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);


       // Palauttaa luvun 0 myös jos parametri paha.
       $lkm[1] = sizeof($hakutulos);

       return $lkm;
   }

   /**
    * Palauttaa taulukon, joka sisältää nimet niistä sessiomuuttujassa 
    * säilytettävän (siksi ei tarvitse täällä välittää) yläluokan lajeista,
    * jotka annettu henkilö on havainnut kyseisenä puolivuotiskautena. Taulukko
    * ei sisällä muuta tietoa havainnoista, eli kyseessä on pelkkä lajinimien
    * luettelo.
    * @param Parametrit $parametriolio
    * @param <type> $puolivuotiskauden_nro: 1->kevät 2010, 2-> syksy2010, 3->kevät2011 jne.
    * Jos tämä on ei-numeerinen, haetaan kaikki.
    *
    * Palauttaa taulukon, joka voi olla tyhjä.
    *
    */
   static function hae_henkilon_bongatut_lajit($parametriolio,$puolivuotiskauden_nro){

       $tietokantaolio = $parametriolio->get_tietokantaolio();
       $henkilo_id = $parametriolio->henkilo_id;
       $ylaluokka_id = $parametriolio->ylaluokka_id_lj;
       $aluerajoitus = $parametriolio->havaintoalue_hav; // "suomi" tai jotakin muuta.

       $bongaaja = new Henkilo($henkilo_id, $tietokantaolio);
       $henkilon_nimi = $bongaaja->get_arvo(Henkilo::$sarakenimi_etunimi);

       // muotoillaan kausi:
       if(!is_numeric($puolivuotiskauden_nro) || $puolivuotiskauden_nro < -200){
           $kausi = Bongaustekstit::$havainnot_elikset;
       }
       else {
           if($puolivuotiskauden_nro % 2 == 0){
               $kausi = Bongaustekstit::$havainnot_syksy." ";
           }
           else{
               $kausi = Bongaustekstit::$havainnot_kevat." ";
           }
           $kausi .= floor(2010+($puolivuotiskauden_nro-1)/2);
       }

       // Ilmoitetaan, onko havainnot Suomesta vai kaikkialta:
       $havainnot = Bongaustekstit::$havainnot_kaikkialla;
       if($parametriolio->havaintoalue_hav == 
               Bongausasetuksia::$nayta_vain_suomessa_havaitut){
           $havainnot = Bongaustekstit::$havainnot_suomessa;
       }

       // Painikkeita:
       $sulkemisnappi = 
           "<button type='button' onclick='sulje_ruutu(\"".
               Bongausasetuksia::$havaintotietotaulu_leftin_id."\")'>".
           Bongauspainikkeet::$HAVAINNOT_SULJE_HENKILON_HAVAINNOT_VALUE.
           "</button>";

       // Muotoillaan yläluokan lause:
       if(isset($ylaluokka_id) && is_numeric($ylaluokka_id) && $ylaluokka_id > 0){
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id";
       }
       else{
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id <> -1";
       }

       // Muotoillaan puolivuotiskauden valinta. Vanhin kausi on siis noin sata vuotta
       // sitten vuosi 1910. Vanhemmat tulkitaan niin, että kaikki kaudet haetaan.
       if(is_numeric($puolivuotiskauden_nro) && ($puolivuotiskauden_nro > -200)){

           $vuosi = floor(2010+($puolivuotiskauden_nro-1)/2);
           $kk_alaraja = 0;
           $kk_ylaraja = 7;
           if($puolivuotiskauden_nro % 2 == 0){
               $kk_alaraja = 6;
               $kk_ylaraja = 13;
           }

           $jaksoaikaehto = "AND ".Havainto::$taulunimi.".vuosi = $vuosi
                           AND ".Havainto::$taulunimi.".kk > $kk_alaraja
                           AND ".Havainto::$taulunimi.".kk < $kk_ylaraja";
       }
       else{
           $jaksoaikaehto = "";    /* Haetaan kaikki! */
       }

       // Muotoillaan varmuusehto:
       $varmuusehto = "AND ".Havainto::$taulunimi.".varmuus >= ".Varmuus::$melkoisen_varma;

       // Tutkitaan, haetaan vain Suomesta vai kaikkialta:
       if($aluerajoitus == Bongausasetuksia::$nayta_vain_suomessa_havaitut){
           $alue_ehto = "AND ".Havainto::$taulunimi.".maa = ".Maat::$suomi;
       }
       else{
           $alue_ehto = "";
       }

       $hakulause =
                   "SELECT ".Havainto::$taulunimi.".lajiluokka_id AS laji_id,
                           ".Kuvaus::$taulunimi.".nimi AS nimi
                   FROM ".Havainto::$taulunimi."
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   JOIN ".Lajiluokka::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   JOIN ".Kuvaus::$taulunimi."
                   ON (".Havainto::$taulunimi.".lajiluokka_id = ".Kuvaus::$taulunimi.".lajiluokka_id
                   AND ".Kuvaus::$taulunimi.".kieli=".Kielet::$SUOMI.")
                   WHERE henkilot.id = $henkilo_id
                   AND $ylaluokkaehto
                   $jaksoaikaehto
                   $varmuusehto
                   $alue_ehto
                   GROUP BY laji_id
                   ORDER BY nimi
                  ";

       $havaintotaulu = 
                   $tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);


       if(empty($havaintotaulu)){
           $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                   $sulkemisnappi."</div>";
           $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
                   <tr>
                   <th>".Bongaustekstit::$ilm_ei_havaintoja."</th></tr></table>";
       }
       else{ // Muotoillaan tiedot nätisti:
           $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                   $havainnot.$sulkemisnappi."<br />".
                   "(".$henkilon_nimi.", ".$kausi.")</div>";

           $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
               <tr>
                   <th>Nro</th>
                   <th>Laji</th>
               </tr>";


           $laskuri = 1; // Auttaa joka toisen rivin eri väriseksi.

           foreach ($havaintotaulu as $havainto) {
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
               $tulos .= "<td>".$havainto->nimi."</td>";
               $tulos .= "</tr>";

               $laskuri++;
           }
           $tulos .= "</table>";
       }
       return $tulos;
   }
   
   /**
    * Palauttaa taulukon, joka sisältää nimet niistä sessiomuuttujassa 
    * säilytettävän (siksi ei tarvitse täällä välittää) yläluokan lajeista,
    * jotka annettu henkilö on havainnut kyseisenä vuotena (tai ikinä) ja jotka 
    * täyttävät parametrina mahdollisesti annetun lisäluokitusehdon.
    * 
    * Taulukko
    * ei sisällä muuta tietoa havainnoista, eli kyseessä on pelkkä lajinimien
    * luettelo.
    * @param Parametrit $parametriolio
    * @param <type> $vuosiluku Jos tämä on ei-numeerinen tai alle 1900, haetaan kaikki.
    * @param int $lisaluokitus Lisäluokitusehto, eli vain tämän ehdon täyttävät
    * lajit otetaan mukaan. Jos tämän arvo on false, haetaan kaikki.
    * 
    * Palauttaa taulukon, joka voi olla tyhjä.
    *
    */
   static function hae_henkilon_pinnalajit($parametriolio, $vuosi, $lisaluokitus){

       $tietokantaolio = $parametriolio->get_tietokantaolio();
       $henkilo_id = $parametriolio->henkilo_id;
       $ylaluokka_id = $parametriolio->ylaluokka_id_lj;
       $aluerajoitus = $parametriolio->havaintoalue_hav; // "suomi" tai jotakin muuta.

       $bongaaja = new Henkilo($henkilo_id, $tietokantaolio);
       $henkilon_nimi = $bongaaja->get_arvo(Henkilo::$sarakenimi_etunimi);

       // muotoillaan kausi:
       if(!is_numeric($vuosi) || $vuosi < 1900){
           $kausi = Bongaustekstit::$havainnot_elikset;
       }
       else {
           $kausi = Bongaustekstit::$havainnot_vuonna." ";
           $kausi .= $vuosi;
       }

       // Ilmoitetaan, onko havainnot Suomesta vai kaikkialta:
       $havainnot = Bongaustekstit::$havainnot_kaikkialla;
       if($parametriolio->havaintoalue_hav == 
               Bongausasetuksia::$nayta_vain_suomessa_havaitut){
           $havainnot = Bongaustekstit::$havainnot_suomessa;
       }
       
       if($lisaluokitus){
           $lisaluokitusasetukset = new Lisaluokitus_asetukset();
           $havainnot .= " (".$lisaluokitusasetukset->hae_nimi($lisaluokitus).")";
       }

       // Painikkeita:
       $sulkemisnappi = 
           "<button type='button' onclick='sulje_ruutu(\"".
               Bongausasetuksia::$havaintotietotaulu_leftin_id."\")'>".
           Bongauspainikkeet::$HAVAINNOT_SULJE_HENKILON_HAVAINNOT_VALUE.
           "</button>";

       // Muotoillaan yläluokan lause:
       if(isset($ylaluokka_id) && is_numeric($ylaluokka_id) && $ylaluokka_id > 0){
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id = $ylaluokka_id";
       }
       else{
           $ylaluokkaehto = Lajiluokka::$taulunimi.".ylaluokka_id <> -1";
       }

       // Muotoillaan vuoden valinta.
       if(!is_numeric($vuosi) || $vuosi > 1900){

           $jaksoaikaehto = "AND ".Havainto::$taulunimi.".vuosi = ".$vuosi;
       }
       else{
           $jaksoaikaehto = "";    /* Haetaan kaikki! */
       }

       // Muotoillaan varmuusehto:
       $varmuusehto = "AND ".Havainto::$taulunimi.".varmuus >= ".Varmuus::$melkoisen_varma;

       // Tutkitaan, haetaan vain Suomesta vai kaikkialta:
       if($aluerajoitus == Bongausasetuksia::$nayta_vain_suomessa_havaitut){
           $alue_ehto = "AND ".Havainto::$taulunimi.".maa = ".Maat::$suomi;
       }
       else{
           $alue_ehto = "";
       }
       
       // Lisäluokitusehto vaatii ylimääräisen liitoksen tekemisen:
       $lisaluokitusehto = " AND ".Lisaluokitus::$taulunimi.".".
                        Lisaluokitus::$SARAKENIMI_LISALUOKITUS."=".
                        $lisaluokitus;
       
       if($lisaluokitus){
           $hakulause =
                   "SELECT ".Havainto::$taulunimi.".lajiluokka_id AS laji_id,
                           ".Kuvaus::$taulunimi.".nimi AS nimi
                   FROM ".Havainto::$taulunimi."
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   JOIN ".Lajiluokka::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   JOIN ".Kuvaus::$taulunimi."
                   ON (".Havainto::$taulunimi.".lajiluokka_id = ".Kuvaus::$taulunimi.".lajiluokka_id
                   AND ".Kuvaus::$taulunimi.".kieli=".Kielet::$SUOMI.")
                       
                   JOIN ".Lisaluokitus::$taulunimi."
                   ON ".Lisaluokitus::$taulunimi.".".
                            Lisaluokitus::$SARAKENIMI_HAVAINTO_ID."=".
                            Havainto::$taulunimi.".id
                            
                   WHERE henkilot.id = $henkilo_id
                   AND $ylaluokkaehto
                   $jaksoaikaehto
                   $varmuusehto
                   $alue_ehto
                   $lisaluokitusehto
                   GROUP BY laji_id
                   ORDER BY nimi
                  ";
           
       } else{  // Ei lisäluokitusehtoa: yksi liitos vähemmän.
           $hakulause =
                   "SELECT ".Havainto::$taulunimi.".lajiluokka_id AS laji_id,
                           ".Kuvaus::$taulunimi.".nimi AS nimi
                   FROM ".Havainto::$taulunimi."
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   JOIN ".Lajiluokka::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   JOIN ".Kuvaus::$taulunimi."
                   ON (".Havainto::$taulunimi.".lajiluokka_id = ".Kuvaus::$taulunimi.".lajiluokka_id
                   AND ".Kuvaus::$taulunimi.".kieli=".Kielet::$SUOMI.")
                   WHERE henkilot.id = $henkilo_id
                   AND $ylaluokkaehto
                   $jaksoaikaehto
                   $varmuusehto
                   $alue_ehto
                   GROUP BY laji_id
                   ORDER BY nimi
                  ";
       }
       
       $havaintotaulu = 
                   $tietokantaolio->tee_OMAhaku_oliotaulukkopalautteella($hakulause);


       if(empty($havaintotaulu)){
           $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                   $sulkemisnappi."</div>";
           $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
                   <tr>
                   <th>".Bongaustekstit::$ilm_ei_havaintoja."</th></tr></table>";
       }
       else{ // Muotoillaan tiedot nätisti:
           $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                   $havainnot.$sulkemisnappi."<br />".
                   "(".$henkilon_nimi.", ".$kausi.")</div>";

           $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
               <tr>
                   <th>Nro</th>
                   <th>Laji</th>
               </tr>";


           $laskuri = 1; // Auttaa joka toisen rivin eri väriseksi.

           foreach ($havaintotaulu as $havainto) {
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
               $tulos .= "<td>".$havainto->nimi."</td>";
               $tulos .= "</tr>";

               $laskuri++;
           }
           $tulos .= "</table>";
       }
       return $tulos;
   }
   
   //==========================================================================
   /**
    * Palauttaa yhden henkilön havainnot taulukkoon muotoiltuna.
    * 
    * Huom! Sotii periaatetta vastaan..
    * 
    * @param Parametrit $parametriolio
    * @return <type> /
    */
   static function hae_henkilon_havainnot($parametriolio){

       $tietokantaolio = $parametriolio->get_tietokantaolio();

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
                           ".Havainto::$taulunimi.".paiva AS paiva
                   FROM ".Lajiluokka::$taulunimi."
                   JOIN ".Kuvaus::$taulunimi."
                   ON ".Kuvaus::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   JOIN ".Havainto::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id = henkilot.id
                   WHERE (".Kuvaus::$taulunimi.".kieli= ".$parametriolio->kieli_kuv.
                   " ".$ylaluokkaehto."
                   AND henkilot.id = ".$henkilo_id.")
                   ORDER by vuosi DESC, kk DESC, paiva DESC, laji;
                  ";

       /*
                   WHERE (".Kuvaus::$taulunimi.".kieli= ".$parametriolio->kieli_kuv."
                   AND henkilot.id = ".$henkilo_id.")*/
       $havaintotaulu = 
               $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);

       if(empty($havaintotaulu)){
           $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                   $sulkemisnappi."</div>";
           $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
                   <tr>
                   <th>".Bongaustekstit::$ilm_ei_havaintoja."</th></tr></table>";
       }
       else{ // Muotoillaan tiedot nätisti:
           $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                   "Havainnot (".$havaintotaulu[0]->nimi.")".$sulkemisnappi."</div>";

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
                   <th>Paikka</th>
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

               // Maa :
               $maa = " (".Maat::hae_maan_nimi($havainto->maa).")";

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
               $tulos .= "<td>".$havainto->paikka.$maa."</td>";
               $tulos .= $toimintopainikkeet;
               $tulos .= "</tr>";

               $laskuri++;
           }

           $tulos .= "</table>";
       }

       return $tulos;
   }

   /**
    * Palauttaa yhden poppoon yhdestä lajista tekemät havainnot taulukkoon 
    * muotoiltuna.
    * @param Parametrit $parametriolio
    * @return <type> 
    */
   static function hae_lajiluokan_havainnot($parametriolio){

       /**
        * @var Tietokantaolio
        */
       $tietokantaolio = $parametriolio->get_tietokantaolio();

       $poppoo_id = $parametriolio->poppoon_id;
       
       $henkilo_id = $parametriolio->henkilo_id;

       // Painikkeita:
       $muokkausnappi = "";    // Määritellään myöhemmin
       $poistonappi = "";      // Määritellään myöhemmin
       $sulkemisnappi = "<button type='button' onclick='sulje_ruutu(\"".
                       Bongausasetuksia::$havaintotietotaulun_id."\")'>".
                       Bongauspainikkeet::$HAVAINNOT_SULJE_HENKILON_HAVAINNOT_VALUE.
                       "</button>";


       $tulos = "";

       // Tässäpä lausetta kerrakseen.
       $hakulause = "SELECT 
                           ".Havainto::$taulunimi.".id AS hav_id,
                           henkilot.etunimi AS nimi,
                           henkilot.id AS henk_id,
                           henkilot.valtuudet AS henk_valtuudet,
                           ".Kuvaus::$taulunimi.".nimi AS laji,
                           ".Havainto::$taulunimi.".paikka AS paikka,
                           ".Havainto::$taulunimi.".kommentti AS kommentti,
                           ".Havainto::$taulunimi.".vuosi AS vuosi,
                           ".Havainto::$taulunimi.".kk AS kk,
                           ".Havainto::$taulunimi.".paiva AS paiva,
                           ".Havainto::$taulunimi.".maa AS maa,
                           ".Havainto::$taulunimi.".varmuus AS varmuus
                   FROM ".Lajiluokka::$taulunimi."
                   JOIN ".Kuvaus::$taulunimi."
                   ON ".Kuvaus::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   JOIN ".Havainto::$taulunimi."
                   ON ".Havainto::$taulunimi.".lajiluokka_id = ".Lajiluokka::$taulunimi.".id
                   JOIN henkilot
                   ON ".Havainto::$taulunimi.".henkilo_id=henkilot.id
                   WHERE ".Kuvaus::$taulunimi.".kieli=".$parametriolio->kieli_kuv.
                   " AND ".Lajiluokka::$taulunimi.".id=".$parametriolio->id_lj.
                   " AND ".Henkilo::$sarakenimi_poppoo_id."=".$poppoo_id.
                   " ORDER by vuosi DESC, kk DESC, paiva DESC, laji;
                  ";

       $havaintotaulu = 
                $tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
       
       if(empty($havaintotaulu)){
           $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                   $sulkemisnappi."</div>";
           $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
                   <tr>
                   <th>".Bongaustekstit::$ilm_ei_havaintoja."</th></tr></table>";
       }
       else{ // Muotoillaan tiedot nätisti:
           $tulos = "<div class=".Bongausasetuksia::$tietotauluotsikko_class.">".
                   "Havainnot (".$havaintotaulu[0]->laji.")".$sulkemisnappi."</div>";

           $omaid = $parametriolio->get_omaid();

           $tulos .= "<table class = ".Bongausasetuksia::$tietotaulun_class.">
               <tr>
                   <th>Nro</th>
                   <th>Havaitsija</th>
                   <th>Aika</th>
                   <th>Paikka</th>
                   <th>Tunnistus</th>
                   <th>Toiminnot</th>
               </tr>";


           $laskuri = 1; // Auttaa joka toisen rivin eri v&auml;riseksi.

           foreach ($havaintotaulu as $havainto) {

               // Määritellään omille havainnoille muokkaus- ja poistopainikkeet:
               // painike, josta saa näkyviin havaintolomakkeen:
               $muokkausnappi = ""; // Nollataan, ettei kummittele!
               $poistonappi = "";
               $toimintopainikkeet = "<td></td>";   // Omistajalle ja hallitsijalle ei-tyhjä.

               // Omiin havaintoihin ja hallitsijan oikeuksilla saa muokata ja
               // poistaa
               if(($havainto->henk_id == $omaid) || ($omaid == Valtuudet::$HALLINTA)){

                   $class = "rinnakkain";
                   $id = "";
                   $action = "index.php?id_hav=".$havainto->hav_id.
                               "&id_lj=".$parametriolio->id_lj;
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

               // Maa :
               $maa = " (".Maat::hae_maan_nimi($havainto->maa).")";

               // Vain epävarmuus näytetään
               $varmuus = "Ok";
               if($havainto->varmuus == Varmuus::$epavarma){
                   $varmuus = "?";
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
               $tulos .= "<td>".$havainto->nimi."</td>";
               $tulos .= "<td>".$aika."</td>";
               $tulos .= "<td>".$havainto->paikka.$maa."</td>";
               $tulos .= "<td>".$varmuus."</td>";
               $tulos .= $toimintopainikkeet;
               $tulos .= "</tr>";

               $laskuri++;
           }

           $tulos .= "</table>";
       }

       return $tulos;
   }

    /**
     * Hakee tähän havaintoon viittaavat havaintokuvalinkit ja palauttaa ne
     * taulukossa Havaintokuvalinkki-luokan olioina. 
     * Palauttaa aina taulukon, joka voi olla tyhjä.
     */
    public function hae_havaintokuvalinkit() {
        $linkit = array();
        
        $hakulause = "SELECT ".Havaintokuvalinkki::$SARAKENIMI_ID.
                    " FROM ".Havaintokuvalinkki::$taulunimi.
                    " WHERE ".Havaintokuvalinkki::$sarakenimi_havainto_id."=".
                            $this->get_id();
        
        $osumat = 
            $this->tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
        
        foreach ($osumat as $osumaolio) {
            $linkki_id = $osumaolio->id;
            array_push($linkit, 
                    new Havaintokuvalinkki($linkki_id, $this->tietokantaolio));
        }
        
        return $linkit;
    }
    
    /**
     * Lisäluokitus tallennetaan vain kerran. Ennen uuden tallennusta 
     * tarkistetaan, ettei kyseistä lisäluokitusta jo ole olemassa.
     * 
     * Palauttaa onnistumisen mukaan joko Lisaluokitus::operaatio_onnistui tai
     * Lisaluokitus::virhe. Virhetilanteessa virheilmoitukset lisätään
     * havainto-olion ilmoituksiin.
     * 
     * @param type $luokitus_id Huomaa että tämä on asetuksen arvo, ei olion id!
     */
    public function tallenna_uusi_lisaluokitus($luokitus_id){
        if(!$this->lisaluokitus_on_jo($luokitus_id)){
            $uusi = new Lisaluokitus(Lisaluokitus::$MUUTTUJAA_EI_MAARITELTY, 
                                    $this->tietokantaolio);
            
            // Asetetaan arvot ja tallennetaan:
            $uusi->set_arvo($this->get_id(), Lisaluokitus::$SARAKENIMI_HAVAINTO_ID);
            $uusi->set_arvo($luokitus_id, Lisaluokitus::$SARAKENIMI_LISALUOKITUS);
            
            //$uusi->set_arvo(1, Lisaluokitus::$SARAKENIMI_HAVAINTO_ID);
            //$uusi->set_arvo(4, Lisaluokitus::$SARAKENIMI_LISALUOKITUS);
            
            $palaute = $uusi->tallenna_uusi();
            
            if($palaute != Lisaluokitus::$OPERAATIO_ONNISTUI){
                $this->lisaa_virheilmoitus($uusi->tulosta_virheilmoitukset());
            }
            
            return $palaute;
        } else{
            return Lisaluokitus::$OPERAATIO_ONNISTUI;
        }
    }
    /**
     * Poistaa havainnolta kaikki kyseisen luokituksen ilmentymät, joita
     * toki pitäisi olla vain yksi.
     * 
     * Palauttaa poistettujen lisaluokitus-olioiden lukumäärän.
     * 
     * Virhetilanteissa virheilmoitukset kopioidaan havainnon ilmoituksiin.
     * 
     * @param type $luokitus_id Lisäluokitusasetuksen arvo
     */
    public function poista_lisaluokitus($luokitus_id){
        $lisaluokitukset = $this->hae_lisaluokitukset();
        
        $laskuri = 0;
        
        foreach ($lisaluokitukset as $poistoehdokas) {
            if($poistoehdokas instanceof Lisaluokitus){
                
                // Huomaa, että alla on jälleen tapaus, jossa kolme === ei toimi.
                if($poistoehdokas->
                        get_arvo(Lisaluokitus::$SARAKENIMI_LISALUOKITUS)==
                        $luokitus_id){
                    $poistopalaute = $poistoehdokas->poista();
                    
                    // Virheilmoituksen kopiointi havainnolle:
                    if($poistopalaute != Lisaluokitus::$OPERAATIO_ONNISTUI){
                        $this->lisaa_virheilmoitus(
                                    $poistoehdokas->tulosta_virheilmoitukset());
                    } else{
                        $laskuri++;
                    }
                }
            }
        }
        return $laskuri;
    }
    
    /**
     * Tarkistaa tietokannasta, onko siellä jo kyseinen lisäluokitus tälle havainnolle.
     * Palauttaa true, jos lisäluokitus löytyy ja false muuten.
     * @param type $luokitus_id
     */
    public function lisaluokitus_on_jo($luokitus_id){
        $hakulause = "SELECT ".Lisaluokitus::$SARAKENIMI_ID.
                    " FROM ".Lisaluokitus::$taulunimi.
                    " WHERE ".Lisaluokitus::$SARAKENIMI_HAVAINTO_ID."=".
                            $this->get_id().
                    " AND ".Lisaluokitus::$SARAKENIMI_LISALUOKITUS."=".
                            $luokitus_id;
        
        $osumat = 
            $this->tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
        
        if(empty($osumat)){
            return false;
        } else{
            return true;
        }
    }
    /**
     * Hakee tietokannasta kaikki tämän havainnon lisäluokitukset ja palauttaa
     * ne taulukossa Lisaluokitus-luokan olioina. 
     * Ellei mitään löydy, palauttaa tyhjän taulukon.
     */
    public function hae_lisaluokitukset(){
        $luokitukset = array();
        
        $hakulause = "SELECT ".Lisaluokitus::$SARAKENIMI_ID.
                    " FROM ".Lisaluokitus::$taulunimi.
                    " WHERE ".Lisaluokitus::$SARAKENIMI_HAVAINTO_ID."=".
                            $this->get_id();
        
        $osumat = 
            $this->tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
        
        foreach ($osumat as $osumaolio) {
            $luokitus_id = $osumaolio->id;
            array_push($luokitukset, 
                    new Lisaluokitus($luokitus_id, $this->tietokantaolio));
        }
        
        return $luokitukset;
    }
    
    /**
     * Tutkii, onko olemassa havaintoon viittaava lisäluokitus, joka lisäksi
     * on parametrina annettua lisäluokitustyyppiä. Palauttaa arvon TRUE, jos
     * lisäluokitus löytyy ja arvon FALSE muuten.
     * 
     * Toisena parametrina annetaan kaikki havaintoon liittyvät lisäluokitukset,
     * jottei niitä tarvitse montaa kertaa hakea (ks. metodi 
     * hae_lisaluokitukset()).
     * 
     * @param type $lisaluokitustyyppi
     */
    public function kuuluu_lisaluokkaan($lisaluokitustyyppi, $lisaluokitukset){
        
        $palaute = false;
        
        foreach ($lisaluokitukset as $ll) {
            if($ll instanceof Lisaluokitus){
                if($ll->get_arvo(Lisaluokitus::$SARAKENIMI_LISALUOKITUS) == 
                                $lisaluokitustyyppi){
                    $palaute = true;
                }
            }
        }
        return $palaute;
    }
    
    /**
     * Tarkistaa tietokannasta, onko siellä jo havaintojaksolinkki tämän
     * havainnon ja annetun havaintojakson välillä.
     * Palauttaa true, jos linkki löytyy ja false muuten.
     * @param int havaintojakso_id
     */
    public function havaintojaksolinkki_olemassa($havaintojakso_id){
        $hakulause = "SELECT ". Havaintojaksolinkki::$SARAKENIMI_ID.
                    " FROM ". Havaintojaksolinkki::$taulunimi.
                    " WHERE ".Havaintojaksolinkki::$SARAKENIMI_HAVAINTO_ID."=".
                            $this->get_id().
                    " AND ".Havaintojaksolinkki::$SARAKENIMI_HAVAINTOJAKSO_ID."=".
                            $havaintojakso_id;
        
        $osumat = 
            $this->tietokantaolio->tee_omahaku_oliotaulukkopalautteella($hakulause);
        
        if(empty($osumat)){
            return false;
        } else{
            return true;
        }
    }
    
    /**
     * Luo Havintojaksolinkki-luokan olion yhdistämään tämän havainnon
     * havaintojaksoon, jonka id annetaan parametrina.
     * 
     * Varmistaa ensin, ettei samaa havaintoa jo merkitty tapahtumaan. Tätä
     * ei ole mielekästä tehdä, jos kyseessä on uuden havaintojakson tallennus
     * samalla kun havaintojen!
     * 
     * Huomaa, että saman lajin voi toki havaita monta kertaa esimerkiksi
     * matkan aikana eri päivinä.
     * 
     * Palauttaa arvon $OPERAATIO_ONNISTUI, jos linkki lisätään.
     * Muuten palauttaa lisää selittävän kommentin tai virheilmoituksen 
     * (Malliluokkapohja-oliolta). Tämähän ei ole yleensä 
     * virhetoiminto, koska todennäköisesti törmäyksiä tulee aika usein, 
     * vaan rutiinitarkastus.
     * 
     * @param int $havaintojakso_id
     * @param bool $tarkista Jos true, tarkistetaan linkin olemassa olo, muuten ei.
     */
    function lisaa_havaintojaksoon($havaintojakso_id, $tarkista){
        
        $tarkistus_ok = true;
        
        if($tarkista){
            if($this->havaintojaksolinkki_olemassa($havaintojakso_id)){
                $palaute =  Bongaustekstit::$ilm_havaintojaksolinkki_jo_olemassa;
                $tarkistus_ok = false;
            } 
        } 
        
        if($tarkistus_ok){
            $linkki = new Havaintojaksolinkki(Havainto::$MUUTTUJAA_EI_MAARITELTY, 
                                                    $this->tietokantaolio);
            
            $linkki->set_arvo($havaintojakso_id, 
                            Havaintojaksolinkki::$SARAKENIMI_HAVAINTOJAKSO_ID);
            $linkki->set_arvo($this->get_id(), 
                            Havaintojaksolinkki::$SARAKENIMI_HAVAINTO_ID);

            $palaute = $linkki->tallenna_uusi();
        }
        return $palaute;
    }
}

?>
