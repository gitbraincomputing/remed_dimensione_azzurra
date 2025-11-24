<script type="text/javascript">

			$("#flex1").flexigrid
			(
			{
			url: '../re_pazienti_ricerca.php',
			dataType: 'json',
			colModel : [
				{display: 'id', name : 'idutente', width : 50, sortable : true, align: 'center'},
				{display: 'Cognome', name : 'cognome', width : 150, sortable : true, align: 'left'},
				{display: 'Nome', name : 'nome', width : 150, sortable : true, align: 'left'},
				{display: 'Data di nascita', name : 'datanascita', width : 100, sortable : true, align: 'left'},
				],
			buttons : [
				{name: 'aggiungi', bclass: 'add', onpress : funzioni_tasto},
				{separator: true},
				{name: 'elimina', bclass: 'delete', onpress : funzioni_tasto},
				{separator: true},
				{name: 'A', onpress: sortAlpha},
                {name: 'B', onpress: sortAlpha},
				{name: 'C', onpress: sortAlpha},
				{name: 'D', onpress: sortAlpha},
				{name: 'E', onpress: sortAlpha},
				{name: 'F', onpress: sortAlpha},
				{name: 'G', onpress: sortAlpha},
				{name: 'H', onpress: sortAlpha},
				{name: 'I', onpress: sortAlpha},
				{name: 'J', onpress: sortAlpha},
				{name: 'K', onpress: sortAlpha},
				{name: 'L', onpress: sortAlpha},
				{name: 'M', onpress: sortAlpha},
				{name: 'N', onpress: sortAlpha},
				{name: 'O', onpress: sortAlpha},
				{name: 'P', onpress: sortAlpha},
				{name: 'Q', onpress: sortAlpha},
				{name: 'R', onpress: sortAlpha},
				{name: 'S', onpress: sortAlpha},
				{name: 'T', onpress: sortAlpha},
				{name: 'U', onpress: sortAlpha},
				{name: 'V', onpress: sortAlpha},
				{name: 'W', onpress: sortAlpha},
				{name: 'X', onpress: sortAlpha},
				{name: 'Y', onpress: sortAlpha},
				{name: 'Z', onpress: sortAlpha}
				],
			searchitems : [
				{display: 'Cognome', name : 'cognome', isdefault: true},
				{display: 'Nome', name : 'nome'}
				],
			sortname: "cognome",
			sortorder: "asc",
			usepager: true,
			title: 'Anagrafica Utenti',
			useRp: true,
			rp: 15,
			width: "auto",
			//onSubmit: addFormData,
			height: "auto",
			
			showTableToggleBtn: true
			
			}
			);
			
			
function doppioclick(elemento){
window.location.href='lista_pazienti.php?do=review&id='+elemento;
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
			window.location.href='lista_pazienti.php?do=add';
           
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

	
</script>