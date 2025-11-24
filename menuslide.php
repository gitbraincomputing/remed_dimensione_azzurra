<?php 
include_once('include/reserved/config.inc.php');
include_once('include/session.inc.php');
include_once('include/functions.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">

*{
	margin: 0px;
	padding: 0px;
}
body{
	margin: 0px;
	text-align:left;
	background-color:#FFF;
	font-family:Arial, Sans-Serif;
	font-size:0.75em;
}
#menu
{
	height: auto;
	width:195px;
	display:none;
	float:left;
	clear: both;
	border-bottom: 1px solid #CCC;
}
#contactLink
{
	height:21px;
	width:185px;
	background: url('images-new/menu-nascondimostra-up.gif') top right no-repeat;
	display:block;
	cursor:pointer;
	float:left;
	clear: both;
	font-size:11px;
	padding: 6px 0 0 10px;
}

#top{
	float: left;
	width: 100%;
	}
	#top img{
		padding: 20px;
	}

#wrap-bar{
	float: left;
	width: 100%;
	background: url('images-new/menu-repeat.gif') top right repeat-x;
}

#wrap-content{
	float: right;
	width: 100%;
	background-color: #999999;
}

/* per accordion */
.urbangreymenu{
	width: 190px; /*width of menu*/
}

.urbangreymenu .headerbar{
	font-size:11px;
	padding: 7px 0 0 10px;
	color: #000;
	text-transform: uppercase;
	height:20px;
	width:185px;
	background: #CCC url('images-new/menu-tab.gif') top right no-repeat;
	}
	.urbangreymenu .headerbar a{
		text-decoration: none;
		color: #000;
		display: block;
		}
	
.urbangreymenu ul{
	list-style-type: none;
	}
	.urbangreymenu ul li{
		padding-top: 1px; /*bottom spacing between menu items*/
		background: #CCC;
		}
		.urbangreymenu ul li a{
			font: 11px;
			color: black;
			background: #F6F6F6;
			display: block;
			line-height: 17px;
			padding-left: 8px; /*link text is indented 8px*/
			text-decoration: none;
		}
		.urbangreymenu ul li a:visited{
			color: #000;
			}
			.urbangreymenu ul li a:hover{ /*hover state CSS*/
				color: #000;
				font-weight:bold;
				background: #ccc;
				}

</style>
<script type="text/javascript" src="jquery/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="jquery/menu/ddaccordion.js"></script>
<script>
$(document).ready(function(){

	//mostro nascondo il menu e vario la larghezza del content principale
	$("#contactLink").click(function(){
		if ($("#menu").is(":hidden")){
			
			width_finestra = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
			width_finestra = width_finestra - 195;
			$("#wrap-content").animate({ 
			width: width_finestra,
			}, 1500, function()	{
				 			$("#menu").slideDown("slow");
				 			}
			);
			
		}
		else{
			$("#menu").slideUp("slow", function() {
      									 $("#wrap-content").animate({ 
											width: "100%",
											}, 1000 );
    									 }
								);
			
		}
	});
                
});

//per effetto accordion
ddaccordion.init({
	headerclass: "headerbar", //Shared CSS class name of headers group
	contentclass: "submenu", //Shared CSS class name of contents group
	revealtype: "mouseover", //Reveal content when user clicks or onmouseover the header? Valid value: "click" or "mouseover
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false
	defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: true, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", "selected"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "normal", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
});

</script>
</head>

<body>

<div id="top">
	<img src="images-new/remedlogo.gif" />
</div>
<div id="wrap-bar">
	<div id="contactLink"> mostra / nascondi menù</div>
</div>
<div id="menu" class="urbangreymenu">
<? 

$conn = db_connect();
for($i=1; $i<=5; $i++) {
?>
<h3 class="headerbar"><a href="#"><?="livello".$i?></a></h3>
<ul class="submenu">
<?
	$key = 0;
	$query = "SELECT id,nome,link FROM menu WHERE (livello = ".$i.") AND (status = 1) ORDER BY ordine";
	$result = mssql_query($query, $conn);
	
	
	if(!$result) error_message(mssql_error());
		while($row = mssql_fetch_assoc($result)) {
			$id = $row['id'];
			$nome_menu = $row['nome'];
			$link_menu = $row['link'];
		
			if(in_array($id, $_SESSION['PERMESSI'])) 
			{
?>
	<li><a href="principale.php?page=<?=$link_menu?>" title="<?$nome_menu?>"><?=$nome_menu?></a></li>
<?
	}
	$key++;
	}
	print("</ul>");
} 
?>

</div>


<div id="wrap-content">
		<p>
    gòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdf
gòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdfgòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdfgòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdfgòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdfgòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdfgòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdf
<br />
<br />
gòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdfgòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdf
<br />
gòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdfgòfkdfgj dfklgj lfkòsdjh gkljklòj gklòsfj hkljsfh lògkdfj hkld j dj klòdfj hldjh lòdf
    </p>

</div>



</body>
</html>
