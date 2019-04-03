<?php
/**
 * Painikkeiden arvot ja erinäisiä metodeita.
 * Näihin static-muuttujiin pääsee käsiksi muualtakin suoraan luokan nimen avulla
 * ilman olion luomista.
 */
class Bongauspainikkeet{

    // Havainnot (toiminnot)
    public static $NAYTA_MONEN_HAVAINNON_VALINTA_VALUE = 
            "Lisää monta kerralla";
    public static $NAYTA_MONEN_HAVAINNON_VALINTA_TITLE = 
            "Avaa ikkunan, josta lajit voi valita kerralla.";
    public static $TALLENNA_MONTA_HAV_KERRALLA_VALUE = "Tallenna valitut";
    public static $TALLENNA_MONTA_HAV_KERRALLA_TITLE = 
            "Tallentaa kaikki valitut lajit samoilla tiedoilla (paikka, aika, kommentti).";
    public static $TALLENNA_UUSI_HAVAINTO_VALUE = "Tallenna uusi havainto";
    public static $TALLENNA_MUOKKAUS_HAVAINTO_VALUE = "Tallenna havainnon muutokset";
    
    public static $HAVAINNOT_TALLENNA_VALITTUJEN_MUOKKAUS_VALUE = 
            "Tallenna muutokset";
    public static $HAVAINNOT_TALLENNA_VALITTUJEN_MUOKKAUS_TITLE = 
            "Tallentaa kaikille valituille havainnoille saman ajan, paikan ja kommentin! Vain omia voi muokata.";
    
    public static $HAVAINNOT_NAYTA_MONIMUOKKAUSLOMAKE_VALUE = 
            "Muokkaa";
    
    public static $HAVAINNOT_NAYTA_MONIMUOKKAUSLOMAKE_TITLE = 
            "Voit valita useita (omia) havaintoja kerralla, mutta huomaa, ett&auml; kaikille tulee sama aika, paikka ja kommentti!";
    
    public static $HAVAINNOT_POISTA_VALITUT_VALUE = "Poista";
    public static $HAVAINNOT_POISTA_VALITUT_TITLE = 
            "Poistaa kaikki valitut (omat) havainnot!";
    
    public static $HAVAINNOT_TILASTOT_VALUE = "Tilastot";
    public static $HAVAINNOT_TILASTOT_TITLE = 
            "T&auml;st&auml; p&auml;&auml;set katselemaan havaintotilastoja!";
    
    public static $HAVAINNOT_MONIPOISTOVAHVISTUS_VALUE = 
            "Vahvista valittujen havaintojen poisto";
    
    public static $HAVAINNOT_MONIPOISTOVAHVISTUS_TITLE = 
            "Poistaa valitut havainnot lopullisesti (voit viel&auml; muokata valintoja)";
    
    public static $HAVAINNOT_MONIPOISTON_PERUMINEN_VALUE = 
            "Peru havaintojen poisto";
    
    public static $HAVAINNOT_MONIPOISTON_PERUMINEN_TITLE = 
            "Takaisin havainton&auml;kym&auml;&auml;n";
    
    public static $HAVAINNOT_MONIKOPIOI_ITSELLE_VALUE = "Kopioi";
    public static $HAVAINNOT_MONIKOPIOI_ITSELLE_TITLE =
        "Lis&auml;&auml; k&auml;ytt&auml;j&auml;lle havainnot, joiden tiedot kopioitu valituista (ei kuvia eik&auml; kommentteja)";
    
    
    public static $POISTA_HAVAINTO_VALUE = "X";
    public static $POISTOVAHVISTUS_HAVAINTO_VALUE = "Vahvista havainnon poisto";
    public static $PERU_POISTO_HAVAINTO_VALUE = "Peru havainnon poisto";
    public static $MUOKKAA_HAVAINTO_VALUE = "Muokkaa";
    public static $PERUMINEN_HAVAINTO_VALUE = "Takaisin havaintoihin";
    public static $UUSI_HAVAINTO_VALUE = "Uusi havainto";
    public static $UUSI_HAVAINTO_TITLE = "Uusien havaintojen lis&auml;&auml;minen";
    public static $KATSO_HAVAINTO_VALUE = "Tarkastele havaintoa";
    public static $TAKAISIN_HAVAINTOIHIN_VALUE = "Takaisin havaintoihin";
    public static $HAVAINNOT_VALITSE_LAJILUOKKA_VALUE = "->";

    public static $HAVAINNOT_PIILOTA_KOMMENTTISARAKE_VALUE = "Kavenna";
    public static $HAVAINNOT_PIILOTA_KOMMENTTISARAKE_TITLE =
        "Piilottaa tai tuo esiin kommenttisarakkeen";

    public static $HAVAINNOT_NAYTA_HENKILON_HAVAINNOT_TITLE =
        "Klikkaamalla n&auml;et henkil&ouml;n havainnot";
    
    public static $HAVAINNOT_NAYTA_PAIKAN_HAVAINNOT_TITLE =
        "Klikkaamalla n&auml;et kyseisen paikan kaikki havainnot";
    
    public static $HAVAINNOT_NAYTA_HENKILON_LAJIT_SUOMI_TITLE =
        "Klikkaamalla saat listan henkil&ouml;n Suomessa havaitsemista lajeista";
    
    public static $HAVAINNOT_NAYTA_HENKILON_LAJIT_KAIKKI_TITLE =
        "Klikkaamalla saat listan henkil&ouml;n havaitsemista lajeista (Suomi ja ulkomaat)";

    public static $HAVAINNOT_NAYTA_LAJIHAVAINNOT_TITLE =
        "Klikkaamalla n&auml;et kaikki havainnot lajista.";

    public static $HAVAINNOT_SULJE_HENKILON_HAVAINNOT_VALUE =
        "Sulje";


    // Aikavalinnat:
    public static $ed_paiva = "<";
    public static $seur_paiva = ">";
    public static $ed_vko = "<<";
    public static $seur_vko = ">>";
    public static $today = "=";
    
    public static $ed_paiva_title = "Edellinen p&auml;iv&auml;";
    public static $seur_paiva_title = "Seuraava p&auml;iv&auml;";
    public static $ed_vko_title = "Edellinen viikko";
    public static $seur_vko_title = "Seuraava viikko";
    public static $today_title = "Tänään";

    // Lajiluokat:
    public static $TALLENNA_UUSI_LAJILUOKKA_VALUE = "Tallenna uusi lajiluokka";
    public static $TALLENNA_MUOKKAUS_LAJILUOKKA_VALUE = "Tallenna havainnon muutokset";
    public static $POISTA_LAJILUOKKA_VALUE = "Poista lajiluokka";
    public static $POISTOVAHVISTUS_LAJILUOKKA_VALUE = "Vahvista havainnon poisto";
    public static $PERU_POISTO_LAJILUOKKA_VALUE = "Peru havainnon poisto";
    public static $MUOKKAA_LAJILUOKKA_VALUE = "Muokkaa lajiluokkaa";
    public static $PERUMINEN_LAJILUOKKA_VALUE = "Poistu tallentamatta";
    public static $UUSI_LAJILUOKKA_VALUE = "Uusi laji";
    public static $UUSI_LAJILUOKKA_TITLE = 
            "Uuden lajin tai luokan tallentaminen";
    public static $KATSO_LAJILUOKKA_VALUE = "Tarkastele lajiluokkaa";
    public static $TAKAISIN_LAJILUOKKA_VALUE = "Takaisin lajiluokkiin";
    
    public static $LAJILUOKAT_SULJE_NAKYMA_VALUE = "X";
    public static $LAJILUOKAT_SULJE_NAKYMA_TITLE = 
            "Sulkee lajiluokkan&auml;kym&auml;n";
    public static $LAJILUOKAT_SULJE_LOMAKENAKYMA_TITLE = 
            "Sulkee lomaken&auml;kym&auml;n";
    
    public static $LAJILUOKAT_NAYTA_VALUE = "Lajiluokat";
    public static $LAJILUOKAT_NAYTA_TITLE = 
            "Lajiluokkien katselu ja muokkaus";
    
    public static $LAJILUOKAT_MUOKKAA_TITLE = "Klikkaamalla muokkaamaan!";
    public static $LAJILUOKAT_SYOTA_UUSI_TITLE = 
            "Klikkaamalla uuden lis&auml;ys!";
    
    public static $LAJILUOKAT_POISTA_VALUE = "Poista";
    public static $LAJILUOKAT_POISTA_TITLE = 
            "Lajiluokan poistaminen";
    
    public static $LAJILUOKAT_SIIRRA_HAVKUV_VALUE = "Siirr&auml;";
    public static $LAJILUOKAT_SIIRRA_HAVKUV_TITLE = 
            "Havaintojen ja kuvien siirto toiselle lajille (esim. ennen tuplalajiluokan poistoa)";
    
    public static $LAJILUOKAT_TALLENNA_UUSI_NIMIKUVAUS_VALUE = "Tallenna";
    public static $LAJILUOKAT_TALLENNA_MUUTOKSET_NIMIKUVAUS_VALUE = "Tallenna muutokset";
    
    public static $LAJILUOKAT_SULJE_NIMIKUVAUSNAKYMA_VALUE = "X";
    public static $LAJILUOKAT_SULJE_NIMIKUVAUSNAKYMA_TITLE = 
            "Sulkee lomaken&auml;kym&auml;n";
    

    // KUVAT:
    public static $TALLENNA_UUSI_KUVA_VALUE = "Tallenna kuva";
    public static $TALLENNA_MUOKKAUS_KUVA_VALUE = "Tallenna kuvatietojen muutokset";
    public static $POISTA_KUVA_VALUE = "Poista kuva";
    public static $POISTOVAHVISTUS_KUVA_VALUE = "Poiston vahvistus";
    public static $PERU_POISTO_KUVA_VALUE = "Peru kuvan poisto";
    public static $MUOKKAA_KUVA_VALUE = "Muokkaa kuvatietoja";
    public static $PERUMINEN_KUVA_VALUE = "Poistu";
    public static $UUSI_KUVA_VALUE = "Uusi kuva";
    public static $NAYTA_KUVA_ALBUMIT_VALUE = "Bongauskuvat";
    public static $NAYTA_KUVA_VALUE = "N&auml;yt&auml; kuva";
    public static $NAYTA_ESIKATSELUKUVAT_VALUE = "Esikatselukuvat";
    public static $KAYNNISTA_DIAESITYS_VALUE = "Diaesitys";

    public static $NAYTA_KUVA_ALBUMIT_TITLE = "Avaa bongausten kuva-albumit";
    public static $NAYTA_KUVA_TITLE = "N&auml;yt&auml; kuvan tietoineen";
    public static $NAYTA_ESIKATSELUKUVAT_TITLE = "Avaa albumin esikatselukuvat";
    public static $KAYNNISTA_DIAESITYS_TITLE = 
                "Käynnistää automaattisen kuvaesityksen";
    
    // Vakihavaintopaikat;
    public static $vakipaikka_luo_uusi_value = "Luo uusi";
    public static $vakipaikka_luo_uusi_title = 
            "Luo uusi vakipaikka, jos käyt siellä toistuvasti";
    
    public static $vakipaikka_muokkaa_value = "Muokkaa";
    public static $vakipaikka_muokkaa_title = 
            "Muokkaa aktiivisen havaintopaikan tietoja";
    
    public static $vakipaikka_tallenna_uusi_value = "Tallenna uusi";
    public static $vakipaikka_tallenna_uusi_title = 
            "Tallentaa uuden vakipaikan";
    public static $vakipaikka_sulje_lomake_value = "Lopeta";
    public static $vakipaikka_sulje_lomake_title = 
            "Sulkee lomakenäkymän. Huomaa: ei tallenna mitään!";
    

    // YLEISET:
    public static $KIRJAUDU_ULOS_VALUE = "Kirjaudu ulos";
}

/**
 * Sisältää sivulla näkyvät kiinteät tekstit lukuunottamatta painikkeita (ovat
 * erikseen)
 *
 */
class Bongaustekstit{
    /**
     * Palauttaa merkkijonon merkityksellä "Tuntematon";
     * @var type 
     */
    public static $tuntematon = "Tuntematon";
    public static $undefined = "-- ei-määritelty --";
    public static $ja = "ja";
    
    public static $havtauluots_nro = "Nro";
    public static $havtauluots_laji = "Laji";
    public static $havtauluots_aika = "Aika";
    public static $havtauluots_paikka = "Paikka";
    public static $havtauluots_kommentti = "Kommentti";
    public static $havtauluots_bongaaja = "Bongaaja";
    public static $havtauluots_toiminnot = "Valinnat";
    public static $havtauluots_pk = "Pikakommentit";
    
    public static $havtauluots_varoitus = 
            "HUOM! Poistettavien havaintojen pikakommentit poistetaan (kuvia ei)!";
    
    public static $havtaulkuvan_klikkausohje = "Klikkaa suuremmaksi!";
    
    // Havaintotaulun lisäluokitusmerkinnät:===================================
    public static $havtaul_lisaluok_elis = "ELIS";
    public static $havtaul_lisaluok_piha = "KOTIPIHA";
    public static $havtaul_lisaluok_maaelis = "MAAELIS";
    public static $havtaul_lisaluok_eko = "EKO";
    public static $havtaul_lisaluok_eko2 = "BUSSIEKO";
    public static $havtaul_lisaluok_tornien_taisto = "TORNIENTAISTO";
    public static $havtaul_lisaluok_vesilla = "VESILL&Auml;";
    //=========================================================================
    public static $havaintopaikkavalikko_otsikko = "Vakipaikka";
    public static $havaintopaikkalomakeohje = "Vakituisen paikan tiedot";
    public static $havaintopaikkalomake_Maa = "Maa";
    public static $havaintopaikkalomake_Selitys = "Selitys";
    
    //=========================================================================
    public static $otsikko1_bongaussivu1 = "Bong!";
    
    
    
    public static $otsikko1_tilastot_puolivuotis = 
            "Havaittujen lajien lukum&auml;&auml;r&auml; puolivuosittain. 
                Lajit saat klikkaamalla lukum&auml;&auml;r&auml;&auml;!";
    
    public static $max_nayttoilm_bongaussivu1 = " uusinta";
    public static $havaintoluokan_valinta_otsikko = "Luokka";

    public static $html_title_bongaussivu1 = "Bongauksia";

    // Ilmoitukset:
    public static $ilm_kuvia_ei_loytynyt =
        "Yht&auml;&auml;n kuvaa ei l&ouml;ytynyt!";
    
   public static $ilm_havaintokuvan_lisaaminen_huomautus =
        "Kuva linkitet&auml;&auml;n kaikkiin valittuihin havaintoihin (alla)";

    
    public static $ilm_ei_valintoja =
        "Yht&auml;&auml;n havaintoa ei ole valittu!";
    
    public static $ilm_ei_kelvollisia_valintoja =
        "Toisten havaintoja ei voi muokata!";

    public static $ilm_toimintoa_ei_toteutettu =
        "Toimintoa ei ole valitettavasti toteutettu!";
    public static $ilm_havainnon_lisays_ok = "Uusi havainto lis&auml;ttiin
                                        onnistuneesti!";
    
    public static $ilm_havaintojen_lisays_ok = "havaintoa lis&auml;ttiin
                                        onnistuneesti";
    public static $ilm_havaintojen_lisays_eiok = 
            "virhett&auml; havaittiin lis&auml;ysprosessissa!";
    public static $ilm_havainto_lajista = "havainto tallennettu!";
    public static $ilm_havainnon_lisays_peruttu = "Uuden havainnon lis&auml;ys
                                        peruttu!";
    
    public static $ilm_havaintojen_lisays_peruttu = 
            "Uusien havaintojen lis&auml;ys peruttu!";

    public static $havainto_nimi_kaytossa_virheilm =
    "Nimi on jo k&auml;yt&ouml;ss&auml;! Samaa ei saa olla kahdesti!";

    public static $ilm_havainnon_lisays_tai_muokkaus_peruttu =
    "Paluu havaintoihin";
    public static $ilm_havainnon_lisays_eiok = "Uuden havainnon lis&auml;ys
                                        ei onnistunut!";

    public static $ilm_havainnon_poisto_eiok = "Havainnon poisto
                                        ei onnistunut! Kokeile uudelleen!";
    
    public static $ilm_havaintojen_poisto_eiok = 
            "virhett&auml; havaintojen poistossa!";
    public static $ilm_havaintojen_poisto_ok = 
            "havaintoa poistettu onnistuneesti!";
    
    public static $ilm_havainnon_lisaluokan_tallennus_eiok = 
            "Havainnon lis&auml;luokan tallennus ei onnistunut!";
    
    public static $ilm_havainnon_lisaluokkaa_poistettu = 
            "havainnon lis&auml;luokkaa poistettu!";
    public static $ilm_havainnon_lisaluokkaa_tallennettu = 
            "havainnon lis&auml;luokkaa tallennettu!";
    
    
    public static $havaintojakso_virheilm_tallennus_eiok = 
            "Havaintojakson tallennus ei onnistunut, eikä havaintoja tallennettu!";
    
    public static $ilm_havaintojaksolinkin_tallennus_eiok = 
            "Havaintojaksolinkin tallennus ei onnistunut!";
    
    public static $ilm_havaintojaksolinkki_jo_olemassa = 
            "Havainto on jo liitetty kyseiseen tapahtumaan!";
    
    public static $ilm_havaintojaksolinkkeja_luotu_kpl = 
            "kpl havaintoja liitetty havaintotapahtumaan.";
    
    // Havaintopaikkatoiminnot:
    public static $ilm_havaintopaikan_lisays_ok = 
            "Uusi havaintopaikka lis&auml;ttiin onnistuneesti!";
    public static $virheilm_havaintopaikan_lisays_eiok = 
            "Virhe uuden havaintopaikan lis&aumlyksessä!";
    public static $ilm_havaintopaikan_poisto_ok = 
            "Havaintopaikka poistettiin onnistuneesti!";
    public static $virheilm_havaintopaikan_poisto_eiok = 
            "Virhe havaintopaikan poistossa!";
    public static $ilm_havaintopaikan_muutos_ok = 
            "Havaintopaikan muutokset tallennettu onnistuneesti!";
    public static $virheilm_havaintopaikan_muutos_eiok = 
            "Virhe havaintopaikan muutosten tallennuksessa!";
    public static $havaintopaikan_poistovarmistus = 
            "Haluatko varmasti poistaa havaintopaikan?";
    
    public static $ilm_havaintopaikkaa_ei_loytynyt = 
            "Havaintopaikkaa ei löytynyt tietokannasta!";
    
    public static $ilm_havaintoa_ei_loytynyt = 
            "Havaintoa ei löytynyt tietokannasta!";
    
    // Seuraavat kaksi kuuluvat yhteen:
    public static $ilm_pikak_kpl_poistettu = 
            "pikakommenttia poistettu";
    public static $ilm_bkuvalinkit_lkm_muokattu = 
            "kuvalinkki&auml; muokattu onnistuneesti!";
    
    
    public static $ilm_havainnon_muokkaustallennus_ok = 
            "Havainnon muutokset tallennettu onnistuneesti!";
    
    public static $ilm_havainnon_monimuokkaustallennus_ok = 
            "havainnon muutokset tallennettu onnistuneesti!";
    
    public static $ilm_havainnon_monimuokkaustallennus_EI_ok = 
            "virhett&auml; havaintojen muutosten tallentamisessa!";
    
    public static $ilm_havainnon_muokkaus_kuvalinkkilj_muutettu=
            "Kuvalinkkien lajiluokat tarkastettu";
    public static $ilm_havainnon_muokkaustallennus_eiok = 
    "Muutoksia ei tehty tai niiden tallennus ep&auml;onnistui!";
    
    public static $ilm_havainnon_muutoksia_ei_havaittu = 
        "Muutoksia ei tehty, joten tallennuksessa ei ole mielt&auml;!";
    
    public static $ilm_havainnon_muokkaus_peruttu = "Havainnon muokkaus
                                        peruttu!";
    
    public static $ilm_havaintojen_muokkausvaroitus = 
            "Muutokset yll&auml; koskevat kaikkia alla olevia valittuja havaintoja!
            Kaikille valituille tulevat samat yll&auml; olevat arvot (muut eiv&auml;t muutu)!
                Valintoja voi poistaa t&auml;&auml;ll&auml;kin.";

    public static $havainnon_poiston_vahvistuskysymys =
        "Oletko varma, ett&auml; haluat poistaa havainnon lopullisesti? <br />
            My&ouml;s havaintoon liittyv&auml;t mahdolliset
            pikakommentit poistetaan samalla.";
    public static $ilm_havainnon_poisto_ok = "Havainnon poisto onnistui!";
    
    public static $ilm_havainnon_poisto_peruttu = "Havainnon poistaminen
                                        peruttu!";

    public static $ilm_lomaketietoja_puuttuu = "Tietoja puuttuu!
        T&auml;ydenn&auml; puuttuvat kohdat!";
    
    public static $tyhja_merkkijono = "Tyhj&auml; merkkijono";

    public static $ilm_pikakommentit_nakyviin =
        "Klikkaamalla saat pikakommentit esille!";

    public static $havainto_virheilm_tallennus_eiok = "Havainnon
    lis&auml;ys ep&auml;onnistui! (Yhteys- tai ohjelmavirhe. Kokeile uudestaan!)";

    // Havaintolomake:
    public static $havaintolomake_uusi_ohje =
    "Uusi havainto: anna tiedot ja paina Tallenna-nappia! (T&auml;hdelliset pakollisia.)";
    
    public static $havaintolomake_uusien_tallennus_ohje =
    "Valitse lajit, anna yhteiset tiedot ja paina Tallenna-nappia! (T&auml;hdelliset pakollisia.)";
    
    public static $havaintolomake_havjaksohje = 
        "Havaintotapahtuma: valitse tapahtuma tai anna uuden tiedot";
    
    public static $havaintolomake_havjaksohje_tarkempi = 
        "Jokainen havainto liittyy johonkin tapahtumaan, oli se sitten
        ikkunasta katselu, tornilla käynti tai viikon matka.
        Tällä tavalla havainnot saadaan ryhmiteltyä ja näytettyä
        vaihtoehtoisilla tavoilla erityisesti, kun tapahtumaan
        liittyy useampia havaintoja." ;
    
    public static $havaintolomakemuokkaus_ohje =
    "Havainnon muokkaus (T&auml;hdelliset pakollisia.)";
    
    public static $havaintolomakemonimuokkaus_ohje =
    "Usean havainnon muokkaus (T&auml;hdelliset pakollisia.)";

    public static $havaintolomake_laji_puuttuu_ohje =
    "Lis&auml;&auml; laji, ellei l&ouml;ydy valikosta!";

    // Lomakkeen kentät (huom aloita mieluummin luokan nimellä, niin löytää!:
    public static $havaintolomake_nro = "Nro";
    public static $havaintolomake_laji = "Laji";
    public static $havaintolomake_vuosi = "Vuosi";
    public static $havaintolomake_kk = "Kuukausi";
    public static $havaintolomake_paiva = "P&auml;iv&auml;";
    public static $havaintolomake_paikka = "Paikka";
    public static $havaintolomake_vakipaikka = "Vakipaikka";
    public static $havaintolomake_kommentti = "Kommentti";
    public static $havaintolomake_henkilo_id = "Henkil&ouml;n id";
    public static $havaintolomake_maa = "Maa";
    public static $havaintolomake_varmuus = "Varmuus";
    public static $havaintolomake_lkm = "Lkm";
    public static $havaintolomake_sukupuoli = "Sukupuoli";
    public static $havaintolomake_lisaluokitukset = "Lis&auml;luokitukset";
    public static $havaintolomake_aloitus = "Alkupvm";
    public static $havaintolomake_aloitusaika = "Aloitusaika (kello)";
    public static $havaintolomake_kesto = "Kesto";
    public static $havaintolomake_kellonaika = "Kellonaika";
    public static $havaintolomake_vrk = "vrk";
    public static $havaintolomake_h = "h";
    public static $havaintolomake_min = "min";
    public static $havaintolomake_uusi = "uusi";
    public static $havaintolomake_jaksovalikko_otsikko = "Tapahtuma";
    
    public static $havaintolomake_jaksonimiohje = "Esim: Päivälintuilu";
    public static $havaintolomake_jaksokommenttiohje = 
        "Esim: Kiva päiväretki Mustasaareen";
    
    public static $havaintolomake_noedit_ilm = "Ei valittu - vanha arvo säilyy";
    
    public static $havaintolomake_muok_jaksolisaysohje = 
            "Havainto liitetään valittuun tapahtumaan (joita voi olla useita).";
    
   
    // Lomakkeen tms. kentät (mieluummin kuten yllä)
    public static $paiva = "P&auml;iv&auml;";
    public static $pvm = "Pvm";
    public static $kk = "Kuu";
    public static $vuosi = "Vuosi";
    public static $aika = "Aika";
    public static $paikka = "Paikka";
    public static $kommentti = "Kommentti";
    public static $nimi = "Nimi";
    public static $laji = "Laji";
    public static $laji_siirto = "Kohdelaji";
    public static $laji_alkup = "Alkuper&auml;inen laji";


    public static $havaintolomake_virheilm_lajivalikko ="Virhe lajivalikkokoodissa";
    public static $havaintolomake_virheilm_pvm = "Pvm ei ole oikein! Korjaa se, tack!";
    public static $havaintoa_ei_loytynyt_virheilm =
                    "Havaintoa ei l&ouml;ytynyt tietokannasta! Ilmoita JP:lle!";
    
    public static $havainnot_suomessa = "Havainnot Suomessa";
    public static $havainnot_kaikkialla = "Havainnot kaikkialla";
    public static $havainnot_elikset = "elikset";
    public static $havainnot_Elikset = "Elikset";
    public static $havainnot_Elikset_title = 
            "Kaikki havaitsemani lajit (klikkaamalla saat lajit esille)";
    
    public static $havainnot_suomielikset_title = 
            "Kaikki Suomessa havaitsemani lajit (klikkaamalla saat lajit esille)";
    public static $havainnot_vuodarit = "Vuodarit";
    public static $havainnot_ekovuodarit = "Ekovuodarit";
    public static $havainnot_vuodarit_kuluva_vuosi_title = 
            "Kaikki tänä vuonna havaitsemani lajit (klikkaa..)";
    
    public static $havainnot_vuodarit_kuluva_vuosi_FIN_title = 
            "Kaikki tänä vuonna Suomessa havaitsemani lajit (klikkaa..)";
    
    public static $havainnot_ekovuodarit_title = 
            "Kaikki tänä vuonna ekotyyliin (asuinpaikasta lihasvoimalla) havaitsemani lajit";
    
    public static $havainnot_syksy = "syksy";
    public static $havainnot_kevat = "kev&auml;t";
    public static $havainnot_vuonna = "vuonna";

    public static $havainnot_aikavalikko_virheilm = "Virhe aikavalikossa!";
    
    //Kielivalikko:
    public static $kielivalikko_otsikko = "Kieli";
    public static $kielivalikko_virheilm = "Virhe kielivalikkokoodissa!";

    // Lajiluokkalomake:
    public static $lajiluokkalomake_ohje =
    "Uusi laji tai luokka: anna tiedot ja paina Tallenna-nappia! (T&auml;hdelliset pakollisia.)";

    public static $lajiluokkalomake_nimiohje =
    "Laji pienell&auml;, luokka isolla alkukirjaimella, kiitos!";

    public static $lajiluokkalomake_nimi_latina = "Nimi (latina)";
    public static $lajiluokkalomake_nimi_omakieli = "Nimi";
    public static $lajiluokkalomake_ylaluokka = "Yl&auml;luokka";
    public static $lajiluokkalomake_kuvaus = "Kuvaus";
    public static $lajiluokkalomake_kieli = "Kieli";
    public static $lajiluokkalomake_ei_ylatasoa = "Ei mit&auml;&auml;n";
    public static $lajiluokkalomake_ylaluokkaohje =
    "(Valitse 'Ei mit&auml;&auml;n', kun haluat tallentaa yl&auml;luokan.)";
    //=========================================================================
    //
    // Nimikuvauslomake:
    public static $nimikuvauslomake_ohje =
    "Kirjoita lajin tai luokan nimi ja kuvaus (ei pakollinen) kyseisell&auml; 
    kielell&auml; ja paina Tallenna-nappia!";

    public static $nimikuvauslomake_nimiohje =
    "Laji pienell&auml;, luokka isolla alkukirjaimella, kiitos!";

    public static $nimikuvauslomake_nimi = "Nimi";
    public static $nimikuvauslomake_kuvaus = "Kuvaus";
    public static $nimikuvauslomake_kieli = "Kieli";
    //=========================================================================

    // Siirtolomake:
    public static $siirtolomakeohje =
        "Valitse valikosta laji, jolle alkuper&auml;isen <br/>
            lajin havainnot ja kuvat siirret&auml;&auml;n:";
    
    public static $siirtopalaute1 =
        " havaintoa ja ";
    
    public static $siirtopalaute2 =
        " kuvaa siirretty toiseen lajiin!";
    
    
    //=========================================================================
    public static $lajiluokka_ja_kuvaus_tallennus_ok = "Lajiluokan ja
        kuvauksen tallennus onnistui hienosti!";
    
    public static $lajiluokka_muutostallennus_ok = 
            "Lajiluokan muutosten tallennus onnistui hienosti!";
    
    public static $lajiluokan_poisto_ok = 
            "Lajiluokan poisto onnistui hienosti!";
    
    public static $lajiluokan_poisto_varmistuskysymys = 
            "Haluatko todella poistaa lajiluokan lopullisesti?";
    
    public static $lajiluokan_poisto_perumisviesti = 
            "Lajiluokan poisto peruttu!";
    
    public static $lajiluokan_muok_ei_voi_suomenkiel = 
        "Sorppa! Suomenkielisi&auml; et voi muokata!";
    
    public static $lajiluokan_havaintosiirtovirhe = 
        "Virhe havaintojen siirrossa! ";
    
    public static $lajiluokan_kuvasiirtovirhe = 
        "Virhe kuvien siirrossa! ";

    public static $lajiluokkalomake_virheilm_ylaluokkavalikko = "Virhe
        yl&auml;luokan valikossa!";

    public static $lajiluokka_virheilm_tallennus_eiok = "Lajiluokan
    lis&auml;ys ep&auml;onnistui! (Yhteys- tai ohjelmavirhe. Kokeile uudestaan!)";
    
    public static $lajiluokka_virheilm_vanha_id_lj_eiok = 
            "Virhe lajiluokkalinkin muutoksessa: vanha lajiluokka-id ei kelpaa!";
    
    public static $lajiluokka_virheilm_lajikuvalinkkia_ei_loytynyt = 
            "Virhe lajiluokkalinkin muutoksessa: lajikuvalinkki&auml; ei l&ouml;ytynyt tietokannasta!";
    
    public static $lajiluokka_virheilm_poisto_eiok_havaintoja_loytyi = 
            "Lajiluokan poisto ei onnistu, koska lajiin liittyy havaintoja!";
    
    public static $lajiluokka_virheilm_poisto_eiok_kuvia_loytyi = 
            "Lajiluokan poisto ei onnistu, koska lajiin liittyy kuvia!";

    public static $lajiluokka_virheilmoitus_tiedoissa_virheita = 
            "Tiedoissa virheit&auml;!";
    public static $lajiluokka_virheilmoitus_muutoksia_ei_havaittu = 
            "Muutoksia ei havaittu!";
    public static $lajiluokka_virheilmoitus_viallinen_nimi_latina = 
            "Lajiluokan nimess&auml; (latinax) on jotakin vikaa tai se on tyhj&auml;!";
    
    public static $lajiluokka_virheilmoitus_viallinen_ylaluokka_id = 
            "Ylaluokka_id on viallinen!";
    public static $lajiluokka_virheilmoitus_on_jo_olemassa_latina = 
            "Lajiluokka (latinaksi) on jo tietokannassa!";
    public static $lajiluokka_virheilmoitus_tallennus_eiok =
            "Tallennus ep&auml;onnistui!";
    public static $lajiluokka_virheilmoitus_muokkaustallennus_eiok =
            "Muokkauksen tallennus ep&auml;onnistui!";
    public static $lajiluokka_virheilmoitus_poisto_eiok =
            "Lajiluokan poisto ep&auml;onnistui!";
    
    public static $lajiluokka_virheilmoitus_poisto_eiok_lajiluokkaa_ei_loyt =
            "Poistettavaa lajiluokkaa ei l&ouml;ytynyt!";
    
    public static $lajiluokka_virheilmoitus_poisto_eiok_aliluokkia =
            "Lajiluokan poisto ep&auml;onnistui, koska lajiluokalla aliluokkia!";
    
    public static $lajiluokka_virheilmoitus_latina_tyhja =
            "Lajin nimi latinaksi ei saa olla tyhj&auml;!";
    
    public static $lajiluokka_virheilmoitus_yhtaan_lajiluokkaa_ei_loytynyt =
            "Yht&auml;&auml;n lajiluokkaa ei l&ouml;ytynyt!";
    
    public static $lajiluokka_lajiluokkataulun_otsikko =
            "Lajien (yl&auml;luokkien) nimet eri kielill&auml;:";
    
    public static $lajiluokka_toimintapainikkeet =
            "Toiminnot";

    public static $nayta_uusimmat = "N&auml;yt&auml; uusimmat";

    // yleisi&auml;:
    public static $ilm_ei_havaintoja = "Yht&auml;&auml;n havaintoa
                                    ei l&ouml;ydetty!";



    public static $ilm_lajiluokka_peruminen = "Lajiluokan lis&auml;ys/muokkaus peruttu";
    public static $ilm_kirjautunut = "Kirjautunut";
    public static $ilm_kirjautui_ulos = "kirjautui ulos"; // Tulee nimen j&auml;lkeen.

    
    public static $virheilmoitus_tietokantaolio_ei_maaritelty = 
            "Tietokantaolio m&auml;&auml;rittelem&auml;t&ouml;n!";
    
    public static $virheilmoitus_tietokantaolio_tai_id_ei_maaritelty = 
            "Tietokantaolio tai id m&auml;&auml;rittelem&auml;t&ouml;n!";
    //==========================================================================
    // Kuvaus-luokkaan liittyvät tekstit:
    // Lomakkeen kentät (huom aloita mieluummin luokan nimellä, niin löytää!:
    public static $kuvauslomake_lajiluokka = "Lajiluokka";
    public static $kuvauslomake_kieli = "Kieli";
    public static $kuvauslomake_nimi = "Nimi";
    public static $kuvauslomake_kuvaus = "Kuvaus";
    
    public static $kuvaus_tallennus_uusi_ok = 
            "Kuvauksen tallennus onnistui hienosti!";
    public static $kuvaus_tallennus_muokkaus_ok = 
            "Muutosten tallennus onnistui hienosti!";
    public static $kuvaus_virheilmoitus_tiedoissa_virheita = 
            "Tallennusta ei tehty! ";
    
    public static $kuvaus_virheilmoitus_nimi_tyhja =
            "Nimi ei saa olla tyhj&auml;! Kokeile uudestaan!";
    
    public static $kuvaus_virheilmoitus_nimi_jo_kaytossa =
            "Nimi on jo olemassa! Samaa lajinime&auml; ei saa olla kahdesti!";
    
    public static $kuvaus_virheilmoitus_muutoksia_ei_havaittu = 
            "Muutoksia ei havaittu!";
    public static $kuvaus_virheilmoitus_viallinen_kuvaus = 
            "Kuvauksessa on jotakin vikaa!";
    public static $kuvaus_virheilmoitus_viallinen_nimi = 
            "Kuvauksen nimess&auml; on jotakin vikaa!";
    public static $kuvaus_virheilmoitus_viallinen_kieli = 
            "Kieless&auml; on jotakin vikaa!";
    public static $kuvaus_virheilmoitus_tallennus_eiok =
            "Tallennus ep&auml;onnistui!";
    
    public static $kuvaus_virheilm_tallennus_eiok = "Virhe kuvauksen
                tallennuksessa! Kokeile uudestaan tai valita JP:lle!";
    public static $kuvaus_virheilmoitus_muokkaustallennus_eiok =
            "Muokkauksen tallennus ep&auml;onnistui!";
    public static $kuvaus_virheilmoitus_poisto_eiok =
            "Kuvauksen poisto ep&auml;onnistui!";
    
    public static $kuvaus_virheilmoitus_kuvaus_lj_kieli_parille_on_jo =
            "Kuvaus kyseiselle kieli-lajiluokka -yhdistelm&auml;lle on olemassa!";
    public static $kuvaus_virheilmoitus_suomenkielista_ei_saa_poistaa =
            "Suomenkielist&auml; kuvausta ei saa poistaa!";
    
    public static  $nimi_tuntematon = "Nimi tuntematon";
    
    
    //************************ Lajikuvalinkkitekstejä **************************
    public static $lajikuvalinkki_virheilmoitus_lj_muutokset_ei_ok = 
            "Virhe metodissa 'korjaa_lajikuvalinkit_lajimuokkauksen_jalkeen()'. Korjattuja: ";
    
    public static $lajikuvalinkki_ilmoitus_lj_muutokset_ok = 
            "Lajikuvalinkkien lajiluokkakorjaukset ok. Korjattuja: ";

    //==========================================================================
    //======================== ASETUSTEKSTEJÄ ==================================
    //==========================================================================
    
    //======================== Lisäluokitukset alku ============================
    public static $aset_lisaluokitus_piha_nimi = "Kotipiha";
    public static $aset_lisaluokitus_piha_selitys = 
                "Havainnot asunnosta tai sen v&auml;litt&ouml;m&auml;st&auml; l&auml;heisyydest&auml;";
    
    public static $aset_lisaluokitus_vesilla_nimi = "Vesill&auml;";
    public static $aset_lisaluokitus_vesilla_selitys = 
                        "Havainnot vesill&auml;: kanootti, vene, uimassa tms.";
    
    public static $aset_lisaluokitus_tornitaisto_nimi = "Tornien taisto";
    public static $aset_lisaluokitus_tornitaisto_selitys = 
                        "Havainnot Tornien taistossa";
    
    public static $aset_lisaluokitus_ekopinna_nimi = "Ekopinna";
    public static $aset_lisaluokitus_ekopinna_selitys = 
                        "Havainto lihasvoimalla";
    
    public static $aset_lisaluokitus_ekopinna2_nimi = "Ekopinna2";
    public static $aset_lisaluokitus_ekopinna2_selitys = 
                        "Havainto julkisen liikenteen avulla";
    
    public static $aset_lisaluokitus_elis_nimi = "Elis";
    public static $aset_lisaluokitus_elis_selitys = 
                "Ensimm&auml;inen havainto kyseisest&auml; lajista";
    
    public static $aset_lisaluokitus_maaelis_nimi = "Maaelis";
    public static $aset_lisaluokitus_maaelis_selitys = 
                    "Ensimm&auml;inen havainto havainto kyseisess&auml; maassa";
    
    //======================== Lisäluokitukset loppu ===========================
    //==========================================================================
}

/**
 * Sisältää toimintoryhmien nimet eli lomakkeiden name-arvot (erityisesti
 * kun yhtä arvoa kohti useita eri value-arvoa).
 * Nämä eivät näy käyttäjälle missään, eikä niitä tarvitse kääntää eri kielille.
 */
class Bongaustoimintonimet{
    public static $perustoiminto = "perustoiminto";
    public static $havaintotoiminto = "havaintotoiminto";
    public static $lajiluokkatoiminto = "lajiluokkatoiminto";
    public static $yllapitotoiminto = "yllapitotoiminto";
    public static $kuvatoiminto = "kuvatoiminto";
}

class Bongausasetuksia{
    
    // Havaintotaulukon kommenttisarakesolujen name-arvo:
    public static $havaintotaulukon_kommenttisolun_name_arvo = "kommentti";

    public static $painikepalkin_id = "painikepalkki";
    
    // Html-elementin id:n arvoja yms.:
    public static $havaintotaulun_id = "havaintotaulu";
    public static $havaintotietotaulun_id = "havaintotietolaatikko";
    public static $havaintotietotaulu_leftin_id = "havaintotietolaatikko_left";
    public static $havaintotaulun_class = "havaintotaulu";
    public static $havaintotauluotsikko_class = "havaintotauluotsikko";
    public static $havaintokuvakommentti_class = "havaintokuvakommentti";
    public static $havaintotaulu_parillinenrivi_class = 
        "havaintotaulu_parillinen_rivi";
    
    public static $havjaksolomake_nimi_id = "havjakslomakenimi_id";
    public static $havjaksolomake_kommentti_id = "havjakslomakekommentti_id";
    public static $havjaksolomake_alkupäiva_id = "paiva2";
    public static $havjaksolomake_alkukk_id = "kk2";
    public static $havjaksolomake_alkuvuosi_id = "vuosi2";
    public static $havjaksolomake_alkuh_id = "havjakslomake_alkuh_id";
    public static $havjaksolomake_alkumin_id = "havjakslomake_alkumin_id";
    public static $havjaksolomake_kestomin_id = "havjakslomake_kestomin_id";
    public static $havjaksolomake_kestoh_id = "havjakslomake_kestoh_id";
    public static $havjaksolomake_kestovrk_id = "havjakslomake_kestovrk_id";
    
    // Tietotaulu (yleisempi)
    public static $tietotaulun_id = "tietotaulu";
    public static $tietotaulun_class = "tietotaulu";
    public static $tietotauluotsikko_class = "tietotauluotsikko";
    public static $tietotaulu_parillinenrivi_class =
        "tietotaulu_parillinen_rivi";

    public static $havaintolomakkeen_id = "tietolomake_rajaton";
    public static $havaintolomake_kaikki_lajit_id = "tietolomake_maxi";
    public static $havaintolomake_lajivalintarivi_id = "lajivalintarivi";
    public static $havaintolomake_lajivalintaohje_id = "lajivalintaohje";
    public static $havaintolomake_lajivalikko_id = "lajivalikko";
    public static $havaintolomake_lajivalikkopainike_id = "lajivalikkopainike";
    public static $havaintolomake_vakipaikkavalikko_id = "paikkavalikko";
    public static $havaintolomake_vakipaikkavalikkopainike_id = 
            "paikkavalikkopainike";
    public static $havaintolomake_tallennustiedote_id = "tallennustiedote";
    
    public static $lajiluokkalomakkeen_id = "tietolomake_rajaton";
    public static $lomaketiedot_kunnossa = "tiedot_ok";
    public static $tietokantahaku_onnistui = "onnistui";
    public static $tietokantahaku_ei_loytynyt = "tuntematon";

    // Nimikuvauslomake
    public static $nimikuvauslomake_id = "tietolomake";
    public static $nimikuvauslomake_nimikentan_id = "nimikuvauslomake_nimikentta";
    public static $nimikuvauslomake_kuvauskentan_id = "nimikuvauslomake_kuvauskentta";
    
    // Havaintokuvasiirtolomake
    public static $havaintokuvasiirtolaatikko_id = "havaintokuvasiirtolaatikko";
    public static $havaintokuvasiirtolomake_id = "havaintokuvasiirtolomake";
    public static $havaintokuvasiirtolomake_name = "havaintokuvasiirtolomake";
    public static $havaintokuvasiirtolomakevalikko_name = "havaintokuvasiirtovalikko";
    public static $havaintokuvasiirtolomakevalikko_id = "havaintokuvasiirtovalikko";
    public static $havaintokuvasiirtolomake_nimikentan_id = "nimikuvauslomake_nimikentta";
    public static $havaintokuvasiirtolomake_kuvauskentan_id = "nimikuvauslomake_kuvauskentta";
    
    // Havaintojen aloitusvuosi:
    public static $aloitusvuosi = 2010;

    public static $nayta_oletushavainnot = 0;
    
    public static $nayta_vain_suomessa_havaitut = "suomi";
    //public static $havaintonayton_aluerajoitus = "havaintoalue_hav";
}
/**
 * Tämä säätää sen, näytetäänkö havainnoista uusimmat, vuoden mukaan vai
 * jotakin muuta:
 */
class Havaintojen_nayttomoodi{
    // Näin monta kork. oletuksena kerralla näytetään.
    public static $havaintojen_max_lkm = 100;

    public static $nayta_uusimmat = "nayta_uusimmat";
    public static $nayta_vuoden_mukaan = "vuoden_mukaan";
}

class Sukupuoli{
    public static $koiras = 1;
    public static $naaras = 2;
    public static $ei_maaritelty = 0;
    
    /**
     * @return <type> Palauttaa taulukon, joka sisältää varmuuksien
     * (valtuuksien) numeroarvot
     */
    public static function hae_sukupuoliarvot(){
        return array(Sukupuoli::$ei_maaritelty,
                    Sukupuoli::$koiras,
                    Sukupuoli::$naaras);
    }

    /**
     * Palauttaa arvoja vastaavat (samassa järjestyksessä)
     * kuvaukset taulukkona. Tarkastaa myös sen, onko
     * nimiä ja arvoja yhtä monta ja kielteisessä tapauksessa heittää
     * poikkeuksen vastaanottavalle ohjelmanosalle.
     * @return <type> Palauttaa taulukon, joka sisältää varmuusn kuvaukset.
     */
    public static function hae_sukupuolikuvaukset(){
        $kuvaukset = array("Ei-m&auml;&auml;ritelty", "Koiras", "Naaras"); //

        // Tarkistetaan täällä, että arvoja ja nimiä on yhtä monta. Ellei ole,
        // heitetään poikkeus.
        if(sizeof(Sukupuoli::hae_sukupuoliarvot()) != sizeof($kuvaukset)){
            throw new Exception("Virhe luokassa 'Sukupuoli': tarkista nimien
                                ja arvojen lukum&auml;&auml;r&auml;t!");
        }

        return $kuvaukset;
    }
    /**
     * Palauttaa lukua eli indeksiä vastaavan sukupuolikuvauksen, tai
     * tekstin "Tuntematon", jos parametri on sopimaton.
     * @param <type> $arvo
     * @return <type>
     */
    public static function hae_sukupuolikuvaus($arvo){
        $kuvaus = "Tuntematon";

        // Jos parametri on ok, haetaan varmuusn nimi. Jos huomataan
        // jotakin outoa, palautetaan virheviesti.
        if(is_numeric($arvo)) {
            try{
                $kuvaukset = Sukupuoli::hae_sukupuolikuvaukset();
                $arvot = Sukupuoli::hae_sukupuoliarvot();

                $i = 0; // Laskuri
                foreach ($arvot as $testiarvo) {
                    if($arvo == $testiarvo){
                        $kuvaus = $kuvaukset[$i];
                        break;
                    }
                    $i++;
                }
            }
            catch(Exception $poikkeus){
                $kuvaus = $poikkeus->getMessage();
            }
        }
        return $kuvaus;
    }
}


// Havainnon varmuus
class Varmuus{

    public static $valikko_otsikko = "Havainnon varmuus";

    // arvot:
    public static $varma = 100; //
    public static $melkoisen_varma = 80; // Tätä ei vielä ole valittavissa
    public static $epavarma = 50; //

    /**
     * @return <type> Palauttaa taulukon, joka sisältää varmuuksien
     * (valtuuksien) numeroarvot
     */
    public static function hae_varmuusarvot(){
        return array(Varmuus::$varma,
                    Varmuus::$epavarma);
    }

    /**
     * Palauttaa varmuusarvoja vastaavat (samassa järjestyksessä)
     * kuvaukset taulukkona. Tarkastaa myös sen, onko
     * nimiä ja arvoja yhtä monta ja kielteisessä tapauksessa heittää
     * poikkeuksen vastaanottavalle ohjelmanosalle.
     * @return <type> Palauttaa taulukon, joka sisältää varmuusn kuvaukset.
     */
    public static function hae_varmuuskuvaukset(){
        $kuvaukset = array("Sangen varma",
                            "Ep&auml;varma"); //

        // Tarkistetaan täällä, että arvoja ja nimiä on yhtä monta. Ellei ole,
        // heitetään poikkeus.
        if(sizeof(Varmuus::hae_varmuusarvot()) != sizeof($kuvaukset)){
            throw new Exception("Virhe luokassa 'Varmuus': tarkista nimien
                                ja arvojen lukum&auml;&auml;r&auml;t!");
        }

        return $kuvaukset;
    }
    /**
     * Palauttaa lukua eli varmuusindeksiä vastaavan varmuuskuvauksen, tai
     * tekstin "Tuntematon", jos parametri on sopimaton.
     * @param <type> $arvo
     * @return <type>
     */
    public static function hae_varmuuskuvaus($arvo){
        $kuvaus = "Tuntematon";

        // Jos parametri on ok, haetaan varmuusn nimi. Jos huomataan
        // jotakin outoa, palautetaan virheviesti.
        if(is_numeric($arvo)) {
            try{
                $kuvaukset = Varmuus::hae_varmuuskuvaukset();
                $arvot = Varmuus::hae_vaikeustasoarvot();

                $i = 0; // Laskuri
                foreach ($arvot as $testiarvo) {
                    if($arvo == $testiarvo){
                        $kuvaus = $kuvaukset[$i];
                        break;
                    }
                    $i++;
                }
            }
            catch(Exception $poikkeus){
                $kuvaus = $poikkeus->getMessage();
            }
        }
        return $kuvaus;
    }

    /**
     * Palauttaa valikon html:n
     */
    public static function muodosta_valikkohtml($otsikolla, $oletusvalinta_arvo){
        $valikkohtml = "";

        try{
            $arvot = Varmuus::hae_varmuusarvot();
            $nimet = Varmuus::hae_varmuuskuvaukset();
            $name_arvo = Havaintokontrolleri::$name_varmuus_hav;
            $id_arvo = "varmuusvalikko";
            $class_arvo = "";
            $oletusvalinta_arvo = $oletusvalinta_arvo;
            $otsikko = "";
            if($otsikolla){
                $otsikko = Varmuus::$valikko_otsikko;
            }
            $onchange_metodinimi = "";
            $onchange_metodiparametrit_array = array();

            $valikkohtml.= Html::luo_pudotusvalikko_onChange($arvot,
                                                            $nimet,
                                                            $name_arvo,
                                                            $id_arvo,
                                                            $class_arvo,
                                                            $oletusvalinta_arvo,
                                                            $otsikko,
                                                            $onchange_metodinimi,
                                                $onchange_metodiparametrit_array);

        }
        catch(Exception $poikkeus){
            // Tätä ei pitäisi juuri tulla käyttäjälle.
            $valikkohtml =  "Virhe varmuusvalikossa! (".$poikkeus->getMessage().")";
        }
        return $valikkohtml;
    }
}

class Bongaustunnisteet{
    public static $kuvaus_tallennapainike_id = "uaauaa";
}

/**
 * Havaintoihin voi liittää lisäluokituksia, jotta esimerkiksi ekopinnat on
 * helppo etisä.
 * 
 * Ainut hankaluus tässä on se, että luokasta pitää luoda olio, jotta
 * perittyihin metodeihin päästään käsiksi.
 * 
 * Huomautus: Tämä voisi olla parempi tehdä
 * kokonaan tietokantaan, jotta mahdolliset esimerkiksi poppoo-kohtaiset
 * lisäykset ja muokkaukset onnistuisivat. Toisaalta tällöin joudutaan kuitenkin 
 * myös koodia muokkaamaan, jotta uusia luokituksia osataan käsitellä. Ehkä tässä
 * vaiheessa on selkeämpi, että vaihtoehdot ovat kiinteästi koodattuja.
 * 
 * Tekstit on selkeästi erikseen, jotta kansainvälistys onnistuu tarvittaessa
 * helpommin.
 */
class Lisaluokitus_asetukset extends Asetuspohja{
    // Mahdolliset arvot:
    public static $piha = 1;    // Deprecated: korvattu vakipaikoilla!
    public static $vesilla = 2; 
    public static $tornien_taisto = 3; 
    public static $ekopinna = 4; //
    public static $ekopinna2 = 5; //
    public static $elis = 6; 
    public static $maaelis = 7;
    
    function __construct() {
        parent::__construct(array(
            new Asetus(Bongaustekstit::$aset_lisaluokitus_ekopinna_nimi, 
                        Lisaluokitus_asetukset::$ekopinna, 
                        Bongaustekstit::$aset_lisaluokitus_ekopinna_selitys),
            new Asetus(Bongaustekstit::$aset_lisaluokitus_ekopinna2_nimi, 
                        Lisaluokitus_asetukset::$ekopinna2, 
                        Bongaustekstit::$aset_lisaluokitus_ekopinna2_selitys),
            new Asetus(Bongaustekstit::$aset_lisaluokitus_vesilla_nimi, 
                        Lisaluokitus_asetukset::$vesilla, 
                        Bongaustekstit::$aset_lisaluokitus_vesilla_selitys),
            new Asetus(Bongaustekstit::$aset_lisaluokitus_elis_nimi, 
                        Lisaluokitus_asetukset::$elis, 
                        Bongaustekstit::$aset_lisaluokitus_elis_selitys),
            new Asetus(Bongaustekstit::$aset_lisaluokitus_maaelis_nimi, 
                        Lisaluokitus_asetukset::$maaelis, 
                        Bongaustekstit::$aset_lisaluokitus_maaelis_selitys),
            new Asetus(Bongaustekstit::$aset_lisaluokitus_tornitaisto_nimi, 
                        Lisaluokitus_asetukset::$tornien_taisto, 
                        Bongaustekstit::$aset_lisaluokitus_tornitaisto_selitys)));
    }
}

?>
