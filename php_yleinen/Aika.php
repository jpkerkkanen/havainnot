<?php

// Tämä vaaditaan alkaen MySQL5.1:
date_default_timezone_set('Europe/Helsinki');

/**
 * Tämä luokka sisältää staattisia metodeita, joiden avulla voidaan
 * käsitellä ja muokata päivämääriä ja aikoja.
 */
class Aika {

    /** Näyttää nykyisen päivän numeron (kuukauden päivä) */
    static function anna_nyk_paivan_nro() {
        $pvm_taulukko = getdate(time());
        return $pvm_taulukko['mday'];
    }

    /*     * Näyttää nykyisen kuukauden numeron */

    static function anna_nyk_kk_nro() {
        $pvm_taulukko = getdate(time());
        return $pvm_taulukko['mon'];
    }

    /** Näyttää nykyisen vuoden numeron */
    static function anna_nyk_vuoden_nro() {
        $pvm_taulukko = getdate(time());
        return $pvm_taulukko['year'];
    }

// Näyttää nykyisen päivän suomeksi
    static function anna_nyk_viikonp_suomeksi() {
        $pvm_taulukko = getdate(time());
        $pv_suom = 'juu';

        switch ($pvm_taulukko['wday']) {
            case "1":
                $pv_suom = "maanantai";
                break;
            case "2":
                $pv_suom = "tiistai";
                break;
            case "3":
                $pv_suom = "keskiviikko";
                break;
            case "4":
                $pv_suom = "torstai";
                break;
            case "5":
                $pv_suom = "perjantai";
                break;
            case "6":
                $pv_suom = "lauantai";
                break;
            case "0":
                $pv_suom = "sunnuntai";
                break;
        }
        return $pv_suom;
    }

// Näyttää nykyisen päivän suomeksi
    static function anna_nyk_viikonp_suomeksi_lyhyt() {
        $pvm_taulukko = getdate(time());
        $pv_suom = 'juu';

        switch ($pvm_taulukko['wday']) {
            case "1":
                $pv_suom = "ma";
                break;
            case "2":
                $pv_suom = "ti";
                break;
            case "3":
                $pv_suom = "ke";
                break;
            case "4":
                $pv_suom = "to";
                break;
            case "5":
                $pv_suom = "pe";
                break;
            case "6":
                $pv_suom = "la";
                break;
            case "0":
                $pv_suom = "su";
                break;
        }
        return $pv_suom;
    }

    /**
     *
     * @param <type> $paiva päivän luku (1-31)
     * @param <type> $kk kuukauden arvo (1-12)
     * @param <type> $vuosi vuosiluku
     * @param <type> $lyhyt_muoto totuusarvo: jos true, niin näytetään viikonpäivien
     * lyhyet muodot, muuten pitkät.
     * @return <type> Palauttaa viikonpäivän nimen tai tyhjän merkkijonon,
     * jos jokin menee vinoon.
     */
    static function anna_viikonp_suomeksi($paiva, $kk, $vuosi, $lyhyt_muoto) {
        $aika_unix = mktime(0, 0, 0, $kk, $paiva, $vuosi); // False, jos huonot parametrit.

        $pvm_taulukko = array();

        if ($aika_unix != false) {
            $pvm_taulukko = getdate($aika_unix);
        }

        $pv_suom = '';

        if ($lyhyt_muoto) {
            switch ($pvm_taulukko['wday']) {
                case "1":
                    $pv_suom = "ma";
                    break;
                case "2":
                    $pv_suom = "ti";
                    break;
                case "3":
                    $pv_suom = "ke";
                    break;
                case "4":
                    $pv_suom = "to";
                    break;
                case "5":
                    $pv_suom = "pe";
                    break;
                case "6":
                    $pv_suom = "la";
                    break;
                case "0":
                    $pv_suom = "su";
                    break;
            }
        } else {
            switch ($pvm_taulukko['wday']) {
                case "1":
                    $pv_suom = "maanantai";
                    break;
                case "2":
                    $pv_suom = "tiistai";
                    break;
                case "3":
                    $pv_suom = "keskiviikko";
                    break;
                case "4":
                    $pv_suom = "torstai";
                    break;
                case "5":
                    $pv_suom = "perjantai";
                    break;
                case "6":
                    $pv_suom = "lauantai";
                    break;
                case "0":
                    $pv_suom = "sunnuntai";
                    break;
            }
        }
        return $pv_suom;
    }

    /**
     * Palauttaa pvm:n ja ajan, jotka saadaan parametrista, joka on
     * time()-funktion lähetyshetkenä antama arvo sekunteina vuoden 1970 alusta.
     * @param int $timestamp time()-funktion palauttama arvo (sekuntien määrä).
     * @return String Palauttaa html-tekstinä viikonpäivänä, päivämäärän ja
     * kellonajan.
     */
    static function anna_pvm_ja_aika($timestamp) {
        $aikataul = getdate($timestamp);

        // Viikonpäivä:
        $tuloshtml = Aika::anna_viikonp_suomeksi($aikataul['mday'], 
                                            $aikataul['mon'], 
                                            $aikataul['year'], 
                                            true);
        //pvm:
        $tuloshtml .= date(" j.n.Y \k\l\o H:i:s", $timestamp);


        return $tuloshtml;
    }

}

?>
