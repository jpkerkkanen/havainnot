<?php

/*
 * Täällä testaan välillä vastaantulevia epävarmuusjuttuja, jotka voivat liittyä
 * mihin vaan.
 */

echo "Testaan, palauttaako <b>is_array</b>(taulukko) falsen my&ouml;s, 
    jos taulukko ei-m&auml;&auml;ritelty.<br/><br/>";

$tulos1 = is_array(array());
$tulos2 = is_array(null);

if($tulos1 && !$tulos2){
    echo "Tulos: Kylla n&auml;in on! Ep&auml;m&auml;&auml;ritelty arvo aiheuttaa palautteen FALSE,
        eli sit&auml; EI tartte erikseen <b>isset</b>(taulukko)-testata!<br/>";
    
}
else{
    echo "Ilmeisesti ei! Palautusarvo null-tapauksessa: ".$tulos2;
}

$testi = "o\l<>en 'puu'";
$testi2 = 'o\l<>en "puu"';
echo "<br/><br/>mysql_real_escape_string-metodin testi&auml;: syotteena= ".$testi;
echo "<br/>Tulos=".mysql_real_escape_string($testi);

echo "<br/><br/>mysql_real_escape_string-metodin testi2: syotteena =".
        $testi2;
echo "<br/>Tulos=".mysql_real_escape_string($testi2);

echo "<br/><br/>mysql_real_escape_string-metodi tuplasti: ".
        "mysql_real_escape_string(mysql_real_escape_string(syote)) <br/>".
        "syote= ".$testi;
echo "<br/>Tulos=".mysql_real_escape_string(mysql_real_escape_string($testi));

// Huomautus: Testin perusteella epämääritelty arvo aiheuttaa palautteen FALSE,
// eli sita ei tartte erikseen isset-testata!
?>
