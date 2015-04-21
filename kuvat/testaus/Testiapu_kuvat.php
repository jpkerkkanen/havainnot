<?php
/**
 * Description of Testiapu
 * Toimii bongaustestien alustana ja perii yleisen Testialusta-luokan
 * "php_yleinen"-kansiosta.
 * @author J-P
 */
class Testiapu_kuvat extends Testialusta{


    public $kuvat;  // taulukko
    public $kuvalinkit;  // taulukko

    //put your code here
    public function  __construct($tietokantaolio, $parametriolio, $luokkanimi) {
        parent::__construct($tietokantaolio, $parametriolio, $luokkanimi);
        $this->kuvat = array();
        $this->kuvalinkit = array();
    }

    /**
     * Lisää uuden kuvan kokoelmaan.
     * @param <type> $uusi
     */
    public function lisaa_kuva($uusi){
        array_push($this->kuvat, $uusi);
    }
    /**
     * Lisää uuden kuvalinkin kokoelmaan.
     * @param <type> $uusi
     */
    public function lisaa_kuvalinkki($uusi){
        array_push($this->kuvalinkit, $uusi);
    }

    
    /**
     * Luo uuden kuvan annetuilla arvoilla, tallentaa sen tietokantaan ja
     * kuvatiedoston omaan kansioonsa
     * ja palauttaa tallennetun id:n tai arvon
     * Kuva::$MUUTTUJAA_EI_MAARITELTY, jos jokin menee vikaan.
     *
     */
    public function luo_ja_tallenna_testikuva(){
        $tallennetun_id = Kuva::$MUUTTUJAA_EI_MAARITELTY;
        
        
        
        
        return $tallennetun_id;
    }
    
    
    
    public function tee_alkusiivous(){
        //================== Alkusiivous =======================================
        // Poistetaan mahdolliset aiempien testien roskat, joita on voinut
        // jäädä, jos testi on keskeytynyt:
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                                            Kuva::$taulunimi,
                                            Kuva::$SARAKENIMI_VUOSI,
                                            Kuvatestaus::$testivuosi);
        
        $poistettujen_lkm = $lkm;
        if($poistettujen_lkm > 0){
            $this->lisaa_ilmoitus(
                    $poistettujen_lkm." vanhaa kuvaa poistettu",false);
        }
        
        //======================= Alkusiivous päättyi====================
    }
    
    public function siivoa_jaljet(){
        //================== loppusiivous =======================================
        // Poistetaan mahdolliset testikuvat ja linkit (cascade pitäisi toimia):
        $lkm = $this->tietokantaolio->poista_kaikki_rivit(
                                            Kuva::$taulunimi,
                                            Kuva::$SARAKENIMI_VUOSI,
                                            Kuvatestaus::$testivuosi);
        
        $poistettujen_lkm = $lkm;
        if($poistettujen_lkm > 0){
            $this->lisaa_ilmoitus(
                    $poistettujen_lkm." vanhaa kuvaa poistettu",false);
        }
        //======================= Alkusiivous päättyi====================
    }
    
    /**
     * Tämä on netistä: http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php (18.1.2014)
     * @param type $kansio_osoite
     */
    public function poista_lataustiedostot($kansio_osoite){
        $files = glob($kansio_osoite."*"); // get all file names
        
        $laskuri = 0;
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
            $laskuri++;
        }
        
        $this->lisaa_kommentti($laskuri." tiedostoa poistettu!");
    }
}
?>
