<?php
/**
 * Linkki lajiluokan ja kuvan välillä. Tämän avulla kuva yhdistetään tiettyyn
lajiin, vaikkapa närheen. Linkki hävitetään, jos kuva tai lajiluokka hävitetään
(tietokantatason CASCADE-ominaisuus).
Kuvalla on yleensä aina linkki lajiluokkaan ja useimmiten myös havaintoon.
Kuitenkin kuvat ovat itsenäisiä kokonaisuuksia ja toimivat myös sellaisenaan
 * 
HUOM: kuvan ja lajiluokan välissä saa olla vain yksi linkki! Tässä mielessä
lajiluokkalinkin voisi sijoittaa kuvaolioon, mutta silloin kuvan yleiskäyttöisyys
kärsisi. Ehkä näin ok. Sitäpaitsi näin erilaisia linkkejä voi kohdistua kuvaan
rajoittamaton määrä.
 * 
 * SQL:
create table lajikuvalinkit
(
  id                    int auto_increment not null,
  kuva_id               int default -1 not null,
  lajiluokka_id         int default -1 not null,
  jarjestysluku         int not null,
  primary key (id),
  index(jarjestysluku),
  index(lajiluokka_id),
  index(kuva_id),
  FOREIGN KEY (lajiluokka_id) REFERENCES lajiluokat (id)
                      ON DELETE CASCADE,
  FOREIGN KEY (kuva_id) REFERENCES kuvat (id)
                      ON DELETE CASCADE
) ENGINE=INNODB; 
 * @author J-P
 */
class Lajikuvalinkki extends Malliluokkapohja{
    // Tietokannan sarakenimet (id tulee malliluokasta):
    public static $sarakenimi_kuva_id = "kuva_id";
    public static $sarakenimi_lajiluokka_id = "lajiluokka_id";
    public static $sarakenimi_jarjestysluku = "jarjestysluku";
    
    public static $taulunimi = "lajikuvalinkit";
    
    private $lajiluokkaa_muutettu;
    /**
     * Luokan muodostin:
     * 
     * @param type $id Mahdollisen tietokantaolio id, tai EI_MAARITELTY.
     * @param Tietokantaolio $tietokantaolio
     */
    function __construct($id, $tietokantaolio) {
        $tietokantasolut = 
            array(new Tietokantasolu(Lajikuvalinkki::$SARAKENIMI_ID, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Lajikuvalinkki::$sarakenimi_kuva_id, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Lajikuvalinkki::$sarakenimi_lajiluokka_id, Tietokantasolu::$luku_int,$tietokantaolio),
                new Tietokantasolu(Lajikuvalinkki::$sarakenimi_jarjestysluku, Tietokantasolu::$luku_int,$tietokantaolio));
        
        $taulunimi = Lajikuvalinkki::$taulunimi;
        parent::__construct($tietokantaolio, $id, $taulunimi, $tietokantasolut);
        $this->lajiluokkaa_muutettu = false;
    }
    /**
     * Settereitä muokataan niin, että lajiluokan muokkaus huomataan:
     * 
     * Palauttaa onnistumisen mukaan joko Pohja::$VIRHE tai 
     * Pohja::$OPERAATIO_ONNISTUI. Muuttaa muuttujan $lajiluokkaa_muutettu
     * arvoon true, jos lajiluokkaa todella muutetaan.
     * 
     * @param type $uusi
     * @param type $sarakenimi
     */
    public function set_arvo($uusi, $sarakenimi) {
        
        // Ollaan varovaisia, jos lajiluokkaa ollaan muuttamassa:
        if($sarakenimi === Lajikuvalinkki::$sarakenimi_lajiluokka_id){
            
            // Otetaan vanha arvo ylös, jotta todellinen muutos selviää:
            $vanha = $this->get_arvo($sarakenimi);
            
            // Tehdään muutos:
            $palaute = parent::set_arvo($uusi, $sarakenimi);
            
            // Verrataan vielä ja varmistetaan, että arvo on todella muuttunut
            // myös tarkistuksien läpi mentyään:
            if($vanha === $this->get_arvo($sarakenimi)){
                $palaute = Pohja::$VIRHE;
            } else{
                $this->lajiluokkaa_muutettu = true;
            }
        } else{
            $palaute = parent::set_arvo($uusi, $sarakenimi);
        }
        
        return $palaute;
    }
    /**
     * Settereitä muokataan niin, että lajiluokan muokkaus huomataan:
     * 
     * Palauttaa onnistumisen mukaan joko Pohja::$VIRHE tai 
     * Pohja::$OPERAATIO_ONNISTUI. Muuttaa muuttujan $lajiluokkaa_muutettu
     * arvoon true, jos lajiluokkaa todella muutetaan.
     * 
     * @param type $uusi
     * @param type $sarakenimi
     */
    public function set_arvo_kevyt($uusi, $sarakenimi) {
        
        // Ollaan varovaisia, jos lajiluokkaa ollaan muuttamassa:
        if($sarakenimi === Lajikuvalinkki::$sarakenimi_lajiluokka_id){
            
            // Otetaan vanha arvo ylös, jotta todellinen muutos selviää:
            $vanha = $this->get_arvo($sarakenimi);
            
            // Tehdään muutos:
            $palaute = parent::set_arvo_kevyt($uusi, $sarakenimi);
            
            // Verrataan vielä ja varmistetaan, että arvo on todella muuttunut
            // myös tarkistuksien läpi mentyään:
            if($vanha === $this->get_arvo($sarakenimi)){
                $palaute = Pohja::$VIRHE;
            } else{
                $this->lajiluokkaa_muutettu = true;
            }
        } else{
            $palaute = parent::set_arvo_kevyt($uusi, $sarakenimi);
        }
        
        return $palaute;
    }
    
    /**
     * Tallentaa uuden Lajikuvalinkki-luokan olion tietokantaan, jos kaikki on
     * kunnossa. Ennen tallennusta tarkistetaan, ettei kuvan ja lajin välillä
     * jo ole linkkiä, koska vain yksi sallitaan.
     * 
     * Palauttaa onnistumisen mukaan joko arvon Lajikuvalinkki::$VIRHE tai
     * Lajikuvalinkki::$OPERAATIO_ONNISTUI.
     * @return type
     */
    function tallenna_uusi() {
        $palaute = Lajikuvalinkki::$VIRHE;
        
        // Tarkistetaan, ettei linkkiä kyseisen kuvan ja lajin välillä jo ole:
        if(!$this->on_jo_olemassa($this->tk_taulunimi, array(
                                    Lajikuvalinkki::$sarakenimi_kuva_id,
                                    Lajikuvalinkki::$sarakenimi_lajiluokka_id))){
            $palaute = parent::tallenna_uusi();
        } else{
            $this->lisaa_virheilmoitus(Kuvatekstit::$virhe_linkin_luomisessa.
                                ": ".Kuvatekstit::$linkki_on_jo_olemassa);
        }
        
        // Palautetaan mahdollisten jatkomuutosten takia:
        $this->lajiluokkaa_muutettu = false;    
        return $palaute;
    }
    
    /**
     * Mieti vielä: voiko yhteen kuvaan liittyä linkit eri lajeihin? Periaatteessa
     * samassa kuvassahan voi olla useampia lajeja! Totta, eli näin tehdään!
     * 
     * Kuitenkin yhden lajin ja kuvan välillä saa olla vain yksi linkki. Se
     * tarkistetaan uutta linkkiä luodessa, mutta myös täällä asia pitää 
     * tarkistaa, jos lajiluokkaa on muutettu. Muussa tapauksessa tarkistuksella ei
     * ole väliä, koska linkin kuva_id-muuttujan arvoa ei voi muuttaa.
     * 
     * Palauttaa onnistumisen mukaan joko arvon Lajikuvalinkki::$VIRHE tai
     * Lajikuvalinkki::$OPERAATIO_ONNISTUI.
     * @return type
     * 
     * @return type
     */
    function tallenna_muutokset() {
        $palaute = Lajikuvalinkki::$VIRHE;
        
        // Jos lajiluokkaa on muutettu, tarkistetaan, ettei linkkiä kyseisen 
        // kuvan ja muutetun lajin välillä jo ole:
        if($this->lajiluokkaa_muutettu){
            if(!$this->on_jo_olemassa($this->tk_taulunimi, array(
                                    Lajikuvalinkki::$sarakenimi_kuva_id,
                                    Lajikuvalinkki::$sarakenimi_lajiluokka_id))){
                $palaute = parent::tallenna_muutokset();
            } else{
                $this->lisaa_virheilmoitus(Kuvatekstit::$virhe_linkin_luomisessa.
                                    ": ".Kuvatekstit::$linkki_on_jo_olemassa);
            }
        } else{
            $palaute = parent::tallenna_muutokset();
        }
        
        // Palautetaan mahdollisten jatkomuutosten takia:
        $this->lajiluokkaa_muutettu = false;    
        return $palaute;
    }
}

?>
