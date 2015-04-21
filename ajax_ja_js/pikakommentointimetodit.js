/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function hae_pikakommentit(kohde_tyyppi, kohde_id){
    try{
        //alert("kohde_tyyppi="+kohde_tyyppi);
        var metodinimi = "nayta_pikakommentit";
        kysely = "kysymys=hae_pikakommentit"+
            "&pk_kohdetyyppi="+kohde_tyyppi+
            "&pk_kohde_id="+kohde_id;
            //"&ikkunan_leveys="+hae_ikkunan_leveys();

        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    metodinimi,'post', 'text',1);
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
                "Virhe (pikakommenttimetodit.js/hae_pikakommentit): "+virhe.description;
    }
}

/**
 * Luodaan tai näkyväistään pikakommenttilaatikko:
 */
function nayta_pikakommentit(html){
    var pk_divi;
    try{
        
        // luodaan uusi elementti vain, ellei sellaista jo olemassa:
        // Luin juuri, että JS:ssä olion epämääräisyys on nimenomaan undefined
        // ja siinä suositeltiin nullin tilalla tutkimaan vain totuusarvoa
        // (undefined -> false). JS ymmärtää kyllä nullin suunnilleen epämääräiseksi ja
        // tulos on sama käytettäessä "=="/"!=" juttuja "==="/"!--" asemesta.
        if(!document.getElementById('pikakommenttilaatikko')){
            body = document.getElementsByTagName("body")[0];
            pk_divi = document.createElement("div");
            pk_divi.setAttribute("id", "pikakommenttilaatikko");
            body.appendChild(pk_divi);
        }
        else{// Muuten vain pannaan näkymään:
            pk_divi = document.getElementById('pikakommenttilaatikko');
            if(pk_divi.style.display === "none"){
                pk_divi.style.display = "inline";
            }
        }
        pk_divi.innerHTML = html;

        // Rullataan aina alas, jotta tekstikenttä ja vikat kommentit näkyy:
        pk_divi.scrollTop = pk_divi.scrollHeight;
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
                "Virhe (pikakommenttimetodit.js/nayta_pikakommentit): "+virhe.description;
    }
}

/* Pikakommenttien piilotus: */
function piilota_pikakommentit(){
    // luodaan uusi elementti vain, ellei sellaista jo olemassa:
    var pk = document.getElementById('pikakommenttilaatikko');
    if(pk!=null){
        pk.style.display = "none";
    }
}

function tallenna_uusi_pikakommentti(kohde_tyyppi, 
                                    kohde_id, 
                                    pk_kohdetyyppi_name, 
                                    pk_kohde_id_name, 
                                    pk_kommenttiteksti_name){
                                        
    // Haetaan teksti syöttökentästä:
    //alert("Haa ollaan metodissa 'tallenna uusi pikakommentti'");
    var teksti = "";
    var tekstikentta = document.getElementById('syottokentta_pikakommentit');
    if(tekstikentta){
        teksti = tekstikentta.value;
        if(teksti.length > 0){
            try{
                var metodinimi = "nayta_tallennusilmoitus";
                kysely = "kysymys=tallenna_uusi_pikakommentti"+
                            "&"+pk_kohdetyyppi_name+"="+kohde_tyyppi+
                            "&"+pk_kohde_id_name+"="+kohde_id+
                            "&"+pk_kommenttiteksti_name+"="+teksti;
                            //"&ikkunan_leveys="+hae_ikkunan_leveys();


                //alert("Kysely: "+kysely);
                            
                toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                            metodinimi,'post','xml',1);
            }
            catch(virhe){
                document.getElementById("ilmoitus").innerHTML =
                        "Virhe (pikakommenttimetodit.js/tallenna_uusi_pikakommentit): "+
                        virhe.description;
            }
        }
        else{
            alert("Tyhjää on teksti täynnä!");
            document.getElementById("ilmoitus").innerHTML =
                        "Tyhjää ei tallenneta!";
        }
    }
}
/* Kysyy poistovarmistuksen: */
function esita_pikakommentin_poistovarmistus(pk_id, pk_id_name){
    // Haetaan teksti syöttökentästä:
    try{
        var metodinimi = "vie_sisalto";
        kysely = "kysymys=nayta_poistovahvistus"+
                        "&"+pk_id_name+"="+pk_id;
        //alert("poistovarmistuskysely ="+kysely);
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    metodinimi,'post','xml',1);
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML = ""; // Valittaa turhasta.
            //"Virhe (pikakommenttimetodit.js/esita_pikakommentin_poistovarmistus): "+
            //virhe.description;
    }
}

/* Vie sisällön xml-koodissa määritellyn "kohde_id":n mukaiseen elementtiin. 
 * Samoin viedään mahdollinen piilotettava juttu piilo_id:n mukaiseen paikkaan.*/
function vie_sisalto(xml){
    // Haetaan teksti syöttökentästä:
    try{
        //alert("xml:"+xml);
        kohde_id = 
           xml.getElementsByTagName("kohde_id")[0].childNodes[0].nodeValue;
        sisalto = 
           xml.getElementsByTagName("sisalto")[0].childNodes[0].nodeValue;

        //alert("kohde_id:"+kohde_id+" ja sisalto: "+sisalto);
        kohde = document.getElementById(kohde_id);
        if(kohde){
            kohde.innerHTML = sisalto;
        }
        
        // Piilotettava tieto:
        piilotietoelem = xml.getElementsByTagName("piiloon")[0];
        piilo_id_elem = xml.getElementsByTagName("piilo_id")[0];
        if(piilotietoelem && piilo_id_elem){
            piilotieto = piilotietoelem.childNodes[0].nodeValue;
            piilo_id = piilo_id_elem.childNodes[0].nodeValue;
            
            //alert("piilotieto="+piilotieto+" ja piilo_id="+piilo_id);
            
            piilodivi = document.getElementById(piilo_id);
            
            // luodaan uusi elementti vain, ellei sellaista jo olemassa:
            if(!piilodivi){
                body = document.getElementsByTagName("body")[0];
                piilodivi = document.createElement("div");
                piilodivi.setAttribute("id", piilo_id);
                piilodivi.style.display = "none";   // Ei näytetä.
                body.appendChild(piilodivi);
            }
            piilodivi.innerHTML = piilotieto;
        }
    }
    catch(virhe){
        /*document.getElementById("ilmoitus").innerHTML =
           "Virhe (pikakommenttimetodit.js/vie_sisalto): "+
            virhe.description;*/
    }
}

// Näyttää ilmoituksen tallennuksen onnistumisesta ja hakee pikakommentit
// uudelleen. Lisää myös lukumäärää osoittavaa lukua, jos sellainen on.
function nayta_tallennusilmoitus(tulosxml){
    try{
        // alert("Metodissa 'nayta_tallennusilmoitus' tulosxml="+tulosxml);
        palaute_elementti = tulosxml.getElementsByTagName("palaute")[0];
        palaute = palaute_elementti.childNodes[0].nodeValue;
        // alert("Palaute="+palaute);
        kohde_id_elementti = tulosxml.getElementsByTagName("kohde_id")[0];
        kohde_id = kohde_id_elementti.childNodes[0].nodeValue;

        //Onnistuu suoraankin:
        kohde_tyyppi =
            tulosxml.getElementsByTagName("kohde_tyyppi")[0].childNodes[0].nodeValue;
        

        //alert("Tiedot: kohde_id="+kohde_id+", kohde_tyyppi="+kohde_tyyppi+", palaute="+palaute);

        palautelaatikko = document.getElementById("ilmoitus");
        
        // Luodaan laatikko, ellei sellaista ole:
        if(!palautelaatikko){
            body = document.getElementsByTagName("body")[0];
            palautelaatikko = document.createElement("div");
            palautelaatikko.setAttribute("id", "ilmoitus");
            body.appendChild(palautelaatikko);
        }
        
        palautelaatikko.innerHTML = palaute;
        
        // Lukumäärän lisäys:
        lukuelem = document.getElementById("id"+kohde_id);
        if(lukuelem){
            luku = 1+parseInt(lukuelem.innerHTML);
            lukuelem.innerHTML = luku;
        }

        // Haetaan pikakommentit uusiksi:
        hae_pikakommentit(kohde_tyyppi, kohde_id);
    }
    catch(virhe){
        // Tässä oudosti kerkis aina heittää virheilmoituksen, jonka sitten
        // korvasi yllä haetulla oikealla tiedolla. Siispä poistin tämän täältä,
        // ettei härnää turhia.
        document.getElementById("ilmoitus").innerHTML = "";
                //"Virhe (pikakommenttimetodit.js/nayta): "+virhe.description;
    }
}
// Näyttää ilmoituksen tallennuksen onnistumisesta ja hakee pikakommentit
// uudelleen. Lisää myös lukumäärää osoittavaa lukua, jos sellainen on.
function nayta_muutostallennusilmoitus(tulosxml){
    try{
        //alert(tulosxml);
        palaute_elementti = tulosxml.getElementsByTagName("palaute")[0];
        palaute = palaute_elementti.childNodes[0].nodeValue;
        kohde_id_elementti = tulosxml.getElementsByTagName("kohde_id")[0];
        kohde_id = kohde_id_elementti.childNodes[0].nodeValue;

        //Onnistuu suoraankin:
        kohde_tyyppi =
            tulosxml.getElementsByTagName("kohde_tyyppi")[0].childNodes[0].nodeValue;


        //alert("Tiedot: kohde_id="+kohde_id+", kohde_tyyppi="+kohde_tyyppi+", palaute="+palaute);

        palautelaatikko = document.getElementById("ilmoitus");

        // Luodaan laatikko, ellei sellaista ole:
        if(!palautelaatikko){
            body = document.getElementsByTagName("body")[0];
            palautelaatikko = document.createElement("div");
            palautelaatikko.setAttribute("id", "ilmoitus");
            body.appendChild(palautelaatikko);
        }

        palautelaatikko.innerHTML = palaute;

        // Haetaan pikakommentit uusiksi:
        hae_pikakommentit(kohde_tyyppi, kohde_id);
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML = "";
                //"Virhe (pikakommenttimetodit.js/nayta): "+virhe.description;
    }
}



/**
 * Vie muokattavan tekstin muokkausruutuun ja vaihtaa samalla Tallenna-napin
 * onclick-metodia niin, että se muokkaa vanhaa.
 *
 * HUOM! Tämän voisi tietty hakea ajax-kyselyn avulla, jolloin muokkaukset
 * voisi tehdä PHP:n avulla, jolloin tekstit yms voidaan hakea yhteisestä
 * tekstivarastosta.
 */
function pk_muokkaa(muokattava,
                    kohde_tyyppi,
                    kohde_id,
                    pikakommentin_id, 
                    pk_kohdetyyppi_name, 
                    pk_kohde_id_name, 
                    pk_kommenttiteksti_name,
                    pk_id_name){

    var syottokentta = document.getElementById("syottokentta_pikakommentit");
    syottokentta.value = muokattava;
    syottokentta.focus();

    // Vaihdetaan onclick-määrite vastaamaan muutosta olemassaolevaan olioon.
    // Huomaa rakenne!
    // Vaihdetaan myös title.
    var tallennuspainike = document.getElementById("tallennuspainike");
    if(tallennuspainike){
        tallennuspainike.onclick= function(){
            tallenna_pikakommentin_muutos(kohde_tyyppi, 
                                            kohde_id, 
                                            pikakommentin_id, 
                                            pk_kohdetyyppi_name, 
                                            pk_kohde_id_name, 
                                            pk_kommenttiteksti_name,
                                            pk_id_name);
                                            };

        tallennuspainike.title = "Tallentaa pikakommentin muutokset";
    }
    else{
        alert("tallennuspainiketta ei löytynyt!");
    }
    //Ohjeen muutos:
    document.getElementById("pikakommenttiohje").innerHTML=
        "Muokkaa ja tallenna!";
}
/**
 * Tallentaa tai ainakin yrittää tallentaa pikakommentin muutoksia.
 * 
 * @param {type} kohde_tyyppi
 * @param {type} kohde_id
 * @param {type} pikakommentin_id
 * @param {type} pk_kohdetyyppi_name
 * @param {type} pk_kohde_id_name
 * @param {type} pk_kommenttiteksti_name
 * @param {type} pk_id_name
 * @returns {undefined}
 */
function tallenna_pikakommentin_muutos(kohde_tyyppi, 
                                    kohde_id, 
                                    pikakommentin_id, 
                                    pk_kohdetyyppi_name, 
                                    pk_kohde_id_name, 
                                    pk_kommenttiteksti_name,
                                    pk_id_name){
                                        
    // Haetaan teksti syöttökentästä:
    var teksti = "";
    var tekstikentta = document.getElementById('syottokentta_pikakommentit');
    if(tekstikentta){
        teksti = tekstikentta.value;
        if(teksti.length > 0){
            try{
                var metodinimi = "nayta_muutostallennusilmoitus";
                kysely = "kysymys=tallenna_pikakommentin_muutos"+
                             "&"+pk_kohdetyyppi_name+"="+kohde_tyyppi+
                            "&"+pk_kohde_id_name+"="+kohde_id+
                            "&"+pk_kommenttiteksti_name+"="+teksti+
                            "&"+pk_id_name+"="+pikakommentin_id;

                toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                            metodinimi,'post', 'xml',1);
            }
            catch(virhe){
                document.getElementById("ilmoitus").innerHTML =
                        "Virhe (pikakommenttimetodit.js/tallenna_pikakommentin_muutos): "+
                        virhe.description;
            }
        }
        else{
            document.getElementById("ilmoitus").innerHTML =
                        "Tyhjää ei tallenneta!";
        }
    }
}

function pk_poista(pk_id, kohde_id, pk_id_name, pk_kohde_id_name){
    try{
        var metodinimi = "nayta_poistoilmoitus";
        kysely = "kysymys=toteuta_pikakommentin_poisto"+
                "&"+pk_id_name+"="+pk_id+
                "&"+pk_kohde_id_name+"="+kohde_id;

        //alert("id="+pk_id);
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    metodinimi,'post','xml',1);
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML = "id="+pk_id;
            /*"Virhe (pikakommenttimetodit.js/pk_poista): "+
            virhe.description;*/
    }
}

// Näyttää ilmoituksen tallennuksen poistamisesta ja poistaa poistetun
// sivulta. Vähentää myös lukumäärää osoittavaa lukua, jos sellainen on.
function nayta_poistoilmoitus(tulosxml){
    try{
        palaute_elementti = tulosxml.getElementsByTagName("palaute")[0];
        palaute = palaute_elementti.childNodes[0].nodeValue;
        //alert("Tiedot: palaute="+palaute);
        kohde_id_elementti = tulosxml.getElementsByTagName("kohde_id")[0];
        kohde_id = kohde_id_elementti.childNodes[0].nodeValue;

        poistettavan_id =
            tulosxml.getElementsByTagName("poistettavan_id")[0].childNodes[0].nodeValue;

        //alert("Tiedot: kohde_id="+kohde_id+", palaute="+palaute+" poistettavan_id="+poistettavan_id);

        palautelaatikko = document.getElementById("ilmoitus");

        // Luodaan laatikko, ellei sellaista ole:
        if(!palautelaatikko){
            body = document.getElementsByTagName("body")[0];
            palautelaatikko = document.createElement("div");
            palautelaatikko.setAttribute("id", "ilmoitus");
            body.appendChild(palautelaatikko);
        }

        palautelaatikko.innerHTML = palaute;

        // Lukumäärän vähennys:
        id = "id"+kohde_id;
        lukuelem = document.getElementById(id);

        if(lukuelem != null){
            luku = parseInt(lukuelem.innerHTML)-1;
            lukuelem.innerHTML = luku;
        }

        // POistetaan näkyvistä:
        poistettavan_id = "pk"+poistettavan_id;
        poista_elementti(poistettavan_id);
    }
    catch(virhe){
        // Tässä oudosti kerkis aina heittää virheilmoituksen, jonka sitten
        // korvasi yllä haetulla oikealla tiedolla. Siispä poistin tämän täältä,
        // ettei härnää turhia.
        document.getElementById("ilmoitus").innerHTML = "";
                //"Virhe (pikakommenttimetodit.js/nayta): "+virhe.description;
    }
}


/**
 * Tässä oli hankaluuksia, koska ennen palautettava html-elementti oli
 * metodin parametrina ja se ei millään meinannut toimia pilkkujen yms.
 * merkkien sekoilun takia. Tämän kiertotien hokasin netistä..
 * 
 * @param {type} tuo_elem_id
 * @param {type} vie_elem_id
 * @returns {undefined}
 */
function peru_poisto(tuo_elem_id, vie_elem_id){
    // Haetaan teksti varastoelementistä:
    //alert("Moi!");
    try{
        document.getElementById(vie_elem_id).innerHTML = 
                    document.getElementById(tuo_elem_id).innerHTML;
    }
    catch(virhe){
        document.getElementById("ilmoitus").innerHTML =
            "Virhe (pikakommenttimetodit.js/peru_poisto): "+
            virhe.description;
    }
}
// Poistaa elementin sivulta (ei tietokannasta tms):
function poista_elementti(poistettavan_id){
    var poistettava = document.getElementById(poistettavan_id);

    if(poistettava){
        poistettava.style.display="none";
        //poistettava.style.height="";
    }
}


