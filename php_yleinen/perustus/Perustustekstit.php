<?php

/**
 * Tänne tulevat varsinkin sellaiset ilmoitukset ja muut tekstit, jotka
 * saattavat päätyä käyttäjälle asti. Kehittäjän testausilmoitukset eivät
 * välttämättä ole täällä (jos laiskottaa).
 *
 * @author kerkjuk_admin
 */
class Perustustekstit {
    public static $ilm_tiedoissa_ei_muutoksia =
            "Tiedoissa ei havaittu muutoksia!";
    public static $ilm_tietoja_ei_tietokannassa =
            "Tietoja ei havaittu tietokannassa!";
    public static $syotteen_tarkistusvirhe =
            "-muuttujan arvossa virhe! Nykyinen arvo: ";
    public static $tyhja_merkkijono =
            "tyhj&auml; merkkijono";
    
    public static $muuttujan_arvo_vaarantyyppinen = 
        "-muuttujan arvo on v&auml;&auml;r&auml;ntyyppinen! ";
    public static $muuttujan_arvo_tyhja = 
        "-muuttujan arvo on tyhj&auml; (ei sallittu)! ";
    
    public static $virhe_arvo_vaarantyyppinen=
            "Virhe: arvo v&auml;&auml;r&auml;ntyyppinen!";
    public static $Tietoja_ei_tallennettu=
            "Tietoja ei tallennettu!";
     
    public static $malliluokkapohja_virheilm_muutostallennuksen_tietokantavirhe=
            "Malliluokkapohja: Virhe tietokantaolio->update_rivi()-metodissa!";
                                   
}

?>
