<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kuvanakymat
 *
 * @author J-P
 */
class Kuvanakymat extends Nakymapohja{
    /**
     * Palauttaa kuvan html:n
     *
     * @param <type> $actionosoite esim index.php
     * @param <type> $actionkyselynimet
     * @param <type> $actionkyselyarvot
     * @param <type> $kuvakansion_os
     * @param <type> $kayttajan_valtuudet
     * @return string kuvan html-koodi
     */
    public function nayta_kuva($actionosoite,
                                $actionkyselynimet,
                                $actionkyselyarvot,
                                $kayttajan_valtuudet){
        $kork = $this->korkeus;
        $lev = $this->leveys;
        $ind = $this->nayttokokoindeksi;

        $nayttolev = laske_kuvan_maksimileveys($ind);
        $nayttokork = laske_kuvan_maksimikorkeus($nayttolev,$lev,$kork);

        /* Poisto- ja muokkauspainike */
        $poista_kuva_painike = "";
        $muokkaa_kuva_painike = "";

        if($kayttajan_valtuudet === Valtuudet::$HALLINTA){

            // Muotoillaan action-koodi eli lomakkeen osoitteen kyselyosa:
            $vain_arvo = true; // Ei haluta "action="-juttua alkuun.
            $url_jatke = "";
            $actionkoodi = luo_action_koodi($actionosoite,
                                        $actionkyselynimet,
                                        $actionkyselyarvot,
                                        $vain_arvo,
                                        $url_jatke);
            
            $form_maaritteet = array('method'=>'post',
                                    'class'=>'rinnakkain',
                                    'action'=>$actionkoodi);

            /* Poistopainike */
            $onsubmit_funktionimi= "nayta_kuvan_poistovahvistus";
            $onsubmit_parametrit = array($this->id);    /* Kuvan id*/

            $input_maaritteet =
                  array('name'=>Toimintonimet::$kuvatoiminto,
                        'value'=>Painikkeet::$POISTA_KUVA_VALUE);

            try{
                $poista_kuva_painike =
                                Html::luo_submit_painike_onsubmit_toiminnolla(
                                                        $onsubmit_funktionimi,
                                                        $onsubmit_parametrit,
                                                        $form_maaritteet,
                                                        $input_maaritteet);
            }
            catch (Exception $poikkeus){
                $palaute .= "Virhe poistopainikkeen luomisessa: ".
                                $poikkeus->getMessage();
            }

            // MUOKKAUSPAINIKE: TArkistetaan urliin menevät tiedot:
            $kuvaotsikko_turv = urlencode($this->kuvaotsikko);
            $kuvaselitys_turv = urlencode($this->kuvaselitys);

            $form_maaritteet = array(
                        'method'=>'post',
                        'class'=>'rinnakkain',
                        'action'=>"{$_SERVER['PHP_SELF']}".
                            $actionkoodi.
                            "&kuvaotsikko_kuva=".$kuvaotsikko_turv.
                            "&kuvaselitys_kuva=".$kuvaselitys_turv.
                            "&vuosi_kuva=".$this->vuosi.
                            "&kk_kuva=".$this->kk.
                            "&paiva_kuva=".$this->paiva.
                            "&nayttokokoindeksi_kuva=".$this->nayttokokoindeksi
                        );

            /* Muokkauspainike: */
            $onsubmit_funktionimi= "nayta_kuvan_muokkauslomake";
            $onsubmit_parametrit = array($this->id);    /* Kuvan id*/

            $input_maaritteet =
                  array('name'=>Toimintonimet::$kuvatoiminto,
                        'value'=>Painikkeet::$MUOKKAA_KUVA_VALUE);

            try{
                $muokkaa_kuva_painike =
                                Html::luo_submit_painike_onsubmit_toiminnolla(
                                                        $onsubmit_funktionimi,
                                                        $onsubmit_parametrit,
                                                        $form_maaritteet,
                                                        $input_maaritteet);
            }
            catch (Exception $poikkeus){
                $palaute .= "Virhe poistopainikkeen luomisessa: ".
                                $poikkeus->getMessage();
            }
        }

        $kuva_id = $this->id;
        $maxmitta = max(Array($nayttokork, $nayttolev));

        // Haku:
        if($maxmitta <= Kuva::$TIETOKANTAKUVA_PIENI_MITTA){

            $osoite_mini1 =
                $pikkukuvakansio_osoite."/".
                Kuva::$pikkukuva1_nimen_osa.$this->tiedostonimi;
            $src = "'".$osoite_mini1."'";
        }
        else if($maxmitta <= Kuva::$TIETOKANTAKUVA_ISO_MITTA){
            $osoite_mini2 =
                $pikkukuvakansio_osoite."/".
                Kuva::$pikkukuva2_nimen_osa.$this->tiedostonimi;
            $src = "'".$osoite_mini2."'";
        }
        else if($maxmitta <= Kuva::$TIETOKANTAKUVA_ISO2_MITTA){
            $osoite_mini3 =
                $pikkukuvakansio_osoite."/".
                Kuva::$pikkukuva3_nimen_osa.$this->tiedostonimi;
            $src = "'".$osoite_mini3."'";
        }
        else{
            $src = "'".$kuvatiedot->src."'";
        }


        $kuva_html =
            "<table class='kuvaraamit'>".
            "<tr>".
                "<th>".
                $this->kuvaotsikko.
                $muokkaa_kuva_painike.
                $poista_kuva_painike.
                "</th>".
            "</tr>".
            "<tr>".
                "<td>".
                " <!-- KUVAKOODI ALKAA: ÄLÄ MUOKKAA!-->".
                    "<img id=".$this->id." title='".$this->kuvaselitys.
                        "' src=".$src." width='".$nayttolev."'".
                        "height='".$nayttokork.
                    "'/>".
                "<!-- KUVAKOODIN LOPPU-->".
                "</td>".
            "</tr>".
            "</table>";

        return $kuva_html;
    }

    /* Palauttaa kuvan html:n ilman mitään painikkeita. */
    public function nayta_pelkka_kuva_ja_otsikko(){
        $kork = $this->korkeus;
        $lev = $this->leveys;
        $ind = $this->nayttokokoindeksi;

        $nayttolev = $this->laske_kuvan_maksimileveys($ind);
        $nayttokork = $this->laske_kuvan_maksimikorkeus($nayttolev,$lev,$kork);

        $kuva_id = $this->id;
            $maxmitta = max(Array($nayttokork, $nayttolev));

        // Haku:
        if($maxmitta <= Kuva::$TIETOKANTAKUVA_PIENI_MITTA){

            $osoite_mini1 =
                $pikkukuvakansio_osoite."/".
                Kuva::$pikkukuva1_nimen_osa.$this->tiedostonimi;
            $src = "'".$osoite_mini1."'";
        }
        else if($maxmitta <= Kuva::$TIETOKANTAKUVA_ISO_MITTA){
            $osoite_mini2 =
                $pikkukuvakansio_osoite."/".
                Kuva::$pikkukuva2_nimen_osa.$this->tiedostonimi;
            $src = "'".$osoite_mini2."'";
        }
        else if($maxmitta <= Kuva::$TIETOKANTAKUVA_ISO2_MITTA){
            $osoite_mini3 =
                $pikkukuvakansio_osoite."/".
                Kuva::$pikkukuva3_nimen_osa.$this->tiedostonimi;
            $src = "'".$osoite_mini3."'";
        }
        else{
            $src = "'".$this->src."'";
        }

        $kuva_html =
            "<table align='right'>".
            "<tr>".
                "<th>".
                $this->kuvaotsikko.
                "</th>".
            "</tr>".
            "<tr>".
                "<td class='rajaton'>".
                " <!-- KUVAKOODI ALKAA: ÄLÄ MUOKKAA!-->".
                    "<img id=".$this->id." title='".$this->kuvaselitys.
                        "' src=".$src." width='".$nayttolev."'".
                        "height='".$nayttokork.
                    "'/>".
                "<!-- KUVAKOODIN LOPPU-->".
                "</td>".
            "</tr>".
            "</table>";

        return $kuva_html;
    }

    /**
     * Palauttaa pelkän kuvan html:n ilman mitään painikkeita tms.
     * @param type $kuva_id
     * @param type $kuva_selitys
     * @param type $kuva_src
     * @param type $nayttolev
     * @param type $nayttokork
     * @return string
     */
    public function nayta_pelkka_kuva($kuva_id, 
                                    $kuva_selitys, 
                                    $kuva_src, 
                                    $nayttolev, 
                                    $nayttokork){
        
        $kuva_html = 
                " <!-- KUVAKOODI ALKAA: ÄLÄ MUOKKAA!-->".
                    "<img id=".$kuva_id." title='".$kuva_selitys.
                        "' src=".$kuva_src." width='".$nayttolev."'".
                        "height='".$nayttokork.
                    "'/>".
                "<!-- KUVAKOODIN LOPPU-->";
                

        return $kuva_html;
    }

    /**
     * Palauttaa html-koodin, jossa kuvan piilotuspainike yms ylhäällä. Kuvan html
     * annetaan parametrina.
     * 
     * Jos poisto_ok-parametri on true, on kysymyksessä kuvan omistaja tai
     * admin, joille näytetään myös mahdollisuus kuvan poistamiseen.
     * 
     * @param type $kuva_html
     * @param type $id_kuva
     * @param type $id_hav
     * @param bool $poisto_ok 
     * @return string
     */
    public function nayta_kuvakehys_iso($kuva_html, $id_kuva, $id_hav, $poisto_ok){
        
        $painikkeet =     
                    Html::luo_button(Kuvatekstit::$painike_piilota_kuva_value, 
                        array(Maarite::onclick("piilota_kuva_ja_pikakommentit", 
                                        array()),
                                Maarite::classs("rinnakkain")));
                
        // Poistopainike vain valituille:
        if($poisto_ok){
            $painikkeet .= Html::luo_forminput_painike(
                        array(Maarite::action(
                                Maarite::muotoile_action_arvo(  
                                    "index.php", 
                                    array(Kuvakontrolleri::$name_id_kuva,
                                        Havaintokontrolleri::$name_id_hav),
                                    array($id_kuva, 
                                        $id_hav))),
                                Maarite::classs("rinnakkain")), 
                        array(Maarite::name(Toimintonimet::$kuvatoiminto),
                                Maarite::value(Kuvatekstit::
                                            $painike_poista_kuva_value),
                                ));
        }
        $html =  Html::luo_div( $painikkeet,
                    array(Maarite::id("kuvapainikerivi"),
                            Maarite::classs("keskitys")));
        
         // Varsinainen kuva:         
        $html .= Html::luo_div($kuva_html, 
                    array(Maarite::id("isokuva"),
                        Maarite::classs("keskitys")));
                
        return $html;
    }
    
    /**
     * Palauttaa kuvan html:n ilman mitään painikkeita. Kuvan koko säädetään
     * niin, ettei kuva mene ikkunan yli miltään puolelta.
     *
     * @param <type> $ikkunan_lev
     * @param <type> $ikkunan_kork
     * @return <type>
     */
    public function nayta_diaesityskuva($ikkunan_lev, $ikkunan_kork){
        


        $kuva_html =
            "<!-- KUVAKOODI ALKAA: ÄLÄ MUOKKAA!-->".
                    "<img id=".$this->id." title='".$this->kuvaselitys.
                        "' src=".$src." width='".$nayttolev."'".
                        "height='".$nayttokork.
                    "'/>".
            "<!-- KUVAKOODIN LOPPU-->";

        return $kuva_html;
    }

    

    /**
     *
     * @param Array $actionosoite action
     * @param Array $actionkyselynimet (action=actionosoite?nimi1=arvo1&nimi2=arvo2 jne)
     * @param Array $actionkyselyarvot
     * @param string $url_jatke urlin loppuun tuleva juttu, esim '#id_arvo'
     * @param <type> $omaid
     * @param <type> $uusi
     * @param <type> $ilmoitus_kuva
     * @param <type> $tietokantaolio
     * @param <type> $id_kuva
     * @param <type> $kuvaotsikko_kuva
     * @param <type> $kuvaselitys_kuva
     * @param <type> $vuosi_kuva
     * @param <type> $kk_kuva
     * @param <type> $paiva_kuva
     * @return <type> 
     */
    public static function nayta_kuvalomake(
                            $actionosoite,
                            $actionkyselynimet,
                            $actionkyselyarvot,
                            $url_jatke,
                            $omaid, $uusi, $ilmoitus_kuva,
                            $tietokantaolio,
                            &$kuvaotsikko_kuva, &$kuvaselitys_kuva,
                            &$vuosi_kuva, &$kk_kuva, &$paiva_kuva,
                            $kuvatoimintonimi
                                ){
        $mj = "";   // Lomakkeen html-koodi.
        //
        // Ilmoitus otetaan mukaan, jos ei tyhjä:
        if($ilmoitus_kuva != ""){
            $ilmoitus_kuva = "<span class='lomakeilmoitus'>"
                        .$ilmoitus_kuva."</span><br />";
        }

        if($uusi)
        {
            $submitnappi = "<input type='submit'
            name='$kuvatoimintonimi'".
            "value='".Kuva::$tallenna_uusi_kuva_value."'/>";
        }
        else // Päivitetään vanhaa suoritusta tai kommentoidaan:
        {
            $submitnappi = "<input type='submit'
            name='$kuvatoimintonimi'".
            "value='".Kuva::$tallenna_muokkaus_kuva_value."'/>";

            /*$perunappi = "<input type='button' name='toiminta'".
            "onClick = 'viestin_peruutus()' value = 'Poistu tallentamatta'/>";*/
        }

        $perunappi = "<input type='submit'
                    name='$kuvatoimintonimi'".
                    "value='".Kuva::$peruminen_kuva_value."'/>";

        if($uusi){
            $latauskoodi = "<div>".
                        "<span class='korostus'>".$ilmoitus_kuva."</span>".
                        "Kirjoita tai hae kuvaosoite:<br/>".
                        "<input type='hidden' name='MAX_FILE_SIZE' 
                            value=".Kuva::$MAX_FILE_SIZE." />".
                        "<input type='file' name='ladattu_kuva' size='80'/>".
                        "</div>";
        }
        else{
            $latauskoodi = "<span class='korostus'>".$ilmoitus_kuva."</span>";
        }

        /* Muotoillaan action-lauseke: id_kuva, id_lj, id_hav*/
        $vain_arvo = false; // Halutaan "action="-juttu alkuun.
        $actionkoodi = luo_action_koodi($actionosoite,
                                        $actionkyselynimet,
                                        $actionkyselyarvot,
                                        $vain_arvo,
                                        $url_jatke);


        $mj = // Kuvan hakuosa:
            "<form align='left' method='post' id='kuvalomake_kapea'".
                $actionkoodi.
                "enctype='multipart/form-data'/>".
                $latauskoodi.
            "<b>Kirjoita kuvan tiedot ja tallenna!</b><br/>".

            "<table summary='uudet_tiedot'>".

            // Otsikko- ja pvmkentät:
            "<tr>".
            "<td>Kuvaotsikko: </td><td><input type='text' size='70' maxlength='200'".
            "name='kuvaotsikko_kuva' value='$kuvaotsikko_kuva' /></td></tr>".

            "<tr><td></td>".
            "<td align='left'>
                <button id='b1' type='button' onclick='nayta_ed_vko()'
                title='Edellinen viikko'>
                &lt;&lt;
                </button>

                <button id='b2'type='button' onclick='nayta_ed()'
                title='Edellinen p&auml;iv&auml;'>
                &lt;
                </button>

                <button id='b5' type='button' onclick='nayta_nyk_pvm()'
                title='N&auml;ytt&auml;&auml; nykyisen".
                " p&auml;iv&auml;m&auml;&auml;r&auml;n'>
                T&auml;m&auml; p&auml;iv&auml;
                </button>

                <button id='b6' type='button' onclick='tyhjenna_pvm()'
                title='Tyhjent&auml;&auml; p&auml;iv&auml;m&auml;&auml;r&auml;n'>
                Tyhjenn&auml;
                </button>

                <button id='b3'type='button' onclick='nayta_seur()'
                title='Seuraava p&auml;iv&auml;'>
                &gt;
                </button>

                <button id='b4' type='button' onclick='nayta_seur_vko()'
                title='Seuraava viikko'>
                &gt;&gt;
                </button> ".
                " <span id='pvm_naytto'></span>
            </td>".
            "</tr>".
            "<tr><td></td><td>
            Vuosi (xxxx): <input id='vuosi' type='text'".
            "size='4' maxlength='4'".
            "name='vuosi_kuva' value='$vuosi_kuva' title='Vuosi, jolloin".
            "kuva on otettu (voi arvioida tai j&auml;tt&auml;&auml; tyhj&auml;ksi)'".
            "onchange='nayta_pvm()' onkeyup='nayta_pvm()'/>

            Kk (1-12): <input id='kk' type='text' size='2' maxlength='2'".
            "name='kk_kuva' value='$kk_kuva' title='Kuukausi, jolloin".
            "kuva on otettu (voi j&auml;tt&auml;&auml; tyhj&auml;ksi)'".
            "onchange='nayta_pvm()' onkeyup='nayta_pvm()'/>

            P&auml;iv&auml; (1-31): <input id='paiva' type='text' size='2' maxlength='2'".
            "name='paiva_kuva' value='$paiva_kuva' title='P&auml;iv&auml;, jolloin".
            "kuva on otettu (voi j&auml;tt&auml;&auml; tyhj&auml;ksi)'".
            "onchange='nayta_pvm()' onkeyup='nayta_pvm()'/>
            </td></tr>".

            // Kuvauskenttä:
            "<tr><td>Kuvaselitys:</td>".
            "<td colspan='2'><textarea cols='55' rows='6' maxlength = '1000'
            name='kuvaselitys_kuva'>$kuvaselitys_kuva".
            "</textarea></td></tr>".

            // Toimintopainikkeet:
            "<tr><td></td><td align='left'>".$submitnappi.
            $perunappi."</td><td></td></tr>".
            "</table>".
            "</form>";

        return $mj;
    }

    
    /**
     *
     * @param \Parametrit $parametriolio
     * @return string Palauttaa html-koodin.
     */
    public static function nayta_kuvalomake_ilman_formia(&$parametriolio){
        
        $omaid = $parametriolio->get_omaid(); 
        $uusi = $parametriolio->uusi_kuva; 
        $ilmoitus_kuva = $parametriolio->ilmoitus_kuva;
        $tietokantaolio = $parametriolio->get_tietokantaolio();
        $kuvaotsikko_kuva = $parametriolio->kuvaotsikko_kuva; 
        $kuvaselitys_kuva = $parametriolio->kuvaselitys_kuva;
        $vuosi_kuva = $parametriolio->vuosi_kuva; 
        $kk_kuva = $parametriolio->kk_kuva; 
        $paiva_kuva = $parametriolio->paiva_kuva;
        $kuvatoimintonimi = Bongaustoimintonimet::$kuvatoiminto;
        
        $mj = "";   // Lomakkeen html-koodi.
        
        // Ilmoitus otetaan mukaan, jos ei tyhjä:
        if($ilmoitus_kuva != ""){
            $ilmoitus_kuva = "<span class='lomakeilmoitus'>"
                        .$ilmoitus_kuva."</span><br />";
        }

        if($uusi)
        {
            $submitnappi = "<input type='submit'
            name='$kuvatoimintonimi'".
            "value='".Kuvatekstit::$painike_tallenna_uusi_kuva_value."'/>";
        }
        else // Päivitetään vanhaa suoritusta tai kommentoidaan:
        {
            $submitnappi = "<input type='submit'
            name='$kuvatoimintonimi'".
            "value='".Kuvatekstit::$painike_tallenna_muokkaus_kuva_value."'/>";
        }

        $perunappi = "<input type='submit'
                    name='$kuvatoimintonimi'".
                    "value='".Kuvatekstit::$painike_peruminen_kuva_value."'/>";

        if($uusi){
            $latauskoodi = 
                Html::luo_div(
                    Html::luo_span(
                        $ilmoitus_kuva, 
                        array(Maarite::classs("korostus"))). // span
                    
                    // Piilokenttä maximikokoa varten.
                    Html::luo_input( 
                        array(Maarite::type("hidden"),
                            Maarite::name("MAX_FILE_SIZE"),
                            Maarite::value(Kuva::$MAX_FILE_SIZE))).
                        
                    // Varsinainen tiedoston hakupainike:
                    Html::luo_input( 
                        array(Maarite::type("file"),
                            Maarite::name(Kuvakontrolleri::$name_ladattu_kuva),
                            Maarite::size(80))),
                        
                    array());   // div
                    
        }
        else{
            $latauskoodi = "<span class='korostus'>".$ilmoitus_kuva."</span>";
        }

        $mj = 
            
            $latauskoodi.
            
            
            
            "<b>Kirjoita kuvan tiedot ja tallenna!</b><br/>".

            "<table summary='uudet_tiedot'>".

            // Otsikko- ja pvmkentät:
            "<tr>".
            "<td>Kuvaotsikko: </td><td><input type='text' size='70' maxlength='200'".
            "name=".Kuvakontrolleri::$name_otsikko_kuva." value='$kuvaotsikko_kuva' /></td></tr>".

            "<tr><td></td>".
            "<td align='left'>
                <button id='b1' type='button' onclick='nayta_ed_vko()'
                title='Edellinen viikko'>
                &lt;&lt;
                </button>

                <button id='b2'type='button' onclick='nayta_ed()'
                title='Edellinen p&auml;iv&auml;'>
                &lt;
                </button>

                <button id='b5' type='button' onclick='nayta_nyk_pvm()'
                title='N&auml;ytt&auml;&auml; nykyisen".
                " p&auml;iv&auml;m&auml;&auml;r&auml;n'>
                T&auml;m&auml; p&auml;iv&auml;
                </button>

                <button id='b6' type='button' onclick='tyhjenna_pvm()'
                title='Tyhjent&auml;&auml; p&auml;iv&auml;m&auml;&auml;r&auml;n'>
                Tyhjenn&auml;
                </button>

                <button id='b3'type='button' onclick='nayta_seur()'
                title='Seuraava p&auml;iv&auml;'>
                &gt;
                </button>

                <button id='b4' type='button' onclick='nayta_seur_vko()'
                title='Seuraava viikko'>
                &gt;&gt;
                </button> ".
                " <span id='pvm_naytto'></span>
            </td>".
            "</tr>".
            "<tr><td></td><td>
            Vuosi (xxxx): <input id='vuosi' type='text'".
            "size='4' maxlength='4'".
            "name=".Kuvakontrolleri::$name_vuosi_kuva." value='$vuosi_kuva' title='Vuosi, jolloin".
            "kuva on otettu (voi arvioida tai j&auml;tt&auml;&auml; tyhj&auml;ksi)'".
            "onchange='nayta_pvm()' onkeyup='nayta_pvm()'/>

            Kk (1-12): <input id='kk' type='text' size='2' maxlength='2'".
            "name=".Kuvakontrolleri::$name_kk_kuva." value='$kk_kuva' title='Kuukausi, jolloin".
            "kuva on otettu (voi j&auml;tt&auml;&auml; tyhj&auml;ksi)'".
            "onchange='nayta_pvm()' onkeyup='nayta_pvm()'/>

            P&auml;iv&auml; (1-31): <input id='paiva' type='text' size='2' maxlength='2'".
            "name=".Kuvakontrolleri::$name_paiva_kuva." value='$paiva_kuva' title='P&auml;iv&auml;, jolloin".
            "kuva on otettu (voi j&auml;tt&auml;&auml; tyhj&auml;ksi)'".
            "onchange='nayta_pvm()' onkeyup='nayta_pvm()'/>
            </td></tr>".

            // Kuvauskenttä:
            "<tr><td>Kuvaselitys:</td>".
            "<td colspan='2'><textarea cols='55' rows='6' maxlength = '1000'
            name=".Kuvakontrolleri::$name_selitys_kuva.">$kuvaselitys_kuva".
            "</textarea></td></tr>".

            // Toimintopainikkeet:
            "<tr><td></td><td align='left'>".$submitnappi.
            $perunappi."</td><td></td></tr>".
            "</table>";

        return $mj;
    }

    /**
     * Antaa mahdollisuuden perua poistokäsky ja käskee miettimään vielä.
     * 
     * Antaa lisäksi mahdollisuuden poistaa pelkän havaintokuvalinkin, jos parametrina
     * annetaan oikea Havainto-luokan olio.
     * 
     * @param type $kuva Kuva-luokan olio, jonka poistamista harkitaan.
     * @param type $havainto Havainto-luokan olion id, johon kuva kenties liittyy.
     * @return type
     */
    function nayta_poistovarmistus_kuva($kuva, $havainto){

        $id_kuva = Kuva::$MUUTTUJAA_EI_MAARITELTY;
        $id_hav = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        
        $html = Kuvatekstit::$ilm_kuva_poistettavaa_ei_loytynyt;
        
        // Tämä ei ole pakollinen, mutta vaikuttaa poistovarmistuksen tyyliin:
        if($havainto instanceof Havainto){
            $id_hav = $havainto->get_id();
        }
        
        // Tämän pitää olla kunnossa, jotta kannattaa jatkaa:
        if($kuva instanceof Kuva){
            $id_kuva = $kuva->get_id();
            
            $maar_array_form = 
                array(Maarite::action(
                                Maarite::muotoile_action_arvo(  
                                        "index.php", 
                                        array(Kuvakontrolleri::$name_id_kuva,
                                            Havaintokontrolleri::$name_id_hav),
                                        array($id_kuva,
                                            $id_hav))));

            // Linkin poisto vain, jos havainto on määritelty:
            if($havainto instanceof Havainto){
                $maar_array_input_link = 
                    array(Maarite::name(Toimintonimet::$kuvatoiminto),
                        Maarite::value(
                            Kuvatekstit::$painike_poistovahvistus_kuvalinkki_value),
                        Maarite::title(
                            Kuvatekstit::$painike_poistovahvistus_kuvalinkki_title));

                $vahvistusnappi_linkkivaa = 
                        Html::luo_forminput_painike(
                                $maar_array_form, 
                                $maar_array_input_link);
            } else{
                $vahvistusnappi_linkkivaa = "";
            }
            

            // Totaatituho:
            $maar_array_input_totaali = 
                array(Maarite::name(Toimintonimet::$kuvatoiminto),
                    Maarite::value(Kuvatekstit::$painike_poistovahvistus_kuva_value),
                    Maarite::title(Kuvatekstit::$painike_poistovahvistus_kuva_title));

            $vahvistusnappi_totaali = 
                    Html::luo_forminput_painike(
                            $maar_array_form, 
                            $maar_array_input_totaali);

            // Perumisnappi:             
            $maar_array_input_peru = array(Maarite::name(Toimintonimet::$kuvatoiminto),
                Maarite::value(Kuvatekstit::$painike_peru_poisto_kuva_value));

            $perumisnappi = 
                    Html::luo_forminput_painike(
                            $maar_array_form, 
                            $maar_array_input_peru);

            //================================================================
            $napit = Html::luo_div($vahvistusnappi_linkkivaa.
                                    $vahvistusnappi_totaali.
                                    $perumisnappi,
                        array(Maarite::id("nappirivi"),
                                Maarite::classs("keskitys")));

            // Haetaan vielä kuva nähtäväksi:
            $tiedostokansio = Kuvakontrolleri::$kuvakansion_osoite;
            $tiedostonimi = $kuva->get_arvo(Kuva::$SARAKENIMI_TIEDOSTONIMI);
            $nayttomitta = Kuva::$KUVATALLENNUS_PIENI5_MITTA;
            $src = $kuva->get_arvo(Kuva::$SARAKENIMI_SRC);
            
            // $src on suurimman kuvan osoite, joka ei aina ole paras.
            $osoite = Kuvakontrolleri::hae_sopivan_kok_kuvan_tied_os(
                                                        $tiedostokansio, 
                                                        $tiedostonimi, 
                                                        $nayttomitta, 
                                                        $src);
            
            $kuvahtml = Html::luo_img(array(Maarite::src($osoite),
                                        Maarite::classs("keskitys")));

            
            $otsikko = Kuvatekstit::$kuvan_poistovarmistusteksti_1option;
            if($havainto instanceof Havainto){
                $otsikko = Kuvatekstit::$kuvan_poistovarmistusteksti_2options;
            }
            
            $html = Html::luo_div("<h3>".$otsikko."</h3>".
                                    $kuvahtml.
                                    $napit,    
                        array(Maarite::id("kuvan_poistovarmistus")));
        }
        return $html;
    }
    
    public function nayta($oliot) {
        
    }
}

?>
