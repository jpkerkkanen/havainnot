<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of kayttajanakymat
 *
 * @author kerkjuk_admin
 */
class kayttajanakymat extends Nakymapohja{
    
    public static $id_poppookirjautumisdivi = "poppookirjautumisdivi";
    public static $id_kirjautumisdivi = "kirjautumisdivi";
    public static $id_henkilotietolomakeilmoitukset = 
                                                "henkilotietolomakeilmoitukset";
    
    function __construct() {
        parent::__construct();
    }
    
    /**============ ADMIN-KAMA ALKAA =========================================*/
    /**
     * Palauttaa html-taulukon, joka sisältää henkilöiden tiedot
     * lukuunottamatta salasanaa
     * @param type $oliot
     */
    public function nayta() {
        
    }

    
    /**
    * Näyttää lomakkeen, jossa voi muuttaa yhden henkilön valtuuksia.
    * @param <type> $tietokantaolio
    * @param <type> $henkilo_id
    */
    function nayta_valtuusmuutoslomake($tietokantaolio,
                                       $henkilo_id){

       // Haetaan tarvittavat henkilötiedot:
       $hakulause = "SELECT id, etunimi, sukunimi, valtuudet
                       FROM henkilot
                       WHERE id = $henkilo_id";
       $haku = $tietokantaolio->tee_OMAhaku($hakulause);
       $tietotaulu = $tietokantaolio->hae_osumarivit_olioina($haku);

       if(sizeof($tietotaulu) == 1) {
           $henkilotiedot = $tietotaulu[0];

           $lomakehtml = "<form method='post' class='tietolomake'".
           "action='{$_SERVER['PHP_SELF']}?henkilo_id=$henkilotiedot->id'>".
           "<p class='keskitys'><b>K&auml;ytt&auml;j&auml;tiedot</b></p>".
           "<table summary='Valtuudet' align='center'>";

           // Muotoillaan ensin käyttöoikeusvalintanapit:
           $arvot = Valtuudet::hae_valtuusarvot();
           $nimet = Valtuudet::hae_valtuuskuvaukset();
           $name_arvo = "valtuudet";

           // Valtaoikeudet tarkistetaan; oletus normaali eli peruskäyttäjä.
           $valta = $henkilotiedot->valtuudet;

           if(!isset($valta) || $valta < 0){
               $valta = Valtuudet::$KATSELU;
           }
           $oletusvalinta_arvo = $valta;
           $vaakatasossa = false;
           $otsikko = "";  // Laitetaan otsikko erikseen.

           $valtuusvalinnat = Html::luo_valintanapit($arvot,
                               $nimet,
                               $name_arvo,
                               $oletusvalinta_arvo,
                               $vaakatasossa,
                               $otsikko);

           $lomakehtml .= "<tr>";
           $lomakehtml .= "<th>".$henkilotiedot->etunimi;
           $lomakehtml .= " ".$henkilotiedot->sukunimi."</th></tr>";
           $lomakehtml .= "<tr><td>".$valtuusvalinnat."</td></tr>";
           $lomakehtml .= "<tr><td>".
                   "<input type='submit' name='erikoistoiminta'".
                   "value='".Painikkeet::$tallenna_valtuuksien_muutokset_value."'/>".
                   "<input type='submit' name='erikoistoiminta'".
                   "value='".Painikkeet::$poistu_valtuuksien_muutoksista_value.
                   "'/></td></tr>";

           $lomakehtml .= "</table></form>";
       }
       else{
           $lomakehtml = "Yht&auml;&auml;n henkil&ouml;&auml; ei l&ouml;ytynyt!";
       }

       return $lomakehtml;
    }
    
   /**
    * Näyttää ylläpitäjälle lomakkeen, josta käyttäjien valtuuksia voidaan
    * muuttaa.
    * @param Tietokantaolio $tietokantaolio
    */
    function nayta_valtuuslomake($tietokantaolio){

        // Haetaan tarvittavat henkilötiedot:
        $hakulause = "SELECT id, etunimi, sukunimi, valtuudet FROM henkilot";
        $haku = $tietokantaolio->tee_OMAhaku($hakulause);
        $valtuustaulu = $tietokantaolio->hae_osumarivit_olioina($haku);

        $lomakehtml =
            "<div class='tietolomake'>".
            "<p class='keskitys'><b>K&auml;ytt&auml;j&auml;tiedot</b></p>".
            "<table summary='uudet_tiedot' align='center'>".
            "<tr><th>Nimi</th><th>K&auml;ytt&ouml;oikeus</th>".
            "<th>Toimenpide</th></tr>";

        if(sizeof($valtuustaulu) > 0){
            foreach ($valtuustaulu as $henkilotiedot) {
                $lomakehtml .= "<tr>";
                $lomakehtml .= "<td>".$henkilotiedot->etunimi;
                $lomakehtml .= " ".$henkilotiedot->sukunimi."</td>";
                $lomakehtml .= "<td>".
                  Valtuudet::hae_valtuuden_kuvaus($henkilotiedot->valtuudet)."</td>";
                $lomakehtml .= "<td><form method='post'".
                    "action='{$_SERVER['PHP_SELF']}?henkilo_id=$henkilotiedot->id'>".
                    "<input type='submit' name='erikoistoiminta'".
                    "value='".Painikkeet::$muokkaa_valtuudet_value.
                    "'/></form></td></tr>";
            }

            $lomakehtml .= "<tr><td></td><td><form method='post'".
                    "action='{$_SERVER['PHP_SELF']}'>".
                    "<input type='submit' name='erikoistoiminta'".
                    "value='".Painikkeet::$poistu_valtuuksista_value.
                    "'/></form></td><td></td></tr>";

            $lomakehtml .= "</table></div>";
        }
        else{
            $lomakehtml = "Yht&auml;&auml;n henkil&ouml;&auml; ei l&ouml;ytynyt!";
        }

        return $lomakehtml;
    }
    
    /********************************FUNCTION NäYTä_LOMAKE_AJAX **************/
    /**
     * Palauttaa kirjautumislomakkeen html:n parametrien mukaan.
     * Lomakkeen id = 'kirjautumislomake', jolla voi muotoilla lomakkeen
     * ulkoasun.
     * 
     * @param string $ktunnus mahdollinen käyttäjätunnus
     * @return string Palauttaa kirjautumislomakkeen html:n.
     */
    function nayta_kirjautuminen_ajax($ktunnus)
    {
        //$hamy = "?".$hamynimi."=".time();  // Näin vältetään välimuistit.

        $id_ktunnuskentta = "ktunnus_id";
        $id_salakentta = "salasana_id";
        $id_kirjautumisdivi = "kirjautumisdivi";
        
        // Jos ktunnus on epämääritelty, ei näytetä sitä, vaan tyhjä merkkijono.
        if($ktunnus === Parametrit::$EI_MAARITELTY){
            $ktunnus = "";
        }
        
        $ktunnuskentta = Html::luo_input(
                        array(Maarite::type("text"),
                            Maarite::value($ktunnus),
                            Maarite::id($id_ktunnuskentta),
                            Maarite::size(8)));
        
        $salasanakentta = Html::luo_input(
                        array(Maarite::type("password"),
                            Maarite::id($id_salakentta),
                            Maarite::size(8)));
        
        $kirjautumispainike = 
                Html::luo_button(Kayttajatekstit::$nappi_kirjaudu_value, 
                            array(Maarite::onclick("kirjaudu", 
                                    array($id_ktunnuskentta,
                                            $id_salakentta,
                                            Kayttajakontrolleri::$name_ktunnus,
                                            Kayttajakontrolleri::$name_salis))));
        
        
        $mj = Kayttajatekstit::$lomakekentta_kayttajatunnus.": ".
            $ktunnuskentta." ".Kayttajatekstit::$lomakekentta_salasana.": ".
            $salasanakentta.$kirjautumispainike;

        $koko_homma = Html::luo_div($mj, 
                        array(Maarite::id($id_kirjautumisdivi),
                            Maarite::style("display:inline")));
        
        return $koko_homma;
    }
    
    /****************************************FUNCTION NäYTä_LOMAKE **************/
    /**
     * Palauttaa kirjautumislomakkeen html:n parametrien mukaan.
     * Lomakkeen id = 'kirjautumislomake', jolla voi muotoilla lomakkeen
     * ulkoasun.
     * 
     * @param string $ktunnus mahdollinen käyttäjätunnus
     * @return string Palauttaa kirjautumislomakkeen html:n.
     */
    function nayta_kirjautuminen(&$ktunnus)
    {
        $id_ktunnuskentta = "ktunnus_id";
        $id_salakentta = "salasana_id";
        
        
        // Jos käyttis ei määritelty, ei näytetä mitään.
        if($ktunnus === Parametrit::$EI_MAARITELTY){
            $ktunnus = "";
        }
        
        $ktunnuskentta = Html::luo_input(
                        array(Maarite::type("text"),
                            Maarite::value($ktunnus),
                            Maarite::id($id_ktunnuskentta),
                            Maarite::name(Kayttajakontrolleri::$name_ktunnus),
                            Maarite::size(8)));
        
        $salasanakentta = Html::luo_input(
                        array(Maarite::type("password"),
                            Maarite::id($id_salakentta),
                            Maarite::name(Kayttajakontrolleri::$name_salis),
                            Maarite::placeholder("Salasana"),
                            Maarite::size(8)));
        
        $kirjautumispainike = 
                Html::luo_input(array(Maarite::type("submit"),
                                Maarite::value(Kayttajatekstit::
                                                $nappi_kirjaudu_value),
                                Maarite::name(Toimintonimet::$kayttajatoiminto)));
        
        $rekisteroitumispainike = 
                Html::luo_input(array(Maarite::type("submit"),
                                Maarite::value(Kayttajatekstit::
                                                $nappi_rekisteroidy_value),
                                Maarite::name(Toimintonimet::$kayttajatoiminto)));
        
        
        $mj = Kayttajatekstit::$lomakekentta_kayttajatunnus.": ".
            $ktunnuskentta." ".Kayttajatekstit::$lomakekentta_salasana.": ".
            $salasanakentta.$kirjautumispainike.
            " ".Kayttajatekstit::$lomakekentta_uusi_kayttaja.": ".
            $rekisteroitumispainike;
        
        $lomake = Html::luo_form($mj, 
                            array(
                                Maarite::action("index.php"),
                                Maarite::method("post"),
                                Maarite::id(kayttajanakymat::$id_kirjautumisdivi)));
        
        return $lomake;
    }
    
    /************************ FUNCTION NAYTA_POPPOOKIRJAUTUMINEN **************/
    /**
     * Palauttaa kirjautumislomakkeen html:n parametrien mukaan.
     * Lomakkeen id = 'poppookirjautumislomake', jolla voi muotoilla lomakkeen
     * ulkoasun.
     * 
     * @param string $poppootunnus mahdollinen käyttäjätunnus
     * @return string Palauttaa kirjautumislomakkeen html:n.
     */
    function nayta_poppookirjautuminen_ajax($poppootunnus)
    {
        $id_poppootunnuskentta = "poppootunnus_id";
        
        
        // Jos ktunnus on epämääritelty, ei näytetä sitä, vaan tyhjä merkkijono.
        if($poppootunnus === Parametrit::$EI_MAARITELTY){
            $poppootunnus = "";
        }
        
        $poppootunnuskentta = Html::luo_input(
                        array(Maarite::type("password"),
                            Maarite::value($poppootunnus),
                            Maarite::id($id_poppootunnuskentta),
                            Maarite::size(20)));
        
        $kirjautumispainike = 
                Html::luo_button(Kayttajatekstit::$nappi_poppoo_jatka_value, 
                            array(Maarite::onclick("tarkista_poppootunnus", 
                                    array($id_poppootunnuskentta,
                                        Kayttajakontrolleri::$name_poppootunnus))));
        
        $paluupainike = 
                Html::luo_forminput_painike(
                    array(Maarite::classs("rinnakkain")), 
                    array(Maarite::value(
                                Kayttajatekstit::$nappi_poppoo_palaa_value),
                        Maarite::name(Toimintonimet::$kayttajatoiminto)));
        
        $mj = Kayttajatekstit::$poppoolomake_ktunnus.": ".
            $poppootunnuskentta." ".$kirjautumispainike.$paluupainike;
        
        $koko_homma = Html::luo_div($mj, 
                        array(Maarite::id(
                                    kayttajanakymat::$id_poppookirjautumisdivi),
                            Maarite::classs("keskitys")));
        
        return $koko_homma;
    }
    
    /*********************** FUNCTION NÄYTÄ_uloskirjauspainike ****************/
    /**
     * Palauttaa uloskirjautumispainikkeen koodin. 
     * @return <type>
     */
    function nayta_uloskirjautumispainike(){
        $mj = Html::luo_forminput_painike(
                array(Maarite::classs("rinnakkain"),
                    Maarite::action("index.php")), 
                array(Maarite::value(Kayttajatekstit::$nappi_kirjaudu_ulos_value), 
                        Maarite::name(Toimintonimet::$kayttajatoiminto)));
        return $mj;
    }
    //==========================================================================
    
    /*********************** FUNCTION NÄYTÄ_omat_tiedot_painike ****************/
    /**
     * Palauttaa omien tietojen katselu/muutospainikkeen koodin. 
     * @return <type>
     */
    function nayta_omat_tiedot_painike(){
        $mj = Html::luo_forminput_painike(
                array(Maarite::classs("rinnakkain")), 
                array(Maarite::value(Kayttajatekstit::$nappi_omat_tiedot_value), 
                        Maarite::name(Toimintonimet::$kayttajatoiminto),
                        Maarite::title(Kayttajatekstit::$nappi_omat_tiedot_title)));
        return $mj;
    }
    //==========================================================================
    
    /*********************** FUNCTION NÄYTÄ_omat_tiedot_painike_kuvalla ********/
    /**
     * Palauttaa omien tietojen katselu/muutospainikkeen koodin. 
     * @return <type>
     */
    function nayta_omat_tiedot_painike_kuvalla(){
        
        $kuvaos1 = "kuvat/sivuston_ulkoasu/painike_omat_tiedot.png";
        $kuvaos_onmouseover = "kuvat/sivuston_ulkoasu/painike_omat_tiedot_hover.png";
        $id = "omatietopainike";
        
        $mj = Html::luo_form(
                    
                Html::luo_input(
                    array(Maarite::type("image"),
                        Maarite::src($kuvaos1),
                        Maarite::id($id),
                        Maarite::alt("Submit"),
                        Maarite::title(Kayttajatekstit::$nappi_omat_tiedot_title), 
                        Maarite::onmouseout("muuta_src", array($id,$kuvaos1)),
                        Maarite::onmouseover("muuta_src", 
                                        array($id,$kuvaos_onmouseover)))).
                Html::luo_input(
                    array(Maarite::type("hidden"),
                        Maarite::value(Kayttajatekstit::$nappi_omat_tiedot_value), 
                        Maarite::name(Toimintonimet::$kayttajatoiminto))),
                        
                // Formin määritteet:
                array(Maarite::classs("rinnakkain"),
                        Maarite::method("post"),
                        Maarite::action("index.php")));
        return $mj;
    }
    //==========================================================================
    
    /*********************** FUNCTION NÄYTÄ_poppootietopainike_kuvalla ********/
    /**
     * Palauttaa poppootietojen katselupainikkeen koodin. 
     * @return <type>
     */
    function nayta_poppootietopainike_kuvalla(){
        
        $kuvaos1 = "kuvat/sivuston_ulkoasu/painike_poppootiedot.png";
        $kuvaos_onmouseover = 
                "kuvat/sivuston_ulkoasu/painike_poppootiedot_onmouseover.png";
        $id = "poppootietopainike";
        
        $mj = Html::luo_form(
                    
                Html::luo_input(
                    array(Maarite::type("image"),
                        Maarite::src($kuvaos1),
                        Maarite::alt("Submit"),
                        Maarite::id($id),
                        Maarite::title(Kayttajatekstit::$nappi_poppootiedot_title), 
                        Maarite::onmouseout("muuta_src", array($id,$kuvaos1)),
                        Maarite::onmouseover("muuta_src", 
                                        array($id,$kuvaos_onmouseover)))).
                Html::luo_input(
                    array(Maarite::type("hidden"),
                        Maarite::value(Kayttajatekstit::$nappi_poppootiedot_value), 
                        Maarite::name(Toimintonimet::$kayttajatoiminto))),
                        
                // Formin määritteet:
                array(Maarite::classs("rinnakkain"),
                        Maarite::method("post"),
                        Maarite::action("index.php")));
        return $mj;
    }
    //==========================================================================
    /*********************** FUNCTION NÄYTÄ_uloskirjautumispainike_kuvalla ********/
    /**
     * Palauttaa ulos-painikkeen koodin. 
     * @return <type>
     */
    function nayta_uloskirjautumispainike_kuvalla(){
        
        $kuvaos1 = "kuvat/sivuston_ulkoasu/painike_kirjaudu_ulos.png";
        $kuvaos_onmouseover = 
                "kuvat/sivuston_ulkoasu/painike_kirjaudu_ulos_onmouseover.png";
        $id = "uloskirjautumispainike";
        
        $mj = Html::luo_form(
                    
                Html::luo_input(
                    array(Maarite::type("image"),
                        Maarite::src($kuvaos1),
                        Maarite::alt("Submit"),
                        Maarite::id($id),
                        Maarite::title(Kayttajatekstit::$nappi_kirjaudu_ulos_title), 
                        Maarite::onmouseout("muuta_src", array($id,$kuvaos1)),
                        Maarite::onmouseover("muuta_src", 
                                        array($id,$kuvaos_onmouseover)))).
                Html::luo_input(
                    array(Maarite::type("hidden"),
                        Maarite::value(Kayttajatekstit::$nappi_kirjaudu_ulos_value), 
                        Maarite::name(Toimintonimet::$kayttajatoiminto))),
                        
                // Formin määritteet:
                array(Maarite::classs("rinnakkain"),
                        Maarite::method("post"),
                        Maarite::action("index.php")));
        return $mj;
    }
    
    //==========================================================================
    /*********************** FUNCTION NÄYTÄ_kotipainike_kuvalla ********/
    /**
     * Palauttaa kotiin-painikkeen koodin. 
     * @return <type>
     */
    function nayta_kotipainike_kuvalla(){
        
        $kuvaos1 = "kuvat/sivuston_ulkoasu/painike_koti.png";
        $kuvaos_onmouseover = 
                "kuvat/sivuston_ulkoasu/painike_koti_onmouseover.png";
        $id = "kotipainike";
        
        $mj = Html::luo_form(
                    
                Html::luo_input(
                    array(Maarite::type("image"),
                        Maarite::src($kuvaos1),
                        Maarite::alt("Submit"),
                        Maarite::id($id),
                        Maarite::title(Kayttajatekstit::$nappi_koti_title), 
                        Maarite::onmouseout("muuta_src", array($id,$kuvaos1)),
                        Maarite::onmouseover("muuta_src", 
                                        array($id,$kuvaos_onmouseover)))).
                Html::luo_input(
                    array(Maarite::type("hidden"),
                        Maarite::value(Kayttajatekstit::$nappi_koti_value), 
                        Maarite::name(Toimintonimet::$kayttajatoiminto))),
                        
                // Formin määritteet:
                array(Maarite::classs("rinnakkain"),
                        Maarite::method("post"),
                        Maarite::action("index.php")));
        return $mj;
    }
    
    //==========================================================================
    /*********************** FUNCTION NÄYTÄ_ilmoittautumispainike_kuvalla ********/
    /**
     * Palauttaa ulos-painikkeen koodin. 
     * @return <type>
     */
    function nayta_ilmoittautumispainike_kuvalla(){
        
        $kuvaos1 = "kuvat/sivuston_ulkoasu/painike_ilmoittaudun.png";
        $kuvaos_onmouseover = 
                "kuvat/sivuston_ulkoasu/painike_ilmoittaudun_onmouseover.png";
        $id = "ilmoittautumispainike";
        
        $mj = Html::luo_form(
                    
                Html::luo_input(
                    array(Maarite::type("image"),
                        Maarite::src($kuvaos1),
                        Maarite::id($id), 
                        Maarite::alt("Submit"),
                        Maarite::title(Kayttajatekstit::$nappi_rekisteroidy_title),
                        Maarite::onmouseout("muuta_src", array($id,$kuvaos1)),
                        Maarite::onmouseover("muuta_src", 
                                        array($id,$kuvaos_onmouseover)))).
                Html::luo_input(
                    array(Maarite::type("hidden"),
                        Maarite::value(Kayttajatekstit::$nappi_rekisteroidy_value), 
                        Maarite::name(Toimintonimet::$kayttajatoiminto))),
                
                // Formin määritteet:
                array(Maarite::classs("rinnakkain"),
                        Maarite::method("post"), 
                        Maarite::action("index.php")));
        return $mj;
    }
    //==========================================================================
    
    //==========================================================================
    /*********************** FUNCTION NÄYTÄ_ilmoittautumispainike_kuvalla ********/
    /**
     * Palauttaa ulos-painikkeen koodin. 
     * @return <type>
     */
    function nayta_adminpainike_kuvalla(){
        
        $kuvaos1 = "kuvat/sivuston_ulkoasu/painike_admin.png";
        $kuvaos_onmouseover = 
                "kuvat/sivuston_ulkoasu/painike_admin_onmouseover.png";
        $id = "adminpainike";
        
        $mj = Html::luo_form(
                    
                Html::luo_input(
                    array(Maarite::type("image"),
                        Maarite::src($kuvaos1),
                        Maarite::alt("Submit"),
                        Maarite::id($id), 
                        Maarite::title(Kayttajatekstit::$nappi_admin_title),
                        Maarite::onmouseout("muuta_src", array($id,$kuvaos1)),
                        Maarite::onmouseover("muuta_src", 
                                        array($id,$kuvaos_onmouseover)))).
                Html::luo_input(
                    array(Maarite::type("hidden"),
                        Maarite::value(Kayttajatekstit::$nappi_admin_value), 
                        Maarite::name(Toimintonimet::$yllapitotoiminto))),
                
                        
                // Formin määritteet:
                array(Maarite::classs("rinnakkain"),
                        Maarite::method("post"), 
                        Maarite::action("index.php")));
        return $mj;
    }
    //==========================================================================

    /****************************FUNCTION nayta_poppoon_jasenet ***************/
    /**
     * Palauttaa Html-taulukon, joka sisältää poppoon nimen ja poppooseen
     * kuuluvien henkilöiden nimet. Nimien perään lisätään painike, josta
     * kyseisen henkilön tietoja voidaan tarkastella, jos $tietojen_katselu-
     * parametrin arvo on true.
     * 
     * @param string $poppoonimi Poppoon nimi.
     * @param array $henkilot Henkilo-luokan olioita taulukossa.
     * @param bool $tietojen_katselu true, jos painikkeet lisätään. False muuten.
     * @param type $admintoiminto jos true, niin muokkaus lisätään.
     * @return \type
     */
    function nayta_poppoon_jasenet($poppoonimi, $henkilot, $tietojen_katselu, 
                                    $admintoiminto){
        $maar_array = array();
        $taulusisalto = Html::luo_tablerivi(
                            Html::luo_tablesolu_otsikko("'".$poppoonimi."'-".
                                Kayttajatekstit::$poppoon_jasenet.":", 
                                $maar_array), 
                            $maar_array);
        
        $tietopainike = "";
        
        $laskuri = 1;
        
        //============================= security ===============================
        if($admintoiminto){
            $funktionimi = "hae_henkilotiedot_admin"; 
        } else{
            $funktionimi = "hae_henkilotiedot"; 
        }
        //============================= security ===============================
        
        
        foreach ($henkilot as $henkilo) {
            if($henkilo instanceof Henkilo){
                
                // Tarvittaessa luodaan painike tietojen katselua varten:======
                if($tietojen_katselu){
                    $nimisolu = 
                        Html::luo_tablesolu($laskuri.") ".
                            $henkilo->get_arvo(Henkilo::$sarakenimi_etunimi).
                            " ".
                            $henkilo->get_arvo(Henkilo::$sarakenimi_sukunimi), 
                            array(Maarite::classs("klikattava_nimi"),
                                    Maarite::id("nimi".$henkilo->get_id()),
                                    Maarite::onclick($funktionimi, 
                                            array($henkilo->get_id(),
                                                Kayttajakontrolleri::
                                                $name_henkilo_id))));
                } else{
                    $nimisolu = 
                        Html::luo_tablesolu($laskuri.") ".
                            $henkilo->get_arvo(Henkilo::$sarakenimi_etunimi).
                            " ".
                            $henkilo->get_arvo(Henkilo::$sarakenimi_sukunimi), 
                            array());
                }
                
                $taulusisalto .= 
                    Html::luo_tablerivi(
                        $nimisolu,
                        array(Maarite::style("white-space:nowrap;")));
                
            }
            $laskuri++;
        }
        $jasentaulukko = Html::luo_table($taulusisalto, array());
        
        return $jasentaulukko;
    }
    
    /****************************FUNCTION NÄYTÄ_henkilotietolomake *************/
    /**
     * Palauttaa koodi, joka luo lomakkeen, jonka avulla voi muokata omaa
     * salasanaa ja muita tietoja. 
     * 
     * @param Parametrit $parametriolio 
     * @param type $uusi jos true, niin kyseessä uuden henkilön luominen.
     * @return type
     */
    function nayta_henkilotietolomake(&$parametriolio, 
                                        $uusi)
    {
        
        $etun = $parametriolio->etun;     
        $sukun = $parametriolio->sukun;
        $lempin = $parametriolio->lempin;
        $komm = $parametriolio->komm;
        $uusktunnus = $parametriolio->uusktunnus;
        $eosoite = $parametriolio->eosoite; 
        $name = Toimintonimet::$kayttajatoiminto;
        $puh = $parametriolio->puhelin;
        $os = $parametriolio->osoite;
        $asuinmaa = $parametriolio->asuinmaa;
        $kieli = $parametriolio->kieli_henkilo;
        
        $tahti_molemmissa = "*";
        $tahti_vain_uudessa = "";
        
        $otsikko = Html::luo_b(
                    Kayttajatekstit::henkilolomakeotsikko_muokkaus(),array());
        $tallennuspainike = Html::luo_input(    // 1. painike
                            array(Maarite::type("submit"),
                                Maarite::name(Toimintonimet::$kayttajatoiminto),
                                Maarite::value(Kayttajatekstit::
                                        $nappi_tallenna_tietomuutokset_value),
                                Maarite::title(Kayttajatekstit::
                                        $nappi_tallenna_tietomuutokset_title),
                                Maarite::classs("rinnakkain")));
        if($uusi){
            $tahti_vain_uudessa = "*";
            $tallennuspainike = Html::luo_input(    // 1. painike
                            array(Maarite::type("submit"),
                                Maarite::name(Toimintonimet::$kayttajatoiminto),
                                Maarite::value(Kayttajatekstit::
                                        $nappi_henkilo_tallenna_uusi_value),
                                Maarite::title(Kayttajatekstit::
                                        $nappi_henkilo_tallenna_uusi_title),
                                Maarite::classs("rinnakkain")));
            $otsikko = Kayttajatekstit::henkilolomakeotsikko_uusi();
        } 
        
        $otsikko .= Html::luo_p(Kayttajatekstit::henkilolomake_nakyvyysselitys(), 
                        array(Maarite::style("font-size: 80%")));
        
        $mj = $otsikko;
        
        // Ilmoitukset:
        $mj .= Html::luo_div($parametriolio->henkiloilmoitus,
                array(Maarite::id(
                        Kayttajanakymat::$id_henkilotietolomakeilmoitukset),
                    Maarite::classs("korostus")));
        
        $mj.= Html::luo_table(
                
                Html::luo_tablerivi(
                    Html::luo_tablesolu("*".Kayttajatekstit::$lomakekentta_etunimi, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::$name_etunimi),
                                Maarite::value($etun),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Sukunimi:
                Html::luo_tablerivi(
                    Html::luo_tablesolu("*".Kayttajatekstit::$lomakekentta_sukunimi, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::$name_sukunimi),
                                Maarite::value($sukun),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Lempinimi
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$lomakekentta_lempinimi, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::$name_lempinimi),
                                Maarite::value($lempin),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Asuinmaa:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Kayttajatekstit::$lomakekentta_asuinmaa, 
                        array(Maarite::title(
                                    Kayttajatekstit::$lomakekentta_asuinmaa))).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Maat::nayta_maavalikko($asuinmaa, 
                                            "", 
                                            Kayttajakontrolleri::$name_asuinmaa),
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Kieli:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Kayttajatekstit::$lomakekentta_kieli, 
                        array(Maarite::title(
                                    Kayttajatekstit::$lomakekentta_kieli_title))).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Kielet::nayta_kielivalikko($kieli, 
                                            "", 
                                            Kayttajakontrolleri::$name_kieli),
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Kommentti eli kuvaus itsestä:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Kayttajatekstit::$lomakekentta_kuvaus_itsesta, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::$name_kommentti),
                                Maarite::value($komm),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // email:
                Html::luo_tablerivi(
                    Html::luo_tablesolu("*".Kayttajatekstit::$lomakekentta_email, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::$name_eosoite),
                                Maarite::value($eosoite),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Osoite:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$lomakekentta_osoite, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::$name_osoite),
                                Maarite::value($os),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Puhelin:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$lomakekentta_puhelin, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::$name_puhelin),
                                Maarite::value($puh),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Käyttäjätunnus:
                Html::luo_tablerivi(
                    Html::luo_tablesolu($tahti_vain_uudessa.
                                    Kayttajatekstit::$lomakekentta_kayttajatunnus, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(
                                    Kayttajakontrolleri::$name_uusktunnus),
                                Maarite::value(""),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Salasana:
                Html::luo_tablerivi(
                    Html::luo_tablesolu($tahti_vain_uudessa.
                                    Kayttajatekstit::$lomakekentta_salasana, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("password"),
                                Maarite::name(
                                    Kayttajakontrolleri::$name_uusisalasana),
                                Maarite::value(""),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Salasanavahvistus:
                Html::luo_tablerivi(
                    Html::luo_tablesolu($tahti_vain_uudessa.
                                Kayttajatekstit::$lomakekentta_salasana_vahvistus, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("password"),
                                Maarite::name(
                                    Kayttajakontrolleri::$name_salasanavahvistus),
                                Maarite::value(""),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Painikkeet:
                Html::luo_tablerivi(
                    Html::luo_tablesolu("",array()).   // Tablesolu 1 (tyhjä)
                    Html::luo_tablesolu(
                        $tallennuspainike.  // Määritelty metodin alussa.
                            
                        Html::luo_input(    // 2. painike
                            array(Maarite::type("submit"),
                                Maarite::name(Toimintonimet::$kayttajatoiminto),
                                Maarite::value(Kayttajatekstit::
                                        $nappi_poistu_tiedoista_value),
                                Maarite::title(Kayttajatekstit::
                                        $nappi_poistu_tiedoista_title),
                                Maarite::classs("rinnakkain"))),
                        array()), // Tablesolu 2
                    array()), // Tablerivi
                
                array(Maarite::summary("Muokkaustiedot"),   // Table
                    Maarite::align("center")));   
        
        
        $lomake = Html::luo_form($mj, 
                    array(Maarite::action("index.php"),
                        Maarite::method("post"),
                        Maarite::classs("tietolomake_rajaton")));
        
        return $lomake;
    }
    
    /****************************FUNCTION NÄYTÄ_tietolomake *****************/
    /**
     * Palauttaa koodin, jossa näkyy yhden henkilön tiedot. Tiedot eivät ole
     * muokattavassa muodossa. Saman poppoon henkilöiden tietoja voi katsella.
     * 
     * Jos $muokkausoikeus = true, on kysymys ylläpidosta ja silloin lisätään
     * muokkauspainike.
     * 
     * @param Henkilo $henkilo 
     * @param bool $muokkausoikeus
     * @return type
     */
    function nayta_henkilotiedot($henkilo, $muokkausoikeus)
    {
        $etun = $henkilo->get_arvo(Henkilo::$sarakenimi_etunimi);     
        $sukun = $henkilo->get_arvo(Henkilo::$sarakenimi_sukunimi); 
        $lempin = $henkilo->get_arvo(Henkilo::$sarakenimi_lempinimi); 
        $komm = $henkilo->get_arvo(Henkilo::$sarakenimi_kommentti); 
        $eosoite = $henkilo->get_arvo(Henkilo::$sarakenimi_eosoite); 
        $puh = $henkilo->get_arvo(Henkilo::$sarakenimi_puhelin); 
        $os = $henkilo->get_arvo(Henkilo::$sarakenimi_osoite); 
        /*$spaiva = $henkilo->get_arvo(Henkilo::$sarakenimi_syntymapaiva); 
        $skk = $henkilo->get_arvo(Henkilo::$sarakenimi_syntymakk); 
        $svuosi = $henkilo->get_arvo(Henkilo::$sarakenimi_syntymavuosi); */
        
        // painikkeet:
        $painikkeet = "";
        $muokkausnappi = "";
        
        if($muokkausoikeus){
            
            $maar_array_form = array(
                Maarite::action(
                    Maarite::muotoile_action_arvo("index.php", 
                                            array(Kayttajakontrolleri::$name_henkilo_id), 
                                            array($henkilo->get_id()))));
            
            $maar_array_input = array(
                        Maarite::value(
                            Kayttajatekstit::$nappi_admin_muokkaa_henkilo_value),
                        Maarite::name(Toimintonimet::$yllapitotoiminto),
                        Maarite::style("display:inline"));
      
            
            $muokkausnappi = Html::luo_forminput_painike($maar_array_form, 
                                                    $maar_array_input);
        }
        $painikkeet .= $muokkausnappi;
        
        $otsikko = Html::luo_b(
                        Kayttajatekstit::$henkilotiedot_otsikko,
                    array()).
                    $painikkeet;
        
        if($muokkausoikeus){
            $adminotsikko = Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$ilmoitus_yllapitajan_alue, 
                        array(Maarite::colspan(2),
                        Maarite::classs("yllapitoilmoitus"))),   // Tablesolu 1
                    array()); // Tablerivi
        } else{
            $adminotsikko ="";
        }
        
        
        $mj = Html::luo_table(
                
                $adminotsikko.
                // Otsikko:
                Html::luo_tablerivi(
                    Html::luo_tablesolu($otsikko, 
                        array(Maarite::colspan(2))),   // Tablesolu 1
                    array()). // Tablerivi
                
                // Koko nimi:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$lomakekentta_nimi.":", 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        $etun." ".$sukun,
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Lempinimi
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$lomakekentta_lempinimi.": ", 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        $lempin, 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Kommentti eli kuvaus itsestä:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Kayttajatekstit::$lomakekentta_kuvaus_itsesta.": ", 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        $komm, 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // email:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$lomakekentta_email.": ", 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        $eosoite, 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Osoite:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$lomakekentta_osoite.": ", 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        $os,
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Puhelin:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Kayttajatekstit::$lomakekentta_puhelin.": ", 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        $puh, 
                        array()), // Tablesolu 2
                    array()), // Tablerivi
                
                array(Maarite::summary("Henkil&ouml;tiedot"),   // Table
                    Maarite::align("left"),
                    (Maarite::classs("tietotaulu"))));   
        
        // Tämä ei toimi oikein.
        /*$tietolaatikko = Html::luo_form($mj, 
                                array(Maarite::id("tietolomake_rajaton")));*/
        
        
        return $mj;
    }

    /****************************FUNCTION NÄYTÄ_poppootietolomake *************/
    /**
     * Palauttaa koodin, joka luo lomakkeen, jonka avulla voi muokata poppoon
     * tietoja tai syöttää uuden poppoon tiedot
     * 
     * @param Parametrit $parametriolio 
     * @param type $uusi jos true, niin kyseessä uuden poppoon luominen.
     * @param type $parametriolio
     * @param type $uusi
     * @param Poppoo $poppoo
     * @return type
     */
    function nayta_poppootietolomake(&$parametriolio, 
                                        $uusi, $poppoo)
    {
        $tahti_vain_uudessa = "";
        
        // Säädetään, jottei uudessa tule -1:stä oletuskooksi:
        if($parametriolio->poppoo_maksimikoko < 0){
            $parametriolio->poppoo_maksimikoko = "";
        }
            
        $otsikko = Html::luo_b(
                    Kayttajatekstit::poppoolomakeotsikko_muokkaus(),array());
        $tallennuspainike = Html::luo_input(    // 1. painike
                            array(Maarite::type("submit"),
                                Maarite::name(Toimintonimet::$yllapitotoiminto),
                                Maarite::value(Kayttajatekstit::
                                        $nappi_poppoo_tallenna_muokkaus_value),
                                Maarite::classs("rinnakkain")));
        if($uusi){
            $tahti_vain_uudessa = "*";
            $tallennuspainike = Html::luo_input(    // 1. painike
                            array(Maarite::type("submit"),
                                Maarite::name(Toimintonimet::$yllapitotoiminto),
                                Maarite::value(Kayttajatekstit::
                                        $nappi_poppoo_tallenna_uusi_value),
                                Maarite::classs("rinnakkain")));
            $otsikko = Kayttajatekstit::poppoolomakeotsikko_uusi();
            $poppoonimi = $parametriolio->poppoo_nimi;
            $kommentti = $parametriolio->poppoo_kommentti;
            $koko = $parametriolio->poppoo_maksimikoko;
            $tunnus = $parametriolio->poppoo_kayttajatunnus;
            
        } else{
            if($poppoo instanceof Poppoo){
                $poppoonimi = $poppoo->get_arvo(Poppoo::$sarakenimi_nimi);
                $kommentti = $poppoo->get_arvo(Poppoo::$sarakenimi_kommentti);
                $koko = $poppoo->get_arvo(Poppoo::$sarakenimi_maksimikoko);
                $tunnus = "";
            }
        }
       
        $mj = $otsikko;
        
        // Ilmoitukset:
        $mj .= Html::luo_div($parametriolio->poppooilmoitus,
                                array(Maarite::classs("korostus")));
        
        $mj.= Html::luo_table(
                
                // nimi:
                Html::luo_tablerivi(
                    Html::luo_tablesolu("*".Kayttajatekstit::$poppoolomake_nimi, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::$name_poppoonimi),
                                Maarite::value($poppoonimi),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // kommentti eli kuvaus poppoosta:
                Html::luo_tablerivi(
                    Html::luo_tablesolu("*".Kayttajatekstit::$poppoolomake_kommentti, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::
                                                        $name_poppookommentti),
                                Maarite::value($kommentti),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Koko eli jäsenten maksimimäärä:
                Html::luo_tablerivi(
                    Html::luo_tablesolu("*".Kayttajatekstit::$poppoolomake_koko, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(Kayttajakontrolleri::
                                                            $name_poppoomaxikoko),
                                Maarite::value($koko),
                                Maarite::size(4))),
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Käyttäjätunnus:
                Html::luo_tablerivi(
                    Html::luo_tablesolu($tahti_vain_uudessa.
                                    Kayttajatekstit::$poppoolomake_ktunnus, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("password"),
                                Maarite::name(
                                    Kayttajakontrolleri::$name_poppootunnus),
                                Maarite::value($tunnus),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Käyttäjätunnuksen vahvistus:
                Html::luo_tablerivi(
                    Html::luo_tablesolu($tahti_vain_uudessa.
                                    Kayttajatekstit::$poppoolomake_ktunnusvahvistus, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("password"),
                                Maarite::name(
                                    Kayttajakontrolleri::$name_poppootunnusvahvistus),
                                Maarite::value(""),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Painikkeet:
                Html::luo_tablerivi(
                    Html::luo_tablesolu("",array()).   // Tablesolu 1 (tyhjä)
                    Html::luo_tablesolu(
                        $tallennuspainike.  // Määritelty metodin alussa.
                            
                        Html::luo_input(    // 2. painike
                            array(Maarite::type("submit"),
                                Maarite::name(Toimintonimet::$yllapitotoiminto),
                                Maarite::value(Kayttajatekstit::
                                        $nappi_poppoo_poistu_value),
                                Maarite::title(Kayttajatekstit::
                                        $nappi_poppoo_poistu_title),
                                Maarite::classs("rinnakkain"))),
                        array()), // Tablesolu 2
                    array()), // Tablerivi
                
                array(Maarite::summary("Muokkaustiedot"),   // Table
                    Maarite::align("center")));   
        
        // Suljetaan koodi vielä form-elementin sisään:
        $lomake = Html::luo_form($mj, 
                    array(Maarite::action("index.php"),
                        Maarite::method("post"),
                        Maarite::classs("tietolomake_rajaton")));
        
        return $lomake;
    }
    //========================== Admin-näkymiä =================================
    
    /****************************FUNCTION NÄYTÄ_tietolomake *****************/
    /**
     * Palauttaa koodi, joka luo lomakkeen, jonka avulla voi muokata valitun
     * henkilon kayttajatunnusta, salasanaa ja poppoota. 
     * 
     * @param Parametrit $parametriolio 
     * @param type $parametriolio
     * @param array $poppoot Poppoo-luokan oliot taulukossa.
     * @param Henkilo $henkilo Henkilo-luokan olio, eli muokattava henkilö.
     * @return type
     */
    function naytad_henkilotietolomake(&$parametriolio, $poppoot, $henkilo)
    {
        
        $tallennuspainike = Html::luo_input(    // 1. painike
                            array(Maarite::type("submit"),
                                Maarite::name(Toimintonimet::$yllapitotoiminto),
                                Maarite::value(Kayttajatekstit::
                                        $adminnappi_tallenna_tietomuutokset_value),
                                Maarite::title(Kayttajatekstit::
                                        $adminnappi_tallenna_tietomuutokset_title),
                                Maarite::classs(Class_arvo::$rinnakkain)));
        
        //=================== Rakennetaan poppoovalikko ========================
        $arvot = array();
        $nimet = array();
        $select_maaritteet = array(Maarite::name(
                        Kayttajakontrolleri::$name_admin_henkilon_poppoo_id));
        $option_maaritteet = array();
        $oletusvalinta_arvo = $henkilo->get_arvo(Henkilo::$sarakenimi_poppoo_id);
        $otsikko = "";
        
        foreach ($poppoot as $poppoo) {
            if($poppoo instanceof Poppoo){
                array_push($arvot, $poppoo->get_arvo(Poppoo::$SARAKENIMI_ID));
                array_push($nimet, $poppoo->get_arvo(Poppoo::$sarakenimi_nimi));
            }
        }
        
        $poppoovalikko = 
                Html::luo_pudotusvalikko_uusi($arvot, 
                                            $nimet, 
                                            $select_maaritteet, 
                                            $option_maaritteet, 
                                            $oletusvalinta_arvo, 
                                            $otsikko);
        //=================== Rakennetaan poppoovalikko ========================
        
        $otsikko = Html::luo_div(
                    Kayttajatekstit::admin_henkilolomakeotsikko_muokkaus(),
                    array(Maarite::classs(Class_arvo::$yllapitoilmoitus)));
        
        $mj = $otsikko;
        
        // Ilmoitukset:
        $mj .= Html::luo_div($parametriolio->henkiloilmoitus,
                array(Maarite::id(
                        Kayttajanakymat::$id_henkilotietolomakeilmoitukset),
                    Maarite::classs(Class_arvo::$korostus)));
        
        $mj.= Html::luo_table(
                
                // Muokattavan nimi:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Html::luo_div(
                            Kayttajatekstit::$lomakekentta_muokattavan_nimi.
                            ": ".$henkilo->get_arvo(Henkilo::$sarakenimi_etunimi).
                            " ".$henkilo->get_arvo(Henkilo::$sarakenimi_sukunimi),
                            array(Maarite::classs(Class_arvo::$korostus))), 
                        array(Maarite::colspan(2))),    // Solu
                    array()). // Tablerivi
                
                // Asuinmaa:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Kayttajatekstit::$lomakekentta_poppoo, 
                        array(Maarite::title(
                                Kayttajatekstit::$lomakekentta_poppoo_title))).   // Tablesolu 1
                    Html::luo_tablesolu(
                        $poppoovalikko,
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Käyttäjätunnus:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                            Kayttajatekstit::$lomakekentta_kayttajatunnus, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name(
                                    Kayttajakontrolleri::$name_uusktunnus),
                                Maarite::value(""),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Salasana:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                                    Kayttajatekstit::$lomakekentta_salasana, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("password"),
                                Maarite::name(
                                    Kayttajakontrolleri::$name_uusisalasana),
                                Maarite::value(""),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Salasanavahvistus:
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                                Kayttajatekstit::$lomakekentta_salasana_vahvistus, 
                        array()).   // Tablesolu 1
                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("password"),
                                Maarite::name(
                                    Kayttajakontrolleri::$name_salasanavahvistus),
                                Maarite::value(""),
                                Maarite::size(40))), 
                        array()), // Tablesolu 2
                    array()). // Tablerivi
                
                // Painikkeet:
                Html::luo_tablerivi(
                    Html::luo_tablesolu("",array()).   // Tablesolu 1 (tyhjä)
                    Html::luo_tablesolu(
                        $tallennuspainike.  // Määritelty metodin alussa.
                            
                        Html::luo_input(    // 2. painike
                            array(Maarite::type("submit"),
                                Maarite::name(Toimintonimet::$yllapitotoiminto),
                                Maarite::value(Kayttajatekstit::
                                        $adminnappi_poistu_tiedoista_value),
                                Maarite::title(Kayttajatekstit::
                                        $adminnappi_poistu_tiedoista_title),
                                Maarite::classs("rinnakkain"))),
                        array()), // Tablesolu 2
                    array()), // Tablerivi
                
                array(Maarite::summary("Muokkaustiedot"),   // Table
                    Maarite::align("center")));   
        
        
        $lomake = Html::luo_form($mj, 
                    array(Maarite::action(Maarite::muotoile_action_arvo(
                                            Yleisasetuksia::$etusivutiedostonimi, 
                                            array(Kayttajakontrolleri::$name_henkilo_id), 
                                            array($henkilo->get_id()))),
                        Maarite::method("post"),
                        Maarite::classs("tietolomake_rajaton")));
        
        return $lomake;
    }
    
    /**
     * Näyttää taulukossa kaikkien poppoiden tiedot sekä painikkeet poistoa ja
     * muokkausta varten. Palauttaa valmiin Html-koodin.
     * 
     * @param type $poppoot
     * @param Parametrit $parametriolio
     * @param type $valtuudet
     * @return type
     */
    function naytad_poppoot($poppoot, $parametriolio, $valtuudet){
        $mj = Html::luo_div(Kayttajatekstit::$poppootiedot_otsikko,
                                array(Maarite::classs("korostus")));     
        
        // Muodostetaan taulukon rivit:
        $rivit =    
            Html::luo_tablerivi(
                Html::luo_tablesolu_otsikko(Kayttajatekstit::$poppoolomake_nimi, 
                    array()).   // Tablesolu 1
                Html::luo_tablesolu_otsikko(Kayttajatekstit::$poppoolomake_kommentti, 
                    array()).   // Tablesolu 2
                Html::luo_tablesolu_otsikko(Kayttajatekstit::$poppoolomake_ktunnus, 
                    array()).   // Tablesolu 3
                Html::luo_tablesolu_otsikko(Kayttajatekstit::$poppootiedot_koko, 
                    array()).   // Tablesolu 4
                Html::luo_tablesolu_otsikko(Kayttajatekstit::$poppootiedot_luomispvm, 
                    array()).   // Tablesolu 5
                Html::luo_tablesolu_otsikko(Kayttajatekstit::$poppootiedot_havainnot, 
                    array()).   // Tablesolu 6
                Html::luo_tablesolu_otsikko(Kayttajatekstit::$poppootiedot_toiminnot, 
                    array()),
               
                array()); // Tablerivi
        
        
        // Muodostetaan tietorivit:
        foreach ($poppoot as $poppoo) {
            if($poppoo instanceof Poppoo){
                
                // Muokkausnapin valtuudet pitää olla huipussaan, koska siitä voi
                // säätää poppoon kaikkia tietoja. Tehdään uusi formi joka
                // poppoota varten:
                $muokkauspainike = "";
                
                // Alla +0 tarvitaan! Muuten ei true!
                if($valtuudet+0 === Valtuudet::$HALLINTA){

                    $maar_array_input = array(Maarite::type("submit"),
                                    Maarite::name(Toimintonimet::$yllapitotoiminto),
                                    Maarite::value(Kayttajatekstit::
                                            $nappi_poppoo_muokkaa_value),
                                    Maarite::classs("rinnakkain"));

                    $maar_array_form = array(Maarite::action("index.php?".
                                            Kayttajakontrolleri::$name_poppoon_id.
                                            "=".$poppoo->get_id()));

                    $muokkauspainike = Html::luo_forminput_painike($maar_array_form, 
                                                                     $maar_array_input);

                }
                
                $painikkeet = $muokkauspainike;
                $jasenet = $poppoo->hae_poppoon_jasenet();
                $havaintomaara = $poppoo->hae_poppoon_havaintomaara($jasenet);
                
                $rivit .=    
                    Html::luo_tablerivi(
                        Html::luo_tablesolu(
                            $poppoo->get_arvo(Poppoo::$sarakenimi_nimi),
                            array(Maarite::classs("klikattava_nimi"),
                                    Maarite::id("nimi".$poppoo->get_id()),
                                    Maarite::title(Kayttajatekstit::
                                            $nappi_ad_muokkaa_poppoon_jasen_title),
                                    Maarite::onclick("hae_poppootiedot_admin", 
                                            array($poppoo->get_id(),
                                                Kayttajakontrolleri::
                                                $name_poppoon_id)))). // Tablesolu 1
                        Html::luo_tablesolu(
                            $poppoo->get_arvo(Poppoo::$sarakenimi_kommentti), 
                            array()).   // Tablesolu 2
                        Html::luo_tablesolu(
                            $poppoo->get_arvo(Poppoo::$sarakenimi_kayttajatunnus), 
                            array()).   // Tablesolu 3
                        Html::luo_tablesolu(
                            sizeof($jasenet)."/".
                            $poppoo->get_arvo(Poppoo::$sarakenimi_maksimikoko), 
                            array()).   // Tablesolu 4
                        Html::luo_tablesolu(
                            $poppoo->get_arvo(Poppoo::$sarakenimi_luomispvm), 
                            array()).   // Tablesolu 5
                        Html::luo_tablesolu($havaintomaara, 
                            array()).   // Tablesolu 6
                        Html::luo_tablesolu($painikkeet, 
                            array()), 

                        array()); // Tablerivi
                
            }
        }
        
        $mj.= Html::luo_table($rivit, 
                array(Maarite::classs("tietotaulu")));
        
        return $mj;
    }
    
    /**
     * Palauttaa painikkeen koodin.
     * @param type $action_os esim. index.php tai "oletus" / "default"
     * @param type $action_kyselymuuttujat kyselymuuttujien nimet
     * @param type $action_kyselyarvot
     *
    public static function luo_jaa_lisaoikeuksia_painike($action_os, 
                                                        $action_kyselymuuttujat,
                                                        $action_kyselyarvot){
        $jaa_oikeuksia_nappi = 
                Html::luo_form(
                    Html::luo_input(
                        array(Maarite::type("submit"),
                            Maarite::name(Toimintonimet::$lv_toiminto),
                            Maarite::value(Kayttajatekstit::
                                $lisavaltuudet_painike_nayta_lv_lomake_value),
                            Maarite::title(Kayttajatekstit::
                                $lisavaltuudet_painike_nayta_lv_lomake_title))),
                    array(Maarite::classs("rinnakkain"),
                        Maarite::method("post"),
                        Maarite::action(Maarite::muotoile_action_arvo(
                                            $action_os, 
                                            $action_kyselymuuttujat,
                                            $action_kyselyarvot))));
        return $jaa_oikeuksia_nappi;
    }*/
}
?>