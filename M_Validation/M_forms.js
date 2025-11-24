var Site;
var validateform=false;
var home;
if (home!=true)
home=false;


function trim(stringa)
{
	while (stringa.substring(0,1) == ' ')
	{
	stringa = stringa.substring(1, stringa.length);
	}
	while (stringa.substring(stringa.length-1, stringa.length) == ' ')
	{
	stringa = stringa.substring(0,stringa.length-1);
	}
return stringa;
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

function tutorMan()
{
    var a = 0;
    
   alert("ok");
        return false;
}



function hidden_display(child,enable)
{

		child.disabled=enable;
		containerchild=child.parentNode;	

	/*	if (enable==false) 
			containerchild.style.display = "block";
		else
			containerchild.style.display = "none";
		*/	
		

}//end function


function check_condition(fat,child,value,action)
{
var father_n=document.getElementById(fat);

	for (var a = 0, aa = father_n.childNodes.length; a < aa; a++) 
	{
		el=father_n.childNodes[a];
	
		if (el.value)
		{
			
			father=el;
		}	
	}	
		

		
	var parent = child.parentNode;		


	var checkvalue=false;

	if (action=="anni_precedenti")
	{
	
		
		var data_father=father_n.value;
		
		if(data_father!=""){
			var diff=validateDate_anni(data_father,"W","P");
		
			if (diff!=false)
			{
				anni=(diff/(86400000*365.25));
				//alert(anni);
				if (anni<value)
				checkvalue=true;			
			}
		}
	
	}
	else
	{
		if (father_n.value==value)
		checkvalue=true;
	
	}


	if (checkvalue==true)
	{
		flushField(child);
		
		if (action=='hidden')
		{
			hidden_display(child,true);
			return false;
		}
		else
		{
			hidden_display(child,false);	
			parent.className = parent.className.replace(/correct/, "");
			parent.className = parent.className.replace(/mandatory/, "");
			parent.className = 'mandatory ' + parent.className;
			//warnField(child, "This field .",setfocus);
			
			return true;
		}
		
	}		
	else	
	{
		flushField(child);
		parent.className = parent.className.replace(/mandatory/, "");
		parent.className = parent.className.replace(/focused/, "");
		parent.className = parent.className.replace(/correct/, "");
		
	   return false;
		
	}


}





function MultiDimensionalArray(iRows,iCols)
{
var i;
var j;
   var a = new Array(iRows);
   for (i=0; i < iRows; i++)
   {
       a[i] = new Array(iCols);
       for (j=0; j < iCols; j++)
       {
           a[i][j] = "";
       }
   }
   return(a);
} 

function prepare(form) {
	

	form.addLOV = addLOV;
	form.addCalendar = addCalendar;
	form.addTimePicker = addTimePicker;
	form.addSubmit = addSubmit;
	form.addActionMenu = addActionMenu;
	form.addTopChecker = addTopChecker;
	form.addPaginator = addPaginator;
	form.addPlaceholder = addPlaceholder;
	form.addToggler = addToggler;
	form.addMandatory = addMandatory;
	if (!form.id) {
		form.setAttribute("id", "form" + (new Date()).getTime());
	}
	var inputs = document.getElementsByTagName("input");
	for (var i = inputs.length - 1; i >= 0; i--) {
		if (inputs[i].name == "sesi") {
			form.session = inputs[i];
		}
		if (inputs[i].name == "orgi") {
			form.organisation = inputs[i];
		}
		if (inputs[i].name == "acti") {
			form.actionid = inputs[i];
			form.def_acti = inputs[i].value;
		}
		if (inputs[i].name == "startfrom") {
			form.startFrom = inputs[i];
		}
		if (inputs[i].name == "fetchcount") {
			if (inputs[i].checked || inputs[i].type == "hidden") {
				form.fetchCount = inputs[i].value;
			}
		}

	}
		form.todef = function() {
		form.target = "";
		form.actionid.value = form.def_acti;
	
	};
	// Validation
	// string - text values
	// integer - integer value
	// float - float value
	// price - money value (0.00)
	// email - e-mail address
	// url - URL
	// not-empty - not empty field
	var isFormValidable = true;
	
	
	
	for (var i = 0, ii = form.elements.length; i < ii; i++) {
		if ((/(^|\s)integer($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)float($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)prezzo_euro($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)email($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)url($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)data_all($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)data_passata($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)controllo_trattamenti($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)data_futura($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)codice_fiscale_omocodia($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)codice_fiscale($|\s)/.test(form.elements[i].parentNode.className)) ||			
			(/(^|\s)controllo_file($|\s)/.test(form.elements[i].parentNode.className)) ||
			((InStr(form.elements[i].parentNode.className,"groups"))) ||	
			((InStr(form.elements[i].parentNode.className,"condition"))) ||	
			((InStr(form.elements[i].parentNode.className,"radio"))) ||				
			(/(^|\s)groups($|\s)/.test(form.elements[i].parentNode.className)) ||
			(/(^|\s)(not[-\s]empty|mandatory)($|\s)/.test(form.elements[i].parentNode.className))) {
			isFormValidable = true;
			break;
		}
	}
	
	
	
	log("Is Form Validatable: " + isFormValidable);
			

	if (isFormValidable) {
		addEvent(form, "submit", function(e) {
			
			
			
									
												

						var j=0;
						var setfocus=true;
						var ck_groups=0;
						var group_element = [];
						var group_array = [];
						var arr_info_group = [];
						var arr_info_radio = [];
						var arr_classes = [];
						
							var radio_one=MultiDimensionalArray(32,32);
							var groups_one=MultiDimensionalArray(32,32);
							var groups_this=MultiDimensionalArray(32,32);
							var groups_all=MultiDimensionalArray(32,32);
							
							
							
							var group_one_cont = [];
							var radio_one_cont = [];
							var group_flushone = [];
							var radio_flushone = [];
							var group_all_cont = [];
							var group_flushall = [];
							
							for (i=0; i < 32; i++) {
						       group_one_cont[i]=0;
						   }
						   
						   for (i=0; i < 32; i++) {
						       radio_one_cont[i]=0;
						   }
						   
						   for (i=0; i < 32; i++) {
						       group_all_cont[i]=0;
						   }
						   
						   
						   for (i=0; i < 32; i++) {
						       group_flushone[i]=false;
						   }
						   
						   for (i=0; i < 32; i++) {
						       radio_flushone[i]=false;
						   }
						   
						    for (i=0; i < 32; i++) {
						       group_flushall[i]=false;
						   }
							
							
							var group_this_cont = [];
							
						
							var group_flushthis = [];
							
				
					
				
					for (var i = 0, ii = this.elements.length; i < ii; i++) {
					checkfield=true;
							
							if ((InStr(form.elements[i].parentNode.className,"mandatory")) && (this.elements[i].disabled==false) && (this.elements[i].type != "hidden")  )
							{
							
							
								if ((InStr(form.elements[i].parentNode.className,"condition")))
								{
									var classe=form.elements[i].parentNode.className;
									arr_classes=classe.split(" ");
									t=false;
									for (var n = 0, nn = arr_classes.length; n < nn; n++)
									{
									
										if (InStr(arr_classes[n],"condition") && checkfield==true )
										{
										classe=arr_classes[n];
										stop=classe.length;
										classe=classe.substring(1,stop-1)
										arr_info_condition=classe.split(",");
										father=arr_info_condition[1];
										value=arr_info_condition[2];
										action=arr_info_condition[3];
										checkfield=check_condition(father,this.elements[i],value,action);
										
										}
										else if (InStr(arr_classes[n],"condition") && checkfield==false )
										{
											flushField(this.elements[i]);
										
										}
										
									
									}
									
								
								}
								
								
								
								
							
						
								
								
								if ((InStr(form.elements[i].parentNode.className,"groups")) && (checkfield==true) && (this.elements[i].disabled==false) && (this.elements[i].type != "hidden"))
								{
									
									var classe=form.elements[i].parentNode.className;
									arr_classes=classe.split(" ");
									
									for (var n = 0, nn = arr_classes.length; n < nn; n++)
									{
									
										if (InStr(arr_classes[n],"groups"))
										classe=arr_classes[n];
									
									}
									
									
									arr_info_group=classe.split("_");
									
									number=	arr_info_group[1];
									parameter=arr_info_group[2];
									
									if (parameter=='one')
									{
										
										group_one_cont[number]=group_one_cont[number]+1
											cont=group_one_cont[number];
										
											groups_one[number][cont]=this.elements[i];
											x=groups_one[number][cont];
											
										
									}
									else if (parameter=='all')
									{
											group_all_cont[number]=group_all_cont[number]+1
											cont=group_all_cont[number];
										
											groups_all[number][cont]=this.elements[i];
											x=groups_one[number][cont];
									
									}
								}
								
								/*if ((InStr(form.elements[i].parentNode.className,"radio")) && (checkfield==true) && (this.elements[i].disabled==false) && (this.elements[i].type != "hidden"))
								{
									
									var classe=form.elements[i].parentNode.className;
									arr_classes=classe.split(" ");
									
									for (var n = 0, nn = arr_classes.length; n < nn; n++)
									{
									
										if (InStr(arr_classes[n],"radio"))
											classe=arr_classes[n];
									
									}
									
									
											arr_info_radio=classe.split("_");
											
											number=	arr_info_radio[1];
											parameter=arr_info_radio[2];
									
											radio_one_cont[number]=radio_one_cont[number]+1
											cont=radio_one_cont[number];
										
											radio_one[number][cont]=this.elements[i];
											x=radio_one[number][cont];
											alert(x.value);
									
								}*/
								
								else if ((InStr(this.elements[i].parentNode.className,"controllo_file")) && (this.elements[i].disabled==false) && (this.elements[i].value!='') ) {
									
									
									if (InStr(this.elements[i].parentNode.className,"generica")) 
									{
										if (controllo_file(this.elements[i].value,".pdf.xls.xlsx.mp3.avi.mpg.mpeg.wav.tif.bmp.gif.rtf.jpg.jpeg.doc.docx.txt")==false)
										{
										j++;
										warnField(this.elements[i], "Attenzione! Possono essere caricati solo file TIF, BMP, GIF, JPG, RTF, DOC,DOCX, TXT, PDF, XLS, XLSX.",setfocus);
										setfocus=false;
										}
										else
										flushField(this.elements[i]);
									}
									
									if (InStr(this.elements[i].parentNode.className,"immagini")) 
									{
										if (controllo_file(this.elements[i].value,".tif.bmp.gif.jpg.jpeg.")==false)
										{
										j++;
										warnField(this.elements[i], "Attenzione! Possono essere caricati solo file TIF, BMP, GIF, JPG.",setfocus);
										setfocus=false;
										}
										else
										flushField(this.elements[i]);
									}
																	
															
									if (InStr(this.elements[i].parentNode.className,"personalizzato")) 
									{
										 var controll=this.elements[i].parentNode.className.split('#');
										   fisrt=controll[0];
										   ext=controll[1];	
										
										if (controllo_file(this.elements[i].value,ext)==false)
										{
										j++;
										warnField(this.elements[i], "Attenzione! il file deve essere solo di tipo "+ext,setfocus);
										setfocus=false;
										}
										else
										flushField(this.elements[i]);
									}
										
									
								}
								
								
								else if ((InStr(this.elements[i].parentNode.className,"data_all")) && (this.elements[i].disabled==false) && (this.elements[i].value!='') ) {
									
									if (validateDate(this.elements[i].value,"W","A")==false)
									{
									j++;
									warnField(this.elements[i], "Selezionare una data valida.",setfocus);
									setfocus=false;
									}
									else
									flushField(this.elements[i]);
									
								}
								else if ((InStr(this.elements[i].parentNode.className,"data_passata")) && (this.elements[i].disabled==false) && (this.elements[i].value!='') ) 
								{
									
									
									if (validateDate(this.elements[i].value,"W","P")==false)
									{
									j++;
									warnField(this.elements[i], "Selezionare una data valida antecedente ad oggi.",setfocus);
									setfocus=false;
									}
									else
									flushField(this.elements[i]);
									
								}
								
								else if ((InStr(this.elements[i].parentNode.className,"data_futura")) && (this.elements[i].disabled==false) && (this.elements[i].value!='') ) {
									
									if (validateDate(this.elements[i].value,"W","F")==false)
									{
									j++;
									warnField(this.elements[i], "Selezionare una data valida successiva ad oggi.",setfocus);
									setfocus=false;
									}
									else
									flushField(this.elements[i]);
									
								}
								
								else if ((InStr(this.elements[i].parentNode.className,"controllo_trattamenti")) && (this.elements[i].disabled==false) && (this.elements[i].value!='') ) {
									
									if ((this.elements[i].value>13)||( Math.ceil(this.elements[i].value) != this.elements[i].value))
									{
									j++;
									warnField(this.elements[i], "Il Numero di trattamenti deve essere un numero intero non superiore a 12.",setfocus);
									setfocus=false;
									}
									else
									flushField(this.elements[i]);
									
								}
								
								
								else if ((InStr(this.elements[i].parentNode.className,"codice_fiscale_omocodia")) && (this.elements[i].disabled==false) && (this.elements[i].value!='') ) {
									
									var str_ret=controllaCF(this.elements[i].value);									
									if (str_ret!="")
									{
									j++;
									warnField(this.elements[i], str_ret,setfocus);
									setfocus=false;
									}
									else
									flushField(this.elements[i]);
									
								}
								else if ((InStr(this.elements[i].parentNode.className,"codice_fiscale")) && (this.elements[i].disabled==false) && (this.elements[i].value!='') ) {
									
									var str_ret=controllaCF_1(this.elements[i].value);									
									if (str_ret!="")
									{
									j++;
									warnField(this.elements[i], str_ret,setfocus);
									setfocus=false;
									}
									else
									flushField(this.elements[i]);
									
								}
								
						// email validation
								else if (/(^|\s)email($|\s)/.test(this.elements[i].parentNode.className) && (this.elements[i].disabled==false)  && !(/^([\w\-\.]+)@((\[([0-9]{1,3}\.){3}[0-9]{1,3}\])|(([\w\-]+\.)+)([a-zA-Z]{2,4}))$/.test(this.elements[i].value))) {
								
									j++;
									warnField(this.elements[i], "Inserire un indirizzo email nel formato corretto.",setfocus);
									setfocus=false;
								}
								
								// /^\d+(\,\d\d?)?$/

								else if ( (/(^|\s)prezzo_euro($|\s)/.test(this.elements[i].parentNode.className)) && (this.elements[i].disabled==false)  && !(/^\d+(\.\d\d?)?$/.test(this.elements[i].value)) && !(/^\d+(\,\d\d?)?$/.test(this.elements[i].value))) {
								
									j++;
									warnField(this.elements[i], "Questo campo ammette solo valori numerici in valuta euro.",setfocus);
									setfocus=false;
								}
								
								
								
								
							
								
										

								// integer field validation
								else if ((InStr(form.elements[i].parentNode.className,"integer")) && (this.elements[i].disabled==false)  && this.elements[i].value != "" ) 
								{
							
									flushField(this.elements[i]);
									if( Math.ceil(this.elements[i].value) != this.elements[i].value){
									
										
										j++;
										warnField(this.elements[i], "Inserire un numero .",setfocus);
										setfocus=false;
									
									}
									else{
										
									
										var allClass=this.elements[i].parentNode.className;
										arr_class=allClass.split(" ");
										var integer_mindigit=0;
										var classe='';
										
										for (var n = 0, nn = arr_class.length; n < nn; n++)
										{
									
										if (InStr(arr_class[n],"integer"))
										classe=arr_classes[n];
										
										}
										
											if (InStr(classe,"_"))
											{
											arr_class_integer=classe.split("_");
											integer_mindigit=arr_class_integer[1];
											}
										
										
										var integer_object=this.elements[i].value;
										
										for (var z = 0, zz = arr_class.length; z < zz; z++) {
										
											if (Math.ceil(arr_class[z])){
												integer_mindigit=arr_class[z];
												break;
											}
										}
										
										
										
										if ((integer_object.length!=integer_mindigit)&&(integer_mindigit>0))
										{
										j++;
										warnField(this.elements[i], "Inserire un numero di   "+integer_mindigit+" cifre.",setfocus);
										setfocus=false;
										}
									}	

								} 		
								// integer field validation
								
					
								//float validation
								else if (/(^|\s)float($|\s)/.test(this.elements[i].parentNode.className) && (this.elements[i].disabled==false)  && this.elements[i].value != "" && this.elements[i].value * 1 != this.elements[i].value) {
								j++;
									warnField(this.elements[i], "This field must be a valid floating point number.",setfocus);
									setfocus=false;

								} 			
								
							

								// url validation
								else if (/(^|\s)url($|\s)/.test(this.elements[i].parentNode.className) && (this.elements[i].disabled==false) && this.elements[i].value != "" && !(/^(((file|gopher|news|nntp|telnet|http|ftp|https|ftps|sftp):\/\/)|(www\.))+(([a-zA-Z0-9\._-]+\.[a-zA-Z]{2,6})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(\/[a-zA-Z0-9\&%_\.\/-~-]*)?$/.test(this.elements[i].value))) {

									warnField(this.elements[i], "This field must be a valid URL starting with http://",setfocus);
									setfocus=false;

								} 
								
								else
									{
										// empty field validation
										
										if (/(^|\s)(not[-\s]empty|mandatory)($|\s)/.test(this.elements[i].parentNode.className) && (this.elements[i].disabled==false) && (trim(this.elements[i].value) == '' || (this.elements[i].type == "checkbox" && !this.elements[i].checked))) {
											
											
											
											j++;
											//if (!(/(^|\s)focused($|\s)/.test(this.elements[i].className))) {
											/*	warnField(this.elements[i], " field is required.",setfocus);*/
											warnField(this.elements[i], "Questo campo e' obbligatorio.",setfocus);
												setfocus=false;
											//}
												

										} else {
											
												if (this.elements[i].disabled==false){
													if(this.elements[i].value != "") {
														flushField(this.elements[i]);
													}
												}

										}
									}
									

							}
							else if ((this.elements[i].disabled==false) && (this.elements[i].type != "hidden"))
							{
						
							
								//IF IS NOT MANDUTARY BUT REQUIRED VALIDATION IS FILLED
								
								
								if ((InStr(form.elements[i].parentNode.className,"condition")))
								{
									var classe=form.elements[i].parentNode.className;
									arr_classes=classe.split(" ");
									
									for (var n = 0, nn = arr_classes.length; n < nn; n++)
									{
									
										if (InStr(arr_classes[n],"condition") && (checkfield==true) )
										{
										classe=arr_classes[n];
										stop=classe.length;
										classe=classe.substring(1,stop-1)
										arr_info_condition=classe.split(",");
										father=arr_info_condition[1];
										value=arr_info_condition[2];
										action=arr_info_condition[3];
										checkfield=check_condition(father,this.elements[i],value,action);
										
										
										}
										else if (InStr(arr_classes[n],"condition") && checkfield==false )
										{
										
										flushField(this.elements[i]);
										
									//		j--;	
										}
										
									
									}
									
								
								}
								
								
								if ((InStr(form.elements[i].parentNode.className,"groups")) && (checkfield==true) && (this.elements[i].disabled==false))
								{
									var classe=form.elements[i].parentNode.className;
									arr_classes=classe.split(" ");
									
									for (var n = 0, nn = arr_classes.length; n < nn; n++)
									{
									
										if (InStr(arr_classes[n],"groups"))
										classe=arr_classes[n];
									
									}
									
									
									arr_info_group=classe.split("_");
									
									number=	arr_info_group[1];
									parameter=arr_info_group[2];
									
									
									if (parameter=='one')
									{
										
										group_one_cont[number]=group_one_cont[number]+1
											cont=group_one_cont[number];
										
											groups_one[number][cont]=this.elements[i];
											x=groups_one[number][cont];
											
										
									}
									else if (parameter=='all')
									{
											group_all_cont[number]=group_all_cont[number]+1
											cont=group_all_cont[number];
										
											groups_all[number][cont]=this.elements[i];
											x=groups_one[number][cont];
									
									}
								
								
								}
								
								
								
								
								
								else if (/(^|\s)email($|\s)/.test(this.elements[i].parentNode.className) && (this.elements[i].disabled==false) ) {
								
									if ( (this.elements[i].value!='') && !(/^([\w\-\.]+)@((\[([0-9]{1,3}\.){3}[0-9]{1,3}\])|(([\w\-]+\.)+)([a-zA-Z]{2,4}))$/.test(this.elements[i].value)))
									{
									j++;
									warnField(this.elements[i], "Questo campo deve essere un indirizzo email.",setfocus);
									setfocus=false;
									}
									else
									flushField(this.elements[i]);
								
								}
								// validazione prezzo in formato euro
								else if ( (/(^|\s)prezzo_euro($|\s)/.test(this.elements[i].parentNode.className)) && (this.elements[i].disabled==false) ) {
								                                           
								
									if ( (this.elements[i].value!='') && !(/^\d+(\.\d\d?)?$/.test(this.elements[i].value))&& !(/^\d+(\,\d\d?)?$/.test(this.elements[i].value))) {
									
									j++;
									warnField(this.elements[i], "Questo campo ammette solo valori numerici in valuta euro.",setfocus);
									setfocus=false;
									}
									else
									
									flushField(this.elements[i]);
								
								}
								
								
								
							
							/*else if ((InStr(this.elements[i].parentNode.className,"controllo_file")) && (this.elements[i].disabled==false) && (this.elements[i].value!='') ) {
									
									if (InStr(this.elements[i].parentNode.className,"generica")) 
									{
										if (controllo_file(this.elements[i].value,".tif.bmp.gif.jpg.jpeg.doc.txt.pdf.xls.")==false)
										{
										j++;
										warnField(this.elements[i], "Attenzione! Possono essere caricati solo file TIF, BMP, GIF, JPG, DOC, TXT, PDF, XLS.",setfocus);
										setfocus=false;
										}
										else
										flushField(this.elements[i]);
									}
									
									if (InStr(this.elements[i].parentNode.className,"immagini")) 
									{
										if (controllo_file(this.elements[i].value,".tif.bmp.gif.jpg.jpeg.")==false)
										{
										j++;
										warnField(this.elements[i], "Attenzione! Possono essere caricati solo file TIF, BMP, GIF, JPG.",setfocus);
										setfocus=false;
										}
										else
										flushField(this.elements[i]);
									}
										
									
								} */
								else if ((InStr(form.elements[i].parentNode.className,"integer")) && (this.elements[i].disabled==false)  && this.elements[i].value != "" ) 
								{
							
								flushField(this.elements[i]);
									if( Math.ceil(this.elements[i].value) != this.elements[i].value){
									
										
										j++;
										warnField(this.elements[i], "Questo campo deve essere un numero.",setfocus);
										setfocus=false;
									
									}
									else{
										
									
										var allClass=this.elements[i].parentNode.className;
										arr_class=allClass.split(" ");
										var integer_mindigit=0;
										var classe='';
										
										for (var n = 0, nn = arr_class.length; n < nn; n++)
										{
									
										if (InStr(arr_class[n],"integer"))
										classe=arr_classes[n];
										
										}
										
											if (InStr(classe,"_"))
											{
											arr_class_integer=classe.split("_");
											integer_mindigit=arr_class_integer[1];
											}
										
										
										var integer_object=this.elements[i].value;
										
										for (var z = 0, zz = arr_class.length; z < zz; z++) {
										
											if (Math.ceil(arr_class[z])){
												integer_mindigit=arr_class[z];
												break;
											}
										}
										
										
										
										if ((integer_object.length!=integer_mindigit)&&(integer_mindigit>0))
										{
										j++;
										warnField(this.elements[i], "This field must be  "+integer_mindigit+" digits.",setfocus);
										setfocus=false;
										}
									}	

								} 		
								// integer field validation
							
							
							
							
							
							}

				
			
				
			}
					
					
					/*VALIDATION GROUP ONE*/
				
					
					for (var i = 0, ii = 31; i <= ii; i++) 
					{
					
						for (var h = 0, hh = 31; h <= hh; h++) 
						{
						elem=groups_one[i][h];
						
							if (elem){
								if(elem.checked==true)
								{
								group_flushone[i]=true;
								}
							}
						}
					
					}
					
					
					
					for (var i = 0, ii = 31; i <=ii; i++) 
					{
						if (group_flushone[i]==true)
						{
					
							for (var h = 0, hh = 31; h <= hh; h++) 
							{
								elem=groups_one[i][h];
								if (elem)
								{
								flushField(elem);
							//	j--;
								}	
						
							}
						}
						else
						{
						
							for (var h = 0, hh = 31; h <= hh; h++) 
							{
								elem=groups_one[i][h];
								if (elem)
								{
									flushField(elem);
									j++;
									warnField(groups_one[i][h], "Uno di questi campi deve essere selezionato.",setfocus);
									setfocus=false;
								}
						
							}
						
							
						}
						
					
					}
					
					/*END*/
					
					/*VALIDATION GROUP ALL*/
				
					
					for (var i = 0, ii = 30; i < ii; i++) 
					{
						group_flushall[i]=false;
						for (var h = 0, hh = 30; h < hh; h++) 
						{
						elem=groups_all[i][h];
						
							if (elem){
								if(elem.value!='')
								group_flushall[i]=true;
							}
						}
					
					}
					
					
					
					for (var i = 0, ii = 30; i < ii; i++) 
					{
						if (group_flushall[i]==true)
						{
					
							for (var h = 0, hh = 30; h < hh; h++) 
							{
								elem=groups_all[i][h];
								if (elem)
								{
								
									if (elem.value!='')
										flushField(elem);
									else
									{
										flushField(elem);
										j++;
										warnField(groups_all[i][h], "This fields must be completed.",setfocus);
										setfocus=false;
									}
								}	
						
							}
						}
						else
						{
							for (var h = 0, hh = 30; h < hh; h++) 
							{
									elem=groups_all[i][h];
									if (elem)
									{
									flushField(elem);
								//	j--;
									}	
							}
						
						}
					
					}
					
					/*END*/
					
					
					/*VALIDATION RADIO*/
				
					
					/*for (var i = 0, ii = 30; i < ii; i++) 
					{
					
						for (var h = 0, hh = 30; h < hh; h++) 
						{
						elem=radio_one[i][h];
						
							if (elem){
							
								
								if (elem.checked==true)
								{
									var classe=elem.parentNode.className;
									arr_classes=classe.split(" ");
									
									for (var n = 0, nn = arr_classes.length; n < nn; n++)
									{
									
										if (InStr(arr_classes[n],"radio"))
										classe=arr_classes[n];
									
									}
									
									
									arr_info_radio=classe.split("_");
									
									condition=	arr_info_group[1];
									value=arr_info_group[2];
									action=arr_info_group[3];
									
									if (condition=='uqual')
									{
										if (elem.value==value)
											radio_flushone[i]=true;
									}
									else if (condition=='notuqual')
									{
									if (elem.value==value)
											radio_flushone[i]=false;
									
									}
							
								}
							}
						}
					
					}
					
					
					
					for (var i = 0, ii = 30; i < ii; i++) 
					{
						if (radio_flushone[i]==true)
						{
					
							for (var h = 0, hh = 30; h < hh; h++) 
							{
								elem=radio_one[i][h];
								if (elem)
								{
								flushField(elem);
							//	j--;
								}	
						
							}
						}
						else
						{
						
							for (var h = 0, hh = 30; h < hh; h++) 
							{
								elem=radio_one[i][h];
								if (elem)
								{
									flushField(elem);
									j++;
									warnField(radio_one[i][h], "One of these fields must be completed.",setfocus);
									setfocus=false;
								}
						
							}
						
							
						}
						
					
					}*/
					
					/*END*/
					
					
					
					
						
						
						if (j>0)
						{
							//	MOSTRARE PULASNTE
							
							
							validateform=false;
							return stopEvent(e || event);
						}
						else
						{
								validateform=true;
							
									// prepare the form when the DOM is ready 
									//$(document).ready(function() { 
									//x=inizializza();
									var options = { 
									//   target:        '#layer_nero',   // target element(s) to be updated with server response 
									beforeSubmit:  showRequest,  // pre-submit callback 
									success:       showResponse  // post-submit callback 

									// other available options: 
									//url:       url         // override for form's 'action' attribute 
									//type:      type        // 'get' or 'post', override for form's 'method' attribute 
									//dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
									//clearForm: true        // clear all form fields after successful submit 
									//resetForm: true        // reset the form after successful submit 

									// $.ajax options can be used here too, for example: 
									//timeout:   3000 
									}; 

									// bind to the form's submit event 
								//	$('#myForm').submit(function() { 
									// inside event callbacks 'this' is the DOM element so we first 
									// wrap it in a jQuery object and then invoke ajaxSubmit 
									//$(this).ajaxSubmit(options);
						
									if (home != true) {
										
										$(this).ajaxSubmit(options);																			
										validateform=false;
										home=true;
										return stopEvent(e || event);
										
									}
									
									// !!! Important !!! 
									// always return false to prevent standard browser submit and page navigation 
								//	return false; 
									//}); 
									
						}
						
						
						
		});
	}
	
	
	
}

function showRequest(formData, jqForm, options) { 



$('#layer_nero').toggle();
return true;

 
} 


function scegli_funzione(idrecord,idfunzione,page)
{
	
	//cambia pagina contenitore principale
	if (idfunzione==1)
	{
		//$("#layer_nero2").toggle();
		$('#wrap-content').innerHTML="";
		 $("#wrap-content").load(page,'', function(){
			   loading_page('loading');
			 });
		
	
	}
	
	if (idfunzione==2)
	{
		//$("#layer_nero2").toggle();
		$('#lista_impegnative').innerHTML="";
		 $("#lista_impegnative").load(page,'', function(){
			   loading_page('loading');
			 });
		
	
	}
	
	if (idfunzione==3)
	{
		page=page+"&id="+idrecord;
		//$("#layer_nero2").toggle();		
		$('#lista_impegnative').innerHTML="";		
		 $("#lista_impegnative").load(page,'', function(){
			   $('#container-4').triggerTab(0);
			   loading_page('loading');			   		   
			 });		
	
	}
	
	if (idfunzione==4)
	{
		page=page+"&id="+idrecord;
		//$("#layer_nero2").toggle();
		$('#sanitaria').innerHTML="";
		 $("#sanitaria").load(page,'', function(){
			   loading_page('loading');
			 });
		
	
	}
	
	if (idfunzione==5)
	{
		page=page+"&id="+idrecord;
		//$("#layer_nero2").toggle();
		$('#cartella_clinica').innerHTML="";
		 $("#cartella_clinica").load(page,'', function(){
			   loading_page('loading');
			 });
		
	
	}
	
	if (idfunzione==6)
	{

		page=page+"&id="+idrecord;
		//$("#layer_nero2").toggle();
		$('#cartella_clinica').innerHTML="";
		 $("#cartella_clinica").load(page,'', function(){
				$('#container-5').triggerTab(0);
			   loading_page('loading');
			 });
		
	
	}
	
	if (idfunzione==7)
	{
		page=page+"&id="+idrecord;
		//$("#layer_nero2").toggle();
		$('#paziente').innerHTML="";
		pagina_da_caricare="re_pazienti_anagrafica.php?do=edit&id="+idrecord;
		$("#paziente").load(pagina_da_caricare,'', function(){
		   loading_page('loading');
		   $("#container-3 .tabs-selected").removeClass('tabs-selected');
		   $('html, body').animate({
			scrollTop: $("#wrap-content").offset().top
			}, 2000);
		 });
	}
	
	if (idfunzione==8)
	{
		
      //  disablePopup_force();
		var parametri=pagina.split('#');	  
		area=parametri[0];
        modulo=parametri[1];
        opeins=parametri[2];
		  
        visualizza_scadenze(area,modulo,opeins);
	}


	
}

 
// post-submit callback 
function showResponse(responseText, statusText)  { 

   //chiamata tipo: ok;1;123#1456#antonio;   
   if (InStr(responseText,";"))
   {
	   var call=responseText.split(';');	  
	   valore=call[0];
	   idrecord=call[1];
	   idfunzione=call[2];
	   pagina=call[3];
	   
	   avvisi_loading(valore);
	   
	   
	  scegli_funzione(idrecord,idfunzione,pagina);
	   
	   //alert(par_call);
	   //par_call=call[2].split('#');
	    //for (var i=0; i < par_call.length; i++) {
	    	//alert(par_call[i]);
	
  }	  
  else
  valore=responseText;
   
   //valore='ok';
if (valore=="")
	valore='no';
   avvisi_loading(valore);
  

} 

function get_validate()
{
	return validateform;
	
}


function createCheckmeClassLevel(newparent,newchild)
{
newparent.className='select-holder checkme';
//newparent.id = 'formfield'+newchild.name;		

}

function createCheckmeClasscreateCheckmeClass(newparent,newchild)
{
newparent.className='checkme';
newparent.id = 'formfield'+newchild.name;		

}

function getLabel(input) {
	if (!input.id) {
		return input.name;
	}
	var labels = document.getElementsByTagName("label");
	for (var i = 0, ii = labels.length; i < ii; i++) {
		if (labels[i].htmlFor == input.id) {
			return labels[i].innerHTML;
		}
	}
	return input.name;
}



/**
* addWarning function
*/
function addWarning(parent,txt) {

	
	var div = document.createElement("div");
	div.className="warning";
	

	/*var warning = document.createTextNode(getLabel(input) + txt);*/
	
	var warning = document.createTextNode(txt);
	
	div.appendChild(warning);

	//var icon = document.createElement("img");
	//icon.src = "../site/images/undo.gif";
	//icon.alt = "Click here to undo the changes";
	
	parent.parentNode.insertBefore(div, parent.nextSibling);
	
}


/**
* warnField function
*/
function warnField(input, wmsg,setfocus) {

	
	abilitaTuttiIForm();
	var checkmeparent = input.parentNode;
	
	
	
	

	if (setfocus)
	{
	input.focus();
	}
	
	if((/(^|\s)correct($|\s)/.test(checkmeparent.className))) {
		checkmeparent.className = checkmeparent.className.replace(/correct/, "");
		
	}
	
		if(!(/(^|\s)focused($|\s)/.test(checkmeparent.className))) {
			checkmeparent.className = checkmeparent.className + " focused";
			
			addWarning(checkmeparent,wmsg);
		}
		
}

/**
* flushField function
*/
function flushField(input) {

	var checkmeparent = input.parentNode;
	
	
		checkmeparent.className = checkmeparent.className.replace(/focused/, "");
		
		if(!(/(^|\s)correct($|\s)/.test(checkmeparent.className))) {
			checkmeparent.className = checkmeparent.className + " correct";
		}	
		
		
		element=checkmeparent.parentNode;
		
		for (var a = 0, aa = element.childNodes.length; a < aa; a++) 
		{
			if(element.childNodes[a])
			{
			if (element.childNodes[a].className=="warning")
			{
				element.removeChild(element.childNodes[a]);
			}
			}
		
		}
		
			
	}	






function addLOV(input) {
	log("Adding LOV for input [" + input.name + "]");
	var hid = input.parentNode.getElementsByTagName("input");
	for (var i = hid.length - 1; i >= 0; i--) {
		if (hid[i].type == "hidden") {
			hid = hid[i];
			break;
		}
	}
	var form = this;
	var listid = input.className.toString().match(/(^|\s)list-of-values-(\d+)(\s|$)/);
	listid = listid[2];

	var button = document.createElement("button");
	button.setAttribute("type", "button");
	var icon = document.createElement("img");
	icon.src = Site.imagepath + "textclear.gif";
	icon.alt = "Clear Field";
	button.onclick = function() {
		if (!input.disabled) {
			input.value = "";
			hid.value = "";
		}
	};
	button.className = "list-of-values clear";
	button.appendChild(icon);
	input.parentNode.insertBefore(button, input.nextSibling);
	input.clearIcon = icon;

	button = document.createElement("button");
	button.setAttribute("type", "button");
	icon = document.createElement("img");
	icon.src = Site.imagepath + "list.gif";
	icon.alt = "List of Values";
	button.onclick = function() {
		if (!input.disabled) {
			var w = open("/jobtools/cmutils.lov?in_nosearch=Y&in_sessionid=" + form.session.value +
						"&in_formname=opener.document.getElementById('" + form.id + "')" +
						"&in_param1=" + hid.name +
						"&in_param2=" + input.name +
						"&in_listid=" + listid +
						"&in_searchtype=Contains&in_organid=" + form.organisation.value,
						"FoldersLOV", "scrollbars=1,resizable=1,width=425,height=425");
			if (w.opener == null) {
				w.opener = window;
			}
			w.focus();
		}
	};
	input.onfocus = function() {
		button.focus();
	};
	button.className = "list-of-values list";
	button.appendChild(icon);
	input.parentNode.insertBefore(button, input.nextSibling);
	input.readOnly = true;
	input.lovIcon = icon;
	input.disable = function() {
		input.disabled = true;
		input.clearIcon.src = Site.imagepath + "textclear-disabled.gif";
		input.lovIcon.src = Site.imagepath + "list-disabled.gif";
	};
	input.enable = function() {
		input.disabled = false;
		input.clearIcon.src = Site.imagepath + "textclear.gif";
		input.lovIcon.src = Site.imagepath + "list.gif";
	};
	if (input.disabled) {
		input.disable();
	}
	log("LOV " + listid + " added");
}


function addTimePicker(input) {
	log("Adding time picker for [" + input.name + "]");
	input.className = "hidden";

	var hours = document.createElement("select");
	hours.id = "hours-" + input.id;
	input.parentNode.appendChild(hours);
	for (var i = 0; i < 24; i++) {
		hours.options[hours.options.length] = new Option(i.zero(), i.zero(), input.value.substring(0, 2) == i.zero());
	}

	input.parentNode.appendChild(document.createTextNode(":"));

	var mins = document.createElement("select");
	mins.id = "mins-" + input.id;
	input.parentNode.appendChild(mins);
	for (var i = 0; i < 61; i+=5) {
		mins.options[mins.options.length] = new Option(i.zero(), i.zero(), input.value.substring(3) == i.zero());
	}
	hours.onchange = mins.onchange = function() {
		input.value = hours.value + ":" + mins.value;
	};
}


function addCalendar(input) {
	log("Adding Calendar for [" + input.name + "]");
	Site.addScript("../../commonhtml/includes/date-picker.js");

	var id = "datepicker" + (Site.calendars?++Site.calendars:Site.calendars = 1);
	document[id] = {value: "", input: input};
	var button = document.createElement("button");
	button.setAttribute("type", "button");
	var icon = document.createElement("img");
	//icon.src = Site.imagepath + "textclear.gif";
	icon.src = "/site/images/date_clear.gif";
	icon.alt = "Clear Field";
	button.onclick = function() {
		input.value = "";
		if (typeof onDatePicked == "function") {
			onDatePicked(document[id].input);
		}
	};
	button.className = "date-picker clear";
	button.appendChild(icon);
	input.parentNode.insertBefore(button, input.nextSibling);

	button = document.createElement("button");
	button.setAttribute("type", "button");
	var icon = document.createElement("img");
	//icon.src = Site.imagepath + "calendar.gif";
	icon.src = "/site/images/date_show.gif";
	icon.alt = "Calendar";
	button.onclick = function() {
		show_calendar(id);
	};
	input.onfocus = function() {
		button.tabindex = this.tabindex;
		button.focus();
	};
	if (!Site.tickers["date-pickers"]) {
		Site.tickers["date-pickers"] = function() {
			for (var i = 1, ii = Site.calendars + 1; i < ii; i++) {
				if (document["datepicker" + i].value) {
					document["datepicker" + i].input.value = document["datepicker" + i].value;
					document["datepicker" + i].value = "";
					if (typeof onDatePicked == "function") {
						onDatePicked(document["datepicker" + i].input);
					}
				}
			}
		};
	}
	button.className = "date-picker show";
	button.appendChild(icon);
	input.parentNode.insertBefore(button, input.nextSibling);
	input.readOnly = true;
	log("Calendar added");
}


function addSubmit(input) {
	log("Adding Submit Action to button [" + input.name + "]");
	var acti = input.className.toString().match(/(^|\s)change-acti-to-(\d+)(\s|$)/);
	acti = acti[2];
	var form = this;
	addEvent(input, "click", function() {
		form.actionid.value = acti;
	});
	log("Submit Action added");
}


function addActionMenu(input) {
	log("Adding Action Menu event handlers");
	Site.AM = {container: input.parentNode};
	Site.AM.button = input;
	Site.AM.select = Site.AM.container.getElementsByTagName("select")[0];
	Site.AM.form = this;
	input.onclick = function(e) {
		if (!Site.AM.select.value || (Site.AM.messages && Site.AM.messages[Site.AM.select.value] && !confirm(Site.AM.messages[Site.AM.select.value]))) {
			return stopEvent(e?e:window.event);
		}
		Site.AM.form.todef();
		Site.AM.form.actionid.value = Site.AM.select.value;
		if (/(^|\s)popup($|\s)/.test(Site.AM.select.options[Site.AM.select.selectedIndex].className)) {
			Site.AM.form.target = "_blank";
		}
		Site.AM.form.submit();
		return false;
	};
	var dl = Site.AM.container.getElementsByTagName("dl");
	for (var i = 0, ii = dl.length; i < ii; i++) {
		if (dl[i].className == "hidden messages") {
			var dt = dl[i].getElementsByTagName("dt");
			var dd = dl[i].getElementsByTagName("dd");
			if (dt.length) {
				Site.AM.messages = {};
			}
			for (var j = 0, jj = dt.length; j < jj; j++) {
				Site.AM.messages[dt[j].innerHTML] = ((dd.length - 1 >= j)?dd[j].innerHTML:"");
			}
		}
	}
	Site.AM.select.onchange = function() {
		var div;
		if (this.divid != "for" + this.value && (div = document.getElementById(this.divid))) {
			div.className = "hidden";
			var selects = div.getElementsByTagName("select");
			var inputs = div.getElementsByTagName("input");
			for (var i = 0, ii = selects.length; i < ii; i++) {
				selects[i].disabled = true;
			}
			for (var i = 0, ii = inputs.length; i < ii; i++) {
				inputs[i].disabled = true;
			}
		}
		div = document.getElementById("for" + this.value);
		if (div) {
			div.className = "";
			this.divid = div.id;
			var selects = div.getElementsByTagName("select");
			var inputs = div.getElementsByTagName("input");
			for (var i = 0, ii = selects.length; i < ii; i++) {
				selects[i].disabled = false;
			}
			for (var i = 0, ii = inputs.length; i < ii; i++) {
				inputs[i].disabled = false;
			}
		}
	};

	Site.tickers["actionmenu"] = function() {
		var valid = false;
		for (var i = 0, ii = Site.AM.button.form.elements.length; i < ii; i++) {
			if ((Site.AM.button.form.elements[i].name == "SELECTED_ITEM" ||
				 Site.AM.button.form.elements[i].name == "SELECT_CAND") && input.form.elements[i].checked) {
				valid = true;
				break;
			}
		}
		// valid = valid && Site.AM.select.value;
		if (/(^|\s)valid($|\s)/.test(Site.AM.select.options[Site.AM.select.selectedIndex].className)) {
			valid = true;
		}
		if (valid) {
			var div = document.getElementById("for" + Site.AM.select.value);
			if (div) {
				var selects = div.getElementsByTagName("select");
				var inputs = div.getElementsByTagName("input");
				for (var i = 0, ii = selects.length; i < ii; i++) {
					valid = valid && selects[i].value;
				}
				for (var i = 0, ii = inputs.length; i < ii; i++) {
					valid = valid && inputs[i].value;
				}
			}
		}
		Site.AM.button.disabled = !valid;
		Site.AM.button.className = valid?"action menu":"disabled action menu";
	};
	log("Action Menu handlers added");
}


function addTopChecker(input) {
	log("Adding top checker handlers");
	var topchecker = {top: input, items: []};
	var column = input.parentNode;
	column.checker = true;
	var tds = column.parentNode.getElementsByTagName(column.tagName);
	var col = 0;
	for (var i = 0, ii = tds.length; i < ii; i++) {
		if (tds[i].checker) {
			break;
		}
		col += (tds[i].getAttribute("colspan"))?tds[i].getAttribute("colspan"):1;
	}
	var tbody = column.parentNode.parentNode.parentNode.getElementsByTagName("tbody")[0];
	if (tbody) {
		var trs = tbody.getElementsByTagName("tr");
		for (var i = 0, ii = trs.length; i < ii; i++) {
			var curcol = 0;
			tds = trs[i].getElementsByTagName("td");
			for (var j = 0, jj = tds.length; j < jj; j++) {
				if (curcol == col) {
					var inputs = tds[j].getElementsByTagName("input");
					for (var k = 0, kk = inputs.length; k < kk; k++) {
						if (inputs[k].getAttribute("type") == "checkbox") {
							topchecker.items.push(inputs[k]);
							break;
						}
					}
					break;
				} else {
					curcol += (tds[j].getAttribute("colspan"))?tds[j].getAttribute("colspan"):1;
				}
			}
		}
		var toplinks = document.getElementById("checkbox-selector");
		var bottomlinks = document.getElementById("checkbox-selector-bottom");
		if (toplinks && bottomlinks) {
			toplinks.style.visibility = "visible";
			bottomlinks.style.visibility = "visible";
			var sall = toplinks.getElementsByTagName("a")[0], snon = toplinks.getElementsByTagName("a")[1];
			var sallb = bottomlinks.getElementsByTagName("a")[0], snonb = bottomlinks.getElementsByTagName("a")[1];
			if (sall && snon && sallb && snonb) {
				sall.onclick = sallb.onclick = function() {
					for (var i = 0, ii = topchecker.items.length; i < ii; i++) {
						if (!topchecker.items[i].disabled) {
							topchecker.items[i].checked = true;
						}
					}
					return false;
				};
				snon.onclick = snonb.onclick = function() {
					for (var i = 0, ii = topchecker.items.length; i < ii; i++) {
						if (!topchecker.items[i].disabled) {
							topchecker.items[i].checked = false;
						}
					}
					return false;
				};
				topchecker.top.style.display = "none";
			} else {
				log("Links were not found in checkbox-selector", ERROR);
			}
		} else {
			log("checkbox-selector was not found", WARNING);
			topchecker.check = function() {
				var allchecked = true;
				for (var i = 0, ii = this.items.length; i < ii; i++) {
					allchecked = allchecked && this.items[i].checked;
					if (!allchecked) {
						break;
					}
				}
				this.top.checked = allchecked;
			};
			topchecker.top.onclick = function() {
				for (var i = 0, ii = topchecker.items.length; i < ii; i++) {
					topchecker.items[i].checked = this.checked;
				}
			};
			for (var i = 0, ii = topchecker.items.length; i < ii; i++) {
				topchecker.items[i].onclick = function() {topchecker.check();};
			}
		}
	}
	log("Top checker handlers added");
}


function addPaginator(input) {
	log("Adding pagination handlers");
	var form = this;
	if (/(^|\s)next(\s|$)/.test(input.className)) {
		form.nextButton = input;
		addEvent(input, "click", function() {
			if (!this.disabled) {
				form.startFrom.value = form.startFrom.value * 1 + form.fetchCount * 1;
			}
		});
		log("Next button hijacked");
	}
	if (/(^|\s)prev(\s|$)/.test(input.className)) {
		form.prevButton = input;
		addEvent(input, "click", function() {
			if (!this.disabled) {
				var start = form.startFrom.value - form.fetchCount;
				form.startFrom.value = (start < 0)?0:start;
			}
		});
		log("Prev button hijacked");
	}
	log("Pagination handlers added");
}


function addPlaceholder(input) {
	var phtext = input.parentNode.getElementsByTagName("span");
	for (var i = 0, ii = phtext.length; i < ii; i++) {
		if (phtext[i].className == "hidden placeholder") {
			// TODO: remove following line later when our CSS would be everywhere
			phtext[i].style.display = "none";
			phtext = phtext[i].innerHTML;
			break;
		}
	}
	if (!phtext) {
		log("Placeholder text wasnâ€™t found for input " + input.id, ERROR);
		return false;
	}
	var className = input.className;
	input.placeholded = (input.value == phtext || input.value == "");
	if (input.placeholded) {
		input.className = className + " placeholded";
		input.value = phtext;
	}
	addEvent(input, "focus", function() {
		if (this.placeholded) {
			this.value = "";
			this.className = className;
			this.placeholded = false;
		}
	});
	addEvent(input, "blur", function() {
		if (this.value == "") {
			this.placeholded = true;
			this.value = phtext;
			this.className = className + " placeholded";
		} else {
			this.placeholded = false;
		}
	});
}


function addToggler(input) {
	var div = document.getElementById(input.className.match(/toggle (.*?)($|\s)/)[1]);
	if (div) {
		addEvent(input, "click", function() {
			if (div.className == "hidden") {
				div.className = "";
			} else {
				div.className = "hidden";
			}
		});
	} else {
		log("Toggler has nothing to toggle", WARNING);
	}
}


function addMandatory(input) {
	function mandatorySubmit(e) {
		if ((/^\s*$/.test(input.value)) && (input.disabled==false)) {
			return stopEvent(e?e:window.event);
		}
	}
	addEvent(this, "submit", mandatorySubmit);
}




// Date Validation Javascript


function valDateFmt(datefmt) {myOption = -1;
for (i=0; i<datefmt.length; i++) {if (datefmt[i].checked) {myOption = i;}}
if (myOption == -1) {alert("You must select a date format");return ' ';}
return datefmt[myOption].value;}

function valDateRng(daterng) {myOption = -1;
for (i=0; i<daterng.length; i++) {if (daterng[i].checked) {myOption = i;}}
if (myOption == -1) {alert("You must select a date range");return ' ';}
return daterng[myOption].value;}

function stripBlanks(fld) {var result = "";for (i=0; i<fld.length; i++) {
if (fld.charAt(i) != " " || c > 0) {result += fld.charAt(i);
if (fld.charAt(i) != " ") c = result.length;}}return result.substr(0,c);}
var numb = '0123456789';

function isValid(parm,val) {if (parm == "") return true;
for (i=0; i<parm.length; i++) {if (val.indexOf(parm.charAt(i),0) == -1)
return false;}return true;}
function isNum(parm) {return isValid(parm,numb);}
var mth = new Array(' ','january','february','march','april','may','june','july','august','september','october','november','december');
var day = new Array(31,28,31,30,31,30,31,31,30,31,30,31);



function controllo_file(file,tipo){
	var ext = file.substr(file.lastIndexOf("."))+".";
	//alert("ddssf"+ext);
//perm = ".gif.jpg.jpeg."
	if(tipo.indexOf(ext.toLowerCase())<0){
		//alert("Attenzione! In Immagine puoi caricare solo file GIF o JPG");
	//document.info.reset();
	//rv = false;
	return false;
	} else {
	//rv = true;
	return true;
	}
	
}


function controllaCF_1(cf)
{
    
	var validi, i, s, set1, set2, setpari, setdisp;
    if( cf == '' )  return '';
    cf = cf.toUpperCase();
    if( cf.length != 16 )
        return "La lunghezza1 del codice fiscale non è\n"
        +"corretta: il codice fiscale dovrebbe essere lungo\n"
        +"esattamente 16 caratteri.\n";
    validi = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for( i = 0; i < 16; i++ ){
        if( validi.indexOf( cf.charAt(i) ) == -1 )
            return "Il codice fiscale contiene un carattere non valido `" +
                cf.charAt(i) +
                "'.\nI caratteri validi sono le lettere e le cifre.\n";
    }
    set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
    setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
    s = 0;
    for( i = 1; i <= 13; i += 2 )
        s += setpari.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
    for( i = 0; i <= 14; i += 2 )
        s += setdisp.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
    if( s%26 != cf.charCodeAt(15)-'A'.charCodeAt(0) )
        return "Il codice fiscale non è corretto:\n"+
            "il codice di controllo non corrisponde.\n";   
	return "";
	
}

function controllaCF(value){
	//var mm="";
	var mm=controllaCF_1(value);	
	if((value.length==16)&&(mm=='')){
		$.ajax({
	   async: false,
	   type: "POST",
	   url: "omocodia.php",
	   data: "codfisc="+value,
	   success: function(msg){				
		  if (parseInt(msg)){ 			
			if (confirm("Attenzione il Codice Fiscale inserito e' stato gia' attribuito ad altro paziente. Vuoi continuare?"))
				{
				mm="";
			}else{
				mm="Attenzione il Codice Fiscale inserito e' stato gia' attribuito ad altro paziente.";
				}			
			}
			else{
			mm="";
			}
	   }
	 });
	}
return mm;	
}	

function validateDate_anni(fld,fmt,rng) {
var dd, mm, yy;var today = new Date;var t = new Date;fld = stripBlanks(fld);

if (fld == '') return false;
var d1 = fld.split('/');



if (d1.length != 3) d1 = fld.split(' ');
if (d1.length != 3) return false;
if (fmt == 'u' || fmt == 'U') {
  dd = d1[1]; mm = d1[0]; yy = d1[2];}
else if (fmt == 'j' || fmt == 'J') {
  dd = d1[2]; mm = d1[1]; yy = d1[0];}
else if (fmt == 'w' || fmt == 'W'){
  dd = d1[0]; mm = d1[1]; yy = d1[2];}
else return false;
var n = dd.lastIndexOf('st');
if (n > -1) dd = dd.substr(0,n);
n = dd.lastIndexOf('nd');
if (n > -1) dd = dd.substr(0,n);
n = dd.lastIndexOf('rd');
if (n > -1) dd = dd.substr(0,n);
n = dd.lastIndexOf('th');
if (n > -1) dd = dd.substr(0,n);
n = dd.lastIndexOf(',');
if (n > -1) dd = dd.substr(0,n);
n = mm.lastIndexOf(',');
if (n > -1) mm = mm.substr(0,n);
if (!isNum(dd)) return false;
if (!isNum(yy)) return false;
if (!isNum(mm)) {
  var nn = mm.toLowerCase();
  for (var i=1; i < 13; i++) {
    if (nn == mth[i] ||
        nn == mth[i].substr(0,3)) {mm = i; i = 13;}
  }
}
if (!isNum(mm)) return false;
dd = parseFloat(dd); mm = parseFloat(mm); yy = parseFloat(yy);
if (yy < 100) yy += 2000;
if (yy < 1582 || yy > 4881) return false;
if (mm == 2 && (yy%400 == 0 || (yy%4 == 0 && yy%100 != 0))) day[mm-1]++;
if (mm < 1 || mm > 12) return false;
if (dd < 1 || dd > day[mm-1]) return false;
t.setDate(dd); t.setMonth(mm-1); t.setFullYear(yy);
if (rng == 'p' || rng == 'P') {
	if (t > today) 
	return false;
	
	else
	{
	diff=today-t;
	return diff;
	}
}	
else if (rng == 'f' || rng == 'F') 
{
	if (t < today) 
	 return false;
	else
	{
	 diff=today-t;
	 return diff;
	}

}
else if (rng != 'a' && rng != 'A') return false;
return true;
}



function validateDate(fld,fmt,rng) {
var dd, mm, yy;var today = new Date;var t = new Date;fld = stripBlanks(fld);

if (fld == '') return false;
var d1 = fld.split('/');



if (d1.length != 3) d1 = fld.split(' ');
if (d1.length != 3) return false;
if (fmt == 'u' || fmt == 'U') {
  dd = d1[1]; mm = d1[0]; yy = d1[2];}
else if (fmt == 'j' || fmt == 'J') {
  dd = d1[2]; mm = d1[1]; yy = d1[0];}
else if (fmt == 'w' || fmt == 'W'){
  dd = d1[0]; mm = d1[1]; yy = d1[2];}
else return false;
var n = dd.lastIndexOf('st');
if (n > -1) dd = dd.substr(0,n);
n = dd.lastIndexOf('nd');
if (n > -1) dd = dd.substr(0,n);
n = dd.lastIndexOf('rd');
if (n > -1) dd = dd.substr(0,n);
n = dd.lastIndexOf('th');
if (n > -1) dd = dd.substr(0,n);
n = dd.lastIndexOf(',');
if (n > -1) dd = dd.substr(0,n);
n = mm.lastIndexOf(',');
if (n > -1) mm = mm.substr(0,n);
if (!isNum(dd)) return false;
if (!isNum(yy)) return false;
if (!isNum(mm)) {
  var nn = mm.toLowerCase();
  for (var i=1; i < 13; i++) {
    if (nn == mth[i] ||
        nn == mth[i].substr(0,3)) {mm = i; i = 13;}
  }
}
if (!isNum(mm)) return false;
dd = parseFloat(dd); mm = parseFloat(mm); yy = parseFloat(yy);
if (yy < 100) yy += 2000;
if (yy < 1582 || yy > 4881) return false;
if (mm == 2 && (yy%400 == 0 || (yy%4 == 0 && yy%100 != 0))) day[mm-1]++;
if (mm < 1 || mm > 12) return false;
if (dd < 1 || dd > day[mm-1]) return false;
t.setDate(dd); t.setMonth(mm-1); t.setFullYear(yy);
if (rng == 'p' || rng == 'P') {
if (t > today) return false;
}
else if (rng == 'f' || rng == 'F') {
if (t < today) return false;
}
else if (rng != 'a' && rng != 'A') return false;
return true;
}

function show_with_filter(input, url) {
	var tail ='';
	if(input.checked = false ) {
		tail = "&show_all=y";
		document.location.href = url + tail;
		input.checked = true;
	}
	else {
		tail = "&show_all=n";
		document.location.href = url + tail;
		input.checked = false;
	}
	
}
// Initialization


if (Site.forms.length) {
	for (var i = Site.forms.length - 1; i >= 0; i--) {
		log("About to prepare form #" + (i + 1));
		prepare(Site.forms[i]);
		log("Form prepared");
		var inputs = Site.forms[i].elements;
		for (var j = inputs.length - 1; j >= 0; j--) {
			if (/(^|\s)list-of-values-\d+(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addLOV(inputs[j]);
			}
			if (/(^|\s)date-picker(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addCalendar(inputs[j]);
			}
			if (/(^|\s)time-picker(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addTimePicker(inputs[j]);
			}
			if (/(^|\s)change-acti-to-\d+(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addSubmit(inputs[j]);
			}
			if (/(^|\s)action menu(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addActionMenu(inputs[j]);
			}
			if (/(^|\s)top checker(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addTopChecker(inputs[j]);
			}
			
			if (/(^|\s)placeholder(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addPlaceholder(inputs[j]);
			}
			
			
			
			if (/(^|\s)toggle(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addToggler(inputs[j]);
			}
			if (/(^|\s)mandatory(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addMandatory(inputs[j]);
			}
		}
		log("Form inputs hooked");
	}
}



function inizializza()
{


disabilitaTuttiIForm();


if (Site.forms.length) {
	for (var i = Site.forms.length - 1; i >= 0; i--) {
		log("About to prepare form #" + (i + 1));
		prepare(Site.forms[i]);
		log("Form prepared");
		var inputs = Site.forms[i].elements;
		for (var j = inputs.length - 1; j >= 0; j--) {
			if (/(^|\s)list-of-values-\d+(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addLOV(inputs[j]);
			}
			if (/(^|\s)date-picker(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addCalendar(inputs[j]);
			}
			if (/(^|\s)time-picker(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addTimePicker(inputs[j]);
			}
			if (/(^|\s)change-acti-to-\d+(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addSubmit(inputs[j]);
			}
			if (/(^|\s)action menu(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addActionMenu(inputs[j]);
			}
			if (/(^|\s)top checker(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addTopChecker(inputs[j]);
			}
			
			if (/(^|\s)placeholder(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addPlaceholder(inputs[j]);
			}
			
			
			
			if (/(^|\s)toggle(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addToggler(inputs[j]);
			}
			if (/(^|\s)mandatory(\s|$)/.test(inputs[j].className)) {
				Site.forms[i].addMandatory(inputs[j]);
			}
		}
		log("Form inputs hooked");
	}
}
return true;
}
