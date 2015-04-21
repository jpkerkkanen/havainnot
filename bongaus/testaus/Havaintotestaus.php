<?php
require_once 'Testiapu_bongaus.php';
/**
 * Testaa Havainto-luokkaa.
create table havainnot
(
  id                    int auto_increment not null,
  henkilo_id            int default -1 not null,
  lajiluokka_id         int default -1 not null,
  vuosi                 smallint default -1,
  kk                    tinyint default -1,
 
  paiva                 tinyint default -1,
  paikka                varchar(300),
  kommentti             varchar(3000),
  maa                   smallint default 1,
  varmuus               smallint default 100,
 
  sukupuoli             tinyint default -1,
  lkm                   int default -1,
 
  primary key (id),
  index(henkilo_id),
  index(vuosi),
  index(kk),
  index(paiva),
  index(paikka),
  index(maa),
  index(lajiluokka_id),
  FOREIGN KEY (lajiluokka_id) REFERENCES lajiluokat (id)
                      ON DELETE CASCADE,
  FOREIGN KEY (henkilo_id) REFERENCES henkilot (id)
                      ON DELETE CASCADE

) ENGINE=INNODB;    
 * 
 * @author J-P
 */
class Havaintotestaus extends Testiapu_bongaus{
    /**
     * Näin Editori hoksaa, mistä oliosta kysymys.
     * @var Havainto
     */
    private $havainto1; /** @var Havainto */
    private $havainto2; /** @var Havainto */
    private $havainto3; /** @var Havainto */
    private $muokattava; /** @var Havainto */
    private $poistettava; /** @var Havainto */

    // testiarvoja
    public static $havainto_paikka1 = "Testipiha";
    public static $havainto_kommentti = "Testihavainto"; // Tää kaikkiin -> poisto helpop.
 
    // Pari apumuuttujaa lajiluokkiin:
    public static $nimi_latina_linnut = "linnut_latinax";
    public static $nimi_latina_varpunen = "varpunen_latinax";
    public $lajiluokka_id1, $lajiluokka_id2;
    
    /**
     * @param Tietokantaolio $tietokantaolio
     * @param Parametrit $parametriolio
     */
    function  __construct($tietokantaolio, $parametriolio) {
        parent::__construct($tietokantaolio, $parametriolio, "Havainto");
        $this->havainto1 = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        $this->havainto2 = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        $this->havainto3 = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        $this->muokattava = Havainto::$MUUTTUJAA_EI_MAARITELTY;
        $this->poistettava = Havainto::$MUUTTUJAA_EI_MAARITELTY;
    }

    public function testaa(){
        // TEstataan havainnon luomista, muokkausta ja poistoa
        // (ja samalla muitakin metodeja, kuten onTallennuskelpoinen-metodi):
        $this->testaa_havainnon_luominen();
        $this->testaa_havainnon_muokkaus();
        $this->testaa_naytot();
        $this->testaa_havainnon_poisto();

        $this->lisaa_virheilmoitus("Lisaluokitustestit toteuttamatta!");
        $this->siivoa_jaljet();
    }

    /**
     * Testaa uuden havainnon luomista ja tallentamista.
     */
    public function testaa_havainnon_luominen(){
        
        $this->lisaa_ilmoitus("<h4>Testataan havainnon luominen</h4>",false);
        //$this->lisaa_ilmoitus("Havainnon luomistesti: toteutus kesken!",true);

        // Luodaan ensin kaksi lajiluokkaa, joihin kuvaukset voidaan liittää.
        $linnut_ljid = Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;
        $varpunen_ljid = Lajiluokka::$MUUTTUJAA_EI_MAARITELTY;
        
        //================== Alkusiivous =======================================
        // Poistetaan mahdolliseta aiempien testien roskat, joita on voinut
        // jäädä, kun testi on keskeytynyt:
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                            Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Havaintotestaus::$nimi_latina_linnut);
        
        $lkm2 = $this->tietokantaolio->poista_kaikki_rivit(Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Havaintotestaus::$nimi_latina_varpunen);

        $poistettujen_lkm = $lkm+$lkm2;
        if($poistettujen_lkm > 0){
            $this->lisaa_ilmoitus(
                    $poistettujen_lkm." vanhaa lajiluokkaa poistettu",false);
        }
        //======================= Alkusiivous päättyi===========================
        
        $linnut_ljid = $this->luo_ja_tallenna_lajiluokka(-1, 
                                    Havaintotestaus::$nimi_latina_linnut);
        if($linnut_ljid !== Lajiluokka::$MUUTTUJAA_EI_MAARITELTY){
            $varpunen_ljid = $this->luo_ja_tallenna_lajiluokka($linnut_ljid, 
                                    Havaintotestaus::$nimi_latina_varpunen);
            $this->lajiluokka_id1 = $linnut_ljid;
        }
        
        // Tarkistetaan, että lajiluokat ok. Muuten ei kannata jatkaa:
        if($varpunen_ljid !== Lajiluokka::$MUUTTUJAA_EI_MAARITELTY){
            $this->tyhjenna_virheilmoitukset();
            $this->lajiluokka_id2 = $varpunen_ljid;
            
            $this->lisaa_ilmoitus("Lajiluokkien tallennus onnistui.",false);
            
            // Havainto hierarkkian päällä (esim "Linnut")
            $this->lisaa_ilmoitus("Tallennetaan havainto linnut-luokalle.",false);
            
            $henkilo_id = 1; 
            $vuosi = 2012; 
            $kk = 3;
            $paiva = 5; 
            $paikka = Havaintotestaus::$havainto_paikka1;
            $kommentti = Havaintotestaus::$havainto_kommentti;
            $maa = Maat::$suomi;
            $varmuus = Varmuus::$melkoisen_varma;
            
            $this->havainto1 = $this->luo_ja_tallenna_havainto($henkilo_id, 
                                                            $linnut_ljid, 
                                                            $vuosi, 
                                                            $kk, 
                                                            $paiva, 
                                                            $paikka, 
                                                            $kommentti, 
                                                            $maa, 
                                                            $varmuus);
            if(!$this->havainto1 instanceof Havainto){
                $this->lisaa_ilmoitus("Linnut-havainnon tallennus".
                            " EI onnistunut! ".$this->tulosta_virheilmoitukset(),true);
            }
            else{
                if($this->havainto1->olio_loytyi_tietokannasta){
                    $this->lisaa_ilmoitus("Linnut-havainnon tallennus".
                            " onnistui!",false);

                    // Lisätään taulukkoon:
                    $this->lisaa_havainto($this->havainto1);
                }
                else{
                    $this->lisaa_ilmoitus("Virhe Linnut-havainnon".
                            " hakemisessa tietokannasta! ".$this->tulosta_virheilmoitukset(),true);
                }
            }
            
            
            
            //======================================================================
           // Havainto varpuseen:
            $this->lisaa_ilmoitus("Tallennetaan varpushavainto.",false);
            
            $henkilo_id = 1; 
            $lajiluokka_id = $varpunen_ljid;
            $vuosi = 2012; 
            $kk = 4;
            $paiva = 6; 
            $paikka = Havaintotestaus::$havainto_paikka1;
            $kommentti = Havaintotestaus::$havainto_kommentti;
            $maa = Maat::$suomi;
            $varmuus = Varmuus::$melkoisen_varma;
            
            $this->havainto2 = $this->luo_ja_tallenna_havainto($henkilo_id, 
                                                            $lajiluokka_id, 
                                                            $vuosi, 
                                                            $kk, 
                                                            $paiva, 
                                                            $paikka, 
                                                            $kommentti, 
                                                            $maa, 
                                                            $varmuus);

            if($this->havainto2 === Havainto::$VIRHE){
                $this->lisaa_ilmoitus("Varpushavainnon tallennus".
                            " EI onnistunut! ".$this->tulosta_virheilmoitukset(),true);
            }
            else{
                if($this->havainto2->olio_loytyi_tietokannasta){
                    $this->lisaa_ilmoitus("Varpushavainnon tallennus".
                            " onnistui!",false);

                    // Lisätään taulukkoon:
                    $this->lisaa_havainto($this->havainto2);
                    
                    // Tarkistetaan, että taulukossa kaksi havaintoa:
                    if(sizeof($this->havainnot) == 2){
                        $this->lisaa_ilmoitus("Toinen uusi havainto lisatty
                        taulukkoon!",false);
                    }
                    else{
                        $this->lisaa_virheilmoitus("Virhe toisen uuden havainnon lisayksessa
                            taulukkoon! (havaintoja ".sizeof($this->havainnot)." kpl)");
                    }
                    $this->lisaa_ilmoitus("Testataan, onko 2. uuden olion paikka ok",false);

                    if($this->havainto2->get_paikka() == Havaintotestaus::$havainto_paikka1){
                        $this->lisaa_ilmoitus("Nimi ok!",false);
                    }
                    else{
                        $this->lisaa_ilmoitus("Virhe tietojen haussa tietokannasta!",false);
                        $this->lisaa_virheilmoitus("Virhe paikassa!".
                            " (paikka: ".$this->havainto2->get_paikka().")");
                    }
                }
                else{
                    $this->lisaa_ilmoitus("Virhe varpushavainnon".
                            " hakemisessa tietokannasta! ".$this->tulosta_virheilmoitukset(),true);
                }
            }
            //============================================================================
            // Havainto, jonka ei pitäisi mennä läpi:
            $this->lisaa_ilmoitus("Tallennetaan HUONO varpushavainto.",false);

            $henkilo_id = Havainto::$MUUTTUJAA_EI_MAARITELTY;
            $lajiluokka_id = $varpunen_ljid;
            $vuosi = "hihihi";
            $kk = "";
            $paiva = "Sdfg";
            $paikka = "";
            $kommentti = Havaintotestaus::$havainto_kommentti;
            $maa = Maat::$suomi;
            $varmuus = Varmuus::$melkoisen_varma;

            $this->havainto3 = $this->luo_ja_tallenna_havainto($henkilo_id,
                                                            $lajiluokka_id,
                                                            $vuosi,
                                                            $kk,
                                                            $paiva,
                                                            $paikka,
                                                            $kommentti,
                                                            $maa,
                                                            $varmuus);

            if($this->havainto3 === Havainto::$VIRHE){
                $this->lisaa_ilmoitus("Epakelvon varpushavainnon tallennus".
                            " EI onnistunut! (OIKEIN) ".
                        $this->tulosta_virheilmoitukset(),false);
            }
            else{
                $this->lisaa_ilmoitus("Epakelvon varpushavainnon tallennus
                        onnistui (VIRHE!!)",true);
            }

            // Tyhjennetään virheilmoitukset:
            $this->tyhjenna_virheilmoitukset();
        }
        else{
            $this->lisaa_ilmoitus("Virhe varpuset-lajiluokan luomisessa.".
                    " Testi lopetetaan kesken!",true);
        }
        
        
        
        //======================================================================
        $this->lisaa_ilmoitus("<h4>Havainnon luomistesti loppui!</h4>",false);
    }
    // Testaa havainnon muokkausta:
    public function testaa_havainnon_muokkaus(){
        $this->lisaa_ilmoitus("<h4>Havainnon muokkaustesti alkaa</h4>",false);

        $this->lisaa_ilmoitus("Muokataan ekaa valmista havaintota (Linnut)",false);
        $this->muokattava = $this->havainto1;

        if($this->muokattava instanceof Havainto){
            //======================================================================
            // Testataan ennen muutoksia onTallennuskelpoinen-metodi, jonka
            // pitäisi valittaa:
            $this->lisaa_ilmoitus("Kokeillaan tallentaa ennen muutoksia,
                minka ei pitaisi onnistua:",false);

            if($this->muokattava->tallenna_muutokset() == Havainto::$OPERAATIO_ONNISTUI){
                $this->lisaa_ilmoitus("Virhe: muuttumattomia tietoja ei
                    ole tarkoitus antaa tallentaa!",true);
            }
            else{
                $this->lisaa_ilmoitus("Oikein: muuttumattomia tietoja ei
                    tallenneta!",false);
            }

            //======================================================================
            // ASetetaan kuvaukselle tahallaan vääriä arvoja (paikaksi tyhjä):
            $this->muokattava->set_paikka("");

            $this->lisaa_ilmoitus("Testataan tahallaan tallennus,
                jonka pitaisi valittaa:",false);

            // tyhjennetään vielä aiemmat virheilmoitukset:
            $this->muokattava->tyhjenna_virheilmoitukset();

            // Virheilmoitus pitäisi tulla:
            if($this->muokattava->tallenna_muutokset() == Havainto::$OPERAATIO_ONNISTUI){
                $this->lisaa_ilmoitus("Virhe: tyhjä paikka-muuttuja livahti ohi
                            tarkastuksen!",true);
            }
            else if($this->muokattava->virheilmoitusten_lkm()==1){
                $this->lisaa_ilmoitus("Oikein:  Tiedoissa virheita:<br/>".
                    $this->muokattava->tulosta_virheilmoitukset(),false);
            }
            else{
                $this->lisaa_ilmoitus("Virhe: virheilmoituksia (".
                ($this->muokattava->virheilmoitusten_lkm())." kpl):<br/>".
                            $this->muokattava->tulosta_virheilmoitukset(),true);
            }
            // tyhjennetään vielä aiemmat virheilmoitukset:
            $this->muokattava->tyhjenna_virheilmoitukset();
            //======================================================================
            // ASetetaan havainnolle hyviä arvoja:
            $muokattu_paikka = "Naapurin pihallla";
            $muokattu_vuosi = 1999;   

            $this->muokattava->set_paikka($muokattu_paikka);
            $this->muokattava->set_vuosi($muokattu_vuosi); 
            $this->lisaa_ilmoitus("Testataan hyvien muutosten tallennus, 
                jonka ei pitaisi valittaa:",false);

            // tyhjennetään vielä aiemmat virheilmoitukset:
            $this->muokattava->tyhjenna_virheilmoitukset();

            // Virheilmoituksia ei pitäisi tulla:
            if($this->muokattava->tallenna_muutokset() === Havainto::$OPERAATIO_ONNISTUI){
                $this->lisaa_ilmoitus("Oikein! Tallennus ok!",false);

                // Kokeillaan hakea sama tietokannasta ja varmistetaan, että
                // muutettu kommentti on todella muuttunut:
                // Huom! Alla vuoden vertailussa pitää olla merkit noin. Täysi
                // identtisyys ei mene läpi. Menisi, jos vuosi olisi
                // määritelty $muokattu_vuosi = "1999". Liittyy ilmeisesti
                // tapaan, jolla numeroarvot jossakin vaiheessa muuttuvat merkkijonoiksi.
                 $testi = new Havainto($this->tietokantaolio,$this->muokattava->get_id());
                 if(($testi->get_paikka() === $muokattu_paikka) &&
                     ($testi->get_vuosi() == $muokattu_vuosi)){

                     $this->lisaa_ilmoitus("Muutokset oikein tietokannassa!
                         Paikka on nykyaan: '".$testi->get_paikka()."' ja ".
                        "vuosi '".$testi->get_vuosi()."'",false);
                 }
                 else{
                     $this->lisaa_ilmoitus("Muutokset vaarin tietokannassa!
                         Paikka on nykyaan: '".$testi->get_paikka()."' ja ".
                        "vuosi '".$testi->get_vuosi()."'",true);
                 }
            } else{
                $this->lisaa_ilmoitus("Virhe:  Tallennus ei ok:<br/>".
                    $this->muokattava->tulosta_virheilmoitukset(),true);

                $testi = new Havainto($this->tietokantaolio,$this->muokattava->get_id());
                $this->lisaa_ilmoitus(" Tietokannassa seuraavat tiedot:
                         Paikka on nykyaan: '".$testi->get_paikka()."' ja ".
                        "vuosi '".$testi->get_vuosi()."'",true);
            }
            
            //=================================================================
            // Testataan kuvalinkkien korjausta havaintomuutoksen yhteydessä
            $this->lisaa_virheilmoitus("Lajikuvalinkkien korjaustesti havainnon".
                    " muokkauksen yhteydessä toteuttamatta!");

            //======================================================================
            

            
        } else{
            $this->lisaa_virheilmoitus("Muokattava olio ei ole kunnollinen ".
                                        "Havainto-olio!");
        }
        
        $this->lisaa_ilmoitus("<h4>Havainnon muokkaustesti loppui</h4>",
                    false);
        // Tyhjennetään virheilmoitukset:
        //$this->tyhjenna_virheilmoitukset();
        //======================================================================
        //======================================================================
    }

    public function testaa_naytot(){
        $this->lisaa_ilmoitus("Havainnon nayttotesti: toteutus kesken", true);
        /*$this->lisaa_ilmoitus("<h4>Testataan nayttamismetodit:</h4>",
                false);
        // Näytetään havainto:
        $naytettava = $this->kuvaukset[0];

        $omaid = 100;
        $kayttajan_valtuudet = Valtuudet::$HALLINTA;
        $html = $naytettava->nayta_havainto($omaid, $kayttajan_valtuudet);
        $html .= $naytettava->nayta_poistovahvistuskysely();
        $this->lisaa_ilmoitus($html,false);*/
    }

    public function testaa_havainnon_poisto(){
        
        $this->lisaa_ilmoitus("<h4>Havainnon poistotesti alkaa</h4>",
                false);
        
        $this->lisaa_ilmoitus("<p>Luodaan poistoa varten uusi havainto.</p>",
                false);

            $henkilo_id = 1;
            $lajiluokka_id = $this->lajiluokka_id2;
            $vuosi = 2003;
            $kk = 6;
            $paiva = 12;
            $paikka = "Koulun piha";
            $kommentti = Havaintotestaus::$havainto_kommentti;
            $maa = Maat::$suomi;
            $varmuus = Varmuus::$melkoisen_varma;

            $this->havainto3 = $this->luo_ja_tallenna_havainto($henkilo_id,
                                                            $lajiluokka_id,
                                                            $vuosi,
                                                            $kk,
                                                            $paiva,
                                                            $paikka,
                                                            $kommentti,
                                                            $maa,
                                                            $varmuus);

        if($this->havainto3 instanceof Havainto){
            $this->lisaa_ilmoitus("Poistettavan luonti onnistui.",
                false);
            
            // Otetaan id talteen:
            $id_poistettava = $this->havainto3->get_id();
            $poistettava = $this->havainto3;

            $palaute = $poistettava->poista();

            if($palaute === Havainto::$OPERAATIO_ONNISTUI){
                $this->lisaa_ilmoitus("Poisto onnistui!",false);

                $this->lisaa_ilmoitus("Tehdaan viela tarkistus tietokannasta:",
                                            false);

                // TArkistetaan vielä tietokanta:
                $lkm = $this->tietokantaolio->
                        hae_osumien_lkm(Havainto::$taulunimi, 
                                        Havainto::$SARAKENIMI_ID,
                                        $id_poistettava);
                if($lkm == 0){
                    $this->lisaa_ilmoitus("OK! Tietokannasta ei
                        loytynyt poistettua havaintoa",false);
                }
                else{
                    $this->lisaa_ilmoitus("Virhe! Tietokannasta
                        loytyi poistetun id:lla ".$lkm." havaintoa",true);
                }
            }
            else{
                $this->lisaa_ilmoitus("Poisto epaonnistui! ".
                        $palaute, true);
            }
        }
        else{
            $this->lisaa_ilmoitus("Poistettavan luonti ep&auml;onnistui!",
                true);
        }
            
        

        $this->lisaa_ilmoitus("Kokeillaan sitten poistaa olematon
            havainto:", false);
        
        $falskihavainto = new Havainto(12345, $this->tietokantaolio);
        
        $palaute = $falskihavainto->poista();
        if($palaute == Havainto::$OPERAATIO_ONNISTUI){
            $this->lisaa_ilmoitus("Virhe: olemattoman poisto onnistui! "
                    , true);
        }
        else{
            $this->lisaa_ilmoitus("Oikein: olemattoman poistoa ei edes
                yriteta! ", false);
        }
        
        $this->lisaa_ilmoitus("<h4>Havainnon poistotesti loppui</h4>",
                false);
        
        
    }

    

    /**
     * Siivoaa tietokannasta kaikki tallenteet:
     */
    public function siivoa_jaljet(){
        $this->lisaa_ilmoitus(
                   "<h4>Tietokannan siivous:</h4>",false);

        
        // Haetaan ensin testihavaintoten määrä tietokannasta:
        $this->lisaa_ilmoitus("Haetaan testihavaintoten lkm tietokannasta.",
                false);
        
        $taulu = Havainto::$taulunimi;
        $taulun_sarake = Havainto::$SARAKENIMI_KOMMENTTI;
        $hakuarvo = Havaintotestaus::$havainto_kommentti;
        
        $havaintoten_lkm_alussa =
            $this->tietokantaolio->hae_osumien_lkm($taulu, $taulun_sarake, $hakuarvo);
        
        if($havaintoten_lkm_alussa == sizeof($this->havainnot)){
            $this->lisaa_ilmoitus("Havaintoja loytyi $havaintoten_lkm_alussa kpl (OIKEIN).",
                false);
        }
        else{
            $this->lisaa_ilmoitus("Virhe! Havaintoja loytyi ".
                    $havaintoten_lkm_alussa." kpl.",true);
        }
       
        // Poistetaan lajiluokat, jolloin havaintoten pitäisi poistua autom.
        $this->lisaa_ilmoitus("Poistetaan lajiluokat, jolloin havaintoten
            pitaisi automaattisesti poistua myos.",
                false);
        
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                            Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Havaintotestaus::$nimi_latina_varpunen);
        
        $lkm2 = $this->tietokantaolio->poista_kaikki_rivit(Lajiluokka::$taulunimi,
                            Lajiluokka::$SARAKENIMI_NIMI_LATINA,
                            Havaintotestaus::$nimi_latina_linnut);

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
                    "Kokeillaan poistaa havainnot, joita ei pitaisi ollakaan:",false);
        
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                            Havainto::$taulunimi,
                            Havainto::$SARAKENIMI_KOMMENTTI,
                            Havaintotestaus::$havainto_kommentti);
     
        if($lkm == 0){
            $this->lisaa_ilmoitus("Havaintoja ei loytynyt (OIKEIN).",false);
        }
        else{
            $lisaa_virheilm = true;
            $uusi = "Virhe: Poistettu ".$lkm." havaintoa!";
            $this->lisaa_ilmoitus($uusi, $lisaa_virheilm);
        }
    }
}
?>
