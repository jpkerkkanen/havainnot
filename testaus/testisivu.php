<?php
/*function testaa(){
    $muuttuja = "alkup";
    $vanha = $muuttuja;
    $muuttuja = "UUSSI";    // Muuttaako myös vanhan arvon? EI MUUTA!!!
    return $vanha;
}*/

function nayta_testisivu($oikea_palkki,
                        $vasen_palkki,
                        $sisalto,
                        $ilmoitus){


// Tulostetaan etusivun html-koodi:
$otsikko = "Testataan!";


$etusivu =
    '<!DOCTYPE html>'.
    '<html>
    <head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type">

    <title>Testisivu</title>
    <link rel="stylesheet" type="text/css" href="../tyylit/perusmuotoilut.css" />
    <link rel="stylesheet" type="text/css" href="../php_yleinen/matematiikka/matematiikkatyylit.css" />  
    <script type="text/javascript" src="../ajax_ja_js/metodit.js"></script>
    <script type="text/javascript" src="../ajax_ja_js/ajaxkirjasto.js"></script>


    <style type="text/css">
    
    </style>
    </head>

    <!--******************* POHJA ***********************************************-->

    <body>
    <div id="pohja_keskitetty">
    <div id="ilmoitus">'.$ilmoitus.'</div>
    <!--******************* OTSIKKOPALKKI ***************************************-->

    <div id="otsikkopalkki">
    <h1>'.$otsikko.'</h1>
    <!--<?php $aikailmoitus." ";?><span id="kellonaika"></span>*/-->
    </div>

    <!--******************* LINKKIPALKKI ****************************************-->

    <div id="palkki_vasen">
    '.$vasen_palkki.'
    </div>

    <!--******************* INFO ELI OIKEANPUOLEINEN PALKKI *********************-->
    <div id="palkki_oikea">
     '.$oikea_palkki.'<br/>
    </div><!--******************* OIKEA PALKKI LOPPUU ***************************-->


    <!--******************* SISäLTö *********************************************-->
    <div id = "sisalto">
    '.$sisalto.'
    </div> <!--******************* SISäLTö LOPPUU **************************-->

    <!--******************* TIEDOT ELI ALAPALKKI ********************************-->

    <div id="palkki_pohja">
    <div id="palkki_pohja_teksti">

    </div>
    </div> <!--******************* TIEDOT ELI ALAPALKKI LOPPUU ******************-->



    </div> <!--******************* POHJA LOPPUU **************************-->
    </body>
    </html>';

    return $etusivu;
}

?>
