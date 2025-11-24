/*
   Milonic DHTML Menu HTML Unordered List Based Module version 1.3 - 20th October 2005
   This module is only compatible with the Milonic DHTML Menu version 5.7 or higher

   Copyright 2004 (c) Milonic Solutions Limited. All Rights Reserved.
   This is a commercial software product, please visit http://www.milonic.com/ for more information.
   
   The object of this module is to enable data entered as a HTML un-ordered list to be used inside menus.
   Data for menu items is taken from HTML Lists and used for menus. Styles are created as normal DHTML Menus and referenced as a CSS classname.
*/


_y5=0;
_y3=new Array();
_y2=0;
_y1=0;
for(_a=0;_a<_d.links.length;_a++){
_y4=_d.links[_a];
	pE=_y4.parentNode;
	if(pE.tagName=="LI"){
		while(pE){
			if(pE.tagName=="UL"){
				
				if(!pE.id){
					
					pE.id="mmenu"+_y5;
					pE.style.display="none"
					_y3[_y5]=new Array();
					if(_y1)_y3[_y1][_y3[_y1].length-1]+=";showmenu=listmenu"+_y5+";";
					if(pE.className)_y3[_y5].menuStyle=pE.className
					_y5++;
				}
				break;
			}
			pE=pE.parentNode;
		}
		_y1=pE.id.substr(5,3);
		if(sfri||ie)txt=_y4.innerHTML;else txt=_y4.text;
		_y3[_y1][_y3[_y1].length]="text="+txt+";"
		if(_y4.href.substr(_y4.href.length-1,1)!="#")_y3[_y1][_y3[_y1].length-1]+="url="+_y4+";"
		_y2++;
	}
}

_y6="";
for(_a=0;_a<_y3.length;_a++){
	_y6+="with(milonic=new menuname('listmenu"+_a+"')){"
	if(_y3[_a].menuStyle)_y6+="style="+_y3[_a].menuStyle+";";else _y6+="style=listStyle;";
	if(_a==0)_y6+=mainMenuSettings;
	for(_b=0;_b<_y3[_a].length;_b++){_y6+="aI(\""+_y3[_a][_b]+";\");\n"}
	_y6+="}"
}
_d.write("<script>"+_y6.replace(/\n/g,"")+"drawMenus();<\/script>")