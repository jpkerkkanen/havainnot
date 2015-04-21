<?php
/**
 * Description of Pikakommenttinakymat:
 * Huolehtii kommenttien ulkoasusta tai tarkemmin sanoen sisältää
 * enemmän tai vähemmän "tyhmät" html-rungot, joihin kommentin tiedot syötetään.
 *
 * @author J-P
 */
class Pikakommenttinakymat {
    //put your code here

    public static $syottokentta_id = "syottokentta_pikakommentit";

    /**
     * Näyttää joukon pikakommentteja.
     * @param <type> $sisalto
     * @param <type> $painikkeet
     * @return <type>
     */
    public static function nayta_pikakommentit($sisalto, $painikkeet){
        $html = "<div class=''>".
                    "<div class='keskitetty'>".$painikkeet."</div>".
                    $sisalto;
        $html .= "</div>"; // Pikakommenttilaatikon loppu
        return $html;
    }

    /**
     * @param <type> $aika
     * @param <type> $lahettaja
     * @param <type> $sisalto
     * @param <type> $painikkeet
     * @return string Palauttaa yhden pikakommentin Html-koodin.
     */
    public static function nayta_pikakommentti($aika,
                                                $lahettaja,
                                                $sisalto,
                                                $painikkeet,
                                                $id){
        $html = "<div class='pikakommentti' id='pk".$id."'>";

        // Otsikko:
        $html .= "<div class='pikakommentti_otsikko'>";
        $html .= "<span class='pikakommentti_lahettaja'>".$lahettaja." </span>";
        $html .= "<span class='pikakommentti_aika'>".$aika."</span>";
        $html .= "</div>"; // Otsikon loppu

        // Sisältö:
        $html .= "<div class='pikakommentti_sisalto'>".
                "<table><tr>".
                    "<td>".$sisalto."</td>".
                    "<td>".$painikkeet."</td>".
                "</tr></table></div>";

        $html .= "</div>"; // Pikakommentin loppu
        return $html;
    }

    /**
     * @param <type> $aika
     * @param <type> $lahettaja
     * @param <type> $sisalto
     * @param <type> $painikkeet
     * @return string Palauttaa yhden pikakommentin Html-koodin.
     */
    public static function nayta_poistovahvistus($aika,
                                                $lahettaja,
                                                $sisalto,
                                                $painikkeet,
                                                $id){
        $html = "<div class='pikakommentti_poistovahvistus' id='pk".$id."'>";

        // Otsikko:
        $html .= "<div class='pikakommentti_otsikko'>";
        $html .= "<span class='pikakommentti_lahettaja'>".$lahettaja." </span>";
        $html .= "<span class='pikakommentti_aika'>".$aika."</span>";
        $html .= "</div>"; // Otsikon loppu

        // Sisältö:
        $html .= "<div class='pikakommentti_sisalto'>".
                "<table><tr>".
                    "<td>".$sisalto."</td>".
                    "<td>".$painikkeet."</td>".
                "</tr></table></div>";

        $html .= "</div>"; // Pikakommentin loppu
        return $html;
    }


    /**
     * Näyttää syöttökentän ja tallennuspainikkeen (joka annetaan
     * parametrina).
     * @param <type> $tallenna_painike
     * @return <type>
     */
    public static function nayta_pikakommenttilomake($tallenna_painike){
        $html = "<hr/>";
        $html .= "<div class='keskitys' id='pikakommenttiohje'>".
            Pikakommenttitekstit::$lomaketeksti_kirjoita_pikakommentti.
            "</div>";
        $html .= "<textarea cols='20' rows='5' id=".
                Pikakommenttinakymat::$syottokentta_id.">".
                "</textarea>";

        $html .= "<div class='keskitys'>".$tallenna_painike."</div>";
        return $html;
    }
}
?>
