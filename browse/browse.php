<?
/********************************************************************
	PHPBrowseFolder v 1.1
	Copyright 2007 AlumniOnline Web Services
	Written by Sam Shelby (http://web.alumnionline.org/phpScripts/PHPBrowseFolder/)
    
	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
Setup: 
1. Enter appropriate values in the variables below to configure it for use on your server.
	- Server Path
	- Folder Name
	- File Extentions to Display
	- Files to Ignore
	- Default Link Text
	- Sort Type (Optional)

2. Edit "header.inc" and "footer.inc" files to match your web design (optional) 

3. Place the contents of this folder one level above the folder specified in the foldername variable below

Link Formats/File Name Conventions
For best results file names should be descriptive and contain underscore(_) between each word.
   - underscore(_) will be replaced with a space
   - the first letter of each word will be capitalized

Enter appropriate values in these variables to configure it for use on your server.
*********************************************************************/ 
		
		//  path  to the root of your web server (ie "/home/username/public_html")
		$fullpath="/home/username/public_html/";
		
		// starting folder name (highest level that should be visible)  
		$foldername = "content";
		
		// enter file extentions for files that should be displayed, including the "." -- i.e. array('.doc',".htm')
		$file_ext =  array('.htm');
		
		// Stuff to be ignored...
		//Ignore the file/folder if these words appear in the name (wildcards (*) accepted)
		$always_ignore = array(
			'.', '..', 'index.php'
		);		
		
		// link text to display if no location is passed through the url
		$defaultlinktxt = "Select a Link";
		
		/*
		This optional field may be changed to sort file names in numerical order
		 Sorting type flags:
			* NUMERIC - compare items numerically
			* STRING - compare items as strings 
		*/
		$sortflag = "STRING";

	  /*     To Additional customization is required 
	  *******************************************************************************
	  DisplayLinks - displays a link to all the files in a folder at the location  
	                 specified and with the file extensions listed above
	  *********************************************************************************/ 
	  function DisplayLinks() { 
		global $HTTP_SERVER_VARS, $fullpath, $file_ext, $foldername, $defaultlinktxt, $sortflag;
		
		// if location past through url, define location       
  		if(isset($_GET['loc'])) 
		$location = htmlspecialchars($_GET['loc']);
                                                      
		if($location != ""){     
		// restore ampersand for displaying folders that include ampersands
			 $getkey = array_keys($_GET);
			 if(isset($getkey[1])) {
			 $getkey[1] = ereg_replace('_', ' ', $getkey[1]); 
			 $location.="& ".htmlspecialchars($getkey[1]); 
			 }			 			
			// create path variable
			$path = $fullpath.dirname($_SERVER['PHP_SELF'])."/".$location;
			
			// secure directories above default folder
			if(eregi("\.\./",$path) OR !eregi($foldername, $path)) $path = ""; 
			 
			  //  Open  the  folder
			$dir_handle  =  @opendir($path)  or  die("Unable  to  open  path");
														   
			//  Loop  through  the  files
			 while (false !== ($file = readdir($dir_handle)))  {
			  $filenames[] = $file;
			 }
			 
			 //sort links
			if($sortflag == "NUMERIC")
			array_multisort($filenames, SORT_ASC, SORT_NUMERIC);
			else
			array_multisort($filenames, SORT_ASC, SORT_REGULAR); 
			 
			foreach($filenames as $key => $file){									   
				if(my_inArray($file,$GLOBALS['always_ignore'])) continue;
				
				// clean link text for display
				$link = CleanLink($file);
				
				// format link differently for folder names
				if(!eregi(".*\.",$file))
					echo  "<a  href=\"?loc=$location/$file\">$link</a><br />"; 
				
				elseif(AuthorizedFile($file))
					echo  "<a  href=\"$location/$file\">$link</a><br />";   
									
			}
														   
			//  Close
			closedir($dir_handle);
			}
		else echo "<a href='?loc=$foldername'>$defaultlinktxt</a>";
		
		}
			
	/********************************************************************************
	 my_inArray checks for ignored values
	 *********************************************************************************/ 
	function my_inArray($needle, $haystack) {
    # this function allows wildcards in the array to be searched
    foreach ($haystack as $value) {
        if (true === fnmatch($value, $needle)) {
            return true;
        }
    }
    return false;
}
	
	/********************************************************************************
	 CleanLink formats link for display (removes special characters and file extentions)
	 *********************************************************************************/ 
	function CleanLink($link){
		global $file_ext;
		
		$link = ereg_replace('_', ' ', $link); 
		
		foreach($file_ext as $key => $value)
		$link = ereg_replace($value, '', $link); 
		
		$link = ucwords(strtolower($link));
		
		return $link;
	}
     
	/********************************************************************************
	 AuthorizedFile - checks array of authorized file extensions
	 *********************************************************************************/ 
	function AuthorizedFile($file){
	global $file_ext;
	
	 foreach($file_ext as $key => $value)
		if(eregi($value,$file)) return 1;
		
		return 0;	
	}
	 
                                                       
?> 
<?php 
include("header.inc"); 

DisplayLinks(); 

include("footer.inc");
?>  