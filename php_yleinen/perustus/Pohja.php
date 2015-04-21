<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pohja
 *
 * Toimii pohja-luokkien hierarkian pohjana, eli kaikki pohja-luokat yleensä perivät
 * tämän. Sisältää yleisimpiä vakioita, ilmoitus-koneiston yms.
 * 
 * @author kerkjuk_admin
 */
abstract class Pohja{
    /** Tänne niin virhe- kuin muutkin ilmoitukset */
    protected $ilmoitukset;
    
    /**
     * Olio voidaan luoda niin, ettei muuttujia ole määritelty (esim. uusi olio).
     * Tällöin olennaiset muuttujat (not null ja id) täytyy määritellä, e
     * nnen kuin oliota on järkeä tallentaa tietokantaan. Tämä vakio on
     * merkkinä siitä, ettei muuttujaa ole määritelty.
     *
     * Älä muuta tätä (ainakaan positiiviseksi), ettei sekaannu.
     */
    public static $MUUTTUJAA_EI_MAARITELTY = -2;
    
    /**
     * Virheellinen arvo viittaa siihen, että käyttäjä tai jokin on antanut
     * olion muuttujalle vääräntyyppisen arvon.
     */
    public static $ARVO_VAARANTYYPPINEN = -22;
    
    /**
     * viittaa siihen, että käyttäjä tai jokin on antanut
     * olion muuttujalle tyhjän arvon. Tätä käytetään silloin, kun tyhjä arvo
     * ei ole sallittu.
     */
    public static $ARVO_TYHJA = -23;

    // Palaute, kun toiminto on onnistunut:
    public static $OPERAATIO_ONNISTUI = "KAIKKI_OK";

    // Palaute, kun toiminto on epäonnistunut:
    public static $VIRHE = "VIRHE";
    
    /**
     * Olion rakentaja. Täällä alustetaan ilmoitukset-taulukko (array)
     * tyhjäksi taulukoksi.
     */
    protected function __construct(){
        $this->ilmoitukset = array();
    }

    /**
     * Palauttaa taulukon, joka sisältää yksittäiset ilmoitukset.
     * @return type
     */
    public function get_ilmoitukset(){
        return $this->ilmoitukset;
    }
    
    /**
     * Lisää merkkijonon ilmoituksiin. Parametri 2 määrää ilmoituksen tyypin,
     * joka löytyy Ilmoitus-luokasta (staattinen muuttuja).
     * @param type $ilmoitus
     * @param type $tyyppi
     */
    public function lisaa_ilmoitus($ilmoitus, $tyyppi) {
        Ilmoitus::lisaa_ilmoitus($ilmoitus, $tyyppi, $this->ilmoitukset);
    }

    public function lisaa_virheilmoitus($ilmoitus) {
        Ilmoitus::lisaa_virheilmoitus($ilmoitus, $this->ilmoitukset);
    }

    public function tulosta_kaikki_ilmoitukset() {
        return Ilmoitus::tulosta_kaikki_ilmoitukset($this->ilmoitukset);
    }

    public function tulosta_virheilmoitukset() {
        return Ilmoitus::tulosta_virheilmoitukset($this->ilmoitukset);
    }
    
    public function tulosta_viimeisin_virheilmoitus() {
        return Ilmoitus::tulosta_viimeisin_virheilmoitus($this->ilmoitukset);
    }

    public function tyhjenna_kaikki_ilmoitukset() {
        Ilmoitus::tyhjenna_kaikki_ilmoitukset($this->ilmoitukset);
    }

    public function tyhjenna_virheilmoitukset() {
        Ilmoitus::tyhjenna_virheilmoitukset($this->ilmoitukset);
    }

    public function virheilmoitusten_lkm() {
        return Ilmoitus::virheilmoitusten_lkm($this->ilmoitukset);
    }
    /**
     * Lisää positiivisen kommentin eli epävirheilmoituksen.
     * @param type $kommentti
     */
    public function lisaa_kommentti($kommentti){
        Ilmoitus::lisaa_ilmoitus($kommentti, Ilmoitus::$TYYPPI_ILMOITUS, 
                                $this->ilmoitukset);
    }
    
    /**
     * Lisää positiivisen kommentin eli epävirheilmoituksen lihavoituna.
     * @param type $kommentti
     */
    public function lisaa_lihava_kommentti($kommentti){
        $kommentti = "<b>".$kommentti."</b>";
        $this->lisaa_kommentti($kommentti);
    }
}

?>
