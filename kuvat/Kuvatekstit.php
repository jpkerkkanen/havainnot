<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tekstit
 *
 * @author kerkjuk_admin
 */
class Kuvatekstit {
    // Muuttujien esittely:
    // ========================================================================
    // Painikkeet:
    public static $painike_tallenna_uusi_kuva_value = "Tallenna kuva";
    public static $painike_tallenna_muokkaus_kuva_value = "Tallenna kuvatietojen muutokset";
    public static $painike_poista_kuva_value = "Poista kuva";
    public static $painike_poistovahvistus_kuvalinkki_value = 
            "Poista vain kuvalinkki";
    public static $painike_poistovahvistus_kuvalinkki_title = 
            "Poistaa vain linkin kuvan ja havainnon v&auml;lilt&auml;, ei itse kuvaa.";
    public static $painike_poistovahvistus_kuva_value = 
            "Poista kuva lopullisesti";
    public static $painike_poistovahvistus_kuva_title = 
            "Poistaa kuvatiedoston kaikki versiot sek&auml; kuvaan liittyv&auml;t pikakommentit lopullisesti";
    public static $painike_peru_poisto_kuva_value = "Peru kuvan poisto";
    public static $painike_muokkaa_kuva_value = "Muokkaa kuvatietoja";
    public static $painike_peruminen_kuva_value = "Poistu";
    public static $painike_piilota_kuva_value = "Palaa takaisin";
    public static $painike_uusi_kuva_value = "Lisää uusi kuva";
    public static $painike_uusi_kuva_havaintoihin_value = "Uusi kuva";
    public static $painike_uusi_kuva_havaintoihin_title = 
            "Uuden kuvan lis&auml;ys niin, ett&auml; se n&auml;kyy kaikissa valituissa havainnoissa.";
    
    public static $painike_nayta_kuva_albumit_value = "Katsele kuvia";
    public static $painike_nayta_kuva_value = "N&aumlyt&auml;";
    public static $painike_nayta_kuvien_esikatselu_value = "Esikatselukuvat";


    

    // Kuvailmoitukset:
    public static $ilm_kuva_uusi_tallennus_ok = 
        "Uusi kuva lis&auml;ttiin onnistuneesti!";
    public static $ilm_kuva_uusi_tallennus_peruttu = 
        "Uuden kuvan lis&auml;ys peruttu!";
    public static $ilm_kuva_uusi_tallennus_eiok =
        "Uuden kuvan lis&auml;ys ei onnistunut!";
    
    public static $ilm_kuvalinkit_tallennus_eiok =
        "Virhe lajikuvalinkkien tai havaintokuvalinkkien tallennuksessa! Tallennettu vain  ";
    
    public static $ilm_kuva_uusi_ladatun_kuvan_tietohaku_eiok =
        "Uuden kuvantiedoston tietojen haku ei onnistunut!";
    
    public static $ilm_kuva_uusi_minikuvatallennus_virh_lkm =
        "Uuden kuvan minikuvatallennuksen virheiden lkm: ";
    
    public static $ilm_kuva_muokkaustallennus_ok =
        "Kuvan muutokset tallennettu onnistuneesti!";
    public static $ilm_kuva_muokkaustallennus_eiok =
        "Kuvan muutosten tallennus ep&auml;onnistui!";
    public static $ilm_kuva_muokkaus_peruttu =
        "Kuvan muokkaus peruttu!";

    public static $ilm_kuva_poisto_ok = 
            "Kuvatietojen ja kuvatiedostojen poisto onnistui!";
    public static $ilm_kuva_poisto_ei_ok = "Virhe kuvan poistossa!";
    public static $ilm_kuva_tied_poisto_ei_ok_poistettu_lkm = 
            "Virhe kuvatiedostojen poistossa! Poistettujen tiedostojen lukumäärä on ";
    public static $ilm_kuva_poisto_peruttu = "Kuvan poistaminen peruttu!";
    public static $ilm_kuva_virhe_kuvatiedoston_poistossa = 
            "Virhe kuvan poistossa! Kuvan tiedot poistettu
                    tietokannasta, mutta kuvatiedoston poisto ei onnistunut. ";
    
    public static $ilm_kuva_virhe_minikuvatiedoston_poistossa = 
            "Virhe minikuvatiedoston poistossa! ";
    
    public static $ilm_kuva_virhe_havaintokuvalinkin_poistossa_id_huono = 
            "Virhe linkin poistossa! Kuvan ja havainnon id ei kelpaa!";
    
    public static $ilm_kuva_virhe_havaintokuvalinkin_poistossa = 
            "Virhe havaintokuvalinkin poistossa, kuvan ja havainnon id ok!";
    
    public static $ilm_kuva_havaintokuvalinkkia_poistettu = 
            " havaintokuvalinkki&auml; poistettu onnistuneesti!";
    
    public static $ilm_kuva_poistettavaa_ei_loytynyt = 
            "Virhe: poistettavaa kuvaa ei l&ouml;ytynyt!";
    
    public static $ilm_kuva_virhe_tied_osoite_ei_kelpaa = 
            "Virheellinen tiedosto-osoite!";
    
    public static $kuvalomakeohje = "Kirjoita kuvan tiedot ja tallenna!";
    
    public static $kuvan_poistovarmistusteksti_2options = 
            "Voit poistaa joko kuvan pikakommentteineen tai sitten vain kuvalinkin, 
            jolloin kuva ja pikakommentit j&auml;&auml;v&auml;t j&auml;ljelle, 
            mutteiv&auml;t n&auml;y t&auml;m&auml;n havainnon yhteydess&auml;.";
    
    public static $kuvan_poistovarmistusteksti_1option = 
            "Vahvista kuvan lopullinen poisto. Kuvatiedosto ja kaikki kuvaan
                liittyv&auml;t pikakommentit poistetaan lopullisesti.";
    
    public static $kuvalomake_nayttokoko_otsikko = "Kuvan n&auml;ytt&ouml;koko";
    
    public static $kuvalomake_virheilm_vuosi = 
            "Vuosiluvun pit&auml;&auml; olla 4-numeroinenluku (esim. 1999) tai tyhj&auml;!";
    public static $kuvalomake_virheilm_kk = 
            "Kuukauden pit&auml;&auml; olla luku v&auml;lilt&auml; 1-12 tai tyhj&auml;!";
    public static $kuvalomake_virheilm_paiva = 
            "Vuosiluvun pit&auml;&auml; olla 4-numeroinen luku (esim. 1999) tai tyhj&auml;!";
    
    public static $kuvalomake_virheilm_tied_ei_havaittu = 
            "Tiedostoa ei havaittu!";
    
    public static $kuvalomake_virheilm_upload_err_ini_size =
            "Tiedosto on liian suuri (php.ini)!";
    public static $kuvalomake_virheilm_upload_err_partial =
            "Tiedoston lataus keskeytyi!";
    public static $kuvalomake_virheilm_upload_err_form_size1 =
            "Tiedosto on liian suuri (html-asetus = ";
    public static $kuvalomake_virheilm_upload_err_form_size2 =
            "kt";
    public static $kuvalomake_virheilm_upload_err_ini_no_file =
            "Mit&auml;&auml;n tiedostoa ei ladattu!";
    public static $kuvalomake_virheilm_upload_err_ini_no_tmp_dir =
            "Palvelimen tmp-kansio puuttuu!";
    public static $kuvalomake_virheilm_upload_err_ini_cant_write =
            "Palvelimelle kirjoitus estetty!";
    public static $kuvalomake_virheilm_upload_err_ini_extension =
            "Failed due to extension!";
    
    public static $kuvalomake_virheilm_tuntematon =
            "Tuntematon virhe!";
    
    public static $kuvalomake_virheilm_tied_ei_kuva1 =
            "Tiedosto <span class=korostus>'";
    public static $kuvalomake_virheilm_tied_ei_kuva2 =
            "'</span> ei taida olla kuva!?";
    
    public static $kuvalomake_virheilm_no_uploaded_file = 
            "Tiedosto ei ole HTTP POST:in kautta ladattu (tietoturvariski)!";
    
    public static $kuvalomake_virheilm_tiedtunniste_vaara = 
           "Tiedostotunniste v&auml;&auml;r&auml;! Vain 
                    jpg-, jpeg- gif- ja png-tyyppiset kuvat kelpaavat.";
    
    public static $kuvalomake_virheilm_tied_liian_iso1 = 
            "Tiedosto ";
    public static $kuvalomake_virheilm_tied_liian_iso2 = 
            " on liian suuri! Kuvan koko saa olla korkeintaan ";
    public static $kilotavulyhenne = 
            "kt";
    
    // Tietokantailmoitukset:
    public static $ilm_tiedoissa_ei_muutoksia = "Tiedoissa ei havaittu muutoksia";

    public static $ilm_kuva_haku_tietokannasta_ei_onnistunut = 
        "Kuvatietojen haku tietokannasta ei onnistunut!";
    
    
    //=========================================================================
    //=========================================================================
    //================== KUVALINKKITEKSTIT =====================================
    public static $linkki_on_jo_olemassa = 
        "Linkki on jo olemassa!";
    public static $linkin_luominen_onnistui = 
        "Linkin luominen onnistui!";
    public static $virhe_linkin_luomisessa = 
        "Virhe linkin luomisessa!";
    public static $linkin_muokkaus_onnistui = 
        "Linkin muokkaus onnistui!";
    public static $virhe_linkin_muokkauksessa = 
        "Virhe linkin muokkauksessa!";
}

?>
