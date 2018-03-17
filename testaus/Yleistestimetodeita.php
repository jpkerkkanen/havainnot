<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Yleistestimetodeita
 *
 * @author J-P
 */
class Yleistestimetodeita {
    //put your code here
    
    
    
    /** 
     * Seuraava saattaa vaatia viilausta. Virheiden lkm ei nyt näy mitenkään
     * palautteessa. 
     * 
     * Palauttaa merkkijonon.
     */
    public function testaa_hae_merkkijonot(){
       /* Testataan merkkijonometodia "php_yleinen/php_yleismetodit.php.
        * hae_merkkijonot($teksti, $alkumj, $loppumj, $hae_kaikki)":*/
        $virheiden_lkm = 0;
        
       $sisalto =  "<h2>Testataan merkkijonometodia 'hae_merkkijonot'</h2>";
       $sisalto .=  "Testataan merkkijonometodia 'hae_merkkijonot'<br/>";

       $teksti = "Auto ajoi (200 km/h) kaarteeseen. Olipa (siinä) vauhtia.";
       $alkumj = "(";
       $loppumj = ")";
       $hae_kaikki = true;

       $sisalto .= "Yritetään etsiä merkkijonosta <br/>";
       $sisalto .= "'<b>".$teksti."</b>'<br/>";
       $sisalto .= "kaikki sulkujen väliin jäävät merkkijonot eli '200 km/h' ".
                   "ja 'siinä'<br/>";

       $tulostaulukko = hae_merkkijonot($teksti, $alkumj, $loppumj, $hae_kaikki);

       if((sizeof($tulostaulukko) == 2) &&
           ($tulostaulukko[0] == "200 km/h") &&
           ($tulostaulukko[1] == "siinä")){

           $sisalto .= "OIKEIN! Loytyi merkkijonot '".$tulostaulukko[0]."' ja '".
                       $tulostaulukko[1]."'";

           $sisalto .= "<br/><br/>";
       }
       else if(empty ($tulostaulukko)){
           $sisalto .= "<div class='virhe'>Virhe! Mitään ei löytynyt!</div>";
           $virheiden_lkm++;
       }
       else{
           $sisalto .= "<div class='virhe'>Virhe! Taulukon sisältö:<br/>";
           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }
           $sisalto .= "</div>";

           $virheiden_lkm++;
       }

       /********************************/

       $teksti = "Auto ajoi (200 km/h) kaarteeseen. Olipa (siinä) vauhtia.";
       $alkumj = "(";
       $loppumj = ")";
       $hae_kaikki = false;

       $sisalto .= "Yritetään etsiä merkkijonosta <br/>";
       $sisalto .= "'<b>".$teksti."</b>'<br/>";
       $sisalto .= "1. sulkujen väliin jäävä merkkijono eli '200 km/h'<br/>";

       $tulostaulukko = hae_merkkijonot($teksti, $alkumj, $loppumj, $hae_kaikki);

       if((sizeof($tulostaulukko) == 1) &&
           ($tulostaulukko[0] == "200 km/h")){

           $sisalto .= "OIKEIN! Loytyi merkkijono '".$tulostaulukko[0]."'";

           $sisalto .= "<br/><br/>";
       }
       else if(empty ($tulostaulukko)){
           $sisalto .= "<div class='virhe'>Virhe! Mitään ei löytynyt!</div>";
           $virheiden_lkm++;
       }
       else{
           $sisalto .= "<div class='virhe'>Virhe! Taulukon sisältö:<br/>";
           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }
           $sisalto .= "</div>";

           $virheiden_lkm++;
       }

       /********************************/

       $teksti = "Auto ajoi (200 km/h) kaarteeseen. Olipa (siinä) vauhtia.";
       $alkumj = "Auto";
       $loppumj = "Oli";
       $hae_kaikki = true;

       $sisalto .= "Yritetään etsiä merkkijonosta <br/>";
       $sisalto .= "'<b>".$teksti."</b>'<br/>";
       $sisalto .= "sanojen 'Auto' ja 'Oli' väliin jäävä merkkijono ".
                   "eli ' ajoi (200 km/h) kaarteeseen. '<br/>";

       $tulostaulukko = hae_merkkijonot($teksti, $alkumj, $loppumj, $hae_kaikki);

       if((sizeof($tulostaulukko) == 1) &&
           ($tulostaulukko[0] == " ajoi (200 km/h) kaarteeseen. ")){

           $sisalto .= "OIKEIN! Loytyi merkkijono '".$tulostaulukko[0]."'";

           $sisalto .= "<br/><br/>";
       }
       else if(empty ($tulostaulukko)){
           $sisalto .= "<div class='virhe'>Virhe! Mitään ei löytynyt!</div>";
           $virheiden_lkm++;
       }
       else{
           $sisalto .= "<div class='virhe'>Virhe! Taulukon sisältö:<br/>";
           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }
           $sisalto .= "</div>";

           $virheiden_lkm++;
       }

       /*****************/
       /* Haetaan a-kirjainten välistä. Kolme peräkkäistä aata pitäisi mennä
        * niin, että eka on lopetus, toka aloitus ja kolmas lopetus ja väliin
        * jäävää tyhjää ei palauteta. */

       $teksti = "Auto ajoi (200 km/h) kaaarteeseen. Olipa (siinä) vauhtia.";
       $alkumj = "a";
       $loppumj = "a";
       $hae_kaikki = true;

       $sisalto .= "Yritetään sitten etsiä merkkijonosta (huom 3 a-kirjainta) <br/>";
       $sisalto .= "'<b>".$teksti."</b>'<br/>";
       $sisalto .= "'a'-kirjainten (pienten) väliin jäävät merkkijonot.<br/>";
       $sisalto .= "<br/>";

       $tulostaulukko = hae_merkkijonot($teksti, $alkumj, $loppumj, $hae_kaikki);

       if((sizeof($tulostaulukko) == 2) &&
           ($tulostaulukko[0] == "joi (200 km/h) k") &&
           ($tulostaulukko[1] == " (siinä) v")){

           $sisalto .= "OIKEIN! Loytyi seuraavat merkkijonot:<br/>";

           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }

           $sisalto .= "<br/><br/>";
       }
       else if(empty ($tulostaulukko)){
           $sisalto .= "<div class='virhe'>Virhe! Mitään ei löytynyt!</div>";
           $virheiden_lkm++;
       }
       else{
           $sisalto .= "<div class='virhe'>Virhe! Taulukon sisältö:<br/>";
           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }
           $sisalto .= "</div>";

           $virheiden_lkm++;
       }

       /*****************/

       $teksti = "Auto ajoi (200 km/h) kaarteeseen. Olipa (siinä) vauhti.";
       $alkumj = "a";
       $loppumj = "a";
       $hae_kaikki = true;

       $sisalto .= "Yritetään sitten etsiä merkkijonosta <br/>";
       $sisalto .= "'<b>".$teksti."</b>'<br/>";
       $sisalto .= "'a'-kirjainten (pienten) väliin jäävät merkkijonot:<br/>";
       $sisalto .= "<br/>";

       $tulostaulukko = hae_merkkijonot($teksti, $alkumj, $loppumj, $hae_kaikki);

       if((sizeof($tulostaulukko) == 2) &&
           ($tulostaulukko[0] == "joi (200 km/h) k") &&
           ($tulostaulukko[1] == "rteeseen. Olip")){

           $sisalto .= "OIKEIN! Loytyi seuraavat merkkijonot:<br/>";

           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }

           $sisalto .= "<br/><br/>";
       }
       else if(empty ($tulostaulukko)){
           $sisalto .= "<div class='virhe'>Virhe! Mitään ei löytynyt!</div>";
           $virheiden_lkm++;
       }
       else{
           $sisalto .= "<div class='virhe'>Virhe! Taulukon sisältö:<br/>";
           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }
           $sisalto .= "</div>";

           $virheiden_lkm++;
       }


       /*****************/

       $sisalto .= "Yritetään sitten etsiä merkkijonosta <br/>";
       $sisalto .= "'<b>".$teksti."</b>'<br/>";
       $sisalto .= "vain 1. a-kirjainten väliin jäävä merkkijonot.<br/>";

       $sisalto .= "<br/>";

       $hae_kaikki = false;
       $tulostaulukko = hae_merkkijonot($teksti, $alkumj, $loppumj, $hae_kaikki);

       if((sizeof($tulostaulukko) == 1) &&
           ($tulostaulukko[0] == "joi (200 km/h) k")){

           $sisalto .= "OIKEIN! Loytyi seuraavat merkkijonot:<br/>";

           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }

           $sisalto .= "<br/><br/>";
       }
       else if(empty ($tulostaulukko)){
           $sisalto .= "<div class='virhe'>Virhe! Mitään ei löytynyt!</div>";
           $virheiden_lkm++;
       }
       else{
           $sisalto .= "<div class='virhe'>Virhe! Taulukon sisältö:<br/>";
           for($i = 0; $i < sizeof($tulostaulukko); $i++){
               $sisalto .=($i+1).". merkkijono: ".$tulostaulukko[$i]."<br/>";
           }
           $sisalto .= "</div>";

           $virheiden_lkm++;
       }
       $sisalto .=  "<h2>Merkkijonometodin 'hae_merkkijonot' testaus loppu!</h2>";
       
       return $sisalto;
    }
    
    public function testaa_matem_kaavaeditori(){
                /***************************************************************************/
        $sisalto .=  "<h2>Testataan luokan Kaavaeditori metodia
                       'muotoile_kaavat(teksti)'</h2>";
        $sisalto .=  "Syötetään metodiin merkkijono '{@4⋅[@4##6@]=5@}'
                   ja katsotaan, näyttääkö tulos hyvältä. Alla tulos:<br/><br/>";

        $teksti = '{@4⋅[@4##6@]=5@}';
        $tulos = Kaavaeditori::muotoile_kaavat($teksti);
        $sisalto .= $tulos;


        $sisalto .= "<a href='../php_yleinen/matematiikka/murtolukutesti.php'>".
               "Murtolukutesti</a><br/>";
        $sisalto .= "<br/>";
        "<h2>Loppu luokan Kaavaeditori metodin 'muotoile_kaavat(teksti)' testi LOPPU</h2>";
        $sisalto .= "******************************************************************<br/>";

        $sisalto .= "<b>toteuta_uloskirjautuminen:</b><br />";
        $palauteolio = toteuta_uloskirjautuminen($parametriolio);
        if($palauteolio->get_virhekoodi() != Palaute::$VIRHEKOODI_KAIKKI_OK){
           $virheiden_lkm++;
           $sisalto .= "<div class='virhe'>VIRHEKOODI=".$palauteolio->get_virhekoodi().
                       "! ".$palauteolio->get_ilmoitus()."</div>";
        }
        else{
           $sisalto .= "<div>Virheita ei havaittu!</div>";
        }
        $sisalto .= $palauteolio->get_sisalto();
    }
}

?>
