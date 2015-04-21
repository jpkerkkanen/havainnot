<?php


/*
 * Käyttäjävaltuuksien (kokonaisuudet) arvot, vastaavat kuvaukset
 * ja erinäisiä metodeita.
 * static-muuttujiin pääsee käsiksi muualtakin suoraan luokan nimen avulla
 * ilman olion luomista.
 *
 * $HALLINTA = 100; // Kaikki oikeudet.
 * $POPPOON_JOHTAJA = 20 // Poppoon johtaja: voi muokata poppootunnusta.
 * $NORMAALI = 10; // Tavallisen käyttäjän oikeudet.
 * $RAJOITETTU = 5; // Ei määritelty nyt.
 * $PANNASSA = 0; // Käyttökiellossa, vaikka omistaa tunnukset.
 */
class Valtuudet{
    
    private $arvo, $kuvaus;
    
    public static $HALLINTA = 100; //
    public static $POPPOON_JOHTAJA = 20;
    public static $NORMAALI = 10; //
    public static $RAJOITETTU = 5; //
    public static $PANNASSA = 0;  // Käyttäjä ei pääse näkemään mitään.
    
    // Kun kuvausta haetaan olemattomaan arvoon:
    public static $VIRHEELLINEN_ARVO = "Valtuusarvo virheellinen";

    private function __construct($arvo, $kuvaus) {
        $this->arvo = $arvo;
        $this->kuvaus = $kuvaus;
    }
    
    /**
     * Palauttaa taulukon, jossa on kaikki mahdolliset valtuusoliot eli
     * tarkemmin Valtuudet-luokan oliot:
     */
    public static function hae_valtuudet(){
        $oliot = array();
        
        array_push($oliot, new Valtuudet(Valtuudet::$HALLINTA, 
                                        "Yll&auml;pit&auml;j&auml;"));
        array_push($oliot, new Valtuudet(Valtuudet::$POPPOON_JOHTAJA, 
                                        "Poppoon johtaja"));
        array_push($oliot, new Valtuudet(Valtuudet::$NORMAALI, 
                                        "Perusk&auml;ytt&auml;j&auml;"));
        array_push($oliot, new Valtuudet(Valtuudet::$RAJOITETTU, 
                                        "Rajoitetut oikeudet"));
        array_push($oliot, new Valtuudet(Valtuudet::$PANNASSA, 
                                        "P&auml;&auml;sy kielletty!"));
        
        return $oliot;
    }
    
    /**
     * @return array Palauttaa taulukon, joka sisältää valtuuksien numeroarvot
     */
    public static function hae_valtuusarvot(){
        $valtuudet = Valtuudet::hae_valtuudet();
        $arvot = array();
        
        foreach ($valtuudet as $valtuusolio) {
            array_push($arvot, $valtuusolio->arvo);
        }
        
        return $arvot;
    }

    /**
     * Palauttaa käyttöoikeusarvoja vastaavat (samassa järjestyksessä)
     * kuvaukset taulukkona. Tarkastaa myös sen, onko
     * nimiä ja arvoja yhtä monta ja kielteisessä tapauksessa heittää
     * poikkeuksen vastaanottavalle ohjelmanosalle.
     * @return <type> Palauttaa taulukon, joka sisältää suojausasetusten kuvauksen.
     */
    public static function hae_valtuuskuvaukset(){
        $valtuudet = Valtuudet::hae_valtuudet();
        $kuv = array();
        
        foreach ($valtuudet as $valtuusolio) {
            array_push($kuv, $valtuusolio->kuvaus);
        }
        
        return $kuv;
    }
    /**
     * Palauttaa lukua eli valtuusindeksiä vastaavan valtuuskuvauksen, tai
     * tekstin Valtuudet::$VIRHEELLINEN_ARVO, jos parametri on sopimaton.
     * @param <type> $arvo
     * @return <type>
     */
    public static function hae_valtuuden_kuvaus($arvo){
        $kuvaus = Valtuudet::$VIRHEELLINEN_ARVO;

        // Jos parametri on ok, haetaan oppiaineen nimi. Jos huomataan
        // jotakin outoa, palautetaan virheviesti.
        if(is_numeric($arvo) && ($arvo > -1)){
            $valtuudet = Valtuudet::hae_valtuudet();
            foreach ($valtuudet as $valtuusolio) {
                if($valtuusolio->arvo == $arvo){
                    $kuvaus = $valtuusolio->kuvaus;
                }
            }
        }
        return $kuvaus;
    }
    
    public function getArvo(){
        return $this->arvo;
    }
    public function getKuvaus(){
        return $this->kuvaus;
    }
}

?>
