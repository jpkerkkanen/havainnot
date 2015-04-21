<?php
//require_once '../../php_yleinen/testaus_yleinen/Testialusta.php';
/**
 * Description of Testiapu
 * Toimii Pikakommentointitestien alustana ja perii yleisen Testialusta-luokan
 * "php_yleinen"-kansiosta.
 * @author J-P
 */
class Testiapu extends Testialusta{

    public $pikakommentit;  // taulukko

    

    //put your code here
    public function  __construct($tietokantaolio, $parametriolio, $luokkanimi) {
        parent::__construct($tietokantaolio, $parametriolio, $luokkanimi);
        $this->pikakommentit = array();
    }

    /**
     * Lisää uuden pikakommentin pikakommentit-kokoelmaan.
     * @param <type> $uusi
     */
    public function lisaa_pikakommentti($uusi){
        array_push($this->pikakommentit, $uusi);
    }

    /**
     * Luo uuden pikakommentin annetuilla arvoilla, tallentaa sen tietokantaan
     * ja palauttaa tallennetun id:n tai arvon
     * Pikakommentti::$MUUTTUJAA_EI_MAARITELTY, jos jokin menee vikaan.
     *
     * @param <type> $henkilo_id
     * @param <type> $kohde_id
     * @param <type> $kohde_tyyppi
     * @param <type> $kommentti
     */
    public function luo_ja_tallenna_pikakommentti($henkilo_id,
                                                    $kohde_id,
                                                    $kohde_tyyppi,
                                                    $kommentti){
        $tallennetun_id = Pikakommentti::$MUUTTUJAA_EI_MAARITELTY;
        $id = Pikakommentti::$MUUTTUJAA_EI_MAARITELTY;
        $pika = new Pikakommentti($id, $this->tietokantaolio);

        $this->lisaa_kommentti("Uusi tyhja pikakommentti luotu!");

        $this->lisaa_kommentti("Asetetaan pikakommentin henkilo_id,
                        kohde_id, kohde_tyyppi ja kommentti, sekä tallennus-
                        ja muokkausajat. ");

        $pika->set_henkilo_id($henkilo_id);
        $pika->set_kohde_id($kohde_id);
        $pika->set_kohde_tyyppi($kohde_tyyppi);
        $pika->set_kommentti($kommentti);
        $pika->set_arvo_kevyt(time(), Pikakommentti::$SARAKENIMI_TALLENNUSHETKI);
        $pika->set_arvo_kevyt(0, Pikakommentti::$SARAKENIMI_MUOKKAUSHETKI);

        if($pika->tallenna_uusi()==Pikakommentti::$OPERAATIO_ONNISTUI){
            $this->lisaa_kommentti("Pikakommentin tallennus ok!");
            $tallennetun_id = $pika->get_id();
        }  
            
        else{
            $this->lisaa_virheilmoitus("Virhe tallennuksessa (luo_
                ja_tallenna_pikakommentti())!".
                " Arvot: henkilo_id=".$pika->get_henkilo_id().
                ", Kohde_id=".$pika->get_kohde_id().
                ", Kohde_tyyppi=".$pika->get_kohde_tyyppi().
                ", Kommentti=".$pika->get_kommentti());
        }
        
        return $tallennetun_id;
    }

     
}
?>
