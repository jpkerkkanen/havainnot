/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/* Tämä on ystävällisesti muotoiltu sivun
 * http://www.codetoad.com/javascript_get_selected_text.asp
 * esimerkin pohjalta*/
function hae_valittu_teksti()
{
    var txt = '';
    if (window.getSelection)
    {
        txt = window.getSelection();
    }
    else if (document.getSelection)
    {
        txt = document.getSelection();
    }
    else if (document.selection)
    {
        txt = document.selection.createRange().text;
    }
    
    return txt;
    /*document.aform.selectedtext.value =  txt;*/
}
/**
 * Lisää annetun merkin valinnan molemmilla puolelle. Jos valinta on tyhjä,
 * lisää kursorin kohdalle merkit. Ellei fokusta tekstialueessa, lisää merkit
 * tekstin loppuun.
 */
function lisaaMerkkipari(elementin_id, merkki)
{
    try{
        var elementti = document.getElementById(elementin_id);
        // MS:
        if (document.selection)
        {
            elementti.focus();

            var ie_valinta = document.selection.createRange();
            var valittu_teksti = ie_valinta.text;

            ie_valinta.text = merkki+valittu_teksti+merkki;
        }
        // Muut selaimet:
        else{
            var valinnan_alku = elementti.selectionStart;
            var valinnan_loppu = elementti.selectionEnd;


            elementti.value =
                elementti.value.substring(0, valinnan_alku)+
                merkki+
                elementti.value.substring(valinnan_alku,valinnan_loppu)+
                merkki+
                elementti.value.substring(valinnan_loppu);
            elementti.focus();

            // Tämä asettaa kursorin paikan (tai valinnan halutessaan)
            // +merkin pituus koska lisäykset pidensivät tekstiä.
            elementti.setSelectionRange(valinnan_loppu+merkki.length,
                                        valinnan_loppu+merkki.length);
        }
    }
    catch(poikkeus){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/lisaaMerkit): "+poikkeus.description;
    }
}

/**
 * Lisää annetun merkin (voi olla merkkijono) valinnan molemmilla puolelle,
 * ensin merkki1, loppuun
 * merkki2. Jos valinta on tyhjä,
 * lisää kursorin kohdalle merkit. Ellei fokusta tekstialueessa, lisää merkit
 * tekstin loppuun tai jonnekin.
 */
function lisaaEriMerkkipari(elementin_id, merkki1, merkki2)
{
    try{
        var elementti = document.getElementById(elementin_id);
        // MS:
        if (document.selection)
        {
            elementti.focus();

            var ie_valinta = document.selection.createRange();
            var valittu_teksti = ie_valinta.text;

            ie_valinta.text = merkki1+valittu_teksti+merkki2;

            // Tämä asettaa kursorin paikan (tai valinnan halutessaan):
            // Apua saatu 'http://www.webmasterworld.com/forum91/4527.htm'
            // To get cursor position, get empty selection range

            //EI VIELÄ TOIMI!!
            // Move selection start to 0 position
            //ie_valinta.moveStart ('character', -elementti.value.length);

            // The caret position is selection length
            /*paikka = ie_valinta.text.length;

            var val = elementti.createTextRange();
            val.collapse(true);
            val.moveEnd('character', paikka);
            val.moveStart('character', paikka);
            val.select();*/
        }
        // Muut selaimet:
        else{
            var valinnan_alku = elementti.selectionStart;
            var valinnan_loppu = elementti.selectionEnd;


            elementti.value =
                elementti.value.substring(0, valinnan_alku)+
                merkki1+
                elementti.value.substring(valinnan_alku,valinnan_loppu)+
                merkki2+
                elementti.value.substring(valinnan_loppu);
            elementti.focus();

            // Tämä asettaa kursorin paikan (tai valinnan halutessaan)
            // +1.merkin pituus koska lisäykset pidensivät tekstiä.
            elementti.setSelectionRange(valinnan_loppu+merkki1.length,
                                    valinnan_loppu+merkki1.length);
        }
    }
    catch(poikkeus){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/lisaaMerkit): "+poikkeus.description;
    }
}


/**
 * Lisää html-tagit valinnan molemmille puolelle.
 * Jos valinta on tyhjä,
 * lisää kursorin kohdalle merkit. Ellei fokusta tekstialueessa, lisää merkit
 * tekstin loppuun / alkuun (riippuu selaimesta).
 */
function lisaaTagit(elementin_id, elementin_tunnus, class_arvo, id_arvo)
{
    try{
        var elementti = document.getElementById(elementin_id);

        /* Muotoilua: Heittomerkkien kanssa oli hankaluuksia, joten
         * jätin ne tästä pois. Näyttää toimivan ilmankin.*/
        var class_koodi = "";
        if(class_arvo != ""){
            class_koodi = " class="+class_arvo;
        }
        var id_koodi = "";
        if(id_arvo != ""){
            id_koodi = " id="+id_arvo;
        }

        var alkutagi = "<"+elementin_tunnus+class_koodi+id_koodi+">";
        var lopputagi = "</"+elementin_tunnus+">";

        // MS:
        if (document.selection)
        {
            elementti.focus();

            var ie_valinta = document.selection.createRange();
            var valittu_teksti = ie_valinta.text;

            ie_valinta.text = alkutagi+valittu_teksti+lopputagi;
        }
        // Muut selaimet:
        else{
            var valinnan_alku = elementti.selectionStart;
            var valinnan_loppu = elementti.selectionEnd;

            elementti.value =
                elementti.value.substring(0, valinnan_alku)+
                alkutagi+
                elementti.value.substring(valinnan_alku,valinnan_loppu)+
                lopputagi+
                elementti.value.substring(valinnan_loppu);
            elementti.focus();

            // Tämä asettaa kursorin paikan (tai valinnan halutessaan)
            var lopputagin_pit = elementin_tunnus.length+3;

            // Mitataan lisätyt tagit mukaan:
            valinnan_loppu += alkutagi.length+lopputagi.length;

            elementti.setSelectionRange(valinnan_loppu-lopputagin_pit,
                                    valinnan_loppu-lopputagin_pit);
        }
    }
    catch(poikkeus){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/lisaaMerkit): "+poikkeus.description;
    }
}

/**
 * Lisää sulut valinnan molemmille puolelle.
 * Jos valinta on tyhjä,
 * lisää kursorin kohdalle merkit. Ellei fokusta tekstialueessa, lisää merkit
 * tekstin loppuun / alkuun (riippuu selaimesta).
 */
function lisaaSulut(elementin_id)
{
    try{
        var elementti = document.getElementById(elementin_id);

        /* Muotoilua: Heittomerkkien kanssa oli hankaluuksia, joten
         * jätin ne tästä pois. Näyttää toimivan ilmankin.*/

        var alkusulku = "(";
        var loppusulku = ")";

        lisaaEriMerkkipari(elementin_id, alkusulku, loppusulku);
    }
    catch(poikkeus){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/lisaaSulut): "+poikkeus.description;
    }
}

/**
 * Lisää annetun merkin valinnan alkuun. Jos valinta on tyhjä,
 * lisää kursorin kohdalle merkit. Ellei fokusta tekstialueessa, lisää merkit
 * tekstin loppuun tai loppuun (tuntuu riippuvan selaimsesta).
 */
function lisaaMerkki(elementin_id, merkki)
{
    try{
        var elementti = document.getElementById(elementin_id);
        // MS:
        if (document.selection)
        {
            elementti.focus();
            var ie_valinta = document.selection.createRange();
            var valittu_teksti = ie_valinta.text;

            /* Merkkien lisäys valinnan alkuun. */
            ie_valinta.text = valittu_teksti+merkki;
        }
        // Muut selaimet:
        else{
            var valinnan_alku = elementti.selectionStart;
            var valinnan_loppu = elementti.selectionEnd;

            elementti.value =
                elementti.value.substring(0, valinnan_alku)+
                elementti.value.substring(valinnan_alku,valinnan_loppu)+
                merkki+
                elementti.value.substring(valinnan_loppu);
            elementti.focus();
            // Tämä asettaa kursorin paikan (tai valinnan halutessaan)
            elementti.setSelectionRange(valinnan_loppu+merkki.length,
                                        valinnan_loppu+merkki.length);
        }
    }
    catch(poikkeus){
        document.getElementById("ilmoitus2").innerHTML =
            "Virhe (metodit.js/lisaaMerkit): "+poikkeus.description;
    }
}
