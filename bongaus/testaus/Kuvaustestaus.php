<?php
require_once 'Testiapu_bongaus.php';
/**
 * Description of Pikakommenttitestaus
 * Testaa Lajiluokka-luokkaa. Kuvaus-oliot liittyvät aina lajiluokkaan, joten
 * ehkä niiden testailun voisi yhdistää tänne.
 *
 * create table bkuvaukset (tietokantataulu)
(
  id                    mediumint auto_increment not null,
  lajiluokka_id         mediumint default -1 not null,
  nimi                  varchar(128) not null,
  kuvaus                varchar(5000) not null,
  kieli                 smallint not null,
  primary key (id),
  index(lajiluokka_id),
  unique index(nimi),
  FOREIGN KEY (lajiluokka_id) REFERENCES blajiluokat (id)
                      ON DELETE CASCADE

) ENGINE=INNODB;  
 * 
 * HUOM! Saman nimen tallennus toiselle lajille ei onnistu, mutta tätä
 * toimintoa ei ole testattu täällä!
 * 
 * @author J-P
 */
class Kuvaustestaus extends Testiapu_bongaus{
    /**
     * Näin Editori hoksaa, mistä oliosta kysymys.
     * @var Kuvaus
     */
    private $kuvaus1; /** @var Kuvaus */
    private $kuvaus2; /** @var Kuvaus */
    private $kuvaus3; /** @var Kuvaus */
    private $muokattava; /** @var Kuvaus */
    private $poistettava; /** @var Kuvaus */

    // Kolmen kuvauksen testiarvot: yksi yläluokka (linnut) ja kaksi alaluokkaa,
    // joiden on tarkoitus kuvata samaa lajia (varpunen) eri kielillä.
    public static $kuvaus_nimi_linnut = "Linnut_testi";
    public static $kuvaus_kuvaus = "Testikuvaus"; // Tää kaikkiin -> poisto helpop.
    public static $kuvaus_nimi_varpunen = "varpunen_testi";
    public static $kuvaus_nimi_sparv = "sparv";
 
    // Pari apumuuttujaa lajiluokkiin:
    public static $nimi_latina_linnut = "linnut_latinax";
    public static $nimi_latina_varpunen = "varpunen_latinax";
    
    /**
     * @param Tietokantaolio $tietokantaolio
     */
    function  __construct($tietokantaolio, $parametriolio) {
        parent::__construct($tietokantaolio, $parametriolio, "Kuvaus");
        $this->kuvaus1 = Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        $this->kuvaus2 = Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        $this->kuvaus3 = Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        $this->muokattava = Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
        $this->poistettava = Kuvaus::$MUUTTUJAA_EI_MAARITELTY;
    }

    public function testaa(){
        // TEstataan kuvauksen luomista, muokkausta ja poistoa
        // (ja samalla muitakin metodeja, kuten onTallennuskelpoinen-metodi):
        $this->testaa_kuvauksen_luominen();
        $this->testaa_kuvauksen_muokkaus();
        //$this->testaa_naytot();
        $this->testaa_kuvauksen_poisto();

        $this->siivoa_jaljet();
    }

    /**
     * Testaa uuden kuvauksen luomista ja tallentamista. 
     */
    public function testaa_kuvauksen_luominen(){
        
        $this->lisaa_ilmoitus("<h4>Testataan kuvauksen luominen</h4>",false);

        // Luodaan ensin kaksi lajiluokkaa, joihin kuvaukset voidaan liittää.
        $linnut_ljid = Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;
        $varpunen_ljid = Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;
        
        //================== Alkusiivous =======================================
        // Poistetaan mahdolliseta aiempien testien roskat, joita on voinut
        // jäädä, kun testi on keskeytynyt:
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                            Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Kuvaustestaus::$nimi_latina_linnut);
        
        $lkm2 = $this->tietokantaolio->poista_kaikki_rivit(Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Kuvaustestaus::$nimi_latina_varpunen);

        $poistettujen_lkm = $lkm+$lkm2;
        if($poistettujen_lkm > 0){
            $this->lisaa_ilmoitus(
                    $poistettujen_lkm." vanhaa lajiluokkaa poistettu",false);
        }
        //======================= Alkusiivous päättyi===========================
        
        $linnut_ljid = $this->luo_ja_tallenna_lajiluokka(-1, 
                                    Kuvaustestaus::$nimi_latina_linnut);
        if($linnut_ljid !== Lajiluokka::$MUUTTUJAA_EI_MAARITELTY){
            $varpunen_ljid = $this->luo_ja_tallenna_lajiluokka($linnut_ljid, 
                                    Kuvaustestaus::$nimi_latina_varpunen);
        }
        
        // Tarkistetaan, että lajiluokat ok. Muuten ei kannata jatkaa:
        if($varpunen_ljid !== Lajiluokka::$MUUTTUJAA_EI_MAARITELTY){
            $this->tyhjenna_virheilmoitukset();
            
            $this->lisaa_ilmoitus("Lajiluokkien tallennus onnistui.",false);
            
            // Kuvaus hierarkkian päällä (esim "Linnut")
            $this->lisaa_ilmoitus("Tallennetaan kuvaus linnut-luokalle.",false);
            $linnut_kuvid = $this->luo_ja_tallenna_kuvaus($linnut_ljid, 
                                            Kuvaustestaus::$kuvaus_nimi_linnut, 
                                            Kuvaustestaus::$kuvaus_kuvaus, 
                                            Kielet::$SUOMI);

            // Haetaan tallennettu olio tietokannasta:
            $this->kuvaus1 = new Kuvaus($this->tietokantaolio, $linnut_kuvid);

            if($this->kuvaus1->olio_loytyi_tietokannasta){
                // Kuvaus hierarkkian toisessa kerroksessa (esim. "varpunen").
                $this->lisaa_ilmoitus("Tallennetaan suomenkielinen".
                        " kuvaus varpuselle.",false);
                $varpunen_kuvid_fi = $this->luo_ja_tallenna_kuvaus($varpunen_ljid, 
                                                Kuvaustestaus::$kuvaus_nimi_varpunen, 
                                                Kuvaustestaus::$kuvaus_kuvaus, 
                                                Kielet::$SUOMI);

                // Haetaan tallennettu olio tietokannasta:
                $this->kuvaus2 = new Kuvaus($this->tietokantaolio, $varpunen_kuvid_fi);
                if($this->kuvaus2->olio_loytyi_tietokannasta){
                    $this->lisaa_ilmoitus("Varpuskuvauksen suomitallennus".
                            " onnistui!",false);
                }else{
                    $this->lisaa_ilmoitus("Varpuskuvauksen suomitallennus".
                            " EI onnistunut!".$this->tulosta_virheilmoitukset(),false);
                }
                
                // Kuvaus hierarkkian toisessa kerroksessa (esim. "varpunen").
                $this->lisaa_ilmoitus("Tallennetaan ruotsinkielinen".
                        " kuvaus varpuselle.",false);
                $varpunen_kuvid_sve = $this->luo_ja_tallenna_kuvaus($varpunen_ljid, 
                                                Kuvaustestaus::$kuvaus_nimi_sparv, 
                                                Kuvaustestaus::$kuvaus_kuvaus, 
                                                Kielet::$RUOTSI);

                // Haetaan tallennettu olio tietokannasta:
                $this->kuvaus3 = new Kuvaus($this->tietokantaolio, $varpunen_kuvid_sve);
                if($this->kuvaus3->olio_loytyi_tietokannasta){
                    $this->lisaa_ilmoitus("Varpuskuvauksen ruotsitallennus".
                            " onnistui!",false);
                }else{
                    $this->lisaa_ilmoitus("Varpuskuvauksen ruotsitallennus".
                            " EI onnistunut!".$this->tulosta_virheilmoitukset(),false);
                }
                
                //==============================================================
                // Yritetään tallentaa toinen suomenkielinen kuvaus varpuselle,
                // minkä ei pitäisi onnistua:
                $this->lisaa_ilmoitus("Tallennetaan toinen suomenkielinen".
                        " kuvaus varpuselle, minka ei pitaisi onnistua.",false);
                $tuplavarpunen_kuvid_fi = $this->luo_ja_tallenna_kuvaus($varpunen_ljid, 
                                                "tuplavarpunen", 
                                                Kuvaustestaus::$kuvaus_kuvaus, 
                                                Kielet::$SUOMI);
                if($tuplavarpunen_kuvid_fi == Kuvaus::$MUUTTUJAA_EI_MAARITELTY){
                    $this->lisaa_ilmoitus("Tuplavarpunen ei mennyt lapi: ".
                    $this->tulosta_virheilmoitukset(),false);
                }else{
                    $this->lisaa_ilmoitus("Tuplavarpunen suomeksi
                        meni lapi, vaikka ei saisi!! KORJAAA",true);
                }
                //==============================================================
            }
            else{
                $this->lisaa_ilmoitus("Linnut-kuvauksen tallennus".
                            " EI onnistunut!".$this->tulosta_virheilmoitukset(),false);
            }
            
            //======================================================================
            // Lisätään taulukkoon, jos löydetty tietokannasta:
            if($this->kuvaus1->olio_loytyi_tietokannasta){

                // Tyhjennetään vanhat virheilmoitukset.
                //$this->tyhjenna_virheilmoitukset();

                $this->lisaa_kuvaus($this->kuvaus1);
                if(sizeof($this->kuvaukset) == 1){
                    $this->lisaa_ilmoitus("Uusi kuvaus lisatty
                    taulukkoon!",false);
                }
                else{
                    $this->lisaa_virheilmoitus("Virhe uuden kuvauksen lisayksessa
                        taulukkoon! (olioita ".sizeof($this->kuvaukset)." kpl)");
                }
            }
            else{
                $this->lisaa_ilmoitus("Kuvauksen haku tietokannasta".
                        " ei onnistunut! <br />".
                        $this->kuvaus1->tulosta_virheilmoitukset(),true);
            }
            if($this->kuvaus2->olio_loytyi_tietokannasta){

                // Tyhjennetään vanhat virheilmoitukset.
                //$this->tyhjenna_virheilmoitukset();

                $this->lisaa_kuvaus($this->kuvaus2);
                if(sizeof($this->kuvaukset) == 2){
                    $this->lisaa_ilmoitus("Toinen uusi kuvaus lisatty
                    taulukkoon!",false);
                }
                else{
                    $this->lisaa_virheilmoitus("Virhe toisen uuden kuvauksen lisayksessa
                        taulukkoon! (olioita ".sizeof($this->kuvaukset)." kpl)");
                }
                $this->lisaa_ilmoitus("Testataan, onko 2. uuden olion nimi ok",false);

                if($this->kuvaus2->get_nimi() == Kuvaustestaus::$kuvaus_nimi_varpunen){
                    $this->lisaa_ilmoitus("Nimi ok!",false);
                }
                else{
                    $this->lisaa_ilmoitus("Virhe tietojen haussa tietokannasta!",false);
                    $this->lisaa_virheilmoitus("Virhe nimessa!".
                        " (nimi: ".$this->kuvaus2->get_nimi().")");
                }
            }
            else{
                $this->lisaa_ilmoitus("Kuvauksen (2:n) haku tietokannasta".
                        " ei onnistunut! <br />".
                        $this->kuvaus1->tulosta_virheilmoitukset(),true);
            }
            
            if($this->kuvaus3->olio_loytyi_tietokannasta){

                // Tyhjennetään vanhat virheilmoitukset.
                //$this->tyhjenna_virheilmoitukset();

                $this->lisaa_kuvaus($this->kuvaus3);
                if(sizeof($this->kuvaukset) == 3){
                    $this->lisaa_ilmoitus("Kolmas uusi kuvaus lisatty
                    taulukkoon!",false);
                }
                else{
                    $this->lisaa_virheilmoitus("Virhe 3. uuden kuvauksen lisayksessa
                        taulukkoon! (olioita ".sizeof($this->kuvaukset)." kpl)");
                }
                $this->lisaa_ilmoitus("Testataan, onko 3. uuden olion nimi ok",false);

                if($this->kuvaus3->get_nimi() == Kuvaustestaus::$kuvaus_nimi_sparv){
                    $this->lisaa_ilmoitus("Nimi ok!",false);
                }
                else{
                    $this->lisaa_ilmoitus("Virhe tietojen haussa tietokannasta!",false);
                    $this->lisaa_virheilmoitus("Virhe nimessa!".
                        " (nimi: ".$this->kuvaus3->get_nimi().")");
                }
            }
            else{
                $this->lisaa_ilmoitus("Kuvauksen (3:n) haku tietokannasta".
                        " ei onnistunut! <br />".
                        $this->kuvaus1->tulosta_virheilmoitukset(),true);
            }

        }
        else{
            $this->lisaa_ilmoitus("Virhe varpuset-lajiluokan luomisessa.".
                    " Testi lopetetaan kesken!",true);
        }
        
        
        
        //======================================================================
        $this->lisaa_ilmoitus("<h4>Kuvauksen luomistesti loppui!</h4>",false);
    }
    // Testaa kuvauksen muokkausta:
    public function testaa_kuvauksen_muokkaus(){
        $this->lisaa_ilmoitus("<h4>Kuvauksen muokkaustesti alkaa</h4>",false);

        $this->lisaa_ilmoitus("Muokataan ekaa valmista kuvausta (Linnut)",false);
        $this->muokattava = $this->kuvaukset[0];

        //======================================================================
        // Testataan ennen muutoksia onTallennuskelpoinen-metodi, jonka
        // pitäisi valittaa:
        $this->lisaa_ilmoitus("Testataan ennen muutoksia
            onTallennuskelpoinen-metodi, jonka pitaisi valittaa:",false);
        $uusi = false;
        if($this->muokattava->on_tallennuskelpoinen($uusi)){
            $this->lisaa_ilmoitus("Virhe: muuttumattomia tietoja ei
                ole tarkoitus antaa tallentaa!",true);
        }
        else{
            $this->lisaa_ilmoitus("Oikein: muuttumattomia tietoja ei
                tallenneta!",false);
        }

        //======================================================================
        // ASetetaan kuvaukselle tahallaan vääriä arvoja:
        $this->muokattava->set_kieli(Kuvaus::$MUUTTUJAA_EI_MAARITELTY);
        $this->muokattava->set_kuvaus(Kuvaus::$MUUTTUJAA_EI_MAARITELTY);
        //$this->muokattava->set_lajiluokka("jkljö"); Tätä ei voi muuttaa!
        $this->muokattava->set_nimi(Kuvaus::$MUUTTUJAA_EI_MAARITELTY); 
        
        $this->lisaa_ilmoitus("Testataan tahallaan vaarien muutosten jalkeen
            onTallennuskelpoinen-metodi, jonka pitaisi valittaa:",false);

        // tyhjennetään vielä aiemmat virheilmoitukset:
        $this->muokattava->tyhjenna_virheilmoitukset();

        // Virheilmoituksia pitäisi tulla, yksi kustakin arvosta yllä:
        if($this->muokattava->on_tallennuskelpoinen(false)){
            $this->lisaa_ilmoitus("Virhe: virheita livahti ohi
                        tarkastuksen!",true);
        }
        // Alla tarkkana lkm:n kanssa! Ilmoituksia tulee nykyään tuplasti, kun
        // lukumuotoinen_muuttuja_ok- ja mjmuotoinen_muuttuja_ok-metodit
        // antavat suoraan virheilmoitukset.
        else if($this->muokattava->virheilmoitusten_lkm()==6){
            $this->lisaa_ilmoitus("Oikein:  Tiedoissa virheita:<br/>".
                $this->muokattava->tulosta_virheilmoitukset(),false);
        }
        else{
            $this->lisaa_ilmoitus("Virhe: virheita (".
            (3-$this->muokattava->virheilmoitusten_lkm())." kpl) livahti ohi
                        tarkastuksen! Seuraavat huomattu:<br/>".
                        $this->muokattava->tulosta_virheilmoitukset(),true);
        }
        //======================================================================
        // ASetetaan kuvaukselle hyviä arvoja:
        $muokattu_kuvaus = "Hurraa Sverige!";
        $muokattu_nimi = "Fåglar";
        $this->muokattava->set_kieli(Kielet::$RUOTSI);
        $this->muokattava->set_kuvaus($muokattu_kuvaus);
        $this->muokattava->set_nimi($muokattu_nimi); 
        $this->lisaa_ilmoitus("Testataan hyvien muutosten jalkeen
            onTallennuskelpoinen-metodi, jonka ei pitaisi valittaa:",false);

        // tyhjennetään vielä aiemmat virheilmoitukset:
        $this->muokattava->tyhjenna_virheilmoitukset();

        // Virheilmoituksia ei pitäisi tulla:
        if($this->muokattava->on_tallennuskelpoinen(false)){
            $this->lisaa_ilmoitus("Oikein! ARvot puhtaita!",false);
        }
        else{
            $this->lisaa_ilmoitus("Virhe:  Tiedoissa olevinaan virheita:<br/>".
                $this->muokattava->tulosta_virheilmoitukset(),false);
        }
        //======================================================================
        // tyhjennetään vielä aiemmat virheilmoitukset:
        $this->muokattava->tyhjenna_virheilmoitukset();
        
        // Kokeillaan sitten tallentaa muuttuneet tiedot:
        $this->lisaa_ilmoitus("Kokeillaan tallentaa muutokset:",false);
        $tallennuspalaute = $this->muokattava->tallenna_muutokset();
        if($tallennuspalaute == Kuvaus::$OPERAATIO_ONNISTUI){
             $this->lisaa_ilmoitus("Muutosten tallennus onnistui!",false);

             // Kokeillaan hakea sama tietokannasta ja varmistetaan, että
             // muutettu kommentti on todella muuttunut:
             $testi = new Kuvaus($this->tietokantaolio,$this->muokattava->get_id());
             if(($testi->get_kuvaus() == $muokattu_kuvaus) &&
                 ($testi->get_kieli() == Kielet::$RUOTSI) &&
                ($testi->get_nimi() == $muokattu_nimi)){
                 
                 $this->lisaa_ilmoitus("Muutokset oikein tietokannassa!
                     Kuvaus on nykyaan: '".$testi->get_kuvaus()."', ".
                    "nimi '".$testi->get_nimi()."', ".
                    "ja kieli '".Kielet::hae_kielen_nimi($testi->get_kieli()).      
                    "'",false);
             }
             else{
                 $this->lisaa_ilmoitus("Muutokset vaarin tietokannassa!
                     Kuvaus on nykyaan: '".$testi->get_kuvaus()."', ".
                    "nimi '".$testi->get_nimi()."', ".
                    "ja kieli '".Kielet::hae_kielen_nimi($testi->get_kieli()).      
                    "'",true);
             }
             
             // Muutetaan kuvaus alkuperäiseksi, jottei siivous häiriinny:
             $this->lisaa_ilmoitus("Muutetaan kuvaus alkuperaiseksi,
                 jotta siivous pysyy kasassa",false);
             
             // Huom! Alla käytetään testi-oliota, koska $this->muokattava ei 
             // ole aivan ajan tasalla.
             $testi->set_kuvaus(Kuvaustestaus::$kuvaus_kuvaus);
             $tallennus2 = $testi->tallenna_muutokset();
             if($tallennus2 != Kuvaus::$OPERAATIO_ONNISTUI){
                 $this->lisaa_virheilmoitus(
                         "Virhe alkup. kuvaus-kuvauksen palauttamisessa: ".
                         $testi->tulosta_virheilmoitukset());
                 
                 $this->lisaa_kommentti("Kuvaus = ".$testi->get_kuvaus().
                         " (pitäisi olla '".Kuvaustestaus::$kuvaus_kuvaus."')");
                 
             } else{
                 $this->lisaa_kommentti("Kuvauksen palauttaminen alkuperaiseksi ok!");
             }
        }else{
             $this->lisaa_ilmoitus($tallennuspalaute,true);
        }
        //====================== Yritetään muuttaa varpus-kuvausta
        // ruotsinkieliseksi, minkä ei pitäisi onnistua, koska sellainen on jo:
        $this->lisaa_ilmoitus("Yritetaan muuttaa varpus-kuvausta
            ruotsinkieliseksi, minka ei pitaisi onnistua, koska sellainen on jo",false);
        $varpunen = $this->kuvaus2;
        $varpunen->set_kieli(Kielet::$RUOTSI);
        $palaute = $varpunen->tallenna_muutokset();
        if($palaute == Kuvaus::$OPERAATIO_ONNISTUI){
            $this->lisaa_ilmoitus("Virhe!! Tupla sparv tallennettu!",true);
        }
        else{
            $this->lisaa_ilmoitus("Tuplaa ei sallittu (OIKEIN): ".
                    $varpunen->tulosta_virheilmoitukset(),false);
        }
        
        
        //======================================================================

        $this->lisaa_ilmoitus("<h4>Kuvauksen muokkaustesti loppui</h4>",
                false);
    }

    public function testaa_naytot(){
        $this->lisaa_ilmoitus("<h4>Testataan nayttamismetodit:</h4>",
                false);
        // Näytetään kuvaus:
        $naytettava = $this->kuvaukset[0];

        $omaid = 100;
        $kayttajan_valtuudet = Valtuudet::$HALLINTA;
        $html = $naytettava->nayta_kuvaus($omaid, $kayttajan_valtuudet);
        $html .= $naytettava->nayta_poistovahvistuskysely();
        $this->lisaa_ilmoitus($html,false);
    }

    public function testaa_kuvauksen_poisto(){
        $this->lisaa_ilmoitus("<h4>Kuvauksen poistotesti alkaa</h4>",
                false);


        // Otetaan id talteen:
        $id_poistettava = $this->kuvaukset[0]->get_id();
        $this->poistettava = $this->kuvaukset[0];
        
        // Muutetaan kieleksi suomi (suomenkielista kuvausta ei voi poistaa):
        $this->poistettava->set_kieli(Kielet::$SUOMI);
        
        $this->lisaa_ilmoitus("Yritetaan poistaa suomenkielinen
            kuvaus, minka ei pitaisi onnistua!",false);
        
        $palaute = $this->poistettava->poista();
        
        if($palaute == Kuvaus::$OPERAATIO_ONNISTUI){
            $this->lisaa_ilmoitus("Virhe! Poisto onnistui suomenkieliselle
                kuvaukselle!",true);
        }else{
            $this->lisaa_ilmoitus("Suomenkielista kuvausta ei 
                voida poistaa. (OIKEIN)",false);
        }
        
        $this->lisaa_ilmoitus("Muutetaan saman kuvauksen kieli
            ruotsiksi, jolloin poiston pitaisi onnistua.",false);
        
        // Muutetaan kieleksi ruotsi, jotta poisto onnistuisi:
        $this->poistettava->set_kieli(Kielet::$RUOTSI);
        $palaute = $this->poistettava->poista();

        if($palaute == Kuvaus::$OPERAATIO_ONNISTUI){
            $this->lisaa_ilmoitus("Poisto onnistui!",false);
            
            $this->lisaa_ilmoitus("Tehdaan viela tarkistus tietokannasta:",
                                        false);

            // TArkistetaan vielä tietokanta:
            $lkm = $this->tietokantaolio->hae_osumien_lkm(Kuvaus::$taulunimi, 
                                                Kuvaus::$SARAKENIMI_ID, 
                                                $id_poistettava);
            if($lkm == 0){
                $this->lisaa_ilmoitus("OK! Tietokannasta ei
                    loytynyt poistettua kuvausa",false);
            }
            else{
                $this->lisaa_ilmoitus("Virhe! Tietokannasta
                    loytyi poistetun id:lla ".$lkm." kuvausa",true);
            }
        }
        else{
            $this->lisaa_ilmoitus("Poisto epaonnistui! ".
                    $palaute, true);
        }

        $this->lisaa_ilmoitus("<h4>Kuvauksen poistotesti loppui</h4>",
                false);
    }

    

    /**
     * Siivoaa tietokannasta kaikki tallenteet:
     */
    public function siivoa_jaljet(){
        $this->lisaa_ilmoitus(
                   "<h4>Tietokannan siivous:</h4>",false);

        
        // Haetaan ensin testikuvausten määrä tietokannasta:
        $this->lisaa_ilmoitus("Haetaan testikuvausten lkm tietokannasta.",
                false);
        
        $kuvausten_lkm_alussa = 
            $this->tietokantaolio->hae_osumien_lkm(Kuvaus::$taulunimi, 
                                                Kuvaus::$SARAKENIMI_KUVAUS, 
                                                Kuvaustestaus::$kuvaus_kuvaus);
        
        if($kuvausten_lkm_alussa == 2){
            $this->lisaa_ilmoitus("Kuvauksia loytyi $kuvausten_lkm_alussa kpl.",
                false);
        }
        else{
            $this->lisaa_ilmoitus("Virhe! Kuvauksia loytyi ".
                    $kuvausten_lkm_alussa." kpl.",true);
        }
       
        // Poistetaan lajiluokat, jolloin kuvausten pitäisi poistua autom.
        $this->lisaa_ilmoitus("Poistetaan lajiluokat, jolloin kuvausten
            pitaisi automaattisesti poistua myos.",
                false);
        
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                            Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Kuvaustestaus::$nimi_latina_varpunen);
        
        $lkm2 = $this->tietokantaolio->poista_kaikki_rivit(Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Kuvaustestaus::$nimi_latina_linnut);

        if(($lkm == $lkm2) && ($lkm == 1)){
            $this->lisaa_ilmoitus(
                    "Kaksi lajiluokkaa poistettu",false);
        }
        else{
            $lisaa_virheilm = true;
            $uusi = "Virhe: Poistettu ".$lkm+$lkm2." lajiluokkaa!";
            $this->lisaa_ilmoitus($uusi, $lisaa_virheilm);
        }
        
        $this->lisaa_ilmoitus(
                    "Kokeillaan poistaa kuvaukset, joita ei pitaisi ollakaan:",false);
        
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                            Kuvaus::$taulunimi,
                            Kuvaus::$SARAKENIMI_KUVAUS,
                            Kuvaustestaus::$kuvaus_kuvaus);
       

        if($lkm == 0){
            $this->lisaa_ilmoitus("Kuvauksia ei loytynyt (OIKEIN).",false);
        }
        else{
            $lisaa_virheilm = true;
            $uusi = "Virhe: Poistettu ".$lkm." kuvausta!";
            $this->lisaa_ilmoitus($uusi, $lisaa_virheilm);
        }
        
        //======================================================
        
    }

    
    
    

}
?>
