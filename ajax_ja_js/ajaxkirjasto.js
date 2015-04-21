/* Tämä tiedosto sisältää AJAX-tekniikan toteuttavia funktioita,
jotka on muokattu kirjan "Kom igång med AJAX" tekijän Phil Ballardin
esimerkkien pohjalta. */

/* luoKyselyolio() Luo ja palauttaa olion, 
joka toimii tiedonvälityksen pohjana:*/

function luoKyselyolio()
{
    try{
        kyselyolio = new XMLHttpRequest(); /* Muut paitsi IE */
    }catch(virhe1){
        try{
            kyselyolio = new ActiveXObject("Msxml2.XMLHTTP"); /* IE:n tietyt versiot */
        }catch(virhe2){
            try{
                kyselyolio = new ActiveXObject("Microsoft.XMLHTTP"); /* IE:n muut versiot */
            }catch(virhe3){
                kyselyolio = false;
            }
        }
    }
    return kyselyolio;
} 

/**
Seuraava funktio toteuttaa GET-tyypin kyselyn. 
Funktio saa parametreinaan pyynnön urlin, kyselyn tiedot
ja kyselyolion. Funktio liittää urliin satunnaisluvun välimuistien
hämäämiseksi, avaa yhteyden ja lähettää kyselyn palvelimelle*/
function kysyGET(url, kysely, kyselyolio)
{
    satluku = parseInt(Math.random()*999999999);
    var urli = url+'?'+kysely+'&luku='+satluku;
    kyselyolio.open("GET", urli, true);
    kyselyolio.send(null);
}

/* Seuraava funktio toteuttaa POST-tyypin kyselyn. 
Funktio saa parametreinaan pyynnön urlin, kyselyn tiedot
ja kyselyolion. Funktio avaa yhteyden, liittää kyselyolioon otsikkorivin (header),
ja lähettää kyselyn palvelimelle*/

function kysyPOST(url, kysely, kyselyolio)
{
    kyselyolio.open("POST", url, true);
    kyselyolio.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    kyselyolio.send(kysely);
}

/** Tämän avulla metodikutsu ja syöte saadaan yhdistettyä. Vähän hassu, mutta
// Phil Ballardia voidaan kiittää...	*/
function kutsu_metodia(metodi, syote)
{
    eval(metodi+'(syote)');
}

/* reagoiTilamuutokseen määrittelee, mitä tehdään silloin, kun
kyselyolion "readyState" muuttuu, eli esimerkiksi palvelimelta tulee
kyselyyn vastaus. 

reagoi: kutsuttavan funktion nimi. Funktio on määritelty usein jossakin
muualla, esim. HTML-sivulla.
kyselyolio: aiemmin luotu olio, joka hoitaa vuoropuhelun.
vastaustyyppi: text tai xml
metodinimi: sen js-metodin nimi, joka hoitaa datan perille (esimerkiksi kirjoittaa
nettisivulle johonkin elementtiin)
nayta_odotusviesti: jos 1, niin odotusviesti annetaan. Muuten ei.
vastaustyyppi: 0 - teksti, 1 - xml*/

function reagoiTilamuutokseen(kyselyolio, vastaustyyppi, metodinimi,
                                nayta_odotusviesti)
{
    var vastaus = "";
    if(kyselyolio.readyState == 4) /* "ladattu"*/
    {
        if(kyselyolio.status == 200)/* Palvelimelta tullut "OK"*/
        {
            if(vastaustyyppi == 'text')
            {
                vastaus = kyselyolio.responseText;
            }
            else
            {
                vastaus = kyselyolio.responseXML;
            }
        }
        else // Palvelimelta tulee eiok-viesti:
        {
            vastaus = "..hetkinen.. palvelinkoodi="+kyselyolio.status;
        }
        kutsu_metodia(metodinimi, vastaus);
    }
    else if(nayta_odotusviesti === 1){
        vastaus = "Pikku hetki vain...";
        kutsu_metodia(metodinimi, vastaus);
    }
}

/* Funktio toteutaAJAX() kuroo yhteen funktiot ja saa toivon mukaan homman 
toimimaan. */
function toteutaAJAX(url, kysely, metodi, kyselytyyppi, vastaustyyppi, nayta_odotusviesti)
{
    var kyselyolio = luoKyselyolio();
	kyselyolio.onreadystatechange =
        function()
        {
            reagoiTilamuutokseen(kyselyolio, vastaustyyppi, metodi, nayta_odotusviesti);
        }
    if(kyselytyyppi === 'get')
    {
        kysyGET(url, kysely, kyselyolio);
    }
    else
    {
        kysyPOST(url, kysely, kyselyolio);
    }
}