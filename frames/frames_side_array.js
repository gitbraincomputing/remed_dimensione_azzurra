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



plain_style=[				// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
"navy",						// Mouse Off Font Color
"ccccff",					// Mouse Off Background Color
"ffebcd",					// Mouse On Font Color
"4b0082",					// Mouse On Background Color
"000000",					// Menu Border Color 
12,							// Font Size (default is px but you can specify mm, pt or a percentage)
"normal",					// Font Style (italic or normal)
"bold",						// Font Weight (bold or normal)
"Verdana, Tahoma, Arial, Helvetica, sans-serif",	// Font Name
4,							// Menu Item Padding
"arrow.gif",				// Sub Menu Image (Leave this blank if not needed)
,							// 3D Border & Separator bar
"66ccff",					// 3D High Color
"000099",					// 3D Low Color
"Purple",					// Current Page Item Font Color (leave this blank to disable)
"pink",						// Current Page Item Background Color (leave this blank to disable)
"images/3darrow_down.gif",	// Top Bar image (Leave this blank to disable)
"ffffff",					// Menu Header Font Color (Leave blank if headers are not needed)
"000099",					// Menu Header Background Color (Leave blank if headers are not needed)
]




addmenu(menu=[				// This is the array that contains your menu properties and details
"simplemenu1",				// Menu Name - This is needed in order for the menu to be called
36,							// Menu Top - The Top position of the menu in pixels
10,							// Menu Left - The Left position of the menu in pixels
110,						// Menu Width - Menus width in pixels
1,							// Menu Border Width 
,							// Screen Position - here you can use "center;left;right;middle;top;bottom" or a combination of "center:middle"
plain_style,				// Properties Array - this is set higher up, as above
1,							// Always Visible - allows the menu item to be visible at all time (1=on/0=off)
"left",						// Alignment - sets the menu elements text alignment, values valid here are: left, right or center
effect,						// Filter - Text variable for setting transitional effects on menu activation - see above for more info
,							// Follow Scrolling Top Position - Tells the menu to follow the user down the screen on scroll placing the menu at the value specified.
, 							// Horizontal Menu - Tells the menu to become horizontal instead of top to bottom style (1=on/0=off)
,							// Keep Alive - Keeps the menu visible until the user moves over another menu or clicks elsewhere on the page (1=on/0=off)
,							// Position of TOP sub image left:center:right
,							// Set the Overall Width of Horizontal Menu to 100% and height to a specified amount
,							// Right To Left - Used in Hebrew for example. (1=on/0=off)
,							// Open the Menus OnClick - leave blank for OnMouseover (1=on/0=off)
,							// ID of the div you want to hide on MouseOver (useful for hiding form elements)
,							// Reserved for future use
,							// Reserved for future use
,							// Reserved for future use
// "Menu Item Text", "URL", "Alternate URL for submenu holders", "Status Text", "Separator Bar Width"
,"Home","/menu/ target=_top;sourceframe=main;",,,1 
,"Languages","show-menu=languages target=main;sourceframe=main;",,"#",1
,"Databases","show-menu=databases target=main;sourceframe=main;",,"#",1
,"Operating Systems","show-menu=oses target=main;sourceframe=main;",,"#",1
,"Web","show-menu=web target=main;sourceframe=main;",,"#",1
,"Home","/menu/ target=_top;sourceframe=main;",,,1 
,"Case Studies","show-menu=web target=main;sourceframe=main;",,,1
,"Contact Us","http://www.emeralys.net/contact.html target=main;sourceframe=contact;",,,1

])


dumpmenus()