/********************* ESIKATSELUKUVAT ****************************************/
var ajaxkyselytiedosto_osoite = "ajax_ja_js/ajax_kyselyt.php";
function nayta_esikatselukuvat(kuvahtml){

    if(kuvahtml == "Istunto vanhentunut!"){
        pysayta_metodit_lm_kuvat();
        siirra_hitaasti("../tunnistus.php?viesti=Istunto vanhentunut!", 500);
    }
    else{
        if(document.getElementById('sisaltoteksti') != undefined){
        document.getElementById('sisaltoteksti').innerHTML = kuvahtml;

        // Piilotetaan moodivaihtojuttu:
        document.getElementById('esikatselusaato').style.display = "none";
        }
        else{
            document.getElementById("ilmoitus2").innerHTML =
                    "Esikatselukuville ei paikkaa!";
        }
    }
    
}

function nayta_esikatselukuvat_bongaus(kuvahtml){

    if(kuvahtml === "Istunto vanhentunut!"){
        pysayta_metodit_lm_kuvat();
        siirra_hitaasti("../tunnistus.php?viesti=Istunto vanhentunut!", 500);
    }
    else{
        if(document.getElementById('sisalto_fixed') != undefined){
        document.getElementById('otsikkopalkki').style.display = 'none';
        document.getElementById('sisalto_fixed').innerHTML = kuvahtml;
        }
        else{
            document.getElementById("ilmoitus2").innerHTML =
                    "Kuvalle ei paikkaa!";
        }
    }
}

function hae_esikatselukuvat(id, kuvia_rinnakkain, kokoelma){
    try{
        var metodinimi = "nayta_esikatselukuvat";
        if(kokoelma == "albumikuvat"){// Kuva::$KUVAT_BONGAUS
            kysely = "kysymys=hae_esikatselukuvat"+
                "&id_alb="+id+
                "&kokoelmanimi="+kokoelma+
                "&ikkunan_leveys="+hae_ikkunan_leveys()+
                "&kuvia_rinnakkain="+kuvia_rinnakkain;
        }
        else if(kokoelma == "bongauskuvat"){ //Kuva::$KUVAT_ALBUMIT
            kysely = "kysymys=hae_esikatselukuvat"+
                "&kokoelmanimi="+kokoelma+
                "&id_lj="+id+"&ikkunan_leveys="+hae_ikkunan_leveys()+
                "&kuvia_rinnakkain="+kuvia_rinnakkain;
            metodinimi = "nayta_esikatselukuvat_bongaus";
        }
        
        toteutaAJAX('../ajax_ja_js/ajax_kyselyt_kuvat.php',kysely,
                    metodinimi,'post', 'text',1);

        return false;   // False -> ei submit-lähetystä painikkeelle.
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/nayta_esikatselukuvat): "+virhe.description;
              
        return true;    // Virhetapauksessa lähetetään sama toiminto
                        // submit-toiminnolla palvelimelle.
    }
}

/*********** Esikatselukuvien koon muutos *************************************/
function pienenna_pikkukuvat(id, rivilla_max, kokoelmanimi){

    var kuvia_rin = "";
    try{
        if(document.getElementById('kuvia_rinnakkain') != undefined){
            kuvia_rin = document.getElementById('kuvia_rinnakkain').innerHTML;

            // Pienennetään lukua yhdellä, jos luku isompi kuin 1. Jos luku
            // on yli rivilla_max, palautetaan suurin mahdollinen luku eli
            // rivilla_max.
            if((kuvia_rin > 1) && (kuvia_rin < rivilla_max+1)){
                kuvia_rin--; // Päivittyy tästä suoraan nettisivulle!!
                hae_esikatselukuvat(id, kuvia_rin, kokoelmanimi);
            }
            else if(kuvia_rin > rivilla_max){
                kuvia_rin = rivilla_max;
                hae_esikatselukuvat(id, kuvia_rin, kokoelmanimi);
            }
            else{
                document.getElementById("ilmoitus2").innerHTML =
                            "Alle yhden ei onnistu!";
            }
        }
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/pienennä_pikkukuvat): "+virhe.description;
    }
    
}
function suurenna_pikkukuvat(id, rivilla_max, kokoelmanimi){

    var kuvia_rin = "";
    try{
        if(document.getElementById('kuvia_rinnakkain') != undefined){
            kuvia_rin = document.getElementById('kuvia_rinnakkain').innerHTML;

            // Suurennetaan lukua yhdellä, jos luku pienempi kuin rivilla_max.
            // Jos luku
            // on alle 1, palautetaan 1 ja jos luku on yli rivilla_max,
            // palautetaan suurin mahdollinen luku rivilla_max.
            if((kuvia_rin > 0) && (kuvia_rin < rivilla_max)){
                kuvia_rin++;             // Nettisivulla päivittyy tämä näin (!)
                hae_esikatselukuvat(id, kuvia_rin, kokoelmanimi);
            }
            else if(kuvia_rin < 1){
                kuvia_rin = 1;
                hae_esikatselukuvat(id, kuvia_rin, kokoelmanimi);
            }
            else{
                document.getElementById("ilmoitus2").innerHTML =
                            "Rivill&auml; n&auml;ytet&auml;&auml;n korkeintaan "+
                            rivilla_max+" kuvaa!";
            }
        }
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/suurenna_pikkukuvat): "+virhe.description;
    }
}


/*********************** DIAESITYSKUVAT ****************************************/
function nayta_kuvan_nro(kuvan_nro, kuvien_lkm){
    var nrodivi;
    try{
        // luodaan uusi elementti vain, ellei sellaista jo olemassa:
        if(document.getElementById('dianumerodivi')==null){
            body = document.getElementsByTagName("body")[0];
            nrodivi = document.createElement("div");

            nrodivi.setAttribute("id", "dianumerodivi");
            
            nrodivi.setAttribute("onclick", "nayta_napit()");

            body.appendChild(nrodivi);
        }
        else{// Muuten vain pannaan näkymään:
            nrodivi = document.getElementById('dianumerodivi');
            if(nrodivi.style.display == "none"){
                nrodivi.style.display = "inline";
            }
        }
        nrodivi.innerHTML = kuvan_nro+"/"+kuvien_lkm;
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/nayta_kuvan_nro): "+virhe.description;
    }
}
function nayta_diaesityskuva(kuvahtml){
    var kuvadivi;
    try{
        // luodaan uusi elementti vain, ellei sellaista jo olemassa:
        if(document.getElementById('diaesitys')==null){
            body = document.getElementsByTagName("body")[0];
            kuvadivi = document.createElement("div");

            kuvadivi.setAttribute("id", "diaesitys");
            //kuvadivi.setAttribute("onmouseover", "nayta_napit()");
            kuvadivi.setAttribute("onclick", "nayta_napit()");

            body.appendChild(kuvadivi);
        }
        else{// Muuten vain pannaan näkymään:
            kuvadivi = document.getElementById('diaesitys');
            if(kuvadivi.style.display == "none"){
                kuvadivi.style.display = "inline";
            }
        }
        // Tämän voisi tehdä nätimmin..
        kuvadivi.innerHTML = kuvahtml;

        // Piilotetaan sisalto_fixed-osa, ettei "roiku" kuvan alla:
        var sisalto = document.getElementById('sisalto_fixed');
        sisalto.style.display = "none";
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/nayta_diaesityskuva): "+virhe.description;
    }
}

function hae_diaesityskuva(id_alb, kuvan_nro, kuvien_lkm, kokoelma){
    try{
        // Näytetään kuvan nro:
        nayta_kuvan_nro(kuvan_nro, kuvien_lkm);

        // Haetaan kuva sitten:
        if(kokoelma == "albumikuvat"){
            kysely = "kysymys=hae_diaesityskuva_albumeista"+
                "&id_alb="+id_alb+"&ikkunan_leveys="+hae_ikkunan_leveys()+
                "&ikkunan_korkeus="+hae_ikkunan_korkeus()+
                "&kokoelmanimi="+kokoelma+
                "&kuvan_nro="+kuvan_nro;

            toteutaAJAX('../ajax_ja_js/ajax_kyselyt_kuvat.php',kysely,
                    'nayta_diaesityskuva','post', 'text',0);
        }
        else if(kokoelma == "bongauskuvat"){
            kysely = "kysymys=hae_diaesityskuva_albumeista"+
                "&id_lj="+id_alb+"&ikkunan_leveys="+hae_ikkunan_leveys()+
                "&ikkunan_korkeus="+hae_ikkunan_korkeus()+
                "&kokoelmanimi="+kokoelma+
                "&kuvan_nro="+kuvan_nro;

            toteutaAJAX('../ajax_ja_js/ajax_kyselyt_kuvat.php',kysely,
                    'nayta_diaesityskuva','post', 'text',0);

        }
        else{
            document.getElementById("ilmoitus2").innerHTML =
                "Tuntematon kokoelmanimi (kuvametodit.js/hae_diaesityskuva)";
        }
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/hae_diaesityskuva): "+virhe.description;
    }
}

/* Diaesityksen säätö: Toimintanapit piiloon */
function piilota_napit(){

    // luodaan uusi elementti vain, ellei sellaista jo olemassa:
    var napit = document.getElementById('diaesitysnapit');
    if(napit){
        napit.style.display = "none";
    }
}

/* Diaesityksen säätö: */
function nayta_napit(){

    // luodaan uusi elementti vain, ellei sellaista jo olemassa:
    var napit;
    if(document.getElementById('diaesitysnapit')==null){
        // Body
        body = document.getElementsByTagName("body")[0];
        napit = document.createElement("div");
        napit.setAttribute("id", "diaesitysnapit");

        nappihtml = "<button type='button' onClick='piilota_napit()' title='"+
                    "Napit saa takaisin klikkaamalla kuvaa'>Piilota napit</button>";
        nappihtml += "<span id='valiaika'></span>";
        nappihtml += "<button type='button' onClick='hidasta_diaesitys()'>Hidasta</button>";
        nappihtml += "<button type='button' onClick='pysayta_diaesitys()'>Pysäytä</button>";
        nappihtml += "<button type='button' onClick='nopeuta_diaesitys()'>Nopeuta</button>";
        nappihtml += "<button type='button' onClick='kelaa_taakse_diaesitys()'>Taaksepäin</button>";
        nappihtml += "<button type='button' onClick='jatka_diaesitys()'>Eteenpäin</button>";
        nappihtml += "<button type='button' onClick='lopeta_diaesitys()'>Lopeta</button>";

        napit.innerHTML = nappihtml;
        body.appendChild(napit);
        napit.innerHTML = nappihtml;
    }
    else{
        var nappidivi = document.getElementById('diaesitysnapit');
        nappidivi.style.display = "block";
    }
    
}

/* Käynnistää diaesityksen: */
var diaesitys;
function kaynnista_diaesitys(id_alb, aloittavan_kuvan_nro, kuvien_lkm, kokoelma){
    diaesitys = new Diaesitys(id_alb, aloittavan_kuvan_nro, kuvien_lkm, kokoelma, "eteen");
    diaesitys.kaynnista();
    nayta_napit();
    document.getElementById("valiaika").innerHTML = "Väliaika: "+
            diaesitys.get_valiaika()/1000+" s ";
}
/* Pysäyttää kuvien kelaamisen */
function pysayta_diaesitys(){
    diaesitys.pysayta();
}
/* Lopettaa diaesityksen ja sulkee esitysnäkymän */
function lopeta_diaesitys(){
    diaesitys.lopeta();
}
/* Lisää aikaa kuvien välillä */
function hidasta_diaesitys(){
    diaesitys.hidasta();
    document.getElementById("valiaika").innerHTML = "Väliaika: "+
            diaesitys.get_valiaika()/1000+" s ";
}
/* vähentää aikaa kuvien välillä */
function nopeuta_diaesitys(){
    diaesitys.nopeuta();
    document.getElementById("valiaika").innerHTML = "Väliaika: "+
            diaesitys.get_valiaika()/1000+" s ";
}
/* Käynnistää uudelleen.*/
function jatka_diaesitys(){
    diaesitys.jatka();
}
/* lähtee kelaamaan taaksepäin */
function kelaa_taakse_diaesitys(){
    diaesitys.kelaa_taakse();
}

// Vaihtaa kuvahakumoodin:*****************************************************
function vaihda_kuvahakumoodi(moodi){
    try{
        kysely = "kysymys=vaihda_kuvahakumoodi"+
                "&kuvahakumoodi="+moodi;

        toteutaAJAX('../ajax_ja_js/ajax_kyselyt_kuvat.php',kysely,
                    'nayta_kuvahakumoodi','post', 'text',1);

        return false;   // False -> ei submit-lähetystä painikkeelle.

    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/vaihda_kuvahakumoodi): "+virhe.description;
    }
}
function nayta_kuvahakumoodi(moodiviesti){
    try{
        document.getElementById("ilmoitus2").innerHTML = moodiviesti;
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/nayta_kuvahakumoodi): "+virhe.description;
    }
}
/* Vaihtaa kuvahakumoodin loppu*************************************************/

function suurenna_pikkukuvat(id, rivilla_max, kokoelmanimi){

    var kuvia_rin = "";
    try{
        if(document.getElementById('kuvia_rinnakkain') != undefined){
            kuvia_rin = document.getElementById('kuvia_rinnakkain').innerHTML;

            // Suurennetaan lukua yhdellä, jos luku pienempi kuin rivilla_max.
            // Jos luku
            // on alle 1, palautetaan 1 ja jos luku on yli rivilla_max,
            // palautetaan suurin mahdollinen luku rivilla_max.
            if((kuvia_rin > 0) && (kuvia_rin < rivilla_max)){
                kuvia_rin++;             // Nettisivulla päivittyy tämä näin (!)
                hae_esikatselukuvat(id, kuvia_rin, kokoelmanimi);
                
                // Pienennetään kuvien padding-arvoa, kun kuvia paljon, ettei
                // kuvat mene reunan yli. Haetaan ensin kaikki elementit, joiden
                // class-arvo on 'kuva': TÄSSÄ KUMMA BUKKI!
                /*if(kuvia_rin > 6){
                    var kuvat = new Array();
                    var elementit = document.getElementsByTagName("*");
                    var lkm_el = elementit.length;
                    /*alert("elementteja "+lkm_el+" kpl"); MIKSI EI TOIMI ILMAN TÄTÄ???!!!*

                    for(i=0; i< elementit.length; i++){
                        if(elementit[i].className == 'kuva'){
                            kuvat.push(elementit[i]);
                        }
                    }
                    alert("kuvia "+kuvat.length+" kpl");
                    for(i=0; i< kuvat.length; i++){                           
                        kuvat[i].style.padding = '0px 1px';
                    }
                }*/
            }
            else if(kuvia_rin < 1){
                kuvia_rin = 1;
                hae_esikatselukuvat(id, kuvia_rin, kokoelmanimi);
            }
            else{
                document.getElementById("ilmoitus2").innerHTML =
                            "Rivill&auml; n&auml;ytet&auml;&auml;n korkeintaan "+
                            rivilla_max+" kuvaa!";
            }
        }
    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/suurenna_pikkukuvat): "+virhe.description;
    }
}
/* IE aiheuttaa ongelmia seuraavassa. Se laukaisee tapahtuman niin
 * monta kertaa, että kone meinaa jumia. KORJAA BONGAUKSEEN MYÖS*/
function onResize_toteutus(){

    if(navigator.appName == "Microsoft Internet Explorer"){
        // IE jumittaa, joten toimintoa ei suoriteta siinä! Ilmeisesti
        // se laukaisee kokomuutoksen niin tiuhaan, jotta kone ei pysy
        // perässä. Muut selaimet kuuntelevat, milloin muutos on loppunut ja
        // vasta sitten laukaisevat tapahtuman.
    }
    else{
        if(document.getElementById('albumi') != undefined){
            if(document.getElementById('piilo_id') != undefined){
                id_alb = document.getElementById('piilo_id').innerHTML;

                // Seuraavassa pitäisi esikatselukuville ja yhdelle kuvalla
                // tehdä eri toteutukset. Jotenkin pitää olla tiedossa, onko
                // kysymyksessä yhden kuvan vai pikkukuvien näyttö ja sitten
                // vastaavaa metodia kutsua (tai hoitaa asia yhdellä metodilla).
                //hae_esikatselukuvat(id_alb, 4, "albumikuvat");
                //hae_kuva_ja_tiedot(id, id_kuva, kokoelma);
            }
            else{
                document.getElementById("ilmoitus2").innerHTML =
                    "Piilo_id kadoksissa!";
            }
        }
    }
}

/* Ikkunan koonmuutoksen aiheuttamat kevyet toimenpiteet bongaussivustossa.
 * IE aiheuttaa helposti ongelmia. Se laukaisee tapahtuman niin
 * monta kertaa, että kone meinaa jumia. */
function onResize_toteutus_bongaus1(){
    vie_muistiin_ikkunan_leveys();
    if(navigator.appName == "Microsoft Internet Explorer"){
        // IE jumittaa, joten raskaita toimintoja ei suoriteta siinä! Ilmeisesti
        // se laukaisee kokomuutoksen niin tiuhaan, jotta kone ei pysy
        // perässä. Muut selaimet kuuntelevat, milloin muutos on loppunut ja
        // vasta sitten laukaisevat tapahtuman.

    }
    else{
        
    }
}

/* Ikkunan koonmuutoksen aiheuttamat toimenpiteet bongaussivustossa.
 * IE aiheuttaa helposti ongelmia. Se laukaisee tapahtuman niin
 * monta kertaa, että kone meinaa jumia. */
function onResize_toteutus_bongaus2(id_lj){
    vie_muistiin_ikkunan_leveys();
    if(navigator.appName == "Microsoft Internet Explorer"){
        // IE jumittaa, joten raskaita toimintoja ei suoriteta siinä! Ilmeisesti
        // se laukaisee kokomuutoksen niin tiuhaan, jotta kone ei pysy
        // perässä. Muut selaimet kuuntelevat, milloin muutos on loppunut ja
        // vasta sitten laukaisevat tapahtuman.

    }
    else{
        if(document.getElementById('albumi') != undefined){
            if(id_lj != undefined){
                hae_esikatselukuvat(id_lj, 4, "bongauskuvat");
            }
            else{
                alert("Lajiluokan tunniste kadoksissa!");
            }
        }
    }
}

/****************************************** **********************************/
/**
 * Näyttää ison kuvan ruudulla ja pikakommentit myös.
 * @param {type} kuvahtml
 * @returns {undefined}
 * 
 * echo '<?xml version="1.0" encoding="'.$koodaus.'"?>';
    echo '<tiedot>';
    echo '<kohde_tyyppi>'.Pikakommentti::$KOHDE_KUVA_BONGAUS.'</kohde_tyyppi>';
    echo '<kohde_id>'.$kuva_id.'</kohde_id>';
    echo '<html>'.$html.'</html>';
    echo '</tiedot>';
 */
function nayta_kuva_ja_tiedot(kuvaxml){
    
    var kuvahtml = hae_xml_elementin_sisalto(kuvaxml, "html", "ei löydy");
    var kohde_tyyppi = hae_xml_elementin_sisalto(kuvaxml, "kohde_tyyppi", "ei löydy");
    var kohde_id = hae_xml_elementin_sisalto(kuvaxml, "kohde_id", "ei löydy");
    
    var kuvaelem;
    var id = "kuvaelementti";
    if(!document.getElementById(id)){
        // Body
        body = document.getElementsByTagName("body")[0];
        kuvaelem = document.createElement("div");
        kuvaelem.setAttribute("id", id);

        kuvaelem.innerHTML = kuvahtml;
        body.appendChild(kuvaelem);
    }
    else{
        var kuvaelem = document.getElementById(id);
        kuvaelem.innerHTML = kuvahtml;
        kuvaelem.style.display = "block";
    }
    
    // Näytetään pikakommentit:
    hae_pikakommentit(kohde_tyyppi, kohde_id);
}

function piilota_kuva_ja_pikakommentit(){
    piilota_elementti("kuvaelementti");
    piilota_elementti("pikakommenttilaatikko");
}


function hae_kuva_ja_tiedot(id_lj, id_kuva, id_hav, name_ikkunan_lev, name_ikkunan_kork, name_id_hav){
   try{
        var nayttometodi = "";
        var kysely = "kysymys=hae_kuva_ja_tiedot"+
            "&id_lj="+id_lj+
            "&"+name_ikkunan_lev+"="+hae_ikkunan_leveys()+
            "&"+name_ikkunan_kork+"="+hae_ikkunan_korkeus()+
            "&"+name_id_hav+"="+id_hav+
            "&id_kuva="+id_kuva;
        nayttometodi = "nayta_kuva_ja_tiedot";
        toteutaAJAX(ajaxkyselytiedosto_osoite,kysely,
                    nayttometodi,'post', 'xml',1);

    }
    catch(virhe){
        document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/hae_kuva_ja_tiedot): "+
                virhe.description+" id_lj: "+id_lj+" id_kuva: "+id_kuva+
                                    " id_hav="+id_hav;
    }
}
/******************************************************************************/
// Tätä kutsutaan ladatessa kuvat.php-tiedosto. Kutsuu tarvittavia metodeita.
function kaynnista_kuvametodit()
{
    kaynnista_kello_kuvat();
    tarkkaile_ilmoituksia();
}



