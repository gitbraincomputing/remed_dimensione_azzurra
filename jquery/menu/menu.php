<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>jQuery Collapsible Menu</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="Expanded jQuery Collapsible Menu by Rowan Lewis at PixelCarnage.net" />
	<meta name="keywords" content="jQuery, Collapsible Menu, Rowan Lewis, PixelCarnage.net, Lousy Llama Productions" />
	<meta name="copyright" content="none" />
	<meta name="robots" content="ALL" />

	<meta name="language" content="EN" />

	
	<style type="text/css">
	<!--
	#menu {
		list-style: none;
		margin: 0;
		padding: 0;
		vertical-align: top;
		width: 145px;
	}
	#menu ul {
		display: none;
		list-style: none;
		margin: 0;
		padding: 0;
	}
	#menu ul ul {
		display: inline;
	}
	#menu ul ul li a {
		padding-left: 20px;
		width: 118px;
	}
	#menu a {
		color: #000;
		cursor: pointer;
		display: block;
		font-weight: bold;
		margin-left: 0;
		padding: 2px 2px 2px 17px;
		width: 121px;
	}
	#menu a.expanded {
		background: #bbb url(images/collapse.gif) no-repeat 3px 50%;
	}
	#menu a.collapsed {
		background: #bbb url(images/expand.gif) no-repeat 3px 50%;
	}
	#menu a:hover {
		text-decoration: none;
	}
	#menu ul a {
		background: #e8e8e8;
		border-top: 2px solid #fff;
		color: #000;
		display: block;
		font-weight: normal;
		padding: 2px 2px 2px 10px;
		width: 128px;
	}
	#menu ul a:hover {
		background : #f5f5f5;
		text-decoration: underline;
	}
	#menu li.active a {
		background: #fff;
	}
	#menu li.active li a {
		background: #e8e8e8;
	}
	#menu .footer {
		background: transparent url(images/menu_footer.jpg) no-repeat 0 0;
		border-top: 2px solid #fff;
		height: 9px;
		margin: 0 0 10px 0;
		width: 142px;
	}
	#menu .footer span {
		display: none;
	}
	-->
	</style>

	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			// Expand only the active menu, which is determined by the class name
			$("#menu > li > a[@class=expanded] ").find("+ ul").slideToggle("medium");

			// Toggle the selected menu's class and expand or collapse the menu
			$("#menu > li > a").click(function() {
				$(this).toggleClass("expanded").toggleClass("collapsed").find("+ ul").slideToggle("medium");
			});
		});
	</script>

</head>

<body>
<h2>jQuery Collapsible Menu</h2>
<ul id="menu">
	<li><a class="expanded">Section A</a>
		<ul>
			<li><a href="#">Link A-A</a>
				<ul>
					<li><a href="#">Link A-A-A</a></li>

					<li><a href="#">Link A-A-B</a></li>
				</ul>
			</li>
			<li class="active"><a href="#">Link A-B</a></li>
			<li><a href="#">Link A-C</a></li>
			<li><a href="#">Link A-D</a></li>
		</ul>

	</li>
	<li class="footer"><span>&nbsp;</span></li>
	<li><a class="collapsed">Section B</a>
		<ul>
			<li><a href="#">Link B-A</a>
				<ul>
					<li><a href="#">Link B-A-A</a></li>

					<li><a href="#">Link B-A-B</a></li>
					<li><a href="#">Link B-A-C</a></li>
				</ul>
			</li>
			<li><a href="#">Link B-B</a>
				<ul>
					<li><a href="#">Link B-B-A</a></li>

					<li><a href="#">Link B-B-B</a></li>
				</ul>
			</li>
			<li><a href="#">Link B-C</a></li>
			<li><a href="#">Link B-D</a></li>
		</ul>
	</li>

	<li class="footer"><span>&nbsp;</span></li>
	<li><a class="collapsed">Section C</a>
		<ul>
			<li><a href="#">Link C-A</a></li>
			<li><a href="#">Link C-B</a></li>
			<li><a href="#">Link C-C</a></li>
			<li><a href="#">Link C-D</a></li>

		</ul>
	</li>
	<li class="footer"><span>&nbsp;</span></li>
</ul>
<a href="/">&lt;&lt; Go Back</a>
</body>
</html>