<script type="text/javascript">

  function eseguiRichiesta(url,parameters) {

        var http_request = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {
                http_request.overrideMimeType('text/xml');
                // Vedi note sotto
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request) {
            alert('Giving up :( Non riesco a creare una istanza XMLHTTP');
            return false;
        }
          http_request.onreadystatechange = function() { alertContents(http_request); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents(http_request) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				result = http_request.responseText;
              document.getElementById('geo').innerHTML = result;			  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

    
   function get_domini(id) {
     var poststr ='rid=' + id;
	  eseguiRichiesta('cat_domini_ajax.php', poststr);
  
   }
   
   function genera_indirizzo(){
	var poststr =""; 
	if (document.form0.citta.value!=""){
		poststr = document.form0.indirizzo.value+", "+document.form0.citta.value;}
		else{
		poststr= document.form0.indirizzo.value;}
		document.form0.text_coordinate.value=poststr;
	
	  eseguiRichiesta('geo.php', poststr);
} 


function bannerBox(val){	
	value=val.substr(0,1);
	if (value==1){
		document.getElementById('banner_box').style.display='none';
		document.getElementById('not_banner_box').style.display='block';
		}else{
			document.getElementById('banner_box').style.display='block';
			document.getElementById('not_banner_box').style.display='none';
		}
	}
   
   function eseguiRichiesta(url,parameters) {

        var http_request = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {
                http_request.overrideMimeType('text/xml');
                // Vedi note sotto
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request) {
            alert('Giving up :( Non riesco a creare una istanza XMLHTTP');
            return false;
        }
          http_request.onreadystatechange = function() { alertContents(http_request); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents(http_request) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				result = http_request.responseText;
              document.getElementById('directory').innerHTML = result;  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

    
   function get_domini(id) {
     var poststr ='rid=' + id;
	  eseguiRichiesta('cat_domini_ajax.php', poststr);
  
   }






function eseguiRichiesta(url,parameters) {

        var http_request = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {
                http_request.overrideMimeType('text/xml');
                // Vedi note sotto
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request) {
            alert('Giving up :( Non riesco a creare una istanza XMLHTTP');
            return false;
        }
          http_request.onreadystatechange = function() { alertContents(http_request); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents(http_request) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				result = http_request.responseText;
              document.getElementById('poss2').innerHTML = result;  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

    
   function get(id) {
     var poststr ='rid=' + id;
	  eseguiRichiesta('domini_ajax.php', poststr);
  
   }
	 
   function data_attivazione(){
	document.form0.data_attivazione.value= document.form0.data_inizio.value;
	return true;
} 




function visualizzacombo() {
	  var f = document.form1;
      var wtipo_ricerca = f.tipo.options[f.tipo.selectedIndex].value;
      if (wtipo_ricerca=='E')	 
	  { 
	   f.linkinterno.disabled=true;
	   f.linkesterno.disabled=false;
	    f.linkesterno.value="http://";
	  }
	  else
	  {
	  f.linkinterno.disabled=false;
	  f.linkesterno.disabled=true;
	  f.linkesterno.value="";
	  }
	  
}

function controlla_categoria() {

	var f = document.frmain;
	var categoria = f.cat_download.options[f.cat_download.selectedIndex].value;
	if (categoria=='0') { 

		f.new_cat.disabled=false;
		f.new_cat.value="inserire il nome della categoria";
		f.new_cat.focus();
	
	} else	{
		f.new_cat.value="";
		f.new_cat.disabled=true;
	}

}

function controlla_stato_gruppo() {

	var f = document.frmain;

	if (f.status.value =='0') 
		confirm("Disabilitando un gruppo, l'accesso a tutti gli utenti partecipanti verr?disabilitato. Continuare?");
	//else if (f.status.value =='1') 
		//alert("L'accesso degli utenti partecipanti verr?riabilitato.");
}


function conferma() {

	if(confirm("Sicuro di voler cancellare questo dato?")) return true;
	else return false;
}

function conferma_copia_struct() {

	if(confirm("Effettuare una copia della struttura corrente? ATTENZIONE: il nome della nuova struttura avrà lo stesso nome con il suffisso '_COPIA'.")) return true;
	else return false;
}

function conferma_invio_fax(num) {

	if(confirm("Inviare un fax al numero "+num+"?")) return true;
	else return false;
}

function conferma_x_bisnonno() {

	if(confirm("Sicuro di voler cancellare il bisnonno di questa struttura?")) {
		document.form0.bisnonno.value='';
		document.form0.bisnonno_nome.value='';
		return true;
	}
	else return false;
}

function conferma_x_nonno() {

	if(confirm("Sicuro di voler cancellare il nonno di questa struttura?")) {
		document.form0.nonno.value='';
		document.form0.nonno_nome.value='';
		return true;
	}
	else return false;
}

function conferma_x_padre() {

	if(confirm("Sicuro di voler cancellare il padre di questa struttura?")) {
		document.form0.padre.value='';
		document.form0.padre_nome.value='';
		return true;
	}
	else return false;
}



function controlla_macro_default()
 {

	var f = document.form0;
	var macro = f.default1.options[f.default1.selectedIndex].value;
	
	if (macro=='0')
	 {
		f.macrostrutture.disabled=false;
		f.macrostrutture.focus();
	    f.possible1.disabled=true;
		f.possible.disabled=true;
		f.mod_descr.disabled=true;
		f.a.disabled=true;
		f.b.disabled=true;
		f.c.disabled=true;
		f.d.disabled=true;
		
		
		f.testo.disabled=true;
		f.chosen1.disabled=true;
		f.testo1.disabled=true;
		
	}
	 else	{
		f.macrostrutture.disabled=true;
		f.possible1.disabled=false;
		f.possible.disabled=false;
		f.mod_descr.disabled=false;
		f.chosen1.disabled=false;
		f.testo1.disabled=false;
		f.testo.disabled=false;
		f.a.disabled=false;
		f.b.disabled=false;
		f.c.disabled=false;
		f.d.disabled=false;
		
			}

}


function controlla_campi_form(campi,etichette) {

		 var s = new String;
		 var t = new String;
		 var nomi = new Array();
		 var nomi = new Array();
		 s=campi;
		 t = etichette;
		 nomi = s.split(",");
		 descrizioni=t.split(",");

		// verifica i campi del
		for (i=0;i<nomi.length;i++)  {

				x=eval('document.form0.' + nomi[i] +'.value');
				if(x=="") {
					 alert("Inserire il campo "+descrizioni[i]+"!");
					 return false;
				 }
		}
		return true;			 	
}


function controlla_parentela() {

		// verifica i campi della parentela
		if(document.form0.bisnonno_nome.value!="") {
			if(document.form0.nonno_nome.value=="") {
					 alert("La scelta del nonno e' obbligatoria!");
					 document.form0.nonno_nome.focus=true;
					 return false;
				 }
			if(document.form0.padre_nome.value=="") {
					 alert("La scelta del padre e' obbligatoria!");
					 document.form0.padre_nome.focus=true;
					 return false;
				 }
		}
		if(document.form0.nonno_nome.value!="") {
			if(document.form0.padre_nome.value=="") {
					 alert("La scelta del padre e' obbligatoria!");
					 document.form0.padre_nome.focus=true;
					 return false;
				 }
		}

		if(document.form0.corretto[0].checked==true) {
		
			if(!confirm("Lo stato di questa struttura è settato a non corretta. Sei sicuro di voler salvare?"))
				return false;
		}
		return true;			 	
}


function aggiungi_manager() {

	document.forms[0].manager.value ="manager";
	return true;
}

function regioni(provincia) {
	
	// definizione dell'array_regioni
	//alert(provincia);
	
	if(provincia>0) 
	{

		var stringa = document.form0.regioni_nascoste.value;
		var stringa1 = new Array();
		
		stringa1 = stringa.split(",");
		
		for(i=0; i<stringa1.length; i++) {

			var arra = new Array();
			arra = stringa1[i].split("-");
			if(arra[0]==provincia) {
				document.form0.regione.value= arra[1];
			}
		}

		
	}
	else alert('Selezionare una provincia dall\' elenco');
	return true;
}


function controlla_sede(n) {

	if(n==2) {
		document.form0.indirizzo.disabled=true;
		document.form0.cap.disabled=true;
		document.form0.tel.disabled=true;
		document.form0.fax.disabled=true;
		document.form0.centralino.disabled=true;
		document.form0.email.disabled=false;
		document.form0.http.disabled=false;
	}
	else if(n==1) {
		document.form0.indirizzo.disabled=false;
		document.form0.cap.disabled=false;
		document.form0.tel.disabled=false;
		document.form0.fax.disabled=false;
		document.form0.centralino.disabled=false;
		document.form0.email.disabled=false;
		document.form0.http.disabled=false;
	}
	return true;
}

function cambia_annuario(url) {

	x = eval(document.form0.annuario.value);
	if(url!="")
	window.location="top.php?page="+url+"&annuario="+x;
	else
	window.location="top.php?annuario="+x;
	return true;
}

function cambia_annuario_generic(url) {

	x = eval(document.form0.annuario.value);
	window.location=""+url+"&annuario="+x;
	return true;
}

function cambia_regione(url) {

	x = eval(document.forms[0].annuario.value);
	y = eval(document.forms[0].settore.value);
	z = eval(document.forms[0].regione.value);
	if(url!="")
	window.location="top.php?page="+url+"&annuario="+x+"&settore="+y+"&regione="+z;
	else
	window.location="top.php?annuario="+x+"&settore="+y+"&regione="+z;
	return true;
}

function cambia_finestra(url) {

	x = eval(document.forms[0].annuario.value);
	y = eval(document.forms[0].settore.value);
	z = eval(document.forms[0].regione.value);
	window.location=url+"?annuario="+x+"&settore="+y+"&regione="+z;
	return true;
}


function cambia_file(file) {

	x = eval(document.form0.annuario.value);
	window.location= file+"&annuario="+x;
	return true;
}


function cancella_settore() {

	alert('ATTENZIONE: se si modifica l\'annuario, il settore verrà cancellato e bisognerà selezionarlo nuovamente!');
	document.form0.settore.value = '';
	document.form0.settore_nome.value = '';
	return true;
}

var maxchars=50;

function CheckLength()
  {
  with (document.form0)
    {
    chars=dato.value
    if (chars.length > maxchars)
      {
      dato.value=chars.substr(0,maxchars);
      dato.blur();
      }
    chr.value=maxchars-dato.value.length;
    }
  }

function sel_tutti(n) {
	
	for (var j = 1; j <= n; j++) {
		box = eval("document.lista.scelta" + j); 
		if (box.checked == false) box.checked = true;
	}
}

function desel_tutti(n) {
	
	for (var j = 1; j <= n; j++) {
		box = eval("document.lista.scelta" + j); 
		if (box.checked == true) box.checked = false;
   }

}

function checkAll(elm,name){
  for (var i = 0; i < elm.form.elements.length; i++)
  	if (elm.form.elements[i].name.indexOf(name) == 0)
	    elm.form.elements[i].checked = elm.checked;
}


function controlla_scelte(n) {
	
	var trovato = false;
	
	for (var j = 1; j <= n; j++) {
		box = eval("document.lista.scelta" + j); 
		if (box.checked == true) trovato = true;
   }
   if(trovato==false) alert("selezionare almeno un settore");
	return trovato;
}

function controlla_cliente() {

	var f = document.frmain;
	var cliente = f.cliente.value;
	if (cliente=='y') { 

		f.tipo_intervento.disabled=false;
	
	} else	{

		f.tipo_intervento.disabled=true;
	}
}

function abilita_cliente(val) {
	
	if(val) {
		
		document.form0.tipo_intervento.disabled = false;
		document.form0.evidenziato.disabled = false;
		document.form0.note_cliente.disabled = false;
	}
	else {
		
		document.form0.tipo_intervento.disabled = true;
		document.form0.evidenziato.disabled = true;
		document.form0.note_cliente.disabled = true;
	}

}


function oscura_select_multipla() {

	if(document.form0.catmerc.value == 'n') {
		
		document.form0.possible.disabled = true;
		document.form0.chosen.disabled = true;
	}
	else {
		
		document.form0.possible.disabled = false;
		document.form0.chosen.disabled = false;
	}


}

function copyToList(from,to)
{
  fromList = eval('document.forms[0].' + from);
  toList = eval('document.forms[0].' + to);
  
  if (toList.options.length > 0 && toList.options[0].value == 'temp')
  {
    toList.options.length = 0;
  }
  var sel = false;
  for (i=0;i<fromList.options.length;i++)
  {
    var current = fromList.options[i];
    if (current.selected)
    {
      sel = true;
      /*if (current.value == 'temp')
      {
        alert ('Impossibile spostare questo elemento');
        return;
      }*/
      txt = current.text;
      val = current.value;
      toList.options[toList.length] = new Option(txt,val);
      fromList.options[i] = null;
      i--;
    }
  }
  
  if (!sel) alert ('Selezionare almeno una categoria merceologica!');

	var risultati = document.forms[0].cat_news;
	risultati.value = '';
	
	if(from == 'possible'){
		  for(i=0; i<toList.options.length; i++) {
				if(i==0) risultati.value = toList.options[i].value;
				else risultati.value = risultati.value + ',' + toList.options[i].value;
		  }

	} else {

		  for(i=0; i<fromList.options.length; i++) {
				if(i==0) risultati.value = fromList.options[i].value;
				else risultati.value = risultati.value + ',' + fromList.options[i].value;
		  }
	}
	//document.forms[0].mod_descr.value='';

}

function copyToList1(from,to)
{
  fromList = eval('document.forms[0].' + from);
  toList = eval('document.forms[0].' + to);
  
  if (toList.options.length > 0 && toList.options[0].value == 'temp')
  {
    toList.options.length = 0;
  }
  var sel = false;
  for (i=0;i<fromList.options.length;i++)
  {
    var current = fromList.options[i];
    if (current.selected)
    {
      sel = true;
      /*if (current.value == 'temp')
      {
        alert ('Impossibile spostare questo elemento');
        return;
      }*/
      txt = current.text;
      val = current.value;
      toList.options[toList.length] = new Option(txt,val);
      fromList.options[i] = null;
      i--;
    }
  }
  
  if (!sel) alert ('Selezionare almeno una directory!');

	var risultati = document.forms[0].directory;
	risultati.value = '';
	
	if(from == 'possible1'){
		  for(i=0; i<toList.options.length; i++) {
				if(i==0) risultati.value = toList.options[i].value;
				else risultati.value = risultati.value + ',' + toList.options[i].value;
		  }

	} else {

		  for(i=0; i<fromList.options.length; i++) {
				if(i==0) risultati.value = fromList.options[i].value;
				else risultati.value = risultati.value + ',' + fromList.options[i].value;
		  }
	}
	//document.forms[0].mod_descr.value='';

}

function copyToList2(from,to)
{
  fromList = eval('document.forms[0].' + from);
  toList = eval('document.forms[0].' + to);
  
  if (toList.options.length > 0 && toList.options[0].value == 'temp')
  {
    toList.options.length = 0;
  }
  var sel = false;
  for (i=0;i<fromList.options.length;i++)
  {
    var current = fromList.options[i];
    if (current.selected)
    {
      sel = true;
      /*if (current.value == 'temp')
      {
        alert ('Impossibile spostare questo elemento');
        return;
      }*/
      txt = current.text;
      val = current.value;
      toList.options[toList.length] = new Option(txt,val);
      fromList.options[i] = null;
      i--;
    }
  }
  
  if (!sel) alert ('Selezionare almeno una categoria merceologica!');

	var risultati = document.forms[0].province;
	risultati.value = '';
	
	if(from == 'possible2'){
		  for(i=0; i<toList.options.length; i++) {
				if(i==0) risultati.value = toList.options[i].value;
				else risultati.value = risultati.value + ',' + toList.options[i].value;
		  }

	} else {

		  for(i=0; i<fromList.options.length; i++) {
				if(i==0) risultati.value = fromList.options[i].value;
				else risultati.value = risultati.value + ',' + fromList.options[i].value;
		  }
	}
	//document.forms[0].mod_descr.value='';

}

function copyToList_ord(from,to,dest)
{
	fromList = eval('document.forms[0].' + from);
  toList = eval('document.forms[0].' + to);
  
  
  if (toList.options.length > 0 && toList.options[0].value == 'temp')
  {
    toList.options.length = 0;
  }
  var sel = false;
  for (i=0;i<fromList.options.length;i++)
  {
    var current = fromList.options[i];
    if (current.selected)
    {
      sel = true;
      /*if (current.value == 'temp')
      {
        alert ('Impossibile spostare questo elemento');
        return;
      }*/
      txt = current.text;
      val = current.value;
      toList.options[toList.length] = new Option(txt,val);
      fromList.options[i] = null;
      i--;
    }
  }
  
  if (!sel) alert ('Selezionare almeno una categoria merceologica!');

	risultati = eval('document.forms[0].' + dest);
	risultati.options.value = '';
	
	if(from == 'possible2'){
		  for(i=0; i<toList.options.length; i++) {
				if(i==0) risultati.options.value = toList.options[i].value;
				else risultati.options.value = risultati.options.value + ',' + toList.options[i].value;
		  }

	} else {

		  for(i=0; i<fromList.options.length; i++) {
				if(i==0) risultati.options.value = fromList.options[i].value;
				else risultati.options.value = risultati.options.value + ',' + fromList.options[i].value;
		  }
	}
	//document.forms[0].mod_descr.value='';

}
</script>