// Ajastimet, jotta voidaan pysäyttää:
var ajastin1, ajastin2, ajastin3, ajastin4, ajastin5;

/** 
 * Adds an element before index given as parameter. If
 * parameter >= array.length, adds en element to the end of array. Returns the
 * result array.
 * 
 * Note: remember that a nodeList doesn't work here because it's immutable.
 * Note this, too: elem.insertBefore(newItem, elem.childNodes[i]);
 * */
function addElemBefore(index_raw, elem, targetArray){
    
    var index = Number(index_raw);
    
    if(index >= targetArray.length || index < 0){
        targetArray.push(elem);
    } else{
        targetArray.splice(index, 0, elem);
        //alert("Funktio addElemBefore: "+targetArray.length+" elems in array");
    }
    return targetArray;
}


/**
 * Short hand notation for document.getElementById(). Returns the same result
 * as the longer variant.
 * @param {type} id
 * @returns {Element}
 */
function find(id){
    return document.getElementById(id);
}

/** 
 * Kirjoittaa parametrina annetun sisällön elementtiin, jonka id annetaan
 * myös parametrina. Ellei elementtiä löydy, palauttaa arvon false. Muuten
 * palauttaa arvon true.
 * 
 * Huom! Käyttää oletuksena innerHTML-komentoa, joka ei toimi syöttökentissä.
 * Jos elementti on sellainen, joka vaatii value-arvon, aseta kolmannelle
 * parametrille arvo true. Muuten parametrin arvon tulee olla false.
 * 
 * @param {int} id
 * @param {String} sisalto
 * @param {Boolean} kayta_value
 * @returns {Boolean}
 */
function kirjoita_elementtiin(id, sisalto, kayta_value){
    var onnistuminen = true;
    var elem = document.getElementById(id);
    
    if(elem){
        if(kayta_value){
            elem.value = sisalto;
        } else{
            elem.innerHTML = sisalto;
        }
    } else{
        onnistuminen = false;
    }
    return onnistuminen;
}

/** 
 * Hakee sen input / form-elementin value-arvon, jonka id annetaan
 * parametrina. Ellei elementtiä löydy, palauttaa arvon virhepalaute, joka
 * annetaan parametrina.
 * 
 * Ei tarkisteta elementin laatua (kehittäjän vastuulla, jotta value toimii :)
 * 
 * @param {String} id
 * @returns {Mixed}
 */
function hae_value(id, virhepalaute){
    var elem = document.getElementById(id);
    
    if(elem){  
      return elem.value;
        
    } else{
        return virhepalaute;
    }
}

/**
 * Palauttaa annetusta xml-koodista haetun elementin sisällön
 * (getElementsByTagName(tagname)[0].childNodes[0].nodeValue;). 
 * 
 * Ellei elementtiä
 * löydy, palauttaa parametrina syötetyn arvon.
 * @param {xml} xml koodi, jonka jonkin elementin sisältöä haetaan.
 * @param {int} tagname elementin nimi
 * @param {type} ei_loydy_palaute Arvo, jonka metodi palauttaa silloin, kun
 * etsittävää xml-elementtiä ei löydy.
 * @returns {undefined}
 */
function hae_xml_elementin_sisalto(xml, tagname, ei_loydy_palaute){
    // Otetaan huomioon, ettei elementtiä välttämättä ole olemassa. Tällöin
    // palautetaan huomautus asiasta.
    var sisalto = "";
    var sisaltoelem =
        xml.getElementsByTagName(tagname)[0].childNodes[0];
    if(sisaltoelem){
        sisalto = sisaltoelem.nodeValue;
    } else{
        sisalto = ei_loydy_palaute;
    }
    return sisalto;
}

/**
 * Piilottaa elementin näkyvistä eli antaa elementin style.display-muuttujalle
 * arvon "none". Ei tee mitään, ellei annetulla id:llä löydy elementtiä.
 * @param {type} id
 * @returns {undefined}
 */
function piilota_elementti(id){
    
    var elem = document.getElementById(id);
    if(elem){
        elem.style.display = "none";
    }
}

/**
 * Asettaa elementin näkyvyyden eli antaa elementin style.display-muuttujalle
 * parametrina annetun arvon (dispVal). Ei tee mitään, ellei annetulla id:llä 
 * löydy elementtiä.
 * @param {type} id
 * @param {type} dispVal
 * @returns {undefined}
 */
function setElemDisplay(id, dispVal){
    
    var elem = document.getElementById(id);
    if(elem){
        elem.style.display = dispVal;
    }
}

//==============================================================================
/**
 * Muokkaa elementin ulkonäköä 
 * elementtiin src-arvoksi parametrina annetun kuvaosoitteen.
 * @param {type} elem_id elementtitunniste, jonka ulkonäköä muokataan
 * @param {type} kuvaosoite uuden kuvan osoite.
 * @returns {undefined}
 */
function muuta_src(elem_id, kuvaosoite){
    //alert("elem_id="+elem_id+" ja kuvaos="+kuvaosoite);
    
    var elementti = document.getElementById(elem_id);
    if(elementti){
        elementti.src = kuvaosoite;
    } else{
        alert("Elementtiä ei löytynyt!");
    }
}


//==============================================================================

// Selainikkunan leveyden hakemiseen malli saatu osoitteesta
// http://www.howtocreate.co.uk/tutorials/javascript/browserwindow.
// Tapa saattaa olla vanhentunut.
// HUOM! Ikkunan leveys näyttää ainakin chromella olevan 17 px isompi kuin
// html-bodyn leveys. Rullauspalkki syynä?
function hae_ikkunan_leveys()
{
  var lev = 0;
  if(typeof(window.innerWidth ) === 'number')
  {
    //Non-IE
    lev = window.innerWidth;
  }
  else if(document.documentElement && document.documentElement.clientWidth)
  {
    //IE 6+ in 'standards compliant mode'
    lev = document.documentElement.clientWidth;
  }
  else if(document.body && document.body.clientWidth)
  {
    //IE 4 compatible
    lev = document.body.clientWidth;
  }
  return lev;
}

function hae_ikkunan_korkeus()
{
  var kork = 0;
  if(typeof(window.innerHeight ) === 'number')
  {
    //Non-IE
    kork = window.innerHeight;
  }
  else if(document.documentElement && document.documentElement.clientHeight)
  {
    //IE 6+ in 'standards compliant mode'
    kork = document.documentElement.clientHeight;
  }
  else if(document.body && document.body.clientHeight)
  {
    //IE 4 compatible
    kork = document.body.clientHeight;
  }
  return kork;
}

// Vie ikkunan leveyden muistiin: ***********************************************
function vie_muistiin_ikkunan_leveys(){
    try{
        kysely = "kysymys=muista_ikkunan_leveys"+
                "&ikkunan_leveys="+hae_ikkunan_leveys();

        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    '','post', 'text',1);

        return false;   // False -> ei submit-lähetystä painikkeelle.

    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/vie_muistiin_ikkunan_leveys): "+virhe.description;
    }
}
// Ylläolevan toiminnan testaus
function leveys_testi(html){
    document.getElementById("ilmoitus2").innerHTML = html;
}

/******************************************************************************/
function nayta_ohje(html){
    document.getElementById('sisaltoteksti').innerHTML = html;
}

function hae_ohje(){
   toteutaAJAX('ohje.php','','nayta_ohje','post', 'text',0);
}
/******************************************************************************/

//==============================================================================
//
//==============================================================================

// Tämä näyttää kellonajan (index.php):
function nayta_aika(aika)
{
    try{
        if(aika == "Istunto vanhentunut!"){
            pysayta_metodit();
            siirra_hitaasti("tunnistus.php?viesti=Istunto vanhentunut!", 500);
        }
        else{
            document.getElementById('kellonaika').innerHTML = aika;
        }
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_aika): "+virhe.description;
    }
}

// Tämä näyttää kellonajan (index.php):
function nayta_aika_lm(aika)
{
    try{
        if(aika == "Istunto vanhentunut!"){
            pysayta_metodit_lm_kuvat()
            siirra_hitaasti("../tunnistus.php?viesti=Istunto vanhentunut!", 500);
        }
        else{
            document.getElementById('kellonaika').innerHTML = aika;
        }
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_aika): "+virhe.description;
    }
}

function kommentoi(teema_id, aihe, viesti, emoid){

    try{
        //alert("emoid: "+emoid);
       kysely = "kysymys=kommenttilomake"+
                "&aihe="+aihe+"&viesti="+viesti+"&teema_id="+teema_id+
                "&emoviesti_id="+emoid;
       toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_kommenttilomake','post', 'text',0);
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/kommentoi): "+virhe.description;
    }
}
// Näyttää kommenttilomakkeen
function nayta_kommenttilomake(tulosHTML){
    try{
        
        document.getElementById("muokkauslaatikko").innerHTML = tulosHTML;

        document.getElementById("ilmoitus2").innerHTML = "Kommentin kirjoitus";
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js): "+virhe.description;
    }
}
/*******************************************************************************/
/* Tämä vie viestin muokkaus lomakkeen pyynnön palvelimelle. */
function hae_viestin_muokkaus(id, teema_id, aihe, viesti, emoid, taso){
    //alert("emoid: "+emoid);
    try{
        kysely = "kysymys=muokkauslomake&viesti_id="+id+
            "&muok_aihe="+aihe+"&muok_viesti="+viesti+"&teema_id="+teema_id+
            "&emoviesti_id="+emoid+"&taso="+taso;

        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_viestin_muokkauslomake','post', 'text',0);
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/hae_viestin_muokkaus): "+virhe.description;
    }
}

/* Tämä näyttää viestin muokkaus lomakkeen eli kyselyn tuloksen. */
function nayta_viestin_muokkauslomake(tulosHTML){
    try{
        document.getElementById("muokkauslaatikko").innerHTML = tulosHTML;

        document.getElementById("ilmoitus2").innerHTML = "Viestin muokkaus";
        
        // Tässä oli ovela ajatus, mutta IE ei suostunut laittamaan
        // lomaketta lomakkeen sisään. Voi olla hyväkin. Täten turha.
        /*id = tulosxml.getElementsByTagName("id")[0].childNodes[0].nodeValue;

        elem_id = "tila"+id;
        lomake = tulosxml.getElementsByTagName("html")[0].childNodes[0].nodeValue;

        // Alla IE ei toimi, jos lomakkeen yrittää työntää toisen sisään!
        document.getElementById("muokkauslaatikko").innerHTML = lomake;

        document.getElementById("ilmoitus").innerHTML = "Viestin muokkaus";*/
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js): "+virhe.description;
    }
}
/*******************************************************************************/

function viestin_peruutus(){
    try{
        document.getElementById("muokkauslaatikko").innerHTML = "";
        document.getElementById("ilmoitus2").innerHTML =
                "Viestin luonti/muokkaus peruutettu";
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/viestin_peruutus): "+virhe.description;
    }
}

/**
 * Fiksumpi eli yleisempi tapa yllä olevista (mutta miten kahta muuttujaa
 * saa tulemaan? Taitaa onnistua vain responsxml:n avulla).
 * */
function nayta_ajax_vastaus(tulos, id)
{
    try{
        document.getElementById(id).innerHTML = tulos;
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_vastaus): "+virhe.description;
    }
    
}


/*******************************************************************************/
/**
 * Hakee sellaisten kuvien lkm:n,
 * jotka ovat tulleet edellisen uloskirjautumisen jälkeen.
 */
function hae_uudet_kuvat_lkm(){

    try{
        kysely = "kysymys=uudet_kuvat_lkm";
        toteutaAJAX('ajax_ja_js/ajax_kyselyt_kuvat.php',kysely,
                    'nayta_uudet_kuvat_lkm','post', 'text',0);
        ajastin4 = setTimeout(function()
        {
            hae_uudet_kuvat_lkm();
        },
        20000);
    }

    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/hae_uudet_kuvat_lkm): "+virhe.description;
    }

}

// Tämä näyttää uusien kuvien lukumäärän:
function nayta_uudet_kuvat_lkm(tuloshtml)
{
    try{
        document.getElementById('uudet_kuvat_lkm').innerHTML =
            "("+tuloshtml+" uutta)";
        
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_uudet_kuvat_lkm): "+virhe.description;
    }
}


/*******************************************************************************/
/**
 * Vie palvelimelle kysymyksen yhdestä keskustelusta, jonka id ja
 * auki-tilan arvo tuodaan parametreina.
 *
 * HUOM! Jostakin syystä totuusarvot eivät mene perille muuten kuin numeroina!
 * (auki on lukuarvo 0 tai 1).
 */
function hae_keskustelu(id, auki){
    try{
        kysely = "kysymys=hae_keskustelu&keskustelun_id="+id+"&auki="+auki;

        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_keskustelu','post', 'xml',0);
    }

    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/hae_keskustelu): "+virhe.description;
    }
}

/**
 * Avaa tai sulkee keskustelun eli näyttää piilotetut kommentit tai piilottaa.
 *
 * HUOM! Selaimet eivät näytä yhdessä textNode-jutussa loputtomasti tekstiä,
 * vaan pilkkovat sen useampaan tekstisolmuun. Mozillassa tämä raja on kuulemma
 * pienin (4K), minkä takia ongelma tuli näkyviin vain siinä. Pitää siis
 * ottaa huomioon, että teksti voi olla jaettu useampaan solmuun.
 * */
function nayta_keskustelu(tulosxml){
    try{
        //alert(tulosxml);
        //document.getElementById("ilmoitus").innerHTML = tulosxml;
        keskustelu = ""; // Keskustelu tekstinä (tageineen kaikkineen)
        kesk_id = tulosxml.getElementsByTagName("k_id")[0].childNodes[0].nodeValue;
                                                    
        keskustelusolmut = tulosxml.getElementsByTagName("kesk")[0].childNodes;
        //alert("tekstisolmuja "+keskustelusolmut.length+" kpl");

        // TÄMÄ SIIS NÄYTTÄÄ VAIN 4K TEKSTIÄ MOZILLASSA, MUISSA YL. 32K:
        //keskustelu = tulosxml.getElementsByTagName("kesk")[0].childNodes[0].nodeValue;

        // Tämä näyttää miten paljon tekstiä vaan!
        for(i = 0; i<keskustelusolmut.length; i++){
            keskustelu += keskustelusolmut[i].nodeValue;
        }

        //alert("kesk:"+keskustelu);
        document.getElementById(kesk_id).innerHTML = keskustelu;
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_keskustelu): "+virhe.description;
    }
}
/*******************************************************************************/
/* Lähettää pyynnon viestien päivityksestä, eli kaikkien sopivan ikäisten
 * viestien hakemisesta. */
function hae_viestit(omaid, vikaviesti_id, aikaraja, teema_indeksi){
    try{
        //alert("omaid:"+omaid+", teemaid: "+teema_indeksi+", vika: "+vikaviesti_id);

        /* Ehto liittyy hae_kaikki-nappiin. Se ei tiedä ajaxin kautta
         * vaihdettua teemaa, joten se haetaan täällä. Tämä on turvallista,
         * sivu on vakaa haun ajan (ei keskellä ajax-hakua) */
        if(teema_indeksi == 0){
            teemaid = hae_teema_id();
        }
        else{
            teemaid = teema_indeksi;
        }
        
        kysely = "kysymys=hae_viestit&omaid="+omaid+"&teema_id="+teemaid+
                "&vika_viesti_id="+vikaviesti_id+"&aikaraja="+aikaraja;

        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_viestit','post', 'text',1);

        // Teemakuvaus eli viestiluokan kuvaus:
        hae_teemakuvaus(teemaid);
    }

    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/hae_viestit): "+virhe.description;
    }
}

function nayta_viestit(tulosHTML){
    try{
        document.getElementById("sisaltoteksti").innerHTML = tulosHTML;
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_viestit): "+virhe.description;
    }
}
/*******************************************************************************/
function hae_teemakuvaus(teema_id){
    try{
        var kuvaus = "";

        kysely = "kysymys=hae_teemakuvaus&teema_id="+teema_id;

        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_teemakuvaus','post', 'text',0);
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/hae_teemakuvaus): "+virhe.description;
    }
}

function nayta_teemakuvaus(tulosHTML){
    try{
        //alert("nayta_teemakuvaus: "+tulosHTML);
        var kuvaus = "";
        document.getElementById("teemakuvaus").innerHTML = tulosHTML;
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_teemakuvaus): "+virhe.description;
    }
}

/*******************************************************************************/
/**
 * Palauttaa teema_id:n eli teemavalinnan luvun 'selectedIndex+1', joka
 * vastaa viestiluokkien lukuvastineita teemat.php:ssa.
 *
 * value-arvossa otetaan huomioon se, että K-18 mahdollisesti puuttuu.
 *
 * HUOM! Tämä aiheuttaa ongelmia, jos sitä haetaan kesken ajax-kutsun, koska
 * silloin sivun html-sisältö voi olla tyhjänä!
 */
function hae_teema_id(){
    var id = 1; // oletus
    try{
        var valinta = document.getElementById("teemavalinta");
        if(valinta != null){
            id = valinta.value;
        }
        else{
            id = 1; /* */
        }
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/hae_teema_id): "+virhe.description;
        id = 1;
    }
    return id;
}

/*******************************************************************************/
function aseta_luokka(onAlaik, omaid, vikaviesti_id, aikaraja, valintaindex){
    try{
        //alert("Valintaindex: "+valintaindex+" ja onAlaik: "+onAlaik);
        hae_viestit(omaid, vikaviesti_id, aikaraja, valintaindex);
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/aseta_luokka): "+virhe.description;
    }
}


// Tämä viimeisen muokkauksen päivämäärän:
function nayta_muokkaus_pvm()
{
    var vikamuokkaus = "22.11.2009";
    document.getElementById('mpvm').innerHTML = vikamuokkaus;
    return vikamuokkaus;
}
/*
 * Näyttää tietoa viimeisestä muokkauksesta.
 */
function nayta_selitys()
{
    var viesti =    "<div id='muokkausselitys'><div id='selityslaatikko'><b>"+
                    "Muutokset "+nayta_muokkaus_pvm()+":</b><br />"+
                    "<p>Terveisin JP</p>"+
                    "<a onclick='sulje_selitys()'>Sulje</a></div></div>";

    document.getElementById('muokkausviesti').innerHTML = viesti;
}
/*
 * Sulkee viimeiseen muokkaukseen liittyvän selitysruudun.
 */
function sulje_selitys()
{
    document.getElementById('muokkausviesti').innerHTML = "";
}


// Näyttää päivitysnapin ohjeen kolme sekuntia ilmoitus-elementissä,
// kun hiiri menee päivitysnapin päälle.
function paivita_ohje(){
    try{
        document.getElementById('ilmoitus2').innerHTML =
        "T&auml;st&auml; painikkeesta viestit p&auml;ivittyv&auml;t "+
        "eli uusimmatkin tulevat n&auml;kyviin.";
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/paivita_ohje): "+virhe.description;
    }
}

// Tätä kutsutaan ladatessa. Kutsuu tarvittavia metodeita.
function kaynnista_metodit()
{
    //kaynnista_kello();
    //tarkkaile_osallistujia();
    //tarkkaile_ilmoituksia("ilmoitus2");
    //tarkkaile_viesteja();
    //tarkkaile_ilmoituksia();
    //hae_uudet_kuvat_lkm();
    
    
}





/* Tämä hoitaa ilmoituksien poiston, etteivät jää roikkumaan. Ylläolevan
 * parannettu versio */
function tarkkaile_ilmoituksia(ilmoituselem_id){
    try{
        var ilmoituselem = document.getElementById(ilmoituselem_id);
        var aika = 5000;
        var aika2 = 2000;

        if(ilmoituselem){
            var ilmoitus = ilmoituselem.innerHTML;
            if(ilmoitus !==""){
                
                setTimeout(function()
                {
                    ilmoituselem.innerHTML = "";
                },
                aika);
            }
        }

        // Toisto päälle joka tapauksessa:
        ajastin3 = setTimeout(function()
        {
            tarkkaile_ilmoituksia(ilmoituselem_id);
        },
        aika2);
    }
    catch(virhe){
        ilmoituselem.innerHTML =
            "Hetkonen.. (metodit.js/tarkkaile_ilmoituksia2): "+virhe.description;
    }
}



// Tätä kutsutaan ladatessa, jotta aika saataisiin sivulla vaihtumaan.
function kaynnista_kello()
{
    try{
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php','kysymys=kellonaika',
                'nayta_aika','post', 'text',0);
        ajastin1 = setTimeout(function()
        {
            kaynnista_kello();
        },
        1000);
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/kaynnista_kello): "+virhe.description;
    }
    
}

/* Hakee osallistujat ja aktiivisuusajat
 */
function tarkkaile_osallistujia()
{
    try{
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php','kysymys=hae_aktiivisuusajat',
                'nayta_osallistujat','post', 'text',0);
        ajastin2 = setTimeout(function()
        {
            tarkkaile_osallistujia();
        },
        10000);
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/tarkkaile_osallistujia): "+virhe.description;
    }
}

// Tämä näyttää osallistujat:
function nayta_osallistujat(tulosHTML)
{
    try{
        document.getElementById('kirjautuneet').innerHTML = tulosHTML;
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_osallistujat): "+virhe.description;
    }
}
/******************************************************************************/
// Tämä seuraa uusien viestien tuloa.
function tarkkaile_viesteja()
{
    try{
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php','kysymys=uudet_viestit_lkm',
                'nayta_uudet_lkm','post', 'xml',0);
        ajastin3 = setTimeout(function()
        {
            tarkkaile_viesteja();
        },
        5000);
    }

    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/tarkkaile_viesteja): "+virhe.description;
    }
}

// Tämä näyttää viimeisen viestihaun jälkeen tulleiden viestien lukumäärän:
function nayta_uudet_lkm(tulosxml)
{
    try{
        luokkatiedot = tulosxml.getElementsByTagName("luokka");
       
        for(i=0; i<luokkatiedot.length; i++){
            id = luokkatiedot[i].childNodes[0].childNodes[0].nodeValue;
            lkm = luokkatiedot[i].childNodes[1].childNodes[0].nodeValue;
            //alert("id: "+id+" ja lkm: "+lkm);

            if(lkm > 0){
                lkm = "<span class='lukemattomat'>"+lkm+"</span>";
            }
            document.getElementById(id).innerHTML = lkm;
        }
    }
    catch(virhe){
        /*document.getElementById("ilmoitus2").innerHTML =
                "Virhe (metodit.js/nayta_uudet_lkm): "+virhe.description;*/
    }
}

/******************************************************************************/
/**
 * Hakee sellaiset viestit, jotka ovat tulleet edellisen haun jälkeen.
 */
function hae_uudet_viestit(){

    try{
        kysely = "kysymys=viestihaku";
        toteutaAJAX('ajax_ja_js/ajax_kyselyt.php',kysely,
                    'nayta_uudet_viestit','post', 'text',0);
        ajastin4 = setTimeout(function()
        {
            hae_uudet_viestit();
        },
        20000);
    }

    catch(virhe){
        /*document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/hae_uudet_viestit): "+virhe.description;*/
    }

}

// Tämä pysäyttää ajastintoiminnot:
function pysayta_metodit()
{
    try{
        clearTimeout(ajastin1);
        //clearTimeout(ajastin2);
        clearTimeout(ajastin3);
        //clearTimeout(ajastin4);
    }

    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/pysayta_metodit): "+virhe.description;
    }
}




function siirra(uusiURL)
{
    try{
        window.location=uusiURL;
    }

    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/siirra): "+virhe.description;
    }
}

// Siirtää käyttäjän uudelle sivulle aikamillisek millisekunnin kuluttua.
function siirra_hitaasti(uusiURL, aikamillisek)
{
    try{
        // Alla huomaa JS:n ilmeisesti ainut keino saada parametri
        // parametrifunktion sisään. Muuten ei tajunnut uusiURL-muuttujaa.
        // Null-juttu kuulemma auttaa
        // IE:n roskien kerääjän epätäydellisyyttä.
        setTimeout(function()
            {
                window.location = uusiURL;
                location.reload(true);
                uusiURL = null;
            },
            aikamillisek);
    }

    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/siirra_hitaasti): "+virhe.description;
    }
}



// Näyttää viikonpäivän ja päivämäärän käyttäjän antamien tietojen mukaan.
function nayta_pvm(idpaiva, idkk, idvuosi, idpvm)
{
    try{
        var tulos = "";
        var paiva = document.getElementById(idpaiva).value;
        var kk = document.getElementById(idkk).value;
        var vuosi = document.getElementById(idvuosi).value;

        //Tarkistetaan tyhjät ja -1:set:
        if((paiva == "") || (paiva == -1) || (kk == "") || (kk == -1)) {
            if((vuosi == "") || (vuosi == -1)){
                tulos = "Aikaa ei asetettu";
            }
            else if((kk == "") || (kk == -1)){
                if(Number(vuosi) != NaN){   //Ei toimi!
                    tulos = vuosi;
                }
                else{
                    tulos = "Vuosi virheellinen!";
                }
            }
            else{
                if((Number(kk) != NaN) && (Number(vuosi) != NaN)){//Ei toimi!
                    tulos = kk+"/"+vuosi;
                }
                else{
                     tulos = "Vuosi tai kuukausi virheellinen!";
                }
            }
        }
        else{   // Kun ei tyhjiä:
            var pvm = new Date();
            pvm.setFullYear(vuosi,kk-1,paiva); //ASettaa uuden päivämäärän.
            // Kuukaudet metodi ottaa muodossa 0-11 (hassua)
            var viikonpaivataulukko=new Array(7);
            viikonpaivataulukko[0]="su";
            viikonpaivataulukko[1]="ma";
            viikonpaivataulukko[2]="ti";
            viikonpaivataulukko[3]="ke";
            viikonpaivataulukko[4]="to";
            viikonpaivataulukko[5]="pe";
            viikonpaivataulukko[6]="la";

            var viikonpaiva = viikonpaivataulukko[pvm.getDay()];
            if((viikonpaiva === undefined) || (paiva<1) ||
                (paiva>31) ||(kk<1) ||(kk>12))
            {
                 tulos = "VIRHEELLINEN PVM!";
            }
            else{
                tulos = viikonpaiva+" "+paiva+"."+kk+"."+vuosi;
            }
        }
    
        document.getElementById(idpvm).innerHTML = tulos;
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/nayta_pvm): "+virhe.description;
    }

}

// Kirjoittaa halutun pvm:n muutoksen kenttiin, joiden id-arvot
// annettu parametreina. Jos
// jokin mainitusta on undefined, palautetaan nykyinen pvm.
function muuta_pvm(muutos, idpaiva, idkk, idvuosi, idpvm)
{
    try{
        var paiva = document.getElementById(idpaiva).value;
        var kk = document.getElementById(idkk).value;
        var vuosi = document.getElementById(idvuosi).value;

        /* Jos jokin kentistä ei ole määritelty, annetaan nykyinen pvm: */
        var pvm = new Date();
        
        if((paiva == undefined) || (kk == undefined)||(vuosi == undefined) ||
            (paiva == -1) || (kk == -1)||(vuosi == -1) ||
            (paiva == "") || (kk == "")||(vuosi == "") ||
            (muutos == 'nyt')){
            pvm.setDate(pvm.getUTCDate());
        }
        else{
            pvm.setFullYear(vuosi,kk-1,paiva); //ASettaa entisen päivämäärän.
            pvm.setDate(pvm.getDate()+muutos);
        }
        
        document.getElementById(idpaiva).value = pvm.getDate();
        document.getElementById(idkk).value = pvm.getMonth()+1;
        document.getElementById(idvuosi).value = pvm.getFullYear();

        nayta_pvm(idpaiva, idkk, idvuosi, idpvm);
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/muuta_pvm): "+virhe.description;
    }
}

// Tyhjentää päivämäärät.
function tyhjenna_pvm()
{
    try{
        document.getElementById("paiva").value = "";
        document.getElementById("kk").value = "";
        document.getElementById("vuosi").value = "";
        document.getElementById("pvm_naytto").innerHTML = "";
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/tyhjenna_pvm): "+virhe.description;
    }
}
/**
 * Muuttaa päivämäärää nimen mukaisesti ja kirjoittaa uuden kenttiin, 
 * joiden id:t ovat "paiva"+id_erotin, "kk"+id_erotin ja "vuosi"+id_erotin.
 * Viikonpäivä ja pvm kirjoitetaan kenttään, jonka id on "pvm_naytto"+
 * id_erotin. 
 * @param {type} id_erotin
 * @returns {undefined}
 */
function nayta_ed(id_erotin)
{
    muuta_pvm(-1, "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
                  
    // Päivitys tapahtuman aikaan:
    if(id_erotin === "" || id_erotin === 0){
      id_erotin = 2;
      muuta_pvm(-1, "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
    }
}

function nayta_seur(id_erotin)
{
    muuta_pvm(1, "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
                  
    // Päivitys tapahtuman aikaan:
    if(id_erotin === "" || id_erotin === 0){
      id_erotin = 2;
      muuta_pvm(1, "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
    }
}

function nayta_seur_vko(id_erotin)
{
    muuta_pvm(7, "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
                  
    // Päivitys tapahtuman aikaan:
    if(id_erotin === "" || id_erotin === 0){
      id_erotin = 2;
      muuta_pvm(7, "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
    }
}

function nayta_ed_vko(id_erotin)
{
    muuta_pvm(-7, "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
                  
    // Päivitys tapahtuman aikaan:
    if(id_erotin === "" || id_erotin === 0){
      id_erotin = 2;
      muuta_pvm(-7, "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
    }
}
function nayta_nyk_pvm(id_erotin){
   
    muuta_pvm("nyt", "paiva"+id_erotin, "kk"+id_erotin, "vuosi"+id_erotin, 
                  "pvm_naytto"+id_erotin);
}
function nayta_pvm_havjaks(){
  nayta_pvm("paiva2", "kk2", "vuosi2", "pvm_naytto2");
}

function nayta_pvm_hav(){
  nayta_pvm("paiva", "kk", "vuosi", "pvm_naytto");
  
  // Muutetaan havaintojakson päivämäärä samaksi kuin havainnon, jos kyseessä
  // uusi havaintojakso (miten varmistus?)
  kirjoita_elementtiin("paiva2", hae_value("paiva", -123), 1);
  kirjoita_elementtiin("kk2", hae_value("kk", -123), 1);
  kirjoita_elementtiin("vuosi2", hae_value("vuosi", -123), 1);
  
  nayta_pvm("paiva2", "kk2", "vuosi2", "pvm_naytto2");
  
}

