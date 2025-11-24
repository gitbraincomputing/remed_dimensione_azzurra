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


timegap=3000					// The time delay for menus to remain visible
followspeed=5				// Follow Scrolling speed
followrate=40				// Follow Scrolling Rate
suboffset_top=10;			// Sub menu offset Top position 
suboffset_left=10;			// Sub menu offset Left position
Frames_Top_Offset=0 		// Frames Page Adjustment for Top Offset
Frames_Left_Offset=110		// Frames Page Adjustment for Left Offset



plain_style=[				// Menu Properties Array
"FFF",						// Off Font Color
"758791",					// Off Back Color
"FFF",					// On Font Color
"bbc0c3",					// On Back Color
"000000",					// Border Color
12,							// Font Size
"normal",					// Font Style
"bold",						// Font Weight
"Verdana, Tahoma, Arial, Helvetica",	// Font
0,							// Padding
"arrow.gif"					// Sub Menu Image
,							// 3D Border & Separator
,"66ccff"					// 3D High Color
,"000099"					// 3D Low Color
]


addmenu(menu=["languages2",
,,308,1,"",plain_style,,,effect,,,,,,,,,,,,
,"<img src=images/st.gif width=10 height=10 border=0/> aziende 1", "history.php?tp=php",,,0
,"<img src=images/st.gif width=10 height=10 border=0/> aziende 2", "history.php?tp=php",,,0
,"<img src=images/st.gif width=10 height=10 border=0/> aziende 3", "history.php?tp=php",,,0
,"<img src=images/st.gif width=10 height=10 border=0/> aziende 4", "history.php?tp=php",,,0
])


//<TAG_MENU_1>


addmenu(menu=["databases",
,,100,1,"",plain_style,,"left",effect,,,,,,,,,,,,
,"MySQL","history.php?tp=mysql",,,0
,"mSQL","history.php?tp=msql",,,0
,"MS-SQL","history.php?tp=mssql",,,0
,"SyBase","history.php?tp=sybase",,,0
,"Informix","history.php?tp=informix",,,0
,"IBM DB/2","history.php?tp=db2",,,0
])



addmenu(menu=["oses",
,,100,1,"",plain_style,,"left",effect,,,,,,,,,,,,
,"FreeBSD","history.php?tp=freebsd",,,
,"Red Hat Linux","history.php?tp=redhatlinux",,,
,"Windows","show-menu=windows",,,
])


addmenu(menu=["windows",
	,,120,1,"",plain_style,,"left",effect,,,,,,,,,,,,
	,"Windows 95","history.php?tp=win95",,,1
	,"Windows 98","history.php?tp=win98",,,1
	,"Windows ME","history.php?tp=winme",,,1
	,"Windows NT","history.php?tp=winnt",,,1
	,"Windows 2000","history.php?tp=win2000",,,1
	])

addmenu(menu=["web",,,120,1,,plain_style,0,"left",effect,0,,,,,,,,,,,
,"Apache Web Server","history.php?tp=apache",,,1])


dumpmenus()