<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kuvatestaus
 *
 * create table kuvat
(
  id                    int auto_increment not null,
  henkilo_id            int not null,
  kuvaotsikko		varchar(200),
  kuvaselitys		varchar(5000),
  vuosi                 smallint default 0,
 
  kk                    tinyint default 0,
  paiva                 tinyint default 0,
  src                   varchar(300),
  leveys                smallint not null,
  korkeus               smallint not null,
 
  tiedostokoko          int not null,
  tiedostotunnus        varchar(20) not null,
  tiedostonimi          varchar(100) not null,
  tallennusaika_sek     int default 0,
  muutosaika_sek        int default 0,
 * 
 * @author J-P
 */
class Kuvatestaus extends Testiapu_kuvat{
    /**
     * Näin Editori hoksaa, mistä oliosta kysymys.
     * @var Kuva
     */
    private $kuva1; /** @var Kuva */
    private $kuva2; /** @var Kuva */
    private $ladattu_kuva;

    // testiarvoja
    public static $testivuosi = 1234; // Tästä tunnistetaan testikuvat..
    
    public static $testikuvatiedostokansion_osoite = "testikuvatiedostot";
    public static $testikuvalatauskansion_osoite = 
            "testikuvatiedostot/testilataukset";
 
    // png-testikuvan osoite:
    public static $koti1710png = "testikuvatiedostot/koti_1710kt_png.png";
    
    /**
     * @param Tietokantaolio $tietokantaolio
     */
    function __construct($tietokantaolio, $parametriolio){
        parent::__construct($tietokantaolio, $parametriolio, "Kuva");
        
        $this->kuva1 = Kuva::$MUUTTUJAA_EI_MAARITELTY;
        $this->kuva2 = Kuva::$MUUTTUJAA_EI_MAARITELTY;
        
        $this->ladattu_kuva = Kuvatestaus::$koti1710png;
    }

    // Setterit ja getterit:
    public function get_ladattu_kuva(){
        return $this->ladattu_kuva;
    }
    public function set_ladattu_kuva($kuva){
        $this->ladattu_kuva = $kuva;
    }
    
    
    /**
     * Testaa uuden kuvan luomista ja tallentamista.
     */
    public function testaa_kuvan_luominen(){
        
        $this->lisaa_ilmoitus("<h4>Testataan kuvan luominen</h4>",false);

        $latauskansion_osoite = "kuvat/testaus/testikuvatiedostot/testilataukset";
        $id = Kuva::$MUUTTUJAA_EI_MAARITELTY;
        $kuva = new Kuva($id, $this->tietokantaolio);
        
        // Asetetaam vuodeksi luku, jonka perusteella testikuvat löydetään:
        $kuva->set_arvo(Kuvatestaus::$testivuosi, Kuva::$SARAKENIMI_VUOSI);
        
        if($kuva->get_arvo(Kuva::$SARAKENIMI_VUOSI) === Kuvatestaus::$testivuosi){
            $this->lisaa_ilmoitus("Kuvaolio luotu ja vuodeksi asetettu luku ".
                Kuvatestaus::$testivuosi, Ilmoitus::$TYYPPI_ILMOITUS);
        }
        
        // Asetetaan hyviä arvoja:
        $kuva->set_arvo("png", Kuva::$SARAKENIMI_TIEDOSTOTUNNUS);
        $kuva->set_arvo("testi", Kuva::$SARAKENIMI_TIEDOSTONIMI);
        $kuva->set_arvo(2000, Kuva::$SARAKENIMI_TIEDOSTOKOKO);
        $kuva->set_arvo(1233, Kuva::$SARAKENIMI_TALLENNUSHETKI_SEK);
        $kuva->set_arvo("source", Kuva::$SARAKENIMI_SRC);
        
        $kuva->set_arvo(12, Kuva::$SARAKENIMI_PAIVA);
        $kuva->set_arvo(0, Kuva::$SARAKENIMI_MUUTOSHETKI_SEK);
        $kuva->set_arvo(123, Kuva::$SARAKENIMI_LEVEYS);
        $kuva->set_arvo("hassu", Kuva::$SARAKENIMI_KUVASELITYS);
        $kuva->set_arvo("hassu", Kuva::$SARAKENIMI_KUVAOTSIKKO);
        
        $kuva->set_arvo(200, Kuva::$SARAKENIMI_KORKEUS);
        $kuva->set_arvo(11, Kuva::$SARAKENIMI_KK);
        $kuva->set_arvo(1, Kuva::$SARAKENIMI_HENKILO_ID);
        
        // Testataan ilman lataustarkistusta:
        if($kuva->tallenna_uusi() === 
            Kuva::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Kuvatietojen tallennus tietokantaan ok.".
                    " Tiedostoa ei vielä mukana.");
        } else{
            $this->lisaa_virheilmoitus("Virhe kuvan tallennuksessa tietokantaan!".
                    $kuva->tulosta_virheilmoitukset());
        }
        
        $this->lisaa_virheilmoitus("Kuvatestit kesken!");
        
        return $kuva;
    }
    
        /**
     * Kutsuu kaikkia kuviin liittyviä testejä ja palauttaa Testipalaute 
     * -luokan olion.
     * @param Tietokantaolio $tietokantaolio
     * @return Testipalaute $testipalaute
     */
    function toteuta_kuvatestit() {
        
        $ots = "Testataan Kuva-luokan metodeita.";
        $this->tee_alkusiivous();
        //=====================================================================
        $this->testaa_kuvan_luominen();
        //$this->testaa_kuvan_muokkaus();
        //$this->testaa_kuvan_poisto();

        $this->siivoa_jaljet();
        //=====================================================================

        $virheilm = $this->tulosta_virheilmoitukset();
        $sis = $this->tulosta_kaikki_ilmoitukset();
        $virheilm_lkm = $this->virheilmoitusten_lkm();
        
        
        $palaute = new Testipalaute($ots, $virheilm, $sis, $virheilm_lkm);
        return $palaute;
        //==========================================================================
    }
    
}


?>