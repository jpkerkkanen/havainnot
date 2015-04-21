
//==============================================================================
//=================== Kirjautuminen ============================================
// Lähettää kirjautumistiedot:
function kirjaudu(id_ktunnuskentta, id_salasana, name_ktunnus, name_salis){
    try{
        var ktunnuskentta = document.getElementById(id_ktunnuskentta);
        var saliskentta = document.getElementById(id_salasana);
        var ktunnus = "";
        var salis = "";
        
        // Jos elementit löytyivät, haetaan niiden sisältö:
        if(ktunnuskentta && saliskentta){
            ktunnus = ktunnuskentta.value;
            salis = saliskentta.value;
        }
        
        var kysely = "kysymys=kirjaudu"+
                "&"+name_ktunnus+"="+ktunnus+
                "&"+name_salis+"="+salis;
        var nayta_viiveilmoitus = 0;
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_kirjautumisen_tulos','post', 'xml', nayta_viiveilmoitus);
                    
        //alert("Kysely: "+kysely);  // Toimii!

    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (kayttajahallinta.js/kirjaudu): "+virhe.description;
    }
}
/**
 * Viesti tulee seuraavassa muodossa:
    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
    echo '<tiedot>';
    echo '<ilmoitus>'.$ilmoitus.'</ilmoitus>';
    echo '<onnistuminen>'.$onnistuminen.'</onnistuminen>';
    echo '</tiedot>';
 * @param {type} tulosxml
 * @returns {undefined}
 */

function nayta_kirjautumisen_tulos(tulosxml){
    try{
        // Haetaan tiedot xml:stä:
        //alert("tulosxml: "+tulosxml);
        var onnistuminen =
        tulosxml.getElementsByTagName("onnistuminen")[0].childNodes[0].nodeValue;
        
        // Otetaan huomioon, ettei elementtiä välttämättä ole olemassa:
        var ilmoitus = "";
        var ilmoituselem =
            tulosxml.getElementsByTagName("ilmoitus")[0].childNodes[0];
        if(ilmoituselem){
            ilmoitus = ilmoituselem.nodeValue;
        }
        vie_ilmoitus(ilmoitus);
        
        // Vain onnistuessa ladataan pääsivu uudelleen. Huom! Alla pitää
        // olla lainausmerkit, koska tieto tulee merkkijonona!
        if(onnistuminen === "1"){
            siirra_hitaasti("index.php", 700);
        }
    }
    catch(virhe){
        var ilmoitus =
            "Virhe (kayttajahallinta.js/nayta_kirjautumisen_tulos): "+
            virhe.description;
        vie_ilmoitus(ilmoitus);
    }
}
//==============================================================================
//============================ henkilön tiedot alku ==========================//
function hae_henkilotiedot(id_henk, name_id_henk){
    try{
        var kysely = "kysymys=hae_poppoohenkilon_tiedot"+
                "&"+name_id_henk+"="+id_henk;
        var nayta_viiveilmoitus = 0;
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_henkilotiedot','post', 'xml', 
                    nayta_viiveilmoitus);
        //alert("kysely: "+kysely);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (kayttajahallinta.js/hae_henkilotiedot): "+
            virhe.description;
    }
}

/**
 * Hakee henkilötiedot niin, että muokkausoikeus annetaan. Toistoa on, mutta
 * tavallaan mukavampi erottaa adminhommat mahd. paljon.
 * @param {type} id_henk
 * @param {type} name_id_henk
 * @returns {undefined}
 */
function hae_henkilotiedot_admin(id_henk, name_id_henk){
    try{
        var kysely = "kysymys=hae_poppoohenkilon_tiedot_admin"+
                "&"+name_id_henk+"="+id_henk;
        var nayta_viiveilmoitus = 0;
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_henkilotiedot','post', 'xml', 
                    nayta_viiveilmoitus);
        //alert("kysely: "+kysely);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (kayttajahallinta.js/hae_henkilotiedot): "+
            virhe.description;
    }
}

function nayta_henkilotiedot(xml){
    var tiedot = hae_xml_elementin_sisalto(xml, "html", "-");   
    var elem_id = hae_xml_elementin_sisalto(xml, "elem_id", false);
    
    if(elem_id){
        // Alla 3. parametri kertoo, käytetäänkö elementti.value metodia
        // elementti.innerHTML metodin asemesta (syöttökentät).
        kirjoita_elementtiin(elem_id, tiedot, false);
    } else{
        vie_ilmoitus("Kohde-elementin id-arvoa ei havaittu!");
    }
}
//============================ henkilön tiedot loppui ========================//
//=================== Poppootoiminnot ==========================================
// Lähettää kirjautumistiedot:
function tarkista_poppootunnus(id_poppootunnuskentta, name_tunnus){
    try{
        var tunnuskentta = document.getElementById(id_poppootunnuskentta);
        var tunnus = "";
        
        // Jos elementit löytyivät, haetaan niiden sisältö:
        if(tunnuskentta){
            tunnus = tunnuskentta.value;
        }
        
        var kysely = "kysymys=tarkista_poppootunnus"+
                "&"+name_tunnus+"="+tunnus;
        var nayta_viiveilmoitus = 0;
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_poppootarkistuksen_tulos','post', 'xml', 
                    nayta_viiveilmoitus);
        //alert("kysely: "+kysely);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (kayttajahallinta.js/tarkista_poppootunnus): "+
            virhe.description;
    }
}
/**
 * header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
    echo '<tiedot>';
    echo '<ilmoitus>'.htmlspecialchars($ilmoitus).'</ilmoitus>';
    echo '<onnistuminen>'.$onnistuminen.'</onnistuminen>';
    echo '<lomakehtml>'.htmlspecialchars($lomakehtml).'</lomakehtml>';
    echo '<elem_id>'.$elem_id.'</elem_id>';
    echo '<id_kirjautumisdivi>'.$id_kirjautumisdivi.'</id_kirjautumisdivi>';
    echo '<id_oikea_palkki>'.$id_oikea_palkki.'</id_oikea_palkki>';
    echo '<html_oikea_palkki>'.$oikea_palkki_html.'</html_oikea_palkki>';
    echo '</tiedot>';
 * @param {type} tulosxml
 * @returns {undefined}
 */

function nayta_poppootarkistuksen_tulos(tulosxml){
    try{

        // Haetaan tiedot xml:stä:
        var onnistuminen =
        tulosxml.getElementsByTagName("onnistuminen")[0].childNodes[0].nodeValue;

        // lomake-elementin id:
        var elementin_id =
            tulosxml.getElementsByTagName("elem_id")[0].childNodes[0].nodeValue;

        var oikean_palkin_id =
            tulosxml.getElementsByTagName("id_oikea_palkki")[0].
                                        childNodes[0].nodeValue;

        // Kirjautumiselementin id:
        var kirjautumiselem_id =
            tulosxml.getElementsByTagName("id_kirjautumisdivi")[0].
                                        childNodes[0].nodeValue;
        //alert("kirjelemid="+kirjautumiselem_id);

        //alert("onnist="+onnistuminen);
        // Otetaan huomioon, ettei elementtiä välttämättä ole olemassa:
        var ilmoitus = "";
        var ilmoituselem =
            tulosxml.getElementsByTagName("ilmoitus")[0].childNodes[0];
        if(ilmoituselem){
            ilmoitus = ilmoituselem.nodeValue;
        }
        vie_ilmoitus(ilmoitus);

        // Vain onnistuessa näytetään lomake:
        if(onnistuminen === "1"){
            var lomakehtml = "";
            var lomakehtmlelem =
                tulosxml.getElementsByTagName("lomakehtml")[0].childNodes[0];
            if(lomakehtmlelem){
                lomakehtml = lomakehtmlelem.nodeValue;

            }
            document.getElementById(elementin_id).innerHTML = lomakehtml;
            
            
            // Samoin viedään poppoon jäsentaulukko:
            var poppootaulukko = "";
            var poppooelem =
                tulosxml.getElementsByTagName("html_oikea_palkki")[0].childNodes[0];
            if(poppooelem){
                poppootaulukko = poppooelem.nodeValue;
            }
            document.getElementById(oikean_palkin_id).innerHTML = poppootaulukko;
            
            // Tyhjennetään kirjautumisjutut näkyvistä:
            var k_elem = document.getElementById(kirjautumiselem_id);
            if(k_elem){
                k_elem.innerHTML = "";
            }
        }
    }
    catch(virhe){
        var ilmoitus =
            "Virhe (kayttajahallinta.js/nayta_poppootarkistuksen_tulos): "+
            virhe.description;
        vie_ilmoitus(ilmoitus);
    }
}

//============================ Poppootiedot alku ==========================//
function hae_poppootiedot(id_poppoo, name_id_poppoo){
    try{
        var kysely = "kysymys=hae_poppootiedot"+
                "&"+name_id_poppoo+"="+id_poppoo;
        var nayta_viiveilmoitus = 0;
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_poppootiedot','post', 'xml', 
                    nayta_viiveilmoitus);
        //alert("kysely: "+kysely);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (kayttajahallinta.js/hae_poppootiedot): "+
            virhe.description;
    }
}

function hae_poppootiedot_admin(id_poppoo, name_id_poppoo){
    try{
        var kysely = "kysymys=hae_poppootiedot_admin"+
                "&"+name_id_poppoo+"="+id_poppoo;
        var nayta_viiveilmoitus = 0;
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_poppootiedot','post', 'xml', 
                    nayta_viiveilmoitus);
        //alert("kysely: "+kysely);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (kayttajahallinta.js/hae_poppootiedot): "+
            virhe.description;
    }
}

function nayta_poppootiedot(xml){
    //alert(xml);
    var tiedot = hae_xml_elementin_sisalto(xml, "html", "-");   
    var nimet = hae_xml_elementin_sisalto(xml, "nimet", "-");   
    var elem_id = hae_xml_elementin_sisalto(xml, "elem_id", false);
    var elem2_id = hae_xml_elementin_sisalto(xml, "elem2_id", false);
    
    if(elem_id){
        // Alla 3. parametri kertoo, käytetäänkö elementti.value metodia
        // elementti.innerHTML metodin asemesta (syöttökentät).
        kirjoita_elementtiin(elem_id, tiedot, false);
    } else{
        vie_ilmoitus("Kohde-elementin id-arvoa ei havaittu!");
    }
    if(elem2_id){
        kirjoita_elementtiin(elem2_id, nimet, false);
    } else{
        vie_ilmoitus("Kohde-elementin (vasen palkki) id-arvoa ei havaittu!");
    }
}

//==============================================================================
//==============================================================================

// Yleinen metodi, joka yksinkertaisesti saa ilmoituksen näkymään käyttäjälle.
// Ilmoituksen sisältö on parametrina annettu merkkijono.
function vie_ilmoitus(ilmoitus){
    document.getElementById('ilmoitus').innerHTML = ilmoitus;
}

