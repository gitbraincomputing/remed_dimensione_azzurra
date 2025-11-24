/*
   Milonic DHTML Menu Frames Based Navigation Module  mm_navframe.js version 1.4 - November 25 2005
   Written by Kevin Clements
   This module is only compatible with the Milonic DHTML Menu version 5.62 or higher

   Copyright 2005 (c) Milonic Solutions Limited. All Rights Reserved.
   This is a commercial software product, please visit http://www.milonic.com/ for more information.
   
   This code should be loaded into the "nav" or "navigation" frame, where the Main Menu will be displayed.
*/

var mm_si;
var mm_tf;

function openSubmenu()
{
  mm_si = _itemRef;
  mm_tf = parent.frames[_mi[mm_si][35]];

  var subTop, subLeft;
  var selectedPos = gpos(gmobj("el" + mm_si));
  var menuObj = mm_tf.gmobj("menu" + mm_tf.getMenuByName(_mi[mm_si][3]));

  if(menuObj)
  {
    subTop = selectedPos[0] + _subOffsetTop + mm_tf._sT;
    subLeft = selectedPos[1] + _subOffsetLeft + mm_tf._sL; 
    mm_tf.popup(_mi[mm_si][3]);
    _ofMT=0
    mm_tf.spos(menuObj, subTop, subLeft);
  }
}

function closeSubmenu(){
	//if(mm_tf&&mm_tf._itemRef==-1) mm_tf.popdown();
	}

