var testo;
function stampa2(tab){
	var elem = " ";
	var tab_da_analizzare = "#"+tab;
	if(!($(tab_da_analizzare).hasClass("tabs-hide"))){
		elem += $(tab_da_analizzare).html();
	};
	
	
	var stili = "top=10, left=10, width=250, height=100, status=no, menubar=no, toolbar=no scrollbar=no";
	testo = window.open("", "", stili);
	testo.document.write("<html>\n");
	testo.document.write(" <head>\n");
	testo.document.write(" <link href=\"style/new_ui_print.css\" rel=\"stylesheet\" type=\"text/css\" media=\"print\" />\n");
	testo.document.write(" <title>remed - stampa</title>\n");
	testo.document.write(" </head>\n");
	testo.document.write("<body topmargin=50>\n");
	testo.document.write("<div class=\"stampa\"><img src=\"images/loading-bianco.gif\" /><br /><br /><br /><br /></div>\n");
	testo.document.write("<div>"+elem+"</div>\n");
	testo.document.write("</body>\n");
	testo.document.write("</html>");
	setTimeout("stoppa()",1325);
}

function stampa(tab){
	var elem = "";
	var tab_da_analizzare = "#"+tab;
	elem += $("#informazioni-utente").html();
	$(tab_da_analizzare).find(".tabs-container").each(
		function(){
			//$(this).css("border","1px solid #ff0000");
			//alert($(this).hasClass("tabs-hide"));
			if(!($(this).hasClass("tabs-hide"))){
				elem += $(this).html();
			}
			//alert( $(this).html());					
		}
	);
	
	
	var stili = "top=10, left=10, width=250, height=100, status=no, menubar=no, toolbar=no scrollbar=no";
	testo = window.open("", "", stili);
	testo.document.write("<html>\n");
	testo.document.write(" <head>\n");
	testo.document.write(" <link href=\"style/new_ui_print.css\" rel=\"stylesheet\" type=\"text/css\" media=\"print\" />\n");
	testo.document.write(" <title>remed - stampa</title>\n");
	testo.document.write(" </head>\n");
	testo.document.write("<body topmargin=50>\n");
	testo.document.write("<div class=\"stampa\"><img src=\"images/loading-bianco.gif\" /><br /><br /><br /><br /></div>\n");
	testo.document.write("<div>"+elem+"</div>\n");
	testo.document.write("</body>\n");
	testo.document.write("</html>");
	setTimeout("stoppa()",1325);
}
function stoppa(finestra){
		testo.window.stop();
		testo.window.print();
		testo.window.close();
}

//mandatory su moduli_medici
function manMedici(value,input){
	
	if (value=="s") {
	//...CODICE SE IL CARATTERE VIENE TROVATO...
		var a=document.getElementById("medico_vis"+input);
		a.className=" mandatory";		
	}else{
		var a=document.getElementById("medico_vis"+input);
		a.className=" nomandatory";				
	}	
}

//mandatory su impegnative
function manImpegnativa(value){
	
	if ((value.indexOf("44")!=-1)||(value.indexOf("PRIVATO")!=-1)) {
	//...CODICE SE IL CARATTERE VIENE TROVATO...
		$('#first_step').slideUp();
		$('#second_step').slideUp();
		var a=document.getElementById("autorizzazione_data");
		a.className=" mandatory";
		var b=document.getElementById("autorizzazione_prot");
		b.className=" mandatory";
		if (value.indexOf("PRIVATO")!=-1) b.className=" nomandatory";		
	}else{
		var a=document.getElementById("autorizzazione_data");
		a.className=" nomandatory";
		var b=document.getElementById("autorizzazione_prot");	
		b.className=" nomandatory";
		$('#first_step').slideDown();
		$('#second_step').slideDown();
	}
	if ((value.indexOf("26")!=-1)||(value.indexOf("08/2003")!=-1)) {
	//...CODICE SE IL CARATTERE VIENE TROVATO...
		//var a=document.getElementById("trattamento_data");
		//a.className=" mandatory";
		//var b=document.getElementById("trattamento_prot");	
		//b.className=" mandatory";
		var a=document.getElementById("prescrizione_data");
		a.className=" mandatory";
		var b=document.getElementById("prescrizione_prot");	
		b.className=" mandatory";
	}else{
		//var a=document.getElementById("trattamento_data");
		//a.className=" nomandatory";
		//var b=document.getElementById("trattamento_prot");	
		//b.className=" nomandatory";
		var a=document.getElementById("prescrizione_data");
		a.className=" nomandatory";
		var b=document.getElementById("prescrizione_prot");	
		b.className=" nomandatory";
	}	
	//alert(value);	
}

// JavaScript Document
function getSuggerimento(numero){
	if(numero==1) return "prova suggerimento";
}
function getSuggerimento_text(testo){
	return testo;
}


function load_content(div,page,elem)
{

//businessKey(2);
//alert("aggiornamento sistema in corso");
$("#layer_nero2").toggle();
	//$("#wrap-content").innerHTML="<div id=wrap-content>caricamento in corso...</div>";
$.get(page, function(data){
	$("#wrap-content").html(data);
	$("#layer_nero2").toggle();
  } );
}


function load_content_menu(div,page,elem)
{
//businessKey(2);
	$("#layer_nero2").toggle();
	//$("#wrap-content").innerHTML="<div id=wrap-content>caricamento in corso...</div>";
$.get(page, function(data){
	$("#wrap-content").html(data);
	$("#layer_nero2").toggle();
  } );
$("#menu").find('a').removeClass("sel");
	$(elem).addClass("sel");
}

function load_content_menu1(div,page,elem)
{
//businessKey(2);
//$("#layer_nero2").toggle();
$('#wrap-content').innerHTML="caricamento in corso...";
 $("#wrap-content").load(page,'', function(){
  // loading_page('loading');
 });
	$("#menu").find('a').removeClass("sel");
	$(elem).addClass("sel");
}

function load_content1(div,page,elem)
{
//businessKey(2);
$("#layer_nero2").toggle();
$('#wrap-content').innerHTML="";
 $("#wrap-content").load(page,'', function(){
   loading_page('loading');
 });
}


function getCookie(NameOfCookie){ 
	if (document.cookie.length > 0) { 
		begin = document.cookie.indexOf(NameOfCookie+"="); 
		if (begin != -1) 
		begin += NameOfCookie.length+1; 
		end = document.cookie.indexOf(";", begin);
		if (end == -1) end = document.cookie.length;
		return unescape(document.cookie.substring(begin, end)); 
		}
		else{
		return null; 
		}
}

function setCookie(NameOfCookie, value, expiredays) 
{ var ExpireDate = new Date ();
ExpireDate.setTime(ExpireDate.getTime() + (expiredays * 24 * 3600 * 1000));
document.cookie = NameOfCookie + "=" + escape(value) + 
((expiredays == null) ? "" : "; expires=" + ExpireDate.toGMTString());
}



$(document).ready(function(){
	
	$('.rounded').corners();
	
	
	
	//script per ricerca veloce
	$('#ffb1').flexbox('re_pazienti_ricerca_quick.php', {  
	  width: 120,
	  showArrow: false,
	  paging: false ,
	  maxVisibleRows: 8, 
	  noResultsText: 'nessuna corrispondenza trovata',  
	  method: 'POST'
	});   	
	
	//mostro nascondo il menu e vario la larghezza del content principale
	$("#contactLink").click(function(){
		aprichiudimenu(0);		
	});
	
	//mostro nascondo ricerca avanzata
	$(".aprichiudiricercaAvanzata").click(function(){
		
		
		if ($("#barra_moreinfo").is(":hidden")){
			$(".pannelli_top").hide();
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
		
	});
	
	
	
	$(".apriChiudiOperatore").click(function(){
		
		if ($("#barra_operatore").is(":hidden")){
			$(".pannelli_top").hide();
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
		
	});
	
	
	
	/* script per tab */

	$('#container-1').tabs({ remote: true, cache: false }, loading_page("loading"));
	$('#container-2').tabs({ remote: true,  cache: false }, loading_page("loading"));
	$('#container-3').tabs({ remote: true,  cache: false }, loading_page("loading"));
	$('#container-4').tabs({ remote: true,  cache: false }, loading_page("loading"));
	$('#container-5').tabs({ remote: true,  cache: false }, loading_page("loading"));
	
	/*$.elementReady('barratop', function(){
	loading_page("loading");
	});*/
	
     
});

function aprichiudimenu(forzaapertura){
		if (($("#menu").is(":hidden"))||(forzaapertura>1)){
			width_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
			//height_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
			if(navigator.userAgent.search(/msie/i)!= -1){
				width_finestra = width_finestra - 201;
				if( width_finestra<760){
					width_finestra = 760;
					$("#wrap-page").animate({
					width: width_finestra +201
					}, 1
					);	
				}
				else{
					$("#wrap-page").animate({
					width: "100%"
					}, 1
					);	
				}
				
			}
			else{
				width_finestra = width_finestra - 181;
				if( width_finestra<780){
					width_finestra = 780;
					$("#wrap-page").animate({
					width: width_finestra +181
					}, 1
					);	
				}
				else{
					$("#wrap-page").animate({
					width: "100%"
					}, 1
					);	
				}
			}
			
			$("#wrap-content").animate({
				width: width_finestra
				}, 300, function(){ $("#menu").slideDown("slow", function(){setAltezzaMenu();})}
			);
			
			
		}
		
		else{
			$("#menu").slideUp("slow", function() {
      									 $("#wrap-content").animate({ 
											width: "100%"
											}, 300 );
											//controlla se tutto il menù può essere visualizzato nell'area visualizzata della finestra
									
    									 }
			);	
		}

		
}

function setta_visual(){
	Preference=getCookie('menu');	
		if(Preference==2){
			setCookie('menu','1',365);}
		else{
			setCookie('menu','2',365);}
}

function visualizzamenu(){
	
	var Preference=getCookie('menu');	
	if ((Preference!=2)&&(Preference!=0)&&(Preference!=1))
     	{aperto=2;
		setCookie('menu','2',365);}
		
	if(Preference==2){
		aprichiudimenu(2);
	}

};

ddaccordion.init({
		headerclass: "headerbar", //Shared CSS class name of headers group
		contentclass: "submenu", //Shared CSS class name of contents group
		revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click" or "mouseover
		mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
		collapseprev: true, //Collapse previous content (so only one open at any time)? true/false
		defaultexpanded: [], //index of content(s) open by default [index1, index2, etc] [] denotes no content
		onemustopen: true, //Specify whether at least one header should be open always (so never all headers closed)
		animatedefault: false, //Should contents open by default be animated into view?
		persiststate: true, //persist state of opened contents within browser session?
		toggleclass: ["", "selected"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
		togglehtml: ["", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
		animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
		oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
			//do nothing
		},
		onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
			//do nothing
		}
	});

/* fix ridimensione pagina - ridai dimensioni ai div */
function setAltezzaMenu(){
	var altezzaMenu = document.getElementById("menu").offsetHeight;
	var altezzaWindow = document.documentElement.clientHeight;
	//alert("1:"+altezzaMenu+" 2:"+altezzaWindow);
	if((altezzaMenu -10) > altezzaWindow){
		//alert("1");
		$("#menu").css("height", "90%");
		$("#menu").css("overflow-y", "scroll");
		$("#menu").css("overflow-x", "hidden");
	}
	else{
		//alert("0");
		$("#menu").css("height", "auto");
		$("#menu").css("overflow", "hidden");
	}
}
function ridimensiona(){
	if ($("#menu").is(":hidden")){
		//do nothing
	}
	else {
		width_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
		//height_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
		if(navigator.userAgent.search(/msie/i)!= -1){
			width_finestra = width_finestra - 201;
			if( width_finestra<760){
				width_finestra = 760;
				$("#wrap-page").animate({
				width: width_finestra +201
				}, 1
				);	
			}
			else{
				$("#wrap-page").animate({
				width: "100%"
				}, 1
				);	
			}
		}
		else{
			setAltezzaMenu();
			width_finestra = width_finestra - 181;
			if( width_finestra<780){
				width_finestra = 780;
				$("#wrap-page").animate({
				width: width_finestra +181
				}, 1
				);	
			}
			else{				
				$("#wrap-page").animate({
				width: "100%"
				}, 1
				);	
			}
			
		}

		
		
		
		$("#wrap-content").animate({
			width: width_finestra
			}, 1
		);	
		
		
	}
}


//////////////////////////////// griglia	
				
	function doppioclick(elemento){
	window.location.href='re_pazienti.php?do=edit&id='+elemento;
		//alert("elemento selezionato: "+elemento);
	}
	
	function funzioni_tasto(com, grid){
		 if (com=='elimina')
			{
				
			   if($('.trSelected',grid).length>0){
				   if(confirm('chiamo delete.php per' + $('.trSelected',grid).length + 'elementi (clicca per visualizzarli)')){
					var items = $('.trSelected',grid);
					var itemlist ='';
					for(i=0;i<items.length;i++){
						itemlist+= items[i].id.substr(3)+",";
						alert(itemlist);
					}
				
				/*
				$.ajax({
				   type: "POST",
				   dataType: "json",
				   url: "delete.php",
				   data: "items="+itemlist,
				   success: function(data){
					alert("Query: "+data.query+" - Total affected rows: "+data.total);
				   $("#flex1").flexReload();
				   }
				 });*/
				}
				} else {
					return false;
				} 
				
				
			}
		else if (com=='aggiungi')
			{
				//alert('Add New Item Action');
				window.location.href='re_pazienti.php?do=add';
			   
			}   
	}
	
	function sortAlpha(com)
				{ 
				jQuery('#flex1').flexOptions({newp:1, params:[{name:'letter_pressed', value: com},{name:'qtype',value:$('select[name=qtype]').val()}]});
				jQuery("#flex1").flexReload(); 
				}
	
	//This function adds paramaters to the post of flexigrid. You can add a verification as well by return to false if you don't want flexigrid to submit			
	/*
	function addFormData()
		{
		
		//passing a form object to serializeArray will get the valid data from all the objects, but, if the you pass a non-form object, you have to specify the input elements that the data will come from
		var dt = $('#sform').serializeArray();
		$("#flex1").flexOptions({params: dt});
		
		

		return true;
		}
	
	*/
	$('#sform').submit
	(
		function ()
			{
				$('#flex1').flexOptions({newp: 1}).flexReload();
				return false;
			}
	);	
	
	///////////////////////////// fine griglia


function get_cert(){		
		$.ajax({
		   type: "POST",
		   url: "get_cert_ajax.php",
		   data: "id=check",
		   success: function(msg){ 
		   return msg;
		   }
			});
		}

function businessKey(id){

	//if (get_cert()=="1")
	//{
	
	//alert("qui"+id);
	//var session="prova";
	var dischi = new Array('D','E','F','G','H','I','L','M','N','O','P','Q','R','S','T','U','V','Z','X','Y','J');
	var lettura="";
	var i = 0;
	//alert("qui"+session);
	while(lettura==""&&i<dischi.length){
		lettura = read(dischi[i]+':\\\InfoCamere\\cert.cer');
		i++;
	}	
	if(id==3){
		//alert("x"+lettura);
		if ((lettura=="")||(lettura=="0")) 
			$(".acqbk").html("<strong>businesskey non rilevata</strong> <u>rileva nuovamente</u>");
			else
			getUser(lettura);
		}
	else getUser(lettura);
	

	function getUser(cert){		
		$.ajax({
		   type: "POST",
		   url: "cert_ajax.php",
		   data: "id="+id+"&certificato="+cert,
		   success: function(msg){
				if(id==3){
					var msg_arr=msg.split(";");
					if(msg_arr[0]==0){
						alert(msg_arr[1]);
						$(".acqbk").html("<strong>businesskey non rilevata correttamente.</strong><br/><u>rileva nuovamente</u>");
						}
					else{
						$("#key_firma").val($.trim(msg_arr[1]));
						$(".acqbk").html("<strong>businesskey acquisita</strong>");
					 }
				 }else{		   
					 if($.trim(msg)==0){
						$(".imgbk").attr("src","images/key_off.png");
						$(".imgbk").attr("alt","businesskey non rilevata");
						$(".imgbk_txt").text("businesskey non rilevata");				
					 }else{
						$(".imgbk").attr("src","images/key_on.png");
						$(".imgbk").attr("alt","businesskey rilevata");
						$(".imgbk_txt").text("businesskey rilevata");
					 }						 
				}
		   }
		 });
	}
	
	function read(myfile) {
	try
	//{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");}
	{netscape.security.PrivilegeManager.enablePrivilege("UniversalPreferenceRead");
	netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	}
	catch (e) {/*alert("Permission to read file denied.");*/ return '';}

	var file = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile );
	file.initWithPath(myfile);

	if (!file.exists()) {/*alert("File not found."); */return '';}

	var is =
	Components.classes["@mozilla.org/network/file-input-stream;1"].createInstance(
	Components.interfaces.nsIFileInputStream );
	is.init(file,0x01, 00004, null);
	var sis =
	Components.classes["@mozilla.org/scriptableinputstream;1"].createInstance(
	Components.interfaces.nsIScriptableInputStream );
	sis.init(is);
	var output = sis.read(sis.available());
	return output;
	}
	
	/*}else{
		if(id==3)
			$(".acqbk").html("<strong>businesskey non rilevata</strong> <u>rileva nuovamente</u>");			
	}*/
}

function avvisi_loading(tipoavv) {
	
	$("#loading_big").fadeOut('fast', function() {
			$('#barra_operatore').hide();				
		$("#loading_big_"+tipoavv).fadeIn(2000,function() {			
			if(navigator.userAgent.search(/msie/i)!= -1){				
				document.getElementById("layer_nero2").style.display="none";
				document.getElementById(eval("\"loading_big_"+tipoavv+"\"")).style.display="none";
				document.getElementById("loading_big").style.display="block";
			}	
			else{		
				$("#layer_nero").fadeOut(2000, function() {
					document.getElementById(eval("\"loading_big_"+tipoavv+"\"")).style.display="none";
					document.getElementById("loading_big").style.display="block";					
				});
				
			}
			
		});
	});
};
 
function loading_page(tipoavv) {
	
	
	$("#loading_big_loading").fadeOut('fast', function() {
			if(navigator.userAgent.search(/msie/i)!= -1){
				document.getElementById("layer_nero2").style.display="none";
				document.getElementById("loading_big_loading").style.display="block";
			}
			else{
				$("#layer_nero2").fadeOut(1000,function() {
					document.getElementById("loading_big_loading").style.display="block";
				});
			}
	});
};

/*************************
script aggiunta residenza tutor etc
************************/
function aggiungi_nuovo(gruppo_campi){
	valore_attuale = "";
	valore_da_stampare ="";
	$("#"+gruppo_campi).find("input:text").each(function() {
		valore_attuale+=$(this).val() + "##";
		valore_da_stampare+=$(this).val() + ", ";
		$(this).attr("value", "");
	});
	eval("valore_attuale_"+gruppo_campi+"=\""+valore_attuale+"\";");
	$("#"+gruppo_campi).find(".comunicazione").html("<div class=\"valore_comunicazione\"><a class=\"cambia_valore\" onclick=\"ripristina_nuovo('"+gruppo_campi+"')\"><img src=\"images/slide_up_notice.gif\" align=\"left\" /></a><strong>"+gruppo_campi+" attuale: </strong>"+valore_da_stampare+"<div class=\"annulla\"> <a onclick=\"ripristina_nuovo('"+gruppo_campi+"')\">clicca qui per annullare l'operazione di cambio "+gruppo_campi+"</a></div>").slideDown();
	$("#aggiungi_"+gruppo_campi).toggle();
	$("#flag_"+gruppo_campi).attr("value", "true");
	
	}

function ripristina_nuovo(gruppo_campi){
	valore_da_ripristinare = eval("valore_attuale_"+gruppo_campi);
	arr_valore_da_ripristinare = valore_da_ripristinare.split("##");
	i=0;
	$("#"+gruppo_campi).find("input:text").each(function() {
		valore_attuale+=$(this).val() + "##";
		$(this).attr("value", arr_valore_da_ripristinare[i]);
		i++;
	});
	$("#"+gruppo_campi).find(".comunicazione").slideUp();
	$("#aggiungi_"+gruppo_campi).toggle();
	$("#flag_"+gruppo_campi).attr("value", "false");
	document.getElementById("res_dec").style.display="none";
	
}

/*************************
fine script aggiunta residenza tutor etc
************************/




