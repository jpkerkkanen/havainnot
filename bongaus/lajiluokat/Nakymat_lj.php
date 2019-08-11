<?php
/**
 * Description of Nakyma_lj:
 * Huolehtii lajiluokkanäkymien ulkoasusta tai tarkemmin sanoen sisältää
 * enemmän tai vähemmän "tyhmät" html-rungot, joihin tiedot syötetään.
 *
 * @author J-P
 */
class Nakymat_lj {
    //put your code here

    public static $syottokentta_id = "syottokentta_lajiluokat";

    /**
     * Palauttaa lomakkeen, jonka avulla voidaan lajiluokan latinaa tai
     * vastaavaa nimeä/kuvausta muokata tai uusia syöttää.
     * 
     * Toimii AJAXin avulla, joten esim. painikkeet ovat pelkästään
     * buttoneita jne.
     * 
     * @param type $nimi
     * @param type $kuvaus
     * @param type $kieli_id 
     * @param type $olio_id Kyseisen lajiluokka- tai kuvausolion id. Jos kieli
     * on latina, on kyseessä lajiluokka, muuten kuvaus.
     * @param string $solu_id sen taulukkosolun id-määritteen arvo, jossa
     * muokattava nimi on tahi johon uusi nimi palautetaan.
     * @param int $id_lj sisältää aina lajiluokan id:n.
     */
    public static function nayta_nimi_kuvaus_lomake( 
                                                    $nimi, 
                                                    $kuvaus,
                                                    $kieli_id,
                                                    $olio_id,
                                                    $solu_id,
                                                    $id_lj
                                                    ){
    $tallennusnappi = "";
    $poistunappi = "";

    // Kyseessä on uusi, jos olio_id == -1
    if($olio_id == -1){
        $uusi = true;
    }
    else{
        $uusi = 0;
    }
    
    // Muotoillaan tallennusnappi:
    if($uusi)
    {
        $value = Bongauspainikkeet::$LAJILUOKAT_TALLENNA_UUSI_NIMIKUVAUS_VALUE;
        $olio_id = -1;  // Tätä ei luonnollisesti ole vielä.
    }
    else // Päivitetään vanhaa suoritusta:
    {
        $value = Bongauspainikkeet::$LAJILUOKAT_TALLENNA_MUUTOKSET_NIMIKUVAUS_VALUE;
    }
    
    $class = "rinnakkain";
    /*$onclick = "tallenna_nimikuvaus(".$kieli_id.",".$olio_id.",\"".$solu_id."\",".$id_lj.",\"".
                            Bongausasetuksia::$nimikuvauslomake_nimikentan_id."\",\"".
                            Bongausasetuksia::$nimikuvauslomake_kuvauskentan_id."\")";*/
    
    $tallennusnappi =
                Html::luo_button(
                        $value, 
                        array(Maarite::classs($class),
                            Maarite::onclick("tallenna_nimikuvaus", 
                                    array($kieli_id,
                                            $olio_id,
                                            $solu_id,
                                            $id_lj,
                                            Bongausasetuksia::$nimikuvauslomake_nimikentan_id,
                                            Bongausasetuksia::$nimikuvauslomake_kuvauskentan_id))));
    
               

    
    // Sitten poistumisnappi:
    $class = "rinnakkain";
    $title = Bongauspainikkeet::$LAJILUOKAT_SULJE_NIMIKUVAUSNAKYMA_TITLE;
    $value = Bongauspainikkeet::$LAJILUOKAT_SULJE_NIMIKUVAUSNAKYMA_VALUE;
    
    $poistunappi =
                Html::luo_button(
                        $value, 
                        array(Maarite::classs($class),
                            Maarite::onclick("sulje_ruutu2", 
                                    array("nimikuvauslaatikko"))));

    // Pienet erot lomakkeeseen riippuen siitä, onko
    // kieli latina (tällöin kuvausta ei tehdä).
    if($kieli_id == Kielet::$LATINA){
        $kuvausrivi = "";
    }
    else{
        $kuvausrivi = 
        "<tr><td align='left'>".Bongaustekstit::$nimikuvauslomake_kuvaus." (".
                Kielet::hae_kielen_nimi($kieli_id)."):</td>
        <td align='left'><textarea cols='50' rows='6' id='".
            Bongausasetuksia::$nimikuvauslomake_kuvauskentan_id."' ".
            "value='$kuvaus'/>".$kuvaus.
        "</textarea></td></tr>";
    }
    
    
    $mj =
        "<table summary='uudet_tiedot' id=".
            Bongausasetuksia::$lajiluokkalomakkeen_id.">".
        "<tr><td colspan=2><b>".
            Bongaustekstit::$nimikuvauslomake_ohje."</b></td></tr>".

        "<tr>".
        "<td align='left'>*".Bongaustekstit::$nimikuvauslomake_nimi." (".
            Kielet::hae_kielen_nimi($kieli_id)."):</td>
        <td align='left'>
        <input type='text' id='".
            Bongausasetuksia::$nimikuvauslomake_nimikentan_id."' ".
            "value='".$nimi."'/></td>
        </tr>".
            
        $kuvausrivi.

        "<tr><td></td><td align='left'>".$tallennusnappi.$poistunappi."</td></tr>".
        "</table>";

    // Palautetaan lomake.
    return $mj;
        
    }
    
    /**
     * Näyttää joukon lajiluokkia ja niiden nimet eri kielillä.
     * @param array $lajiluokat Sisältää kaikki lajiluokat olioina
     * @param <type> $painikkeet
     * @param <type> $on_admin TRUE, jos käyttäjällä admin-oikeudet
     * @return <type>
     */
    public static function nayta_lajiluokat($lajiluokat, $painikkeet, $on_admin){
        
        // Haetaan kielten nimet ja arvot:
        $kielinimet = Kielet::hae_kielten_nimet();
        $kieliarvot = Kielet::hae_kielten_arvot();
        $kielet = Kielet::hae_kielet();
        
        $sarakkeiden_lkm = sizeof($kieliarvot)+1;
        
        $html = "<div class=''>";
        $html = "<div class=''>".$painikkeet."</div>";
        $html .= "<table class='tietotaulu'>";
        $html .= "<tr><td colspan=".$sarakkeiden_lkm.
                    "class='tietotauluotsikko'>".
                    Bongaustekstit::$lajiluokka_lajiluokkataulun_otsikko.
                "</td></tr>";
        
        $html .= "<tr>";
        
        foreach ($kielinimet as $kieli) {
            $html .= "<th>".$kieli."</th>";
        }
        $html .= "<th>".Bongaustekstit::$lajiluokka_toimintapainikkeet."</th>";
          
        $html .= "</tr>";
        
        // Haetaan sitten kuvaukset:
        $laskuri = 0;
        
        foreach ($lajiluokat as $lajiluokka) {
            if($lajiluokka instanceof Lajiluokka){
                
                // Pariton rivi eri värillä:
                if($laskuri % 2 == 0){
                    $html .= "<tr class='tietotaulu_parillinen_rivi'>";
                }
                else{
                    $html .= "<tr>";
                }
                
                // Painikkeet havaintojen siirtoon ja lajiluokan poistoon vain,
                // jos järkevää, eli kun havaintoja/kuvia on olemassa tai
                // poistossa ei ole :)
                $poistonappi = "";
                $siirtonappi = "";
                
                if($on_admin){
                    // Poistonappi:
                    if(!$lajiluokka->lajiin_kohdistuu_havaintoja() &&
                        !$lajiluokka->lajiin_kohdistuu_kuvia() && 
                        !$lajiluokka->lajilla_on_aliluokkia()){

                        $id ="";
                        $class ="rinnakkain"; 
                        $value = Bongauspainikkeet::$LAJILUOKAT_POISTA_VALUE;
                        $title = Bongauspainikkeet::$LAJILUOKAT_POISTA_TITLE;
                        
                        $poistonappi = 
                            Html::luo_button($value, 
                                array(Maarite::id($id),
                                    Maarite::classs($class),
                                    Maarite::title($title),
                                    Maarite::onclick("poista_lajiluokka", 
                                        array($lajiluokka->get_id(),
                                            Bongaustekstit::
                                            $lajiluokan_poisto_varmistuskysymys,
                                            Bongaustekstit::
                                            $lajiluokan_poisto_perumisviesti))));
                    }

                    // Siirtonappi:
                    if($lajiluokka->lajiin_kohdistuu_havaintoja() ||
                        $lajiluokka->lajiin_kohdistuu_kuvia()){

                        $id ="";
                        $class ="rinnakkain"; 
                        $value = Bongauspainikkeet::$LAJILUOKAT_SIIRRA_HAVKUV_VALUE;
                        $title = Bongauspainikkeet::$LAJILUOKAT_SIIRRA_HAVKUV_TITLE;
                        $onclick = "hae_siirtolomake(".$lajiluokka->get_id().")";
                        
                        $siirtonappi = 
                            Html::luo_button($value, 
                                array(Maarite::id($id),
                                    Maarite::classs($class),
                                    Maarite::title($title),
                                    Maarite::onclick("hae_siirtolomake", 
                                        array($lajiluokka->get_id()))));
                    }
                }
                
                
                // Haetaan nimet muilla kielillä:
                $kuvaukset = $lajiluokka->hae_kuvaukset();
                
                // Kuvauksen sisältämä nimi pitää tulla oikeaan paikkaan
                // kielen mukaisesti. Ellei kuvausta löydy, jätetään tyhjä solu.
                $sarakkeiden_lkm = sizeof($kielet);
                $laskuri_vaaka = 0;
                
                // Käydään läpi kaikki yhden lajiluokan (rivin) kielet:
                foreach ($kielet as $kieli) {
                 
                    // Jokaisella solulla pitää olla oma erityinen id,
                    // jotta solu tunnistetaan:
                    $soluid = "nimisolu".$laskuri.$laskuri_vaaka;
                    $id_koodi = "id='".$soluid."'";
                    
                    // Soluihin lisätään onclick-määre, jotta niitä päästään
                    // muokkaamaan:
                    // 2. parametri -1 -> olemattoman olion id. 
                    $onklikki = "onclick = 'hae_nimikuvauslomake(".
                                    $kieli->get_id().",-1,\"".$soluid."\",".
                                    $lajiluokka->get_id().
                                    " )'";
                    
                    $kielinimi = $kieli->get_nimi();
                    
                    // Latina:
                    if($kieli->get_id()+0 === Kielet::$LATINA){
                        
                        // Latinassa olio on lajiluokka, jonka id tarvitaan
                        // jatkossa. Tämä on aina olemassa.
                        $onklikki = "onclick = 'hae_nimikuvauslomake(".
                                    $kieli->get_id().",".$lajiluokka->get_id().
                                    ",\"".$soluid."\",".
                                    $lajiluokka->get_id().
                                    " )'";
                        
                        $html .= "<td class='huomio'".$id_koodi.$onklikki." title='".
                                Bongauspainikkeet::$LAJILUOKAT_MUOKKAA_TITLE.
                                " (".$kielinimi.")'>".
                                $lajiluokka->get_nimi_latina_html_encoded()."</td>";
                    }
                    
                    // Muut kuin latina:
                    else if(!empty($kuvaukset)){
                        
                        // Valitaan kieltä vastaava kuvaus (jota ei välttämättä
                        // ole):
                        $osuma = "";
                        foreach ($kuvaukset as $kuvaus) {
                            if($kuvaus instanceof Kuvaus){
                                if($kuvaus->get_kieli()+0 === $kieli->get_id()){
                                    $osuma = $kuvaus;
                                }
                            }
                        }
                        
                        if($osuma instanceof Kuvaus){

                            // Suomea ei päästetä muokkaamaan kuin admin:
                            if($kieli->get_id()+0 === Kielet::$SUOMI){
                                if($on_admin){
                                    $onklikki = "onclick = 'hae_nimikuvauslomake(".
                                    $osuma->get_kieli().",".
                                    $osuma->get_id().",\"".$soluid."\",".
                                    $lajiluokka->get_id().
                                    " )'";
                                }
                                else{
                                    $onklikki = 
                                    "onclick = 'nayta_viesti(\"".
                                    Bongaustekstit::$lajiluokan_muok_ei_voi_suomenkiel."\")'";
                                }
                            }
                            else{
                                $onklikki = "onclick = 'hae_nimikuvauslomake(".
                                    $osuma->get_kieli().",".
                                    $osuma->get_id().",\"".$soluid."\",".
                                    $lajiluokka->get_id().
                                    " )'";
                            }
                            
                            
                            $html .= "<td class='huomio' ".$id_koodi.$onklikki." title='".
                                Bongauspainikkeet::$LAJILUOKAT_MUOKKAA_TITLE.
                                " (".$kielinimi.")'>".$osuma->get_nimi_html_encoded()."</td>";

                        }
                        else{
                            $html .= "<td class='huomio' ".$id_koodi.$onklikki." title='".
                                Bongauspainikkeet::$LAJILUOKAT_SYOTA_UUSI_TITLE.
                                " (".$kielinimi.")'></td>";
                        }
                    }
                    else{
                        $html .= "<td class='huomio' ".$id_koodi.$onklikki."  title='".
                                Bongauspainikkeet::$LAJILUOKAT_SYOTA_UUSI_TITLE.
                                " (".$kielinimi.")'></td>";
                    }
                    $laskuri_vaaka++;
                }
                
                //Lisätään painikkeet:
                $html .= "<td>".$siirtonappi.$poistonappi."</td>";
                
                $html .= "</tr>";
                $laskuri++;
            }
        }
        $html .= "</table>";
        $html .= "</div>"; // Lajiluokkalaatikon loppu
        return $html;
    }

    /**
     * Palauttaa lajiluokan html-koodin. HUOM! Kuvaukset pitäisi kaiketi
     * yhdistää tänne, jotta saadaan lajinimet eri kielillä!
     *
     * @param type $omaid
     * @param type $kayttajan_valtuudet
     * @param type $lj Lajiluokkaolio, joka näytetään maailmalle
     * @return type 
     */
    public function nayta_lajiluokka($omaid, $kayttajan_valtuudet, $lj){

        $html="";

        // Jos on tallennuskelpoinen, on myös löytänyt tiedot
        // tietokannasta. $uusi=true, koska muuten valittaa, ellei
        // tietoja muutettu. Nyt id:tä ei tarkisteta, mutta ei sillä
        // väliä.
        if($this->on_tallennuskelpoinen(true)){
            $aika = anna_pvm_ja_aika($this->get_tallennushetki_sek());

            // true on muuttujan 'vain_etunimi' arvo.
            $lahettaja = hae_henkilon_nimi($this->get_henkilo_id(),
                                            true, $this->tietokantaolio);

            $sisalto = $this->get_kommentti();

            // Luodaan muokkaus/tuhouspainikkeet, jos kysymyksessä
            // oma tai kuningas:
            if(($omaid == $this->get_henkilo_id()) ||
                ($kayttajan_valtuudet== Valtuudet::$HALLINTA)){

                $painikkeet =
                "<button type='button' onclick=".
                  "'pk_muokkaa(\"".$this->get_kommentti()."\",".
                                    $this->get_kohde_tyyppi().",".
                                    $this->get_kohde_id().",".
                                    $this->get_id().")'".
                "title='".
                Lajiluokkatekstit::$muokkaa_lajiluokka_title."'>".
                Lajiluokkatekstit::$muokkaa_lajiluokka_value.
                "</button>".
                "<button type='button' onclick=".
                "'esita_kuvauksen_poistovarmistus(".$this->get_id().
                        ")'title='".
                Lajiluokkatekstit::$poista_lajiluokka_title."'>".
                Lajiluokkatekstit::$poista_lajiluokka_value.
                "</button>";
            }
            else{
                $painikkeet = "";
            }


            $html = "<div class='lajiluokka' id='pk".$id."'>";

            // Otsikko:
            $html .= "<div class='lajiluokka_otsikko'>";
            $html .= "<span class='lajiluokka_lahettaja'>".$lahettaja." </span>";
            $html .= "<span class='lajiluokka_aika'>".$aika."</span>";
            $html .= "</div>"; // Otsikon loppu

            // Sisältö:
            $html .= "<div class='lajiluokka_sisalto'>".
                    "<table><tr>".
                        "<td>".$sisalto."</td>".
                        "<td>".$painikkeet."</td>".
                    "</tr></table></div>";

            $html .= "</div>"; // Lajiluokan loppu
        }
        else{
             $html=Lajiluokkatekstit::$ilmoitus_pikakommentteja_ei_loytynyt;
        }
        return $html;
    }
    /**
     * Näyttää lajivalikon, josta kohdelaji valitaan.
     *
     * @param Parametrit $parametriolio
     * @param type $lajivalikko
     * @return string 
     */
    public static function nayta_havaintojen_ja_kuvien_siirtolomake(
                                                            $parametriolio,
                                                            $lajivalikko){

        $mj = "";
        $siirtonappi = "";
        $poistunappi = "";

        $value = Bongauspainikkeet::$LAJILUOKAT_SIIRRA_HAVKUV_VALUE;
        $title = Bongauspainikkeet::$LAJILUOKAT_SIIRRA_HAVKUV_TITLE;
        
        $siirtonappi = 
            Html::luo_button($value, 
                array(Maarite::title($title),
                    Maarite::onclick("siirra_kuvat_ja_havainnot", 
                        array($parametriolio->id_lj,
                            Bongausasetuksia::$havaintokuvasiirtolomakevalikko_id))));
        
        $value = Bongauspainikkeet::$LAJILUOKAT_SULJE_NAKYMA_VALUE;
        $title = Bongauspainikkeet::$LAJILUOKAT_SULJE_LOMAKENAKYMA_TITLE;
        $poistunappi = 
            Html::luo_button($value, 
                array(Maarite::title($title),
                    Maarite::onclick("sulje_ruutu2", 
                        array(Bongausasetuksia::$havaintokuvasiirtolaatikko_id))));

        $siirtolomakeohje = Bongaustekstit::$siirtolomakeohje;

        $mj = 
            "<form name='".Bongausasetuksia::$havaintokuvasiirtolomake_name."'>".
            "<table summary='uudet_tiedot' id=".
                Bongausasetuksia::$havaintolomakkeen_id.">".
            "<tr><td colspan=2><b>".
                $siirtolomakeohje."</b></td></tr>".
          
            "<tr>".
            // Alkuperäinen laji:
            "<td align='left'>".Bongaustekstit::$laji_alkup.": </td>".
            "<td align='left'>".$parametriolio->nimi_kuv."</td>
            </tr>".
                
            "<tr>".
            // Lajivalinta 
            "<td align='left'>".Bongaustekstit::$laji_siirto.": </td><td align='left'
                id= ".Bongausasetuksia::$havaintolomake_lajivalintarivi_id.">
            <span id = ".Bongausasetuksia::$havaintolomake_lajivalikko_id.">".
            $lajivalikko."</span></td>
            </tr>".

            "<tr><td></td><td align='left'>".$siirtonappi.$poistunappi."</td></tr>".
            "</table></form>";

        // Palautetaan lomake.
        return $mj;

    }
    
    /**
     * Palauttaa poistovahvistus-html:n, joka sisältää kuvauksen tiedot
     * ja poiston vahvistus- ja perumispainikkeet.
     * 
     * KORJAA TÄÄ ON IHAN RIKKI!
     */
    public function nayta_poistovahvistuskysely(){

        // Tämä perumista varten (ENT_QUOTES->muuttaa sekä yksöis- että
        // kaksoislainausmerkit). Eka parametri varmistaa, että painikkeet
        // tulevat näkyviin perumisen jälkeenkin. Sikälihän poistamispainikekin
        // näkyy vain, jos käyttäjällä on oikeus poistoon.
        $sisalto_html = 
            htmlspecialchars($this->
                        nayta_lajiluokka(-1, Valtuudet::$NORMAALI),
                        ENT_QUOTES);

        $html= "";

        // Jos on tallennuskelpoinen, on myös löytänyt tiedot
        // tietokannasta. $uusi=true, koska muuten valittaa, ellei
        // tietoja muutettu. Nyt id:tä ei tarkisteta, mutta ei sillä
        // väliä. Idea on vain katsoa, ettei lajiluokka tyhjä.
        if($this->on_tallennuskelpoinen(true)){
            $aika = anna_pvm_ja_aika($this->get_tallennushetki_sek());

            // true on muuttujan 'vain_etunimi' arvo.
            $lahettaja = hae_henkilon_nimi($this->get_henkilo_id(),
                                            true, $this->tietokantaolio);

            $sisalto = $this->get_kommentti();

            $elem_id = "pk".$this->get_id();

            // Luodaan vahvistus- ja perumispainikkeet
            $painikkeet =
            "<button type='button' onclick=".
              "'pk_poista(".$this->get_id().",".$this->kohde_id.")'".
            "title='".
                Lajiluokkatekstit::$poistovahvistus_lajiluokka_title."'>".
                Lajiluokkatekstit::$poistovahvistus_lajiluokka_value.
            "</button>".
            "<button type='button' onclick=".
            "'peru_poisto(\"".$sisalto_html."\",\"".$elem_id."\")'title='".
                Lajiluokkatekstit::$peruminen_lajiluokka_title."'>".
                Lajiluokkatekstit::$peru_poisto_lajiluokka_value.
            "</button>";

            $html= Nakyma_lajiluokat::nayta_poistovahvistus(
                                    $aika,
                                    $lahettaja,
                                    $sisalto,
                                    $painikkeet,
                                    $this->get_id());
        }
        else{
            $html = Lajiluokkatekstit::$virheilmoitus_ei_tallennuskelpoinen;
        }
        return $html;
    }
    
    /**
     * @param <type> $aika
     * @param <type> $lahettaja
     * @param <type> $sisalto
     * @param <type> $painikkeet
     * @return string Palauttaa yhden lajiluokan Html-koodin.
     */
    public static function nayta_poistovahvistus($aika,
                                                $lahettaja,
                                                $sisalto,
                                                $painikkeet,
                                                $id){
        $html = "<div class='lajiluokka_poistovahvistus' id='pk".$id."'>";

        // Otsikko:
        $html .= "<div class='lajiluokka_otsikko'>";
        $html .= "<span class='lajiluokka_lahettaja'>".$lahettaja." </span>";
        $html .= "<span class='lajiluokka_aika'>".$aika."</span>";
        $html .= "</div>"; // Otsikon loppu

        // Sisältö:
        $html .= "<div class='lajiluokka_sisalto'>".
                "<table><tr>".
                    "<td>".$sisalto."</td>".
                    "<td>".$painikkeet."</td>".
                "</tr></table></div>";

        $html .= "</div>"; // Pikakommentin loppu
        return $html;
    }

/**
    * Palauttaa lajivalikon html-koodin. Pohjautuu tässä vaiheessa vain
    * kaksikerroksiseen lajiluokkahierarkkiaan.
     * @param type $oletus_id_lj
     * @param type $sisarlajiluokat
     * @param type $otsikko
     * @param type $kieli_id
     * @param type $name_arvo
     * @return string 
     */
    public static function nayta_lajivalikko(&$oletus_id_lj,
                                            $sisarlajiluokat,
                                            $otsikko,
                                            $kieli_id,
                                            $name_arvo){


        // Luodaan taulukot:
        $arvot = array();
        $nimet = array();

        // Viedään otsikot ja vastaavat arvot taulukoihin:
        if(!empty($sisarlajiluokat)){
            foreach ($sisarlajiluokat as $lajiluokka) {
                if($lajiluokka instanceof Lajiluokka){
                    $kuvaus = $lajiluokka->hae_kuvaus($kieli_id);
                    if($kuvaus instanceof Kuvaus){
                        array_push($arvot, $lajiluokka->get_id());
                        array_push($nimet, $kuvaus->get_nimi());
                    }
                }
            }
        }

        $valikkohtml = "";

        try{
            $id = Bongausasetuksia::$havaintokuvasiirtolomakevalikko_id;
            $class = "";
            $oletusvalinta_arvo = $oletus_id_lj;
            $valikkohtml.= Html::luo_pudotusvalikko3($arvot,
                                                    $nimet,
                                                    $name_arvo,
                                                    $id,
                                                    $class,
                                                    $oletusvalinta_arvo,
                                                    $otsikko);
        }
        catch(Exception $poikkeus){
            $valikkohtml =
                Bongaustekstit::$havaintolomake_virheilm_lajivalikko." (".
                            $poikkeus->getMessage().")";
        }
        return $valikkohtml;
    }
    
    /**
    * Näyttää lomakkeen, jonka avulla saadaan luotua uusia ja muokattua vanhoja
    * lajiluokkia.
    *
    * @param <type> $id_lj
    * @param <type> $ylaluokka_id_lj
    * @param <type> $nimi_latina_lj
    * @param <type> $lajiluokka_id_kuv
    * @param <type> $nimi_kuv
    * @param <type> $kuv_kuv
    * @param <type> $kieli_kuv
    * @return <type>
    */
   public static function nayta_lajiluokkalomake(&$ylaluokka_id_lj,
                                   &$nimi_latina_lj,
                                   &$nimi_kuv,
                                   &$kuv_kuv,
                                   &$kieli_kuv,
                                   $uusi,
                                   $tietokantaolio
                                   ){


       $submitnappi = "";
       $poistunappi = "";

       $class = "rinnakkain";
       $id = "";
       $action = "oletus";
       $name = Bongaustoimintonimet::$lajiluokkatoiminto;
       $value = Bongauspainikkeet::$PERUMINEN_LAJILUOKKA_VALUE;

       $poistunappi =
                   Html::luo_painikelomake($class, $id, $action, $name, $value);

       if($uusi)
       {
           $class = "rinnakkain";
           $id = "";
           $action = "oletus";
           $name = Bongaustoimintonimet::$lajiluokkatoiminto;
           $value = Bongauspainikkeet::$TALLENNA_UUSI_LAJILUOKKA_VALUE;
           $submitnappi =
                   Html::luo_painikelomake($class, $id, $action, $name, $value);
       }
       else // Päivitetään vanhaa suoritusta:
       {
           $class = "";
           $id = "";
           $action = "oletus";
           $name = Bongaustoimintonimet::$lajiluokkatoiminto;
           $value = Bongauspainikkeet::$TALLENNA_MUOKKAUS_LAJILUOKKA_VALUE;
           $submitnappi =
                   Html::luo_painikelomake($class, $id, $action, $name, $value);
       }

       // Valikot:
       /*$kielivalikko = nayta_kielivalikko($kieli_kuv,"");*/
       $kielivalikko = "suomi";

       // Jos yläluokkaa ei ola määritelty (=-1), etsitään sen luokan id, jonka
       // suomenkielisen kuvauksen nimi on "Linnut":
       if($ylaluokka_id_lj == -1){
           $taulunimi = Kuvaus::$taulunimi;
           $sarakenimi = Kuvaus::$SARAKENIMI_NIMI;
           $hakuarvo = "Linnut";
           $kuvausolio = $tietokantaolio->
                           hae_eka_osuma_oliona($taulunimi, $sarakenimi, $hakuarvo);

           if($kuvausolio != Bongausasetuksia::$tietokantahaku_ei_loytynyt){
               $ylaluokka_id_lj = $kuvausolio->lajiluokka_id;
           }
       }

       $otsikko = ""; // Otsikko laitetaan eri paikkaan.
       $js_metodinimi = "";
       $js_param_array = "";
       $nayta_tyhja = Bongaustekstit::$lajiluokkalomake_ei_ylatasoa;   //=true
       $ylaluokkavalikko = Lajiluokka::nayta_ylaluokkavalikko($nayta_tyhja,
                                                                $tietokantaolio,
                                                                $ylaluokka_id_lj,
                                                                $kieli_kuv,
                                                                $otsikko,
                                                                $js_metodinimi,
                                                                $js_param_array);

       $mj =
           "<form align='center' method='post' action='index.php' id=".
               Bongausasetuksia::$lajiluokkalomakkeen_id.">".
           "<table summary='uudet_tiedot'>".
           "<tr><td colspan=2><b>".
               Bongaustekstit::$lajiluokkalomake_ohje."</b></td></tr>".


           "<tr>".
           "<td align='left'>*".Bongaustekstit::$lajiluokkalomake_ylaluokka.":</td>
           <td align='left'> ".$ylaluokkavalikko." ".
           Bongaustekstit::$lajiluokkalomake_ylaluokkaohje."</td>
           </tr>".

           "<tr>".
           "<td align='left'>*".Bongaustekstit::$lajiluokkalomake_nimi_latina.":
           </td><td align='left'>
           <input type='text' name='nimi_latina_lj' value='".$nimi_latina_lj."'/></td>
           </tr>".

           "<tr>".
           "<td align='left'>*".Bongaustekstit::$lajiluokkalomake_nimi_omakieli.
           " (".$kielivalikko."):
           </td><td align='left'>
           <input type='text' name='nimi_kuv' value='$nimi_kuv'/>
           </td>
           </tr>".


           "<tr><td align='left'>".Bongaustekstit::$lajiluokkalomake_kuvaus.":</td>
           <td align='left'><textarea cols='50'
           rows='6'name='kuv_kuv'>$kuv_kuv".
           "</textarea></td></tr>".


           "<tr><td></td><td align='left'>".$submitnappi.$poistunappi."</td></tr>".
           "</table>".
           "</form>";

       // Palautetaan lomake.
       return $mj;
   }
}
?>