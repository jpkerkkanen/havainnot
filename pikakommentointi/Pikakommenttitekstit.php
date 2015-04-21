<?php
/**
 * Description of Pikakommenttitekstit
 * Tämä luokka sisältää sellaiset tekstit, jotka näkyvät käyttäjälle ja jotka
 * käännetään tarvittaessa eri kielille. Tämä luokka lähinnä on kokoelma
 * erilaisia tekstejä. Metodeja ei juuri ole.
 * @author J-P
 */
class Pikakommenttitekstit {
    
    // Lomaketekstit (ei välttämättä näy käyttäjälle, mutta liittyy tietokantaan):
    public static $lomake_kommentti = "Kommentti";
    public static $lomake_kohde_id = "Kohde_id";
    public static $lomake_kohde_tyyppi = "Kohde_tyyppi";
    public static $lomake_tekija_id = "Tekij&auml;n id";
            
    
    // Virheilmoitukset:
    public static $virheilmoitus_tiedoissa_virheita = "Tiedoissa virheit&auml;!";

    public static $virheilmoitus_ei_tallennuskelpoinen =
        "Pikakommentti ei ole tallennuskelpoinen!";
    public static $virheilmoitus_muutoksia_ei_havaittu =
        "Tiedoissa ei havaittu muutoksia!";
    public static $virheilmoitus_tietokanta_palautti_tyhjan =
        "Tietokannasta ei l&ouml;ytynyt haettua tietoa!";

    public static $virheilmoitus_pikakommentin_lisays_eiok =
        "Uuden pikakommentin lis&auml;ys ei onnistunut!";

    public static $virheilmoitus_pikakommentti_nyk_pikakommentti_ei_maaritelty = 
        "Nykyinen pikakommentti ei ole Pikakommentti-luokan olio!";
    
    public static $virheilmoitus_pikakommentin_poisto_eiok =
        "Pikakommentin poisto ei onnistunut! Kokeile uudelleen!";
    public static $virheilmoitus_pikakommenttia_ei_loytynyt_poistettavaksi =
        "Poistettavaa pikakommenttia ei loytynyt tietokannasta!";

    public static $virheilmoitus_pikakommentin_muokkaustallennus_eiok =
        "Muutoksia ei tehty tai niiden tallennus ep&auml;onnistui!";

    public static $virheilmoitus_pikakommentin_tallennus_eiok =
        "Pikakommentin lis&auml;ys ep&auml;onnistui!
        (Yhteys- tai ohjelmavirhe. Kokeile uudestaan!)";

    public static $virheilmoitus_viallinen_henkilo_id =
        "Henkil&ouml;_id:ss&auml; jotakin vikaa!";
    public static $virheilmoitus_viallinen_tallennushetki =
        "Tallennushetki-arvossa on jotakin vikaa!";
    public static $virheilmoitus_viallinen_muokkaushetki =
        "Muokkaushetki-arvossa on jotakin vikaa!";
    public static $virheilmoitus_viallinen_kohde_id =
        "Kohteen id-arvossa on jotakin vikaa!";
    public static $virheilmoitus_viallinen_kohde_tyyppi =
        "Kohteen tyypiss&auml; on jotakin vikaa!";
    public static $virheilmoitus_viallinen_kommentti =
        "Kommentissa on jotakin vikaa!";

    public static $virheilmoitus_lomaketietoja_puuttuu =
        "Tietoja puuttuu! T&auml;ydenn&auml; puuttuvat kohdat!";

    // Ilmoitukset:
    public static $ilmoitus_pikakommentin_muokkaus_peruttu =
        "Pikakommentin muokkaus peruttu!";

    public static $ilmoitus_pikakommentin_muokkaustallennus_ok =
        "Pikakommentin muutokset tallennettu onnistuneesti!";

    public static $ilmoitus_pikakommentin_poiston_vahvistuskysymys =
        "Oletko varma, ett&auml; haluat poistaa pikakommentin lopullisesti?";

    public static $ilmoitus_pikakommentin_poisto_ok =
        "Pikakommentin poisto onnistui!";
    
    public static $ilmoitus_pikakommenttia_poistettu =
        "pikakommenttia poistettu";
    
    public static $ilmoitus_pikakommentin_poisto_peruttu =
        "Pikakommentin poistaminen peruttu!";

    public static $ilmoitus_pikakommentteja_ei_loytynyt =
        "Ei aiempia pikakommentteja";

    public static $ilmoitus_uuden_pikakommentin_tallennus_ok =
        "Uuden pikakommentin tallennus onnistui!";

    public static $lomaketeksti_kirjoita_pikakommentti =
        "Kirjoita kommenttisi alle!";


   

    // Painikkeet:
    public static $tallenna_uusi_pikakommentti_value =
        "Tallenna";
    public static $tallenna_uusi_pikakommentti_title =
        "Tallentaa uuden pikakommentin";

    public static $tallenna_muokkaus_pikakommentti_value =
        "Tallenna muutokset";
    public static $poista_pikakommentti_value = "X";
    public static $poista_pikakommentti_title = "Poistaa pikakommentin";
    public static $poistovahvistus_pikakommentti_value =
        "Vahvista poisto";
    public static $poistovahvistus_pikakommentti_title =
        "Klikkaamalla pikakommentti tuhotaan lopullisesti";
    public static $peru_poisto_pikakommentti_value =
        "Peru poisto";
    public static $muokkaa_pikakommentti_value =
        "Muokkaa";
    public static $muokkaa_pikakommentti_title =
        "Muokkaa pikakommenttia";
    public static $peruminen_pikakommentti_value =
        "Takaisin pikakommentteihin";
    public static $peruminen_pikakommentti_title =
        "Peruu toiminnon eikä tee muutoksia tietokantaan";
    
    public static $takaisin_pikakommentteihin_value =
        "Takaisin";
    public static $pikakommentit_nayta_value =
        "Pikakommentit";
    public static $pikakommentit_nayta_title =
        "Klikkaamalla n&auml;et pikakommentit";
    public static $pikakommentit_sulje_value =
        "Sulje";
    public static $pikakommentit_sulje_title =
        "Klikkaamalla suljet pikakommenttiruudun.";


    public static function ilmoitus_pikakommentit_poistettu($lkm){
        if($lkm > 0){
            $mj = "<br /> Pikakommentit (yht. ".$lkm." kpl) poistettu.";
        }
        else{
            $mj = "<br /> (Poistettavia pikakommentteja ei l&ouml;ytynyt)";
        }
        return $mj;
    }
}
?>
