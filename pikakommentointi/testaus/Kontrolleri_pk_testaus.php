<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kontrolleri_pk_testaus
 * Testaa Kontrolleri_pikakommentti-luokkaa
 * @author J-P
 */
class Kontrolleri_pk_testaus extends Testiapu {

    public static $kohde_id = 10;

    private $kontrolleri;

    public function  __construct($tietokantaolio, $parametriolio) {
        parent::__construct($tietokantaolio, $parametriolio, 
                            "Kontrolleri_pikakommentit");


        $PK = Pikakommentti::$MUUTTUJAA_EI_MAARITELTY;

        $this->kontrolleri = new Pikakommenttikontrolleri($tietokantaolio,
                                                            $parametriolio,
                                                            $PK);
    }


    /**
     * Testaa metodia poista_pikakommentit()
     */
    public function testaa_pikakommenttien_poisto(){
        $palaute = "Toteutus kesken";

        return $palaute;
    }

    public function suorita_testit_ja_siivoa(){
        $this->lisaa_kommentti("Haetaan tietokannasta oikean henkilon id,
                sita tarvitaan pikakommentin viite-eheys-juttuihin", false);
        
        $this->id_testihenkilo1 = $this->hae_henkilon_id();

        $this->lisaa_kommentti("Luodaan ja tallennetaan
                    tietokantaan kolme uutta pikakommenttia.");


        $henkilo_id = $this->id_testihenkilo1;  // Todellinen!
        $kohde_id = Kontrolleri_pk_testaus::$kohde_id;    // Keksitty
        $kohde_tyyppi = Pikakommentti::$KOHDE_BONGAUS;
        $kommentti = "Kommentti 1";
        $tallennetun_id = $this->luo_ja_tallenna_pikakommentti($henkilo_id,
                                            $kohde_id,
                                            $kohde_tyyppi,
                                            $kommentti);

        // Haetaan tietokannasta ja sijoitetaan taulukkoon uusi pikakommentti:
        if($tallennetun_id != Pikakommentti::$MUUTTUJAA_EI_MAARITELTY){
            $this->lisaa_pikakommentti(new Pikakommentti($tallennetun_id,
                                                        $this->tietokantaolio));
            $this->lisaa_kommentti("Tallennus onnistui!");
        }

        $kommentti = "Kommentti 2";
        $tallennetun_id = $this->luo_ja_tallenna_pikakommentti($henkilo_id,
                                            $kohde_id,
                                            $kohde_tyyppi,
                                            $kommentti);
        // Haetaan tietokannasta ja sijoitetaan taulukkoon uusi pikakommentti:
        if($tallennetun_id != Pikakommentti::$MUUTTUJAA_EI_MAARITELTY){
            $this->lisaa_pikakommentti(new Pikakommentti($tallennetun_id,
                                                        $this->tietokantaolio));
            $this->lisaa_kommentti("Tallennus onnistui!");
        }

        $kommentti = "Kommentti 3";
        $tallennetun_id = $this->luo_ja_tallenna_pikakommentti($henkilo_id,
                                            $kohde_id,
                                            $kohde_tyyppi,
                                            $kommentti);

        // Haetaan tietokannasta ja sijoitetaan taulukkoon uusi pikakommentti:
        if($tallennetun_id != Pikakommentti::$MUUTTUJAA_EI_MAARITELTY){
            $this->lisaa_pikakommentti(new Pikakommentti($tallennetun_id,
                                                        $this->tietokantaolio));
            $this->lisaa_kommentti("Tallennus onnistui!");
        }



        // POistetaan kaikki pikakommentit, joilla sama kohde_id ja kohde_tyypi:
        $palaute = $this->kontrolleri->poista_pikakommentit($this->tietokantaolio,
                                                            $kohde_tyyppi,
                                                            $kohde_id);

        if($palaute==3){
            $this->lisaa_kommentti("Poisto onnistui! $palaute
                                        pikakommenttia kolmesta poistettu!");
        }
        else{
            $this->lisaa_kommentti("Virhe poistossa! Vain $palaute
                                        pikakommenttia poistettu, vaikka
                                        kolme piti poistaa!!",true);
        }
        //======================================================================
        $uusi = "<h3>Kontrolleritestit suoritettu!</h3>";
        $this->lisaa_kommentti($uusi, false);

        $this->siivoa_jaljet();

    }

    /**
     * Siivoaa tietokannasta kaikki tallenteet:
     */
    public function siivoa_jaljet(){
        $this->lisaa_kommentti(
                   "<h4>Tietokannan siivous:</h4>");

        $lkm = $this->tietokantaolio->poista_kaikki_rivit("henkilot",
                                                    "sukunimi",
                            Pikakommenttitestaus::$sukun_testihenkilo1);
        $this->lisaa_kommentti(
                    "Tietokannasta poistettu ".$lkm." henkiloa.");

        $lkm = $this->tietokantaolio->poista_kaikki_rivit("pikakommentit",
                                                    "kohde_id",
                            Pikakommenttitestaus::$kohteen_testi_id);

        if($lkm == 0){
            $this->lisaa_kommentti(
                    "Tietokannasta ei loytynyt testipikakommenttia, koska se
                        poistui viimeistaan automaattisesti henkilon
                        poiston yhteydessa (cascade - delete -juttu). OIKEIN!");
        }
        else{
            $lisaa_virheilm = true;
            $uusi = "Virhe: pikakommentit eivät olleet hävinneet, vaikka näin
                pitäisi käydä, kun henkilö hävitetään.";
            $this->lisaa_kommentti($uusi, $lisaa_virheilm);
        }
    }

}
?>
