var ajaxkyselytiedosto_osoite = "ajax_ja_js/ajax_kyselyt.php";

// Tämä tekee muutamia pikatarkistuksia lisäluokituksiin. Esimerkiksi jos
// elis-kohta valitaan, valitsee tämä automaattisesti myös maaeliksen (loogista).
function tarkista_lisaluokitusvalinnat(class_arvo){
    var valintaboxit = document.getElementsByClassName(class_arvo);
    
    // Jos kotipiha on valittu, valitaan myös eko ja eko2:
    if(valintaboxit[0].checked){
        valintaboxit[1].checked="checked";
        valintaboxit[2].checked="checked";
        
    } else if(valintaboxit[1].checked){ // eko -> eko2
        valintaboxit[2].checked="checked";
    }
    
    // Elis -> maaelis
    if(valintaboxit[4].checked){
        valintaboxit[5].checked="checked";
    } 
    
    /*for (i=0;i<valintaboxit.length;i++){
        if(valintaboxit[i].value === value_arvo){
            if (valintaboxit[i].checked){
                alert("Ruutu arvoltaan "+valintaboxit[i].value+" on valittu!");
            } else{
                alert("Ruutu arvoltaan "+valintaboxit[i].value+" on poisvalittu!");
            }
        }
        
    }*/
}

// // Säätää havaintotaulukon leveyttä piilottamalla tai tuomalla näkyviin
// kommentit.
function vaihda_kommenttinakyvyys(elementin_name){
    var elementtitaulukko = document.getElementsByName(elementin_name);
    var elementti;
    var piilotus = true;    // Piilotus/esiin tuonti

    for(var i = 0; i < elementtitaulukko.length; i++){
        elementti = elementtitaulukko[i];
        
        // Eka kerralla tarkistetaan suunta.
        if(i === 0){
            if(elementti.style.display === "none"){
                piilotus = false;
            }
        }

        if(piilotus){
            elementti.style.display = "none";
        }
        else{
            elementti.style.display = "table-cell";
        }
    }
    if(piilotus){
        document.getElementById("piilotusnappi").childNodes[0].nodeValue=
            "Levennä";
    }
    else{
        document.getElementById("piilotusnappi").childNodes[0].nodeValue=
            "Kavenna";
    }
}

// Toimiiko?
function korosta_elementti(id){
    elementti = document.getElementById(id);
    if(elementti){
        elementti.style.color = "red";
    }
}

// Tätä kutsutaan ladatessa. Kutsuu tarvittavia metodeita.
function kaynnista_bongausmetodit()
{
    // haetaan ihan aluksi ikkunan leveys:
    vie_muistiin_ikkunan_leveys();
    //kaynnista_kello_lm();
    tarkkaile_ilmoituksia("ilmoitus");
    
    // Tämä saa käyttäjätunnuskentän aktiiviseksi, niin ei tartte klikata ensin:
    //document.forms[0].ktunnus.focus();
    var kkkentta = document.getElementById('ktunnus_id');
    if(kkkentta){
        kkkentta.focus();
    }
    
}

// Tämä on vielä toteuttamatta. Toiminto toimii melkein jo itsessään, mutta
// kyllä tällä voisi paremman saada. Erityisesti pienillä laitteilla auttaisi.
function muokkaa_havaintolomake(havaintolomake_id,
                                lajivalintarivi_id,
                                lajivalikko_id,
                                lajipainike_id,
                                ylaluokka_id){
    /*try{
        var valinta = document.getElementById(havaintolomake_id);
        if(valinta != undefined){
            //alert("Moi! Avasit näemmä havaintolomakkeen!");

            var lajivalintarivi = document.getElementById(lajivalintarivi_id);
            if(lajivalintarivi != undefined){
                sis = lajivalintarivi.innerHTML;

                // Lisätään tekstikenttä:
                kentta = "<input type='text' size='15' "+
                            "value=''"+
                            "onkeyup='etsi_laji(this.value,"+ylaluokka_id+")'/>";
                        
                lajivalintarivi.innerHTML = kentta+sis;
            }
            else{
                alert("Lajivalintarivi undefined!");
            }
        }
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
                "Virhe (bongausmetodit.js/muokkaa_havaintolomake): "
                +virhe.description;
    }*/
}

// Lähettää kyselyn koskien lajiluokkaa, joka täsmää kirjoitetun alukkeen kanssa.
function etsi_laji(alkumj, ylaluokka_id){
    try{
        kysely = "kysymys=etsi_laji&ylaluokka_id_lj="+ylaluokka_id+"&alkumj="+alkumj;
        nayta_viiveilmoitus = 0;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_lajit','post', 'text', nayta_viiveilmoitus);
                    
        //alert("Kirjoitit: "+alkumj);  // Toimii!

    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/etsi_laji): "+virhe.description;
    }
}

function nayta_lajit(html){
    try{
        document.getElementById('uudet_kuvat_lkm').innerHTML =
            "("+tuloshtml+" uutta)";

    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/nayta_lajit): "+virhe.description;
    }
}

/******************************************************************************/
/* Tässä haetaan erityisesti puolivuotiskaudet, eli vain havaitut eri lajit: */
function hae_henkilon_bongauslajit(henk_id, puolivuotiskausi, havaintoalue){
    try{
        kysely = "kysymys=nayta_henkilon_bongauslajit"+
                "&henkilo_id="+henk_id+
                "&puolivuotkaudnum_hav="+puolivuotiskausi+
                "&havaintoalue_hav="+havaintoalue;
        nayta_viiveilmoitus = 1;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_havainnot_left','post', 'text', nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_henkilon_bongauslajit): "+virhe.description;
    }
}
/* Tässä haetaan erityisesti vuosipinnat, eli vain havaitut eri lajit. Lisäluokitukset
 * otetaan huomioon täällä! */
function hae_henkilon_pinnalajit(henk_id, vuosi, havaintoalue, lisaluok_arvo, lisaluok_name){
    try{
        kysely = "kysymys=nayta_henkilon_pinnalajit"+
                "&henkilo_id="+henk_id+
                "&vuosi_hav="+vuosi+
                "&havaintoalue_hav="+havaintoalue+
                "&"+lisaluok_name+"="+lisaluok_arvo;
        nayta_viiveilmoitus = 1;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_havainnot_left','post', 'text', nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_henkilon_bongauslajit): "+virhe.description;
    }
}

function hae_henkilon_havainnot(henk_id){
    try{
        kysely = "kysymys=nayta_henkilon_havainnot&"+
                "henkilo_id="+henk_id;
        nayta_viiveilmoitus = 0;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_havainnot','post', 'text', nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_henkilon_havainnot): "+virhe.description;
    }
}
function nayta_havainnot(html){
    try{
        //alert("havainnot: "+html);
        if(html === ""){
            html = "Ajax palautti tyhj&auml;n!";
        }
        document.getElementById('havaintotietolaatikko').innerHTML = html;
        var laatikko = document.getElementById('havaintotietolaatikko');
        laatikko.style.padding = "5px";
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/nayta_henkilon_havainnot): "+virhe.description;
    }
}

function nayta_havainnot_left(html){
    try{
        if(html === ""){
            html = "Ajax palautti tyhj&auml;n!";
        }
        document.getElementById('havaintotietolaatikko_left').innerHTML = html;
        var laatikko = document.getElementById('havaintotietolaatikko_left');
        laatikko.style.padding = "5px";
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/nayta_havainnot_left): "+virhe.description;
    }
}

// Tämä on vähän outo, enkä muista, miksi näin toteutin. Ehkä liittyy johonkin
// yksittäiseen juttuun:
function sulje_ruutu(elem_id){
    var laatikko = document.getElementById(elem_id);
    if(laatikko){
        laatikko.style.padding = "0";
        laatikko.style.border = "none";
        laatikko.innerHTML = "";
    }
}

// Näin vaikuttaa fiksummalta:
function sulje_ruutu2(elem_id){
    var laatikko = document.getElementById(elem_id);
    if(laatikko){
        laatikko.style.display = "none";
    }
}

function hae_lajihavainnot(id_lj){
    try{
        kysely = "kysymys=nayta_lajihavainnot&"+
                "id_lj="+id_lj;
        nayta_viiveilmoitus = 0;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_havainnot','post', 'text', nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_lajihavainnot): "+virhe.description;
    }
}
/************ VUOSITTAISTEN HAVAINTOJEN HAKU **********************************/
function hae_havainnot(vuosi){
    try{
        kysely = "kysymys=nayta_havainnot&"+
                "nayttovuosi_hav="+vuosi;
        nayta_viiveilmoitus = 1;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_havainnot_sisaltoruutuun','post', 'text',
                    nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_havainnot): "+virhe.description;
    }
}
function nayta_havainnot_sisaltoruutuun(html){
    try{
        if(html === ""){
            html = "Ajax palautti tyhj&auml;n!";
        }
        document.getElementById('sisalto').innerHTML = html;
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/nayta_havainnot): "+virhe.description;
    }
}
//==============================================================================
/********* Metodit lajiluokkien ja kuvausten näyttämiseen ja muokkaukseen: *****/
function hae_lajiluokat(ylaluokka_id){
    try{
        kysely = "kysymys=nayta_lajiluokat&ylaluokka_id_lj="+ylaluokka_id;
        nayta_viiveilmoitus = 1;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_lajiluokat','post', 'text',
                    nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_lajiluokat): "+virhe.description;
    }
}
function nayta_lajiluokat(html){
    
    try{
        // luodaan uusi elementti vain, ellei sellaista jo olemassa:
        // Luin juuri, että JS:ssä olion epämääräisyys on nimenomaan undefined
        // ja siinä suositeltiin nullin tilalla tutkimaan vain totuusarvoa
        // (undefined -> false).
        if(!document.getElementById('lajiluokkalaatikko')){
            body = document.getElementsByTagName("body")[0];
            divi = document.createElement("div");
            divi.setAttribute("id", "lajiluokkalaatikko");
            body.appendChild(divi);
        }
        else{// Muuten vain pannaan näkymään:
            divi = document.getElementById('lajiluokkalaatikko');
            
            divi.style.display = "inline";
            
        }
        divi.innerHTML = html;
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/nayta_lajiluokat): "+virhe.description;
    }
}

// Nimikuvauslomakkeen näyttö. Olio_id-parametri on joko lajiluokan (latina)
// tai kuvauksen id (muut kielet). Id_lj tarvitaan kuitenkin aina 
// tallennettaessa, joten se on aina mukana.
function hae_nimikuvauslomake(kieli_id, olio_id, taulukkosolun_id, id_lj){
    
    try{
        kysely = "kysymys=nayta_nimikuvauslomake"+
                "&kieli_id="+kieli_id+
                "&nimikuvausolio_id="+olio_id+
                "&taulukkosolun_id="+taulukkosolun_id+
                "&id_lj="+id_lj;
        nayta_viiveilmoitus = 0;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_nimikuvauslomake','post', 'text',
                    nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_nimikuvauslomake): "+virhe.description;
    }
}
function nayta_nimikuvauslomake(html){
    
    try{
        // luodaan uusi elementti vain, ellei sellaista jo olemassa:
        if(!document.getElementById('nimikuvauslaatikko')){
            body = document.getElementsByTagName("body")[0];
            divi = document.createElement("div");
            divi.setAttribute("id", "nimikuvauslaatikko");
            body.appendChild(divi);
        }
        else{// Muuten vain pannaan näkymään:
            divi = document.getElementById('nimikuvauslaatikko');
            if(divi.style.display == "none"){
                divi.style.display = "inline";
            }
        }
        divi.innerHTML = html;
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/nayta_nimikuvauslomake): "+virhe.description;
    }
}
/* Tallentaa sekä uuden että muokatun homman. */
function tallenna_nimikuvaus(kieli_id, 
                            olio_id, 
                            solu_id, 
                            id_lj,
                            nimikentan_id, 
                            kuvauskentan_id){
    try{
        var nimi = "";
        var kuvaus = "";
        
        // Haetaan nimi ja kuvaus kentistä:
        var solu = document.getElementById(nimikentan_id);
        if(solu){
            nimi = solu.value;
        }
        var solu2 = document.getElementById(kuvauskentan_id);
        if(solu2){
            kuvaus = solu2.value;
        }
        var kysely = "kysymys=tallenna_nimikuvaus"+
                "&nimi="+nimi+
                "&kuvaus="+kuvaus+
                "&kieli_id="+kieli_id+
                "&id_lj="+id_lj+
                "&nimikuvausolio_id="+olio_id+
                "&taulukkosolun_id="+solu_id;
        nayta_viiveilmoitus = 0;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_tallennustulos','post', 'xml',
                    nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/tallenna_nimikuvaus): "+virhe.description;
    }
}
/* Tiedot tulevat:
echo '<tiedot>';
echo '<taulukkosolun_id>'.$taulukkosolun_id.'</taulukkosolun_id>';
echo '<nimi>'.$nimi.'</nimi>';
echo '<sisalto>'.$sisalto.'</sisalto>';
echo '<ilmoitus>'.$palauteolio->get_ilmoitus().'</ilmoitus>';
echo '</tiedot>';*/
function nayta_tallennustulos(tulosxml){   
   // alert("Metodissa 'nayta_tallennustulos' tulosxml: "+tulosxml );
    // Haetaan xml:stä tiedot esille:
    var taulukkosolun_id =
        tulosxml.getElementsByTagName("taulukkosolun_id")[0].childNodes[0].nodeValue;
    
    var id_lj =
        tulosxml.getElementsByTagName("id_lj")[0].childNodes[0].nodeValue;
    
    var kieli_id =
        tulosxml.getElementsByTagName("kieli_id")[0].childNodes[0].nodeValue;
    
    var olio_id =
        tulosxml.getElementsByTagName("olio_id")[0].childNodes[0].nodeValue;
    
    var ylaluokka_id =
        tulosxml.getElementsByTagName("ylaluokka_id")[0].childNodes[0].nodeValue;
    
    // Ilmeisesti tyhjä nimi aiheuttaa sen, ettei kyseistä elementtiä tai
    // tarkemmin sen childNodes[0]-elementtiä ole ollenkaan! Pitää
    // siis ottaa varovasti!
    var nimi = "";
    var nimielementti =
        tulosxml.getElementsByTagName("nimi")[0].childNodes[0];
    if(nimielementti){
        nimi = nimielementti.nodeValue;
    }
    // HUOM Parseint pitää tehdä, ettei luku ole tekstinä!
    var onnistuminen = parseInt(
        tulosxml.getElementsByTagName("onnistuminen")[0].childNodes[0].nodeValue);
   
    var ilmoitus = "";
    var ilmoituselem =
        tulosxml.getElementsByTagName("ilmoitus")[0].childNodes[0];
    if(ilmoituselem){
        ilmoitus = ilmoituselem.nodeValue;
    }

    nayta_viesti(ilmoitus);


    // Viedään uusi nimi taulukkoon, ellei virheitä tullut.
    // HUom! Alla ehdossa pelkkä onnistuminen ei suluissa riitä!
    if(onnistuminen===1){
        // Huom! onclick-metodin toista
        // parametria pitää muuttaa, kun kysymys on uuden tallennuksesta!
        // Arvo -1 pitää saada uuden id:n arvoksi, jolloin koko onclick-metodi
        // pitää kirjoittaa uusiksi.
        var solu = document.getElementById(taulukkosolun_id);
        if(solu){
            solu.innerHTML = nimi;
            solu.onclick =  
                function() {
                    hae_nimikuvauslomake(kieli_id, olio_id, taulukkosolun_id, id_lj);
                };
        }
        
        // nimikuvauslaatikko pois:
        sulje_ruutu2("nimikuvauslaatikko");
        
    } else{
        
    }
}

function siirra_kuvat_ja_havainnot(alkup_lajiluokka_id, valikko_id){
    
    try{
        var uusi_lajiluokka_id = -1;
        var valikkoelem = document.getElementById(valikko_id);
        if(valikkoelem){
            uusi_lajiluokka_id = 
                valikkoelem.options[valikkoelem.selectedIndex].value;
        }
        
        var kysely = "kysymys=siirra_kuvat_ja_havainnot"+
                "&id_lj="+alkup_lajiluokka_id+
                "&siirtokohde_id_lj="+uusi_lajiluokka_id;
        nayta_viiveilmoitus = 0;
        //alert(kysely);
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_siirron_tulos','post', 'xml',
                    nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/siirra_kuvat_ja_havainnot): "+virhe.description;
    }
}
function nayta_siirron_tulos(tulosxml){
    // Haetaan xml:stä tiedot esille:
    //alert(tulosxml);
    var siirtolomakelaatikon_id = 
            hae_xml_elementin_sisalto(tulosxml, "siirtolaatikko_id", "Missa lie?");
        

    var ylaluokka_id =
        hae_xml_elementin_sisalto(tulosxml, "ylaluokka_id", -1);
    
    var ilmoitus =
        hae_xml_elementin_sisalto(tulosxml, "ilmoitus", "Ilmoitusta ei löytynyt!");
       
    nayta_viesti(ilmoitus);

    // laatikot pois:
    sulje_ruutu2("nimikuvauslaatikko");
    sulje_ruutu2(siirtolomakelaatikon_id);

    // Haetaan lajinäkymä uudelleen:
    hae_lajiluokat(ylaluokka_id); 

}
function hae_siirtolomake(id_lj){
    try{
        kysely = "kysymys=nayta_siirtolomake"+
                "&id_lj="+id_lj;
        nayta_viiveilmoitus = 0;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_siirtolomake','post', 'xml',
                    nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_siirtolomake): "+virhe.description;
    }
}
function nayta_siirtolomake(tulosxml){
    try{
        //alert(tulosxml);
        var siirtolomakelaatikon_id =
        tulosxml.getElementsByTagName("laatikko_id")[0].childNodes[0].nodeValue;
    
        var lomakehtml = "";
        var lomakehtmlelem =
            tulosxml.getElementsByTagName("lomakehtml")[0].childNodes[0];
        if(lomakehtmlelem){
            lomakehtml = lomakehtmlelem.nodeValue;
        }
   
        // luodaan uusi elementti vain, ellei sellaista jo olemassa:
        if(!document.getElementById(siirtolomakelaatikon_id)){
            body = document.getElementsByTagName("body")[0];
            divi = document.createElement("div");
            divi.setAttribute("id", siirtolomakelaatikon_id);
            body.appendChild(divi);
        }
        else{// Muuten vain pannaan näkymään:
            divi = document.getElementById(siirtolomakelaatikon_id);
            if(divi.style.display === "none"){
                divi.style.display = "inline";
            }
        }
        divi.innerHTML = lomakehtml;
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/nayta_siirtolomake): "+virhe.description;
    }
}

function poista_lajiluokka(id_lj, vahvistuskysymys, perumisviesti){
    try{
        if(confirm(vahvistuskysymys)){
            kysely = "kysymys=poista_lajiluokka"+
                "&id_lj="+id_lj;
            nayta_viiveilmoitus = 0;
            toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                        'nayta_poistoviesti','post', 'xml',
                        nayta_viiveilmoitus);
        }
        else{
            nayta_viesti(perumisviesti);
        }
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_siirtolomake): "+virhe.description;
    }
}

function nayta_poistoviesti(xml){
    var ilmoitus = "";
    var ilmoituselem =
        xml.getElementsByTagName("ilmoitus")[0].childNodes[0];
    if(ilmoituselem){
        ilmoitus = ilmoituselem.nodeValue;
    }
    
    var onnistuminen = "";
    var onnistuminenelem =
        xml.getElementsByTagName("onnistuminen")[0].childNodes[0];
    if(onnistuminenelem){
        onnistuminen = onnistuminenelem.nodeValue;
    }
    
    var ylaluokka_id =
        xml.getElementsByTagName("ylaluokka_id")[0].childNodes[0].nodeValue;
    
    nayta_viesti(ilmoitus);
    
    if(onnistuminen){
        // laatikot pois:
        sulje_ruutu2("nimikuvauslaatikko");

        // Haetaan lajinäkymä uudelleen:
        hae_lajiluokat(ylaluokka_id); 
    }
}

function nayta_viesti(viesti){
    var palautelaatikko = document.getElementById("ilmoitus");

    // Luodaan laatikko, ellei sellaista ole:
    if(!palautelaatikko){
        body = document.getElementsByTagName("body")[0];
        palautelaatikko = document.createElement("div");
        palautelaatikko.setAttribute("id", "ilmoitus");
        body.appendChild(palautelaatikko);
    }
    else{
        palautelaatikko.style.display = "inline";
    }

    palautelaatikko.innerHTML = viesti;
}

// Tätä ei vielä toteutettu tätä kautta.
function bongaus_kopioi_havainto(param){
    return false;
}

// Piilottaa elementin, muttei poista (display="none").
function piilota_elementti(id){
    var elem = find(id);
    if(elem){
        elem.style.display = "none";
    }
}

/* Näyttää vakipaikkalomakkeen, eli hakee ajaxin avulla html-koodin. */
function hae_vakipaikkalomake(){
    try{
        kysely = "kysymys=nayta_vakipaikkalomake";
                //"&id_lj="+id_lj;
        nayta_viiveilmoitus = 0;
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_vakipaikkalomake','post', 'text',
                    nayta_viiveilmoitus);
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/hae_vakipaikkalomake): "+virhe.description;
    }
}
/* Näyttää vakipaikkalomakkeen, eli haetun html-koodin yleislaatikon sisällä.*/
function nayta_vakipaikkalomake(html){
    try{
        //alert(html);
        var yleislaatikon_id = "yleislaatikko";
   
        // luodaan uusi elementti vain, ellei sellaista jo olemassa:
        if(!document.getElementById(yleislaatikon_id)){
            body = document.getElementsByTagName("body")[0];
            divi = document.createElement("div");
            divi.setAttribute("id", yleislaatikon_id);
            body.appendChild(divi);
        }
        else{// Muuten vain pannaan näkymään:
            divi = document.getElementById(yleislaatikon_id);
            if(divi.style.display === "none"){
                divi.style.display = "block";
            }
        }
        divi.innerHTML = html;
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/nayta_vakipaikkalomake): "+virhe.description;
    }
}

/* Tallentaa sekä uuden että muokatun homman. */
function tallenna_vakipaikka(vakipaikka_id, // olion id > 0, jos vanhan muokkaus. 
                            maavalikko_id,
                            paikkaruutu_id, 
                            selitysruutu_id,
                            vakipaikka_id_name,
                            maa_id_name,
                            paikka_name,
                            selitys_name){
    try{
        
        // Haetaan arvot kentistä:
        var maa_id =-1;
        var paikka = "";
        var selitys = "";
        
        var maavalikko = find(maavalikko_id);
        if(maavalikko){
            maa_id = maavalikko.options[maavalikko.selectedIndex].value;
        }
        
        var paikkaruutu = find(paikkaruutu_id);
        if(paikkaruutu){
            paikka = paikkaruutu.value;
        }
        
        var selitysruutu = find(selitysruutu_id);
        if(selitysruutu){
            selitys = selitysruutu.value;
        }
        
        var kysely = "kysymys=tallenna_vakipaikka"+
                "&"+vakipaikka_id_name+"="+vakipaikka_id+
                "&"+maa_id_name+"="+maa_id+
                "&"+paikka_name+"="+paikka+
                "&"+selitys_name+"="+selitys;
        alert(kysely);
        nayta_viiveilmoitus = 0;
        /*toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    'nayta_vakipaikkatallennustulos','post', 'xml',
                    nayta_viiveilmoitus);*/
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/tallenna_nimikuvaus): "+virhe.description;
    }
}
/* Tiedot tulevat:
echo '<tiedot>';
echo '<taulukkosolun_id>'.$taulukkosolun_id.'</taulukkosolun_id>';
echo '<nimi>'.$nimi.'</nimi>';
echo '<sisalto>'.$sisalto.'</sisalto>';
echo '<ilmoitus>'.$palauteolio->get_ilmoitus().'</ilmoitus>';
echo '</tiedot>';*/
function nayta_vakipaikkatallennustulos(tulosxml){   
   // alert("Metodissa 'nayta_tallennustulos' tulosxml: "+tulosxml );
    // Haetaan xml:stä tiedot esille:
    var taulukkosolun_id =
        tulosxml.getElementsByTagName("taulukkosolun_id")[0].childNodes[0].nodeValue;
    
    var id_lj =
        tulosxml.getElementsByTagName("id_lj")[0].childNodes[0].nodeValue;
    
    var kieli_id =
        tulosxml.getElementsByTagName("kieli_id")[0].childNodes[0].nodeValue;
    
    var olio_id =
        tulosxml.getElementsByTagName("olio_id")[0].childNodes[0].nodeValue;
    
    var ylaluokka_id =
        tulosxml.getElementsByTagName("ylaluokka_id")[0].childNodes[0].nodeValue;
    
    // Ilmeisesti tyhjä nimi aiheuttaa sen, ettei kyseistä elementtiä tai
    // tarkemmin sen childNodes[0]-elementtiä ole ollenkaan! Pitää
    // siis ottaa varovasti!
    var nimi = "";
    var nimielementti =
        tulosxml.getElementsByTagName("nimi")[0].childNodes[0];
    if(nimielementti){
        nimi = nimielementti.nodeValue;
    }
    // HUOM Parseint pitää tehdä, ettei luku ole tekstinä!
    var onnistuminen = parseInt(
        tulosxml.getElementsByTagName("onnistuminen")[0].childNodes[0].nodeValue);
   
    var ilmoitus = "";
    var ilmoituselem =
        tulosxml.getElementsByTagName("ilmoitus")[0].childNodes[0];
    if(ilmoituselem){
        ilmoitus = ilmoituselem.nodeValue;
    }

    nayta_viesti(ilmoitus);


    // Viedään uusi nimi taulukkoon, ellei virheitä tullut.
    // HUom! Alla ehdossa pelkkä onnistuminen ei suluissa riitä!
    if(onnistuminen===1){
        // Huom! onclick-metodin toista
        // parametria pitää muuttaa, kun kysymys on uuden tallennuksesta!
        // Arvo -1 pitää saada uuden id:n arvoksi, jolloin koko onclick-metodi
        // pitää kirjoittaa uusiksi.
        var solu = document.getElementById(taulukkosolun_id);
        if(solu){
            solu.innerHTML = nimi;
            solu.onclick =  
                function() {
                    hae_nimikuvauslomake(kieli_id, olio_id, taulukkosolun_id, id_lj);
                };
        }
        
        // nimikuvauslaatikko pois:
        sulje_ruutu2("nimikuvauslaatikko");
        
    } else{
        
    }
}



/*
 * Vaihtaa monivalintalomakkeen havaintojaksoruudun tiedot, 
 * kun havaintojaksoa vaihdetaan (default uusi).
 */
function vaihda_havjaks(new_id, havjaks_idname){
  try{
    //alert("Kukkuu! Havaintojakson id="+new_id);
      kysely = "kysymys=vaihda_havjakso_lomake"+
          "&"+havjaks_idname+"="+new_id;
      nayta_viiveilmoitus = 0;
      toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                  'nayta_havjakstiedot','post', 'xml',
                  nayta_viiveilmoitus);
    
    }

    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (bongausmetodit.js/vaihda_havjaks): "+
            virhe.description;
    }
  //var e = document.getElementById("havaintojaksovalikko");
  //var strUser = e.options[e.selectedIndex].value;
}
/**
 * Näyttää vaihdetun havaintojakson tiedot kentissä ja muuttaa
 * ne ei-muokattaviksi.
 * @param {type} xml
 * @returns {undefined}
 */
function nayta_havjakstiedot(xml){
  //alert("xml="+xml);
  var nimet = ["nimi", "kommentti", "alkuh", "alkukk", "alkumin",
          "alkupaiva", "alkuvuosi", "kestoh", "kestovrk", "kestomin"];

  var arvo, id, xmlElem_arvo, xmlElem_id, onUusi_raaka, onUusi, elem;

  onUusi_raaka = xml.getElementsByTagName("onUusi")[0].childNodes[0].nodeValue;
  if(onUusi_raaka === "1"){
    onUusi = true;
  } else{
    onUusi = false;
  }
  
  for (var i=0; i < nimet.length; i++){
    arvo = "";
    id = "";
    
    xmlElem_id = xml.getElementsByTagName("id_"+nimet[i])[0].childNodes[0];
    if(xmlElem_id){
        id = xmlElem_id.nodeValue;
    }
    
    xmlElem_arvo = xml.getElementsByTagName(nimet[i])[0].childNodes[0];
    if(xmlElem_arvo){
        arvo = xmlElem_arvo.nodeValue;
    }
    
    elem = document.getElementById(id);
    
    if(elem){
      elem.value = arvo;
      if(onUusi){
        elem.disabled = false;
      } else{
        elem.disabled = true;
      }
    }
  }
}