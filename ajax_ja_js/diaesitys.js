/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/* Diaesitys-luokka
 *
 * Luokka sisältää riippuvuuksia metodeihin tiedostossa kuvametodit.js.
 * */
function Diaesitys(par_id_alb, 
                    par_aloittavan_kuvan_nro,
                    par_kuvien_lkm,
                    par_kokoelmanimi,
                    par_suunta)
{
    this.aloittavan_kuvan_nro=par_aloittavan_kuvan_nro;
    

    // Muuttujat, joita kutsutaan setInterval-metodin sisällä (this. ei onnistu):
    var id_alb = par_id_alb;
    var kuvien_lkm=par_kuvien_lkm;
    var valiaika = 4000;
    var kuvan_nro = 1; // Oletus yksi
    var esitysajastin = 0;
    var kokoelmanimi = par_kokoelmanimi;
    var suunta = par_suunta;    // Kelataanko eteen vai taakse päin.

    // Tarkistetaan suunta:
    if(suunta != "taakse"){
        suunta = "eteen";
    }

    this.esitys_kaynnissa = false;


    // Metodit:
    this.get_id_alb = function(){return id_alb;}
    this.get_aloittavan_kuvan_nro = function(){return aloittavan_kuvan_nro;}
    this.get_kuvien_lkm = function(){return kuvien_lkm;}
    this.get_valiaika = function(){return valiaika;}
    this.set_valiaika = function(uusi){valiaika=uusi;}
    this.get_id_alb = function(){return id_alb;}

    // Näin toimii, mutta ei erillisenä metodina!
    this.starttaa_toisto = function(){
        try{
            this.esitys_kaynnissa = true;

            // Varmistetaan, ettei mennä indeksien yli:
            if(suunta == "eteen"){
                if(kuvan_nro > kuvien_lkm){
                    kuvan_nro = 1;
                }
            }
            else{
                if(kuvan_nro < 1){
                    kuvan_nro = kuvien_lkm;
                }
            }
            
            // Eka kuva näytetään heti, muut vasta tauon päästä:
            hae_diaesityskuva(id_alb, kuvan_nro, kuvien_lkm, kokoelmanimi);
            if(suunta == "eteen"){
                kuvan_nro++;
            }
            else{
                kuvan_nro--;
            }
            
            // Sitten käynnistetään toisto:
            esitysajastin = setInterval(function()
                {
                    // Varmistetaan, ettei mennä indeksien yli:
                    if(suunta == "eteen"){
                        if(kuvan_nro > kuvien_lkm){
                            kuvan_nro = 1;
                        }
                    }
                    else{
                        if(kuvan_nro < 1){
                            kuvan_nro = kuvien_lkm;
                        }
                    }
                    hae_diaesityskuva(id_alb, kuvan_nro, kuvien_lkm, kokoelmanimi);
                    if(suunta == "eteen"){
                        kuvan_nro++;
                    }
                    else{
                        kuvan_nro--;
                    }
                },
                valiaika);
        }
        catch(virhe){
            document.getElementById("ilmoitus2").innerHTML =
                "Virhe (kuvametodit.js/Diaesitys.starttaa_toisto()): "+
                virhe.description;
        }
    }
    this.kaynnista =
        function(){
            try{
                kuvan_nro = this.aloittavan_kuvan_nro;
                var paik_valiaika = valiaika;

                this.starttaa_toisto();
            }
            catch(virhe){
                document.getElementById("ilmoitus2").innerHTML =
                    "Virhe (kuvametodit.js/Diaesitys.kaynnista()): "+
                    virhe.description;
            }
        };// kaynnista()-metodin loppu


    this.lopeta =
        function(){
            try{
                clearInterval(esitysajastin);
                this.esitys_kaynnissa = false;

                var kuvadivi = document.getElementById('diaesitys');
                kuvadivi.style.display = "none";

                var nappidivi = document.getElementById('diaesitysnapit');
                nappidivi.style.display = "none";

                var dianumerodivi = document.getElementById('dianumerodivi');
                dianumerodivi.style.display = "none";

                // Näytetään sisalto_fixed-osa, joka on piilotettu:
                var sisalto = document.getElementById('sisalto_fixed');
                sisalto.style.display = "block";
            }
            catch(virhe){
                document.getElementById("ilmoitus2").innerHTML =
                    "Virhe (kuvametodit.js/Diaesitys.lopeta()): "+
                    virhe.description;
            }
        };// lopeta()-metodin loppu


    this.nopeuta =
        function(){
            // Pitää vissiin toisto lopettaa, jotta saa muutoksen menemään peril.
            clearInterval(esitysajastin);

            if(valiaika > 30000){
                valiaika = valiaika-10000;
            }
            else if(valiaika > 20000){
                valiaika = valiaika-5000;
            }
            else if(valiaika > 7000){
                valiaika = valiaika-3000;
            }
            else{
                valiaika = valiaika-1000;
            }
            if(valiaika < 500){
                valiaika = 500;
            }
            this.starttaa_toisto();
        };// hidasta()-metodin loppu

    this.hidasta =
        function(){
            // Pitää vissiin toisto lopettaa, jotta saa muutoksen menemään peril.
            clearInterval(esitysajastin);

            if(valiaika == 500){
                valiaika = valiaika+500;
            }
            else if(valiaika > 15000){
                valiaika = valiaika+10000;
            }
            else if(valiaika > 5000){
                valiaika = valiaika+3000;
            }
            else{
                valiaika = valiaika+1000;
            }
            if(valiaika > 100000){
                valiaika = 100000;
            }
            this.starttaa_toisto();
        };// nopeuta()-metodin loppu

    this.pysayta =
        function(){
            try{
                this.esitys_kaynnissa = false;
                clearInterval(esitysajastin);
            }
            catch(virhe){
                document.getElementById("ilmoitus2").innerHTML =
                    "Virhe (kuvametodit.js/Diaesitys.pysayta()): "+
                    virhe.description;
            }
        };// pysayta()-metodin loppu
        
    this.jatka =
        function(){
            try{
                this.pysayta();
                suunta = "eteen";
                kuvan_nro++;    // Näytetään heti seuraava
                this.starttaa_toisto();
            }
            catch(virhe){
                document.getElementById("ilmoitus2").innerHTML =
                    "Virhe (kuvametodit.js/Diaesitys.pysayta()): "+
                    virhe.description;
            }
        };// jatka()-metodin loppu

    this.kelaa_taakse =
        function(){
            try{
                this.pysayta();
                suunta = "taakse";
                kuvan_nro--;    // Näytetään heti edellinen
                this.starttaa_toisto();
            }
            catch(virhe){
                document.getElementById("ilmoitus2").innerHTML =
                    "Virhe (kuvametodit.js/Diaesitys.pysayta()): "+
                    virhe.description;
            }
        };// jatka()-metodin loppu
}



