<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



class Html{

    /************************* LUO SUBMIT-PAINIKE *****************************/
    /**
     * DEPRECATED. Luo lomakkeen eli form-elementin, joka sisältää ainoastaan yhden
     * painikkeen. Lomakkeen id- ja actionarvot sekä painikkeen name- ja
     * value-arvot annetaan parametreina (tässä järjestyksessä).
     * METHOD-arvo on kiinteästi 'POST' ja painikkeen TYPE='SUBMIT'.
     *
     * Huom! Jos $action-parametrilla on arvo "oletus" tai "default",
     * annetaan sille automaattisesti arvo '{$_SERVER['PHP_SELF']}'!
     */
    public static function luo_painikelomake($class, $id, $action, $name, $value)
    {
        // Oletusaktion:
        if($action == "oletus" || $action == "default"){
            $action = "{$_SERVER['PHP_SELF']}";
        }

        $nappimerkkaus = "";
        $nappimerkkaus = "<form method='post'".
                        "id='".$id."' ".
                        "class='".$class."' ".
                        "action='".$action."'>".
                            "<input type='submit'".
                                "name='".$name."' ".
                                "value='".$value."'/>".
                        "</form>";
        return $nappimerkkaus;
    }

    /************************* LUO INPUT-ELEMENTTI *****************************/
    /**
     * DEPRECATED.
     * Luo painikkeen ilman form-elementtiä. Painikkeen type, id, name-
     * value- ja onclick -arvot annetaan parametreina (tässä järjestyksessä).
     *
     * Tästä saa kätevästi myös tavan button-elementin antamalla type=button.
     * Value ja name ovat pakollisia, jos type=submit!
     *
     * OLETUKSET:
     *  Jos type != "button" -> type=submit;
     *
     * @param <type> $type
     * @param <type> $class
     * @param <type> $id
     * @param <type> $name
     * @param <type> $value
     * @param <type> $onclick
     * @return <type>
     */
    public static function luo_painike_ilman_formia($type,
                                                    $class,
                                                    $id,
                                                    $name,
                                                    $value,
                                                    $onclick)
    {
        if($type != "button"){
            $type = "submit";
        }

        if(empty($onclick)){
            $onklikki = "";
        }
        else{
            $onklikki = "onclick='".$onclick."'";
        }

        if(($type="submit") && (($name == "") || ($value == ""))){
            $nappimerkkaus = "Virhe! Name- tai value-arvo tyhj&auml;!";
        }
        else{
            $nappimerkkaus = "";
            $nappimerkkaus = "<input type='$type'".
                                "id='".$id."' ".
                                "class='".$class."' ".
                                "name='".$name."' ".
                                "value='".$value."' ".
                                $onklikki."/>";
        }
        
        return $nappimerkkaus;
    }
    
    

    /******************* luo_submit_painike_onsubmit_toiminnolla **************/
    /**
     * DEPRECATED.
     * Luo submit-painikkeen, jossa mukana onsubmit-metodi.
     * Tällä voidaan kätevästi toteuttaa ajax-toiminnot niin, että jos js on
     * poissa päältä, onsubmit palauttaa arvon true, jolloin lomake lähetetään
     * normaalisti ilman javascript-toimintoja.
     *
     * HUOM! Palauttaa aina lomakkeen (form), jonka sisällä painike on.
     *
     * @param string $onsubmit_funktionimi Ennen lomakkeen lähettämistä
     * kutsuttavan javascript-metodin nimi merkkijonona. Tämä metodi palauttaa
     * totuusarvon, jonka perusteella lomake joko lähetetään tai ei lähetetä.
     * @param array $onsubmit_parametrit taulukko js-metodin parametrien arvoista
     * oikeassa järjestyksessä
     * @param array $form_maaritteet Taulukko form-elementin määritteistä.
     * Jokaisella määritteellä on html:n mukainen nimi. Taulukko voi olla tyhjä.
     * Luonti seuraavaan tyyliin:
     *
     * $form_maaritteet['value']='piip'; $form_maaritteet['id']=30 jne.
     *
     * TAI:
     *
     * $form_maaritteet = array("method"=>"post",
     *                          "id"=>30,
     *                          "action"="{$_SERVER['PHP_SELF']}?id_alb=20");
     *
     * @param array $input_maaritteet Taulukko input-elementin määritteistä.
     * Jokaisella määritteellä on html:n mukainen nimi.
     * Valuen pitää olla määritelty, muut saavat vaikka puuttua. Luonti kuten
     * alla. HUOM! type='submit' on jo valmiina, eli sitä ei tarvitse lisätä!
     *
     * $input_maaritteet = array('name'=>'toiminta',
     *                          'value'=>'Paina!');
     *
     * Jos parametreissa havaitaan puutteita, heitetään poikkeus.
     */
    public static function luo_submit_painike_onsubmit_toiminnolla(
                                                    $onsubmit_funktionimi,
                                                    $onsubmit_parametrit,
                                                    $form_maaritteet,
                                                    $input_maaritteet){

        $painikekoodi = "";

        // Testataan parametrejä. Jos huonot, heitetään poikkeus:
        if(!is_array($form_maaritteet) || !is_array($input_maaritteet)){
            throw new Exception("Vikaa parametreissa
                                    (luo_submit_painike_onsubmit_toiminnolla)");
        }
        else if(!isset($input_maaritteet['value'])){
            throw new Exception("Value-määrite ei määritelty!
                                    (luo_submit_painike_onsubmit_toiminnolla)");
        }
        else{
            // Haetaan ja muotoillaan onsubmit_parametrit:
            $parametrikoodi = "";
            $laskuri = 0; // Tämän avulla saadaan pilkut kohdalleen.

            /* Jos parametri on taulukko, edetään. Muuten parametrikoodi on "". */
            if(is_array($onsubmit_parametrit)){
                foreach ($onsubmit_parametrit as $parametri){
                    $laskuri++;
                    if($laskuri == 1){
                        $parametrikoodi = $parametri;
                    }
                    else{
                        $parametrikoodi .= ",".$parametri;
                    }
                }
            }
            
            // Sitten muotoillaan lomakkeen form- ja inputmääritteet:
            $form_maaritekoodi = "";
            $avaimet = array_keys($form_maaritteet);
            foreach($avaimet as $avain){
                $form_maaritekoodi .= " ".$avain."='".$form_maaritteet[$avain]."'";
            }

            $input_maaritekoodi = "";
            $avaimet = array_keys($input_maaritteet);
            foreach($avaimet as $avain){
                $input_maaritekoodi .= " ".$avain."='".$input_maaritteet[$avain]."'";
            }

            // Painikkeen onsubmit-tarkistus: jos kutsuttava metodi palauttaa
            // arvonaan true, lomake lähetetään (submit). Muuten lomaketta ei lähetetä.
            $on_submit_koodi =
                "onsubmit='return ".$onsubmit_funktionimi."(".$parametrikoodi.")'";

            $painikekoodi ="<form ".$form_maaritekoodi.$on_submit_koodi.">".
                            "<input type='submit'".$input_maaritekoodi."/>".
                            "</form>";
        }
        return $painikekoodi;
    }

    /**
     * Palauttaa pudotusvalikon html-koodin. Asettaa valituksi parametrina annetun
     * arvon (tai sitä vastaavan nimen).
     * Arvoja ja nimiä tulee olla yhtä monta elementtiä sisältäviä taulukoita
     * (array). Ellei näin ole, palautetaan virheviesti.
     *
     * Pudotusvalikon otsikko määritellään viimeisellä parametrilla ($otsikko).
     *
     * @param array $arvot option value -arvot
     * @param array $nimet option-tagien väliin asetettava määre, eli tämä näkyy
     * käyttäjälle pudotusvalikon vaihtoehtona.
     * @param string $name_arvo select name -määreen arvo (nimi), jonka avulla
     * valittu arvo välittyy eteenpäin.
     * @param int $oletusvalinta_arvo (oletus)vaihtoehtoa vastaava arvo. Jos tämä
     * on tyhjä, muuten epäkelpo tai alle 0, näytetään ensimmäinen vaihtoehto.
     * @param string $otsikko Pudotusvalikon eteen kirjoitettava otsikko/kuvaus.
     * @return string Palauttaa pudotusvalikon html-koodin.
     */
    public static function luo_pudotusvalikko($arvot,
                                $nimet,
                                $name_arvo,
                                $oletusvalinta_arvo,
                                $otsikko){

        if(empty ($otsikko)){
            $valikkohtml = "<select name = '$name_arvo'>";
        }
        else{
            $valikkohtml = $otsikko.": <select name = '$name_arvo'>";
        }

        if(is_array($arvot) && is_array($nimet) &&
            sizeof($arvot) == sizeof($nimet) && !empty ($arvot)){

            // Varmistetaan oletusvalinnan kelpoisuus:
            if(!isset($oletusvalinta_arvo) ||
                !is_numeric($oletusvalinta_arvo) ||
                $oletusvalinta_arvo < 0){
                $oletusvalinta_arvo = $arvot[0];
            }

            for ($i = 0; $i < sizeof($arvot); $i++) {
                if($oletusvalinta_arvo == $arvot[$i]){
                    $valikkohtml .= "<option value = '$arvot[$i]'".
                                    "selected = 'selected'>";
                }
                else{
                    $valikkohtml .= "<option value = '$arvot[$i]'>";
                }

                $valikkohtml .= $nimet[$i];
                $valikkohtml .= "</option>";
            }
            $valikkohtml .= "</select>";
        }

        else if(empty ($arvot)){
            $valikkohtml .= "</select>";
        }

        // Ellei parametritaulukot täytä ehtoja:
        else{
            $valikkohtml = "Virhe parametreissa!
                            (php_yleismetodit.luo_pudotusvalikko)";
        }

        return $valikkohtml;
    }

    /**
     * Sama kuin luo_pudotusvalikko, mutta lisää parametrina annettavan
     * onChange-metodin ja sen parametrit elementin määritteisiin.
     * HUOM! Valittu arvo saadaan parametrilla THIS.VALUE!
     * Palauttaa pudotusvalikon html-koodin. Asettaa valituksi parametrina annetun
     * arvon (tai sitä vastaavan nimen).
     * Arvoja ja nimiä tulee olla yhtä monta elementtiä sisältäviä taulukoita
     * (array). Ellei näin ole, palautetaan virheviesti.
     *
     * Pudotusvalikon otsikko määritellään viimeisellä parametrilla ($otsikko).
     *
     * @param array $arvot option value -arvot
     * @param array $nimet option-tagien väliin asetettava määre, eli tämä näkyy
     * käyttäjälle pudotusvalikon vaihtoehtona.
     * @param string $name_arvo select name -määreen arvo (nimi), jonka avulla
     * valittu arvo välittyy eteenpäin.
     * @param int $oletusvalinta_arvo (oletus)vaihtoehtoa vastaava arvo. Jos tämä
     * on tyhjä tai null tai epäkelpo, näytetään ensimmäinen vaihtoehto.
     * @param string $otsikko Pudotusvalikon eteen kirjoitettava otsikko/kuvaus.
     * @param <type> $onchange_metodinimi js-metodin nimi Voi jättää tyhjäksi.
     * @param array $onchange_metodiparametrit_array js-metodin parametrit.
     * @return string Palauttaa pudotusvalikon html-koodin.
     */
    public static function luo_pudotusvalikko_onChange($arvot,
                                $nimet,
                                $name_arvo,
                                $id_arvo,
                                $class_arvo,
                                $oletusvalinta_arvo,
                                $otsikko,
                                $onchange_metodinimi,
                                $onchange_metodiparametrit_array){

        // JS-metodin parametrien muotoilu:
        $param = "";
        if(is_array($onchange_metodiparametrit_array)){
            $laskuri = 0;
            foreach($onchange_metodiparametrit_array as $parametri){
                if($laskuri == 0){
                    $param .= $parametri;
                }
                else{
                    $param .= ",".$parametri;
                }
                $laskuri++;
            }
        }

        // OnChange-jutun muotoilu:
        if($onchange_metodinimi == ""){
            $onchange_html = "";
        }
        else{
            $onchange_html= "onChange='$onchange_metodinimi($param)'";
        }

        if(!empty ($otsikko)){
            $otsikko = $otsikko.": ";

        }

        $valikkohtml = $otsikko."<select name = '".$name_arvo."' ".
                                    " id = '".$id_arvo."' ".
                                    " class = '".$class_arvo."' ".
                                    $onchange_html.">";
        
        if(is_array($arvot) && is_array($nimet) &&
            sizeof($arvot) == sizeof($nimet) && !empty($arvot)){

            // Varmistetaan oletusvalinnan kelpoisuus. HUOM! Ei voida olettaa,
            // että arvo olisi luku. Voi olla myös esim. merkkijono.
            if(!isset($oletusvalinta_arvo)){
                $oletusvalinta_arvo = $arvot[0];
            }

            for ($i = 0; $i < sizeof($arvot); $i++) {
                if($oletusvalinta_arvo == $arvot[$i]){
                    $valikkohtml .= "<option value = '$arvot[$i]'".
                                    "selected = 'selected'>";
                }
                else{
                    $valikkohtml .= "<option value = '$arvot[$i]'>";
                }

                $valikkohtml .= $nimet[$i];
                $valikkohtml .= "</option>";
            }
            $valikkohtml .= "</select>";
        }

        else if(empty ($arvot)){
            $valikkohtml .= "</select>";
        }

        // Ellei parametritaulukot täytä ehtoja:
        else{
            $valikkohtml = "Virhe parametreissa!
                            (php_yleismetodit.luo_pudotusvalikko_onChange)";
        }

        return $valikkohtml;
    }
    
    /**
     * Muuten kuten luo_pudotusvalikko, mutta tähän voi antaa parametrina
     * myös id:n ja class-määritteen.
     * Palauttaa pudotusvalikon html-koodin. Asettaa valituksi parametrina annetun
     * arvon (tai sitä vastaavan nimen).
     * Arvoja ja nimiä tulee olla yhtä monta elementtiä sisältäviä taulukoita
     * (array). Ellei näin ole, palautetaan virheviesti.
     *
     * Pudotusvalikon otsikko määritellään viimeisellä parametrilla ($otsikko).
     *
     * @param array $arvot option value -arvot
     * @param array $nimet option-tagien väliin asetettava määre, eli tämä näkyy
     * käyttäjälle pudotusvalikon vaihtoehtona.
     * @param string $name_arvo select name -määreen arvo (nimi), jonka avulla
     * valittu arvo välittyy eteenpäin.
     * @param int $oletusvalinta_arvo (oletus)vaihtoehtoa vastaava arvo. Jos tämä
     * on tyhjä, muuten epäkelpo tai alle 0, näytetään ensimmäinen vaihtoehto.
     * @param string $otsikko Pudotusvalikon eteen kirjoitettava otsikko/kuvaus.
     * @return string Palauttaa pudotusvalikon html-koodin.
     */
    public static function luo_pudotusvalikko3($arvot,
                                                $nimet,
                                                $name_arvo,
                                                $id,
                                                $class,
                                                $oletusvalinta_arvo,
                                                $otsikko){

        if(empty ($otsikko)){
            $valikkohtml = "<select name='$name_arvo' id='$id' class='$class'>";
        }
        else{
            $valikkohtml = $otsikko.": <select name='$name_arvo' id='$id' class='$class'>";
        }

        if(is_array($arvot) && is_array($nimet) &&
            sizeof($arvot) == sizeof($nimet) && !empty ($arvot)){

            // Varmistetaan oletusvalinnan kelpoisuus:
            if(!isset($oletusvalinta_arvo) ||
                !is_numeric($oletusvalinta_arvo) ||
                $oletusvalinta_arvo < 0){
                $oletusvalinta_arvo = $arvot[0];
            }

            for ($i = 0; $i < sizeof($arvot); $i++) {
                if($oletusvalinta_arvo == $arvot[$i]){
                    $valikkohtml .= "<option value = '$arvot[$i]'".
                                    "selected = 'selected'>";
                }
                else{
                    $valikkohtml .= "<option value = '$arvot[$i]'>";
                }

                $valikkohtml .= $nimet[$i];
                $valikkohtml .= "</option>";
            }
            $valikkohtml .= "</select>";
        }

        else if(empty ($arvot)){
            $valikkohtml .= "</select>";
        }

        // Ellei parametritaulukot täytä ehtoja:
        else{
            $valikkohtml = "Virhe parametreissa!
                            (php_yleismetodit.luo_pudotusvalikko)";
        }

        return $valikkohtml;
    }
    
    /**
     * Muuten kuten luo_pudotusvalikko, mutta tähän voi antaa mielivaltaisen
     * määrän parametreja uudella tekniikalla. Muutenkin toteutus uusien
     * html-metodien pohjalta.
     * <p>Palauttaa pudotusvalikon html-koodin. Asettaa valituksi parametrina annetun
     * arvon (tai sitä vastaavan nimen).</p>
     * <p>Arvot ja nimet -muuttujien tulee olla yhtä monta elementtiä sisältäviä 
     * taulukoita (array). Ellei näin ole, palautetaan virheviesti (string).</p>
     * <p>Pudotusvalikon otsikko määritellään viimeisellä parametrilla ($otsikko).</p>
     *
     * Otsikko tulee label for -elementtinä.
     * 
     * @param array $arvot option value -arvot
     * @param array $nimet option-tagien väliin asetettava määre, eli tämä näkyy
     * käyttäjälle pudotusvalikon vaihtoehtona.
     * @param type $select_maaritteet Maarite-luokan olioita taulukossa.
     * Täällä pitää olla name-arvo, jos halutaan kuljettaa arvot http:n kautta
     * (ilman AJAX-tekniikkaa).
     * @param type $option_maaritteet Maarite-luokan olioita taulukossa. Huomaa,
     * että nämä tulevat samanlaisina kaikille option-elementeille. Voi olla
     * tyhjä. Metodissa varmistetaan, että kysymyksessä on taulukko.
     * @param int $oletusvalinta_arvo (oletus)vaihtoehtoa vastaava arvo. Jos tämä
     * on tyhjä, muuten epäkelpo tai alle 0, näytetään ensimmäinen vaihtoehto.
     * @param string $otsikko Pudotusvalikon eteen kirjoitettava otsikko/kuvaus.
     * Ellei tarvetta, kannattaa antaa tyhjä merkkijono. Muuten otsikko
     * sullotaan label-tagien sisään ja se yhdistetään valikon id:n kanssa.
     * @return string Palauttaa pudotusvalikon html-koodin.
     */
    public static function luo_pudotusvalikko_uusi($arvot,
                                                $nimet,
                                                $select_maaritteet,
                                                $option_maaritteet,
                                                $oletusvalinta_arvo,
                                                $otsikko){

        $option_html = "";
        
        if(!is_array($option_maaritteet)){
            $option_maaritteet = array();
        }
        
        // Otsikon muokkaus:
        if(!empty ($otsikko)){
            // Jotta label-elementti voidaan kiinnittää select-elementtiin,
            // tarvitaan tietää sen id:n arvo. Ellei sellaista löydy,
            // luodaan sellainen (pitäisi olla yksilöllinen).
            $id = Maarite::etsi_id($select_maaritteet);

            if($id === Maarite::$EI_LOYTYNYT){

                // Arvotaan luku väliltä 100000-1000000
                $id = rand(100000, 1000000);

                // Lisätään id taulukkoon:
                array_push($select_maaritteet, new Maarite("id",$id, false));
            }

            $otsikko = Html::luo_label_for($id, $otsikko.": ", array());
        }
        
        
        if(empty ($arvot)){
            $valikkohtml = $otsikko.Html::luo_select($option_html, array());
        }
        
        else if(is_array($arvot) && is_array($nimet) &&
            sizeof($arvot) == sizeof($nimet)){

            // Varmistetaan että oletusvalinta määritelty. Ellei,
            // asetetaan valituksi ensimmäinen taulukon alkio.
            if(!isset($oletusvalinta_arvo)){
                $oletusvalinta_arvo = $arvot[0];
            }

            // Luodaan option-elementti kustakin arvosta:
            for ($i = 0; $i < sizeof($arvot); $i++) {
                
                // Lisätään value-määrite
                Maarite::lisaa_maarite(Maarite::value($arvot[$i]), 
                                        $option_maaritteet);
                
                // Jos oletusvalinta täsmää:
                if($oletusvalinta_arvo == $arvot[$i]){
                    
                    // Lisätään selected-määrite, mutta vähän hankalasti, ettei
                    // jää kaikkiin kummittelemaan!
                    $selected_maaritteet = 
                        array_merge($option_maaritteet, array(Maarite::selected()));
                    
                    $option_html .= Html::luo_option($nimet[$i], $selected_maaritteet);
                }
                else{
                    $option_html .= Html::luo_option($nimet[$i], $option_maaritteet);
                }
            }
            
            $valikkohtml = 
                    $otsikko.Html::luo_select($option_html, $select_maaritteet);
        }

        // Jos parametreissä virhe:
        else{
            $valikkohtml = "Virhe parametreissa!
                            (php_yleismetodit.luo_pudotusvalikko_uusi)"; 
        }
        
        return $valikkohtml;
    }


    /**
     * Palauttaa valintanappien (input type='radio') html-koodin ILMAN
     * FORM-tageja, eli napit tulevat olemassaolevien form-tagien sisälle. Tämä
     * auttaa siinä, että voidaan laittaa useammat valintanapit samaan lomakkeeseen.
     *
     * Asettaa valituksi parametrina annetun arvon (tai sitä vastaavan nimen).
     * Arvoja ja nimiä tulee olla yhtä monta elementtiä sisältäviä taulukoita
     * (array). Ellei näin ole, palautetaan virheviesti.
     *
     * Valintanappien otsikko määritellään viimeisellä parametrilla ($otsikko).
     *
     * @param array $arvot option value -arvot
     * @param array $nimet option-tagien väliin asetettava kuvaus, eli tämä näkyy
     * käyttäjälle valintanapin edessä selityksenä.
     * @param string $name_arvo select name -määreen arvo (nimi), jonka avulla
     * valittu arvo välittyy eteenpäin.
     * @param int $oletusvalinta_arvo (oletus)vaihtoehtoa vastaava arvo. Jos tämä
     * on tyhjä tai null tai epäkelpo, näytetään ensimmäinen vaihtoehto.
     * @param bool $vaakatasossa totuusarvo true, jos napit halutaan vaakatasoon,
     * muuten arvon tulee olla false (jolloin napit allekain).
     * @param string $otsikko Pudotusvalikon eteen kirjoitettava otsikko/kuvaus. Voi
     * jättää tyhjäksi.
     * @return string Palauttaa pudotusvalikon html-koodin.
     */
    public static function luo_valintanapit($arvot,
                                $nimet,
                                $name_arvo,
                                $oletusvalinta_arvo,
                                $vaakatasossa,
                                $otsikko){

        $valintapainikeMJ = ""; // Tämä palautetaan.

        // Rivinvaihdot vain, jos vaihtoehdot eivät ole vaakatasossa:
        $rivinvaihto = "<br />";
        if($vaakatasossa){
            $rivinvaihto = "";
        }

        if($otsikko != ""){
            $valintapainikeMJ = $otsikko.$rivinvaihto.": ";
        }

        if(is_array($arvot) && is_array($nimet) &&
            sizeof($arvot) == sizeof($nimet)){

            // Varmistetaan oletusvalinnan kelpoisuus:
            if(!isset($oletusvalinta_arvo) ||
                !is_numeric($oletusvalinta_arvo) || // HUOMAA is_int ei käy! (arvo tulee tekstinä)
                $oletusvalinta_arvo < 0){
                $oletusvalinta_arvo = $arvot[3];
            }

            for ($i = 0; $i < sizeof($arvot); $i++) {
                if($oletusvalinta_arvo == $arvot[$i]){

                    $valintapainikeMJ .= "<span style='white-space:nowrap'>".
                                    $nimet[$i].":".
                                    "<input type='radio' checked='checked'
                                    name='$name_arvo' value='$arvot[$i]'/>
                                    </span> $rivinvaihto";

                }
                else{
                    $valintapainikeMJ .= "<span style='white-space:nowrap'>".
                                    $nimet[$i].":<input type='radio'
                                    name='$name_arvo' value='$arvot[$i]'/>
                                    </span> $rivinvaihto";
                }
            }
        }

        // Ellei parametritaulukot täytä ehtoja:
        else{
            $valintapainikeMJ = "Virhe parametreissa!
                            (php_yleismetodit.luo_valintanapit)";
        }

        return $valintapainikeMJ;
    }


    /* ***************************** FUNCTION LUO_PAINIKERIVI *****************/
    /**
     * Palauttaa LOMAKKEEN merkkauksen, joka näyttää painikkeet rivissä.
     * Painikkeita on yhtä monta, kuin parametritaulukoissa on alkioita.
     * Parametritaulukot sisältävät painikkeiden value- ja name-attribuutit.
     * Action arvo on
     * painikkeissa sivulle itselleen eli ACTION='{$_SERVER['PHP_SELF']}'.
     * Lähetysmetodina on POST.
     *
     * HUOM! Kaikki painikkeet ovat siis yhden lomakkeen sisällä!
     *
     * @param array $valuet value-kenttien arvot
     * @param array $namet name-kenttien arvot
     * @param string $kysely tämä liitetään action-osoitteen perään!
     * HUOM! KYSELY-LAUSE PITÄÄ RAWURLENCOODATA AIEMMIN JOS TARPEEN!
     * @return string Palauttaa painikkeiden html-koodin.
     */
    public static function luo_painikerivi($namet, $valuet ,$kysely)
    {
        // Varmistetaan, että taulukot ovat samansuuruisia:
        if(sizeof($valuet) == sizeof($namet)){

            $nappimerkkaus = "<form method='post'".
                    "action='{$_SERVER['PHP_SELF']}?".$kysely."'>";
            for($i = 0; $i < sizeof($valuet); $i++){
                $nappimerkkaus .=
                    "<input type='submit' name='$namet[$i]' value='$valuet[$i]'/>";
            }
            $nappimerkkaus .= "</form>";
        }

        else{
            $nappimerkkaus = "Virhe: name- ja valueparametrej&auml; ".
                            "eri m&auml;&auml;r&auml;!";
        }
        return $nappimerkkaus;
    }
    
    //==========================================================================
    /* Seuraavat metodit ovat järjestelmällinen helpotus html:n kirjoittamiseen
     * php:n avulla. Osa on tarkoitettu
     * vain sisäiseen käyttöön, jolloin ne eivät ole julkisia.
     */
    
    
    // Ellen ikinä huomaa ihmisen hätää, en ole ihminen.
    // Ellen ikinä ohita ihmisen hätää, näännyn.
    
    /**
     * Palauttaa alkutagin, jossa voi olla erilaisia määreitä. Jos määreen
     * arvo on tyhjä merkkijono, se ohitetaan.
     * @param type $taginimi
     * @param type $maarite_array elementin määritteet Maarite-luokan olioina
     * taulukossa.
     * @return string 
     */
    private static function luo_alkutagi($taginimi, $maarite_array){ 
        
        $maaritteet = Maarite::muotoile_maareet($maarite_array);
        
        // Paketoidaan koko homma:
        $html = "<".$taginimi.$maaritteet.">";
        
        return $html;
    }
    private static function luo_lopputagi($taginimi){ 
        $html = "</".$taginimi.">";
        
        return $html;
    }
    
    /**
     * Palauttaa html, jossa sisällön ympärille on lisätty halutut tagit.
     * Elementin nimeä ei täällä tarkasteta, joten jää kehittäjän huoleksi olla
     * huolellinen. Esimerkiksi yksiosaiset tagit kuten br ei toimi tällä!
     * 
     * On suositeltavaa käyttää suoria eri tageille tarkoitettuja
     * metodeita, jos sellainen on olemassa.
     * @param type $elem_nimi
     * @param type $sisalto 
     */
    private static function luo_elem_2os($elem_nimi, $sisalto, $maarite_array){
        $html = Html::luo_alkutagi($elem_nimi, $maarite_array).
                $sisalto.
                Html::luo_lopputagi($elem_nimi);
        return $html;
    }
    
    /**
     * Palauttaa 1-osaisen elementin html:n, esim "<br/>. Näissä ei ole
     * sisältöä.
     * @param type $elem_nimi
     * @param type $maarite_array
     * @return string 
     */
    private static function luo_elem_1os($elem_nimi, $maarite_array){
        $maaritteet = Maarite::muotoile_maareet($maarite_array);
        
        // Paketoidaan koko homma:
        $html = "<".$elem_nimi.$maaritteet."/>";
        return $html;
    }
    
    /**
     * Luo div-elementin, jonka sisältää parametrina annetun sisällön ja
     * parametrit.
     */
    public static function luo_div($sisalto, $maar_array){
        return Html::luo_elem_2os("div", $sisalto, $maar_array);
    }
    
    public static function luo_p($sisalto, $maar_array){
        return Html::luo_elem_2os("p", $sisalto, $maar_array);
    }
    public static function luo_i($sisalto, $maar_array){
        return Html::luo_elem_2os("i", $sisalto, $maar_array);
    }
    public static function luo_b($sisalto, $maar_array){
        return Html::luo_elem_2os("b", $sisalto, $maar_array);
    }
    
    public static function luo_small($sisalto, $maar_array){
        return Html::luo_elem_2os("small", $sisalto, $maar_array);
    }
    
    public static function luo_span($sisalto, $maar_array){
        return Html::luo_elem_2os("span", $sisalto, $maar_array);
    }
    public static function luo_table($sisalto, $maar_array){
        return Html::luo_elem_2os("table", $sisalto, $maar_array);
    }
    public static function luo_tablerivi($sisalto, $maar_array){
        return Html::luo_elem_2os("tr", $sisalto, $maar_array);
    }
    public static function luo_tablesolu($sisalto, $maar_array){
        return Html::luo_elem_2os("td", $sisalto, $maar_array);
    }
    public static function luo_tablesolu_otsikko($sisalto, $maar_array){
        return Html::luo_elem_2os("th", $sisalto, $maar_array);
    }
    
    /**
     * Muista: tänne yleensä laitetaan määritteet type=post ja 
     * action=osoite?kyselyt ainakin.
     * @param type $sisalto
     * @param type $maar_array
     * @return type
     */
    public static function luo_form($sisalto, $maar_array){
        return Html::luo_elem_2os("form", $sisalto, $maar_array);
    }
    
    /**
     * Palauttaa form-lomakkeen sisällä olevan input-elementin koodin. Parametrina
     * annetaan kummankin $maar_array. Jos $maar_array_form-taulukossa on
     * action-määritteellä arvo "oletus", "default" tai määrite puuttuu, 
     * annetaan arvoksi "{$_SERVER['PHP_SELF']}".
     * 
     * <p>Nämä pakotetaan joka tapauksessa riippumatta parametreista:
     * Form method="post" ja input type="submit"</p>
     * 
     * Helpoimmillaan painikkeen saa siis toimimaan antamalla parametreina yhden 
     * tyhjän taulukon ja toiseen ($maar_array_input) vain value- ja namemääritteet:
     * array(Maarite::value($painikkeen_nimi), Maarite::name($toimintonimi))
     * 
     * @param type $maar_array_form
     * @param type $maar_array_input
     * @return type
     */
    public static function luo_forminput_painike($maar_array_form,
                                                $maar_array_input){
        
        Maarite::lisaa_maarite(Maarite::method("post"), $maar_array_form);
        Maarite::lisaa_maarite(Maarite::type("submit"), $maar_array_input);
        
        // Tarkastellaan action-määritettä:
        if(Maarite::etsi_maarite("action", $maar_array_form)==
            Maarite::$EI_LOYTYNYT ||
            Maarite::etsi_maarite("action", $maar_array_form)=="oletus" ||
            Maarite::etsi_maarite("action", $maar_array_form)=="default"){
            
            Maarite::lisaa_maarite(Maarite::action("{$_SERVER['PHP_SELF']}"), 
                                    $maar_array_form);
        }
                
        $sisalto = Html::luo_input($maar_array_input);
        return Html::luo_elem_2os("form", $sisalto, $maar_array_form);
    }
    
    /**
     * Luo Button-painikkeen, jonka type-määrite on pakotetaan
     * arvoon "button" (muuten voi tulla ongelmia selainerojen takia). Jos type-
     * määritteelle on annettu jokin arvo, se ylikirjoitetaan.
     * 
     * @param type $painiketeksti
     * @param type $maar_array
     * @return type 
     */
    public static function luo_button($painiketeksti, $maar_array){
        
        if(!is_array($maar_array)){
            $maar_array = array();
        }
        
        // Lisätään määritteet (ylikirjoittaen mahdolliset vanhat arvot):
        $maar_array = Maarite::lisaa_maarite(Maarite::type("button"), $maar_array);
        
        return Html::luo_elem_2os("button", $painiketeksti, $maar_array);
    }
    public static function luo_select($sisalto, $maar_array){
        return Html::luo_elem_2os("select", $sisalto, $maar_array);
    }
    
    /**
     * Luo pudotusvalikon (SELECT-elementti) vaihtoehdon. Oletusvalinnaksi
     * voit merkitä "selected"-määritteellä.
     * @param type $sisalto
     * @param type $maar_array
     * @return string Html-koodi
     */
    public static function luo_option($sisalto, $maar_array){
        return Html::luo_elem_2os("option", $sisalto, $maar_array);
    }
    public static function luo_textarea($sisalto, $maar_array){
        return Html::luo_elem_2os("textarea", $sisalto, $maar_array);
    }
    
    public static function luo_script_js($koodi){
        
        // Lisätään type-määrite:
        $maar_array = array();
        Maarite::lisaa_maarite(Maarite::type("text/javascript"), $maar_array);
        return Html::luo_elem_2os("script", $koodi, $maar_array);
    }
    
    /**
     * Luo linkin. Url koodataan.
     * @param type $url
     * @param type $teksti
     * @param type $maar_array
     * @return type 
     */
    public static function luo_a_linkto($url, $teksti, $maar_array){
        $maar_array = Maarite::lisaa_maarite(Maarite::href(rawurlencode($url)), $maar_array);
        return Html::luo_elem_2os("a", $teksti, $maar_array);
    }
    
    /**
     * Palauttaa html-koodin (input type="image"), joka luo kuvan, 
     * jota klikkaamalla lähetetään lomakkeen tiedot palvelimelle aivan kuin 
     * "input type='submit'" -elementillä. Parametreina annetaan kuvan 
     * suhteellinen tiedostopolku, alt-arvo, korkeus, leveys ja muut määritteet
     * (neljä ensimmäista erikseen, jotteivat unohdu. Muista myös name-arvo!).
     * 
     * @param type $src
     * @param type $alt
     * @param type $height
     * @param type $width
     * @param type $maar_array
     * @return type 
     */
    public static function luo_imagesubmit_painike($src, 
                                                    $alt, 
                                                    $height, 
                                                    $width, 
                                                    $maar_array){
        
        
        if(!is_array($maar_array)){
            $maar_array = array();
        }
        
        // Lisätään määritteet (ylikirjoittaen mahdolliset vanhat arvot):
        Maarite::lisaa_maarite(Maarite::src($src), $maar_array);
        Maarite::lisaa_maarite(Maarite::alt($alt), $maar_array);
        Maarite::lisaa_maarite(Maarite::height($height), $maar_array);
        Maarite::lisaa_maarite(Maarite::width($width), $maar_array);
        
        return Html::luo_input($maar_array);
    }
    
    /**
     * Luo rastitusruudun ilman tekstejä.
     * @param type $maar_array
     * @return type 
     */
    public static function luo_checkbox($maar_array){
        Maarite::lisaa_maarite(Maarite::type("checkbox"), $maar_array);
        return Html::luo_input($maar_array);
    }
    
    /**
     * Luo valintapallukan ilman tekstejä.
     * @param type $maar_array
     * @return type 
     */
    public static function luo_radiobutton($maar_array){
        Maarite::lisaa_maarite(Maarite::type("radio"), $maar_array);
        return Html::luo_input($maar_array);
    }
    
    /**
     * Luo label-elementin, jonka for-määritteen arvo annetaan parametrina.
     * Tämä auttaa hiiri-ihmisiä, koska esim. valintanapit toimivat tekstistä
     * myös.
     * 
     * Ellei $maar_array-parametri ole kelvollinen taulukko, luodaan tyhjä
     * tilalle.
     * 
     * @param type $for_id
     * @param type $teksti
     * @param type $maar_array
     * @return type 
     */
    public static function luo_label_for($for_id, $teksti, $maar_array){
        
        // Lisätään for-määrite:
        if(!is_array($maar_array)){
            $maar_array = array();
        }
        $maar_array = Maarite::lisaa_maarite(Maarite::forr($for_id), $maar_array);
        
        return Html::luo_elem_2os("label", $teksti, $maar_array);
    }
    
    /**
     * Luo label+input (type='radio') -elementit! Huom! $maar_array koskee
     * input-elementtiä. Label-elementti tarvitsee inputin id-arvon, joten
     * ellei sellaista ole, sellainen arvotaan ja lisätään input-elementille.
     * Label-elementille ei tässä voi asettaa määritteitä. Tarvittaessa voi
     * muuttaa lisäämällä toinen maaritetaulukko labelia varten.
     * @param type $painiketeksti
     * @param type $maar_array
     * @return type 
     */
    public static function luo_labeled_radiobutton($painiketeksti, $maar_array){
        $koodi = "";
        
        // Varmistetaan, että taulukko kunnossa:
        if(!isset($maar_array) || !is_array($maar_array)){
            $maar_array = array();
        }
        
        // Jotta label-elementti voidaan kiinnittää radiopainikkeeseen,
        // tarvitaan tietää radion id:n arvo. Ellei sellaista löydy,
        // luodaan sellainen (pitäisi olla yksilöllinen)
        $id = Maarite::etsi_id($maar_array);
        
        if($id === Maarite::$EI_LOYTYNYT){
            
            // Arvotaan luku väliltä 100000-1000000
            $id = rand(100000, 1000000);
            
            // Lisätään id taulukkoon:
            array_push($maar_array, new Maarite("id",$id, false));
        }
        
        $koodi .= Html::luo_label_for($id, $painiketeksti, "");
        
        // Lisätään määritteisiin type='radio'
        Maarite::lisaa_maarite(Maarite::type("radio"), $maar_array);
        
        $koodi .= Html::luo_input($maar_array);
        return $koodi;
    }
    
    /**
     * Luo label+input (type='checkbox') -elementit! Huom! $maar_array koskee
     * input-elementtiä. Label-elementti tarvitsee inputin id-arvon, joten
     * ellei sellaista ole, sellainen arvotaan ja lisätään input-elementille.
     * Label-elementille ei tässä voi asettaa määritteitä. Tarvittaessa voi
     * muuttaa lisäämällä toinen maaritetaulukko labelia varten.
     * 
     * Muutos 5.1.2014: mahdollinen title-määrite lisätään myös labelille, mikä
     * on käyttäjäystävällistä.
     * 
     * @param type $painiketeksti
     * @param type $maar_array
     * @return type 
     */
    public static function luo_labeled_checkbox($teksti, $maar_array){
        $koodi = "";
        
        // Varmistetaan, että taulukko kunnossa:
        if(!isset($maar_array) || !is_array($maar_array)){
            $maar_array = array();
        }
        
        // Jotta label-elementti voidaan kiinnittää radiopainikkeeseen,
        // tarvitaan tietää radion id:n arvo. Ellei sellaista löydy,
        // luodaan sellainen (pitäisi olla yksilöllinen)
        $id = Maarite::etsi_id($maar_array);
        
        if($id === Maarite::$EI_LOYTYNYT){
            
            // Arvotaan luku väliltä 100000-1000000
            $id = rand(100000, 1000000);
            
            // Lisätään id taulukkoon:
            array_push($maar_array, new Maarite("id",$id, false));
        }
        
        // Tarkistetaan title-määritteen olemassaolo ja jos löytyy, lisätään se
        // myös label-elementille:
        $maar_array_label = array();
        $titlen_arvo = Maarite::etsi_maarite("title", $maar_array);
        if($titlen_arvo != Maarite::$EI_LOYTYNYT){
            array_push($maar_array_label, new Maarite("title",$titlen_arvo, false));
        }
        
        // Labelin koodi:
        $koodi .= Html::luo_label_for($id, $teksti, $maar_array_label);
        
        // Lisätään määritteisiin type='checkbox'
        $maar_array = Maarite::lisaa_maarite(Maarite::type("checkbox"), $maar_array);
        
        $koodi .= Html::luo_input($maar_array);
        return $koodi;
    }
    
    /**
     * Luo input-elementin. Ei rajoituksia.
     * @param type $maar_array
     * @return type
     */
    public static function luo_input($maar_array){
        return Html::luo_elem_1os("input", $maar_array);
    }
    
    /**
     * Luo img-elementin.
     * @param type $maar_array
     * @return type
     */
    public static function luo_img($maar_array){
        return Html::luo_elem_1os("img", $maar_array);
    }
    
    /**
     * Luo rivinvaihdon eli br-elementin. Sillä ei ole määritteitä (näkymätön
     * elementti).
     * @return type 
     */
    public static function luo_br(){
        return Html::luo_elem_1os("br", "");
    }
    
    
    
}

/**
 * Tämä luokka auttaa html-määritteiden syötössä metodeille ja siis sisältää
 * yhden määritteen nimen, arvon ja mahdolliset metodiparametrit (js-metodi). 
 */
class Maarite{
    private $nimi, $arvo, $js_parametrit;
    
    public static $EI_LOYTYNYT = 10;
    
    // True, jos kysymyksessä js-metodi, kuten onclick. Sillä ei kuitenkaan
    // aina ole parametreja.
    private $on_js_metodi; 
    
    function __construct($nimi, $arvo, $on_js_metodi){
        $this->arvo = $arvo;
        $this->nimi = $nimi;
        $this->js_parametrit = array();
        $this->on_js_metodi = $on_js_metodi;
    }
    
    
    
    function get_nimi(){
        return $this->nimi;
    }
    function get_arvo(){
        return $this->arvo;
    }
    function set_arvo($uusi){
        $this->arvo = $uusi;
    }
    
    /**
     * Ilman get-alkua luonnollisempi boolean palautteessa.
     * @return type
     */
    function on_js_metodi(){
        return $this->on_js_metodi;
    }
    
    function get_js_parametrit(){
        return $this->js_parametrit;
    }
    function set_js_parametrit($param_array){
        if($this->on_js_metodi && is_array($param_array)){
            $this->js_parametrit = $param_array;
        }
    }
    
    public function html(){
        if($this->on_js_metodi){
            $html = $this->muotoile_js_koodi();
        }
        else{
            $html = " ".$this->nimi."='".$this->arvo."' ";
        } 
        return $html;
    }
    
    /**
     * Muotoilee js-parametrit. this-avainsanan käyttö ei toimi vielä, mutta
     * siihen olisi hyvä keksiä jotakin.
     * @return string
     */
    function muotoile_js_koodi(){
        // JS-metodin parametrien muotoilu:
        $koodi = "";
        $param = "";
        if(!empty($this->arvo) && !empty($this->nimi)){
            
            $laskuri = 0;
            foreach($this->js_parametrit as $parametri){

                // Lisätään pilkku silloin, kun ei ole eka parametri:
                if($laskuri > 0){
                    $param .= ",";
                }

                // Muotoillaan js-metodiin sopivasti:
                if(is_numeric($parametri) || is_array($parametri)){
                    $param .= $parametri;
                }
                else if (is_bool($parametri)) {
                    if($parametri){
                        $param .= 1;
                    }
                    else{
                        $param .= 0;
                    }
                }
                else{   // merkkijonot yms.

                    $param .= "\"".$parametri."\"";

                }
                $laskuri++;
            }

            $koodi= " ".$this->nimi."='$this->arvo($param)' ";
            
        }

        return $koodi;
    }
    
    /**
     * Palauttaa maaritekoodin muotoiltuna niin, että sen voi sijoittaa
     * suoraan tagin sisään.
     * @param type $maarite_array 
     */
    static function muotoile_maareet($maarite_array){
        $koodi = "";
        
        // Huom! is_array palauttaa FALSEn, ellei parametri määritelty. 
        // Testasin 9.2012.
        if(is_array($maarite_array)){
            foreach ($maarite_array as $maariteolio) {
                if($maariteolio instanceof Maarite){
                    $koodi .= $maariteolio->html();
                }
            }
        } 
        return $koodi;
    }
    /**
     * Etsii Maarite-luokan olioista id:tä ja palauttaa sen arvon. Ellei
     * id:tä löydy, palauttaa arvon Maarite::EI_LOYTYNYT
     * @param type $maarite_array
     * @return type 
     */
    static function etsi_id($maarite_array){
        return Maarite::etsi_maarite("id", $maarite_array);
    }
    
    /**
     * Etsii Maarite-luokan olioista nimen mukaista maaritetta ja 
     * palauttaa sen arvon. Ellei löydy, palauttaa arvon Maarite::EI_LOYTYNYT
     * @param type $maarite_array
     * @return type 
     */
    static function etsi_maarite($nimi, $maarite_array){
        $koodi = Maarite::$EI_LOYTYNYT;
        if(is_array($maarite_array)){
            foreach ($maarite_array as $maariteolio) {
                if($maariteolio instanceof Maarite){
                    if($maariteolio->nimi === $nimi){
                        $koodi = $maariteolio->arvo;
                    }
                }
            }
        } 
        return $koodi;
    }
    
    /**
     * Lisää määrite-taulukkoon määritteen niin, että jos kyseinen määrite
     * siellä jo on, sen arvo muutetaan. Muuten lisätään uusi määrite.
     * Palauttaa määritetaulukon.
     * 
     * <p>Ellei määrite-taulukko ole määritelty taulukko, luodaan sen sijaan
     * uusi tyhjä taulukko.</p>
     * 
     * <p>Ellei $uusi ole Maarite-luokan olio, ei tehdä mitään!</p>
     * 
     * <p>Jos määrite on parametrillinen js-metodi (onclick, 
     * onmouseover tms.) muutetaan mahdollisesta jo olemassaolevasta 
     * määritteestä arvon (metodin nimen) lisäksi myös metodin parametrit
     * (taulukko).</p>
     * 
     * <p>HUOM! $maarite_array-parametrin edessä & -> muutokset siirtyvät 
     * suoraan taulukkoon.</p>
     * 
     * @param Maarite $uusi
     * @param type $maarite_array
     * @return type array Palauttaa muokatun $maarite_array-taulukon.
     */
    static function lisaa_maarite($uusi, &$maarite_array){
        $koodi = Maarite::$EI_LOYTYNYT;
        
        if($uusi instanceof Maarite){
            
            // is_array palauttaa falsen, jos parametri null.
            if(!is_array($maarite_array)){
                $maarite_array = array();
            }
            else{
                foreach ($maarite_array as $maariteolio) {
                    if($maariteolio instanceof Maarite){
                        
                        // Jos määrite löytyi, muutetaan sitä:
                        if($maariteolio->get_nimi() == $uusi->get_nimi()){

                            // muutetaan arvo:
                            $maariteolio->set_arvo($uusi->get_arvo());
                            
                            // JS-tapauksessa myös parametrit:
                            if($uusi->on_js_metodi() && $maariteolio->on_js_metodi()){
                                $maariteolio->set_js_parametrit(
                                                    $uusi->get_js_parametrit());
                            }
                            $koodi = "loytyipa";
                        }
                    }
                }
            }
            
            // Liitetään uusi taulukkoon, ellei löytynyt.
            if($koodi === Maarite::$EI_LOYTYNYT){
                
                // Lisätään määrite taulukkoon:
                array_push($maarite_array, $uusi);
            }
        }
        
        return $maarite_array;
    }
    
    //==========================================================================
    /* 
     * Seuraavat metodit liittyvät Maarite-luokan käyttöön ja ideana on tehdä
     * html-elementtien määritteiden syötöstä mahdollisimman helppoa.
     */
    public static function id($arvo){
        return new Maarite("id", $arvo, false);
    }
    public static function classs($arvo){
        return new Maarite("class", $arvo, false);
    }
    public static function title($arvo){
        return new Maarite("title", $arvo, false);
    }
    public static function name($arvo){
        return new Maarite("name", $arvo, false);
    }
    public static function value($arvo){
        return new Maarite("value", $arvo, false);
    }
    public static function type($arvo){
        return new Maarite("type", $arvo, false);
    }
    
    /**
     * Huomaa metodi muotoile_action_arvo() ainakin jos kyselyitä on monia.
     * @param type $arvo
     * @return \Maarite
     */
    public static function action($arvo){
        return new Maarite("action", $arvo, false);
    }
    
    /**
     * Palauttaa merkkijonon, joka sopii action-määritteen arvoksi.
     * Parametreiksi annetaan käsittelijän osoite ja mahdollisen kyselyn
     * muuttujanimet ja arvot taulukoissa. <b>Järjestyksen pitää olla sama!</b> 
     * 
     * <p>Jos parametrit huonot (esim taulukot erikokoisia), palauttaa
     * pelkästään osoitteen.</p>
     * 
     * Jos osoite on arvoltaan "oletus" tai "default" (kirjainkoolla ei 
     * merkitystä), annetaan osoitteeksi "{$_SERVER['PHP_SELF']}".
     * 
     * @param type $osoite esim index.php
     * @param array $muuttujanimet Muuttujien nimet taulukossa (ks Parametrit)
     * @param array $arvot Muuttujien arvot
     */
    public static function muotoile_action_arvo($osoite, $muuttujanimet, $arvot){
        $palaute = $osoite;
        
        if(strtolower($osoite) == "oletus"  || 
           strtolower($osoite) == "default"){
            $palaute = "{$_SERVER['PHP_SELF']}";
        }
        
        if(is_array($muuttujanimet) && is_array($arvot) &&
                (sizeof($muuttujanimet) == sizeof($arvot) &&
                !empty($arvot))){
            $palaute .= "?";
            
            for($i = 0; $i < sizeof($arvot); $i++){
                if($i > 0){
                    $palaute .= "&";
                }
                $palaute .= $muuttujanimet[$i]."=".$arvot[$i];
            }
        }
        return $palaute;
    }
    
    /**
     *Tämä sopii vaikka kuvien lataamiseen form-elementin määritteeksi.
     * @return \Maarite 
     */
    public static function enctype_multipart_form_data(){
        return new Maarite("enctype", "multipart/form-data", false);
    }
    
    public static function method($arvo){
        return new Maarite("method", $arvo, false);
    }
    /**
     * Taulukon määre.
     * @param type $arvo
     * @return \Maarite 
     */
    public static function summary($arvo){
        return new Maarite("summary", $arvo, false);
    }
    
    /**
     * Valittu-määrite valintanappeihin: Arvo aina 'checked'.
     * @param type $arvo
     * @return \Maarite 
     */
    public static function checked(){
        return new Maarite("checked", "checked", false);
    }
    /**
     * Soveltuu OPTION-elementtiin.
     * Asettaa valintalistan kohdan (option-elementin) valituksi pudotusvalikossa.
     * 
     * Toimii kaikissa suurissa selaimissa.
     * 
     * @return \Maarite 
     */
    public static function selected(){
        return new Maarite("selected", "selected", false);
    }
    
    public static function alt($arvo){
        return new Maarite("alt", $arvo, false);
    }
    public static function src($arvo){
        return new Maarite("src", $arvo, false);
    }
    public static function size($arvo){
        return new Maarite("size", $arvo, false);
    }
    public static function max_length($arvo){
        return new Maarite("maxlength", $arvo, false);
    }
    
    /**
     * HUOM! Myös Html5:n input-elementtiin "TYPE=IMAGE" soveltuva määrite. 
     * Toimii lomakkeessa kuten Submit-painike. Lähettää palvelimelle muiden 
     * tietojen lisäksi myös x- ja y-koordinaatit klikkauksesse. 
     * Alla esimerkki koodista:
     * 
     * <p><input type="image" src="img_submit.gif" alt="Submit" width="48" height="48"/></p>
     * (http://www.w3schools.com/html5/html5_form_attributes.asp)
     * 
     * <p>Toimii 9/2012 kaikissa suurissa selaimissa.</p>
     * 
     * <p>Tätä voi toki käyttää myös esim. img-elementin kanssa vanhaan tyyliin.</p>
     * 
     * @param type $arvo
     * @return \Maarite 
     */
    public static function height($arvo){
        return new Maarite("height", $arvo, false);
    }
     
    /**
     * HUOM! Myös Html5:n input-elementtiin "TYPE=IMAGE" soveltuva määrite. 
     * Toimii lomakkeessa kuten Submit-painike. Lähettää palvelimelle muiden 
     * tietojen lisäksi myös x- ja y-koordinaatit klikkauksesse. 
     * Alla esimerkki koodista:
     * 
     * <p><input type="image" src="img_submit.gif" alt="Submit" width="48" height="48"/></p>
     * (http://www.w3schools.com/html5/html5_form_attributes.asp)
     * 
     * <p>Toimii 9/2012 kaikissa suurissa selaimissa.</p>
     * 
     * <p>Tätä voi toki käyttää myös esim. img-elementin kanssa vanhaan tyyliin.</p>
     * 
     * @param type $arvo
     * @return \Maarite 
     */
    public static function width($arvo){
        return new Maarite("width", $arvo, false);
    }
    public static function align($arvo){
        return new Maarite("align", $arvo, false);
    }
    public static function style($arvo){
        return new Maarite("style", $arvo, false);
    }
    /**
     * Palauttaa for-määritteen html:n.
     * @param type $id
     * @return \Maarite
     */
    public static function forr($id){
        return new Maarite("for", $id, false);
    }
    /**
     * 
     * @param type $os
     * @return \Maarite
     */
    public static function href($os){
        return new Maarite("href", $os, false);
    }
    
    public static function cols($arvo){
        return new Maarite("cols", $arvo, false);
    }
    public static function rows($arvo){
        return new Maarite("rows", $arvo, false);
    }
    public static function colspan($arvo){
        return new Maarite("colspan", $arvo, false);
    }
    public static function rowspan($arvo){
        return new Maarite("rowspan", $arvo, false);
    }
    public static function onclick($metodinimi, $parametri_array){
        $m = new Maarite("onclick", $metodinimi, true);
        $m->set_js_parametrit($parametri_array);
        return $m;
    }
    public static function onchange($metodinimi, $parametri_array){
        $m = new Maarite("onchange", $metodinimi, true);
        $m->set_js_parametrit($parametri_array);
        return $m;
    }
    public static function onmouseover($metodinimi, $parametri_array){
        $m = new Maarite("onmouseover", $metodinimi, true);
        $m->set_js_parametrit($parametri_array);
        return $m;
    }
     public static function onmouseout($metodinimi, $parametri_array){
        $m = new Maarite("onmouseout", $metodinimi, true);
        $m->set_js_parametrit($parametri_array);
        return $m;
    }
    public static function onkeyup($metodinimi, $parametri_array){
        $m = new Maarite("onkeyup", $metodinimi, true);
        $m->set_js_parametrit($parametri_array);
        return $m;
    }
    
    /**
     * Huom! Metodin pitää palauttaa boolean-arvo. True aiheuttaa
     * lomakkeen lähettämisen, false taas peruuttaa sen. 
     */
    public static function onsubmit($metodinimi, $parametri_array){
        
        // Lisätään return ennen metodinimeä, jotta homma toimii. 
        $m = new Maarite("onsubmit", "return ".$metodinimi, true);
        $m->set_js_parametrit($parametri_array);
        return $m;
    }
    
    //=========================================================================
    // Html5-juttuja, jotka ei välttämättä toimi kaikissa selaimissa. Tosin
    // silloin ne eivät myöskään haittaa normaalia toimintaa
    
    /**
     * Form- ja/tai input-elementtiin soveltuva määrite. Voi olla kummassakin.
     * Aiheuttaa automaattisen entisillä arvoilla täytön tai sitten estää sen.
     * Parametrin arvo on merkkijono "on" tai "off".
     * @param type $on_tai_off
     * @return \Maarite 
     */
    public static function autocomplete($on_tai_off){
        return new Maarite("autocomplete", $on_tai_off, false);
    }
    
    /**
     * Input-elementtiin soveltuva määrite. Elementti saa automaattisesti
     * huomion sivun latauduttua.
     * @param bool $boolean
     * @return \Maarite 
     */
    public static function autofocus($boolean){
        return new Maarite("autofocus", $boolean, false);
    }
    
    /**
     * Input-elementtiin soveltuva määrite. Määrittelee lomakkeen, johon
     * input-elementti kuuluu, vaikka elementti olisikin lomakkeen ulkopuolella!
     *
     * <p>Toimii 9/2012 muissa suurissa selaimissa paitsi IE:ssä.</p>
     * 
     * @param type $form_id
     * @return \Maarite 
     */
    public static function form($form_id){
        return new Maarite("form", $form_id, false);
    }
    
    /**
     * Input-elementtiin TYPE=FILE tai TYPE=EMAIL soveltuva määrite. 
     * Tuo käyttäjälle mahdollisuuden valita useita tiedostoja/osoitteita 
     * kerralla, mikä nopeuttaa lataamista.
     * 
     * <p>Määritteen arvo on aina "multiple", joten sitä ei tarvitse antaa. Itse
     * asiassa arvolla ei näytä ainakaa Chromessa olevan merkitystä, vaan
     * toimii esim. arvolla "none" tai "". Lähettää palvelimelle tiedot 
     * (esim. kuvissa tiedostonimet) &-merkillä erotettuina.</p>
     *
     * <p>Toimii 9/2012 muissa suurissa selaimissa paitsi IE:ssä.</p>
     * 
     * @return \Maarite 
     */
    public static function multiple(){
        return new Maarite("multiple", "multiple", false);
    }
    
    /**
     * Input-tekstielementtiin (text, search, url, tel, email, tai 
     * password) soveltuva määrite. 
     * Näyttää kentässä ennen siihen kirjoittamista parametrina annetun tekstin
     * harmaana.
     *
     * <p>Toimii 9/2012 muissa suurissa selaimissa paitsi IE:ssä.</p>
     * 
     * @return \Maarite 
     */
    public static function placeholder($teksti){
        return new Maarite("placeholder", $teksti, false);
    }
    
    /**
     * Input-elementtiin (text, search, url, tel, email, password, date pickers, 
     * number, checkbox, radio, ja file.) soveltuva boolean-määrite. 
     * 
     * <p>Vaatii elementin täyttämisen (tai valinnan) ennen kuin lähetys
     * hyväksytään.</p>
     *
     * <p>Toimii 9/2012 muissa suurissa selaimissa paitsi IE:ssä ja Safarissa.</p>
     * 
     * @return \Maarite 
     */
    public static function required(){
        return new Maarite("required", "required", false);
    }
    
    /**
     * Kaikkiin elementteihin soveltuva määrite. Määrää sen, voiko elementtiä
     * muokata lennosta.
     * 
     * <p>Toimii 9/2012 kaikissa suurissa selaimissa.</p>
     * 
     * @param $arvo "true", "false" tai "inherit".
     * @return \Maarite 
     */
    public static function contenteditable($arvo){
        return new Maarite("contenteditable", $arvo, false);
    }
    
    
}



?>