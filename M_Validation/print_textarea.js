function copytodiv(field)
	{
	    text=field.value;
	    text=text.replace(/\n/g,"<br/>");
		x=create_div(field);
		
		var divpreview='preview'+field.id;
	    document.getElementById(divpreview).innerHTML=text;
	
	}

function create_div(field)
{	
	var divpreview='preview'+field.id;
	if (!document.getElementById(divpreview))
	{
		var div = document.createElement("div");
		div.className="preview";
		div.id="preview"+field.id;
		
		field.parentNode.insertBefore(div, field.nextSibling);
	}
	return true;
}	

function preparetoprint()
{
	form=document.forms[0];
	for (var i = 0, ii = form.elements.length; i < ii; i++) {
		
		
		if( form.elements[i].type == "textarea" )
		{
		
		copytodiv(form.elements[i]);
		}
		
		
	}
	
	




}