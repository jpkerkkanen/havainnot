<?php


/**
 * Description of Tietokantarivi:
 * Sisältää tiedot tietokantataulusta. Yhden sarakkeen tiedot aina
 * Tietokantasolu-luokan oliossa, jotka tietokantarivi hallinnoi taulukossa.
 *
 * @author J-P
 */
class Tietokantarivi {
    
    private $tietokantasolut;
    private $taulunimi;
    private $sarakkeiden_lkm;   //Tietokantataulun sarakkeiden lkm (mukana id)
    
    function __construct($taulunimi, $tietokantasolut_array){
        
        $this->taulunimi = $taulunimi;
        $this->tietokantasolut = array();
        
        foreach ($tietokantasolut_array as $soluehdokas) {
            if($soluehdokas instanceof Tietokantasolu){
                array_push($this->tietokantasolut, $soluehdokas);
            }
        }
        
        $this->sarakkeiden_lkm = sizeof($this->tietokantasolut);
    }
    
    public function get_taulunimi(){
        return $this->taulunimi;
    }
    
    /**
     * Palauttaa tietokantataulun kaikkien sarakkeiden lukumäärän. Tämän avulla
     * voidaan tarkistaa ennen tallennusta, että määriteltyjä tietokantasoluja
     * (tiedot_ok) on oikea määrä. 
     * 
     * HUOMAA: tässä on myös id-sarake mukana,
     * jota ei ole metodien get_sarakenimet() ja get_arvot() palautteissa!
     * 
     * @return type 
     */
    public function get_sarakkeiden_lkm(){
        return $this->sarakkeiden_lkm;
    }

    /**
     * Palauttaa aina taulukon, joka useimmiten sisältää Tietokantasolu-
     * luokan olioita. Voi olla myös tyhjä.
     * @return type 
     */
    public function get_tietokantasolut(){
        return $this->tietokantasolut;
    }

    /**
     * Palauttaa arvomääriteltyjen solujen sarakenimet taulukossa 
     * (voi olla tyhjä). EI PALAUTA ID-SARAKETTA!
     * @return array 
     */
    public function get_sarakenimet_paitsi_id(){
        
        $sarakenimet = array();
        
        foreach ($this->tietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu){
                if($solu->tiedot_ok() && 
                    $solu->get_sarakenimi() != Malliluokkapohja::$SARAKENIMI_ID){
                    array_push($sarakenimet, $solu->get_sarakenimi());
                }
            }
        }
        return $sarakenimet;
    }
    
    /**
     * Palauttaa kaikkien solujen sarakenimet taulukossa 
     * (voi olla tyhjä). Ei vaadi arvon määrittämistä ensin.
     * 
     * Palauttaa myös id-sarakkeen nimen, joten älä
     * käytä tietokantaan tallennuksen yhteydessä (id:n tietokanta
     * muodostaa aina itse)!
     * @return array 
     */
    public function get_sarakenimet_kaikki(){
        
        $sarakenimet = array();
        
        foreach ($this->tietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu){
                array_push($sarakenimet, $solu->get_sarakenimi());
            }
        }
        return $sarakenimet;
    }
    
    /**
     * Palauttaa MÄÄRITELLYT arvot taulukossa (voi olla tyhjä) lukuunottamatta
     * id-sarakkeen arvoa (jotta sitä ei vaihdettaisi vahingossa).
     * 
     * EI PALAUTA ID-SARAKKEEN ARVOA turvallisuussyistä.
     * 
     * @return array 
     */
    public function get_arvot_paitsi_id(){
        
        $arvot = array();
        
        foreach ($this->tietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu){
                if($solu->tiedot_ok()&& 
                    $solu->get_sarakenimi() != Malliluokkapohja::$SARAKENIMI_ID){
                    array_push($arvot, $solu->get_arvo());
                }
            }
        }
        return $arvot;
    }
    
    /**
     * Palauttaa yksittäisen solun arvon tai arvon Pohja::MUUTTUJAA_EI_MAARITELTY. 
     * 
     */
    public function get_arvo($sarakenimi){
        $palaute = Pohja::$MUUTTUJAA_EI_MAARITELTY;
        foreach ($this->tietokantasolut as $solu) {
            if($solu->get_sarakenimi() === $sarakenimi){
                $palaute = $solu->get_arvo();
            }
        }
        return $palaute;
    }
    
    /**
     * Palauttaa yksittäisen solun arvon tai arvon Pohja::MUUTTUJAA_EI_MAARITELTY.
     * Arvo muokataan niin, että myös erikoismerkit näkyvät html-sivulla. 
     * 
     */
    public function get_html_encoded_arvo($sarakenimi){
        $palaute = Pohja::$MUUTTUJAA_EI_MAARITELTY;
        foreach ($this->tietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu){
                if($solu->get_sarakenimi() === $sarakenimi){
                    $palaute = $solu->get_html_encoded_arvo();
                }
            }
        }
        return $palaute;
    }
    
    /**
     * Asettaa yksittäisen solun arvon. Metodi kutsuu Tietokantasolu-luokan
     * metodia set_arvo(), joka huolehtii tarkastuksesta. 
     * 
     * Palauttaa onnistumisen mukaan joko Pohja::$VIRHE tai 
     * Pohja::$OPERAATIO_ONNISTUI
     * 
     */
    public function set_arvo($uusi_arvo, $sarakenimi){
        
        $palaute = Pohja::$VIRHE;
        
        foreach ($this->tietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu){
                if($solu->get_sarakenimi() === $sarakenimi){
                  
                    $solu->set_arvo($uusi_arvo);
                    
                    if($solu->tiedot_ok()){
                        $palaute = Pohja::$OPERAATIO_ONNISTUI;
                    }
                    break;
                }
            }
        }
        return $palaute;
    }
    
    /**
     * Asettaa yksittäisen solun arvon ilman tarkistuksia. 
     * Metodi kutsuu Tietokantasolu-luokan metodia set_arvo_kevyt(), joka 
     * huolehtii tarkastuksesta. 
     * 
     * <p>
     * HUOM! Kaikki tietokantaan menevä käyttäjän syöttämä tieto pitää
     * ehdottomasti asettaa set_arvo()-metodin avulla!
     * </p>
     * 
     * Palauttaa onnistumisen mukaan joko Pohja::$VIRHE tai 
     * Pohja::$OPERAATIO_ONNISTUI
     * 
     */
    public function set_arvo_kevyt($uusi_arvo, $sarakenimi){
        
        $palaute = Pohja::$VIRHE;
        
        foreach ($this->tietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu){
                if($solu->get_sarakenimi() === $sarakenimi){
                   
                    $solu->set_arvo_kevyt($uusi_arvo);
                    
                    if($solu->tiedot_ok()){
                        $palaute = Pohja::$OPERAATIO_ONNISTUI;
                    }
                    break;
                }
            }
        }
        return $palaute;
    }
    
    /**
     * Palauttaa sen Tietokantasolu-luokan olion, jonka sarakenimi vastaa
     * parametria, tai arvon Pohja::$MUUTTUJA_EI_MAARITELTY, ellei solua löydy.
     * @param type $sarakenimi
     * @return Tietokantasolu
     */
    public function get_tietokantasolu($sarakenimi){
        
        $tietokantasolu = Pohja::$MUUTTUJAA_EI_MAARITELTY;
        
        foreach ($this->tietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu){
                if($solu->get_sarakenimi() === $sarakenimi){
                    $tietokantasolu = $solu;
                    break;
                }
            }
        }
        return $tietokantasolu;
    }
    
    /**
     * Palauttaa tietokantarivin tiedot merkkijonoksi muotoiltuna.
     */
    public function toString(){
        $mj = "Tietokantarivi: <br/> ";
        foreach ($this->tietokantasolut as $solu) {
            if($solu instanceof Tietokantasolu){
                
                $mj .= $solu->get_sarakenimi()." = ".$solu->get_arvo();
                
                if($solu->tiedot_ok()){
                    $mj .= " (ok)";
                } else{
                    $mj .= " (EI ok!)";
                }
                
                $mj .= "<br/>";
            }
        }
        return $mj;
    }
}

?>
