fixMozillaZIndex=true; //Fixes Z-Index problem  with Mozilla browsers but causes odd scrolling problem, toggle to see if it helps
_menuCloseDelay=500;
_menuOpenDelay=150;
_subOffsetTop=2;
_subOffsetLeft=-2;


with(menuStyle=new mm_style()){
bordercolor="#999999";
borderstyle="solid";
borderwidth=1;
fontfamily="Verdana, Tahoma, Arial";
fontsize="75%";
fontstyle="normal";
headerbgcolor="#ffffff";
headercolor="#000000";
offbgcolor="#eeeeee";
offcolor="#000000";
onbgcolor="#ddffdd";
oncolor="#000099";
outfilter="randomdissolve(duration=0.3)";
overfilter="Fade(duration=0.2);Alpha(opacity=90);Shadow(color=#777777', Direction=135, Strength=3)";
padding=4;
pagebgcolor="#82B6D7";
pagecolor="black";
separatorcolor="#999999";
separatorsize=1;
subimage="arrow.gif";
subimagepadding=2;
}

with(milonic=new menuname("Main Menu")){
alwaysvisible=1;
left=10;
orientation="horizontal";
style=menuStyle;
top=10;
aI("showmenu=Samples;text=ricerca;");
}

with(milonic=new menuname("Samples")){
overflow="scroll";
style=menuStyle;
aI("text=cerca struttura;url=struct.php;")
aI("text=cerca manager;url=struct.php;")
aI("text=cerca prodotto;url=struct.php;")
aI("text=cerca altro dato;url=struct.php;")

}

drawMenus();

