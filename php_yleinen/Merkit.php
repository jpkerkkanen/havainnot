<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Merkit
 *
 * @author kerkjuk_admin
 */
class Merkit {
    // Suorat lainausmerkit ovat varattuja html:ssä!
    public static $lainausmerkki_yks = "&#39;"; // VARATTU MERKKI (')!
    public static $lainausmerkki_kaks = "&#34;"; // VARATTU MERKKI (")!

    /* Tekstissä olevat lainausmerkit voi kirjoittaa vinoina, jolloin ei
     * ongelmaa html-kääntäjän kanssa: */
    public static $lainausmerkkiVINO_yks = "&rsquo;";
    public static $lainausmerkkiVINO_kaks = "&rdquo;";

    // Nuolet:
    public static $nuoli_oik = "&#8594"; //rightwards arrow	&rarr;
    public static $nuoli_vas = "&#8592"; //leftwards arrow	&larr;
    public static $nuoli_ylos = "&#8593"; //upwards arrow	&uarr;
    public static $nuoli_alas = "&#8595"; //downwards arrow	&darr;
    
    // Kaksoisnuolet:
    public static $nuoli_tupla_oik_seuraus = "&#8658;"; //rightwards double arrow &rArr;	
    public static $nuoli_ekvivalenssi = "&#8660;"; //left right double arrow &hArr;	
    
    /* Matemaattiset ja loogiset symbolit: */
    public static $kaikille  = "&#8704;";    // &forall;
    public static $on_olemassa  = "&#8707;";    // &exist;
    public static $kuuluu_joukkoon = "&#8712;"; //&isin;
    public static $ei_kuulu_joukkoon = "&#8713;"; //&notin;	
    public static $summamerkki = "&#8721;"; //&sum;
    public static $aareton= "&#8734;"; //&infin;            
    public static $joukkojen_yhdiste= "&#8746;"; //&cup;
    public static $integraali= "&#8747;"; //&int;
    public static $erisuuri= "&#8800;"; //&ne;
    public static $pienempi_tai_yhtasuuri= "&#8804;"; //&le;
    public static $suurempi_tai_yhtasuuri= "&#8805;"; //&ge;	
    public static $kohtisuorassa= "&perp;"; //&perp;
    
    public static $kertomerkki = "&sdot;";
    public static $miinusmerkki = "&#8722;"; /* ajatusviiva (virallinen) */
    public static $likiarvo = "&#8776;";     // ≈ &asymp;almost equal
    
    //public static $neliojuuri = "&#8730;";     // radical sign
    public static $neliojuuri = "&radic;"; 
    
    // Heksa (&#x2551) vaatii x:n, mutta ei toiminut IE:n painikkeissa
    public static $tuplapystyviiva = "&#9553;";

    public static $vasen_nuoli = "&lt;";
    public static $oikea_nuoli = "&gt;";
    public static $et_merkki = "&amp;";

    // Kreikkalaiset kirjaimet:
    // Isot:
    public static $Alpha_iso = "&#913;";    //&Alpha;Α
    public static $Beta_iso = "&#914;";    //&Beta;    Β
    public static $Gamma_iso = "&#915;";    //&Gamma;    Γ
    public static $delta_iso = "&#916;";        //&#916;";    // Δ
    public static $Epsilon_iso = "&#917;";    //&Epsilon;    Ε
    public static $Zeta_iso = "&#918;";    //&Zeta;    Ζ
    public static $Eta_iso = "&#919;";    //&Eta;    Η
    public static $Theta_iso = "&#920;";    //&Theta;    Θ
    public static $Iota_iso = "&#921;";    //&Iota;    Ι
    public static $Kappa_iso = "&#922;";    //&Kappa;    Κ
    public static $Lambda_iso = "&#923;";    //&Lambda;    Λ
    public static $Mu_iso = "&#924;";    //&Mu;    Μ
    public static $Nu_iso = "&#925;";    //&Nu;    Ν
    public static $Xi_iso = "&#926;";    //&Xi;    Ξ
    public static $Omicron_iso = "&#927;";    //&Omicron;    Ο
    public static $Pi_iso = "&#928;";    //&Pi;    Π
    public static $Rho_iso = "&#929;";    //&Rho;    Ρ
    public static $Sigma_iso = "&#931;";    //&Sigma;    Σ
    public static $Tau_iso = "&#932;";    //&Tau;    Τ
    public static $Upsilon_iso = "&#933;";    //&Upsilon;    Υ
    public static $Phi_iso = "&#934;";    //&Phi;    Φ
    public static $Chi_iso = "&#935;";    //&Chi;    Χ
    public static $Psi_iso = "&#936;";    //&Psi;    Ψ
    public static $Omega_iso = "&#937;";    //&Omega;    Ω
         
    //pienet:
    public static $alpha_pieni = "&#945;";    //&alpha;    α
    public static $beta_pieni = "&#946;";    //&beta;    β
    public static $gamma_pieni = "&#947;";    //&gamma;    γ
    public static $delta_pieni = "&#948;";    //&delta;    δ
    public static $epsilon_pieni = "&#949;";    //&epsilon;    ε
    public static $zeta_pieni = "&#950;";    //&zeta;    ζ
    public static $eta_pieni = "&#951;";    //&eta;    η
    public static $theta_pieni = "&#952;";    //&theta;    θ
    public static $iota_pieni = "&#953;";    //&iota;    ι
    public static $kappa_pieni = "&#954;";    //&kappa;    κ
    public static $lambda_pieni = "&#955;";    //&lambda;    λ
    public static $mu_pieni = "&#956;";    //&mu;    μ
    public static $nu_pieni = "&#957;";    //&nu;    ν
    public static $xi_pieni = "&#958;";    //&xi;    ξ
    public static $omicron_pieni = "&#959;";    //&omicron;    ο
    public static $pi_pieni = "&#960;";    //&pi;    π
    public static $rho_pieni = "&#961;";    //&rho;    ρ
    public static $sigmaf_pieni = "&#962;";    //&sigmaf;    ς
    public static $sigma_pieni = "&#963;";    //&sigma;    σ
    public static $tau_pieni = "&#964;";    //&tau;    τ
    public static $upsilon_pieni = "&#965;";    //&upsilon;    υ
    public static $phi_pieni = "&#966;";    //&phi;    φ
    public static $chi_pieni = "&#967;";    //&chi;    χ
    public static $psi_pieni = "&#968;";    //&psi;    ψ
    public static $omega_pieni = "&#969;";    //&omega;    ω


}
?>
