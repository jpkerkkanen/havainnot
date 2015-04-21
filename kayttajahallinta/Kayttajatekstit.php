<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kayttajatekstit
 *
 * @author J-P
 */
class Kayttajatekstit {
    
    //=========================== Painiketekstit ===============================
    public static $nappi_kirjaudu_value = "Kirjaudu";
    public static $nappi_kirjaudu_ulos_value = "Kirjaudu ulos";
    public static $nappi_kirjaudu_ulos_title = "Kirjaudu ulos sivustosta";
    public static $nappi_rekisteroidy_value = "Ilmoittaudu";
    public static $nappi_rekisteroidy_title = "Ilmoittaudu j&auml;seneksi";
    
    public static $nappi_admin_value = "Sivuston hallinta";
    public static $nappi_admin_title = "Sivuston hallinta";
    
    public static $nappi_admin_muokkaa_henkilo_value = "Muokkaa tietoja";
    
    public static $nappi_koti_value = "Kotiin";
    public static $nappi_koti_title = "Sivuston p&auml;&auml;sivulle";
    
    public static $nappi_omat_tiedot_value = "Omat tiedot";
    public static $nappi_omat_tiedot_title = 
            "Voit katsella ja muokata omia tietojasi";
    
    public static $nappi_poppootiedot_value = "Oman poppoon tiedot";
    public static $nappi_poppootiedot_title = 
            "Voit tarkastella oman poppoon j&auml;senten tietoja";
    
    public static $nappi_nayta_henkilotiedot_value = "Tiedot";
    public static $nappi_nayta_henkilotiedot_title = 
            "Voit katsella henkil&ouml;n tietoja";
    
    public static $nappi_tallenna_tietomuutokset_value = "Tallenna muutokset";
    public static $nappi_tallenna_tietomuutokset_title = 
            "Tallentaa muutetut tiedot. J&auml;t&auml; tunnukset tyhjiksi, ellet halua muuttaa niit&auml;";
    
    public static $adminnappi_tallenna_tietomuutokset_value = "Tallenna muutokset";
    public static $adminnappi_tallenna_tietomuutokset_title = 
            "Tallentaa muutetut tiedot. J&auml;t&auml; tunnukset tyhjiksi, ellet halua muuttaa niit&auml;";
    
    public static $nappi_poistu_tiedoista_value = "Poistu tiedoista";
    public static $nappi_poistu_tiedoista_title = 
            "Palaa takaisin tallentamatta tallentamattomia muutoksia.";
    
    public static $adminnappi_poistu_tiedoista_value = "Poistu tiedoista";
    public static $adminnappi_poistu_tiedoista_title = 
            "Palaa takaisin tallentamatta tallentamattomia muutoksia.";
    
    public static $nappi_henkilo_tallenna_uusi_value = "Tallenna";
    public static $nappi_henkilo_tallenna_uusi_title = 
            "Tallentaa poppoon uuden jäsenen tiedot tietokantaan.";
    public static $nappi_henkilo_peru_lisaaminen_value = "Peru tallentamatta";
    
    public static $nappi_henkilo_nayta_valtuudet_value = "Nayta valtuudet";
    public static $nappi_henkilo_muokkaa_valtuudet_value = "Muokkaa valtuuksia";
    public static $nappi_henkilo_poistu_valtuuksista_value = 
            "Poistu valtuustiedoista";
    public static $nappi_henkilo_poistu_valtuuksien_muutoksista_value = 
            "Peru valtuusmuutokset";
    public static $nappi_henkilo_tallenna_valtuuksien_muutokset_value = 
            "Tallenna valtuuksien muutokset";
    
    public static $nappi_henkilo_poista_poppoosta_value = 
            "Poista poppoosta";
    public static $nappi_henkilo_poista_poppoosta_title = 
            "Poistaa poppoosta, mutta henkilön tiedot ja 
            merkinn&auml;t s&auml;ilyv&auml;t";
    
    public static $nappi_henkilo_poista_value = 
            "Poista henkilö";
    
    public static $nappi_henkilo_poista_title = 
            "Poistaa henkilön tiedot ja 
            merkinn&auml;t lopullisesti";
    
    public static $nappi_ad_muokkaa_poppoon_jasen_title = 
            "Klikkaamalla p&auml;&auml;set tarkastelemaan ja muokkaamaan tietoja";
    
    
    
    public static $nappi_poppoo_jatka_value = "Jatka";
    public static $nappi_poppoo_palaa_value = "Palaa takaisin";
    public static $nappi_poppoo_luo_uusi_value = "Luo uusi poppoo";
    public static $nappi_poppoo_muokkaa_value = "Muokkaa poppoota";
    public static $nappi_poppoo_tallenna_muokkaus_value = "Tallenna muokatut tiedot";
    public static $nappi_poppoo_tallenna_uusi_value = "Tallenna uusi poppoo";
    public static $nappi_poppoo_poista_value = "Poista poppoo";
    public static $nappi_poppoo_poista_title = "Poistaa poppoon. Miten käy ihmisille?";
    public static $nappi_poppoo_poistu_value = "Poistu tallentamatta";
    public static $nappi_poppoo_poistu_title = 
            "Palaa takaisin perusn&auml;kym&auml;&auml;n. Muutokset eiv&auml;t tallennu.";
    
    //================= lomakekenttätekstejä ====================================
    public static $lomakekentta_kayttajatunnus = "K&auml;ytt&auml;j&auml;tunnus";
    public static $lomakekentta_salasana = "Salasana";
    public static $lomakekentta_salasana_vahvistus = "Vahvista salasana";
    public static $lomakekentta_etunimi = "Etunimi";
    public static $lomakekentta_sukunimi = "Sukunimi";
    public static $lomakekentta_nimi = "Nimi";
    public static $lomakekentta_muokattavan_nimi = "Muokattavan nimi";  // Admin
    
    public static $lomakekentta_svuosi = "Syntym&auml;vuosi";
    public static $lomakekentta_skk = "Kuukausi";
    public static $lomakekentta_spaiva = "P&auml;iv&auml;";
    public static $lomakekentta_asuinmaa = "Asuinmaa";
    public static $lomakekentta_asuinmaa_title = "Nykyinen asuinmaa";
    public static $lomakekentta_kieli = "Kieli";
    public static $lomakekentta_kieli_title = 
            "Kieli, jota haluat k&auml;ytt&auml;&auml;";
    public static $lomakekentta_poppoo = "Poppoo";
    public static $lomakekentta_poppoo_title = 
            "T&auml;st&auml; voi henkil&ouml;n siirt&auml;&auml; toiseen poppooseen";
    public static $lomakekentta_email = "S&auml;hk&ouml;posti";
    public static $lomakekentta_osoite = "Osoite";
    
    public static $lomakekentta_puhelin = "Puhelin";
    public static $lomakekentta_lempinimi = "Lempinimi";
    public static $lomakekentta_kuvaus_itsesta = "Kuvaus itsest&auml;";
    
    public static $poppoolomake_ktunnus = "Poppootunnus";
    public static $poppoolomake_ktunnusvahvistus = "Poppootunnusvahvistus";
    public static $poppoolomake_nimi = "Poppoon nimi";
    public static $poppoolomake_koko = "Koko (j&auml;senten maksimilkm)";
    public static $poppoolomake_kommentti = "Kommentti";
    public static $poppoon_jasenet = "poppoon nykyiset j&auml;senet";
    public static $Poppoon_jasenet = "Poppoon j&auml;senet";
    
    public static function henkilolomakeotsikko_muokkaus(){ 
        $mj = "T&auml;&auml;ll&auml; voit muokata tietojasi. ".
            "K&auml;ytt&auml;j&auml;tunnus ja salasana s&auml;ilyv&auml;t
            entisell&auml;&auml;n, jos ne j&auml;tet&auml;&auml;n tyhjiksi.";
        
        return $mj;
    }
    public static function henkilolomakeotsikko_uusi(){ 
        $mj = "Jos haluat mukaan poppooseen, kirjoita tietosi kenttiin ".
                "ja paina Tallenna-nappia.".
            " Pakolliset kent&auml;t on merkitty t&auml;hdell&auml;.";
        
        return $mj;
    }
    public static function admin_henkilolomakeotsikko_muokkaus(){ 
        $mj = "T&auml;&auml;ll&auml; voi yll&auml;pit&auml;j&auml; muokata henkil&ouml;n tietoja. ".
            "K&auml;ytt&auml;j&auml;tunnus ja salasana s&auml;ilyv&auml;t
            entisell&auml;&auml;n, jos ne j&auml;tet&auml;&auml;n tyhjiksi.";
        
        return $mj;
    }
    
    public static function henkilolomake_nakyvyysselitys(){ 
        $mj = "Huomaa, ett&auml; tietosi n&auml;kyv&auml;t t&auml;m&auml;n ".
            "poppoon j&auml;senille, mutta eiv&auml;t muille. ".
            "K&auml;ytt&auml;j&auml;tunnusta ja salasanaa ".
            "eiv&auml;t muut k&auml;ytt&auml;j&auml;t n&auml;e lainkaan".
            " (poikkeuksena sivuston yll&auml;pit&auml;j&auml;, joka pystyy".
            " saamaan selville k&auml;ytt&auml;j&auml;tunnuksen, ".
            "muttei salasanaa).";
        
        return $mj;
    }
    
    public static function poppoolomakeotsikko_muokkaus(){ 
        $mj = "T&auml;&auml;ll&auml; voit muokata poppoon tietoja. ".
            "K&auml;ytt&auml;j&auml;tunnus s&auml;ilyy
            entisell&auml;&auml;n, jos se j&auml;tet&auml;&auml;n tyhjäksi.";
        
        return $mj;
    }
    public static function poppoolomakeotsikko_uusi(){ 
        $mj = "Kirjoita uuden poppoon tiedot kenttiin ".
                "ja paina Tallenna-nappia.".
            " Pakolliset kent&auml;t on merkitty t&auml;hdell&auml;.";
        
        return $mj;
    }
    
    public static $lomakekentta_uusi_kayttaja = "Uusi k&auml;ytt&auml;j&auml;";
    
    // Henkilötietolomakkeen virheilmoitukset:
    public static $henkilolomake_jokin_pakollisista_kentista_tyhja =
            "Jokin pakollisista kentist&auml; on tyhj&auml;!";
    public static $henkilolomake_syntymavuosi_ei_ole_oikein =
            "Syntym&auml;vuosi ei ole oikein!";
    public static $henkilolomake_kuukausi_ei_ole_oikein =
            "Kuukausi ei ole oikein!";
    public static $henkilolomake_paiva_ei_ole_oikein =
            "P&auml;iv&auml; ei ole oikein!";
    //*========================================================================
    
    //==========================================================================
    // Tunnuksiin liittyvät virheilmoitukset:
    public static $tunnus_jo_kaytossa = 
                "K&auml;ytt&auml;j&auml;tunnus on jo k&auml;yt&ouml;ss&auml;!";
    public static $tunnus_poppoon_jo_kaytossa = 
                "Poppootunnus on jo k&auml;yt&ouml;ss&auml;!";
    public static $tunnus_pituus_vaara = "Tunnus on liian lyhyt tai pitk&auml;!";
    public static $tunnus_merkkivirhe = 
        "Tunnus sis&auml;lt&auml;&auml; v&auml;&auml;ri&auml; merkkej&auml;!";
    public static $tunnus_pituus_tai_merkkivirhe = 
        "Virhe merkkijonon pituudessa tai merkeiss&auml;!";
    public static $tunnus_salasana_pituus_tai_merkkivirhe = 
        "Virhe salasanan pituudessa tai merkeiss&auml;!";
    public static $tunnus_kayttajatunnus_pituus_tai_merkkivirhe = 
        "Virhe k&auml;ytt&auml;j&auml;tunnuksen pituudessa tai merkeiss&auml;!";
    public static $tunnus_poppoo_pituus_tai_merkkivirhe = 
        "Virhe poppootunnuksen pituudessa tai merkeiss&auml;!";
    public static $tunnus_vain_seuraavat_merkit_sopivat=
        "Vain seuraavat merkit k&auml;yv&auml;t";
    public static $tunnus_kayttajatunnuksen_min_pituus_on=
        "K&auml;ytt&auml;j&auml;tunnuksen minimipituus on";
    public static $tunnus_poppootunnuksen_min_pituus_on=
        "Poppootunnuksen minimipituus on";
    public static $tunnus_salasanan_min_pituus_on=
        "Salasanan minimipituus on";
    public static $tunnus_kayttajatunnuksen_max_pituus_on=
        "K&auml;ytt&auml;j&auml;tunnuksen maksimipituus on";
    public static $tunnus_poppootunnuksen_max_pituus_on=
        "Poppootunnuksen maksimipituus on";
    public static $tunnus_salasanan_max_pituus_on=
        "Salasanan maksimipituus on";
    public static $tunnus_vahvistus_ei_tasmaa = 
        "Salasana ja sen vahvistus ovat erilaiset!";
    
    public static $virhe_samoilla_tunnuksilla_monta_kayttajaa = 
        "Virhe: samoilla tunnuksilla monta k&auml;ytt&auml;j&auml;&auml;!";
    
    //==========================================================================
    public static $henkilotiedot_otsikko =
            "Henkil&ouml;n tiedot";
    
    //========================== Muita =========================================
    
    public static $ilmoitus_yllapitajalta = 
        "";
    
    public static $ilmoitus_sessio_vanhentunut = 
        "Sessio on vanhentunut. Kirjaudu uudelleen, kiitos!";
    
    public static $ilmoitus_olet_kirjautunut_ulos = 
        "Olet kirjautunut ulos. Kirjaudu uudelleen, kiitos!";
    
    public static $ilmoitus_Olet_jo_kirjautunut = 
        "Olet jo kirjautunut!";
    
    public static $ilmoitus_et_ole_kirjautunut = 
        "Ei ole kirjautunut. Voit kirjautua, jos sinulla on tunnukset.";
    
    public static $ilmoitus_Kirjautunut = 
        "Kirjautunut";
    
    public static $ilmoitus_yllapitaja = 
        "sivuston yll&auml;pit&auml;j&auml;";
    
    public static $ilmoitus_yllapitajan_alue = 
        "Yll&auml;pit&auml;j&auml;n toiminta-alue!";
    
    public static $ilmoitus_poppoon_johtaja = 
        "poppoon johtaja";
    
    public static $ilmoitus_Poppoo = 
        "Poppoo";
    
    public static $ilmoitus_tunnukset_ei_kaytossa = 
            "Tunnukset eiv&auml;t ole k&auml;yt&ouml;ss&auml;!";
    
    public static $ilmoitus_tervetuloa = 
            "Tervetuloa";
    
    // Seuraavat kaksi liittyvät uloskirjautumiseen.
    public static $ilmoitus_Hei_hei = 
            "Heippa ";
    
    public static $ilmoitus_ja_tervetuloa_uudelleen = 
            "ja tervetuloa uudelleen!";
    
    public static $ilmoitus_tietojen_muokkaustallennus_ok = 
            "Tietomuutosten tallennus onnistui!";
    public static $virheilmoitus_tietojen_muokkaustallennus_ei_ok = 
            "Virhe tietomuutosten tallennuksessa!";
    
    public static $ilmoitus_uuden_henkilon_tallennus_ok = 
            "Tietojen tallennus onnistui!";
    
    public static $ilmoitus_uuden_henkilon_tallennus_ei_ok = 
            "Virhe uuden ihmisen tietojen tallennuksessa!";
    
    
    
    //================ poppoo-ilmoitukset=======================================
    public static $poppooilmoitus_tunnusta_ei_loytynyt = 
            "Tunnusta ei l&ouml;ytynyt!";
    
    public static $poppooilmoitus_tyhja_poppoo = 
            "Poppoossa ei ole yht&auml;&auml;n j&auml;sent&auml;";
    
    public static $poppooilmoitus_ei_loytynyt = 
            "Poppoota ei l&ouml;ytynyt! Tarkista sessio-id!";
    
    public static $poppooilmoitus_tunnus_ok_rekisteroidy = 
            "Tunnus ok! Anna tiedot ja tallenna.";
    
    public static $poppooilmoitus_uuden_poppoon_tallennus_ok = 
            "Poppoon tallennus onnistui!";
    
    public static $poppoovirheilmoitus_uuden_henkilon_tallennus_ei_ok = 
            "Virhe uuden poppoon tietojen tallennuksessa!";
    public static $poppooilmoitus_muokkauksen_tallennus_ok = 
            "Poppootietojen muutosten tallennus onnistui!";
    
    // Poppoolomakkeen virheilmoitukset:
    public static $poppoolomake_jokin_pakollisista_kentista_tyhja =
            "Jokin pakollisista kentist&auml; on tyhj&auml;!";
    public static $poppoolomake_koko_ei_ole_oikein =
            "Maksimikoko ei ole oikein!";
    
    public static $poppoolomake_koko_on_luku_valilta =
            "Koon tulee olla luku v&auml;lilt&auml; ";
    
    public static $poppoovirheilmoitus_muokkauksen_tallennus_ei_ok = 
            "Virhe poppootietojen muutosten tallennuksessa!";
    public static $poppoovirheilmoitus_tunnus_vahvistus_ei_tasmaa = 
        "Poppootunnus ja sen vahvistus ovat erilaiset!";
    
    
    public static $poppootiedot_otsikko = 
            "Alla kaikkien poppoiden tiedot taulukossa:";
    public static $poppootiedot_havainnot = 
            "Havainnot (lkm)";
    public static $poppootiedot_luomispvm = 
            "Luotu (pvm)";
    public static $poppootiedot_toiminnot = 
            "Toiminnot";
    public static $poppootiedot_koko = 
            "J&auml;seni&auml; / max";
    //============== VIRHEILMOITUSET ===========================================
    public static $virheilmoitus_uloskirjautuminen_epaonnistui = 
            "Virhe uloskirjautumisessa!";
    public static $virheilmoitus_henkiloa_ei_loytynyt = 
            "Henkil&ouml;&auml; ei l&ouml;ytynyt!";
    
}

?>
