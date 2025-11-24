var IE = /*@cc_on function(){ switch(@_jscript_version){ case 1.0:return 3; case 3.0:return 4; case 5.0:return 5; case 5.1:return 5; case 5.5:return 5.5; case 5.6:return 6; case 5.7:return 7; }}()||@*/0;
var IElt7 = IE && (IE < 7);
var lg = [],
Site = {
	imagepath: "/std/images/icons/default/",
	jspath: "M_Validation/",
	Plugins: [	"filters-panel",
				"quicksearch",
				"search-pages",
				"dashboard",
				"ajex",
				"candidate-table",
				"change-log",
				"rowmover",
				"wizard",
				"TASK_DUE_DATE_DD",
				"TASK_RCAT_TASK_STATUS"
	],
	checkers: [],
	tickers: {},
	addScript: function(url) {
		var head = document.getElementsByTagName("head")[0];
		if (head) {
			var scripts = document.getElementsByTagName("script");
			for (var i = 0, ii = scripts.length; i < ii; i++) {
				if (scripts[i].src.indexOf(this.jspath + url) > -1) {
					log("Script [" + url + "] already exists in the page", WARNING);
					return false;
				}
			}
			var s = document.createElement("script");
			s.src = this.jspath + url;
			s.type = "text/javascript";
			head.appendChild(s);
		}
		log(">>Script [" + url + "] was included to the page");
	},
	ticker: function() {
		for (tick in Site.tickers) {
			if (typeof Site.tickers[tick] == "function") {
				Site.tickers[tick]();
			} else {
				log(tick + " is not a function. It is a " + typeof Site.tickers[tick], ERROR);
			}
		}
	},
	addBlanket: function() {
		log("Adding DIV for dimming the screen", EXTRA);
		this.blanket = document.createElement("div");
		this.blanket.className = "dimm";
		this.blanket.style.height = document.documentElement.scrollHeight + "px";
		if (IE && IE < 7 && this.isWhole) {
			this.iframe = document.createElement("iframe");
			this.iframe.className = "dimmer";
			this.iframe.style.height = document.documentElement.scrollHeight + "px";
			this.body.appendChild(this.iframe);
			if (!this.isWhole) {
				this.iframe.style.display = "none";
			}
			log("iframe added");
		}
		this.body.appendChild(this.blanket);
		addEvent(this.blanket, "click", function() {
			if (!Site.poppedelement) {
				Site.undimm();
			}
		});
		log("DIV added", EXTRA);
	},
	ifWizard: function() {
		if (this.body && this.body.className == "wizard") {
			this.popelement("content", true);
			this.body.style.backgroundColor = "#fff";
			log("Wizard mode detected");
		}
	},
	hookMessages: function() {
		var messages = document.getElementsByTagName("small");
		for (var i = 0, ii = messages.length; i < ii; i++) {
			if (messages[i].className == "hidden message") {
				var link = messages[i].parentNode.tagName.toLowerCase() == "button"?messages[i].parentNode:
								messages[i].parentNode.getElementsByTagName("a").length?
									messages[i].parentNode.getElementsByTagName("a")[0]:
										null;
				if (link) {
					link.message = messages[i].innerHTML;
					log("Adding message: '" + link.message + "'", EXTRA);
				}
			}
		}
	},
	hookLinks: function() {
		var buttons = document.getElementsByTagName("button");
		for (var i = 0, ii = buttons.length; i < ii; i++) {
			if (buttons[i].className == "link action") {
				var a = buttons[i].getElementsByTagName("a");
				if (a && a.length) {
					a = a[0];
					buttons[i].href = a.href;
					addEvent(buttons[i], "click", function(e) {
						if ((this.message && confirm(this.message)) || !this.message) {
							document.location.href = this.href;
						}
					});
				} else {
					log("Link button doesn't have link inside.", ERROR);
				}
			} else if (/(^|\s)action($|\s)/.test(buttons[i].className)) {
				addEvent(buttons[i], "click", function(e) {
					if ((this.message && confirm(this.message)) || !this.message) {
						return true;
					}
					return stopEvent(e || event);
				});
			}
		}
		if (buttons.length) {
			log(ii + " buttons were hooked", EXTRA);
		}
	},
	addStripes: function() {
		this.tables = document.getElementsByTagName("table");
		for (var i = this.tables.length - 1; i >= 0; i--) {
			if (/(^|\s)results(\s|$)/.test(this.tables[i].className)) {
				log("Adding stripes to table", EXTRA);
				var tbody = this.tables[i].getElementsByTagName("tbody");
				if (tbody.length) {
					tbody = tbody[0];
					var trs = tbody.getElementsByTagName("tr");
					for (var j = 0, jj = trs.length; j < jj; j += 2) {
						trs[j].className += " alternate";
					}
					log("Stripes added", EXTRA);
				}
			}
		}
	},
	addPopups: function() {
		var links = document.getElementsByTagName("a");
		for (var i = 0, ii = links.length; i < ii; i++) {
			if (links[i].getAttribute("rel") == "popup") {
				log('"Popuping" link ' + links[i].innerHTML, EXTRA);
				addEvent(links[i], "click", function(e) {
					var newWinx = (window.screenLeft || window.screenX || 0) + 150;
					var newWinh = window.screen.availHeight - 70;
					var newWinw = window.screen.width - newWinx - 50;
					
					if (newWinw < 780) {newWinw = 780;}
					
					var w = open(this.getAttribute("href"), "popup", "height=" + newWinh + ",width=" + newWinw + ",toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=1,top=0,left=" + newWinx);
					if (w.opener == null) {
						w.opener = window;
					}
					w.focus();
					return stopEvent(e?e:window.event);
				});
			}
			if (/(^|\s)append parameters from ([A-Za-z\-\d]*)(\s|$)/.test(links[i].className)) {
				var t = links[i].className.toString().match(/(^|\s)append parameters from ([A-Za-z\-\d]*)(\s|$)/);
				if (t.length == 4) {
					var form = document.getElementById(t[2]);
					links[i].onclick = function() {
						var query = "";
						for (var i = 0, ii = form.elements.length; i < ii; i++) {
							if (form.elements[i].name) {
								query += "&" + escape(form.elements[i].name) + "=" + escape(form.elements[i].value);
							}
						}
						this.href += query;
					};
				}
			}
		}
	},
	hookSortable: function() {
		var headers = document.getElementsByTagName("th");
		for (var i = 0, ii = headers.length; i < ii; i++) {
			if (/(^|\s)sortable by [^\s]+ and [^\s]+ as [^\s]+($|\s)/.test(headers[i].className)) {
				var t = headers[i].className.toString().match(/(^|\s)sortable by ([^\s]+) and ([^\s]+) as ([^\s]+)(\s|$)/);
				if (t.length == 6) {
					log("Sortable header found",  EXTRA);
					var input1 = document.getElementById(t[2]);
					var input2 = document.getElementById(t[3]);
					headers[i].value = t[4];
					if (input1 && input2) {
						log("making column '" + headers[i].innerHTML + "' sortable", EXTRA);
						headers[i].onclick = function() {
							this.order = (this.order?(this.order == "asc"?"desc":"asc"):"asc");
							input2.value = this.order;
							input1.value = this.value;
							input1.form.todef();
							input1.form.submit();
						};
						if (input1.value == headers[i].value) {
							headers[i].getElementsByTagName("span")[0].className += (input2.value == "asc"?" up":" down");
							headers[i].order = input2.value;
						}
					}
				}
			}
		}
	},


	hookCloseButton: function() {
		var buttons = document.getElementsByTagName("button");
		for (var i = 0, ii = buttons.length; i < ii; i++) {
			if (buttons[i].className.indexOf("close window") > -1) {
				buttons[i].onclick = function() {
					window.close();
				};
				log("Close button was hooked", EXTRA);
			}
		}
	},
	hookLimitedTextareas: function() {
		var tas = document.getElementsByTagName("textarea");
		for (var i = 0, ii = tas.length; i < ii; i++) {
			var chars = tas[i].className.match(/\blimited to (\d+) char/);
			if (chars && chars.length && (chars = chars[1])) {
				var j = Math.abs(i);
				this.tickers["limited-textarea"] = function() {
					if (tas[j].value.length > chars) {
						tas[j].value = tas[j].value.substring(0, chars);
					}
				};
			}
		}
		log(ii + " limited textarea" + (ii == 1?" was":"s were") + " hooked", EXTRA);
	},
	hookMore: function() {
		var elm = document.getElementsByTagName("*");
		for (var i = 0, ii = elm.length; i < ii; i++) {
			if (/(^|\s)more(\s|$)/.test(elm[i].className)) {
				var text = elm[i].getElementsByTagName("span");
				if (text && text.length && (text = text[0].innerHTML) && text) {
					elm[i].tooltip = text;
					elm[i].onclick = function(e) {
						e = e || event;
						var panel = document.createElement("div");
						panel.className = "floating panel";
						panel.innerHTML = this.tooltip;
						document.getElementsByTagName("body")[0].appendChild(panel);
						if (document.panel) {
							document.getElementsByTagName("body")[0].removeChild(document.panel);
						}
						document.panel = panel;
						stopEvent(e);
					};
					document.onclick = function() {
						if (this.panel) {
							document.getElementsByTagName("body")[0].removeChild(this.panel);
							this.panel = null;
						}
					};
				}
			}
		}
	},
	
	
	hookTree: function() {
		var theall = document.getElementsByTagName("*");
		var trees = [], forest = [];
		for (var i = 0, ii = theall.length; i < ii; i++) {
			if ((" " + theall[i].className + " ").indexOf(" tree ") + 1) {
				trees.push(theall[i]);
			}
		}
		log(trees, EXTRA);
		for (var i = 0, ii = trees.length; i < ii; i++) {
			var Tree = trees[i];
			forest.push({
				input: Tree.getElementsByTagName("input"),
				root: Tree.getElementsByTagName("select")[0],
				tree: Tree,
				selects: Tree.getElementsByTagName("select"),
				hideall: function() {
					for (var j = 1, jj = this.selects.length; j < jj; j++) {
						this.selects[j].parentNode.className = "hidden";
					}
				},
				hidesome: function(select) {
					var res = false;
					for (var j = 0, jj = select.options.length; j < jj; j++) {
						if (document.getElementById("for" + select.options[j].value)) {
							document.getElementById("for" + select.options[j].value).className = "hidden";
							res = true;
						}
					}
					return res;
				},
				show: function(id, level) {
					
					this.input[level].value = id;
					
					if (document.getElementById("for" + id)) {
					
					
					
						if ((level>0))
						{
						
							
							
							var id1=this.input[level-1].value;
							
						
							
							if ((document.getElementById("sel" + id1)))
							{
								
							
								var elemprev=document.getElementById("sel" + id);
								var elemnext=document.getElementById("sel" + id1);
								
								
								
								var elemprevtext=elemprev[elemprev.selectedIndex].text;
								var elemnexttext=elemnext[elemnext.selectedIndex].text;
								
							
								
								if ((elemprevtext!=elemnexttext)&&(elemprev.options.length>2))
								document.getElementById("for" + id).className = "select-holder";
							}
							else
							document.getElementById("for" + id).className = "select-holder";
						}
						else
						document.getElementById("for" + id).className = "select-holder";
						
						
						
						this.show(document.getElementById("sel" + id).value, level + 1);
						
					
					}
				},
				setlevel: function(select, level) {
				
					select.level = level;
					for (var i = 0, ii = select.options.length; i < ii; i++) {
						var sel = document.getElementById("sel" + select.options[i].value);
						
						if (sel) {
							this.setlevel(sel, level + 1);
							
							
							
							
						}
								
					
						
					}
				},
				init: function() {
					log(this.selects.length, EXTRA);
					for (var j = 0, jj = this.selects.length; j < jj; j++) {
						var Forest = this;
						this.selects[j].onchange = function() {
							Forest.hideall();
							Forest.show(Forest.root.value, 0);
						};
					}
					this.setlevel(this.root, 0);
					this.hideall();
					this.show(this.root.value, 0);
				}
			});
			forest[forest.length - 1].init();
			log(forest, EXTRA);
		}
	},


	/*highlightOn: function(object,msg){
		object.className="valid";
		returnmsg = msg + '\n';
		vMessage = vMessage +returnmsg;
		if(vObj==""){vObj=eval(object);}
		return;
	},

	highlightOff: function (object){
		object.className="normal";
		return;
	},*/


	init: function() {
		try {
			var site = this;
			if (IE) {
				log("Your browser is IE " + IE + ". Shame on you!", ERROR);
			}
			this.body = document.getElementsByTagName("body")[0];

			this.isWhole = (this.body.id == "au.com.recruitasp");
			!this.isWhole && log("Legacy code recognised. Beware!", WARNING);

			// Blanket
			// this.isWhole && this.addBlanket();

			// Wizard
			// this.isWhole && this.ifWizard();

			// running through forms
			this.forms = document.getElementsByTagName("form");
			if (this.forms.length) {
				this.addScript("M_forms.js?v=3");
			}

			// message support to links and actions
			this.hookMessages();

			// linked buttons
			this.hookLinks();

			// add stripes
			// this.addStripes();

			// popup links
			this.addPopups();

			// sortable table headers
			this.hookSortable();
			
			// close window buttons
			this.hookCloseButton();
			
			// limited textareas
			this.hookLimitedTextareas();
			
			this.hookMore();
			this.hookTree();
			
			// different plug-ins
			for (var i = 0, ii = this.Plugins.length; i < ii; i++) {
				if (document.getElementById(this.Plugins[i])) {
					this.addScript(this.Plugins[i].toLowerCase() + ".js");
					log("Plug-in " + this.Plugins[i] + " installed.");
				}
			}
			
			// default focus
			var def = document.getElementById("default-focus");
			if (def && def.focus) {
				def.focus();
				log("Default focus was set");
			} else {
				log("There are no elements with default focus");
			}

			setInterval(this.ticker, 5);

			log("Initialization was successful");
		} catch(e) {
			log(e, ERROR);
			log("Initialization was unsuccessful", ERROR);
		}
	}
};




// tools
// LOV
function returnLOV(hidid, hidvalue, visid, visvalue) {
	if (opener) {
		var obj;
		if (obj = opener.document.getElementById(hidid)) {
			obj.value = hidvalue;
		}
		if (obj = opener.document.getElementById(visid)) {
			obj.value = visvalue;
		}
		window.close();
	}
	return false;
}


function printPreview(yesno) {
	var css = {
		master: document.getElementById("master-css"),
		preview: document.getElementById("preview-css")
	};
	if (css.master && css.preview) {
		css.master.rel = yesno?"alternative stylesheet":"stylesheet";
		css.master.disabled = yesno;
		css.preview.rel = yesno?"stylesheet":"alternative stylesheet";
		css.preview.disabled = !yesno;
	}
}


function runAndRepeat(func, ms) {
	if (typeof func == "string") {
		var time1 = (new Date()).getTime();
		eval(func);
		var time2 = (new Date()).getTime();
		var time = ms - (time2 - time1);
		setTimeout("runAndRepeat('" + func.addSlashes(1) + "', ms)");
	}
}


Number.prototype.zero = function() {
	if (this < 10) {
		return "0" + this;
	}
	return this.toString();
};


String.prototype.zero = function() {
	if (parseInt(this)) {
		return parseInt(this).zero();
	}
};


String.prototype.addSlashes = function(num) {
	if (num == 1) {
		this.replace(/(')/g, "\\'");
	} else
	if (num == 2) {
		this.replace(/(")/g, '\\"');
	} else
	this.replace(/(["'])/g, '\\$1');
};


String.prototype.ISO2date = function() {
	var val = this.toString();
	val = val.match(/^(\d{4})\-?(\d{2})\-?(\d{2})T(\d{2}):?(\d{2}):?(\d{2}).*$/);
	var dt = new Date();
	if (val.length == 7) {
		dt.setYear(val[1]);
		dt.setMonth(val[2] - 1);
		dt.setDate(val[3]);
		dt.setHours(val[4]);
		dt.setMinutes(val[5]);
		dt.setSeconds(val[6]);
		dt.setMilliseconds(0);
	}
	return dt;
};

Array.prototype.isin = function(value) {
	for (var i = 0; i < this.length; i++) {
		if (this[i] == value) {
			return i + 1;
		}
	}
	return false;
};


Array.prototype.remove = function(value) {
	var flag = false;
	for (var i = 0, j = this.length; i < j; i++) {
		if (this[i] == value) {
			flag = true;
		}
		if (flag && i < j - 1) {
			this[i] = this[i + 1];
		}
	}
	if (flag) {
		this.pop();
	}
};


Date.prototype.iso = function(utc) {
	var temp = new Date(this);
	var soffset = "";
	if (utc && (utc == "offset" || utc == "utc")) {
		var offset = (new Date()).getTimezoneOffset();
		if (utc == "offset") {
			soffset = ((offset > 0)?"+":"-") + (Math.abs(offset) / 60).zero() + (Math.abs(offset) % 60).zero();
		} else {
			soffset = "Z";
		}
		temp.setMinutes(temp.getMinutes() + offset);
	}
	return temp.getFullYear() + "-" +
		(temp.getMonth() + 1).zero() + "-" +
		temp.getDate().zero() +
		"T" + temp.getHours().zero() + ":" +
			  temp.getMinutes().zero() + ":" +
			  temp.getSeconds().zero() + soffset;
};


function getCurrentStyle(ele, prop)
{
	if (ele.currentStyle) {
		prop = prop.replace(/(\-\w)/gi, function(w){return w.replace("-", "").toUpperCase();});
		return ele.currentStyle[prop];
	} else {
		return document.defaultView.getComputedStyle(ele, "").getPropertyValue(prop);
	}
	return null;
}


function createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	}
	else expires = "";
	document.cookie = name + "=" + value + expires + "; path=/";
}


function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i=0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}


function addEvent(obj, type, fn)
{
	if (obj.addEventListener)
		obj.addEventListener(type, fn, false);
	else if (obj.attachEvent)
	{
		obj["e" + type + fn] = fn;
		obj[type  + fn] = function() {
			obj["e" + type + fn](window.event);
		};
		obj.attachEvent("on" + type, obj[type + fn]);
	}
}


function stopEvent(e) {
	if (e.stopPropagation)
		e.stopPropagation();
	if (e.preventDefault)
		e.preventDefault();

	e.cancelBubble = true;
	e.returnValue  = false;
	return false;
}

var NORMAL = 0,
	ERROR = 1,
	WARNING = 2,
	EXTRA = 3;
function log(message, type) {
	var colors = ["#fff", "#ff3d3d", "#de730c", "#666"];
	type = type || NORMAL;
	var dt = new Date();
	lg.push({text: dt.getMinutes() + ":" + dt.getSeconds() + ' : <span style="color:' + colors[type] + '">' + message + "</span>", type: type});
}


function showlog(detailed) {
	if (document.log) {
		document.getElementsByTagName("body")[0].removeChild(document.log);
		document.log = null;
	} else {
		var out = document.createElement("pre");
		out.style.textAlign = "left";
		out.style.width = "60%";
		out.style.height = "300px";
		out.style.position = IE?"absolute":"fixed";
		out.style.overflow = "auto";
		out.style.whiteSpace = "pre";
		out.style.top = "50%";
		out.style.left = "50%";
		out.style.zIndex = "1000";
		out.style.color = "#333";
		out.style.backgroundColor = "#000";
		out.style.font = '10px Monaco, "Lucida Console", monospace';
		out.style.opacity = "0.9";
		out.style.margin = "-150px 0 0 -30%";
		out.style.border = "solid 5px #000";
		out.style.borderRadius = "10px";
		out.style.MozBorderRadius = "10px";
		out.style.WebkitBorderRadius = "10px";
		res = "";
		for (var i = 0, ii = lg.length; i < ii; i++) {
			if (lg[i].type < 3 || detailed) {
				res += lg[i].text + "<br />";
			}
		}
		out.innerHTML = res;
		document.getElementsByTagName("body")[0].appendChild(out);
		document.log = out;
		out.onclick = function() {
			document.getElementsByTagName("body")[0].removeChild(out);
			document.log = null;
		};
	}
}


function console() {
	var div = document.createElement("div");
	var ta = document.createElement("textarea");
	var b = document.createElement("input");
	b.value = "Execute";
	b.type = "button";
	var c = document.createElement("input");
	c.value = "Cancel";
	c.type = "button";
	
	div.appendChild(ta);
	div.appendChild(b);
	div.appendChild(c);
	div.style.position = IE?"absolute":"fixed";
	div.style.width = "60%";
	div.style.height = "300px";
	div.style.textAlign = "center";
	ta.style.width = "100%";
	ta.style.height = "260px";
	ta.style.textAlign = "left";
	ta.style.display = "block";
	div.style.top = "50%";
	div.style.left = "50%";
	div.style.zIndex = "1000";
	div.style.color = "#333";
	div.style.backgroundColor = "#000";
	ta.style.color = "#ccc";
	ta.style.border = "none";
	ta.style.backgroundColor = "#000";
	ta.style.font = '10px monospace';
	div.style.opacity = "0.9";
	div.style.margin = "-150px 0 0 -30%";
	div.style.border = "solid 10px #000";
	div.style.borderRadius = "10px";
	div.style.MozBorderRadius = "10px";
	div.style.WebkitBorderRadius = "10px";
	ta.value = document.lastCommand || "";
	document.getElementsByTagName("body")[0].appendChild(div);
	ta.focus();
	b.onclick = function() {
		document.lastCommand = ta.value;
		try {
			eval(ta.value);
		} catch(e) {
			alert(e);
		}
		this.parentNode.parentNode.removeChild(this.parentNode);
	};
	c.onclick = function() {
		this.parentNode.parentNode.removeChild(this.parentNode);
	};
}

//////////////////////////////////////////////////////////////////////

addEvent(window, "load", function() {Site.init();});

