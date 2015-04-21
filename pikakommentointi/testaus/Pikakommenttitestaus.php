<?php
require_once 'Testiapu.php';
/**
 * Description of Pikakommenttitestaus
 * Testaa Pikakommentti-luokkaa.
 *
 *
 * @author J-P
 */
class Pikakommenttitestaus extends Testiapu{

    /**
     * Näin Editori hoksaa, mistä oliosta kysymys.
     * @var Pikakommentti
     */
    private $muokattava, $poistettava;

    public static $kohteen_testi_id = 1;    // Ei tarvitse olla olemassa.

    /**
     * @param Tietokantaolio $tietokantaolio
     */
    function  __construct($tietokantaolio, $parametriolio) {
        parent::__construct($tietokantaolio, $parametriolio, "Pikakommentti");
        
        
        $this->id_testihenkilo1 = -1;
    }

    public function testaa(){
        //Luodaan uusi testihenkilö;
        $this->luo_testihenkilo1();

        // Haetaan henkilön nimi:
        $this->hae_henkilon_nimi($this->id_testihenkilo1);

        // TEstataan pikakommentin luomista, muokkausta ja poistoa
        // (ja samalla muitakin metodeja, kuten onTallennuskelpoinen-metodi):
        $this->testaa_pikakommentin_luominen();
        $this->testaa_pikakommentin_muokkaus();
        $this->testaa_naytot();
        $this->testaa_pikakommentin_poisto();


        $this->siivoa_jaljet();
    }

    public function testaa_pikakommentin_luominen(){

        $this->lisaa_kommentti("<h4>Testataan pikakommenttin luominen</h4>");
        $id = Pikakommentti::$MUUTTUJAA_EI_MAARITELTY;
        $pika = new Pikakommentti($id, $this->tietokantaolio);

        $this->lisaa_kommentti("Uusi tyhja pikakommentti luotu!");

        // Ei pitäisi olla tallennuskelpoinen:
        $palaute = $pika->tallenna_uusi();
        if($palaute == Pikakommentti::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Ei pitaisi olla tallennuskelpoinen!");
        }
        else{
            $this->lisaa_kommentti("Ei ole tallennettava, joten".
                    " saatiin aivan oikein seuraava palaute:".
                    $pika->tulosta_virheilmoitukset());
                    $pika->tyhjenna_virheilmoitukset();
        }

        //======================================================================
        $this->lisaa_kommentti("Asetetaan pikakommentin henkilo_id,
                        kohde_id, kohde_tyyppi ja kommentti, sekä tallennus-ja
                        muokkauspäivämäärät. Testataan
                        uudelleen, onko nyt tallennuskelpoinen:");

        //$pika->set_henkilo_id($this->id_testihenkilo1);
        $pika->set_arvo($this->id_testihenkilo1, Pikakommentti::$SARAKENIMI_HENKILO_ID);
        $pika->set_kohde_id(Pikakommenttitestaus::$kohteen_testi_id);
        $pika->set_kohde_tyyppi(Pikakommentti::$KOHDE_BONGAUS);
        $pika->set_kommentti("Ihan hassu suoritus!");
        $pika->set_arvo(time(), Pikakommentti::$SARAKENIMI_TALLENNUSHETKI);
        $pika->set_arvo(0, Pikakommentti::$SARAKENIMI_MUOKKAUSHETKI);

        // Nyt pitäisi olla kelvollinen tallennukseen:
        $palaute = $pika->tallenna_uusi();
        if($palaute == Pikakommentti::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Pikakommentti on nyt tallennettu!");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe uuden tallennuksessa: ei tallennuskelpoinen!".
                    " Nykyinen arvo: ".$pika->get_muokkaushetki_sek());
            $this->lisaa_virheilmoitus("Palaute:".
                    $pika->tulosta_virheilmoitukset());
        }

        //======================================================================
        // Lisätään taulukkoon:
        $this->lisaa_pikakommentti($pika);
        if(sizeof($this->pikakommentit) == 1){
            $this->lisaa_kommentti("Uusi pikakommentti lisatty
            taulukkoon!");
        }
        else{
            $this->lisaa_virheilmoitus("Virhe uuden pikakommentin lisayksessa
                taulukkoon! (olioita ".sizeof($this->pikakommentit)." kpl)");
        }

        //======================================================================
        // Etsitään pikakommentit tietokannasta (voi olla useampia)

        $hakulause = "SELECT * FROM pikakommentit
                        WHERE kohde_id=".Pikakommenttitestaus::$kohteen_testi_id;
                                                   
        $tk_oliot = $this->tietokantaolio->
                            tee_omahaku_oliotaulukkopalautteella($hakulause);

        if(sizeof($tk_oliot) > 0){
             $this->lisaa_kommentti("Testipikakommentteja loytyi ".
                sizeof($tk_oliot)." kpl tietokannasta.");
        }
        else{
            $this->lisaa_kommentti("Tietokannasta ei loytynyt
                    yhtaan testipikakommentia kohde_id; arvolla ".
                    Pikakommenttitestaus::$kohteen_testi_id);
            $this->lisaa_virheilmoitus("Tietokannasta ei loytynyt
                    yhtaan testipikakommentia kohde_id; arvolla ".
                    Pikakommenttitestaus::$kohteen_testi_id);
        }
       

        //======================================================================
        // "Haetaan olio tietokannasta id:n avulla, eli luodaan uusi
        // pikakommentti, jossa id mukana.
        $this->lisaa_kommentti("Haetaan olio tietokannasta id:n avulla,
            eli luodaan uusi pikakommentti, jossa id mukana.");

        $id = $this->pikakommentit[0]->get_id();
        $pika2 = new Pikakommentti($id, $this->tietokantaolio);

        $this->lisaa_kommentti("Testataan, onko uudella oliolla samat
                tiedot kuin ekalla (pitaisi olla)");

        //HUOM! Alla pitää olla yhtäläisyydet noin. Kolme varmistaa identtisyyden
        // eli tarkistaa olion tyypin myös ja alla ekasta ja kolmannesta tulee
        // sillä epätosi, vaikka kahdella tosi!
        if($pika2->get_kohde_id() == $pika->get_kohde_id() &&
            $pika2->get_kommentti() === "Ihan hassu suoritus!" &&
            $pika2->get_kohde_tyyppi() == Pikakommentti::$KOHDE_BONGAUS){

            $this->lisaa_kommentti("Kaikki kunnossa! Esimerkiksi
                pikakommentin kommentti = ".$pika2->get_kommentti());
        }
        else{
            $this->lisaa_kommentti("Virhe tietojen haussa tietokannasta!");
            $this->lisaa_virheilmoitus("Kommenttia ei kai loytynyt tietokannasta!
                (id: ".$this->pikakommentit[0]->get_id().")");
        }
        //======================================================================
        $this->lisaa_kommentti("<h4>Pikakommenttin luomistesti loppui!</h4>");
    }
    
    
    //==========================================================================
    //==========================================================================
    //==========================================================================
    
    // Testaa pikakommentin muokkausta:
    public function testaa_pikakommentin_muokkaus(){
        $this->lisaa_kommentti("<h4>Pikakommentin muokkaustesti alkaa</h4>");

        $this->lisaa_kommentti("Luodaan uusi pikakommentti hakemalla
            tiedot tietokannasta (id-parametri asetettu)");
        $this->muokattava = new Pikakommentti($this->pikakommentit[0]->get_id(),
                                        $this->tietokantaolio);

        //======================================================================
        // Testataan tallennusta ennen muutoksia:
        $this->lisaa_kommentti("Testataan ennen muutoksia
            onTallennuskelpoinen-metodi, jonka pitaisi valittaa:");
        $uusi = false;
        
        if($this->muokattava->tallenna_muutokset()==
                Pikakommentti::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: samoja tietoja ei pida
                paastaa muokkaamaan!");
        }
        else{
            $this->lisaa_kommentti("Oikein:  samoja tietoja ei pida
                paastaa muokkaamaan!");
        }

        //======================================================================
        // ASetetaan pikakommentille tahallaan vääriä arvoja:
        $this->muokattava->set_henkilo_id("piip");
        $this->muokattava->set_kohde_id("piip");
        $this->muokattava->set_kohde_tyyppi("piip");
        $this->muokattava->set_kommentti(1000); // Ei enää aiheuta virhettä
        // johtuen set-metodin sisältämästä tarkistusmetodista!
        
        
        
        $this->lisaa_kommentti("Testataan tahallaan vaarien muutosten vaikutus:");

        $this->lisaa_kommentti("Huonot arvot: henkilo_id=".
                            $this->muokattava->get_henkilo_id().", ".
                            "kohde_id=".
                            $this->muokattava->get_kohde_id().", ".
                            "kohde_tyyppi=".
                            $this->muokattava->get_kohde_tyyppi().", ".
                            "kommentti=".
                            $this->muokattava->get_kommentti());
        
        // tyhjennetään vielä aiemmat virheilmoitukset:
        $this->muokattava->tyhjenna_virheilmoitukset();

        // Virheilmoituksia pitäisi tulla kolme, yksi kustakin arvosta yllä:
        if($this->muokattava->tallenna_muutokset() == 
            Pikakommentti::$OPERAATIO_ONNISTUI){
            $this->lisaa_virheilmoitus("Virhe: virheita livahti ohi
                        tarkastuksen!");
        }
        else if($this->muokattava->virheilmoitusten_lkm()>=4){
            $this->lisaa_kommentti("Oikein:  Tiedoissa virheita:<br/>".
                $this->muokattava->tulosta_virheilmoitukset());
        }
        else{
            $this->lisaa_virheilmoitus("Virhe: virheita livahti ohi
                        tarkastuksen! Seuraavat huomattu:<br/>".
                        $this->muokattava->tulosta_virheilmoitukset());
        }
        //======================================================================
        // ASetetaan pikakommentille hyviä arvoja:
        $muutettu_kommentti = "Mulla <b>on</b> 1000 'ankka', j&auml;hj&auml;h!";
        $this->muokattava->set_henkilo_id($this->id_testihenkilo1);
        $this->muokattava->set_kohde_id(Pikakommenttitestaus::$kohteen_testi_id);
        $this->muokattava->set_kohde_tyyppi(Pikakommentti::$KOHDE_BONGAUS);
        $this->muokattava->set_kommentti($muutettu_kommentti);
        $this->lisaa_kommentti("Testataan hyvien muutosten jalkeen
            onTallennuskelpoinen-metodi, jonka ei pitaisi valittaa:");

        $this->lisaa_kommentti("Hyvät arvot: henkilo_id=".
                            $this->muokattava->get_henkilo_id().", ".
                            "kohde_id=".
                            $this->muokattava->get_kohde_id().", ".
                            "kohde_tyyppi=".
                            $this->muokattava->get_kohde_tyyppi().", ".
                            "kommentti=".
                            $this->muokattava->get_kommentti());
        
        // tyhjennetään vielä aiemmat virheilmoitukset:
        $this->muokattava->tyhjenna_virheilmoitukset();

        // Virheilmoituksia ei pitäisi tulla:
        if($this->muokattava->tallenna_muutokset() == 
            Pikakommentti::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Oikein! Tallennus onnistui!");
            
            // Kokeillaan hakea sama tietokannasta ja varmistetaan, että
             // muutettu kommentti on todella muuttunut:
             $testi = new Pikakommentti($this->muokattava->get_id(),
                                        $this->tietokantaolio);
             if($testi->get_kommentti() == $muutettu_kommentti){
                 $this->lisaa_kommentti("Muutokset oikein tietokannassa!
                     Kommentti on nykyaan: ".$testi->get_kommentti());
             }
             else{
                 $this->lisaa_virheilmoitus("Muutokset vaarin tietokannassa!
                     Kommentti on nykyaan: ".$testi->get_kommentti());
             }
        }
        else{
            $this->lisaa_virheilmoitus("Virhe:  Tiedoissa olevinaan virheita:<br/>".
                $this->muokattava->tulosta_virheilmoitukset());
        }
        //======================================================================

        $this->lisaa_kommentti("<h4>Pikakommentin muokkaustesti loppui</h4>");
    }

    public function testaa_naytot(){
        $this->lisaa_kommentti("<h4>Testataan nayttamismetodit:</h4>");
        // Näytetään pikakommentti:
        $naytettava = $this->pikakommentit[0];

        $omaid = 100;
        $kayttajan_valtuudet = Valtuudet::$HALLINTA;
        $html = $naytettava->nayta_pikakommentti($omaid, $kayttajan_valtuudet);
        $html .= $naytettava->nayta_poistovahvistuskysely();
        $this->lisaa_kommentti($html);
    }

    public function testaa_pikakommentin_poisto(){
        $this->lisaa_kommentti("<h4>Pikakommentin poistotesti alkaa</h4>");


        // Otetaan id talteen:
        $id_poistettava = $this->pikakommentit[0]->get_id();
        $this->poistettava = new Pikakommentti($id_poistettava,
                                            $this->tietokantaolio);

        $palaute = $this->poistettava->poista();

        if($palaute == Pikakommentti::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Poisto onnistui!");
            
            $this->lisaa_kommentti("Tehdaan viela tarkistus tietokannasta:");

            // TArkistetaan vielä tietokanta:
            $tulos = $this->tietokantaolio->
                    hae_eka_osuma_oliona(Pikakommentti::$taulunimi, 
                                        Pikakommentti::$SARAKENIMI_ID, 
                                        $id_poistettava);
            
            
            if($tulos === Tietokantaolio::$HAKU_PALAUTTI_TYHJAN){
                $this->lisaa_kommentti("OK! Tietokannasta ei
                    loytynyt poistettua pikakommenttia");
            }
            else{
                $this->lisaa_virheilmoitus("Virhe! Tietokannasta
                    loytyi poistetun id:lla pikakommentti, jonka
                        kommentti = ".$tulost->kommentti." ja 
                        id=".$tulos->id);
            }
        }
        else{
            $this->lisaa_virheilmoitus("Poisto epaonnistui! ".
                    $palaute);
        }

        $this->lisaa_kommentti("<h4>Pikakommentin poistotesti loppui</h4>");
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
            $uusi = "Virhe: pikakommentit eiv&auml;t olleet h&auml;vinneet, vaikka n&auml;in
                pit&auml;isi k&auml;yd&auml;, kun henkil&ouml; h&auml;vitet&auml;&auml;n.";
            $this->lisaa_kommentti($uusi, $lisaa_virheilm);
        }
    }
}
?>