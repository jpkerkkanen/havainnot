<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Havaintonakymat
 *
 * @author kerkjuk_admin
 */
class Havaintonakymat extends Nakymapohja{
    
    /**
     *
     * @var \Parametrit $parametriolio 
     */
    public $parametriolio;  // Tätä taidetaan tarvita.
     /**
     *
     * @var \Tietokantaolio $tietokantaolio 
     */
    public $tietokantaolio;  // Tätä myös, vaikka pitkin hampain.
    
    /**
     *
     * @var \Kuvanakymat $kuvanakymat 
     */
    public $kuvanakymat;  // Tätä taidetaan tarvita myös.
    
    public $aikavalikko;    // Tuodaan html-koodi näin, niin ei tartte täällä tehdä.
    
    public function __construct($tietokantaolio, $parametriolio, $kuvanakymat){
        parent::__construct();
        if($parametriolio instanceof Parametrit){
            $this->parametriolio = $parametriolio;
        }
        else{
            $this->parametriolio = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        }
        
        if($tietokantaolio instanceof Tietokantaolio){
            $this->tietokantaolio = $tietokantaolio;
        }
        else{
            $this->tietokantaolio = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        }
        $this->kuvanakymat = $kuvanakymat;
        $this->aikavalikko = "Aikavalikko tyhjä";
        
    }
    
    /**
     * Palauttaa muokkauslomakkeen koodin. Lomakkeessa otetaan huomioon sekä
     * yksittäinen että monen havainnon muokkaus. Monen muokkauksessa ei 
     * muuteta lajiluokkaa, lukumäärää tai sukupuolta.
     * @param array $muokattavat Havainto-luokan olioita.
     */
    public function luo_monimuokkauslomake($muokattavat){
        $html = "";
        
        // Otetaan ekan muokattavan tiedot lähtökohdaksi:
        /**
         * @var \Havainto $muokattava
         */
        $muokattava = $muokattavat[0];
        
        // Ellei eka muokattava ole kunnollinen, ei näytetä edes lomaketta ja 
        // valitetaan ihan kaameesti.
        if($muokattava instanceof Havainto){
        
            $id_hav = $muokattava->get_id();    // Tarvitaan muokkauksessa!
            $lajiluokka_id_hav = $muokattava->get_lajiluokka_id();
            $paiva_hav = $muokattava->get_paiva();
            $kk_hav = $muokattava->get_kk();
            $vuosi_hav = $muokattava->get_vuosi();
            
            // Tämä auttoi heittomerkkien kanssa:
            $paikka_hav = htmlspecialchars($muokattava->get_paikka(), ENT_QUOTES);
            $kommentti_hav = htmlspecialchars($muokattava->get_kommentti(), ENT_QUOTES);
            
            $maa_hav = $muokattava->get_maa();
            $varmuus_hav = $muokattava->get_varmuus();
            
            if(sizeof($muokattavat)==1){
                $ylaluokka_id_lj = $this->parametriolio->ylaluokka_id_lj;
                //echo "ylaluokka_id_lj=".$ylaluokka_id_lj;
            }
                
            $kieli_kuv = $this->parametriolio->kieli_kuv;

            $tietokantaolio = $this->parametriolio->get_tietokantaolio();

            // $tallennuskommentti kertoo mikä laji tallennettiin viimeksi.
            $tallennuskommentti = $this->parametriolio->get_tallennuspalaute();
            
            //=============================================================

            $poistunappi = Html::luo_input(
                                array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::name(Bongaustoimintonimet::
                                                $havaintotoiminto),
                                    Maarite::value(Bongauspainikkeet::
                                                $PERUMINEN_HAVAINTO_VALUE)));

            if(sizeof($muokattavat)==1){
                $uusi_laji_nappi = 
                            Html::luo_input(
                                array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::name(Bongaustoimintonimet::
                                                $havaintotoiminto),
                                    Maarite::value(Bongauspainikkeet::
                                                $UUSI_LAJILUOKKA_VALUE)));
            }

            /*$parametriolio->set_naytettavan_id_hav($id_hav);*/
            $url_jatke_nyk = "#havainto".$id_hav;
            $url_id = "?id_hav=".$id_hav.$url_jatke_nyk;  // Näin löytyy päivitettävä havainto!

            

            // Hiukan eri painiketeksti yhteen ja moneen:
            if(sizeof($muokattavat)==1){
                $value_arvo = Bongauspainikkeet::
                                        $TALLENNA_MUOKKAUS_HAVAINTO_VALUE;
                $havaintolomakeohje = 
                                Bongaustekstit::$havaintolomakemuokkaus_ohje;
            }
            else{
                $value_arvo = Bongauspainikkeet::
                                $HAVAINNOT_TALLENNA_VALITTUJEN_MUOKKAUS_VALUE;
                $havaintolomakeohje = 
                            Bongaustekstit::$havaintolomakemonimuokkaus_ohje;
            }
            
            $submitnappi = Html::luo_input(
                                array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::name(Bongaustoimintonimet::
                                        $havaintotoiminto),
                                    Maarite::value($value_arvo)));


            // Lajivalikko (kun vain yksi valittu)
            if(sizeof($muokattavat)==1){
                $otsikko = "";
                $lajivalikko = Lajiluokka::nayta_lajivalikko($lajiluokka_id_hav,
                                            $tietokantaolio,
                                            $ylaluokka_id_lj,
                                            $kieli_kuv,
                                            $otsikko);
            }
            
            /*************************************************************************/
            $maavalikkohtml = "";

            try{
                $arvot = Maat::hae_maiden_arvot();
                $nimet = Maat::hae_maiden_nimet();
                $name_arvo = Havaintokontrolleri::$name_maa_hav;
                $id_arvo = "";
                $class_arvo = "maavalikko";
                $oletusvalinta_arvo = $maa_hav;
                $otsikko = Maat::$valikko_otsikko;
                $onchange_metodinimi = "kirjoita_maa";
                $onchange_metodiparametrit_array = array();

                $maavalikkohtml.= Html::luo_pudotusvalikko_onChange(
                                            $arvot,
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
                $maavalikkohtml =  "Virhe maavalikossa! (".$poikkeus->getMessage().")";
            }
            /******************************************************************/
            /************************* Sukupuolivalikko ***********************/
            if(sizeof($muokattavat)==1){
                $arvot = Sukupuoli::hae_sukupuoliarvot();
                $nimet = Sukupuoli::hae_sukupuolikuvaukset();
                $select_maaritteet = array(Maarite::name(Havaintokontrolleri::
                                                        $name_sukupuoli_hav));
                $option_maaritteet = array();
                $oletusvalinta_arvo = $muokattava->get_sukupuoli();
                $otsikko = Bongaustekstit::$havaintolomake_sukupuoli;

                $sukupuolivalikko = Html::luo_pudotusvalikko_uusi($arvot, 
                                                                $nimet,
                                                                $select_maaritteet, 
                                                                $option_maaritteet, 
                                                                $oletusvalinta_arvo, 
                                                                $otsikko);
            } else{
                $sukupuolivalikko = "";
            }
            
        
            /******************************************************************/
            /************************* lisäluokitusvalikko ******************/
            $haetaan_luokitukset_tietokannasta = true;
            $lisaluokitusradionapit = 
                        $this->luo_lisaluokitusradionapit($this->parametriolio, 
                                        $haetaan_luokitukset_tietokannasta); 
            /**********************************************************************/
        
            /******************************************************************/

            $naytettava_valinta = $varmuus_hav;
            $varmuusvalikko = Varmuus::muodosta_valikkohtml(false, $naytettava_valinta);

            // kommentin muotoilu:
            if(!empty ($tallennuskommentti)){
                $tallennuskommentti = $tallennuskommentti.Html::luo_br();
            }
            
            // Lukumäärän muotoilu: vain ykkösmuokkauksiin.
            if(sizeof($muokattavat)==1){
                // Jos lkm on tyhjä, annetaan arvo ei-määritelty.
                if($muokattava->get_lkm() == Parametrit::$EI_MAARITELTY){
                    $lkm = "";
                } else{
                    $lkm = $muokattava->get_lkm();
                }
                $lkm_koodi = 
                    Html::luo_label_for("lkm_kentta", " ".
                                Bongaustekstit::$havaintolomake_lkm.": ", "").
                    Html::luo_input(array(Maarite::type("text"),
                                    Maarite::id("lkm_kentta"),
                                    Maarite::size(5),
                                    Maarite::name(
                                        Havaintokontrolleri::$name_lkm_hav),
                                    Maarite::value($lkm)));
            } else{
                $lkm_koodi = "";
            }

            $maar_array = array();

            // Rivi1: ohjeita
            $rivi1 = 
                    Html::luo_tablerivi(
                        Html::luo_tablesolu(
                            Html::luo_b(
                                Html::luo_span(
                                    $tallennuskommentti, 
                                    array(Maarite::id(Bongausasetuksia::
                                        $havaintolomake_tallennustiedote_id))).    // span
                                    $havaintolomakeohje,
                                $maar_array), // b-elementti
                            array(Maarite::colspan(2))), // solu
                        $maar_array);   // taulukkorivi 

            // Toinen rivi: pvm-painikkeet
            $rivi2 =
                    Html::luo_tablerivi(
                        Html::luo_tablesolu(

                            Html::luo_button(
                                Bongauspainikkeet::$ed_vko,
                                array(Maarite::id("b1"),
                                    Maarite::onclick("nayta_ed_vko", ""))). // button1-elementti

                            Html::luo_button(
                                Bongauspainikkeet::$ed_paiva,
                                array(Maarite::id("b2"),
                                    Maarite::onclick("nayta_ed", ""))). // button2-elementti

                            Html::luo_button(
                                Bongauspainikkeet::$seur_paiva,
                                array(Maarite::id("b3"),
                                    Maarite::onclick("nayta_seur()", ""))). // button3-elementti

                            Html::luo_button(
                                Bongauspainikkeet::$seur_vko,
                                array(Maarite::id("b4"),
                                    Maarite::onclick("nayta_seur_vko()", ""))), // button4-elementti

                            array(Maarite::colspan(2),
                                Maarite::align("left"))), // solu
                        $maar_array);   // taulukkorivi 

            // Rivi3: pvm-kentät
            $rivi3 =
                    Html::luo_tablerivi(

                        // Päivän syöttö (vähän solurajat hassusti):
                        Html::luo_tablesolu(
                            Html::luo_label_for("paiva", "*".
                                            Bongaustekstit::$paiva.":", ""), 
                            array(Maarite::align("left"))). // solu1

                        Html::luo_tablesolu(
                            Html::luo_input(
                                array(Maarite::type("text"),
                                    Maarite::id("paiva"),
                                    Maarite::name("paiva_hav"),
                                    Maarite::value($paiva_hav),
                                    Maarite::size("4"),
                                    Maarite::max_length("2"),
                                    Maarite::onchange("nayta_pvm", ""),
                                    Maarite::onkeyup("nayta_pvm", ""))). // input 



                            // Kuukauden syöttö:
                            Html::luo_label_for("kk", "*".
                                            Bongaustekstit::$kk.":", "").
                            Html::luo_input(
                                array(Maarite::type("text"),
                                    Maarite::id("kk"),
                                    Maarite::name("kk_hav"),
                                    Maarite::value($kk_hav),
                                    Maarite::size("4"),
                                    Maarite::max_length("2"),
                                    Maarite::onchange("nayta_pvm", ""),
                                    Maarite::onkeyup("nayta_pvm", ""))). // input 


                            // Vuoden syöttö:
                            Html::luo_label_for("vuosi", "*".
                                            Bongaustekstit::$vuosi.":", "").
                            Html::luo_input(
                                array(Maarite::type("text"),
                                    Maarite::id("vuosi"),
                                    Maarite::name("vuosi_hav"),
                                    Maarite::value($vuosi_hav),
                                    Maarite::size("4"),
                                    Maarite::max_length("4"),
                                    Maarite::onchange("nayta_pvm", ""),
                                    Maarite::onkeyup("nayta_pvm", ""))). // input 

                            Html::luo_span("", 
                                array(Maarite::id("pvm_naytto"))),  //span

                            array(Maarite::align("left"))), // solu2
                        $maar_array);   // taulukkorivi 



            // rivi4: Lajivalinta (tätä ei ole järkeä tehdä monelle!)
            if(sizeof($muokattavat) == 1){
                $rivi4 =
                    Html::luo_tablerivi(
                        Html::luo_tablesolu(
                            Html::luo_label_for("lisaa_myohemmin", 
                                        "*".Bongaustekstit::$laji.": ", ""),
                            array(Maarite::align("left"))). // solu

                        Html::luo_tablesolu(
                            Html::luo_span($lajivalikko, 
                                array(Maarite::id(Bongausasetuksia::
                                        $havaintolomake_lajivalikko_id))).   //span

                            Html::luo_span($uusi_laji_nappi, 
                                array(Maarite::id(Bongausasetuksia::
                                        $havaintolomake_lajivalikkopainike_id))).   //span

                            Html::luo_span("(".Bongaustekstit::
                                        $havaintolomake_laji_puuttuu_ohje.")", 
                                array(Maarite::id(Bongausasetuksia::$havaintolomake_lajivalintaohje_id))),   //span

                            array(Maarite::align("left"),
                                Maarite::id(Bongausasetuksia::
                                    $havaintolomake_lajivalintarivi_id))), // solu   

                        $maar_array);   // taulukkorivi 
            }
            else{
                $rivi4="";
            }
           

            // rivi5: Paikka ja maa:
            $rivi5 = 
                    Html::luo_tablerivi(
                        Html::luo_tablesolu(
                            Html::luo_label_for("lisaa myohemmin", 
                                        "*".Bongaustekstit::$paikka.": ", ""),

                            array(Maarite::align("left"))). // solu

                        Html::luo_tablesolu(

                            Html::luo_input(
                                array(Maarite::type("text"),
                                    Maarite::name("paikka_hav"),
                                    Maarite::value($paikka_hav))). // input 
                            $maavalikkohtml,

                            array(Maarite::align("left"))), // solu   

                        $maar_array);  // taulukkorivi 

            // rivi6: Havainnon varmuus ja lkm:
            $rivi6 = 
                    Html::luo_tablerivi(
                        Html::luo_tablesolu(
                            Html::luo_label_for("lisaa myohemmin",
                                            Varmuus::$valikko_otsikko.":", ""),
                                array(Maarite::align("left"))). // solu

                        Html::luo_tablesolu(
                            $varmuusvalikko." ".$sukupuolivalikko.
                            $lkm_koodi,
                            array(Maarite::align("left"))), // solu   
                            
                        $maar_array);   // taulukkorivi 
            
            

            // rivi7: Havaintokommentti myös vain yhden muokkaukseen:
            if(sizeof($muokattavat) == 1){
                $rivi7 = 
                        Html::luo_tablerivi(
                            Html::luo_tablesolu(Bongaustekstit::$kommentti,
                                array(Maarite::align("left"))). // solu

                            Html::luo_tablesolu(
                                Html::luo_textarea($kommentti_hav, 
                                    array(Maarite::cols(50),
                                            Maarite::rows(6),
                                            Maarite::name("kommentti_hav"))),   // textarea
                                array(Maarite::align("left"))), // solu   
                                
                            $maar_array);   // taulukkorivi 
            } else{
                $rivi7 = "";
            }

            // rivi7_2: Lisäluokitukset:
            $rivi7_2 = 
                    Html::luo_tablerivi(
                        Html::luo_tablesolu(
                            Html::luo_label_for("ll1_valikko",
                                    Bongaustekstit::$havaintolomake_lisaluokitukset.
                                    ":", ""),
                                array(Maarite::align("left"))). // solu

                        Html::luo_tablesolu(
                            $lisaluokitusradionapit,
                            array(Maarite::align("left"))), // solu   
                            
                        $maar_array);   // taulukkorivi 
            
            // rivi8: Painikkeet:
            $rivi8 = 
                    Html::luo_tablerivi(
                        Html::luo_tablesolu("",
                            array(Maarite::align("left"))). // solu

                        Html::luo_tablesolu($submitnappi.$poistunappi,
                            array(Maarite::align("left"))), // solu   

                        $maar_array);   // taulukkorivi 




            // Rivit taulukon sisään:
            $html = 
                Html::luo_table(
                    $rivi1.$rivi2.$rivi3.$rivi4.$rivi5.$rivi6.$rivi7.$rivi7_2.$rivi8, 
                    array(Maarite::summary("uudet_tiedot")));

            // Luodaan valittujen taulukko (näin saadaan myös valinnat eteenpäin):
            if(sizeof($muokattavat)==1){
                $html .= Html::luo_div(
                            $this->luo_havaintotaulukko($muokattavat, 
                                                        true,
                                                        $this->kuvanakymat,
                                                        $this->parametriolio->kieli_henkilo),
                            array(Maarite::style("display: none")));
            }
            else{
                $html .= Html::luo_div(
                            Html::luo_div(Bongaustekstit::
                                    $ilm_havaintojen_muokkausvaroitus, 
                                array(Maarite::classs("havaintomuokkausvaroitus"))).
                            $this->luo_havaintotaulukko($muokattavat, true,
                                                        $this->kuvanakymat),
                            array());
            }
           
            // Taulukko lomakkeen sisään:
            $html = Html::luo_form($html, 
                        array(Maarite::align("center"),
                                Maarite::method("post"),
                                Maarite::action("index.php".$url_id),
                                Maarite::id(Bongausasetuksia::$havaintolomakkeen_id)));

            // näytetään js-päivämäärä
            $html .= Html::luo_script_js("nayta_pvm();");
        }
        
        return $html;
    }
    
    /**
     * Luo ja palauttaa html-taulukon, jossa on parametrina annettujen
     * havaintojen tiedot. 
     * 
     * Huom! Tämän ympärillä ei ole lomaketageja, jotka tarvitaan, jos
     * esimerkiksi valintoja halutaan hyödyntää ilman JavaScriptiä.
    
     * @param type $havainnot
     * @param bool $erikoisvarustelu jos true, niin sopii esim. poistovahvistukseen.
     * Valinnat tällöin valittuna ja muita mahdollisia säätöjä.
     * @param type $kuvanakymat Kuvanakymat-luokan olio tarvitaan havaintokuvien
     * takia.
     * @return type
     */
    private function luo_havaintotaulukko($havainnot, $erikoisvarustelu, 
                                        $kuvanakymat, $kieli){
        if(empty($havainnot)){
            
            // sisalto
            $sisalto = Html::luo_tablerivi(
                            Html::luo_tablesolu_otsikko(
                                    Bongaustekstit::$ilm_ei_havaintoja,
                                    array()), 
                            array());
        }
        else{ // Muotoillaan tiedot nätisti:
            
            // Taulukon otsikkosolut:
            $sisalto =  Html::luo_tablerivi(
                            Html::luo_tablesolu_otsikko(
                                            Bongaustekstit::$havtauluots_nro, 
                                            array()).
                            Html::luo_tablesolu_otsikko(
                                            Bongaustekstit::$havtauluots_laji, 
                                            array()).
                            Html::luo_tablesolu_otsikko(
                                            Bongaustekstit::$havtauluots_aika, 
                                            array()).
                            Html::luo_tablesolu_otsikko(
                                            Bongaustekstit::$havtauluots_paikka, 
                                            array()).
                            Html::luo_tablesolu_otsikko(
                                            Bongaustekstit::$havtauluots_kommentti, 
                                array(Maarite::name(Bongausasetuksia::  // Jotta kommentit saadaan piiloon!
                                    $havaintotaulukon_kommenttisolun_name_arvo))).
                            Html::luo_tablesolu_otsikko(
                                            Bongaustekstit::$havtauluots_bongaaja, 
                                            array()).
                            Html::luo_tablesolu_otsikko(
                                            Bongaustekstit::$havtauluots_toiminnot, 
                                            array()).
                            Html::luo_tablesolu_otsikko(
                                            Bongaustekstit::$havtauluots_pk, 
                                            array()),
                            array());
            
            
            // Muille riveille tiedot yms.
            $rivinro = 1;
            foreach ($havainnot as $hav) {
                if($hav instanceof Havainto){
                    $sisalto .= $this->luo_taulukkorivi($hav, 
                                                        $rivinro, 
                                                        $erikoisvarustelu,
                                                        $kuvanakymat,
                                                        $kieli);
                    $rivinro++;
                }
            }
        }
        
        $koko_homma = Html::luo_table($sisalto, 
                            array(Maarite::classs(Bongausasetuksia::
                                        $havaintotaulun_class)));
        return $koko_homma;
    }
    
    /**
     * Palauttaa poistovahvistuslomakkeen koodin. Lomakkeessa näytetään
     * poistettavat havainnot lähes kuten normaalissa havaintonäytössäkin.
     * @param array $poistettavat Havainto-luokan olioita.
     */
    public function luo_poistovahvistuslomake($poistettavat){
        
        //======================== Toimintapainikkeet ==========================
      
        // Poiston vahvistus:
        $vahvistuspainike = 
            Html::luo_input(array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value(Bongauspainikkeet::
                                            $HAVAINNOT_MONIPOISTOVAHVISTUS_VALUE),
                                    Maarite::title(Bongauspainikkeet::
                                            $HAVAINNOT_MONIPOISTOVAHVISTUS_TITLE),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $havaintotoiminto)));
        
        // Poiston peruminen:
        $perumispainike = 
            Html::luo_input(array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value(Bongauspainikkeet::
                                            $HAVAINNOT_MONIPOISTON_PERUMINEN_VALUE),
                                    Maarite::title(Bongauspainikkeet::
                                            $HAVAINNOT_MONIPOISTON_PERUMINEN_TITLE),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $havaintotoiminto)));
        
        //=====================================================================
        
            
        // Otsikkorivi:
        $sisalto = Html::luo_div($vahvistuspainike." ".
                            Html::luo_b(Bongaustekstit::$havtauluots_varoitus, 
                                array())." ".
                            $perumispainike, 
                        array(Maarite::classs(Bongausasetuksia::
                                            $havaintotauluotsikko_class)));

        // Lisätään valitut taulukossa:
        $erikoisvarustelu = true;
        $sisalto .= $this->luo_havaintotaulukko($poistettavat, 
                                                $erikoisvarustelu,
                                                $this->kuvanakymat);
        
        // Ja palat yhteen:
        $html = 
                Html::luo_form(
                    Html::luo_table($sisalto, 
                            array(Maarite::classs(Bongausasetuksia::
                                        $havaintotaulun_class))), 
                        array(Maarite::action("index.php"),
                            Maarite::method("post")));
        
        return $html;
    }
    
    /**
     * Palauttaa yhden olion tiedot taulukkoriviin pakattuna (tr-elementti).
     * Huomaa huolehtia muista taulukkotageista!
     * @param \Havainto $hav
     * @param int $rivinro Taulukon rivin juokseva nro alkaen yhdestä.
     * @param bool $erikoisvarustelu TRUE -> sopii poistovahvistukseen jne.
     * @param Kuvanakymat $kuvanakymat Kuvanakymat-luokan olio tarvitaan kuvien takia.
     * @return type
     */
    public function luo_taulukkorivi($hav, $rivinro, $erikoisvarustelu,
                                    $kuvanakymat, $kieli) {
        
        
        
        $rivi_class = "";
        if($rivinro % 2 == 0){
            $rivi_class = Bongausasetuksia::$havaintotaulu_parillinenrivi_class;
        }
        
        $rivi_id = "havainto".$hav->get_id();
        
        // Haetaan sitten lajiluokan nimi :
        $lj = new Lajiluokka($this->tietokantaolio, $hav->get_lajiluokka_id());
        
        // Lajiluokan nimi oikealla kielellä:
        $nimi = $lj->hae_lajiluokan_nimi($kieli);
        
        $bongari = new Henkilo($hav->get_henkilo_id(), $this->tietokantaolio);
        
        $bongaajan_nimi = $bongari->get_arvo(Henkilo::$sarakenimi_etunimi).
                        " ".$bongari->get_arvo(Henkilo::$sarakenimi_sukunimi);
        
        // Haetaan havaintoon liittyvät lisäluokitukset:
        $lisaluokat = $hav->hae_lisaluokitukset();
        
        // Lisätään sopivat tarkennukset kommenttiin: =========================
        $kommentti = "";
        if($hav->kuuluu_lisaluokkaan(Lisaluokitus_asetukset::$elis, $lisaluokat)){
            $kommentti .= Html::luo_span(Bongaustekstit::$havtaul_lisaluok_elis." ", 
                                    array(Maarite::classs("havtaul_lisaluok_elis")));

        } else if($hav->kuuluu_lisaluokkaan(Lisaluokitus_asetukset::$maaelis, $lisaluokat)){
            $kommentti .= Html::luo_span(Bongaustekstit::$havtaul_lisaluok_maaelis." ", 
                                    array(Maarite::classs("havtaul_lisaluok_maaelis")));
        }
        
        if($hav->kuuluu_lisaluokkaan(Lisaluokitus_asetukset::$ekopinna, $lisaluokat)){
            $kommentti .= Html::luo_span(Bongaustekstit::$havtaul_lisaluok_eko." ", 
                                    array(Maarite::classs("havtaul_lisaluok_eko")));
        } else if($hav->kuuluu_lisaluokkaan(Lisaluokitus_asetukset::$ekopinna2, $lisaluokat)){
            $kommentti .= Html::luo_span(Bongaustekstit::$havtaul_lisaluok_eko2." ", 
                                    array(Maarite::classs("havtaul_lisaluok_eko2")));
        }
        
        if($hav->kuuluu_lisaluokkaan(Lisaluokitus_asetukset::$piha, $lisaluokat)){
            $kommentti .= Html::luo_span(Bongaustekstit::$havtaul_lisaluok_piha." ", 
                                    array(Maarite::classs("havtaul_lisaluok_piha")));
        }
        
        if($hav->kuuluu_lisaluokkaan(Lisaluokitus_asetukset::$vesilla, $lisaluokat)){
            $kommentti .= Html::luo_span(Bongaustekstit::$havtaul_lisaluok_vesilla." ", 
                                    array(Maarite::classs("havtaul_lisaluok_vesilla")));
        }
        
        if($hav->kuuluu_lisaluokkaan(Lisaluokitus_asetukset::$tornien_taisto, $lisaluokat)){
            $kommentti .= Html::luo_span(Bongaustekstit::$havtaul_lisaluok_tornien_taisto." ", 
                                    array(Maarite::classs("havtaul_lisaluok_tornientaisto")));
        }
        
        $kommentti .= $hav->get_kommentti();
        //======================================================================
        
        //========================== PIkakommentit =========================
        // Toiminnot on tässä vaiheessa valintaruutu. Hakasulut pitää lisätä,
        // jotta ymmärretään taulukoksi!
        $maar_array_valinnat = 
                array(Maarite::name(
                        Havaintokontrolleri::$name_havaintovalinnat_hav."[]"),
                            Maarite::value($hav->get_id()));
            
        
        $toimintasolu = Html::luo_tablesolu(
                                Html::luo_checkbox($maar_array_valinnat), 
                                    array());
        
        // Esim. poistovahvistuksessa valinta oletuksena päällä (koska nämä
        // on valittu):
        if($erikoisvarustelu){
            Maarite::lisaa_maarite(Maarite::checked(), $maar_array_valinnat);
            $toimintasolu = Html::luo_tablesolu(
                                Html::luo_checkbox($maar_array_valinnat), 
                                    array());
        }
        
        //========================== PIkakommentit =========================
        $kommenttien_lkm = 0;
        $on_uusia_pk = false;

        $pikakommentit = 
        Pikakommentti::hae_pikakommentit_poppoorajoituksella(
                                        Pikakommentti::$KOHDE_BONGAUS, 
                                        $hav->get_id(), 
                                        $this->parametriolio->poppoon_id,
                                        $this->tietokantaolio);

        // Jos viimeistä katseluaikaa ei ole asetettu, asetetaan 0:
        if(!isset($_SESSION[Sessio::$edellinen_uloskirjausaika_sek])){
            $_SESSION[Sessio::$edellinen_uloskirjausaika_sek] = 0;
        }

        // Korostetaan solu, jos uusin on riittävän uusi eikä oma:
        if(!empty($pikakommentit)){
            $kommenttien_lkm = sizeof($pikakommentit);
            if(($pikakommentit[0]->get_tallennushetki_sek() >
                $_SESSION['edellinen_uloskirjausaika_sek']) &&
                ($pikakommentit[0]->get_henkilo_id() != 
                        $this->parametriolio->get_omaid()))
            {
                $on_uusia_pk = true;
            }
        }

        // Taulukon solun muotoilu:
        $onclick = Maarite::onclick("hae_pikakommentit", 
                    array(Pikakommentti::$KOHDE_BONGAUS, $hav->get_id()));

        $pk_class = "huomio";
        if($on_uusia_pk){
            $pk_class = "huomio_on_uusia";
        }
        $pikakommenttisolu = Html::luo_tablesolu(
                                Html::luo_span($kommenttien_lkm, 
                                        array(Maarite::id("id".$hav->get_id()))),
                                array(Maarite::classs($pk_class),
                                    
                                    Maarite::title(Bongaustekstit::
                                                    $ilm_pikakommentit_nakyviin),
                                    
                                    Maarite::onclick("hae_pikakommentit", 
                                            array(Pikakommentti::$KOHDE_BONGAUS, 
                                                    $hav->get_id())),
                                    
                                ));
        
        //============= PIkakommentit loppu ================================
        
        // Muokataan varmuus- ja maamerkinnät:
        // Maa merkitään, ellei Suomi:
        if($hav->get_maa() == Maat::$suomi){
            $maa = "";
        }
        else{
            $maa = " (".Maat::hae_maan_nimi($hav->get_maa()).")";
        }

        // Vain epävarmuus näytetään
        $varmuus = ""; 
        if($hav->get_varmuus() == Varmuus::$epavarma){
            $varmuus = " (?)";
        }

        // Haetaan havaintoon liittyvät kuvat:
        $kuvat = $hav->hae_kuvat();
        $kuvahtml = "";
        
        foreach ($kuvat as $kuva) {
            $kuvakoodi = "";
            $kuva_id = -1;
            
            if($kuva instanceof Kuva){
                
                $maxkork = $this->parametriolio->max_nayttokork_kuva;
                $maxlev = $this->parametriolio->max_nayttolev_kuva;
                
                // Lasketaan näyttömitat:
                $mitat = Kuvakontrolleri::laske_kuvan_nayttomitat($maxlev,
                                                            $maxkork,
                                                            $kuva->get_leveys(), 
                                                            $kuva->get_korkeus());
                $nayttolev = $mitat[0];
                $nayttokork = $mitat[1];

                $nayttomitta = max(Array($nayttokork, $nayttolev));
                $kuvakansion_osoite = Kuvakontrolleri::$kuvakansion_osoite;
                $tiedostonimi = $kuva->get_arvo(Kuva::$SARAKENIMI_TIEDOSTONIMI);
                $src = $kuva->get_arvo(Kuva::$SARAKENIMI_SRC);

                // Haku:
                $optimi_src = Kuvakontrolleri::hae_sopivan_kok_kuvan_tied_os(
                                                                $kuvakansion_osoite, 
                                                                $tiedostonimi, 
                                                                $nayttomitta, 
                                                                $src);

                // Näytetään kuvaselityksen sijaan klikkausohje:
                $title = Bongaustekstit::$havtaulkuvan_klikkausohje;

                $kuvakoodi =
                    $kuvanakymat->nayta_pelkka_kuva($kuva->get_id(), 
                                                    $title, 
                                                    $optimi_src, 
                                                    $nayttolev, 
                                                    $nayttokork);

                $kuva_id = $kuva->get_id();
        
                // Mahdollistetaan kuvan näyttö klikkaamalla:
                // Kuvaa klikkaamalla saadaan se isoksi:
                $klikkaus = "";
                if($kuvakoodi != ""){
                    $klikkaus = Maarite::onclick("hae_kuva_ja_tiedot", 
                                        array($hav->get_lajiluokka_id(),
                                            $kuva_id,
                                            $hav->get_id(),
                                            Kuvakontrolleri::$name_ikkunan_lev,
                                            Kuvakontrolleri::$name_ikkunan_kork,
                                            Havaintokontrolleri::$name_id_hav));
                }
                
                // Sujautetaan vielä kuva taulukkosolun sisään:
                $kuvahtml .= Html::luo_span($kuvakoodi, 
                                    array(Maarite::classs("rajaton"),
                                        $klikkaus,
                                        Maarite::title($title))); // solu
                
            }
        }
        
        $kuvasolu = "";
        if(!empty($kuvahtml)){
            $kuvasolu .= Html::luo_tablesolu($kuvahtml, 
                                    array(Maarite::classs("rajaton"))); // solu
        }
        
        
        
      
        
        return         Html::luo_tablerivi(
                            Html::luo_tablesolu(
                                    $rivinro, 
                                    array()).
                
                            Html::luo_tablesolu(
                                    $nimi.$varmuus, 
                                    array(Maarite::classs("huomio"),
                                            Maarite::title(Bongauspainikkeet::
                                                $HAVAINNOT_NAYTA_LAJIHAVAINNOT_TITLE),
                                            Maarite::onclick("hae_lajihavainnot", 
                                                array($hav->get_lajiluokka_id())))).
                
                            Html::luo_tablesolu(
                                    $hav->hae_pvm(), 
                                    array()).
                
                            Html::luo_tablesolu(
                                    $hav->get_paikka().$maa, 
                                    array()).
                
                            Html::luo_tablesolu(
                                Html::luo_table(
                                    Html::luo_tablerivi(
                                        Html::luo_tablesolu(
                                            Html::luo_span($kommentti, 
                                                array(Maarite::classs(Bongausasetuksia::
                                                        $havaintokuvakommentti_class))), // span
                                            array(Maarite::classs("rajaton"))). // Solu
                                        $kuvasolu,
                                        array()), //rivi
                                    array()),  // taulu
                                array(Maarite::name(Bongausasetuksia::  // Jotta kommentit saadaan piiloon!
                                    $havaintotaulukon_kommenttisolun_name_arvo))).  // solu
                
                            Html::luo_tablesolu(
                                    $bongaajan_nimi, 
                                array(Maarite::classs("huomio"),
                                    Maarite::title(Bongauspainikkeet::
                                        $HAVAINNOT_NAYTA_HENKILON_HAVAINNOT_TITLE),
                                    Maarite::onclick("hae_henkilon_havainnot", 
                                        array($hav->get_henkilo_id(),
                                            $hav->get_lajiluokka_id())))).
                
                            $toimintasolu.
                            
                            $pikakommenttisolu, 
                                            
                            array(Maarite::classs($rivi_class), 
                                Maarite::id($rivi_id)));
    }
   

    /**
     * Palauttaa html-koodin, jossa havainnot ja toimintopainikkeet eli
     * kaikki on nätisti taulukkoon aseteltu.
     * 
     * Sidokset: Bongausasetuksia, Bongaustekstit
     * 
     * @param array $havainnot
     * @param int $kieli_id
     * @return type
     */
    public function nayta($havainnot) {
        $sisalto = "";
        
        $kayttaja = new Henkilo($this->parametriolio->get_omaid(), 
                                $this->tietokantaolio);
        $kieli = $kayttaja->get_arvo(Henkilo::$sarakenimi_kieli);
        //===================== Nämä ei ole tarkoitettu normaalikäyttöön! ======
     
        /* painike, josta saa kopioitua lajiluokat ja kuvaukset:
        $korjaus_kopioi_lajiluokat_kuvaukset_painike =
                    Html::luo_input(array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value("Kopioi vanhat"),
                                    Maarite::title("Kopioi vanhat lajiluokat ja kuvaukset"),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $lajiluokkatoiminto)));
        
        // painike, josta saa kopioitua havainnot:
        $korjaus_kopioi_havainnot_painike =
                    Html::luo_input(array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value("Kopioi vanhat havainnot"),
                                    Maarite::title("Kopioi havainnot vanhasta tietokantataulusta"),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $lajiluokkatoiminto)));*/
        
        
        //======================================================================
        $kommentin_piilotusnappi =
            Html::luo_button(Bongauspainikkeet::$HAVAINNOT_PIILOTA_KOMMENTTISARAKE_VALUE, 
                array(Maarite::id("piilotusnappi"),
                        Maarite::title(Bongauspainikkeet::
                                        $HAVAINNOT_PIILOTA_KOMMENTTISARAKE_TITLE),
                        Maarite::onclick("vaihda_kommenttinakyvyys", 
                            array(Bongausasetuksia::
                                    $havaintotaulukon_kommenttisolun_name_arvo))));      


        // painike, josta saa näkyviin havaintolomakkeen:
        $uusi_havainto_painike = 
            Html::luo_input(array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value(Bongauspainikkeet::
                                                    $UUSI_HAVAINTO_VALUE),
                                    Maarite::title(Bongauspainikkeet::
                                                    $UUSI_HAVAINTO_TITLE),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $havaintotoiminto)));
        
        // painike, josta saa näkyviin havaintotilastot:
        $tilastopainike = 
            Html::luo_input(array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value(Bongauspainikkeet::
                                                    $HAVAINNOT_TILASTOT_VALUE),
                                    Maarite::title(Bongauspainikkeet::
                                                    $HAVAINNOT_TILASTOT_TITLE),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $havaintotoiminto)));
        

        // painike, josta saa näkyviin lajiluokkalomakkeen:
        $uusi_lajiluokka_painike =
                    Html::luo_input(array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value(Bongauspainikkeet::
                                                    $UUSI_LAJILUOKKA_VALUE),
                                    Maarite::title(Bongauspainikkeet::
                                                    $UUSI_LAJILUOKKA_TITLE),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $lajiluokkatoiminto)));

        // Painike, joka avaa lajiluokkanäkymän:
        $avaa_lajiluokkanakyma =
            Html::luo_button(Bongauspainikkeet::$LAJILUOKAT_NAYTA_VALUE, 
                    array(Maarite::title(Bongauspainikkeet::$LAJILUOKAT_NAYTA_TITLE),
                            Maarite::onclick("hae_lajiluokat", 
                                array($this->parametriolio->ylaluokka_id_lj)))); 

        // painike, josta saa näkyviin albumit:
        $albuminaytto =
                    Html::luo_input(array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value(Bongauspainikkeet::
                                                    $NAYTA_KUVA_ALBUMIT_VALUE),
                                    Maarite::title(Bongauspainikkeet::
                                                    $NAYTA_KUVA_ALBUMIT_TITLE),
                                    Maarite::onsubmit("bongaus_nayta_albumit", 
                                                            array()),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $kuvatoiminto)));


        // Ylaluokkavalikko+painike:
        $ylaluokka_id = $this->parametriolio->ylaluokka_id_lj;
        $otsikko = Bongaustekstit::$havaintoluokan_valinta_otsikko;
        $kieli_id = Kielet::$SUOMI; // Tätä pitää vähän korjata (latina!).
        $js_metodinimi = "hae_luokan_havainnot";
        $js_param_array = array("this.value");
        $nayta_tyhja = false;

        $valikko = Lajiluokka::nayta_ylaluokkavalikko($nayta_tyhja,
                                                    $this->tietokantaolio,
                                                    $ylaluokka_id,
                                                    $kieli_id,
                                                    $otsikko,
                                                    $js_metodinimi,
                                                    $js_param_array);


        $ylaluokkapainike = Html::luo_input(
                                array(Maarite::type("submit"),
                                    Maarite::classs("rinnakkain"),
                                    Maarite::value(Bongauspainikkeet::
                                            $HAVAINNOT_VALITSE_LAJILUOKKA_VALUE),
                                    Maarite::name(Bongaustoimintonimet::
                                                        $havaintotoiminto)));
        

        $ylaluokkahtml = $valikko." ".$ylaluokkapainike;
        
        //====================================================================
        // Kopiointipainike:
        // Kopioinnin jälkeen pitäisi pystyä näyttämään kopioitu (1.) havainto.
        // 
        // Haetaan suurin olemassaolevista havainto-id:eistä, jotta mahdollisen 
        // kopioitavan/uuden havainnon id voidaan "arvata" (=yhtä isompi).
        /*$suurin_havaintoid = hae_suurin_id($this->tietokantaolio, "bhavainnot");
        $kopioitavan_id = $suurin_havaintoid+1;
        $url_jatke_seur = "#havainto".$kopioitavan_id;
        if($suurin_havaintoid == -1){
            $url_jatke_seur = "";
        }*/
        
        $kopiointinappi = 
                Html::luo_input(
                        array(Maarite::type("submit"),
                                Maarite::name(Bongaustoimintonimet::
                                                $havaintotoiminto),
                                Maarite::value(Bongauspainikkeet::
                                            $HAVAINNOT_MONIKOPIOI_ITSELLE_VALUE),
                                Maarite::title(Bongauspainikkeet::
                                            $HAVAINNOT_MONIKOPIOI_ITSELLE_TITLE),
                                Maarite::onsubmit("bongaus_kopioi_havainto", 
                                                array("valittujen_idt_miten?"))));    //input
                    
                    
        
        // Painikkeita, joiden käyttöä hiukan rajoitetaan. Tosin tässä
        // rajoitus tehdään vasta jatkossa, koska kaikki on pakko pystyä
        // valitsemaan kopioinnin takia. Pitää vain muistaa..
        $muokkausnappi = 
            Html::luo_input(array(Maarite::type("submit"),
                                Maarite::classs("rinnakkain"),
                                Maarite::value(Bongauspainikkeet::
                                    $HAVAINNOT_NAYTA_MONIMUOKKAUSLOMAKE_VALUE),
                                Maarite::title(Bongauspainikkeet::
                                    $HAVAINNOT_NAYTA_MONIMUOKKAUSLOMAKE_TITLE),
                                Maarite::name(Bongaustoimintonimet::
                                                    $havaintotoiminto)));

  
        $poistonappi =
            Html::luo_input(array(Maarite::type("submit"),
                                Maarite::classs("rinnakkain"),
                                Maarite::value(Bongauspainikkeet::
                                    $HAVAINNOT_POISTA_VALITUT_VALUE),
                                Maarite::title(Bongauspainikkeet::
                                    $HAVAINNOT_POISTA_VALITUT_TITLE),
                                Maarite::name(Bongaustoimintonimet::
                                                    $havaintotoiminto)));

        $lisaa_kuva_nappi =
            Html::luo_input(array(Maarite::type("submit"),
                                Maarite::classs("rinnakkain"),
                                Maarite::value(Kuvatekstit::
                                        $painike_uusi_kuva_havaintoihin_value),
                                Maarite::title(Kuvatekstit::
                                        $painike_uusi_kuva_havaintoihin_title),
                                Maarite::name(Bongaustoimintonimet::
                                                    $kuvatoiminto)));
        
        //=====================================================================
        // Aikavalintavalikko:
        $aikavalintavalikko = $this->aikavalikko;
        
        // Haetaan muutama tieto havaitsijan heti nähtäväksi, vaikka
        // varsinaiset tilastot ovatkin muualla.
        $pikatilasto = "";
        $laskut = Havainto::laske_henkilon_bongausten_lkm($this->tietokantaolio, 
                                                $this->parametriolio->get_omaid(), 
                                                $this->parametriolio->ylaluokka_id_lj, 
                                                "hae_kaikkien_elisten_lkm");
        $elikset = $laskut[0];
        $elikset_suomi = $laskut[1];
        
        $vuodarit = Havainto::laske_henkilon_vuodarit($this->tietokantaolio, 
                                                $this->parametriolio->get_omaid(), 
                                                $this->parametriolio->ylaluokka_id_lj, 
                                                "hae_kuluva_vuosi");
        $vuod_kaikki = $vuodarit[0];
        $vuod_suomi = $vuodarit[1];
        $ekovuod = $vuodarit[2];
        
        // Muotoillaan klikattaviksi, jotta saadaan lajit näkyviin:
        $elikset_klik = 
            Html::luo_span(Bongaustekstit::$havainnot_Elikset.": ".$elikset." ", 
                array(Maarite::classs("huomio2"),
                    Maarite::title(Bongaustekstit::$havainnot_Elikset_title),
                    Maarite::onclick("hae_henkilon_pinnalajit", 
                    array($this->parametriolio->get_omaid(), 
                        0,  // Vuosi < 1900 -> haetaan kaikki. 
                        Bongausasetuksia::$nayta_oletushavainnot, 
                        0, // Ei lisäluokitusta. False ei kulje, joten siksi 0.
                        Havaintokontrolleri::$name_lisaluokitusehto_hav))));
        
        $elikset_suomi_klik = 
            Html::luo_span("(FIN: ".$elikset_suomi.") ", 
                array(Maarite::classs("huomio2"),
                    Maarite::title(Bongaustekstit::$havainnot_suomielikset_title),
                    Maarite::onclick("hae_henkilon_pinnalajit", 
                    array($this->parametriolio->get_omaid(), 
                        0,  // Vuosi < 1900 -> haetaan kaikki. 
                        Bongausasetuksia::$nayta_vain_suomessa_havaitut, 
                        0, // Ei lisäluokitusta. False ei kulje, joten siksi 0.
                        Havaintokontrolleri::$name_lisaluokitusehto_hav))));
        
        $vuod_kaikki_klik = 
            Html::luo_span(Bongaustekstit::$havainnot_vuodarit.": ".$vuod_kaikki, 
                array(Maarite::classs("huomio2"),
                    Maarite::title(Bongaustekstit::
                                        $havainnot_vuodarit_kuluva_vuosi_title),
                    Maarite::onclick("hae_henkilon_pinnalajit", 
                    array($this->parametriolio->get_omaid(), 
                        Aika::anna_nyk_vuoden_nro(), 
                        Bongausasetuksia::$nayta_oletushavainnot, 
                        0, // Ei lisäluokitusta. False ei kulje, joten siksi 0.
                        Havaintokontrolleri::$name_lisaluokitusehto_hav))));
        
        $vuod_suomi_klik = 
            Html::luo_span(" (FIN: ".$vuod_suomi.") ", 
                array(Maarite::classs("huomio2"),
                    Maarite::title(Bongaustekstit::
                                $havainnot_vuodarit_kuluva_vuosi_FIN_title),
                    Maarite::onclick("hae_henkilon_pinnalajit", 
                    array($this->parametriolio->get_omaid(), 
                        Aika::anna_nyk_vuoden_nro(), 
                        Bongausasetuksia::$nayta_vain_suomessa_havaitut, 
                        0, // Ei lisäluokitusta. False ei kulje, joten siksi 0.
                        Havaintokontrolleri::$name_lisaluokitusehto_hav))));
        
        $ekovuod_klik = 
            Html::luo_span(Bongaustekstit::$havainnot_ekovuodarit.": ".$ekovuod, 
                array(Maarite::classs("huomio2"),
                    Maarite::title(Bongaustekstit::
                                        $havainnot_ekovuodarit_title),
                    Maarite::onclick("hae_henkilon_pinnalajit", 
                    array($this->parametriolio->get_omaid(), 
                        Aika::anna_nyk_vuoden_nro(), 
                        Bongausasetuksia::$nayta_oletushavainnot, 
                        Lisaluokitus_asetukset::$ekopinna, 
                        Havaintokontrolleri::$name_lisaluokitusehto_hav))));
        
        $pikatilasto .= $elikset_klik.
                        $elikset_suomi_klik.
                        $vuod_kaikki_klik.
                        $vuod_suomi_klik.
                        $ekovuod_klik;
        
        // Kuvatoimintoja ei ole vielä tehty.
        $albuminaytto = "";
        
        if(empty($havainnot)){
          
            // sisalto
            $sisalto =  
                        Html::luo_div($aikavalintavalikko.
                                    $uusi_havainto_painike.$uusi_lajiluokka_painike.
                                    $avaa_lajiluokkanakyma.$ylaluokkahtml.
                                    $albuminaytto, 
                            array(Maarite::classs(Bongausasetuksia::
                                                    $havaintotauluotsikko_class))).
                        // Taulukon sisältö:
                        Html::luo_tablerivi(
                            Html::luo_tablesolu_otsikko(
                                    Bongaustekstit::$ilm_ei_havaintoja,
                                    array()), 
                            array());
        }
        else{ // Muotoillaan tiedot nätisti:
            
            // Otsikkorivi:
            $sisalto = Html::luo_div($pikatilasto, array()).
                    
                        Html::luo_div($aikavalintavalikko.
                                    $uusi_havainto_painike.$tilastopainike.
                                    $uusi_lajiluokka_painike.
                                    $avaa_lajiluokkanakyma.$ylaluokkahtml.
                                    $albuminaytto.$kommentin_piilotusnappi.
                                    $kopiointinappi.$muokkausnappi.
                                    $lisaa_kuva_nappi.$poistonappi, 
                            array(Maarite::classs(Bongausasetuksia::
                                                $havaintotauluotsikko_class))).
                        // Lisätään havainnot taulukossa:
                        $this->luo_havaintotaulukko($havainnot, 
                                                    false, 
                                                    $this->kuvanakymat,
                                                    $kieli);
        }
        
        $koko_homma = 
                Html::luo_form(
                    Html::luo_table($sisalto, 
                        array(Maarite::classs(Bongausasetuksia::
                                        $havaintotaulun_class))), 
                    array(Maarite::action("index.php"),
                            Maarite::method("post")));
        return $koko_homma;
    }
    
    /**
     * Palauttaa monen havainnon tietojen syöttöön tarkoitetun
     * lomakkeen.
     *
     * @param Parametrit $parametriolio
     * @return string
     */
    function nayta_uusi_monen_havainnon_lomake($parametriolio){
        
        $paiva_hav = $parametriolio->paiva_hav;
        $kk_hav = $parametriolio->kk_hav;
        $vuosi_hav = $parametriolio->vuosi_hav;
        $paikka_hav = $parametriolio->paikka_hav;
        $kommentti_hav = $parametriolio->kommentti_hav;
        $tietokantaolio = $this->tietokantaolio;
        
        // Havaintojaksomuuttujat:
        $hj_uusi = $parametriolio->uusi_havjaks;
        $hj_nimi = $parametriolio->nimi_havjaks;
        $hj_kommentti = $parametriolio->kommentti_havjaks;
        $hj_alkuaikapaiva = $parametriolio->alkuaika_sek_havjaks;
        $hj_alkuaikakk = $parametriolio->alkuaika_kk_havjaks;
 
        $hj_alkuaikavuosi= $parametriolio->alkuaika_vuosi_havjaks;
        $hj_alkuaikah= $parametriolio->alkuaika_h_havjaks;
        $hj_alkuaikamin= $parametriolio->alkuaika_min_havjaks;
        $hj_kestomin= $parametriolio->kesto_min_havjaks;
        $hj_kestoh= $parametriolio->kesto_h_havjaks;
        $hj_kestovrk= $parametriolio->kesto_vrk_havjaks;
        
        $hj_valittu = $parametriolio->id_havjaks;

        // Taulukon järjestys. Jos false, niin näytetään riveittäin.
        $lajijarj_ylh_alas = true;
        
        $poistunappi = Html::luo_input(
                            array(Maarite::type("submit"),
                                Maarite::classs("rinnakkain"),
                                Maarite::name(Bongaustoimintonimet::
                                            $havaintotoiminto),
                                Maarite::value(Bongauspainikkeet::
                                            $PERUMINEN_HAVAINTO_VALUE)));
        
        $submitnappi = Html::luo_input(
                            array(Maarite::type("submit"),
                                Maarite::classs("rinnakkain"),
                                Maarite::name(Bongaustoimintonimet::
                                            $havaintotoiminto),
                                Maarite::value(Bongauspainikkeet::
                                            $TALLENNA_MONTA_HAV_KERRALLA_VALUE),
                                Maarite::title(Bongauspainikkeet::
                                            $TALLENNA_MONTA_HAV_KERRALLA_TITLE)));

        $suurin_havaintoid = 
                Yleismetodit::hae_suurin_id($tietokantaolio, Havainto::$taulunimi);

        // Seuraava liittyy siihen, että tallennetut näytetään taulukossa:
        // Luotavan (ekan) id on todennäköisesti yhtä suurempi kuin äsken laskettu:
        $uuden_id = $suurin_havaintoid+1;

        // Otetaan arvo ylös:
        $parametriolio->set_naytettavan_id_hav($uuden_id);

        $url_jatke_seur = "?id_hav=".$uuden_id."#havainto".$uuden_id;
        if($suurin_havaintoid == -1){
            $url_jatke_seur = "";
        }
        $url_id = $url_jatke_seur;

        

        /*************************************************************************/
        $maavalikkohtml = "";

        try{
            $arvot = Maat::hae_maiden_arvot();
            $nimet = Maat::hae_maiden_nimet();
            $name_arvo = Havaintokontrolleri::$name_maa_hav;
            $id_arvo = "";
            $class_arvo = "maavalikko";
            $oletusvalinta_arvo = $parametriolio->maa_hav;
            $otsikko = Maat::$valikko_otsikko;
            $onchange_metodinimi = "kirjoita_maa";
            $onchange_metodiparametrit_array = array();

            $maavalikkohtml.= Html::luo_pudotusvalikko_onChange($arvot,
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
            $maavalikkohtml =  "Virhe maavalikossa! (".$poikkeus->getMessage().")";
        }
        /*************************************************************************/

        $naytettava_valinta = $parametriolio->varmuus_hav;
        $varmuusvalikko = Varmuus::muodosta_valikkohtml(false, $naytettava_valinta);

        // kommentin muotoilu:
        $tallennuskommentti = $this->parametriolio->get_tallennuspalaute();
        if(!empty ($tallennuskommentti)){
            $tallennuskommentti = $tallennuskommentti."<br/>";
        }

        
        /************************* lisäluokitusvalikko *************************/
        $haetaan_luokitukset_tietokannasta = false; // koska uusia!
        $lisaluokitusradionapit = 
                    $this->luo_lisaluokitusradionapit($this->parametriolio,
                                $haetaan_luokitukset_tietokannasta); 
        /**********************************************************************/
        
        //=========================================================================
        //Muotoillaan lajivalinnat:
        $lajioliot = Lajiluokka::hae_kaikki_lajiluokat($parametriolio->kieli_id, 
                                                $parametriolio->ylaluokka_id_lj,
                                                $parametriolio->get_tietokantaolio());
        // Lisätään järjestysnrot:
        $jnro = 1;
        foreach ($lajioliot as $lajiolio) {
            if($lajiolio instanceof Lajiluokka){
                $lajiolio->set_jnro($jnro);
            }
            $jnro++;
        }
        
        
        $rivin_pituus_max = 8;  // Näin monta lajia rivissä.
        
        /* 
         * Tässä pitikin mennä kieli keskellä suuta, jotta järjestyksen saa
         * sarakkeisiin ylhäältä alaspäin. Ensin lajiolio-taulukko jaetaan
         * $rivin_pituus-arvon mukaiseen lukumäärään uusia taulukoita. Sitten
         * oliot kopioidaan takaisin alkuperäiseen taulukkoon niin, että
         * kustakin alitaulukosta otetaan yksi kerrallaan yksi alkio. Tämän
         * jälkeen homma etenee entisen kaavan mukaan.
         */
        if($lajijarj_ylh_alas){
            $lkm = sizeof($lajioliot);
        
            // Tämä on taulukon korkeus korkeimmillaan eli vasemmassa laidassa.
            // Oikea laita voi olla yhtä pienempi (jakojäännös muodostaa alimman
            // rivin). Tämä on siis ensimmäisten aputaulukoiden koot (näitä on
            // $lkm % $rivin_pituus kpl), joiden jälkeen aputaulukoiden koko on
            // yhtä pienempi.
            //$rivien_lkm_vasen_laita = $lkm % $rivin_pituus_max + 1;

            // Taulukko, joka sisältää aputaulukko-taulukot:
            $aputaulukot = array();

            // Luodaan yhtä monta aputaulukkoa, kuin taulukossa on rivejä:
            for($i=0; $i<$rivin_pituus_max; $i++){
                array_push($aputaulukot, array());
            }

            $sarakelaskuri = 0; // Käsitellään tämän sarakkeen mukaista oliota.
            $rivilaskuri = 1; // Osoittaa rivin, jolla ollaan menossa aputaulukossa.

            // Viimeisellä rivillä on useimmiten epätäysi rivi.
            $vika_rivilla_olioita = $lkm % $rivin_pituus_max;
            $vikarivin_vika_indeksi = $vika_rivilla_olioita-1;
            
            // Täysien rivien lkm:
            $taydet_rivit_lkm = floor($lkm/$rivin_pituus_max);

            // Jaetaan aputaulukoihin $lajioliot-taulukon sisältö:
            foreach ($lajioliot as $lajiolio) {

                // Lisätään lajiolio sarakelaskurin mukaiseen aputaulukkoon:
                array_push($aputaulukot[$sarakelaskuri], $lajiolio);

                // Saraketta muutetaan, jos yhteen sarakkeeseen on asetettu kaikki
                // siihen kuuluvat $taydet_rivit_lkm (+mahdollisesti 1) oliota:
                if($sarakelaskuri <= $vikarivin_vika_indeksi){
                    $sarakkeen_korkeus = $taydet_rivit_lkm+1;
                } else{
                    $sarakkeen_korkeus = $taydet_rivit_lkm;
                }

                // Huom! Alla ei toiminut kolmella "="-merkillä! Miksiköhän??
                // Vastaus: floor-metodi palauttaa float-arvon (suuri alue!), 
                // jolloin lukujen tyypit eivät ole samoja. Auttaisi muuttaa
                // sarakkeen_korkeus intiksi.
                if($rivilaskuri == $sarakkeen_korkeus){

                    // Suurennetaan sarakelaskuria yhdellä, ellei mene yli:
                    if($sarakelaskuri < ($rivin_pituus_max-1)){
                        $sarakelaskuri++;
                    }
                    $rivilaskuri = 1;   // Aloitetaan uusi rivi.

                } else{
                    $rivilaskuri++; // Lisätään samaan sarakkeeseen.
                }
            }

            // Luodaan uusi taulukko, johon oliot kopioidaan niin, että kustakin
            // aputaulukosta otetaan yksi olio kerrallaan:
            $lajioliot_uusi = array();
            $testi_max_aputaulu = 0;
            for($i=0; $i<($taydet_rivit_lkm+1); $i++){
                foreach ($aputaulukot as $aputaulukko) {    
                    if($i < sizeof($aputaulukko)){
                        array_push($lajioliot_uusi, $aputaulukko[$i]);
                    }
                    if(sizeof($aputaulukko)>$testi_max_aputaulu){
                        $testi_max_aputaulu = sizeof($aputaulukko);
                    }
                }
            }
        } else{
            $lajioliot_uusi = $lajioliot;
        }
        
        
        // Sitten muodostetaan rivit lajiolioista niin, että lajit ovat
        // aakkosjärjestyksessä sarakkeittain.
        $laskuri = 1;
        $rivi = "";
        $taulukko = "";
        foreach ($lajioliot_uusi as $lajiolio) {
            if($lajiolio instanceof Lajiluokka){

                // Haetaan lajinimi:
                $kuvaus = $lajiolio->hae_kuvaus($parametriolio->kieli_id);
                
                // Ellei kielistä kuvausta löydy, esitetään latina:
                if(!$kuvaus instanceof Kuvaus){
                    $kuvaus = $lajiolio->hae_kuvaus(Kielet::$SUOMI);
                }
                
                // nimi:
                //$lajinimi = $lajiolio->get_jnro().". ".$kuvaus->get_nimi();
                $lajinimi = $kuvaus->get_nimi();

                // Tarkistetaan, onko tämä ollut valittu (esim. muokkaus):
                $checked = "";
                foreach ($parametriolio->lajivalinnat_hav as $valitun_id) {
                    if($lajiolio->get_id() === $valitun_id){
                        $checked = "checked";
                    }
                }
                if(empty($checked)){
                    $solusis = Html::luo_labeled_checkbox($lajinimi,     
                    array(Maarite::name(Havaintokontrolleri::$name_lajivalinnat_hav."[]"),
                            Maarite::value($lajiolio->get_id()))); 
                }
                else{
                    $solusis = Html::luo_labeled_checkbox($lajinimi,     
                    array(Maarite::name(Havaintokontrolleri::$name_lajivalinnat_hav."[]"),
                            Maarite::value($lajiolio->get_id()),
                            Maarite::checked($checked))); 
                }

                $rivi .= Html::luo_tablesolu($solusis, 
                                        array(Maarite::classs("valintasolu")));

                // Jos rivi täynnä tai ollaan vikassa lajissa, 
                // suljetaan se ja tyhjennetään rivimuuttuja:
                if(($laskuri % $rivin_pituus_max === 0) || ($laskuri === sizeof($lajioliot_uusi))){
                    $taulukko .= Html::luo_tablerivi($rivi, array());
                    $rivi = "";
                }
                $laskuri++;
            }
        }
        
        $taulukko = Html::luo_table($taulukko, 
                array(Maarite::classs("tietotaulu_ulkorajaton"),
                        Maarite::align("center")));

        //========================================================================
        // Luodaan form-elementin sisältö:
        $tallennuskommenttipaikka_ja_ohje =
                
            // Paikka tallennuskommentille ja ohje:
            Html::luo_div(Html::luo_span($tallennuskommentti, 
                                        array(Maarite::id(Bongausasetuksia::
                                            $havaintolomake_tallennustiedote_id))).
                        Bongaustekstit::$havaintolomake_uusien_tallennus_ohje, 
                array(Maarite::style("font-weight:bold"),
                    Maarite::classs("havaintolomakerivi")));
            
        $pvmrivi = 
            Html::luo_div(
                Html::luo_span("*".Bongaustekstit::$pvm.": ", array()).
                Html::luo_button(Bongauspainikkeet::$ed_vko, 
                    array(Maarite::id("b1"), 
                            Maarite::type("button"),
                            Maarite::title(Bongauspainikkeet::$ed_vko_title ),
                            Maarite::onclick("nayta_ed_vko", array("")))).

                Html::luo_button(Bongauspainikkeet::$ed_paiva, 
                    array(Maarite::id("b2"), 
                            Maarite::type("button"),
                            Maarite::title(Bongauspainikkeet::$ed_paiva_title),
                            Maarite::onclick("nayta_ed", array(""))))." ".

                Html::luo_input(array(
                        Maarite::type("text"),
                        Maarite::id("paiva"),
                        Maarite::name("paiva_hav"),
                        Maarite::value($paiva_hav),
                        Maarite::size(2),
                        Maarite::max_length(2),
                        Maarite::title(Bongaustekstit::$havaintolomake_paiva),
                        Maarite::onchange("nayta_pvm_hav", ""),
                        Maarite::onkeyup("nayta_pvm_hav", ""))).   

                Html::luo_input(array(
                        Maarite::type("text"),
                        Maarite::id("kk"),
                        Maarite::name("kk_hav"),
                        Maarite::value($kk_hav),
                        Maarite::size(2),
                        Maarite::max_length(2),
                        Maarite::title(Bongaustekstit::$havaintolomake_kk),
                        Maarite::onchange("nayta_pvm_hav", ""),
                        Maarite::onkeyup("nayta_pvm_hav", ""))).     

                Html::luo_input(array(
                        Maarite::type("text"),
                        Maarite::id("vuosi"),
                        Maarite::name("vuosi_hav"),
                        Maarite::value($vuosi_hav),
                        Maarite::size(4),
                        Maarite::max_length(4),
                        Maarite::title(Bongaustekstit::$havaintolomake_vuosi),
                        Maarite::onchange("nayta_pvm_hav", ""),
                        Maarite::onkeyup("nayta_pvm_hav", "")))." ".         


                Html::luo_span(" ", array(Maarite::id("pvm_naytto")))." ". 

                Html::luo_button(Bongauspainikkeet::$seur_paiva, 
                    array(Maarite::id("b3"), 
                            Maarite::type("button"),
                            Maarite::title(Bongauspainikkeet::$seur_paiva_title),
                            Maarite::onclick("nayta_seur", array("")))).

                Html::luo_button(Bongauspainikkeet::$seur_vko, 
                    array(Maarite::id("b4"), 
                            Maarite::type("button"),
                            Maarite::title(Bongauspainikkeet::$seur_vko_title),
                            Maarite::onclick("nayta_seur_vko", array("")))),
                array(Maarite::classs("havaintolomakerivi")));
            
        $paikka_ja_maa = 
            Html::luo_div(
                " *".Bongaustekstit::$paikka.": ".
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::name("paikka_hav"),
                    Maarite::value($paikka_hav),
                    Maarite::size(43)))." ".
                $maavalikkohtml,
                array(Maarite::classs("havaintolomakerivi")));

        $mj = 
            Html::luo_div(
                $tallennuskommenttipaikka_ja_ohje.
                $pvmrivi.        
                $paikka_ja_maa.

                Html::luo_div(Varmuus::$valikko_otsikko.": ".$varmuusvalikko,
                    array(Maarite::classs("havaintolomakerivi"))).  


                Html::luo_div(  
                    Bongaustekstit::$havaintolomake_lisaluokitukset.": ".
                    $lisaluokitusradionapit,
                    array(Maarite::classs("havaintolomakerivi"))),
                array(Maarite::id("havaintolomake_osa1"))).

 
            $this->luo_havaintojaksolomakeruutu($hj_uusi, 
                                                $hj_nimi, 
                                                $hj_kommentti, 
                                                $hj_alkuaikapaiva, 
                                                $hj_alkuaikakk, 

                                                $hj_alkuaikavuosi, 
                                                $hj_alkuaikah, 
                                                $hj_alkuaikamin, 
                                                $hj_kestomin,
                                                $hj_kestoh,
                                                $hj_kestovrk,
                                                $hj_valittu).
            $taulukko.
            $submitnappi.$poistunappi;

        // Sullotaan sisältö form-tagien sisään:
        $mj = Html::luo_form($mj, 
                    array(Maarite::classs("keskitys"),
                        Maarite::method("post"), 
                        Maarite::action("index.php".$url_id), 
                        Maarite::id(Bongausasetuksia::$havaintolomake_kaikki_lajit_id)));

        // Palautetaan lomake ja näytetään nyt päivämäärä;.
        return $mj.Html::luo_script_js("nayta_nyk_pvm('');nayta_nyk_pvm(2);");

    }
    
    function luo_havaintojaksovalikko($valittu){
        
        $valikkohtml = "";
        $max = 100;     // Korkeintaan näin monta näytetään valikossa (uusimmat).

        try{
            $jaksot = Havaintojakso::hae_uusimmat($this->tietokantaolio, $max);
            $arvot = Havaintojakso::hae_jaksojen_idt($jaksot);
            $nimet = Havaintojakso::hae_jaksojen_valikkonimet($jaksot);
            
            // Lisätään ekaksi arvoksi "Uusi": -1;
            Yleismetodit::array_add_first_elem(-1, $arvot);
            Yleismetodit::array_add_first_elem(
                Bongaustekstit::$havaintolomake_uusi, $nimet);
            
            $oletusvalinta_arvo = $valittu;
            $otsikko = Bongaustekstit::$havaintolomake_jaksovalikko_otsikko;
            
            $select_maaritteet = array(
                Maarite::classs("havaintojaksovalikko"),
                Maarite::id("havaintojaksovalikko"),
                Maarite::name(Havaintokontrolleri::$name_id_havjaks),
                Maarite::onchange("vaihda_havjaks", 
                        array("this", Havaintokontrolleri::$name_id_havjaks))
            );
            $option_maaritteet = array();
            
            $valikkohtml .= Html::luo_pudotusvalikko_uusi(
                $arvot,
                $nimet,
                $select_maaritteet,
                $option_maaritteet,
                $oletusvalinta_arvo,
                $otsikko);
        }
        catch(Exception $poikkeus){
            $valikkohtml =  "Virhe havaintojaksovalikossa! (".
                $poikkeus->getMessage().")";
        }
        return $valikkohtml;
    }
    
    /**
     * Palauttaa havaintolomakkeen havaintojakso-osion html-koodin:
     * @return type
     */
    function luo_havaintojaksolomakeruutu(&$uusi, 
                                            &$nimi, 
                                            &$kommentti, 
                                            &$alkuaikapaiva, 
                                            &$alkuaikakk,
                                            &$alkuaikavuosi,
                                            &$alkuaikah,
                                            &$alkuaikamin,
                                            &$kestomin,
                                            &$kestoh,
                                            &$kestovrk,
                                            &$valittu){
        
        $nimi = Parametrit::$EI_MAARITELTY ? "" : $nimi;
        $kommentti = Parametrit::$EI_MAARITELTY ? "" : $kommentti;
        $alkuaikah = Parametrit::$EI_MAARITELTY ? "" : $alkuaikah;
        $alkuaikamin = Parametrit::$EI_MAARITELTY ? "" : $alkuaikamin;
        $kestovrk = Parametrit::$EI_MAARITELTY ? "" : $kestovrk;
        $kestoh = Parametrit::$EI_MAARITELTY ? "" : $kestoh;
        $kestomin = Parametrit::$EI_MAARITELTY ? "" : $kestomin;
        
        $havjaks_ohje = 
            // Div ohjeelle:
            Html::luo_div(
                Bongaustekstit::$havaintolomake_havjaksohje. 
                Html::luo_span(" [Info]", array(Maarite::title(
                        Bongaustekstit::$havaintolomake_havjaksohje_tarkempi))),
                    
                array(Maarite::style("font-weight:bold"),
                    Maarite::classs("havaintolomakerivi")));
        
        $havjaks_valikko = 
            Html::luo_div(
                $this->luo_havaintojaksovalikko($valittu),
                Maarite::classs("havaintolomakerivi"));
        
        $havjaks_pvm =
            Html::luo_div("*".
                Html::luo_span(Bongaustekstit::$havaintolomake_aloitus.": ", array()).
                Html::luo_button(Bongauspainikkeet::$ed_vko, 
                        array(Maarite::id("hb1"), 
                                Maarite::type("button"),
                                Maarite::title(Bongauspainikkeet::$ed_vko_title ),
                                Maarite::onclick("nayta_ed_vko", array(2)))).

                Html::luo_button(Bongauspainikkeet::$ed_paiva, 
                        array(Maarite::id("hb2"), 
                                Maarite::type("button"),
                                Maarite::title(Bongauspainikkeet::$ed_paiva_title),
                                Maarite::onclick("nayta_ed", array(2)))).

                " ".
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_alkupäiva_id),
                    Maarite::name(Havaintokontrolleri::$name_alkuaika_paiva_havjaks),
                    Maarite::value($alkuaikapaiva),
                    Maarite::size(2),
                    Maarite::max_length(2),
                    Maarite::onchange("nayta_pvm_havjaks", ""),
                    Maarite::onkeyup("nayta_pvm_havjaks", ""))).   


                
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_alkukk_id),
                    Maarite::name(Havaintokontrolleri::$name_alkuaika_kk_havjaks),
                    Maarite::value($alkuaikakk),
                    Maarite::size(2),
                    Maarite::max_length(2),
                    Maarite::onchange("nayta_pvm_havjaks", ""),
                    Maarite::onkeyup("nayta_pvm_havjaks", ""))).     

                
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_alkuvuosi_id),
                    Maarite::name(Havaintokontrolleri::$name_alkuaika_vuosi_havjaks),
                    Maarite::value($alkuaikavuosi),
                    Maarite::size(4),
                    Maarite::max_length(4),
                    Maarite::onchange("nayta_pvm_havjaks", ""),
                    Maarite::onkeyup("nayta_pvm_havjaks", "")))." ".         


                Html::luo_span(" ", array(Maarite::id("pvm_naytto2")))." ". 

                Html::luo_button(Bongauspainikkeet::$seur_paiva, 
                        array(Maarite::id("hb3"), 
                                Maarite::type("button"),
                                Maarite::title(Bongauspainikkeet::$seur_paiva_title),
                                Maarite::onclick("nayta_seur", array(2)))).

                Html::luo_button(Bongauspainikkeet::$seur_vko, 
                        array(Maarite::id("hb4"), 
                                Maarite::type("button"),
                                Maarite::title(Bongauspainikkeet::$seur_vko_title),
                            Maarite::onclick("nayta_seur_vko", array(2)))), 
            array(Maarite::classs("havaintolomakerivi")));   // Div määritteet
        
        $havjaks_kellonaika_ja_kesto =
            Html::luo_div(
                
                " *".Bongaustekstit::$havaintolomake_aloitusaika." ".
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_alkuh_id),
                    Maarite::name(Havaintokontrolleri::$name_alkuaika_h_havjaks),
                    Maarite::value($alkuaikah),
                    Maarite::size(2),
                    Maarite::placeholder("hh"))).":".
                /*" ".Bongaustekstit::$havaintolomake_h.*/ 
                    
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_alkumin_id),
                    Maarite::name(Havaintokontrolleri::$name_alkuaika_min_havjaks),
                    Maarite::value($alkuaikamin),
                    Maarite::size(2),
                    Maarite::placeholder("mm"))).
                //" ".Bongaustekstit::$havaintolomake_min
                    
                " *".Bongaustekstit::$havaintolomake_kesto.": ".
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_kestovrk_id),
                    Maarite::name(Havaintokontrolleri::$name_kesto_vrk_havjaks),
                    Maarite::value($kestovrk),
                    Maarite::size(2),
                    Maarite::placeholder("vrk"))).
                    
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_kestoh_id),
                    Maarite::name(Havaintokontrolleri::$name_kesto_h_havjaks),
                    Maarite::value($kestoh),
                    Maarite::size(2),
                    Maarite::placeholder("tunnit"))).
                    
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_kestomin_id),
                    Maarite::name(Havaintokontrolleri::$name_kesto_min_havjaks),
                    Maarite::value($kestomin),
                    Maarite::size(2),
                    Maarite::placeholder("min"))),
                
            array(Maarite::classs("havaintolomakerivi")));  // Div määritteet
        
        /*$havjaks_kesto =
            Html::luo_div(
                
                " *".Bongaustekstit::$havaintolomake_kesto.": ".
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::name(Havaintokontrolleri::$name_kesto_vrk_havjaks),
                    Maarite::value($kestovrk),
                    Maarite::size(2),
                    Maarite::placeholder("vrk"))).
                    
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::name(Havaintokontrolleri::$name_kesto_h_havjaks),
                    Maarite::value($kestoh),
                    Maarite::size(2),
                    Maarite::placeholder("tunnit"))).
                    
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::name(Havaintokontrolleri::$name_kesto_min_havjaks),
                    Maarite::value($kestomin),
                    Maarite::size(2),
                    Maarite::placeholder("min"))),
                
                
            array(Maarite::classs("havaintolomakerivi")));  // Div määritteet*/
        
        $havjaks_nimi_kommentti = 
            Html::luo_div(
                
                " *".Bongaustekstit::$nimi.": ".
                Html::luo_input(array(
                    Maarite::type("text"),
                    Maarite::id(Bongausasetuksia::$havjaksolomake_nimi_id),
                    Maarite::name(Havaintokontrolleri::$name_nimi_havjaks),
                    Maarite::placeholder(
                        Bongaustekstit::$havaintolomake_jaksonimiohje),
                    Maarite::value($nimi),
                    Maarite::size(13)))." ".

                Bongaustekstit::$kommentti.": ".
                    Html::luo_input(array(
                        Maarite::type("text"),
                        Maarite::id(Bongausasetuksia::$havjaksolomake_kommentti_id),
                        Maarite::name(Havaintokontrolleri::$name_kommentti_havjaks),
                        Maarite::placeholder(
                            Bongaustekstit::$havaintolomake_jaksokommenttiohje),
                        Maarite::value($kommentti),
                        Maarite::size(30))), 
            array(Maarite::classs("havaintolomakerivi")));  // Div määritteet
        
        $havjaks_valikko = 
            Html::luo_div(
                $this->luo_havaintojaksovalikko($this->parametriolio->id_havjaks), 
            array(Maarite::classs("havaintolomakerivi")));  // Div määritteet
        
        $sisalto = $havjaks_ohje.
                $havjaks_valikko.
                $havjaks_nimi_kommentti.
                $havjaks_pvm.
                $havjaks_kellonaika_ja_kesto;    
        
        $html = Html::luo_div($sisalto, 
                            array(Maarite::id("havaintojaksolomakeruutu")));
        
        return $html;
    }
    
    /**
     * Palauttaa uuden yksittäisen havainnon tietojen syöttöön tarkoitetun
     * lomakkeen.
     * 
     * <p>Riippuvuudet: yhteiset/php_yhteiset.php</p>
     * 
     * @return string
     */
    function nayta_uusi_havaintolomake(){
        
        $ylaluokka_id_lj = $this->parametriolio->ylaluokka_id_lj;

        $kieli_kuv = $this->parametriolio->kieli_kuv;

        $tietokantaolio = $this->parametriolio->get_tietokantaolio();

        // $tallennuskommentti kertoo mikä laji tallennettiin viimeksi.
        $tallennuskommentti = $this->parametriolio->get_tallennuspalaute();
        
        // Mahdolliset arvot voivat olla epätyhjiä puutteellisen yrityksen
        // jälkeen (ei tartte uudestaan naputella):
        $id_hav = $this->parametriolio->id_hav;    
        $lajiluokka_id_hav = $this->parametriolio->lajiluokka_id_hav;
        $paiva_hav = $this->parametriolio->paiva_hav;
        $kk_hav = $this->parametriolio->kk_hav;
        $vuosi_hav = $this->parametriolio->vuosi_hav;
        $paikka_hav = $this->parametriolio->paikka_hav;
        $kommentti_hav = $this->parametriolio->kommentti_hav;
        $maa_hav = $this->parametriolio->maa_hav;
        $varmuus_hav = $this->parametriolio->varmuus_hav;

        
        //=============================================================

        $poistunappi = Html::luo_input(
                            array(Maarite::type("submit"),
                                Maarite::classs("rinnakkain"),
                                Maarite::name(Bongaustoimintonimet::
                                            $havaintotoiminto),
                                Maarite::value(Bongauspainikkeet::
                                            $PERUMINEN_HAVAINTO_VALUE)));

        
        $uusi_laji_nappi = 
                        Html::luo_input(
                            array(Maarite::type("submit"),
                                Maarite::classs("rinnakkain"),
                                Maarite::name(Bongaustoimintonimet::
                                            $lajiluokkatoiminto),
                                Maarite::value(Bongauspainikkeet::
                                            $UUSI_LAJILUOKKA_VALUE)));
        

        $havaintolomakeohje = Bongaustekstit::$havaintolomake_uusi_ohje;

        
        $submitnappi = Html::luo_input(
                            array(Maarite::type("submit"),
                                Maarite::classs("rinnakkain"),
                                Maarite::name(Bongaustoimintonimet::
                                    $havaintotoiminto),
                                Maarite::value(Bongauspainikkeet::
                                            $TALLENNA_UUSI_HAVAINTO_VALUE)));


        // Lajivalikko (kun vain yksi valittu)
        $otsikko = "";
        $lajivalikko = Lajiluokka::nayta_lajivalikko($lajiluokka_id_hav,
                                        $tietokantaolio,
                                        $ylaluokka_id_lj,
                                        $kieli_kuv,
                                        $otsikko);
        

        /*************************************************************************/
        $maavalikkohtml = "";

        try{
            $arvot = Maat::hae_maiden_arvot();
            $nimet = Maat::hae_maiden_nimet();
            $name_arvo = Havaintokontrolleri::$name_maa_hav;
            $id_arvo = "maavalikko";
            $class_arvo = "";
            $oletusvalinta_arvo = $maa_hav;
            $otsikko = Maat::$valikko_otsikko;
            $onchange_metodinimi = "kirjoita_maa";
            $onchange_metodiparametrit_array = array();

            $maavalikkohtml.= Html::luo_pudotusvalikko_onChange(
                                        $arvot,
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
            $maavalikkohtml =  "Virhe maavalikossa! (".$poikkeus->getMessage().")";
        }
        /******************************************************************/
        /************************* Sukupuolivalikko ****************************/
        $arvot = Sukupuoli::hae_sukupuoliarvot();
        $nimet = Sukupuoli::hae_sukupuolikuvaukset();
        $select_maaritteet = array(Maarite::name(Havaintokontrolleri::$name_sukupuoli_hav));
        $option_maaritteet = array();
        $oletusvalinta_arvo = "ekavaa";
        $otsikko = Bongaustekstit::$havaintolomake_sukupuoli;
        
        $sukupuolivalikko = Html::luo_pudotusvalikko_uusi($arvot, 
                                                        $nimet,
                                                        $select_maaritteet, 
                                                        $option_maaritteet, 
                                                        $oletusvalinta_arvo, 
                                                        $otsikko);
        
        /***********************************************************************/
        /************************* lisäluokitusvalikko *************************/
        $haetaan_luokitukset_tietokannasta = false; // koska uusi!
        $lisaluokitusradionapit = 
                    $this->luo_lisaluokitusradionapit($this->parametriolio,
                                $haetaan_luokitukset_tietokannasta); 
        /**********************************************************************/

        $naytettava_valinta = $varmuus_hav;
        $varmuusvalikko = Varmuus::muodosta_valikkohtml(false, $naytettava_valinta);

        // kommentin muotoilu:
        if(!empty ($tallennuskommentti)){
            $tallennuskommentti = $tallennuskommentti.Html::luo_br();
        }
        
        // Tallennetun näyttö tallennuksen jälkeen. Nyt pitää arvata eli
        // katsoa tietokannasta suurin id ja sillä perusteella asettaa 
        // arvoksi sitä yksi isompi.
        $id_hav_uusi = Yleismetodit::hae_suurin_id($this->tietokantaolio, 
                                    Havainto::$taulunimi)+1;
        $url_jatke_nyk = "#havainto".$id_hav_uusi;
        $url_id = "?id_hav=".$id_hav_uusi.$url_jatke_nyk;  // Näin löytyy päivitettävä havainto!

        $maar_array = array();

        // Rivi1: ohjeita
        $rivi1 = 
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Html::luo_b(
                            Html::luo_span(
                                $tallennuskommentti, 
                                array(Maarite::id(Bongausasetuksia::
                                    $havaintolomake_tallennustiedote_id))).    // span
                                $havaintolomakeohje,
                            $maar_array), // b-elementti
                        array(Maarite::colspan(2))), // solu
                    $maar_array);   // taulukkorivi 

        // Toinen rivi: pvm-painikkeet
        $rivi2 =
                Html::luo_tablerivi(
                    Html::luo_tablesolu(

                        Html::luo_button(
                            Bongauspainikkeet::$ed_vko,
                            array(Maarite::id("b1"),
                                Maarite::onclick("nayta_ed_vko", ""))). // button1-elementti

                        Html::luo_button(
                            Bongauspainikkeet::$ed_paiva,
                            array(Maarite::id("b2"),
                                Maarite::onclick("nayta_ed", ""))). // button2-elementti

                        Html::luo_button(
                            Bongauspainikkeet::$seur_paiva,
                            array(Maarite::id("b3"),
                                Maarite::onclick("nayta_seur()", ""))). // button3-elementti

                        Html::luo_button(
                            Bongauspainikkeet::$seur_vko,
                            array(Maarite::id("b4"),
                                Maarite::onclick("nayta_seur_vko()", ""))), // button4-elementti

                        array(Maarite::colspan(2),
                            Maarite::align("left"))), // solu
                    $maar_array);   // taulukkorivi 

        // Rivi3: pvm-kentät
        $rivi3 =
                Html::luo_tablerivi(

                    // Päivän syöttö (vähän solurajat hassusti):
                    Html::luo_tablesolu(
                        Html::luo_label_for("paiva", "*".
                                        Bongaustekstit::$paiva.":", ""), 
                        array(Maarite::align("left"))). // solu1

                    Html::luo_tablesolu(
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::id("paiva"),
                                Maarite::name("paiva_hav"),
                                Maarite::value($paiva_hav),
                                Maarite::size("4"),
                                Maarite::max_length("2"),
                                Maarite::onchange("nayta_pvm", ""),
                                Maarite::onkeyup("nayta_pvm", ""))). // input 



                        // Kuukauden syöttö:
                        Html::luo_label_for("kk", "*".
                                        Bongaustekstit::$kk.":", "").
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::id("kk"),
                                Maarite::name("kk_hav"),
                                Maarite::value($kk_hav),
                                Maarite::size("4"),
                                Maarite::max_length("2"),
                                Maarite::onchange("nayta_pvm", ""),
                                Maarite::onkeyup("nayta_pvm", ""))). // input 


                        // Vuoden syöttö:
                        Html::luo_label_for("vuosi", "*".
                                        Bongaustekstit::$vuosi.":", "").
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::id("vuosi"),
                                Maarite::name("vuosi_hav"),
                                Maarite::value($vuosi_hav),
                                Maarite::size("4"),
                                Maarite::max_length("4"),
                                Maarite::onchange("nayta_pvm", ""),
                                Maarite::onkeyup("nayta_pvm", ""))). // input 

                        Html::luo_span("", 
                            array(Maarite::id("pvm_naytto"))),  //span

                        array(Maarite::align("left"))), // solu2
                    $maar_array);   // taulukkorivi 

        // painike, josta saadaan näkyviin monta lajia kerralla:
        $rivi4 = Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        "",
                        array(Maarite::align("left"))). // solu1)
                
                    Html::luo_tablesolu(
                        Html::luo_input(
                                array(Maarite::type("submit"),
                                        Maarite::value(Bongauspainikkeet::
                                            $NAYTA_MONEN_HAVAINNON_VALINTA_VALUE),
                                        Maarite::title(Bongauspainikkeet::
                                            $NAYTA_MONEN_HAVAINNON_VALINTA_TITLE),
                                        Maarite::name(Bongaustoimintonimet::
                                            $havaintotoiminto))), 
                        array(Maarite::align("left"))), // solu2)
                    $maar_array);

        // rivi5: Lajivalinta 
        $rivi5 =
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Html::luo_label_for("lisaa_myohemmin", 
                                    "*".Bongaustekstit::$laji.": ", ""),
                        array(Maarite::align("left"))). // solu

                    Html::luo_tablesolu(
                        Html::luo_span($lajivalikko, 
                            array(Maarite::id(Bongausasetuksia::
                                    $havaintolomake_lajivalikko_id))).   //span

                        Html::luo_span($uusi_laji_nappi, 
                            array(Maarite::id(Bongausasetuksia::
                                    $havaintolomake_lajivalikkopainike_id))).   //span

                        Html::luo_span("(".Bongaustekstit::
                                    $havaintolomake_laji_puuttuu_ohje.")", 
                            array(Maarite::id(Bongausasetuksia::$havaintolomake_lajivalintaohje_id))),   //span

                        array(Maarite::align("left"),
                            Maarite::id(Bongausasetuksia::
                                $havaintolomake_lajivalintarivi_id))), // solu   

                    $maar_array);   // taulukkorivi 
        


        // rivi6: Paikka ja maa:
        $rivi6 = 
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Html::luo_label_for("lisaa myohemmin", 
                                    "*".Bongaustekstit::$paikka.": ", ""),

                        array(Maarite::align("left"))). // solu

                    Html::luo_tablesolu(

                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::name("paikka_hav"),
                                Maarite::value($paikka_hav))). // input 
                        $maavalikkohtml,

                        array(Maarite::align("left"))), // solu   

                    $maar_array);  // taulukkorivi 
        
        // rivi7: Havainnon varmuus:
        $rivi7 = 
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Html::luo_label_for("lisaa myohemmin",
                                        Varmuus::$valikko_otsikko.":", ""),
                            array(Maarite::align("left"))). // solu

                    Html::luo_tablesolu(
                        $varmuusvalikko." ".$sukupuolivalikko.
                        Html::luo_label_for("lkm_kentta", " ".
                                Bongaustekstit::$havaintolomake_lkm.":", "").
                        Html::luo_input(
                            array(Maarite::type("text"),
                                Maarite::id("lkm_kentta"),
                                Maarite::size(5),
                                Maarite::name(
                                        Havaintokontrolleri::$name_lkm_hav))),
                        array(Maarite::align("left"))), // solu   

                    $maar_array);   // taulukkorivi 

       
        
        // rivi8: Havaintokommentti:
        $rivi8 = 
                Html::luo_tablerivi(
                    Html::luo_tablesolu(Bongaustekstit::$kommentti,
                        array(Maarite::align("left"))). // solu

                    Html::luo_tablesolu(
                        Html::luo_textarea($kommentti_hav, 
                            array(Maarite::cols(50),
                                    Maarite::rows(6),
                                    Maarite::name("kommentti_hav"))),   // textarea
                        array(Maarite::align("left"))), // solu   

                    $maar_array);   // taulukkorivi 

        // rivi8_2: Lisäluokitukset:
        $rivi8_2 = 
                Html::luo_tablerivi(
                    Html::luo_tablesolu(
                        Html::luo_label_for("ll1_valikko",
                                Bongaustekstit::$havaintolomake_lisaluokitukset.
                                ":", ""),
                            array(Maarite::align("left"))). // solu

                    Html::luo_tablesolu(
                        $lisaluokitusradionapit,
                        array(Maarite::align("left"))), // solu   

                    $maar_array);   // taulukkorivi 
        
        // rivi9: Painikkeet:
        $rivi9 = 
                Html::luo_tablerivi(
                    Html::luo_tablesolu("",
                        array(Maarite::align("left"))). // solu

                    Html::luo_tablesolu($submitnappi.$poistunappi,
                        array(Maarite::align("left"))), // solu   

                    $maar_array);   // taulukkorivi 




        // Rivit taulukon sisään:
        $taulukko = 
            Html::luo_table(
                $rivi1.$rivi2.$rivi3.$rivi4.$rivi5.$rivi6.$rivi7.$rivi8.$rivi8_2.$rivi9, 
                array(Maarite::summary("uudet_tiedot")));


        // Taulukko lomakkeen sisään:
        $html = Html::luo_form($taulukko, 
                    array(Maarite::align("center"),
                            Maarite::method("post"),
                            Maarite::action("index.php".$url_id),
                            Maarite::id(Bongausasetuksia::$havaintolomakkeen_id)));

        // näytetään js-päivämäärä
        $html .= Html::luo_script_js("nayta_pvm();");
        
        
        return $html;
    }
    
    /**
    * Palauttaa linkit, joista päästään näkemään kaikki havainnot vuosittain.
    * @param Parametrit $parametriolio 
    */
   static function nayta_arkistolinkit(){
       $nyk_vuosi = anna_nyk_vuoden_nro();
       $aloitusvuosi = Bongausasetuksia::$aloitusvuosi;
       
       $palaute = Html::luo_a_linkto("", 
                        Bongaustekstit::$nayta_uusimmat, 
                    array(Maarite::onclick("hae_havainnot", 
                                array(Bongausasetuksia::$nayta_oletushavainnot))));

     
       // Muodostetaan linkit, joista lähetetään ajax-kutsu kysely vuoden havainnoista.
       for($i=$nyk_vuosi; $i >= $aloitusvuosi; $i--){
           $linkkiteksti = $i;
           $url = "";   // Toimii Ajaxin kautta
           
           // Vuosilinkit
           $palaute .= Html::luo_a_linkto($url, 
                            $linkkiteksti, 
                            array(Maarite::onclick("hae_havainnot", array($i))));
           
       }
       return $palaute;
   }
   /**
    * Luo radionappien koodin. Jos kyse on muokkauksesta, hakee valituista
    * havainnoista ensimmäisen mukaiset valinnat tietokannasta.
    * @param Parametrit $parametriolio
    * @param type $tarkista_tietokanta
    * @return type
    */
   public function luo_lisaluokitusradionapit($parametriolio, $tarkista_tietokanta){
        $lisaluokitukset_olio = new Lisaluokitus_asetukset();
        $vaihtoehdot = $lisaluokitukset_olio->get_asetukset();

        $lisaluokitusradionapit = ""; 

        // Tämä liittyy vain muokkaukseen. Haetaan 1. valitun havainnon kaikki
        // lisäluokitukset tietokannasta. 
        if($tarkista_tietokanta){
            if(!empty($parametriolio->havaintovalinnat_hav)){
                $valitun_havainnon_id = $parametriolio->havaintovalinnat_hav[0];
            } else{
                $valitun_havainnon_id = Havainto::$MUUTTUJAA_EI_MAARITELTY;
            }
            $havainto = new Havainto($valitun_havainnon_id, $this->tietokantaolio);
            if($havainto->olio_loytyi_tietokannasta){
                $lisaluokitukset = $havainto->hae_lisaluokitukset();
            } else{
                $lisaluokitukset = array();
            }
        }
        
        foreach ($vaihtoehdot as $lisaluokitusasetus){
            if($lisaluokitusasetus instanceof Asetus){

                $checked = "";
                if($tarkista_tietokanta){
                    foreach ($lisaluokitukset as $oleva) {
                        if($oleva instanceof Lisaluokitus){
                            if($lisaluokitusasetus->get_arvo() == $oleva->get_arvo(
                                        Lisaluokitus::$SARAKENIMI_LISALUOKITUS)){
                                $checked = "checked";
                            }
                        }
                    }
                } else{
                    // Tarkistetaan, onko tämä ollut valittu (esim. virhesyöte):
                    foreach ($parametriolio->lisaluokitusvalinnat_hav as $valitun_id) {
                        if($lisaluokitusasetus->get_arvo() === $valitun_id){
                            $checked = "checked";
                        }
                    }
                }
                

                // Muodostetaan radionappi:
                if(empty($checked)){
                    $solusis = Html::luo_labeled_checkbox($lisaluokitusasetus->get_nimi(),     
                    array(Maarite::name(Havaintokontrolleri::
                                       $name_lisaluokitusvalinnat_hav."[]"),
                                   
                            Maarite::title($lisaluokitusasetus->get_selitys()),
                            Maarite::value($lisaluokitusasetus->get_arvo()),
                            Maarite::classs("lisaluokitusnappi"),
                            Maarite::onclick("tarkista_lisaluokitusvalinnat", 
                                            array("lisaluokitusnappi")))); 
                }
                else{
                    $solusis = Html::luo_labeled_checkbox($lisaluokitusasetus->get_nimi(),     
                    array(Maarite::name(Havaintokontrolleri::
                                        $name_lisaluokitusvalinnat_hav."[]"),
                                     
                            Maarite::title($lisaluokitusasetus->get_selitys()),
                            Maarite::value($lisaluokitusasetus->get_arvo()),
                            Maarite::checked($checked),
                            Maarite::classs("lisaluokitusnappi"),
                            Maarite::onclick("tarkista_lisaluokitusvalinnat", 
                                            array("lisaluokitusnappi")))); 
                }

                $lisaluokitusradionapit .= Html::luo_span($solusis,
                                        array(Maarite::classs("labelradionappi")));
            }
        }
        return $lisaluokitusradionapit;
   }
   
   /**
    * Palauttaa kuvalomakkeen, jossa on kuvan tiedot ja tiedot valituista
    * lajiluokista.
    * 
    * <p>Riippuvuuksia: </p>
    */
   public function luo_kuvalomake($valitut_hav, $kuvalomake_ilman_formia){
       $ylaosa = $kuvalomake_ilman_formia;
       $alaosa = $this->luo_havaintotaulukko($valitut_hav, true, $this->kuvanakymat);
       $varoitus = Html::luo_div(Bongaustekstit::
                                    $ilm_havaintokuvan_lisaaminen_huomautus.":", 
                                array(Maarite::classs(Bongausasetuksia::
                                                $havaintotauluotsikko_class)));
       
       $lomake = Html::luo_form($ylaosa.$varoitus.$alaosa, 
                    array(Maarite::align("left"),
                            Maarite::method("post"),
                            Maarite::id("kuvalomake"),
                            Maarite::action("index.php?"),
                            Maarite::enctype_multipart_form_data()));
       
       return $lomake;
   }
}
?>