/*
developed by Antonio Esposito - 07/05/2008 

Overview:
This function is used for div collapse. It is an Hard Code function and required to make one more for any custumer with different conditions.
Function does the follow steps:

1. Check if a particolar field is present in the page (this operation is important because It can undestand which step is loading
2. Make an array of fields that we want to hide to default
3. Call check function 
	- if you want to hide fields you need to pass the flag=true
	- if you want to show you need to pass the flag=false
4. Show particular div if particular conditions are true

*/

/* insert into xsl file the javascript function : convert_comobo(this.value); and for bodyonload ('d_v_p_convert_combo') */ 
function setFormFocus(formId){
   var form = document.getElementById(formId);
    var len = form.elements.length;

    for(var i = 0;i < len;i++){
      var curElement = form.elements[i];

      if((curElement.constructor  == HTMLInputElement) && (curElement.type!='hidden') && (curElement.disabled==false) ){
		
        curElement.focus();
        return;
      }
    }
}


function convert_combo(value)
{

	var arr_dipendance=new Array('C30','C31');

	//main condition
	if ( (value=='d_v_p_convert_combo') || (check_array_value(arr_dipendance,value))){

		// to determinate the current step
		// if the control is true it mean that this is the step that we need
		if ((document.getElementById('C30'))){

			var obj1= document.getElementById('C31');
			var obj2= document.getElementById('C30');

			// every time we call this function we need to hidd all dinamic div. It's is very important because when the page start we need to show only some div and to hidden others.
			noview_div= new Array('C35','C34','C52','C53','C38','C39','C40','C13');
			check(noview_div,true);

			view_div= new Array('C13');
			check(view_div,false);
			
					//condition 1
					if ( (obj1.name=='C31') && ( (InStr(obj1.value,'Permanent'))|| (InStr(obj1.value,'Fixed')) )){
						//condition 1.1.1
						
						
						if (InStr(obj1.value,'Permanent'))
						{
						noview_div= new Array('C13');
						check(noview_div,true);
						}
						
						
						if ( (obj2.name=='C30')&&(InStr(obj2.value,'New'))){
							
							view_div= new Array('C35');
							check(view_div,false);
						}
						//condition 1.1.2
						else if ( (obj2.name=='C30')&&(InStr(obj2.value,'Replacement'))){
							view_div= new Array('C34','C35');
							check(view_div,false);
						}
						//condition 1.2.1
						if (InStr(obj1.value,'Part')){
								
								view_div= new Array('C52','C53');
								//document.getElementById('C35').value='';
								check(view_div,false);		
						}

						view_div= new Array('C38','C39','C40');
						check(view_div,false);			


						// autoset N. of positions to 1
						if ( (obj2.name=='C30')&&(InStr(obj2.value,'New'))){
							document.getElementById('C35').readOnly=false;
							
						}
						

					}
					
				
					//condition 3
					else if ( (obj1.name=='C31') && ((InStr(obj1.value,'Temporary')))){
					document.getElementById('C35').readOnly=false;
					
						view_div= new Array('C35','C38');
						check(view_div,false);		
							//condition 3.1
							if (InStr(obj1.value,'Part')){
								view_div= new Array('C52','C53');
								check(view_div,false);	
							}
							//default div for condition 3
							/*view_div= new Array('C45','C46');
							check(view_div,false);	*/
					}
					
					
					
					
					
		}//end condition current step
		
		if ((document.getElementById('C30'))){
		
		if ( (obj2.name=='C30')&&(InStr(obj2.value,'Replacement'))){
							view_div= new Array('C35');
							check(view_div,false);
							document.getElementById('C35').value='1';
							document.getElementById('C35').readOnly=true;
					}
		}			
		
	}//end main condition

	//if (value=='d_v_p_convert_combo')
	//setFormFocus('form_acti');
	
		
	
}//end function


function check_array_value(arraytochek,value)
{
	for (i=0;i<arraytochek.length;i++){
		if (arraytochek[i]==value)
			return true;
	}
	return false;
}


function check(iddiv,enable)
{
	//number of div to change
	var len=iddiv.length;
	var divhidden='';
	var div='';

	for (j=0;j<len;j++){

			div=iddiv[j];
			enable_disable(div,enable);
			divhidden='div'+div;

		if (document.getElementById(divhidden))
		{
		if (enable==false) 
			document.getElementById(divhidden).style.display = "block";
		else
			document.getElementById(divhidden).style.display = "none";
			}
			enable_disable(div,enable)	
	}//end for
		

}//end function


function enable_disable(id,ena)
{
	
	document.getElementById(id).disabled=ena;
	
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