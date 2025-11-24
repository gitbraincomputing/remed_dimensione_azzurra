
function eseguiRichiesta_combovalori(url,parameters,id) {

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
          http_request.onreadystatechange = function() { alertContents_combo_valori(http_request,id); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_combo_valori(http_request,id) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				result = http_request.responseText;	
				//document.getElementById('province_res'+id).innerHTML=result;
				var nome='#combovalori';				
				$(nome).html(result);				  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_combo_valori(id){
	var poststr ="";	
	poststr='idcombo='+id;
	var nome='#combovalori';	
	$(nome).html("<em>attendere il caricamento dei valori...</em>");		
	eseguiRichiesta_combovalori('valori_combo_ajax.php', poststr,id);
} 


function eseguiRichiesta_dis(url,parameters,id) {

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
          http_request.onreadystatechange = function() { alertContents_dis(http_request,id); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_dis(http_request,id) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				//result = http_request.responseText;	
				//var nome='#div_distretto';				
				$('#div_distretto').html(http_request.responseText);
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_distretto(id){
	var poststr ="";	
	poststr='asl='+id;
	//var nome='#div_distretto';	
	$('#div_distretto').html("<em>attendere la selezione dei distretti...</em>");	
	eseguiRichiesta_dis('distretti_ajax.php', poststr,id);
} 


function eseguiRichiesta_capsed(url,parameters,id) {

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
          http_request.onreadystatechange = function() { alertContents_capsed(http_request,id); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_capsed(http_request,id) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				result = http_request.responseText;	
				var nome='#cap_res'+id;				
				$(nome).html(result);
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_cap_sede(id,nome_com){
	var poststr ="";	
	poststr='nome_com='+nome_com+'&id_req='+id;
	var nome='#cap_res'+id;	
	$(nome).html("<em>attendere la selezione del cap...</em>");	
	eseguiRichiesta_capsed('cap_sed_ajax.php', poststr,id);
} 

function eseguiRichiesta_comsed(url,parameters,id) {

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
          http_request.onreadystatechange = function() { alertContents_comsed(http_request,id); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_comsed(http_request,id) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				result = http_request.responseText;	
				//document.getElementById('province_res'+id).innerHTML=result;
				var nome='#comuni_res'+id;				
				$(nome).html(result);						  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_comuni_sede(id_prov,id,nome_com){
	var poststr ="";	
	poststr='id_prov='+id_prov+'&nome_com='+nome_com+'&id_req='+id;	
	var nome='#comuni_res'+id;	
	$(nome).html("<em>attendere la selezione dei comuni...</em>");		
	eseguiRichiesta_comsed('comuni_sed_ajax.php', poststr,id);
} 


function eseguiRichiesta_prosed(url,parameters,id) {

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
          http_request.onreadystatechange = function() { alertContents_prosed(http_request,id); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_prosed(http_request,id) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				result = http_request.responseText;	
				//document.getElementById('province_res'+id).innerHTML=result;
				var nome='#province_res'+id;				
				$(nome).html(result);				  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_province_sede(nome_prov,id){
	var poststr ="";	
	poststr='nome_prov='+nome_prov+'&id_req='+id;
	var nome='#province_res'+id;	
	$(nome).html("<em>attendere la selezione delle province...</em>");		
	eseguiRichiesta_prosed('province_sed_ajax.php', poststr,id);
} 


function eseguiRichiesta_cap(url,parameters,id) {

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
          http_request.onreadystatechange = function() { alertContents_cap(http_request,id); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_cap(http_request,id) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				result = http_request.responseText;	
				switch (id) { 
				case 1: document.getElementById('cap_r').innerHTML=result;
				break;
				case 2: document.getElementById('cap_t').innerHTML=result;
				break;
				case 3: document.getElementById('cap_med').innerHTML=result;
				break;
				}		  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_cap(id,nome_com){
	var poststr ="";	
	poststr='nome_com='+nome_com+'&id_req='+id;
	switch (id) { 
	case 1: document.getElementById('cap_r').innerHTML="<em>attendere la selezione del cap...</em>";
	break;
	case 2: document.getElementById('cap_t').innerHTML="<em>attendere la selezione del cap...</em>";
	break;
	case 3: document.getElementById('cap_med').innerHTML="<em>attendere la selezione del cap...</em>";
	break;	
	}
	eseguiRichiesta_cap('cap_ajax.php', poststr,id);
} 
   
   

function post_ajax_1(formname,url_post,divagg)
{


if ($("#barra_operatore").is(":hidden")){
			//width_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
			//height_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
			//width_finestra = width_finestra - 181;
			$("#barra_operatore").slideDown(300/*, function(){ $("#barra_moreinfo").dropShadow({left: 4, top: 3, opacity: 0.4, blur: 4})}*/
										  );
		}
		
		else{
			/*$(".dropShadow").hide()*/
			$("#barra_operatore").slideUp(300);	
		}

 // prepare the form when the DOM is ready 
									//$(document).ready(function() { 
									//x=inizializza();
									var options2 = { 
									//   target:        '#layer_nero',   // target element(s) to be updated with server response 
									beforeSubmit:  showRequest_post,  // pre-submit callback 
									success:       showResponse_post  // post-submit callback 
									}; 

	$('#modifica').ajaxSubmit(options2);
	
									
									



return false;
	
}
   
   
   function post_ajax(formname,url_post,divagg)
{


if ($("#barra_moreinfo").is(":hidden")){
			//width_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
			//height_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
			//width_finestra = width_finestra - 181;
			$("#barra_moreinfo").slideDown(300/*, function(){ $("#barra_moreinfo").dropShadow({left: 4, top: 3, opacity: 0.4, blur: 4})}*/
										  );
		}
		
		else{
			/*$(".dropShadow").hide()*/
			$("#barra_moreinfo").slideUp(300);	
		}

 // prepare the form when the DOM is ready 
									//$(document).ready(function() { 
									//x=inizializza();
									var options2 = { 
									//   target:        '#layer_nero',   // target element(s) to be updated with server response 
									beforeSubmit:  showRequest_post,  // pre-submit callback 
									success:       showResponse_post  // post-submit callback 
									}; 

	$('#ricerca').ajaxSubmit(options2);
	
									
									



return false;
	
}


// post-submit callback 
function showResponse_post(responseText, statusText)  { 

   //chiamata tipo: ok;1;123#1456#antonio;
   //alert(responseText);

 $('#wrap-content').html(responseText);

  $('#layer_nero').toggle();
 
  
  

} 


function showRequest_post(formData, jqForm, options) { 

$('#layer_nero').toggle();
return true;

 
} 


function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}



function controlla_campi_operatori(fun)
{
	
query='';	
if ((fun=='edit')|| (fun=='add'))
{
 ds=document.getElementsByName("dir_sanitario");
 
 if (getCheckedValue(ds)=='y')
 {
 	data_inizio_ds=document.getElementById("san_inizio").value;
	if (query!='')
	query=query+"&san=si;"+data_inizio_ds;
	else
	query="san=si;"+data_inizio_ds;	
 }
 
 dt=document.getElementsByName("dir_tecnico");
 if (getCheckedValue(dt)=='y')
 {
 	data_inizio_dt=document.getElementById("tec_inizio").value;
	if (query!='')
	query=query+"&tec=si;"+data_inizio_dt;
	else
	query="tec=si;"+data_inizio_dt;
 }
 

}
 if (fun=='add')
 {
	user=document.getElementsByName("usr").value;
	if (query!='')
	query=query+"&usr="+user;
	else
	query="usr="+user;
	
	rpwd=document.getElementById('rpassword').value;
	if (query!='')
		query=query+"&pwd="+document.getElementById('password').value+";"+rpwd;
	else
		query="pwd="+document.getElementById('password').value+";"+rpwd;
 }
 
  if (fun=='edit')
 {
	id_ope=document.getElementById('id').value;
	if (query!='')
	query=query+"&id_ope="+id_ope;
	else
	query="id_ope="+id_ope;
 }

	
	$('#layer_nero').toggle();
var data = $.ajax({
  url: "controllo_operatore_ajax.php?"+query,
  async: false
 }).responseText;
$('#layer_nero').toggle();

if (data!='ok')
	  {
	  	alert(data);
	  	return false;
	  }
	  else{
	  
		 return true;
		
	  }
		
//document.getElementById("salva_operatore").disabled=true;
//$.get("controllo_operatore_ajax.php?"+query, controlla_get);
		
}
	

function eseguiRichiesta_sta(url,parameters,id,tutor) {

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
          http_request.onreadystatechange = function() { alertContents_sta(http_request,id,tutor); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_sta(http_request,id,tutor) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				result = http_request.responseText;	
				switch (id) { 
				case 1: document.getElementById('province').innerHTML=result;
				break;
				case 4: $('.province_tut_n'+tutor).html(result);
				break;				
				}					  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_stato(nome_prov,id,tutor){
	var poststr ="";
	if(tutor==undefined) tutor="";	
	switch (id) { 
	case 1: document.getElementById('province').innerHTML="<em>attendere la selezione delle province...</em>";
	break;	
	case 2: 
		$('.province_tut_n'+tutor).html('<em>attendere la selezione delle province...</em>'); 
		id=4;
	break;	
	}
	poststr='nome_prov='+nome_prov+'&id_req='+id+'&id_tutor='+tutor;
	/*if(id==1){
		document.getElementById('province').innerHTML="<em>attendere la selezione delle province...</em>";
	}else{
		window.opener.document.getElementById('province_res').innerHTML="<em>attendere la selezione dei comuni di appartenenza alla provincia selezionata...</em>";
	}*/
	eseguiRichiesta_sta('province_ajax.php', poststr,id,tutor);
} 

function eseguiRichiesta_pro(url,parameters,id,tutor) {

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
          http_request.onreadystatechange = function() { alertContents_pro(http_request,id,tutor); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_pro(http_request,id,tutor) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				result = http_request.responseText;	
				switch (id) { 
				case 1: document.getElementById('province').innerHTML=result;
				break;
				case 2: document.getElementById('province_res').innerHTML=result;
				break;
				case 3: $('.province_tut'+tutor).html(result);
				break;
				case 4: $('.province_tut_n'+tutor).html(result);
				break;
				case 5: document.getElementById('province_med').innerHTML=result;
				break;
				}
				/*if(id==1){
					document.getElementById('province').innerHTML = result;	
				}else{
					window.opener.document.getElementById('province').innerHTML = result;
				}
              document.getElementById('province').innerHTML = result;*/			  
            } else {
                alert('Si � verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_province(nome_prov,id,tutor){
	var poststr ="";	
	poststr='nome_prov='+nome_prov+'&id_req='+id+'&id_tutor='+tutor;
	if(tutor==undefined) tutor="";
	switch (id) { 
	case 1: document.getElementById('province').innerHTML="<em>attendere la selezione delle province...</em>";
	break;
	case 2: document.getElementById('province_res').innerHTML="<em>attendere la selezione delle province...</em>";
	break;
	case 3: $('.province_tut'+tutor).html('<em>attendere la selezione delle province...</em>');
	break;
	case 4: $('.province_tut_n'+tutor).html('<em>attendere la selezione delle province...</em>'); 
	break;
	case 5: document.getElementById('province_med').innerHTML="<em>attendere la selezione delle province...</em>";
	break;
	}
	/*if(id==1){
		document.getElementById('province').innerHTML="<em>attendere la selezione delle province...</em>";
	}else{
		window.opener.document.getElementById('province_res').innerHTML="<em>attendere la selezione dei comuni di appartenenza alla provincia selezionata...</em>";
	}*/
	eseguiRichiesta_pro('province_ajax.php', poststr,id,tutor);
} 


function eseguiRichiesta_com(url,parameters,id,tutor) {

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
          http_request.onreadystatechange = function() { alertContents_com(http_request,id,tutor); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_com(http_request,id,tutor) {

        if (http_request.readyState == 4) {			
            if (http_request.status == 200) {				
				result = http_request.responseText;	
				switch (id) { 
				case 1: document.getElementById('comuni').innerHTML=result;
				break;
				case 2: document.getElementById('comuni_res').innerHTML=result;
				break;
				case 3: $('.comuni_tut'+tutor).html(result);
				break;
				case 4: $('.comuni_tut_n'+tutor).html(result);
				break;
				case 5: document.getElementById('comuni_med').innerHTML=result;
				break;
				}		  
            } else {
                alert('Si è verificato un problema con la richiesta');
            }
        }

    }

   
   function carica_comuni(id_prov,id,nome_com,tutor){
	var poststr ="";
	if(tutor==undefined) tutor="";
	poststr='id_prov='+id_prov+'&nome_com='+nome_com+'&id_req='+id+'&id_tutor='+tutor;
	switch (id) { 
	case 1: document.getElementById('comuni').innerHTML="<em>attendere la selezione dei comuni...</em>";
	break;
	case 2: document.getElementById('comuni_res').innerHTML="<em>attendere la selezione dei comuni...</em>";
	break;
	case 3: $('.comuni_tut'+tutor).html('<em>attendere la selezione dei comuni...</em>'); 
	break;
	case 4: $('.comuni_tut_n'+tutor).html('<em>attendere la selezione dei comuni...</em>');
	break;
	case 5: document.getElementById('comuni_med').innerHTML="<em>attendere la selezione dei comuni...</em>";
	break;
	}
	eseguiRichiesta_com('comuni_ajax.php', poststr,id,tutor);
} 



function add_nome_ordine(campo, valore){
	 document.form0.nome.value = valore;
	 return true;
	}
	
function eseguiRichiesta_msg(url,parameters) {

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
          http_request.onreadystatechange = function() { alertContents_msg(http_request); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_msg(http_request) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				result = http_request.responseText;
              document.getElementById('azione_msg').innerHTML = result;			  
            } else {
                alert('Si � verificato un problema con la richiesta');
            }
        }

    }

   
   function add_azione_msg(){
	var poststr ="";	
	poststr='agente='+document.form0.agente.value+'&note='+document.form0.note.value+'&stato_msg='+document.form0.stato_msg.value+'&id_msg='+document.form0.id.value+'&operatore='+document.form0.operatore.value;
	 eseguiRichiesta_msg('add_azione_msg.php', poststr);
} 



function eseguiRichiesta_geo(url,parameters) {

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
          http_request.onreadystatechange = function() { alertContents_geo(http_request); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_geo(http_request) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				result = http_request.responseText;
              document.getElementById('geo').innerHTML = result;			  
            } else {
                alert('Si � verificato un problema con la richiesta');
            }
        }

    }

   
   function genera_indirizzo(){
	var poststr =""; 
	if (document.form0.citta.value!=""){
		poststr = 'address=' +document.form0.indirizzo.value+", "+document.form0.citta.value;
		document.form0.text_coordinate.value=document.form0.indirizzo.value+", "+document.form0.citta.value;}
		else{
		poststr='address=' + document.form0.indirizzo.value;
		document.form0.text_coordinate.value=document.form0.indirizzo.value;}
	
	  eseguiRichiesta_geo('geo.php', poststr);
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
   
   function eseguiRichiesta_dom(url,parameters) {

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
          http_request.onreadystatechange = function() { alertContents_dom(http_request); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_dom(http_request) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				result = http_request.responseText;
              document.getElementById('dir_dom').innerHTML = result;  
            } else {
                alert('Si � verificato un problema con la richiesta');
            }
        }

    }

    
   function get_domini(id,id_ord) {
     var poststr ='rid=' + id+'&idordine='+id_ord;
	  eseguiRichiesta_dom('cat_domini_ajax.php', poststr);
  
   }



   function eseguiRichiesta_zone(url,parameters) {

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
          http_request.onreadystatechange = function() { alertContents_zone(http_request); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_zone(http_request) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				result = http_request.responseText;
              document.getElementById('poss2').innerHTML = result;  
            } else {
                alert('Si � verificato un problema con la richiesta');
            }
        }

    }

    
   function get_zone(id) {
     var poststr ='zona=' + id;
	  eseguiRichiesta_zone('domini_ajax_zone.php', poststr);
  
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
                alert('Si � verificato un problema con la richiesta');
            }
        }

    }

    
   function get(id) {
     var poststr ='rid=' + id;
	  eseguiRichiesta('domini_ajax.php', poststr);
  
   }
   
   
   
 function eseguiRichiesta_reg(url,parameters, div) {

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
          http_request.onreadystatechange = function() { alertContents_reg(http_request,div); };
          http_request.open('POST', url, true);
          http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  http_request.setRequestHeader("Content-length", parameters.length);
		  http_request.setRequestHeader("Connection", "close");
		  http_request.send(parameters);

    }

    function alertContents_reg(http_request,div) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
				result = http_request.responseText;
				alert(div);
				
				var newdiv=document.createElement("div");
				newdiv.setAttribute('id', div);
				newdiv.innerHTML = result;
				document.body.appendChild(newdiv);

							
					
					
					
				//var oggetto=document.getElementById(div);
				//oggetto.innerHTML = result;  
				//}
				//else
				//alert("impossibile trovare oggetto div");
             
            } else {
                alert('Si � verificato un problema con la richiesta');
            }
        }

    }

    
   function get_reg(id,div,el) {
     var poststr ='rid=' + id+'&element='+el;
	  eseguiRichiesta_reg('domini_ajax_reg.php', poststr, div);
  
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

	if(confirm("Effettuare una copia della struttura corrente? ATTENZIONE: il nome della nuova struttura avr� lo stesso nome con il suffisso '_COPIA'.")) return true;
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

function ControlloEmail(email){	   
   StrMail=email;
   if (StrMail.length>6){
	  var pos;
	  var dotpos;
	  pos = StrMail.indexOf("@");
	  if ( (pos >= (StrMail.length-3) ) || (pos < 2) )
		 {
		alert("Il campo email non � stato inserito nel formato corretto.");		 
		 return (false);		 
		 }
	  pos=pos+1;
	  dotpos = StrMail.indexOf(".", pos);
	  if (dotpos > (StrMail.length-3) )
		 {
		alert("Il campo email non � stato inserito nel formato corretto.");		 
		 return (false);
		 }
	  Strmail=StrMail.substr(pos);
	  if ( (StrMail.length < 5) || (dotpos <= 0) )
		 {			 
		 alert("Il campo email non � stato inserito nel formato corretto.");		 
		 return (false);
		 }
	  }
   else
	  {
	  alert("Il campo email non � stato inserito nel formato corretto.");	  
	  return (false);
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
				
				if (nomi[i]=="agg"){
					x=document.getElementById('tel').checked;													
					if (x==false){
							y=eval('document.form0.email.value');
							if(y=="") {
								 alert("Inserire il campo email!");
								 return false;
							 }else{
							 if (ControlloEmail(y)==false) {								
								 return false;
							 }
							 }
						}
					}
				
				
				
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
		
			if(!confirm("Lo stato di questa struttura � settato a non corretta. Sei sicuro di voler salvare?"))
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

	alert('ATTENZIONE: se si modifica l\'annuario, il settore verr� cancellato e bisogner� selezionarlo nuovamente!');
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

function copyToList_set(from,to)
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

	var risultati = document.forms[0].testo;
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
//if (CercaSottoStringa(risultati.value,toList.options[i].value)==false)
	
function copyToList2(from,to)
{
  fromList = eval('document.forms[0].' + from);
  toList = eval('document.forms[0].' + to);
  
  if (toList.options.length > 0 && toList.options[0].value == 'temp')
  {
    toList.options.length = 0;
  }
  var sel = false;
  
  var trovato=false;	
  for (i=0;i<fromList.options.length;i++)
  {
    var current = fromList.options[i];    
	if (current.selected)
    {
      sel = true;
      txt = current.text;
    }
  }
  
  for(i=0;i<toList.options.length;i++){
	  var current_1 =toList.options[i];
	  txt_1=current_1.text;	  
	   if (txt_1==txt) trovato=true;
	  }
  
  if (from=='chosen2') trovato=false;
  if (!trovato){
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
}


function copyAllList(from,to)
{
  fromList = eval('document.forms[0].' + from);
  toList = eval('document.forms[0].' + to);
  
  //if (toList.options.length > 0 && toList.options[0].value == 'temp')
  //{
  // toList.options.length = 0;
  //}
  var sel = false;
  
 	
  for (i=0;i<fromList.options.length;i++)
  {
    var trovato=false;
	var current = fromList.options[i];    	
    sel = true;
    txt = current.text;
	val = current.value;	
	
	for(y=0;y<toList.options.length;y++){
	  var current_1 =toList.options[y];
	  txt_1=current_1.text;	  
	   if (txt_1==txt) trovato=true;
	  }
	
	if (!trovato){
	 	toList.options[toList.length] = new Option(txt,val);
		//fromList.options[i] = null;
	}
	var risultati = document.forms[0].province;
	risultati.value = '';
	if(from == 'possible2'){			 
	  for(z=0; z<toList.options.length; z++) {
			if(z==0) risultati.value = toList.options[z].value;
			else risultati.value = risultati.value + ',' + toList.options[z].value;				
	  	}
		} else {
			  for(z=0; z<fromList.options.length; z++) {
					if(z==0) risultati.value = fromList.options[z].value;
					else risultati.value = risultati.value + ',' + fromList.options[z].value;
			  }
		}  
  
 
  }
 
}



function copyAllList_ord(from,to,dest)
{
  fromList = eval('document.forms[0].' + from);
  toList = eval('document.forms[0].' + to);
  
  //if (toList.options.length > 0 && toList.options[0].value == 'temp')
  //{
  // toList.options.length = 0;
  //}  
  var sel = false;
  
 	
  for (i=0;i<fromList.options.length;i++)
  {
    var trovato=false;
	var current = fromList.options[i];    	
    sel = true;
    txt = current.text;
	val = current.value;
		
	for(y=0;y<toList.options.length;y++){
	  var current_1 =toList.options[y];
	  txt_1=current_1.text;	  
	   if (txt_1==txt) trovato=true;
	  }
	
	if (!trovato){		
	 	toList.options[toList.length] = new Option(txt,val);
	
	}
	
	
	//var risultati = document.forms[0].province;
	//risultati.value = '';
	risultati = eval('document.forms[0].' + dest);
	risultati.value = '';
	
	value=to.substr(0,3);
	if (value=='pos'){value=to}else{value=from}
	if(from == value){
		  for(z=0; z<toList.options.length; z++) {
				if(z==0) risultati.value = toList.options[z].value;
				else risultati.value = risultati.value + ',' + toList.options[z].value;
		  }

	} else {

		  for(z=0; z<fromList.options.length; z++) {
				if(z==0) risultati.value = fromList.options[z].value;
				else risultati.value = risultati.value + ',' + fromList.options[z].value;
		  }
	}
  
 
  }
 
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

  if (!sel) alert ('Selezionare almeno un elemento!');

	//var risultati = document.forms[0].lista;
	//dest='lista';
	risultati = eval('document.forms[0].' + dest);
	risultati.value = '';
	
	value=to.substr(0,3);
	if (value=='pos'){value=to}else{value=from}
	if(from == value){
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




function CercaSottoStringa(stringa,chiave) {
 
    // definiamo la stringa in cui effettuare la ricerca
    // e la sottostringa da ricercare
    //String stringa = "In questa frase � contenuta la parola Java?";
    //String sottostringa = "Java";

    // al momento non � stata effettuata alcuna ricerca
    // ne conseguito alcun risultato 		
    cerca = false;

    // calcoliamo lunghezza di stringa e sottostringa
    // la differenza sar� condizione di terminazione per il ciclo for
    max = stringa.length - chiave.length;

    // ricerchiamo la sottostringa ciclando il contenuto di quest'ultima
    test:
    for (i = 0; i <= max; i++) {
      n = chiave.length;
      j = i;
      k = 0;
      while (n-- != 0) {
        if (stringa.charAt(j++) != chiave.charAt(k++)) {
          continue test;
        }
      }

      // a questo punto � stata effettuata una ricerca
      // sar� possibile produrre un output			
      cerca = true;
      break test;
    }
	return cerca;
    // stampiamo l'output sulla base dell'esito della ricerca		
    //System.out.println(cerca ? "Tovata" : "Non Trovata");

}


var array_div = [];

//inizializza l'array
numerofield=100;

for (i=1; i < numerofield; i++) 
{
      array_div[i]=i;
}




function InStr(String1, String2)
{
    var a = 0;
    
    if (String1 == null || String2 == null)
        return (false);
    
    String1 = String1.toLowerCase();
    String2 = String2.toLowerCase();
    
    a = String1.indexOf(String2);
    if (a == -1)
        return false;
    else
        return true;
}


function DinamicDiv(divid,position,num_field)
{
	numerofield=num_field;
	if(position=='su')
	{
		//se voglio spostare verso l'alto allora position sara' settato su 
	var currentdiv = document.getElementById(""+divid+"");
	var parent=currentdiv.parentNode;
		//prendo il div corrente che passo alla funzione
		

		var prevdivn="";
		var nextdivn="";
		var k=1;
		var trovato=false;
		var nhidden=0;
		
		for (i=1; i < numerofield; i++) 
		{
		
		
			//scorro l'array fin quando non trovo il div corrente
			if((array_div[i]==divid))
			{
				
				
				for (j=i; j > 1; j--)
				{			
					  //prendo il l'id del div precedente
				      prevdivn=array_div[j-1];
					
					  //prendo il div corrente
					  nextdivn=divid;
					  
					
						 var prevdiv1 = document.getElementById(""+prevdivn+"");
						 
						  
						  if ((prevdiv1))
						  {
						  
						
						 	   trovato=InStr(prevdiv1.className,"hidden");
							
								if (trovato==false)
								{
									
									nhidden=0;
									k=j;
									array_div[k-1-nhidden]=nextdivn;
									array_div[i]=prevdivn;
							
									
									prevdiv1.parentNode.insertBefore(currentdiv, prevdiv1);
								   
									

								  //ref_node.parentNode.insertBefore(err_node, ref_node);
								//	parent.insertBefore(currentdiv,prevdiv1);
								    trovato=true;
								    j=0;
									return false;
								} 
								else
									nhidden++;
						  }		
				}	
			}
		}
		return false;
	}
	
	else if(position=='giu')
	{
		//se voglio spostare verso l'alto allora position sara' settato su 
		var currentdiv = document.getElementById(""+divid+"");
		var parent=currentdiv.parentNode;
		//prendo il div corrente che passo alla funzione
		var currentdiv = document.getElementById(""+divid+"");
		var prevdivn="";
		var nextdivn="";
		var k=1;
		var trovato=false;
		var nhidden=0;
		for (i=1; i < numerofield; i++) 
		{
			
				for (j=i; j < (numerofield-i); j++)
				{	
				
				if((array_div[i]==divid))
				{
					  //prendo il l'id del div precedente
				      nextdivn=array_div[j+1];
					  //prendo il div corrente
					  prevdivn=divid;
					  
					
						 var nextdiv1 = document.getElementById(""+nextdivn+"");
						  
						  
						  if (nextdiv1)
						  {
						 	   trovato=InStr(nextdiv1.className,"hidden");
								if (trovato==false)
								{
									k=j;
									//esco dal ciclo
									nhidden=0;
									var nextdiv = document.getElementById(""+nextdivn+"");
									array_div[k+nhidden+1]=prevdivn;
									array_div[i]=nextdivn;
									
									
									
								  	currentdiv.parentNode.insertBefore(nextdiv,currentdiv);
								    trovato=true;
								    j=numerofield;
									return false;
								} 
								else
								nhidden++;
						  }		
				}
				}	
		}
	
		
		return false;
	
	
	}
	
	else if(position=='elimina_c')
	{	
	var currentdiv = document.getElementById(""+divid+"");
	document.getElementById("et"+divid+"").disabled=true;
	document.getElementById("el"+divid+"").value="si";
	currentdiv.style.display='none';
	currentdiv.className=currentdiv.className +" hidden";
	
	
	}
	
	else if(position=='elimina')
	{	
	var currentdiv = document.getElementById(""+divid+"");
	
	var element=currentdiv
	if (document.getElementById("et"+divid+""))
	{
		idclasse="div_et"+divid;
		if (document.getElementById("div_et"+divid+""))
		{
		
			ogg=document.getElementById("div_et"+divid+"");
			$('#'+idclasse).removeClass("focused");
			if(!(/(^|\s)correct($|\s)/.test(ogg.className)))
					ogg.className=ogg.className + " correct";
		}
		document.getElementById("et"+divid+"").value="";
		document.getElementById("et"+divid+"").disabled=true;	
	}
	if (document.getElementById("ulfile"+divid+""))
	{
		if (document.getElementById("div_file"+divid+""))
		{
		ogg=document.getElementById("div_file"+divid+"");
		idclasse="div_file"+divid;
		ogg=document.getElementById("div_file"+divid+"");
		$('#'+idclasse).removeClass("focused");
		if(!(/(^|\s)correct($|\s)/.test(ogg.className)))
				ogg.className=ogg.className + " correct";
		}		
			document.getElementById("ulfile"+divid+"").disabled=true;	
			
		
			
	
	document.getElementById("ulfile"+divid+"").value="";
	}
	

	
	
	document.getElementById("et"+divid+"").disabled=true;	
	currentdiv.style.display='none';
	currentdiv.className=currentdiv.className +" hidden";
	
	
	
	
	}
	
	
	else if(position=='add')
	{
	   
		var firstavailable;
		var nextdivn="";
		var k=0;
		var trovato=false;
		
		
		for (i=1; i < numerofield; i++) 
		{
			 
			if(document.getElementById(""+i+""))
			{
				var f = document.getElementById(""+i+"");
				trovato=InStr(f.className,"hidden");
				if (trovato)
				{
					firstavailable=f;
					divid=i;
					i=1000;
				}
			}
		}
	
	if (trovato)
	{
	    var debugvalue=document.getElementById("debug").value
		document.getElementById("debug").value=debugvalue+" "+divid;
		document.getElementById('et'+divid).disabled=false;
		if (document.getElementById('ulfile'+divid)) 
			document.getElementById('ulfile'+divid).disabled=false;
		var parent=firstavailable.parentNode;
		var last=parent.lastChild;
		//parent.insertBefore(last,firstavailable);
		
		firstavailable.style.display='';
		firstavailable.className = firstavailable.className.replace("hidden", "");
	
	    //se voglio spostare verso l'alto allora position sara' settato su 
		
		
		//prendo il div corrente che passo alla funzione
		var currentdiv = firstavailable;
		var prevdivn="";
		var nextdivn="";
		var k=1;
		trovato=false;
		var nhidden=0;
		for (i=1; i < numerofield; i++) 
		{
			
				for (j=i; j < (numerofield-i); j++)
				{	
				
					if((array_div[i]==divid))
					{
						  //prendo il l'id del div precedente
					      nextdivn=array_div[j+1];
						  //prendo il div corrente
						  prevdivn=divid;
						  
						
							 var nextdiv1 = document.getElementById(""+nextdivn+"");
							
							  
							  if (nextdiv1)
							  {
							 	   trovato=InStr(nextdiv1.className,"hidden");
								   
								 
									if (trovato==false)
									{
									
										k=j;
										//esco dal ciclo
										nhidden=0;
										var nextdiv = document.getElementById(""+nextdivn+"");
										array_div[k+nhidden+1]=prevdivn;
										array_div[i]=nextdivn;
									  	currentdiv.parentNode.insertBefore(nextdiv,currentdiv);
									    trovato=true;
									    j=numerofield;
										return false;
									} 
									else
									nhidden++;
							  }		
					}
				}	
		}
	
	}
	else
	alert("non � possibile inserire ulteriori campi nel modulo creato");
	
	return false;
		
	
	
	}

}

function LessList_1(from)
{
  fromList = eval('document.forms[0].' + from);
  
  //if (toList.options.length > 0 && toList.options[0].value == 'temp')
  //{
  // toList.options.length = 0;
  //}
  var risultati = document.forms[0].province;
   risultati.value = ''; 
   fromList.options.length=0;
 
}
