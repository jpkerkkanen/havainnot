<?php
/* 
 * Description of Tekstityokalupalkki
 *
 * Luo painikkeet, joiden avulla esimerkiksi textarea-elementin tekstiä
 * voi muokata.
 *
 * HUOM! Olio vaatii toimiakseen js-metodit
 * tiedostossa "tekstinmuokkausmetodit.js".
 *
 * @author Jukka-Pekka Kerkkänen
 */
class Tekstityokalupalkki {
    //put your code here

    private $kaavapainikeryhmatyyli =
        "background-color:pink;display:inline;padding:2px";
    private $painikeryhmatyyli =
        "background-color:#ddd;display:inline;padding:3px; margin:0;";

    /*public static $taulukko_alkutagi =
        "\"&lt;table class=\'taulukko1\'&gt;&lt;tr&gt;&lt;td&gt;\"";
    public static $taulukko_lopputagi =
        "\"&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;\"";*/

    /**
     * Näillä säädetään palkkiin tulevia painikkeita.
     */
    public static $PERUSPAINIKKEET = 1;
    public static $PERUSPAINIKKEET_JA_MATEMAATTISET = 2;


    /* Tekstissä olevat lainausmerkit voi kirjoittaa vinoina, jolloin ei
     * ongelmaa html-kääntäjän kanssa: */
    public static $tekstimuokkaus_lainausmerkkiVINO_yks = "&rsquo;";
    public static $tekstimuokkaus_lainausmerkkiVINO_kaks = "&rdquo;";
    public static $tekstimuokkaus_sulut = "(...)";
    public static $tekstimuokkaus_hakasulut = "[...]";
    public static $tekstimuokkaus_aaltosulut = "{...}";

    public static $tekstimuokkaus_kertomerkki = "&sdot;";
    public static $tekstimuokkaus_miinusmerkki = "&#8722;"; 
    public static $tekstimuokkaus_lihavointi = "<b>a</b>";
    public static $tekstimuokkaus_kursivointi = "<i>a</i>";
    public static $tekstimuokkaus_alleviivaus = "<u>a</u>";
    public static $tekstimuokkaus_ylaindeksi = "a<sup style='font-size:60%'>2</sup>";
    public static $tekstimuokkaus_alaindeksi = "a<sub style='font-size:60%'>2</sub>";
    public static $tekstimuokkaus_korostus = "<span class='korostus'>a</span>";
    public static $tekstimuokkaus_rivinvaihto = "&lt;br /&gt;"; //Rivinvaihto
    public static $tekstimuokkaus_kappale = "&lt;p&gt;";
    public static $tekstimuokkaus_code = "&lt;code&gt;";
    public static $tekstimuokkaus_linkki = "&lt;a&gt;";
    public static $tekstimuokkaus_erikoismerkit = "\"&lt;\", \"&gt;\" ja \"&amp;\"";

    public static $tekstimuokkaus_tuplapystyviiva = "&#2551";

    public static $tekstimuokkaus_skripti = "&lt;script&gt;";
    public static $tekstimuokkaus_div = "&lt;div&gt;";

    // Nämä pitää kääntää tarvittaessa kielille:
    public static $tekstimuokkaus_koodi = "Koodi";
    public static $tekstimuokkaus_taulukko = "Taulukko";
    public static $tekstimuokkaus_taulukkorivi = "Rivi";
    public static $tekstimuokkaus_taulukkosolu = "Solu";


    public static $tekstimuokkaus_kaava = "Kaava";
    public static $tekstimuokkaus_murtoluku = "Murtoluku";
    public static $tekstimuokkaus_kaavan_jakoviiva = "Jakoviiva";
    public static $tekstimuokkaus_laventaja = "Laventaja";
    public static $tekstimuokkaus_supistaja = "Supistaja";

    public static $tekstimuokkaus_suure = "Suure";
    public static $tekstimuokkaus_yksikko = "Yksikk&ouml;";
    
    public static $tekstimuokkaus_neliojuuri_1rivi = 
            "<span style='font-size:60%'>&radic;</span>";
    public static $tekstimuokkaus_neliojuuri_2rivi = "&radic;";

    // Apupainikkeet:
    public static $tekstimuokkaus_vie_merkki = "->";

    

    // Painikevihjeet:
    public static $tekstimuokkaus_rivinvaihto_title =
        "Lis&auml;&auml; rivinvaihdon (&lt;br /&gt;)";

    public static $tekstimuokkaus_kappale_title =
        "Lis&auml;&auml; uuden kappaleen (&lt;p&gt;&lt;/p&gt;)";

    public static $tekstimuokkaus_linkki_title =
        "Lis&auml;&auml; uuden linkin (a-elementin)";

    public static $tekstimuokkaus_code_title =
        "Lis&auml;&auml; uuden code-elementin (&lt;code&gt;&lt;/code&gt;)";

    public static $tekstimuokkaus_vie_merkki_title =
        "Lis&auml;&auml; alasvetovalikon merkin tekstiin";
    public static $tekstimuokkaus_lainausmerkki_yks_title =
        "Lis&auml;&auml; lainausmerkit valinnan ymp&auml;rille";
    public static $tekstimuokkaus_lainausmerkki_kaks_title =
        "Lis&auml;&auml; lainausmerkit valinnan ymp&auml;rille";
    public static $tekstimuokkaus_kertomerkki_title =
        "Lis&auml;&auml; kertomerkin";
    public static $tekstimuokkaus_miinusmerkki_title =
        "Lis&auml;&auml; pitk&auml;n miinusmerkin";
    public static $tekstimuokkaus_sulut_title =
        "Lis&auml;&auml; sulut valinnan ymp&auml;rille";
    public static $tekstimuokkaus_hakasulut_title =
        "Lis&auml;&auml; hakasulut valinnan ymp&auml;rille";
    public static $tekstimuokkaus_aaltosulut_title =
        "Lis&auml;&auml; aaltosulut valinnan ymp&auml;rille";

    public static $tekstimuokkaus_likiarvo_title =
        "Lis&auml;&auml; likiarvomerkin";

    public static $tekstimuokkaus_tuplapystyviiva_title = "Tuplapystyviiva";

     public static $tekstimuokkaus_skripti_title = 
        "Javascript-tagit: &lt;script&gt;&lt;/script&gt;";

     public static $tekstimuokkaus_div_title =
        "Lis&auml;&auml; div-elementin (&lt;div&gt;&lt;/div&gt;)";
     
    public static $tekstimuokkaus_neliojuuri_1rivi_title = 
            "Neli&ouml;juuri kaavaan (juurrettava yhdell&auml; rivill&auml;)";
    public static $tekstimuokkaus_neliojuuri_2rivi_title = 
            "Iso neli&ouml;juuri kaavaan (juurrettavaksi voi lis&auml;t&auml; my&ouml;s murtoluvun tai neli&ouml;juuren)";

    // Kaava MIKSI EI TOIMI NÄIN?? LIITTYY ILMEISESTI SIIHEN, ETTÄ
    // LUOKAN MUUTTUJAT VOI ESITELLÄ TÄÄLLÄ, MUTTA KOVIN ISOJA OPERAATIOITA
    // EI SULATETA:
    /*public static $jakoviiva = Kaavaeditori::$jakomerkki;
    public static $alku = Kaavaeditori::$kaavan_alku;
    public static $loppu = Kaavaeditori::$kaavan_loppu;
    public static $jako_alku = Kaavaeditori::$jako_alku;
    public static $jako_loppu = Kaavaeditori::$jako_loppu;

    public static $tekstimuokkaus_kaava_title =
        $alku."Matemaattinen kaava".$loppu;
    public static $tekstimuokkaus_murtoluku_title =
        $alku."Matemaattinen kaava ".$kaavan_alku."murtolukumuoto".$jako_loppu.
        $loppu;
    public static $tekstimuokkaus_kaavan_jakoviiva_title =
        "Lis&auml;&auml; kaavan murtolukumuotoon jakoviivan merkin (".
        $jakoviiva.").";*/

    // Ilmeisesti ei tykkää muuttujista merkkijonossa.
    public static $tekstimuokkaus_kaava_title =
        "{@Matemaattinen kaava@}";
    public static $tekstimuokkaus_murtoluku_title =
        "{@Matemaattinen kaava [@murtolukumuoto@]@}";
    public static $tekstimuokkaus_kaavan_jakoviiva_title =
        "Lis&auml;&auml; kaavan murtolukumuotoon jakoviivan merkin (##)";
    public static $tekstimuokkaus_laventaja_title =
        "Lis&auml;&auml; kaavaan laventajan";
    public static $tekstimuokkaus_supistaja_title =
        "Lis&auml;&auml; kaavaan supistajan";


    // Suure ja yksikkö:
    public static $tekstimuokkaus_suure_title = "Fysiikan suureen muotoilu";
    public static $tekstimuokkaus_yksikko_title = "Fysiikan yksik&ouml;n muotoilu";

    public static $tekstimuokkaus_lihavointi_title = "Lihavoi valitun tekstin";
    public static $tekstimuokkaus_kursivointi_title = "Kursivoi valitun tekstin";
    public static $tekstimuokkaus_alleviivaus_title = "Alleviivaa valinnan";
    public static $tekstimuokkaus_korostus_title = "Korostaa valinnan";
    public static $tekstimuokkaus_ylaindeksi_title = "Tekee valinnasta yl&auml;indeksin";
    public static $tekstimuokkaus_alaindeksi_title = "Tekee valinnasta alaindeksin";

    public static $tekstimuokkaus_koodi_title = 
    "Muokkaa n&auml;ytett&auml;v&auml;n koodin siististi";
    public static $tekstimuokkaus_taulukko_title = 
    "Lis&auml;&auml; taulukkomerkkauksen (&lt;table&gt;&lt;/table&gt;) valinnan ymp&auml;rille";
    public static $tekstimuokkaus_taulukkorivi_title =
    "Lis&auml;&auml; taulukon rivin merkkauksen (&lt;tr&gt;&lt;/tr&gt;) valinnan ymp&auml;rille";
    public static $tekstimuokkaus_taulukkosolu_title =
    "Lis&auml;&auml; taulukon solun merkkauksen (&lt;td&gt;&lt;/td&gt;) valinnan ymp&auml;rille";

    public static $tekstimuokkaus_erikoismerkit_title =
    "Muuttaa HTML-erikoismerkit ep&auml;erikoisiksi";

    private $elementin_id;
    private $alasvetovalikon_id;

    public function __construct($elementin_id, $alasvetovalikon_id) {
        $this->elementin_id = $elementin_id;
        $this->alasvetovalikon_id = $alasvetovalikon_id;
    }

    function luo_painikkeet($tarkennus){
        /* Lisätään muutama painike tekstin muokkausta varten: */
        $lainausmerkki_yks = Tekstityokalupalkki::$tekstimuokkaus_lainausmerkkiVINO_yks;
        $lainausmerkki_kaks = Tekstityokalupalkki::$tekstimuokkaus_lainausmerkkiVINO_kaks;
        $kertomerkki = Tekstityokalupalkki::$tekstimuokkaus_kertomerkki;
        $miinusmerkki = Merkit::$miinusmerkki;

        // Olkoot arvot ja nimet samoja. Tässä ei niin merkitystä.
        $arvot = Array(
                        Merkit::$alpha_pieni, 
                        Merkit::$beta_pieni, 
                        Merkit::$gamma_pieni, 
                        Merkit::$delta_pieni, 
                        Merkit::$delta_iso, 
                        Merkit::$Omega_iso,
                        Merkit::$pi_pieni,
                        Merkit::$rho_pieni,
                        Merkit::$epsilon_pieni,
                        Merkit::$neliojuuri,
                        Merkit::$aareton,
                        Merkit::$kaikille,
                        Merkit::$ei_kuulu_joukkoon,
                        Merkit::$erisuuri,
                        Merkit::$on_olemassa,
                        Merkit::$suurempi_tai_yhtasuuri,
                        Merkit::$pienempi_tai_yhtasuuri,
                        Merkit::$summamerkki,
                        Merkit::$integraali,
                        Merkit::$joukkojen_yhdiste,
                        Merkit::$nuoli_tupla_oik_seuraus,
                        Merkit::$nuoli_ekvivalenssi,
                        Merkit::$nuoli_ylos,
                        Merkit::$nuoli_oik,
                        Merkit::$nuoli_alas,
                        Merkit::$nuoli_vas
            );
        $nimet = Array(
                        Merkit::$alpha_pieni, 
                        Merkit::$beta_pieni, 
                        Merkit::$gamma_pieni, 
                        Merkit::$delta_pieni, 
                        Merkit::$delta_iso, 
                        Merkit::$Omega_iso,
                        Merkit::$pi_pieni,
                        Merkit::$rho_pieni,
                        Merkit::$epsilon_pieni,
                        Merkit::$neliojuuri,
                        Merkit::$aareton,
                        Merkit::$kaikille,
                        Merkit::$ei_kuulu_joukkoon,
                        Merkit::$erisuuri,
                        Merkit::$on_olemassa,
                        Merkit::$suurempi_tai_yhtasuuri,
                        Merkit::$pienempi_tai_yhtasuuri,
                        Merkit::$summamerkki,
                        Merkit::$integraali,
                        Merkit::$joukkojen_yhdiste,
                        Merkit::$nuoli_tupla_oik_seuraus,
                        Merkit::$nuoli_ekvivalenssi,
                        Merkit::$nuoli_ylos,
                        Merkit::$nuoli_oik,
                        Merkit::$nuoli_alas,
                        Merkit::$nuoli_vas
            );
        $name_arvo = "";
        $id_arvo = $this->alasvetovalikon_id;
        $class_arvo = "";
        $oletusvalinta_arvo = "";
        $otsikko = "";
        $onclick_metodinimi = "lisaaMerkki";
        $onclick_metodiparametrit_array = Array("\"".$this->elementin_id."\"",
                    "this.options[this.selectedIndex].value");
                    
        
        $merkkivalikko = Html::luo_pudotusvalikko_onChange($arvot,
                                $nimet,
                                $name_arvo,
                                $id_arvo,
                                $class_arvo,
                                $oletusvalinta_arvo,
                                $otsikko,
                                $onclick_metodinimi,
                                $onclick_metodiparametrit_array);

        $tekstimuokkauspainikkeet =
        "<div style=".$this->painikeryhmatyyli.">".
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"b\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_lihavointi_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_lihavointi.
        "</button>".
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"i\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_kursivointi_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_kursivointi.
        "</button>".
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"span\",\"korostus\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_korostus_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_korostus.
        "</button>".
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"u\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_alleviivaus_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_alleviivaus.
        "</button>".
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"sup\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_ylaindeksi_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_ylaindeksi.
        "</button>".
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"sub\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_alaindeksi_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_alaindeksi.
        "</button>".

        "<button type='button'".
            "onclick='lisaaMerkki(\"".$this->elementin_id."\",\"".$kertomerkki."\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_kertomerkki_title."'>".
            $kertomerkki.
        "</button>".
        "<button type='button'".
            "onclick='lisaaMerkki(\"".$this->elementin_id."\",\"".
                                    Merkit::$miinusmerkki."\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_miinusmerkki_title."'>".
            Merkit::$miinusmerkki.
        "</button>".
        "<button type='button'".
            "onclick='lisaaMerkki(\"".$this->elementin_id."\",\"".
                                    Merkit::$likiarvo."\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_likiarvo_title."'>".
            Merkit::$likiarvo.
        "</button>".

        "<button type='button'".
            "onclick='lisaaMerkki(\"".$this->elementin_id."\",\"".
                                    Merkit::$tuplapystyviiva."\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_tuplapystyviiva_title."'>".
            Merkit::$tuplapystyviiva.
        "</button>".

        "<button type='button'".
            "onclick='lisaaSulut(\"".$this->elementin_id."\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_sulut_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_sulut.
        "</button>".
        "<button type='button'".
            "onclick='lisaaEriMerkkipari(\"".$this->elementin_id."\",\"[\",\"]\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_hakasulut_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_hakasulut.
        "</button>".

        "<button type='button'".
            "onclick='lisaaEriMerkkipari(\"".$this->elementin_id."\",\"{\",\"}\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_aaltosulut_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_aaltosulut.
        "</button>".
        

        "<button type='button'".
            "onclick='lisaaMerkkipari(\"".$this->elementin_id.
                                        "\",\"".Merkit::$lainausmerkki_yks."\")'".
            "title='".
                Tekstityokalupalkki::$tekstimuokkaus_lainausmerkki_yks_title."'>".
            Merkit::$lainausmerkki_yks."...".Merkit::$lainausmerkki_yks.
        "</button>".
        "<button type='button'".
            "onclick='lisaaMerkkipari(\"".$this->elementin_id.
                                   "\",\"".$lainausmerkki_kaks."\")'".
            "title='".
                Tekstityokalupalkki::$tekstimuokkaus_lainausmerkki_kaks_title."'>".
            Merkit::$lainausmerkki_kaks."...".Merkit::$lainausmerkki_kaks.
        "</button>";

        

        // Merkkivalikko:
        // Luodaan ensin vientipainike:
        $painike = "<button type='button'".
            "onclick='lisaaMerkki(\"".$this->elementin_id."\",".
                "document.getElementById(\"".$this->alasvetovalikon_id."\").".
                "options[document.getElementById(\"".
                $this->alasvetovalikon_id."\").selectedIndex].value)'".

            "title='".Tekstityokalupalkki::$tekstimuokkaus_vie_merkki_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_vie_merkki.
        "</button>";

        // Sijoitetaan alasvetovalikko ja sen painike taulukkoon:
        $tekstimuokkauspainikkeet .= $merkkivalikko.$painike;

        $tekstimuokkauspainikkeet .= "<br />";

        $tekstimuokkauspainikkeet .=
        "<button type='button'".
            "onclick='lisaaMerkki(\"".$this->elementin_id."\",\"<br />\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_rivinvaihto_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_rivinvaihto.
        "</button>".

        // Kappalevaihto:
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"p\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_kappale_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_kappale.
        "</button>".

        // Linkki:
        "<button type='button'".
            "onclick='lisaaLinkkitagit(\"".$this->elementin_id."\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_linkki_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_linkki.
        "</button>".

        // Div:
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"div\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_div_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_div.
        "</button>".

        // Taulukko:
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"taulukko\",\"taulukko1\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_taulukko_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_taulukko.
        "</button>".

        /*"<button type='button'".
            "onclick='lisaaEriMerkkipari(\"".$this->elementin_id."\",".
                Tekstityokalupalkki::$taulukko_alkutagi.
                Tekstityokalupalkki::$taulukko_lopputagi.")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_taulukko_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_taulukko.
        "</button>".*/

        // Rivi taulukkoon:
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"tr\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_taulukkorivi_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_taulukkorivi.
        "</button>".

        // Solu taulukkoon:
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"td\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_taulukkosolu_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_taulukkosolu.
        "</button>".

        // Script-tagit:
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"script\",\"\",\"\",\"text/javascript\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_skripti_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_skripti.
        "</button>".

        // Lyhytkoodipainike:
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"code\",\"\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_code_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_code.
        "</button>".

        // Koodipainike:
        "<button type='button'".
            "onclick='lisaaTagit(\"".$this->elementin_id."\",\"div\",\"koodi\",\"\",\"\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_koodi_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_koodi.
        "</button>";

        // Rivinvaihto:
        $tekstimuokkauspainikkeet .= "<br />".

        // HTML-erikoismerkkien muotoilu:
        "<button type='button'".
            "onclick='muokkaaErikoiset(\"".$this->elementin_id."\")'".
            "title='".Tekstityokalupalkki::$tekstimuokkaus_erikoismerkit_title."'>".
            Tekstityokalupalkki::$tekstimuokkaus_erikoismerkit.
        "</button>";

        

        // Lisätään vielä kaavan lisäämiseen tarvittavat painikkeet, jos
        // valittu matemaattiset painikkeen:
        if($tarkennus == Tekstityokalupalkki::$PERUSPAINIKKEET_JA_MATEMAATTISET){
            $tekstimuokkauspainikkeet .=
            "<span style=".$this->kaavapainikeryhmatyyli."><button type='button'".
                "onclick='lisaaEriMerkkipari(\"".$this->elementin_id."\",\"".
                    Kaavaeditori::$kaavan_alku."\",\"".
                    Kaavaeditori::$kaavan_loppu."\")'".
                "title='".Tekstityokalupalkki::$tekstimuokkaus_kaava_title."'>".
                Tekstityokalupalkki::$tekstimuokkaus_kaava.
            "</button>".

            "<button type='button'".
                "onclick='lisaaEriMerkkipari(\"".$this->elementin_id."\",\"".
                    Kaavaeditori::$jako_alku."\",\"".
                    Kaavaeditori::$jako_loppu."\")'".
                "title='".Tekstityokalupalkki::$tekstimuokkaus_murtoluku_title."'>".
                Tekstityokalupalkki::$tekstimuokkaus_murtoluku.
            "</button>".

            "<button type='button'".
                "onclick='lisaaMerkki(\"".$this->elementin_id."\",\"".
                                        Kaavaeditori::$jakomerkki."\")'".
                "title='".Tekstityokalupalkki::$tekstimuokkaus_kaavan_jakoviiva_title."'>".
                Tekstityokalupalkki::$tekstimuokkaus_kaavan_jakoviiva.
            "</button>".

            "<button type='button'".
                "onclick='lisaaEriMerkkipari(\"".$this->elementin_id."\",\"".
                    Kaavaeditori::$supistaja_alku."\",\"".
                    Kaavaeditori::$supistaja_loppu."\")'".
                "title='".Tekstityokalupalkki::$tekstimuokkaus_supistaja_title."'>".
                Tekstityokalupalkki::$tekstimuokkaus_supistaja.
            "</button>".

            "<button type='button'".
                "onclick='lisaaEriMerkkipari(\"".$this->elementin_id."\",\"".
                    Kaavaeditori::$laventaja_alku."\",\"".
                    Kaavaeditori::$laventaja_loppu."\")'".
                "title='".Tekstityokalupalkki::$tekstimuokkaus_laventaja_title."'>".
                Tekstityokalupalkki::$tekstimuokkaus_laventaja.
            "</button>".
            
            // Tässä hiukan kätevämpi tapa:
            Html::luo_button(
                Tekstityokalupalkki::$tekstimuokkaus_neliojuuri_1rivi, 
                array(Maarite::onclick(
                            'lisaaEriMerkkipari',
                            array($this->elementin_id, 
                                Kaavaeditori::$radix_yksirivi_alku, 
                                Kaavaeditori::$radix_yksirivi_loppu)),
                        Maarite::title(Tekstityokalupalkki::
                                        $tekstimuokkaus_neliojuuri_1rivi_title))).
                    
            Html::luo_button(
                Tekstityokalupalkki::$tekstimuokkaus_neliojuuri_2rivi, 
                array(Maarite::onclick(
                            'lisaaEriMerkkipari',
                            array($this->elementin_id, 
                                Kaavaeditori::$radix_kaksirivi_alku, 
                                Kaavaeditori::$radix_kaksirivi_loppu)),
                        Maarite::title(Tekstityokalupalkki::
                                        $tekstimuokkaus_neliojuuri_2rivi_title))).
            
            "</span>".  // Kaavapainikeryhmän loppu.

            // Sitten painikkeet suureelle ja yksikölle:
            "<button type='button'".
                "onclick='lisaaTagit(\"".$this->elementin_id."\",\"span\",\"suure\",\"\",\"\")'".
                "title='".Tekstityokalupalkki::$tekstimuokkaus_suure_title."'>".
                Tekstityokalupalkki::$tekstimuokkaus_suure.
            "</button>".
            "<button type='button'".
                "onclick='lisaaTagit(\"".$this->elementin_id."\",\"span\",\"yksikko\",\"\",\"\")'".
                "title='".Tekstityokalupalkki::$tekstimuokkaus_yksikko."'>".
                Tekstityokalupalkki::$tekstimuokkaus_yksikko.
            "</button>";
        }
        
        $tekstimuokkauspainikkeet .= "</div>";

        return $tekstimuokkauspainikkeet;
    }
}
?>
