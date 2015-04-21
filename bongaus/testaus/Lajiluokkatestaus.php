<?php
require_once 'Testiapu_bongaus.php';
/**
 * Description of Pikakommenttitestaus
 * Testaa Lajiluokka-luokkaa. Kuvaus-oliot liittyvät aina lajiluokkaan, joten
 * ehkä niiden testailun voisi yhdistää tänne.
 * 
 * HUOM! Saman nimen_latina tallennus toiselle lajille ei onnistu, mutta tätä
 * toimintoa ei ole testattu täällä! Samoin poiston yhteydessä ei ole testattu
 * sitä, estävätkö lajiin kohdistuvat havainnot poistamisen (pitäisi estää).
 *
 * @author J-P
 */
class Lajiluokkatestaus extends Testiapu_bongaus{
    /**
     * Näin Editori hoksaa, mistä oliosta kysymys.
     * @var Lajiluokka
     */
    private $muokattava, $poistettava;

    public static $lj1_ylaluokka_id = -1;
    public static $lj1_nimi_latina = "testilatina";
 
    // Toisen testilajiluokan on tarkoitus olla ekan alla
    public static $lj2_nimi_latina = "testilatina2";
    
    public static $lj2_nimi_latina_muutettu = "testilatina2_muutetttu";
    /**
     * @param Tietokantaolio $tietokantaolio
     */
    function  __construct($tietokantaolio, $parametriolio) {
        parent::__construct($tietokantaolio, $parametriolio, "Lajiluokka");
        
        $this->id_testihenkilo1 = -1;
    }

    public function testaa(){
       
        // TEstataan lajiluokan luomista, muokkausta ja poistoa
        // (ja samalla muitakin metodeja, kuten onTallennuskelpoinen-metodi):
        $this->testaa_lajiluokan_luominen();
        $this->testaa_lajiluokan_muokkaus();
        //$this->testaa_naytot();
        $this->testaa_lajiluokan_poisto();


        $this->siivoa_jaljet();
    }

    /**
     * Testaa uuden lajiluokan luomista ja tallentamista. 
     */
    public function testaa_lajiluokan_luominen(){

        $this->lisaa_lihava_kommentti("Testataan lajiluokan luominen");

        //================== Alkusiivous =======================================
        // Poistetaan mahdolliseta aiempien testien roskat, joita on voinut
        // jäädä, kun testi on keskeytynyt:
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                            Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Lajiluokkatestaus::$lj1_nimi_latina);
        
        $lkm2 = $this->tietokantaolio->poista_kaikki_rivit(Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Lajiluokkatestaus::$lj2_nimi_latina);
        
        $lkm3 = $this->tietokantaolio->poista_kaikki_rivit(Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Lajiluokkatestaus::$lj2_nimi_latina_muutettu);

        $poistettujen_lkm = $lkm+$lkm2+$lkm3;
        if($poistettujen_lkm > 0){
            $this->lisaa_kommentti(
                    $poistettujen_lkm." vanhaa lajiluokkaa poistettu");
        }
        //======================= Alkusiivous päättyi===========================
        
        // Lajiluokka hierarkkian päällä (esim "Linnut")
        $uuden_id = $this->luo_ja_tallenna_lajiluokka(
                            Lajiluokkatestaus::$lj1_ylaluokka_id, 
                            Lajiluokkatestaus::$lj1_nimi_latina);
        
        // Haetaan tallennettu olio tietokannasta:
        $lj1 = new Lajiluokka($this->tietokantaolio, $uuden_id);
        
        // Lajiluokka hierarkkian toisessa kerroksessa (esim. "harakka").
        // Yritetään tallentaa sama latina toiseen kertaan, minkä ei pitäisi
        // onnistua:
        $this->lisaa_kommentti("Yritetaan tallentaa uusi lajiluokka,
            jolla on sama nimi_latina kuin edellisella. Ei pitaisi onnistua.");
        $uuden_id2 = $this->luo_ja_tallenna_lajiluokka(
                            $lj1->get_id(), 
                            Lajiluokkatestaus::$lj1_nimi_latina);
        
        if($uuden_id2 === Lajiluokka::$MUUTTUJAA_EI_MAARITELTY){
            $this->lisaa_ilmoitus("Tuplalatina ei mennyt l&auml;pi.",false);
        }
        else{
            $this->lisaa_ilmoitus("Tuplalatina meni l&auml;pi, vaikka".
                " ei saisi!",true);
        }
        
        $this->lisaa_ilmoitus("Tallennetaan nyt eri latinanimella, jolloin
            tallennuksen pitaisi onnistua.",false);
        
        // Tallennetaan nyt eri lajina:
        $uuden_id2 = $this->luo_ja_tallenna_lajiluokka(
                            $lj1->get_id(), 
                            Lajiluokkatestaus::$lj2_nimi_latina);
        // Haetaan tallennettu olio tietokannasta:
        $lj2 = new Lajiluokka($this->tietokantaolio, $uuden_id2);
        
        //======================================================================
        // Lisätään taulukkoon, jos löydetty tietokannasta:
        if($lj1->olio_loytyi_tietokannasta){
            
            // Tyhjennetään vanhat virheilmoitukset.
            $this->tyhjenna_virheilmoitukset();
            
            $this->lisaa_lajiluokka($lj1);
            if(sizeof($this->lajiluokat) == 1){
                $this->lisaa_ilmoitus("Uusi lajiluokka lisatty
                taulukkoon!",false);
            }
            else{
                $this->lisaa_virheilmoitus("Virhe uuden lajiluokan lisayksessa
                    taulukkoon! (olioita ".sizeof($this->lajiluokat)." kpl)");
            }
        }
        else{
            $this->lisaa_ilmoitus("Lajiluokan haku tietokannasta".
                    " ei onnistunut! <br />".
                    $lj1->tulosta_virheilmoitukset(),true);
        }
        if($lj2->olio_loytyi_tietokannasta){
            
            // Tyhjennetään vanhat virheilmoitukset.
            $this->tyhjenna_virheilmoitukset();
            
            $this->lisaa_lajiluokka($lj2);
            if(sizeof($this->lajiluokat) == 2){
                $this->lisaa_ilmoitus("Toinen uusi lajiluokka lisatty
                taulukkoon!",false);
            }
            else{
                $this->lisaa_virheilmoitus("Virhe toisen uuden lajiluokan lisayksessa
                    taulukkoon! (olioita ".sizeof($this->lajiluokat)." kpl)");
            }
            $this->lisaa_ilmoitus("Testataan, onko 2. uuden olion latina ok",false);

            if($lj2->get_nimi_latina() == Lajiluokkatestaus::$lj2_nimi_latina){
                $this->lisaa_ilmoitus("Latina ok!",false);
            }
            else{
                $this->lisaa_ilmoitus("Virhe tietojen haussa tietokannasta!",false);
                $this->lisaa_virheilmoitus("Virhe nimi_latinassa!".
                    " (nimi_latina: ".$lj2->get_nimi_latina().")");
            }
        }
        else{
            $this->lisaa_ilmoitus("Lajiluokan (2:n) haku tietokannasta".
                    " ei onnistunut! <br />".
                    $lj1->tulosta_virheilmoitukset(),true);
        }

        
        //======================================================================
        $this->lisaa_ilmoitus("<h4>Lajiluokan luomistesti loppui!</h4>",false);
    }
    
    //==========================================================================
    //==========================================================================
    
    
    /**
     *  Testaa lajiluokan muokkausta:
     */
    public function testaa_lajiluokan_muokkaus(){
        $this->lisaa_ilmoitus("<h4>Lajiluokan muokkaustesti alkaa</h4>",false);

        $this->lisaa_ilmoitus("Otetaan muokattavaksi viimeksi luotu
            lajiluokka",false);
        $this->muokattava = $this->lajiluokat[1];

        //======================================================================
        // Testataan ennen muutoksia onTallennuskelpoinen-metodi, jonka
        // pitäisi valittaa:
        $this->lisaa_ilmoitus("Testataan ennen muutoksia
            onTallennuskelpoinen-metodi, jonka pitaisi valittaa:",false);
        $uusi = false;
        if($this->muokattava->on_tallennuskelpoinen(false)){
            $this->lisaa_ilmoitus("Virhe: samoja tietoja ei pida
                paastaa muokkaamaan!",true);
        }
        else{
            $this->lisaa_ilmoitus("Oikein:  samoja tietoja ei pida
                paastaa muokkaamaan! Kommentit: ".
                    $this->muokattava->tulosta_virheilmoitukset(),false);
        }

        $this->muokattava->tyhjenna_virheilmoitukset();
        //======================================================================
        // ASetetaan pikakommentille tahallaan vääriä arvoja:
        $this->muokattava->set_nimi_latina("");
        
        // Yritetään muuttaa ylaluokka_id:tä:
        $onnistumispalaute = $this->muokattava->set_ylaluokka_id(345);
        
        //ylaluokka_id:tä ei pitäisi pystyä muokkaamaan:
        if($onnistumispalaute == false){
            $this->lisaa_ilmoitus("Ylaluokka_id:n muutos ei
                onnistunut (OIKEIN)",false);
        } else{
            $this->lisaa_ilmoitus("Virhe: Ylaluokka_id:n muutos
                meni lapi!",true);
        }
        $this->muokattava->tyhjenna_virheilmoitukset();
        
        $this->lisaa_ilmoitus("Asetetaan nimi_latinaksi tyhj&auml;.".
                " Talloin onTallennuskelpoinen-metodin pitaisi valittaa.",false);

        if(!$this->muokattava->on_tallennuskelpoinen(false)){
            $this->lisaa_ilmoitus("Tyhj&auml; latina ei mennyt l&auml;pi (OIKEIN).".
                    " Saatiin seuraava ilmoitus:".
                    $this->muokattava->tulosta_virheilmoitukset(),false);
        }else{
            $this->lisaa_ilmoitus("Virhe: Tyhj&auml; latina meni l&auml;pi!",true);
        }
        $this->muokattava->tyhjenna_virheilmoitukset();
        //======================================================================
        
         $this->lisaa_ilmoitus("Asetetaan nimi_latinaksi jo kaytossa oleva.".
                " Talloin onTallennuskelpoinen-metodin pitaisi valittaa.",false);

        $this->muokattava->set_nimi_latina(Lajiluokkatestaus::$lj1_nimi_latina);
        
        $palaute = $this->muokattava->tallenna_muutokset();
        if($palaute != Lajiluokka::$OPERAATIO_ONNISTUI){
            $this->lisaa_ilmoitus("Tuplalatina ei mennyt l&auml;pi (OIKEIN).".
                    " Saatiin seuraava ilmoitus:".
                    $this->muokattava->tulosta_virheilmoitukset(),false);
        }else{
            $this->lisaa_ilmoitus("Virhe: Tuplalatina meni l&auml;pi!",true);
        }
        //======================================================================
        // ASetetaan nyt muutettavaksi hyviä arvoja:
        $this->muokattava->set_nimi_latina(Lajiluokkatestaus::$lj2_nimi_latina_muutettu);
        $this->lisaa_ilmoitus("Muutetaan latinaa laillisesti.",false);

        // Virheilmoituksia ei pitäisi tulla:
        if($this->muokattava->on_tallennuskelpoinen(false)){
            $this->lisaa_ilmoitus("Oikein! ARvot puhtaita!",false);
        }
        else{
            $this->lisaa_ilmoitus("Virhe:  Tiedoissa olevinaan virheita:<br/>".
                $this->muokattava->tulosta_virheilmoitukset(),false);
        }
        //======================================================================
        
        // Kokeillaan sitten tallentaa muuttuneet tiedot:
        $this->lisaa_ilmoitus("Kokeillaan tallentaa muutokset:",false);
        $tallennuspalaute = $this->muokattava->tallenna_muutokset();
        if($tallennuspalaute == Lajiluokka::$OPERAATIO_ONNISTUI){
             $this->lisaa_ilmoitus("Muutosten tallennus onnistui!",false);

             // Kokeillaan hakea sama tietokannasta ja varmistetaan, että
             // muutettu kommentti on todella muuttunut:
             $testi = new Lajiluokka($this->tietokantaolio,
                                        $this->muokattava->get_id());
             if($testi->get_nimi_latina() == Lajiluokkatestaus::$lj2_nimi_latina_muutettu){
                 $this->lisaa_ilmoitus("Muutokset oikein tietokannassa!
                     Nimi_latina on nykyaan: ".$testi->get_nimi_latina(),false);
             }
             else{
                 $this->lisaa_ilmoitus("Muutokset vaarin tietokannassa!
                     Nimi_latina on tietokannassa: ".$testi->get_nimi_latina(),true);
             }

        }else{
             $this->lisaa_ilmoitus($tallennuspalaute,true);
        }
        //======================================================================

        $this->lisaa_ilmoitus("<h4>Lajiluokan muokkaustesti loppui</h4>",
                false);
    }

    public function testaa_naytot(){
        $this->lisaa_ilmoitus("<h4>Testataan nayttamismetodit:</h4>",
                false);
        // Näytetään lajiluokka:
        $naytettava = $this->lajiluokat[0];

        $omaid = 100;
        $kayttajan_valtuudet = Valtuudet::$HALLINTA;
        $html = $naytettava->nayta_lajiluokka($omaid, $kayttajan_valtuudet);
        $html .= $naytettava->nayta_poistovahvistuskysely();
        $this->lisaa_ilmoitus($html,false);
    }

    public function testaa_lajiluokan_poisto(){
        $this->lisaa_ilmoitus("<h4>Lajiluokan poistotesti alkaa</h4>",
                false);

        $this->poistettava = $this->lajiluokat[0];

        $this->lisaa_ilmoitus("Yritetaan poistaa lajiluokka, jolla
            on aliluokka, minka ei pitaisi onnistua!",false);
        $palaute = $this->poistettava->poista();

        if($palaute == Lajiluokka::$OPERAATIO_ONNISTUI){
            $this->lisaa_ilmoitus("Virhe! Poisto onnistui,
                vaikka lajiluokalla aliluokkia! ",true);
        }
        else{
            $this->lisaa_ilmoitus("Poisto ei onnistunut, koska lajiluokalla
                on aliluokkia (OIKEIN)!".
                    $palaute, false);
        }
        // Onnistuva poistotesti:
        // Otetaan id talteen:
        $id_poistettava = $this->lajiluokat[1]->get_id();
        $this->poistettava = $this->lajiluokat[1];
        
        $this->lisaa_ilmoitus("Yritetaan poistaa lajiluokka, jolla
            ei ole aliluokkia, minka pitaisi onnistua!",false);
        $palaute = $this->poistettava->poista();
        
        if($palaute == Lajiluokka::$OPERAATIO_ONNISTUI){
            $this->lisaa_ilmoitus("Poisto onnistui!",false);
            
            $this->lisaa_ilmoitus("Tehdaan viela tarkistus tietokannasta:",
                                        false);

            // TArkistetaan vielä tietokanta:
            $lkm = $this->tietokantaolio->hae_osumien_lkm(Lajiluokka::$taulunimi, 
                                            Lajiluokka::$SARAKENIMI_ID, 
                                            $id_poistettava);
            if($lkm == 0){
                $this->lisaa_ilmoitus("OK! Tietokannasta ei
                    loytynyt poistettua lajiluokkaa",false);
            }
            else{
                $this->lisaa_ilmoitus("Virhe! Tietokannasta
                    loytyi poistetun id:lla ".$lkm." lajiluokkaa",true);
            }
        }
        else{
            $this->lisaa_ilmoitus("Poisto epaonnistui! ".
                    $palaute, true);
        }

        $this->lisaa_ilmoitus("<h4>Lajiluokan poistotesti loppui</h4>",
                false);
    }

    

    /**
     * Siivoaa tietokannasta kaikki tallenteet:
     */
    public function siivoa_jaljet(){
        $this->lisaa_ilmoitus(
                   "<h4>Tietokannan siivous:</h4>",false);

        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                            Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Lajiluokkatestaus::$lj1_nimi_latina);
        
        $lkm2 = $this->tietokantaolio->poista_kaikki_rivit(Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Lajiluokkatestaus::$lj2_nimi_latina);
        
        $lkm3 = $this->tietokantaolio->poista_kaikki_rivit(Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Lajiluokkatestaus::$lj2_nimi_latina_muutettu);

        if($lkm+$lkm2+$lkm3 == 1){
            $this->lisaa_ilmoitus(
                    "Yksi lajiluokka poistettu (OIKEIN)",false);
        }
        else{
            $lisaa_virheilm = true;
            $lkm = $lkm+$lkm2+$lkm3;
            $uusi = "Virhe: Poistettu ".$lkm." lajiluokkaa!";
            $this->lisaa_ilmoitus($uusi, $lisaa_virheilm);
        }
    }

    
    
    

}
?>
