<?php

/**
 * Linkki havainnon ja kuvan välillä. Tämän avulla kuva yhdistetään tiettyyn
    havaintoon. Linkki hävitetään, jos kuva tai havainto hävitetään
    (tietokantatason CASCADE-ominaisuus).

    Kuvalla on yleensä aina linkki lajiluokkaan ja useimmiten myös havaintoon (ei
    välttämättä aina). Kuitenkin kuvat ovat itsenäisiä kokonaisuuksia ja toimivat 
    myös sellaisenaan (tämä aiheuttaa jonkin verran tiedon toistoa esimerkiksi
    päivämäärissä, mikä pitää ottaa huomioon, kun tietoja muokataan. Toisaalta
    kuvaluokat ovat yleiskäyttöisempiä mahdollisimman itsenäisinä).

    HUOM: kuvan ja havainnon välissä saa olla vain yksi linkki!
 * 
 * Tämä luokka yhdistää kuvan tiettyyn havaintoon. 
create table havaintokuvalinkit
(
  id                    int auto_increment not null,
  kuva_id               int default -1 not null,
  havainto_id           int default -1 not null,
  jarjestysluku         int not null,
  primary key (id),
  index(jarjestysluku),
  index(havainto_id),
  index(kuva_id),
  FOREIGN KEY (havainto_id) REFERENCES havainnot (id)
                      ON DELETE CASCADE,
  FOREIGN KEY (kuva_id) REFERENCES kuvat (id)
                      ON DELETE CASCADE
) ENGINE=INNODB;
 * @author J-P
 */
class Havaintokuvalinkki extends Malliluokkapohja{
    // Tietokannan sarakenimet (id tulee malliluokasta):
    public static $sarakenimi_kuva_id = "kuva_id";
    public static $sarakenimi_havainto_id = "havainto_id";
    public static $sarakenimi_jarjestysluku = "jarjestysluku";
    
    public static $taulunimi = "havaintokuvalinkit";
    
    private $havainto_id_muutettu;
    /**
     * Luokan muodostin:
     * 
     * @param type $id Mahdollisen tietokantaolio id, tai EI_MAARITELTY.
     * @param Tietokantaolio $tietokantaolio
     */
    function __construct($id, $tietokantaolio) {
        $tietokantasolut = 

            array(new Tietokantasolu(Havaintokuvalinkki::$SARAKENIMI_ID, Tietokantasolu::$luku_int, $tietokantaolio),
                new Tietokantasolu(Havaintokuvalinkki::$sarakenimi_kuva_id, Tietokantasolu::$luku_int, $tietokantaolio),
                new Tietokantasolu(Havaintokuvalinkki::$sarakenimi_havainto_id, Tietokantasolu::$luku_int, $tietokantaolio),
                new Tietokantasolu(Havaintokuvalinkki::$sarakenimi_jarjestysluku, Tietokantasolu::$luku_int, $tietokantaolio));
        
        $taulunimi = Havaintokuvalinkki::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
        $this->havainto_id_muutettu = false;
    }
    
    /**
     * Settereitä muokataan niin, että havainto_id:n muokkaus huomataan:
     * 
     * Palauttaa onnistumisen mukaan joko Pohja::$VIRHE tai 
     * Pohja::$OPERAATIO_ONNISTUI. Muuttaa muuttujan $havainto_id_muutettu
     * arvoon true, jos havaintoa todella muutetaan (ei välttämättä edes
     * mahdollista).
     * 
     * @param type $uusi
     * @param type $sarakenimi
     */
    public function set_arvo($uusi, $sarakenimi) {
        
        // Ollaan varovaisia, jos havaintoa ollaan muuttamassa:
        if($sarakenimi === Havaintokuvalinkki::$sarakenimi_havainto_id){
            
            // Otetaan vanha arvo ylös, jotta todellinen muutos selviää:
            $vanha = $this->get_arvo($sarakenimi);
            
            // Tehdään muutos:
            $palaute = parent::set_arvo($uusi, $sarakenimi);
            
            // Verrataan vielä ja varmistetaan, että arvo on todella muuttunut
            // myös tarkistuksien läpi mentyään:
            if($vanha === $this->get_arvo($sarakenimi)){
                $palaute = Pohja::$VIRHE;
            } else{
                $this->havainto_id_muutettu = true;
            }
        } else{
            $palaute = parent::set_arvo($uusi, $sarakenimi);
        }
        return $palaute;
    }
    /**
     * Settereitä muokataan niin, että havainto_id:n muokkaus huomataan:
     * 
     * Palauttaa onnistumisen mukaan joko Pohja::$VIRHE tai 
     * Pohja::$OPERAATIO_ONNISTUI. Muuttaa muuttujan $havainto_id_muutettu
     * arvoon true, jos havaintoa todella muutetaan (ei välttämättä edes
     * mahdollista).
     * 
     * @param type $uusi
     * @param type $sarakenimi
     */
    public function set_arvo_kevyt($uusi, $sarakenimi) {
        
        // Ollaan varovaisia, jos lajiluokkaa ollaan muuttamassa:
        if($sarakenimi === Havaintokuvalinkki::$sarakenimi_havainto_id){
            
            // Otetaan vanha arvo ylös, jotta todellinen muutos selviää:
            $vanha = $this->get_arvo($sarakenimi);
            
            // Tehdään muutos:
            $palaute = parent::set_arvo_kevyt($uusi, $sarakenimi);
            
            // Verrataan vielä ja varmistetaan, että arvo on todella muuttunut
            // myös tarkistuksien läpi mentyään:
            if($vanha === $this->get_arvo($sarakenimi)){
                $palaute = Pohja::$VIRHE;
            } else{
                $this->havainto_id_muutettu = true;
            }
        } else{
            $palaute = parent::set_arvo_kevyt($uusi, $sarakenimi);
        }
        return $palaute;
    }
    
    /**
     * Tallentaa uuden Havaintokuvalinkki-luokan olion tietokantaan, jos kaikki on
     * kunnossa. Ennen tallennusta tarkistetaan, ettei kuvan ja havainnon välillä
     * jo ole linkkiä, koska vain yksi sallitaan.
     * 
     * Palauttaa onnistumisen mukaan joko arvon Havaintokuvalinkki::$VIRHE tai
     * Havaintokuvalinkki::$OPERAATIO_ONNISTUI.
     * @return type
     */
    function tallenna_uusi() {
        $palaute = Havaintokuvalinkki::$VIRHE;
        
        // Tarkistetaan, ettei linkkiä kyseisen kuvan ja havainnon välillä jo ole:
        if(!$this->on_jo_olemassa($this->tk_taulunimi, array(
                                    Havaintokuvalinkki::$sarakenimi_kuva_id,
                                    Havaintokuvalinkki::$sarakenimi_havainto_id))){
            $palaute = parent::tallenna_uusi();
        } else{
            $this->lisaa_virheilmoitus(Kuvatekstit::$virhe_linkin_luomisessa.
                                ": ".Kuvatekstit::$linkki_on_jo_olemassa);
        }
        
        // Palautetaan mahdollisten jatkomuutosten takia:
        $this->havainto_id_muutettu = false;    
        return $palaute;
    }
    
    /**
     * Mieti vielä: voiko yhteen kuvaan liittyä linkit eri havaintoihin? Periaatteessa
     * samassa kuvassahan voi olla useampia lajeja! Totta, eli näin tehdään!
     * 
     * Kuitenkin yhden havainnon ja kuvan välillä saa olla vain yksi linkki. Se
     * tarkistetaan uutta linkkiä luodessa, mutta myös täällä asia pitää 
     * tarkistaa, jos havaintoa on muutettu. Muussa tapauksessa tarkistuksella ei
     * ole väliä, koska linkin kuva_id-muuttujan arvoa ei voi muuttaa.
     * 
     * Palauttaa onnistumisen mukaan joko arvon Havaintokuvalinkki::$VIRHE tai
     * Havaintokuvalinkki::$OPERAATIO_ONNISTUI.
     * @return type
     * 
     * @return type
     */
    function tallenna_muutokset() {
        $palaute = Havaintokuvalinkki::$VIRHE;
        
        // Jos havainto_id:tä on muutettu, tarkistetaan, ettei linkkiä kyseisen 
        // kuvan ja muutetun havainto_id:n välillä jo ole:
        if($this->havainto_id_muutettu){
            if(!$this->on_jo_olemassa($this->tk_taulunimi, array(
                                    Havaintokuvalinkki::$sarakenimi_kuva_id,
                                    Havaintokuvalinkki::$sarakenimi_havainto_id))){
                $palaute = parent::tallenna_muutokset();
            } else{
                $this->lisaa_virheilmoitus(Kuvatekstit::$virhe_linkin_luomisessa.
                                    ": ".Kuvatekstit::$linkki_on_jo_olemassa);
            }
        } else{
            $palaute = parent::tallenna_muutokset();
        }
        
        // Palautetaan mahdollisten jatkomuutosten takia:
        $this->havainto_id_muutettu = false;    
        return $palaute;
    }
}

?>
