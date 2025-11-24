/*
 Milonic DHTML Website Navigation Menu
 Written by Andy Woolley - Copyright 2002 (c) Milonic Solutions Limited. All Rights Reserved.
 Please visit http://www.milonic.com/ for more information.
 
 The Free use of this menu is only available to Non-Profit, Educational & Personal web sites.
 Commercial and Corporate licenses  are available for use on all other web sites & Intranets.
 All Copyright notices MUST remain in place at ALL times and, please keep us informed of your 
 intentions to use the menu and send us your URL.
 
 If you are having difficulty with the menu please read the FAQ at http://www.milonic.com/faq.php before contacting us.

*/

//The following line is critical for menu operation, and MUST APPEAR ONLY ONCE. If you have more than one menu_array.js file rem out this line in subsequent files
menunum=0;menus=new Array();_d=document;function addmenu(){menunum++;menus[menunum]=menu;}function dumpmenus(){mt="<script language=javascript>";for(a=1;a<menus.length;a++){mt+=" menu"+a+"=menus["+a+"];"}mt+="<\/script>";_d.write(mt)}
//Please leave the above line intact. The above also needs to be enabled if it not already enabled unless this file is part of a multi pack.



////////////////////////////////////
// Editable properties START here //
////////////////////////////////////


// Special effect string for IE5.5 or above please visit http://www.milonic.com/filters_sample.php for more filters
if(navigator.appVersion.indexOf("MSIE 6.0")>0){
	effect = "Fade(duration=0.2);Alpha(style=0,opacity=88);Shadow(color='#777777', Direction=135, Strength=5)"
}
else{
	effect = "Shadow(color='#777777', Direction=135, Strength=5)"
}


timegap=500					// The time delay for menus to remain visible
followspeed=5				// Follow Scrolling speed
followrate=40				// Follow Scrolling Rate
suboffset_top=10;			// Sub menu offset Top position 
suboffset_left=10;			// Sub menu offset Left position
Frames_Top_Offset=0 		// Frames Page Adjustment for Top Offset
Frames_Left_Offset=-36		// Frames Page Adjustment for Left Offset



plain_style=[				// Menu Properties Array
"navy",						// Off Font Color
"ccccff",					// Off Back Color
"FFEBCD",					// On Font Color
"4B0082",					// On Back Color
"000000",					// Border Color
12,							// Font Size
"normal",					// Font Style
"bold",						// Font Weight
"Verdana, Tahoma, Arial, Helvetica",	// Font
4,							// Padding
"arrow.gif",				// Sub Menu Image (Leave this blank if not needed)
,							// 3D Border & Separator bar
"66ffff",					// 3D High Color
"000099",					// 3D Low Color
,							// Current Page Item Font Color (leave this blank to disable)
,							// Current Page Item Background Color (leave this blank to disable)
"arrowdn.gif",				// Top Bar image (Leave this blank to disable)
]


addmenu(menu=[		// This is the array that contains your menu properties and details
"simplemenu1",		// Menu items Name
36,					// Top
140,				// left
,					// Width
1,					// Border Width
,					// Screen Position - here you can use "center;left;right;middle;top;bottom"
plain_style,		// Properties Array - this is set higher up, as above
1,					// Always Visible - allows the menu item to be visible at all time
"left",				// Alignment - sets the menu elements alignment, HTML values are valid here for example: left, right or center
effect,				// Filter - Text variable for setting transitional effects on menu activation
,					// Follow Scrolling - Tells the menu item to follow the user down the screen
1, 					// Horizontal Menu - Tells the menu to be horizontal instead of top to bottom style
,					// Keep Alive - Keeps the menu visible until the user moves over another menu or clicks elsewhere on the page (1=on/0=off)
,					// Position of TOP sub image left:center:right
,					// Set the Overall Width of Horizontal Menu to 100% and height to the specified amount (Leave blank to disable)
,					// Right To Left - Used in Hebrew for example. (1=on/0=off)
,					// Open the Menus OnClick - leave blank for OnMouseover (1=on/0=off)
,					// ID of the div you want to hide on MouseOver (useful for hiding form elements)
,					// Background image for menu when BGColor set to transparent.
,					// Scrollable Menu
,					// Reserved for future use
,"Home","/menu/ target=_top;sourceframe=main;",,,1 // "Description Text", "URL", "Alternate URL", "Status", "Separator Bar"
,"Languages&nbsp;&nbsp;","show-menu=languages target=main;sourceframe=main;",,"#",1
,"Databases&nbsp;&nbsp;","show-menu=databases target=main;sourceframe=main;",,"#",1
,"Operating&nbsp;Systems&nbsp;&nbsp;","show-menu=oses target=main;sourceframe=main;",,"#",1
,"Web&nbsp;&nbsp;","show-menu=web target=main;sourceframe=main;",,"#",1
])


dumpmenus()