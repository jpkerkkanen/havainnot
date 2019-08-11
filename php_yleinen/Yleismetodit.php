<?php

/**
 * Sisältää staaattisia yleisluontoisia metodeita.
 */
class Yleismetodit{
    
    
    /**
     * Tutkii, onko parametrin mukaisessa tietokantataulussa kyseistä arvoa
     * kyseisessä sarakkeessa. Jos on vähintää yksi, palauttaa arvon true, 
     * muuten false.
     * 
     * Tätä käytetään muun muassa unique-arvojen tarkistamiseen ennen
     * tietokantaan tallentamista (esim. kayttajatunnus).
     * 
     * @param type $taulunimi
     * @param type $sarakenimi
     * @param type $arvo    // ARvo, jonka olemassaoloa tutkitaan.
     * @param Tietokantaolio $tietokantaolio
     */
    static function arvo_jo_kaytossa($taulunimi, $sarakenimi, $arvo, $tietokantaolio){
        $palaute = true;
        $tulos = 
            $tietokantaolio->hae_eka_osuma_oliona($taulunimi,$sarakenimi, $arvo);
        
        if($tulos == Tietokantaolio::$HAKU_PALAUTTI_TYHJAN){
            $palaute = false;
        }
        return $palaute;
    }
    
    
    /**
     * Hakee suurimmän parametrina annettavan tietokantataulun id-kentän arvoista.
     * Ellei mitään löydy, palauttaa arvon -1.
     * 
     * Tehty havaintoja varten, mutta soveltuu muuhunkin.
     * @param Tietokantaolio $tietokantaolio
     * @param <type> $taulunimi
     */
   static function hae_suurin_id($tietokantaolio, $taulunimi){
       // Havainnot: Haetaan suurin olemassaolevista havainto-id:eistä, jotta
       // mahdollisen kopioitavan/uuden havainnon id voidaan "arvata"
       // (=yhtä isompi). Tämä ei välttämättä pidä paikkaansa esimerkiksi
       // tapauksessa, jossa joku toinen ehtii tallentamaan välissä.
       $hakulause = "SELECT MAX(id) AS suurin FROM $taulunimi";
       $osumataulukko = $tietokantaolio->
                       tee_OMAhaku_oliotaulukkopalautteella($hakulause);

       $suurin_id = -1;

       if(!empty ($osumataulukko) && is_numeric($osumataulukko[0]->suurin)){
           $suurin_id = $osumataulukko[0]->suurin;
       }
       return $suurin_id;
   }
   
   /**
    * Returns an array having as its first element the element given as 
    * argument ($elem). Additionally the new array contains all the elements
    * of the array $array given as parameter.
    * 
    * Replaces the parameter array named as &$array by the new one.
    * 
    * @param type $elem
    * @param array $array
    * @return array An array in which the $elem has been added as the first elem.
    */
   public static function array_add_first_elem($elem, &$array){
       $new = array();
       array_push($new, $elem);
       
       // Adds all the old element to the end of the new array:
       foreach ($array as $old_elem) {
           array_push($new, $old_elem);
       }
       $array = $new;
       return $new;
   }
    
    /**
    * Hakee annetusta tekstistä sellaiset merkkijonot, jotka ovat
    * merkkijonojen $alkumj ja $loppumj välissä. Esimerkiksi jos $alkumj="alku",
    * $loppumj="loppu" ja $teksti="alkukylläpä oli juttu!loppu", palautetaan
    * taulukko, jossa on yksi alkio "kylläpä oli juttu".
    *
    * Parametri $hae_kaikki ratkaisee sen, lopetetaanko etsintä ensimmäiseen
    * osumaan, vai otetaanko mukaan kaikki sopivat merkkijonot (jos $alkumj ja
    * $loppumj esiintyvät tekstissä useamman kerran.
    *
    * Jos tekstistä löytyy $alkumj mutta ei $loppumj, ei kyseistä merkkijonoa
    * oteta mukaan tuloksiin. Myöskään tyhjää merkkijonoa ei oteta mukaan.
    *
    * $loppumj ei voi toimia seuraavan merkkijonon alkumerkkinä, vaikka merkit
    * olisivat samat. Uuden alkumerkin etsintä aloitetaan aina vasta loppumerkkiä
    * seuraavasta merkistä.
    *
    * Ellei mitään löydy, tai tapahtuu muuta kummaa, palautetaan tyhjä taulukko.
    *
    * Etsinnässä isoilla ja pienillä kirjaimilla ON väliä (case-sensitive).
    *
    * @param <type> $teksti
    * @param <type> $alkumj
    * @param <type> $loppumj
    * @param <type> $hae_kaikki
    * @return Array löytyneet merkkijonot tai tyhjä taulukko, ellei mitään löytyny.
    */
   static function hae_merkkijonot($teksti, $alkumj, $loppumj, $hae_kaikki){

       $palautus = Array();

       // Tarkistetaan ensin, jotta parametrit kunnossa:
       if(isset($teksti) && is_string($teksti) && ($teksti != "") &&
           isset($alkumj) && is_string($alkumj) &&
           isset($loppumj) && is_string($loppumj) &&
           isset($hae_kaikki) && is_bool($hae_kaikki)){

           // TArkistetaan, että alku- ja loppumerkki esiintyvät kumpikin
           // ainakin kerran tekstissä. Muuten ei kannata etsiä mitään. Tämä ei
           // kuitenkaan takaa esiintymän löytymistä, koska merkit voivat olla
           // väärin päin eli loppumerkki ennen alkumerkkiä.
           if((substr_count ($teksti, $alkumj) > 0) &&
               (substr_count ($teksti, $loppumj) > 0)){

               // Käydään merkkijonoa läpi, kunnes löytyy $alkumj:
               $alkumerkin_pit = strlen($alkumj);
               $loppumerkin_pit = strlen($loppumj);
               $alkupiste = 0; // Tästä lähdetään aina etsimään seuraavaa merkkiä.
               $alkuind = 0; // Palautettavan mj:n 1. merkin indeksi
               $loppuind = 0; // Palautettavan mj:n viimeisen merkin indeksi

               // Kun alkupiste on niin tekstin alussa, jotta sen jälkeen mahtuu
               // vielä sekä alku- että loppumerkki:
               while($alkupiste < (strlen($teksti)-$alkumerkin_pit-$loppumerkin_pit)){

                   // Haetaan ensimmäisen alkumerkin alkukohdan indeksi.
                   $alkuind = strpos ($teksti, $alkumj, $alkupiste);

                   // Tässä pitää käyttää '!==', koska 0 pitää hyväksyä!
                   if($alkuind !== false){

                       // lisätään alkumerkin pituus, jolloin saadaan oikea alkuind:
                       $alkuind += $alkumerkin_pit;

                       // Siirretään alkupiste alkuindeksin kohdalle:
                       $alkupiste = $alkuind;

                       // Haetaan ensimmäisen (seuraavan) loppumerkin alkukohdan
                       // indeksi jolloin saadaan loppuind:
                       $loppuind =
                           strpos ($teksti, $loppumj, $alkupiste);

                       if($loppuind !== false){

                           // Siirretään alkupiste loppuindeksin yli:
                           $alkupiste = $loppuind+1;

                           $pituus = $loppuind-$alkuind;
                           $merkkijono = substr($teksti, $alkuind, $pituus);

                           if($merkkijono != false){

                               // Tyhjää merkkijonoa ei huolita:
                               if(strlen($merkkijono)>0){
                                   array_push($palautus, $merkkijono);

                                   // Jos $hae_kaikki on false, lopetetaan etsintä:
                                   if(!$hae_kaikki){
                                       $alkupiste = strlen($teksti);
                                   }
                               }
                           }           
                       }
                       else{   // Ellei loppumerkkiä löytynyt:
                           $alkupiste = strlen($teksti);   // Lopetetaan etsintä
                       }
                   }
                   else{   // Ellei alkumerkkiä löytynyt:
                       $alkupiste = strlen($teksti);   // Lopetetaan etsintä
                   }      
               }
           }
       }
       return $palautus;
   }


   /**
    * Kirjoittaa JS:n avulla elementtiin parametrina tuodun tekstin.
    * Elementin id tuodaan parametrina myös.
    */
   static function kirjoita_elementtiin($id, $teksti)
   {
       $kirjoitus = <<<HUU
           <script type='text/javascript'>
           document.getElementById('$id').innerHTML = '$teksti';
           </script>
HUU;
       echo $kirjoitus;
   }


   /****************************** FUNCTION LEIKKAA_MERKKIJONO ********************
    * Palauttaa saamansa merkkijonon $pituus ensimmäistä merkkiä lisättynä
    * kolmella pisteellä. Jos saatu merkkijono on lyhempi kuin $pituus, palautetaan
    * saatu merkkijono ilman muutoksia.
    *
    * HUOM! mb-alkuisten metodien pitäisi olla mukana PHP5:ssä, mutta oma
    * palvelin valittaa, ettei löydy! Webhotellissa toimii toki. Ilman mb:tä
    * ääkkösten kanssa tulee ongelmia, kun merkkijono katkeaa niin, ettei
    * merkkiä tunnisteta.
    *
    * @param <type> $mjono
    * @param <type> $pituus
    */
   static function leikkaa_merkkijono($mjono, $pituus){
       $palaute = "";
       if(is_string($mjono)){
           if(strlen($mjono) <= $pituus){
               $palaute = $mjono;
           }
           else{
               $alku = 0;
              
               // Alempi rivi toimii muuten, mutta ei ymmärrä esim. ääkkösiä.
               $palaute = mb_strcut($mjono, $alku, $pituus, "UTF-8")."...";
               //$palaute = substr($mjono, $alku, $pituus)."...";
           }
       }
       return $palaute;
   }
   
   
   
   /**
    * Tämä on muokattu kopio sivulta
    * http://theserverpages.com/php/manual/en/function.ucfirst.php (9.9.2010)
    * HUOM! ääkköset ei yleensä muutu, koska eivät ole tässä muodossa.
    * @param <type> $str
    * @return <type>
    */
   static function eka_kirjain_isoksi($str){
       if(!empty($str)) {
          $str[0] = strtr($str,
           "abcdefghijklmnopqrstuvwxyzäöå"
           ,
           "ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÅ");
       }

       return $str;
   }

   /**
    * Tämä on muokattu kopio sivulta
    * http://theserverpages.com/php/manual/en/function.ucfirst.php (9.9.2010)
    * HUOM! ääkköset ei yleensä muutu, koska eivät ole tässä muodossa.
    * @param <type> $str
    * @return <type>
    */
   static function eka_kirjain_pieneksi($str){
       if(!empty($str)) {
           $str[0] = strtr($str,
               "ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÅ",
               "abcdefghijklmnopqrstuvwxyzäöå"
           );
       }

       return $str;
   }
}


?>
