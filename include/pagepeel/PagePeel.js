// ONLY EDIT THE SELECTIONS WITH A "//" NOTE AFTER IT

var pagepeel = new Object();

pagepeel.ad_url = escape('http://www.networkmatrimonio.it'); // Link Address when ad is clicked on

pagepeel.small_path = 'http://netmat.braincomputing.com/netmat/republic/include/pagepeel/small.swf'; // Path where you placed the small.swf
pagepeel.small_image = escape('http://netmat.braincomputing.com/netmat/republic/include/pagepeel/images/small.jpg'); // Small Image before hover (.jpg, .gif, .png, .swf) - 75X75
pagepeel.small_width = '100';
pagepeel.small_height = '100';
pagepeel.small_params = 'ico=' + pagepeel.small_image;

pagepeel.big_path = 'http://netmat.braincomputing.com/netmat/republic/include/pagepeel/big.swf'; // Path where you placed the big.swf
pagepeel.big_image = escape('http://netmat.braincomputing.com/netmat/republic/include/pagepeel/images/big.jpg'); // Large Image after hover (.jpg, .gif, .png, .swf) - 500X500
pagepeel.big_width = '650';
pagepeel.big_height = '650';
pagepeel.big_params = 'big=' + pagepeel.big_image + '&ad_url=' + pagepeel.ad_url;

function sizeup987(){
	document.getElementById('PagePeelBig').style.top = '0px';
	document.getElementById('PagePeelSmall').style.top = '-1000px';
}

function sizedown987(){
	document.getElementById("PagePeelSmall").style.top = "0px";
	document.getElementById("PagePeelBig").style.top = "-1000px";
}

pagepeel.putObjects = function () {
// <PagePeelSmall>
document.write('<div id="PagePeelSmall" style="position:absolute;width:'+ pagepeel.small_width +'px;height:'+ pagepeel.small_height +'px;z-index:9999;right:0px;top:0px;">');
// object
document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"');
document.write(' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0"');
document.write(' id="PagePeelSmallObject" width="'+pagepeel.small_width+'" height="'+pagepeel.small_height+'">');
// object params
document.write(' <param name="allowScriptAccess" value="always"/> ');
document.write(' <param name="movie" value="'+ pagepeel.small_path +'?'+ pagepeel.small_params +'"/>');
document.write(' <param name="wmode" value="transparent" />');
document.write(' <param name="quality" value="high" /> ');
document.write(' <param name="FlashVars" value="'+pagepeel.small_params+'"/>');
// embed
document.write('<embed src="'+ pagepeel.small_path + '?' + pagepeel.small_params +'" name="PagePeelSmallObject" wmode="transparent" quality="high" width="'+ pagepeel.small_width +'" height="'+ pagepeel.small_height +'" flashvars="'+ pagepeel.small_params +'" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>');
document.write('</object></div>');
document.write('</script>');
// </PagePeelSmall>
// <PagePeelBig>
document.write('<div id="PagePeelBig" style="position:absolute;width:'+ pagepeel.big_width +'px;height:'+ pagepeel.big_height +'px;z-index:9999;right:0px;top:0px;">');
// object
document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"');
document.write(' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0"');
document.write(' id="PagePeelBigObject" width="'+ pagepeel.big_width +'" height="'+ pagepeel.big_height +'">');
// object params
document.write(' <param name="allowScriptAccess" value="always"/> ');
document.write(' <param name="movie" value="'+ pagepeel.big_path +'?'+ pagepeel.big_params +'"/>');
document.write(' <param name="wmode" value="transparent"/>');
document.write(' <param name="quality" value="high" /> ');
document.write(' <param name="FlashVars" value="'+ pagepeel.big_params +'"/>');
// embed
document.write('<embed src="'+ pagepeel.big_path + '?' + pagepeel.big_params +'" id="PagePeelBigEmbed" name="PagePeelBigObject" wmode="transparent" quality="high" width="'+ pagepeel.big_width +'" height="'+ pagepeel.big_height +'" flashvars="'+ pagepeel.big_params +'" swliveconnect="true" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>');
document.write('</object></div>');
// </PagePeelBig>
setTimeout('document.getElementById("PagePeelBig").style.top = "-1000px";',1000);
}
pagepeel.putObjects();