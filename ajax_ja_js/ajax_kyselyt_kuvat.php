<?php
/* Tänne tulevat ajax-kyselyt index.php-sivulta.
session_start();    // Aloitetaan istunto.
require_once('../asetukset/yleinen.php');

// Liekö alla oleva tarpeellinen, muttei siitä haittaakaan lie.
/*if(!isset($_SESSION['tunnistus']) || $_SESSION['tunnistus'] != 'kunnossa')
{
    echo Kayttajatekstit::$ilmoitus_sessio_vanhentunut;
    exit(); // Varmistetaan, että mitään alla olevaa ei suoriteta.
}

// Tarkistetaan istunnon laiskan ajan kesto ja kirjataan laiska ulos:
else if(isset($_SESSION['viim_aktiivisuus']) &&
    ((time()-$_SESSION['viim_aktiivisuus']) > Aikarajat::$LAISKA_ISTUNTOAIKA)){

    require_once('../kayttajahallinta/php_kayttajahallintametodit.php');

    echo Yleisarvoja::$istunto_vanhentunut;
    $kansiotaso = 2;    // Toisella tasolla.
    toteuta_passiivinen_ulos_toiminto($kansiotaso);
    exit;
}
else    // Jos tunnistus on kunnossa
{
    require_once('../asetukset/valtuudet.php');
    require_once('../asetukset/Kielet.php');
    require_once('../liikuntamuistio/muistioasetukset.php');
    require_once('../kayttajahallinta/php_kayttajahallintametodit.php');
    require_once('../php_yleinen/Ilmoitusrajapinta.php');
    require_once('../php_yleinen/Ilmoitus.php');
    require_once('../php_yleinen/Malliluokkapohja.php');
    require_once('../php_yleinen/Kuva.php');
    require_once('../bongaus/bongausasetukset.php');

    date_default_timezone_set  ('Europe/Helsinki'); // Vaaditaan alkaen MySQL5.1
    //$koodaus = "ISO-8859-1";
    $koodaus = "UTF-8";

    // Tämä ei ole välttämättä käyttäjä:
    $henkilo_id = isset($_REQUEST['henkilo_id']) ?
                            $_REQUEST['henkilo_id']: "-1";

    // Ikkunan leveys saaadaan ajax-kyselyn kautta.
    $ikkunan_leveys = isset($_REQUEST['ikkunan_leveys']) ?
                    $_REQUEST['ikkunan_leveys']: Kuva::$KUVALAATIKON_OLETUSLEV;

    // Ikkunan leveys saaadaan ajax-kyselyn kautta.
    $ikkunan_korkeus = isset($_REQUEST['ikkunan_korkeus']) ?
                    $_REQUEST['ikkunan_korkeus']: Kuva::$OLETUSMAKSIMIKORKEUS;


    $id_alb = isset($_REQUEST['id_alb']) ? $_REQUEST['id_alb']: 1;
    $id_lj = isset($_REQUEST['id_lj']) ? $_REQUEST['id_lj']: 1;
    $id_kuva = isset($_REQUEST['id_kuva']) ? $_REQUEST['id_kuva']: 1;

    // Monesko diaesityksen kuva:
    $kuvan_nro = isset($_REQUEST['kuvan_nro']) ? $_REQUEST['kuvan_nro']: 1;

    // Kokoelmanimi:
    $kokoelmanimi = isset($_REQUEST['kokoelmanimi']) ?
            $_REQUEST['kokoelmanimi']: "";

    // Haetaan kuvahakumoodi sessiotiedoista, tai jos muuttunut, muutetaan
    // sessiotieto samaksi:
    if(isset($_REQUEST['kuvahakumoodi'])){
        $_SESSION['kuvahakumoodi'] = $_REQUEST['kuvahakumoodi'];
        $kuvahakumoodi = $_SESSION['kuvahakumoodi'];
    }
    else if(isset($_SESSION['kuvahakumoodi'])){
        $kuvahakumoodi = $_SESSION['kuvahakumoodi'];
    }
    else{
        $kuvahakumoodi = Kuvat::$KUVAHAKUMOODI_pikkukuvat_tiedosto;
    }

    $kuvia_rinnakkain = isset($_REQUEST['kuvia_rinnakkain']) ?
        $_REQUEST['kuvia_rinnakkain']: Kuva::$ESIKATSELUKUVIA_RIVILLA_LKM_OLETUS;

    // Ajax-kyselykysymys:
    $kysymys = isset($_REQUEST['kysymys']) ? $_REQUEST['kysymys']: "";

    // Painiketekstejä (ei tarvitsisi, mutta ei jaksa muuttaa kaikkialla):
    $tallenna_uusi_alb = Painikkeet::$TALLENNA_UUSI_ALB_VALUE;
    $tallenna_muokkaus_alb = Painikkeet::$TALLENNA_MUOKKAUS_ALB_VALUE;
    $poista_alb = Painikkeet::$POISTA_ALB_VALUE;
    $poistovahvistus_alb = Painikkeet::$POISTOVAHVISTUS_ALB_VALUE;
    $peru_poisto_alb = Painikkeet::$PERU_POISTO_ALB_VALUE;
    $muokkaa_alb = Painikkeet::$MUOKKAA_ALB_VALUE;
    $peruminen_alb = Painikkeet::$PERUMINEN_ALB_VALUE;
    $uusi_albumi = Painikkeet::$UUSI_ALB_VALUE;
    $katso_alb = Painikkeet::$KATSO_ALB_VALUE;
    $takaisin_albumilistaan_alb = Painikkeet::$TAKAISIN_ALBUMILISTAAN_ALB_VALUE;
    $ed_kuva_alb = Painikkeet::$ED_KUVA_ALB_VALUE;
    $seur_kuva_alb = Painikkeet::$SEUR_KUVA_ALB_VALUE;
    $tauko_alb = Painikkeet::$TAUKO_ALB_VALUE;
    $jatka_esitysta_alb = Painikkeet::$JATKA_ESITYSTA_ALB_VALUE;
    $tallenna_uusi_kuva = Painikkeet::$TALLENNA_UUSI_KUVA_VALUE;
    $tallenna_muokkaus_kuva = Painikkeet::$TALLENNA_MUOKKAUS_KUVA_VALUE;
    $poista_kuva = Painikkeet::$POISTA_KUVA_VALUE;
    $poistovahvistus_kuva = Painikkeet::$POISTOVAHVISTUS_KUVA_VALUE;
    $peru_poisto_kuva = Painikkeet::$PERU_POISTO_KUVA_VALUE;
    $muokkaa_kuva = Painikkeet::$MUOKKAA_KUVA_VALUE;
    $peruminen_kuva = Painikkeet::$PERUMINEN_KUVA_VALUE;
    $uusi_kuva = Painikkeet::$UUSI_KUVA_VALUE;


    $alaikainen = true;

    $omaid = $_SESSION['tiedot']->id;
    $omat_valtuudet = $_SESSION['tiedot']->valtuudet;
    $kuningas = on_kuningas_pika($omat_valtuudet);

    /************************ KYSELYT *****************************************/

    /************************ KELLONAIKA **************************************
    // Ensin otetaan "kevyet" tiedustelut, joihin ei tarvita tietokantayhteyttä.
    // Kellonaikakysely:
    if($kysymys == "kellonaika"){
        echo date("\k\l\o H:i:s");
    }
    else if($kysymys == "vaihda_kuvahakumoodi"){
        $_SESSION['kuvahakumoodi'] = $kuvahakumoodi;
        if($kuvahakumoodi == Kuvat::$KUVAHAKUMOODI_pikkukuvat_tiedosto){
            $viesti = "Esikatselukuvien hakutapa: tiedostosta pikkukuvina (nopea)";
        }
        else if($kuvahakumoodi == Kuvat::$KUVAHAKUMOODI_pikkukuvat_tietokanta){
            $viesti = "Esikatselukuvien hakutapa: tietokannasta pikkukuvina".
                " (pienill&auml; nopea, mutta isommilla t&ouml;kkii ".
                "jostakin syyst&auml;)";
        }
        else {
            $viesti = "Esikatselukuvien hakutapa: tiedostosta ".
                    "isoina kuvina (vanha tapa)";
        }
        echo $viesti;
    }


    // Sitten raskaammat kyselyt liittyen tietokantaan:
    else{
        // Haetaan asetukset ja avataan yhteys tietokantaan:
        require_once('../php_yleinen/php_yleismetodit.php');
        require_once('../php_yleinen/aika.php');
        
        require_once('../asetukset/tietokantayhteys.php');
        require_once('../yhteiset/php_yhteiset.php');
        require_once('../kuvatoiminnot/php_kuvametodit.php');
        require_once('../php_yleinen/Tietokantaolio.php');
        require_once('../php_yleinen/html.php');
        require_once('../viestit/Viesti.php');
        require_once('../yhteiset/Parametrit.php');
        require_once('../yhteiset/Palaute.php');
        require_once('../pikakommentointi/Pikakommenttitekstit.php');
        require_once('../pikakommentointi/Pikakommentti.php');
        require_once('../bongaus/lajiluokat/Kuvaus.php');
        require_once('../bongaus/lajiluokat/Lajiluokka.php');

        // Yhdistetään tietokantaan:
        $tietokantaolio = new Tietokantaolio($dbtyyppi, $dbhost, $dbuser, $dbsalis);
        //$tietokantaolio->yhdista_tietokantaan($dbnimi);
        $tietokantaolio->yhdista_tietokantaan_uusi_yhteys($dbnimi);

        /********************************************************************
        // Tarkistetaan, ettei käyttäjää ole potkaistu tai itse kirjautunut ulos:
        // Tämä voisi olla aiemmin, mutta en halunnut rasittaa liian usein
        // tapahtuvaksi.
        if(!online($_SESSION['tiedot']->id, $tietokantaolio)){
            $kansiotaso = 2;
            toteuta_passiivinen_ulos_toiminto($kansiotaso);
            echo Yleisarvoja::$istunto_vanhentunut;
            exit;
        }
        /******************************************************************

        // Ikätarkistus:
        if(on_alaikainen($omaid, $tietokantaolio)){
            $alaikainen = true;
        }
        else{
            $alaikainen = false;
        }

        // Haetaan parametrit;
        $parametriolio = new Parametrit($kokoelmanimi, $omaid, $tietokantaolio);

        /******************* ESIKATSELUKUVAHAKU *******************************
        if($kysymys == "hae_esikatselukuvat"){
            $kuvahtml= "Kokoelmanimi tuntematon!";
            if($kokoelmanimi == Kuva::$KUVAT_ALBUMIT){
                $kuvahtml = nayta_albumin_esikatselu($omaid, $id_alb, $uusi_kuva,
                                                $takaisin_albumilistaan_alb,
                                                $tietokantaolio, $omat_valtuudet,
                                                $kuvia_rinnakkain, $ikkunan_leveys,
                                                $kuvahakumoodi);
            }
            else if($kokoelmanimi == Kuva::$KUVAT_BONGAUS){
                $palauteolio = toteuta_nayta_esikatselukuvat($parametriolio);
                $kuvahtml = $palauteolio->get_sisalto();
            }
            else{
                $kuvahtml= "Kokoelmanimi '$kokoelmanimi' tuntematon!";
            }
            echo $kuvahtml;
        }
        // Kuva-albumiin:
        else if($kysymys == "hae_kuva_ja_tiedot"){

            $pikkukuvakansio_osoite = Kuva::$kansion_os_kuvat_pikkukuvat;

            $kuvahtml = nayta_albumin_yksi_kuva($omaid,
                                $id_alb,
                                $id_kuva,
                                $muokkaa_kuva,
                                $uusi_kuva,
                                $poista_kuva,
                                $takaisin_albumilistaan_alb,
                                $ed_kuva_alb,
                                $seur_kuva_alb,
                                $tauko_alb,
                                $jatka_esitysta_alb,
                                $tietokantaolio,
                                $omat_valtuudet,
                                $ikkunan_leveys,
                                $ikkunan_korkeus,
                                $pikkukuvakansio_osoite);
            echo $kuvahtml;
        }

        // Kuva-albumiin:
        else if($kysymys == "hae_bongauskuva_ja_tiedot"){

            $pikkukuvakansio_osoite = Kuva::$kansion_os_bongauskuvat_pikkukuvat;

            $kuvahtml = bongaus_nayta_albumin_yksi_kuva(
                                                $omaid,
                                                $id_lj,
                                                $id_kuva,
                                                $tietokantaolio,
                                                $omat_valtuudet,
                                                $ikkunan_leveys,
                                                $pikkukuvakansio_osoite);
            echo $kuvahtml;
        }

        // Kuva-albumin kuva halutaan diaesitystä varten. Tarvittavia tietoja
        // ovat ainoastaan albumin id, kuvan nro (monesko kuva) ja
        // ikkunan leveys ja korkeus.
        else if($kysymys == "hae_diaesityskuva_albumeista"){

            $kuvahtml = "Virhe (ajax_kyselyt_kuvat.php)";

            if($kokoelmanimi == Kuva::$KUVAT_ALBUMIT){
                $albumin_kuva_idt =
                                hae_albumin_kuvien_idt($id_alb, $tietokantaolio);
            }
            else if($kokoelmanimi == Kuva::$KUVAT_BONGAUS){
                $albumin_kuva_idt =
                        bongaus_hae_kuvien_idt($id_lj, $tietokantaolio);
            }
            else{
                $albumin_kuva_idt = array();    // Tyhjä taulukko.
            }
            

            if(!empty ($albumin_kuva_idt) && 
                (sizeof($albumin_kuva_idt) >= $kuvan_nro)){
                $naytettavan_kuvan_id = $albumin_kuva_idt[($kuvan_nro-1)];

                /* Haetaan id:hen liittyvä kuva (1 kpl) *
                $hakulause = "SELECT kuvat.id AS id,
                                    kuvat.henkilo_id AS henkilo_id,
                                    kuvat.kuvaotsikko AS kuvaotsikko,
                                    kuvat.kuvaselitys AS kuvaselitys,
                                    kuvat.vuosi AS vuosi,
                                    kuvat.kk AS kk,
                                    kuvat.paiva AS paiva,
                                    kuvat.src AS src,
                                    kuvat.leveys AS leveys,
                                    kuvat.korkeus AS korkeus,
                                    kuvat.tiedostokoko AS tiedostokoko,
                                    kuvat.tiedostotunnus AS tiedostotunnus,
                                    kuvat.tiedostonimi AS tiedostonimi,
                                    kuvat.tallennusaika_sek AS tallennusaika_sek
                            FROM kuvat
                            WHERE kuvat.id = $naytettavan_kuvan_id
                            LIMIT 1";

                $osumataulukko = $tietokantaolio->
                                tee_OMAhaku_oliotaulukkopalautteella($hakulause);

                // Luodaan kuvaolio:
                if(!empty ($osumataulukko)){
                    $tk_kuva = $osumataulukko[0];
                    if($kokoelmanimi == Kuva::$KUVAT_ALBUMIT){
                        $kuva = new Kuva($tk_kuva, Kuva::$KUVAT_ALBUMIT);
                    }
                    else if($kokoelmanimi == Kuva::$KUVAT_BONGAUS){
                        $kuva = new Kuva($tk_kuva, Kuva::$KUVAT_BONGAUS);
                    }
                    else{
                        $kuva = "";
                    }

                    if($kuva == ""){
                        $kuvahtml = "";
                    }
                    else{
                        // Ja tulostetaan varsinainen kuvahtml:
                        $kuvahtml = $kuva->nayta_diaesityskuva($ikkunan_leveys,
                                                        $ikkunan_korkeus);
                    }
                }
            }
            else{ // kuvaidt ei täsmää kuvannroon
                $kuvahtml = "Kuvia ei l&ouml;ytynyt! Albumin id: ".$id_alb;
            }

            echo $kuvahtml;
        }

        /*********** UUSIEN KUVIEN LUKUMÄÄRÄN HAKU ****************************
        else if($kysymys == "uudet_kuvat_lkm"){ // Haetaan uusia kuvia:

            $lkm = "";

            // Jos viimeistä katseluaikaa ei ole asetettu, asetetaan 0:
            if(!isset($_SESSION['kuvat_katsottu_viimeksi'])){
                $_SESSION['kuvat_katsottu_viimeksi'] = 0;
            }

            // Tämä laskee myös yksityisiin lisätyt kuvat:
            /*$hakulause = "SELECT COUNT(*) AS uudet_lkm
                        FROM kuvat
                        WHERE tallennusaika_sek > ".
                                $_SESSION['kuvat_katsottu_viimeksi'];*

            // Ei ota huomioon yksityisiin kansioihin lisättyjä kuvia:
            $hakulause = "SELECT COUNT(*) AS uudet_lkm
                        FROM kuvat
                        JOIN kuva_albumi_linkit AS linkit
                        ON kuvat.id = linkit.kuva_id
                        JOIN albumit
                        ON albumit.id = linkit.albumi_id
                        WHERE (kuvat.tallennusaika_sek > ".
                                $_SESSION['kuvat_katsottu_viimeksi']."
                        AND albumit.suojaus <> ".Albumisuojaus::$YKSITYINEN.")";

            $hakutulos = $tietokantaolio->tee_OMAhaku($hakulause);
            if($hakutulos != false){
                $lkmtaulukko =
                        $tietokantaolio->hae_osumarivit_olioina($hakutulos);

                // Kai taulukossa aina vähintään nolla on, joten
                // tämä tarkistus lienee tarpeeton.
                if(sizeof($lkmtaulukko)>0){
                    $lkm = $lkmtaulukko[0]->uudet_lkm;
                }
                else{
                    $lkm = 0;
                }
            }
            
            echo $lkm;
        }
        else{
            echo "Tuntematon kysely!";
        }


        /******************* KESKUSTELUHAKU ************************************/
        /*else if($kysymys == "hae_keskustelu"){
            $kesk_html = hae_keskustelu($keskustelun_id, $auki, $kiinni_lkm,
                        $aikaraja,$tietokantaolio, $kuningas, $omaid);

            // Tämä pitää olla, ettei html-tageja lueta elementeiksi.
            $sis = htmlspecialchars($kesk_html,ENT_NOQUOTES);

            header('Content-type: text/xml');

            // HUOM! encoding alla pitää olla, muuten ääkköset aiheuttavat
            // ajax-hommissa. Jostakin syystä utf-8 ei toimi myöskään
            // omalla palvelimella. Kerkkaset.fissa pitää olla UTF-8 Hmm..
            echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
            echo '<keskustelu>';
            echo '<k_id>'.$keskustelun_id.'</k_id>';
            echo '<kesk>'.$sis.'</kesk>';
            echo '</keskustelu>';
        }

        $tietokantaolio->sulje_tietokanta($dbnimi);
    //}
}*/
?>