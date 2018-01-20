<?php
/**
 * TURVALLISUUSHUOMAUTUS:
 * Sisäänkirjautuminen hoidetaan ajax-kutsun
// kautta, koska vain siten onnistuin estämään selaimen takaisin-painikkeesta
// aiheutuvan tietojen uudelleennäytön uloskirjautumisen jälkeen. Täällä ei siis
// ollenkaan käsitellä sisäänkirjautumista, jolloin myöskään sisäänkirjautumis-
// tiedot eivät jää tämän sivun välimuistiin.
// Uloskirjautuminen toteutetaan täällä, eli JS:n poiskytkeminen kesken kaiken
// ei haittaa.
 */
session_start();    // Aloitetaan istunto.

require_once('asetukset/tietokantayhteys.php');
require_once('asetukset/Valtuudet.php');
require_once('asetukset/yleinen.php');
require_once('asetukset/Kielet.php');

require_once('html_tulostus.php');

require_once('php_yleinen/perustus/Ilmoitus.php');
require_once('php_yleinen/perustus/Pohja.php');
require_once('php_yleinen/perustus/Tietokantaolio.php');
require_once('php_yleinen/perustus/Tietokantarivi.php');
require_once('php_yleinen/perustus/Tietokantasolu.php');
require_once('php_yleinen/perustus/Kontrolleripohja.php');
require_once('php_yleinen/perustus/Malliluokkapohja.php');
require_once('php_yleinen/perustus/Nakymapohja.php');
require_once('php_yleinen/perustus/Perustustekstit.php');

require_once('php_yleinen/Aika.php');
require_once('php_yleinen/Asetuspohja.php');
require_once('php_yleinen/Html.php');
require_once('php_yleinen/Merkit.php');
require_once('php_yleinen/Yleismetodit.php');
require_once('php_yleinen/Tekstityokalupalkki.php');

require_once('kayttajahallinta/Henkilo.php');
require_once('kayttajahallinta/Tunnukset.php');
require_once('kayttajahallinta/Aktiivisuus.php');
require_once('kayttajahallinta/Poppoo.php');
require_once('kayttajahallinta/Kayttajakontrolleri.php');
require_once('kayttajahallinta/Kayttajanakymat.php');
require_once('kayttajahallinta/Kayttajatekstit.php');

require_once('yhteiset/Palaute.php');
require_once('yhteiset/Parametrit.php');

// Bongaus:
require_once('bongaus/bongausasetukset.php');
require_once('bongaus/havainnot/Havainto.php');
require_once('bongaus/havainnot/Havaintokontrolleri.php');
require_once('bongaus/havainnot/Havaintonakymat.php');
require_once('bongaus/havainnot/Havaintojakso.php');
require_once('bongaus/havainnot/Havaintojaksolinkki.php');
require_once('bongaus/havainnot/Lisaluokitus.php');
require_once('bongaus/lajiluokat/Lajiluokka.php');
require_once('bongaus/lajiluokat/Kuvaus.php');
require_once('bongaus/lajiluokat/Kontrolleri_lj.php');
require_once('bongaus/lajiluokat/Nakymat_lj.php');

// Pikakommentit
require_once('pikakommentointi/Pikakommenttikontrolleri.php');
require_once('pikakommentointi/Pikakommenttinakymat.php');
require_once('pikakommentointi/Pikakommentti.php');
require_once('pikakommentointi/Pikakommenttitekstit.php');

// Kuvat
require_once('kuvat/Kuvatekstit.php');
require_once('kuvat/Kuva.php');
require_once('kuvat/Kuvakontrolleri.php');
require_once('kuvat/Kuvanakymat.php');
require_once('kuvat/Lajikuvalinkki.php');
require_once('kuvat/Havaintokuvalinkki.php');
     
// Luodaan heti aluksi tietokantaolio, parametriolio, palauteolio ja kayttaja:
$tietokantaolio = new Tietokantaolio($dbtyyppi, $dbhost, $dbuser, $dbsalis);
$tietokantaolio->yhdista_tietokantaan($dbnimi);
$parametriolio = new Parametrit($tietokantaolio);
$palauteolio = new Palaute();

// Käyttäjän kontrollointi:
$kayttajakontrolleri = new Kayttajakontrolleri($tietokantaolio, $parametriolio);

// Kirjautumistarkistus:
$kayttajakontrolleri->toteuta_kirjautumistarkastus($palauteolio);

// Havaintokontrolleri, joka ohjaa monia sivun olennaisia toimintoja:
$havaintokontrolleri = new Havaintokontrolleri($tietokantaolio, $parametriolio);

$lajiluokkakontrolleri = new Kontrolleri_lj($tietokantaolio, $parametriolio);

// Kuvakontrolleri:
$kuvakontrolleri = new Kuvakontrolleri($tietokantaolio, $parametriolio);

//------------------------------------------------------------------------------
// Haetaan sitten toiminto. Huomaa, että kirjautuminen hoidetaan ajax-kutsun
// kautta, koska vain siten onnistuin estämään selaimen takaisin-painikkeesta
// aiheutuvan tietojen uudelleennäytön uloskirjautumisen jälkeen. Täällä ei siis
// ollenkaan käsitellä sisäänkirjautumista, jolloin myöskään sisäänkirjautumis-
// tiedot eivät jää tämän sivun välimuistiin.
// Uloskirjautuminen toteutetaan täällä, eli JS:n poiskytkeminen kesken kaiken
// ei haittaa.

//==============================================================================
//==============================================================================
//==============================================================================
//====================== KÄYTTÄJÄTOIMINNOT ALKU ================================
$toiminto = $parametriolio->get_kayttajatoiminto();
if(!$palauteolio->get_kirjautuminen_ok()){
    $kayttajakontrolleri->toteuta_vierailijanakyma($palauteolio);
    
    if($toiminto === Kayttajatekstit::$nappi_rekisteroidy_value){
            $kayttajakontrolleri->toteuta_nayta_poppookirjautuminen($palauteolio);
    } else if($toiminto === Kayttajatekstit::$nappi_poppoo_palaa_value){
    } else if($toiminto === Kayttajatekstit::$nappi_henkilo_tallenna_uusi_value){
        $kayttajakontrolleri->toteuta_tallenna_uusi($palauteolio);
        $havaintokontrolleri->toteuta_nayta($palauteolio);
    }
} else {
    
    // Täällä ei kirjautumiskenttiä näytetä, ellei toiminto ole ollut
    // uloskirjautuminen.
    
    // Jos käyttäjä on jo sisällä ja tehnyt jotakin:
    if($parametriolio->get_kayttajatoiminto() !== Parametrit::$EI_MAARITELTY){
        
        // Haetaan toiminnon nimi (yleensä sama kuin painikkeen nimi) ja tehdään
        // tarvittavat toimenpiteet.
        if($toiminto === Kayttajatekstit::$nappi_kirjaudu_ulos_value){
            $kayttajakontrolleri->toteuta_uloskirjautuminen($palauteolio);
            
        } else if($toiminto === Kayttajatekstit::$nappi_omat_tiedot_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kayttajakontrolleri->
                    toteuta_nayta_tietolomake_muokkaus($palauteolio);
        } else if($toiminto === 
                        Kayttajatekstit::$nappi_tallenna_tietomuutokset_value){
            $kayttajakontrolleri->
                    toteuta_tallenna_muokkaus($palauteolio);
            
            
            if($palauteolio->get_onnistumispalaute()===
                                        Palaute::$ONNISTUMISPALAUTE_KAIKKI_OK){
                $havaintokontrolleri->toteuta_nayta($palauteolio);
            }
            
            
        } else if($toiminto === 
                        Kayttajatekstit::$nappi_poistu_tiedoista_value){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            
        } else if($toiminto === 
                        Kayttajatekstit::$nappi_poppootiedot_value){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kayttajakontrolleri->toteuta_nayta_poppootiedot($palauteolio);
        } else if($toiminto === Kayttajatekstit::$nappi_koti_value){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
        }
    //====================== KÄYTTÄJÄTOIMINNOT LOPPU ===========================
    //==========================================================================
    //==========================================================================
    //======================= HAVAINTOTOIMINNOT ALKU ===========================
        
    } else if ($parametriolio->get_havaintotoiminto() !== Parametrit::$EI_MAARITELTY){
        $havaintotoiminto = $parametriolio->get_havaintotoiminto();
        if($havaintotoiminto == Bongauspainikkeet::$KATSO_HAVAINTO_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if(($havaintotoiminto == Bongauspainikkeet::$UUSI_HAVAINTO_VALUE)){

            $havaintokontrolleri->toteuta_nayta_yksi_uusi_lomake($palauteolio);
        }
        
        else if(($havaintotoiminto == 
                        Bongauspainikkeet::$NAYTA_MONEN_HAVAINNON_VALINTA_VALUE)){

            $havaintokontrolleri-> 
                            toteuta_nayta_moniuusitallennuslomake($palauteolio);
        }
        
        else if(($havaintotoiminto == 
                        Bongauspainikkeet::$TALLENNA_MONTA_HAV_KERRALLA_VALUE)){

            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_tallenna_monta_uutta($palauteolio);
        }
        
        else if($havaintotoiminto == Bongauspainikkeet::$TALLENNA_UUSI_HAVAINTO_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_tallenna_uusi($palauteolio);
        }
        else if($havaintotoiminto == Bongauspainikkeet::$PERUMINEN_HAVAINTO_VALUE){
            $ilmoitus = Bongaustekstit::$ilm_havainnon_lisays_tai_muokkaus_peruttu;
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            $palauteolio->set_ilmoitus($ilmoitus);
        }
    
        else if($havaintotoiminto ==
                        Bongauspainikkeet::$TALLENNA_MUOKKAUS_HAVAINTO_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_tallenna_muokkaus($palauteolio);
        }
        else if($havaintotoiminto == Bongauspainikkeet::$POISTA_HAVAINTO_VALUE){
            $havaintokontrolleri->toteuta_nayta_poistovarmistus($palauteolio);
        }
        else if($havaintotoiminto == Bongauspainikkeet::$POISTOVAHVISTUS_HAVAINTO_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_poista($palauteolio);
        }
        else if($havaintotoiminto == Bongauspainikkeet::$PERU_POISTO_HAVAINTO_VALUE){
            $ilmoitus = Bongaustekstit::$ilm_havainnon_poisto_peruttu;
            //$sisalto = hae_havainnot($parametriolio);
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($havaintotoiminto == Bongauspainikkeet::$TAKAISIN_HAVAINTOIHIN_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($havaintotoiminto ==
                        Bongauspainikkeet:: $HAVAINNOT_VALITSE_LAJILUOKKA_VALUE){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
        }
        
        else if($havaintotoiminto == 
                        Bongauspainikkeet::$HAVAINNOT_MONIKOPIOI_ITSELLE_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_kopioi_itselle($palauteolio);
        }
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_NAYTA_MONIMUOKKAUSLOMAKE_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta_monimuokkauslomake($palauteolio);
        }
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_TALLENNA_VALITTUJEN_MUOKKAUS_VALUE){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_tallenna_muokkaus($palauteolio);
        }
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_POISTA_VALITUT_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta_poistovarmistus($palauteolio);
        }
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_MONIPOISTOVAHVISTUS_VALUE){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_poista($palauteolio);
        }
        
        else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_MONIPOISTON_PERUMINEN_VALUE){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            
        } else if($havaintotoiminto == 
                    Bongauspainikkeet::$HAVAINNOT_TILASTOT_VALUE){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta_tilasto_puolivuotisnakyma($palauteolio);
            $palauteolio->set_nayttomoodi(Html_tulostus::$nayttomoodi_kolmipalkki);
        }

    //====================== HAVAINTOTOIMINNOT LOPPU ===========================
    //==========================================================================
    //====================== LAJILUOKKATOIMINNOT ALKU ==========================
    } else if($parametriolio->get_lajiluokkatoiminto() !== Parametrit::$EI_MAARITELTY){
        
        $lajiluokkatoiminto = $parametriolio->get_lajiluokkatoiminto();
        if($lajiluokkatoiminto == Bongauspainikkeet::$KATSO_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
        }
        else if($lajiluokkatoiminto == Bongauspainikkeet::$UUSI_LAJILUOKKA_VALUE){
            $lajiluokkakontrolleri->
                    toteuta_nayta_lajiluokkalomake($palauteolio);
        }
        else if($lajiluokkatoiminto == 
                            Bongauspainikkeet::$TALLENNA_UUSI_LAJILUOKKA_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $lajiluokkakontrolleri->toteuta_tallenna_uusi($palauteolio);
        }
        else if($lajiluokkatoiminto ==
                                Bongauspainikkeet::$PERUMINEN_LAJILUOKKA_VALUE){
            $ilmoitus = Bongaustekstit::$ilm_lajiluokka_peruminen;
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($lajiluokkatoiminto ==
                                Bongauspainikkeet::$MUOKKAA_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($lajiluokkatoiminto ==
                        Bongauspainikkeet::$TALLENNA_MUOKKAUS_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($lajiluokkatoiminto ==
                                    Bongauspainikkeet::$POISTA_LAJILUOKKA_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $ilmoitus = "Toimintoa ei toteutettu!";
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($lajiluokkatoiminto ==
                            Bongauspainikkeet::$POISTOVAHVISTUS_LAJILUOKKA_VALUE){
            $ilmoitus = "Toimintoa ei toteutettu!";
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($lajiluokkatoiminto ==
                                Bongauspainikkeet::$PERU_POISTO_LAJILUOKKA_VALUE){
            $ilmoitus = "HUU";
            //$sisalto = hae_havainnot($parametriolio);
            
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            $palauteolio->set_ilmoitus($ilmoitus);
        }
        else if($lajiluokkatoiminto == Bongauspainikkeet::$TAKAISIN_LAJILUOKKA_VALUE){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
            $palauteolio->set_ilmoitus($ilmoitus);
            
        //=========================== tietokannan säätö - ei normaalitoiminto ==
        } /*else if($lajiluokkatoiminto == "Kopioi vanhat"){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $lajiluokkakontrolleri->toteuta_kopioi_lajiluokat_ja_kuvaukset($palauteolio);
            
        } else if($lajiluokkatoiminto == "Kopioi vanhat havainnot"){
            
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $lajiluokkakontrolleri->toteuta_kopioi_havainnot($palauteolio);
            
        }*/
        //=========================== tietokannan säätö - loppui ===============
        
    //==========================================================================
    //======================= YLLÄPITOTOIMINNOT ALKU ===========================
        
    } else if ($parametriolio->get_yllapitotoiminto() !== Parametrit::$EI_MAARITELTY){
        $yllapitotoiminto = $parametriolio->get_yllapitotoiminto();
        
        if($yllapitotoiminto === Kayttajatekstit::$nappi_admin_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kayttajakontrolleri->toteutad_nayta_adminnakyma($palauteolio);
        
        } else if($yllapitotoiminto === Kayttajatekstit::$nappi_poppoo_luo_uusi_value){
            $kayttajakontrolleri->toteutad_nayta_poppoolomake_uusi($palauteolio);
            
            
        } else if($yllapitotoiminto === Kayttajatekstit::$nappi_poppoo_poistu_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kayttajakontrolleri->toteutad_nayta_adminnakyma($palauteolio);
        
        } else if($yllapitotoiminto === Kayttajatekstit::$nappi_poppoo_tallenna_uusi_value){
            $kayttajakontrolleri->toteutad_tallenna_poppoo_uusi($palauteolio);
        
        } else if($yllapitotoiminto === Kayttajatekstit::$nappi_poppoo_tallenna_muokkaus_value){
            $kayttajakontrolleri->toteutad_tallenna_poppoomuokkaus($palauteolio);
            
        } else if($yllapitotoiminto === Kayttajatekstit::$nappi_poppoo_muokkaa_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kayttajakontrolleri->toteutad_nayta_poppoolomake_muokkaus($palauteolio); 
        
        } else if($yllapitotoiminto === Kayttajatekstit::$nappi_admin_muokkaa_henkilo_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kayttajakontrolleri->toteutad_nayta_henkilon_tietolomake($palauteolio);
            
        } else if($yllapitotoiminto === Kayttajatekstit::$adminnappi_poistu_tiedoista_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kayttajakontrolleri->toteutad_nayta_adminnakyma($palauteolio);
            
        } else if($yllapitotoiminto === Kayttajatekstit::$adminnappi_tallenna_tietomuutokset_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kayttajakontrolleri->toteutad_tallenna_henkilomuokkaus($palauteolio);
            
        } else{
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
        }

    //====================== YLLÄPITOTOIMINNOT LOPPU ===========================
    //==========================================================================
    //
    //
    //
    //==========================================================================
    //======================= KUVATOIMINNOT ALKU ===========================
    } else if ($parametriolio->get_kuvatoiminto() !== Parametrit::$EI_MAARITELTY){
        
        $kuvatoiminto = $parametriolio->get_kuvatoiminto();
        if($kuvatoiminto == Kuvatekstit::$painike_uusi_kuva_value){
          
        }
        else if($kuvatoiminto == Kuvatekstit::$painike_tallenna_uusi_kuva_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kuvakontrolleri->toteuta_tallenna_uusi($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
        }
        else if($kuvatoiminto == Kuvatekstit::$painike_uusi_kuva_havaintoihin_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->
                    toteuta_nayta_kuvalomake_havaintoihin($palauteolio, 
                                                            $kuvakontrolleri);
        }
        else if($kuvatoiminto == Kuvatekstit::$painike_peruminen_kuva_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
        }
        
        else if($kuvatoiminto == Kuvatekstit::$painike_muokkaa_kuva_value){
            
        }
        else if($kuvatoiminto == Kuvatekstit::$painike_tallenna_muokkaus_kuva_value){
           
        }
        else if($kuvatoiminto === Kuvatekstit::$painike_poista_kuva_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kuvakontrolleri->toteuta_nayta_poistovarmistus($palauteolio);
        }
        else if($kuvatoiminto === Kuvatekstit::$painike_poistovahvistus_kuva_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kuvakontrolleri->toteuta_poista($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
        }
        else if($kuvatoiminto === Kuvatekstit::$painike_poistovahvistus_kuvalinkki_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $kuvakontrolleri->toteuta_poista_havaintokuvalinkki($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
        }
        else if($kuvatoiminto == Kuvatekstit::$painike_peru_poisto_kuva_value){
            $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
            $havaintokontrolleri->toteuta_nayta($palauteolio);
        }
        else if($kuvatoiminto == Kuvatekstit::$painike_nayta_kuva_albumit_value){
           
        }
        else if($kuvatoiminto == Kuvatekstit::$painike_nayta_kuva_value){
            
        }
        else if($kuvatoiminto == Kuvatekstit::$painike_nayta_kuvien_esikatselu_value){
            
        }
        

    //====================== KUVATOIMINNOT LOPPU ===============================
    //==========================================================================
        
    } else{
    
        // Perusnäkymä eli kirjautuneen ensimmäinen näkymä:
        $kayttajakontrolleri->toteuta_kirjautunut_nakyma($palauteolio);
        $havaintokontrolleri->toteuta_nayta($palauteolio);
    }
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$painikkeet = $palauteolio->get_painikkeet();
$ylapalkki = $palauteolio->get_kirjautumistiedot();
$sisalto = $palauteolio->get_sisalto();
$ilmoitus = $palauteolio->tulosta_kaikki_ilmoitukset();
$vasen_palkki = $palauteolio->get_vasen_palkki();
$oikea_palkki = $palauteolio->get_oikea_palkki();
$alapalkki = $palauteolio->get_alapalkki();
$nayttomoodi = $palauteolio->get_nayttomoodi();

echo Html_tulostus::nayta_bongaussivu($painikkeet,
                    $sisalto,
                    $ilmoitus,
                    $ylapalkki,
                    $vasen_palkki,
                    $oikea_palkki,
                    $alapalkki,
                    $nayttomoodi);

?>