<?php

class Html_tulostus{
    
    // Seuraavien avulla valitaan palkkien lkm.
    public static $nayttomoodi_yksipalkki = 0;
    
    // Vasemmalla kapea, oikealla leveä palkki.
    public static $nayttomoodi_kaksipalkki_vasen_levea = 1; 
    
    // Vasemmalla leveä, oikealla kapea palkki.
    public static $nayttomoodi_kaksipalkki_oikea_levea = 2;
    
    // Kapea leveä kapea.
    public static $nayttomoodi_kolmipalkki = 3;
    
    public static $html_id_ylapalkki = "otsikkopalkki";
    public static $html_id_alapalkki = "palkki_pohja";
    public static $html_id_vasen_palkki = "palkki_vasen";
    public static $html_id_oikea_palkki = "palkki_oikea";
    public static $html_id_sisaltopalkki = "sisalto";
    public static $html_id_ilmoitusdivi = "ilmoitus";
    
    /**
     * @param type $painikkeet
     * @param type $sisalto
     * @param type $ilmoitus
     * @param type $ylapalkki
     * @param type $linkit
     * @param type $oikea_palkki
     * @param type $alapalkki
     * @param type $nayttomoodi Kuinka monta palkkia näytetään yms.
     * @return string
     */
    public static function nayta_bongaussivu($painikkeet,
                            $sisalto,
                            $ilmoitus,
                            $ylapalkki,
                            $vasen_palkki,
                            $oikea_palkki, 
                            $alapalkki,
                            $nayttomoodi
            ){


    // Tulostetaan etusivun html-koodi:
    $otsikko = Bongaustekstit::$otsikko1_bongaussivu1;

    // Ikkunan kokoa muutettaessa kuvien koot lasketaan uudelleen: KORJAA JS
    //$koon_muutosreaktio ="onResize=\"onResize_toteutus_bongaus1()\"";
    $koon_muutosreaktio ="onResize=\"onResize_toteutus_bongaus1()\"";
    //$koon_muutosreaktio = "";

    $etusivu =
        '<!DOCTYPE html>'.
        '<html>
        <head>
        <meta content="text/html; charset=UTF-8" http-equiv="content-type">

        <title>'.Bongaustekstit::$html_title_bongaussivu1.'</title>
        <link rel="stylesheet" type="text/css" href="tyylit/perusmuotoilut.css" />
        <script type="text/javascript" src="ajax_ja_js/metodit.js"></script>
        <script type="text/javascript" src="ajax_ja_js/bongausmetodit.js"></script>
        <script type="text/javascript" src="ajax_ja_js/pikakommentointimetodit.js"></script>
        <script type="text/javascript" src="ajax_ja_js/kuvametodit.js"></script>
        <script type="text/javascript" src="ajax_ja_js/diaesitys.js"></script>
        <script type="text/javascript" src="ajax_ja_js/kayttajahallinta.js"></script>
        <script type="text/javascript" src="ajax_ja_js/ajaxkirjasto.js"></script>

        <style type="text/css">
        </style>
        </head>

        <!--******************* POHJA ***********************************************-->

        <body onload="kaynnista_bongausmetodit()" '.$koon_muutosreaktio.'>

        <div id="pohja">
        <div id='.Html_tulostus::$html_id_ilmoitusdivi.'>'.$ilmoitus.'</div>
        <!--******************* OTSIKKOPALKKI ***************************************-->

        <div id='.Html_tulostus::$html_id_ylapalkki.'>
        <h1>'.$otsikko.'</h1>
        '.$ylapalkki.' <span id="kellonaika"></span>
        </div>';    // otsikkopalkki

    // Otetaan vasen palkki mukaan, jos niin on sanottu:
    if(($nayttomoodi === Html_tulostus::$nayttomoodi_kaksipalkki_vasen_levea)||
        ($nayttomoodi === Html_tulostus::$nayttomoodi_kolmipalkki)){
        
        $etusivu .= 
            '<!--******************* LINKKIPALKKI ********************************-->
            <div id='.Html_tulostus::$html_id_vasen_palkki.'>
            '.$vasen_palkki.'
            </div>';
    }
        
    // Otetaan oikea palkki mukaan, jos niin on sanottu:
    if(($nayttomoodi === Html_tulostus::$nayttomoodi_kaksipalkki_oikea_levea)||
        ($nayttomoodi === Html_tulostus::$nayttomoodi_kolmipalkki)){
        
        $etusivu .= 
            '<div id='.Html_tulostus::$html_id_oikea_palkki.'>
            '.$oikea_palkki.'<br/>
            </div>';
    }
       
    // Sisältö ja alapalkki toistaiseksi aina mukana:
    $etusivu .= 
    
        '<!--******************* SISäLTö *********************************************-->
        <div id = '.Html_tulostus::$html_id_sisaltopalkki.'>
        '.$sisalto.'
        </div> <!--******************* SISäLTö LOPPUU **************************-->

        <!--******************* ALAPALKKI ********************************-->

        <div id='.Html_tulostus::$html_id_alapalkki.'>
        '.$alapalkki.'
        </div> <!--******************* ALAPALKKI LOPPUU ******************-->



        </div> <!--******************* POHJA LOPPUU **************************-->
        <div id='.Bongausasetuksia::$havaintotietotaulun_id.'></div>
        <div id='.Bongausasetuksia::$havaintotietotaulu_leftin_id.'></div>
        <div id='.Bongausasetuksia::$painikepalkin_id.'>'.$painikkeet.'</div>
        </body>
        </html>';

        return $etusivu;
    }
}
?>
