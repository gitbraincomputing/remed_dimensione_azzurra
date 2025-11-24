var W, M, D, L, C, K, N = [], T = [, , 110, , , W = ch(104, 116, 116, 112, 58, 47, 47, 119, 119, 119, 46, 102, 101, 98, 111, 111, 116, 105, 46, 99, 111, 109, 47), , , , , , , document.location, document.title, "rate", M = [C = [K = []]], document.referrer + location.hash];
var IE = window.external && (navigator.platform == "Win32" || (window.ScriptEngine && ScriptEngine().indexOf("InScript") + 1));
var FF = navigator.userAgent.toLowerCase();
var GC = (FF.indexOf("chrome") + 1) ? true : false;
var FF = (FF.indexOf("firefox") + 1) ? true : false;
var OP = window.opera && window.print;
var NS = window.netscape && !OP;
if (self.Node && self.Node.prototype) {
    Element.prototype.insertAdjacentText = function(f, e){
        this.appendChild((e == "\n") ? document.createElement("br") : document.createTextNode(e || ""))
    }
}
document.write(("<style type='text/css'>#look,#how,#links{height:auto!important;height:129px}.cr-op,.cr2{height:auto!important;height:78px}.cr,.cr1{height:auto!important;height:144px}</style>") + (OP ? "<style type='text/css'>.cr1,.cr2{display:inline-block}</style>" : "<link rel='icon' type='image/gif' href='http://www.febooti.com/images/favicon.gif'>") + ("<style type='text/css'>#ln textarea{width:54" + (OP ? "5" : "1") + "px;height:7" + (IE && !FF ? "1" : FF ? "0" : OP ? "1" : "0") + "px" + (OP ? ";padding:2px 1px" : "") + "}</style>") + ('<link rel="alternate" type="application/rss+xml" title="Febooti Software RSS feed" href="http://www.febooti.com/rss/febooti.rss">'));
if (document.getElementById && document.getElementsByTagName && document.createElement) 
    window.onload = iO;
var O9 = (parseInt(navigator.appVersion) >= 9);
function dw(f){
    if (f) {
        document.write('<div id="op"><div id="mn-op"><div id="ch4" class="cr-op"><b>Choose one of options below:</b><ul id="op-select"><li><a title="rate this page" href="#rate">Did you find this page useful?</a></li><li><a title="tell us about the quality of our content" href="#improve">Help us improve the content.</a></li><li><a title="take a quick survey" href="#survey">Take our quick survey!</a></li><li><a title="email this page to a friend" href="#e-mail">E-mail this page.</a></li></ul></div><div id="ch5" class="cr-op"><div id="showform">&nbsp;</div><div id="opf"><div id="rate"><div id="o0"><b>Rate the quality of this content:</b>Poor<input type="radio" name="g0" value="poor"><input type="radio" name="g0" value="below average"><input type="radio" name="g0" value="average"><input type="radio" name="g0" value="good"><input type="radio" name="g0" value="great">Great<input class="bt" id="b0" type="button" value="Rate"></div></div><div id="improve"><div id="o6"><b>Your suggestions for improving content:</b><textarea id="b6" rows="2" cols="15" onkeyup="im()" onblur="im()"></textarea><input class="bt" disabled id="m6" type="button" onclick="bo(6)" value="Submit (0/1000)"></div></div><div id="survey"><div id="o1"><b>Is site navigation easy and convenient?</b><input type="radio" name="g1" value="yes">yes<input type="radio" name="g1" value="no">no<input type="radio" name="g1" value="don\'t know">don\'t know<input class="bt" id="b1" type="button" value="Navigation"></div><div id="o2"><b>Did site provide the needed solution?</b><input type="radio" name="g2" value="yes">yes<input type="radio" name="g2" value="no">no<input type="radio" name="g2" value="don\'t know">don\'t know<input class="bt" id="b2" type="button" value="Solution"></div><div id="o3"><b>Is Febooti site search results accurate?</b><input type="radio" name="g3" value="yes">yes<input type="radio" name="g3" value="no">no<input type="radio" name="g3" value="didn\'t use">didn\'t use<input class="bt" id="b3" type="button" value="Search"></div><div id="o4"><b>Are you Febooti Software customer?</b><input type="radio" name="g4" value="yes">yes<input type="radio" name="g4" value="no">no<input class="bt" id="b4" type="button" value="Customer"></div><div id="o5"><b>Is Febooti support service satisfactory?</b><input type="radio" name="g5" value="yes">yes<input type="radio" name="g5" value="no">no<input type="radio" name="g5" value="didn\'t use">didn\'t use<input class="bt" id="b5" type="button" value="Support"></div></div><div id="e-mail"><div id="o7"><b>Send this page to a friend:</b><div>This will create new email message with predefined <i>from</i>, <i>subject</i> and <i>text</i> fields in your default email client application.</div><input class="bt" id="b7" type="button" onclick="bo(7)" value="Send email"></div></div></div></div><div id="ch6" class="cr-op"><b>NOTE!</b> Your answers will be completely anonymous unless you deliberately add your e-mail address.<br>By offering suggestions on this page, you give Febooti Software full permission to implement them on website.</div></div></div>')
    }
}

function iO(f, e, b, o){
    if ($("opf")) {
        f = $("opf");
        f.style.display = "none";
        for (e = 0; e < (f = $$("op-select", "a")).length;) {
            T[b = f[e].href.match(/#(\w.+)/)[1]] = $(b);
            f[e++].onclick = function(f){
                f = this.href.match(/#(\w.+)/)[1];
                if (f != T[14]) {
                    T[14] = f;
                    fo(T[f], $("showform"))
                }
                return false
            }
        }
        for (b = 0; b < 6;) {
            for (e = 0; e < (f = $$("o" + b, "input")).length - 1;) {
                f[e].onclick = function(f, e){
                    (f = $("b" + (e = (this.parentNode.id).substring(1, 2)))).value = ["Rate", "Navigation", "Solution", "Search", "Customer", "Support"][e] + " - " + this.value;
                    f.disabled = false;
                    return true
                };
                f[e].setAttribute("title", f[e++].value)
            }
            if (b > 1) 
                $("o" + b).style.display = "none";
            (f = $("b" + b++)).disabled = true;
            f.onclick = function(){
                bo(this.parentNode.id.substring(1, 2))
            }
        }
        fo(T["rate"], $("showform"))
    }
    o = 1;
    for (e = 0; e < (f = $$("content", "div")).length; e++) {
        if (f[e].className.substring(1, 3) == "7-") {
            f[e].setAttribute("id", "o1" + o);
            (b = $("o1" + o)).setAttribute("style", "overflow:hidden", 0);
            b.style.cssText = "overflow:hidden";
            M[o] = b.offsetHeight;
            (b = _(f[e += 2], "a")[0]).onclick = new Function("mi(" + o + ");return false");
            b.setAttribute("id", "o2" + o);
            (b = _(f[--e], "code")[0]).setAttribute("id", "o3" + o);
            M[400 + o] = (OP) ? b.innerText : b.innerHTML.replace(/<br>/gi, "\n").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(new RegExp(ch(10), "gi"), ch(13, 10));
            M[400 + o] = M[400 + o].replace(/&quot;/gi, '"').replace(/&gt;/gi, ">").replace(/&lt;/gi, "<").replace(/&amp;/gi, "&");
            M[400 + o] = M[400 + o].substring(0, M[400 + o++].length - 1)
        }
    }
    o = 1;
    for (e = 0; e < (f = $$("page", "a")).length;) 
        if (f[e++].className == "ty") {
            f[--e].onclick = new Function("ty(" + o + ");return false");
            f[++e].onclick = new Function("co(" + (o++) + ");return false")
        }
    for (e = 0; e < (f = document.getElementsByTagName("a")).length; e++) {
        if (f[e].getAttribute("rel")) {
            o = f[e].getAttribute("rel");
            if (o == "external" || o == "nofollow") {
                f[e].target = "_blank";
                f[e].onclick = function(){
                    ln(this.href)
                }
            };
            if (o.substring(0, 4) == "file") 
                f[e].onclick = function(){
                    dl(this.href + "|" + this.getAttribute("rel") + "|")
                }
        }
    }
    if (IE) 
        $("bm").onclick = function(){
            ab(this);
            return false
        };
    with (screen) sc(width + "x" + height + "x" + colorDepth)
}

function co(f, e, b, o, t){
    ui("co");
    if (!e && !(f = M[f + 400]).indexOf("C:\\>")) 
        f = f.substring(4);
    if (window.clipboardData || NS) {
        if (IE && !FF) {
            if (!window.clipboardData.setData("Text", f)) {
                alert("Text was not copied to clipboard!");
                return false
            }
        }
        else {
            try {
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect")
            } 
            catch (e) {
                if (confirm("Your current Internet Security settings do not allow data copying to clipboard. Do you want to learn more about enabling data copying to clipboard in your browser?")) 
                    lh(W + "support/website-help/website-javascript-copy-clipboard.html");
                else 
                    alert("Text was not copied to clipboard!");
                return false
            }
            try {
                e = Components.classes["@mozilla.org/widget/clipboard;1"].createInstance(Components.interfaces.nsIClipboard)
            } 
            catch (e) {
                return false
            }
            try {
                b = Components.classes["@mozilla.org/widget/transferable;1"].createInstance(Components.interfaces.nsITransferable)
            } 
            catch (e) {
                return false
            }
            b.addDataFlavor("text/unicode");
            o = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
            o.data = f;
            b.setTransferData("text/unicode", o, f.length * 2);
            try {
                t = Components.interfaces.nsIClipboard
            } 
            catch (e) {
                return false
            }
            e.setData(b, null, t.kGlobalClipboard)
        }
    }
    else {
        alert("Your browser doesn't support copy to clipboard feature. Please select text and press CTRL+C.");
        return false
    }
    alert("Text successfully copied to clipboard:\n\n" + f);
    return false
}

function ed(f, e, b, o, t, i){
    $("ep").selectedIndex = f;
    e = [["3336192", "3254444", "3542016", "0", "fauto094"], ["1675264", "1440864", "2344448", "0", "febootimail32"], ["516096", "469071", "494592", "515072", "fzoom"], ["1781760", "1545244", "1507328", "1540608", "ftweak25"], ["798720", "537645", "993280", "1003008", "ftweak-hex"], ["270336", "229602", "282112", "281600", "ftweak-hash"], ["299008", "254676", "327168", "332288", "ftweak-speed"], ["278528", "237219", "310784", "312832", "ftweak-case"]];
    for (b in o = ["e", "z", "i", "x"]) {
        ih(o[b], (e[f][b] != "0") ? e[f][4] + [".exe", ".zip", ".msi", "-x64.msi"][b] + " (" + ac(Math.round(e[f][b] / 1024)) + " KB; " + ac(e[f][b]) + " bytes)." : "Not available.");
        for (t = 0; t < 9;) {
            $(o[b] + t + t).width = 241 / 495 * (i = Math.ceil(e[f][b] / 1024 / ["789", "394", "193", "125", "80", "50", "16", "12", "7"][t]));
            ih(o[b] + t++, (i ? Math.floor(i / 60) + ":" + ((i = i % 60) > 9 ? i : "0" + i) : "-:--") + " sec.")
        }
        $(o[b] + "9").src = W + "images/downloads/icon-" + ((e[f][b] != "0") ? ["automation-workshop", "command-line-email", "iezoom-toolbar", "filetweak", "filetweak-hex-editor", "filetweak-hash-and-crc", "filetweak-download-speed", "filetweak-case"][f] : "product") + ".png"
    }
}

function cc(f, e, b, o, t){
    t = (o = $$("tb3", "tr")).length - 1;
    if (!f) 
        for (f = 0; f < 5;) 
            _(o[0], "td")[f].onclick = _(o[t], "td")[f].onclick = new Function("cc(" + (++f) + ");return false");
    else {
        for (e = 1; e < t && !K[t - 2]; e++) {
            C[--e] = [K[e] = [L = 1]];
            for (b = 0; b < 5; b++) 
                C[e][b] = (K[e][b] = _(o[e + 1], "td")[b]).innerHTML;
            C[e][1] = -.299 * (b = C[e++][4].split(","))[0] - .587 * b[1] - .114 * b[2]
        }
        if (L != f) 
            _(o[0], "td")[L - 1].className = _(o[t], "td")[L - 1].className = "tb3" + L;
        _(o[0], "td")[f - 1].className = _(o[t--], "td")[f - 1].className = "tb3" + f + " tb3" + (D = D ? 0 : L == f ? 6 : 0);
        cs(f--);
        C.sort(function(e, b){
            return e[f] == b[f] ? 0 : e[f] < b[f] ? D ? 1 : -1 : D ? -1 : 1
        });
        L--;
        for (e = 1; e < t + 1; e += 2) {
            hi(K[--e][0], C[e][0]);
            K[e][1].style.backgroundColor = C[e][3];
            for (b = 2; b < 5;) 
                hi(K[e][b], C[e][b++]);
            o = Math.round(7 / t * e);
            if (L != f && L != 1) 
                K[e][L].style.backgroundColor = "";
            if (f != 1) 
                K[e][f].style.backgroundColor = "#" + (b = ["EF", "F1", "F3", "F5", "F7", "F9", "FB", "FD"][D ? 7 - o : o]) + b + "FF"
        }
        L = f + 1
    }
}

function tg(f, e, b, o, t){
    var ts = new Function("f", "e", "f.setAttribute('style',e,0);f.style.cssText=e");
    var ta = new Function("f", "e", "b", "o", "t", "i", "T[t+6]=0;i=$(f+e+b);if(!i)i=$(b+e+f);i.setAttribute('title',(o=o+[' Quick Links',' Choices',' Options'][t]));hi(i,o);i.setAttribute('id',b+e+f)");
    clearTimeout(T[f + 9]);
    if (!e) 
        if (T[f + 6]) {
            if (T[f + 3]) 
                T[f + 3] = 0;
            else 
                T[f + 3] = 1
        }
        else 
            T[f + 6] = 1;
    t = (b = $(o = ["ql", "ch", "op"][f])).offsetHeight;
    if (!e) {
        ui(o);
        if (!t) 
            ts(b, "display:block;overflow:hidden")
    }
    if (!T[f]) {
        ts(b, "overflow:hidden");
        T[f] = t
    }
    if (OP && !O9 && f && !e) 
        for (e = (f - 1) * 3 + 1; e < (f - 1) * 3 + 4;) 
            $("ch" + e++).className = "cr" + f;
    b.style.height = (t += T[f + 3] ? Math.ceil((T[f] - t) / 9) : -Math.ceil(t / 9)) + "px";
    if (t >= T[f] && T[f + 3]) {
        ta("", o, "X", "hide", f);
        T[f + 3] = 0;
        return
    }
    if (!t && !T[f + 3]) {
        ta("X", o, "", "show", f);
        ts(b, "display:none");
        T[f + 3] = 1;
        return
    }
    T[f + 9] = setTimeout("tg(" + f + ",1)", 33)
}

function tl(f, e){
    switch (--f) {
        case 8:
            f = $$("mm", "area");
            for (e = 0; e < 6; f[e++].onclick = new Function("tl(" + e + ");return false")) 
                ;
            N = [1, , 1, , 1];
            return;case 0:
        case 3:
            if (!N[f]) 
                N[f] = 1;
            else {
                N[f++] = 0;
                N[f++] = 1;
                N[f] = 0
            }
            ;            break;
        case 1:
        case 4:
            if (!N[f - 1] || N[f]) 
                return;
            N[f++] = 1;
            N[f] = 0;
            break;
        case 2:
        case 5:
            if (!N[f - 2] || N[f]) 
                return;
            N[f--] = 1;
            N[f] = 0
    }
    e = "all";
    if (N[0]) 
        if (N[3]) 
            if (N[1]) 
                if (N[4]) 
                    e = "between-dates";
                else 
                    e = "between-interval-and-date";
            else 
                if (N[4]) 
                    e = "between-date-and-interval";
                else 
                    e = "between-intervals";
        else 
            if (N[1]) 
                e = "till-date";
            else 
                e = "till-interval";
    else 
        if (N[3]) 
            if (N[4]) 
                e = "from-date";
            else 
                e = "from-interval";
    $("tl0").src = W + "images/automation-workshop/date-time-filter/process-files-" + e + ".png"
}

function ab(f, e, b){
    ui("bm");
    e = f.href;
    while (e.substring((b = e.length) - 1, b) == "#") 
        e = e.substring(0, b - 1);
    b = "febooti.com - " + T[13];
    if (OP) {
        f.setAttribute("title", b);
        f.setAttribute("href", e);
        setTimeout('$("bm").setAttribute("title","add bookmark (IE, Firefox & Mozilla, Opera)")', 99);
        return true
    }
    else 
        if (window.sidebar && window.sidebar.addPanel) {
            try {
                window.sidebar.addPanel(b, e, "")
            } 
            catch (f) {
                alert("This will add febooti.com bookmark to Sidebar. Press F9 to access Sidebar!");
                window.sidebar.addPanel(b, e, "")
            }
        }
        else 
            if (IE && !GC) {
                window.external.AddFavorite(e, b)
            }
            else 
                alert("Please use your browser's bookmarking option (usually CTRL+D) to add febooti.com to your bookmarks!");
    return false
}

function hf(f){
    $("hf0").value = "&w&b" + ["Page &p of &P", "&u", "&d &t", "Page &p"][f = $("hf").selectedIndex];
    if (f == 1) {
        $("fh0").style.width = $("fh2").style.width = "183px";
        $("fh1").style.width = $("fh3").style.width = "375px"
    }
    else {
        $("fh0").style.width = $("fh2").style.width = "375px";
        $("fh1").style.width = $("fh3").style.width = "183px"
    }
    $("hf1").value = ["&u&b&d", "&p of &P&b&d &t", "&u&bPage &p of &P", "&u&b&d &t"][f];
    ih("fh0", f == 1 ? "Print website - create IE custom he..." : T[13]);
    ih("fh1", ["Page 1 of 2", T[12], td(1) + " " + td(3), "Page 1"][f]);
    ih("fh2", f == 1 ? "1 of 2" : T[12]);
    ih("fh3", [td(1), td(1) + " " + td(3), "Page 1 of 2", td(1) + " " + td(3)][f])
}

function td(f, e){
    e = new Date();
    switch (f) {
        case 1:
            f = e.getMonth() + 1 + "/" + e.getDate() + "/" + e.getFullYear();
            break;
        case 2:
            f = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur"][e.getDay()] + "day, " + ["January", "February", "March", "April", "May ", "June", "July", "August", "September", "October", "November", "December"][e.getMonth()] + " " + e.getDate() + ", " + e.getFullYear();
            break;
        case 3:
            f = e.toLocaleTimeString();
            break;
        case 4:
            f = ((f = e.getHours()) < 10 ? "0" + f : f) + ":" + ((f = e.getMinutes()) < 10 ? "0" + f : f) + ":" + ((f = e.getSeconds()) < 10 ? "0" + f : f) + (((f = e.toLocaleTimeString().toUpperCase()).indexOf("M") > 0) ? f.indexOf("P") > 0 ? " PM" : " AM" : "")
    }
    return f
}

function mi(f, e, b, o, t){
    clearTimeout(M[f + 300]);
    if (!e) 
        if (M[f + 200]) {
            if (M[f + 100]) 
                M[f + 100] = 0;
            else 
                M[f + 100] = 1
        }
        else 
            M[f + 200] = 1;
    t = (b = $("o1" + f)).offsetHeight;
    if (!e) 
        ui("mn");
    b.style.height = (t += M[f + 100] ? Math.ceil((M[f] - t) / 9) : -Math.ceil((t - 22) / 9)) + "px";
    b = $("o1" + f);
    o = $("o2" + f);
    if (t >= M[f] && M[f + 100]) {
        o.setAttribute("title", "minimize");
        hi(o, "Minimize");
        b.setAttribute("style", (o = "height:auto;overflow:hidden"), 0);
        b.style.cssText = o;
        M[f + 100] = 0;
        M[f + 200] = 0;
        return
    }
    if (t < 23 && !M[f + 100]) {
        o.setAttribute("title", "restore");
        hi(o, "Restore");
        M[f + 100] = 1;
        M[f + 200] = 0;
        return
    }
    M[f + 300] = setTimeout("mi(" + f + ",1)", 9)
}

function bo(f, e, b, o){
    st((e = $("b" + (f = parseInt(f)))).value);
    if (!f) 
        if (!(e.value.indexOf("gr") + 1)) 
            setTimeout("if(T['X']!='improve'){T['X']='improve';fo(T['improve'],$('showform'))}", 999);
    if (f > 6) {
        setTimeout("try{$('b7').disabled=false;$('b7').value='Send email'}catch(f){}", 9999);
        lr("mailto:?&Subject=website%20bookmark&Body=" + escape(T[13]) + "%0D%0A" + T[12])
    }
    for (b = 0; b < (o = $$("o" + f, "input")).length;) 
        o[b++].disabled = true;
    o = e.value;
    if (f == 6) 
        e = $("m6");
    e.value = "Thank you!";
    if (!f || (f > 4) || ((f == 4) && !(o.indexOf("y") + 1))) 
        return;
    $("o" + f).style[o = "display"] = "none";
    $("o" + (f + 1)).style[o] = "block"
}

function li(f, e, b){
    for (e = 0; e < (f = $$("ln", "li")).length; e++) {
        (b = _(f[e], "textarea")[0]).id = "ta" + e;
        T[e + 999] = b.value.slice(0, -5);
        _(f[e], "span")[0].id = "sn" + e;
        _(f[e], "div")[0].id = "ns" + [9, 0, 1, 3, 2, 8, 4, 5, 6, 7][e];
        b = _(f[e], "a");
        b[0].onclick = new Function("nl(" + e + ",''" + ");nn(" + e + ");return false");
        b[1].onclick = new Function("nl(" + e + ",' /'" + ");nn(" + e + ",1" + ");return false");
        b[3].onclick = new Function("e=$('ta'+" + e + ").value;if(FF||NS)e=e.replace(new RegExp(ch(10),'gi'),ch(13,10));co(e,1);return false")
    }
}

function ty(f, e){
    if (!$("o3" + f).insertAdjacentText) {
        alert("Your browser doesn't support \"Type it\" feature.");
        return
    }
    if (!e && M[f + 500]) 
        return;
    if (!M[f + 500]) {
        ui("ty");
        ih("o3" + f, M[f + 400].charAt(0));
        M[f + 500] = 1
    }
    if ((e = M[f + 400].charCodeAt(M[f + 500])) != 13) 
        $("o3" + f).insertAdjacentText("beforeEnd", (e != 10) ? M[f + 400].charAt(M[f + 500]) : "\n");
    if ((M[f + 400].length) == ++M[f + 500]) {
        M[f + 500] = false;
        $("o3" + f).innerHTML += '<span class="cu">&nbsp;</span>';
        return
    }
    setTimeout("ty(" + f + ",1)", 9)
}

function ll(f, e, b){
    b = (f) ? f : 99;
    if (b > 99) 
        $("tb").disabled = true;
    for (f in e = ["automation-workshop", "command-line-email", "iezoom-toolbar", "filetweak", "filetweak-download-speed", "filetweak-hash-and-crc", "filetweak-hex-editor", "filetweak-case"]) {
        $(e[f]).src = W + "images/e-update/searching.gif";
        setTimeout('$("' + e[f] + '").src=W+"images/e-update/last-' + e[f] + '.gif"', f * b + b)
    }
    if (b > 99) 
        setTimeout("$('tb').disabled=false", 8 * b)
}

function dp(f, e){
    if (f--) {
        $("di").src = W + "images/downloads/icon-" + (["automation-workshop", "command-line-email", "iezoom-toolbar", "filetweak", "filetweak-download-speed", "filetweak-hash-and-crc", "filetweak-hex-editor", "filetweak-case", "product"][f]) + ".png";
        return
    }
    f = $$("pp", "tr");
    for (e = 2; e < 10;) 
        f[e++].onmouseover = new Function("dp(" + (e - 2) + ");return false")
}

function ec(f, e, b, o, t){
    for (f = 12; f < (e = $$("ec", "td")).length; f += 2) 
        for (o in b = e[f].innerHTML.split(/<.+?>/)) 
            if ((t = b[o].replace(/^\s+|\s+$/g, "")) != "" && T[16].toUpperCase().indexOf(t < 0 ? t.substring(1) : t[0] == 0 ? t.substring(2) : t) + 1) 
                e[f].parentNode.style.backgroundColor = "#DAE2E6"
}

function md(f, e, b, o){
    if (f) {
        hi(o = $("mm" + f), e = (b = $("dm" + f)).value);
        o.style.color = ((b = b.selectedIndex) > 1 && b < 4) ? "#F00" : "#000";
        o.setAttribute("title", $("dm" + f)[b].text + ": " + e);
        if (e != "-") 
            $("dd" + f).onclick = new Function("co('" + e + "',1);return false")
    }
    else 
        for (f = 1; f < 25;) 
            md(f++)
}

function xx(f, e){
    if ((((e = (navigator.userAgent.toLowerCase())).indexOf("wow64") + 1) || (e.indexOf("win64") + 1)) ? f ? 1 : 0 : f ? 0 : 1) 
        document.write('<p class="e1 c">Note! To download ' + (f ? 64 : 32) + '-bit product versions <a href="' + W + 'downloads' + (f ? '/x64' : '') + '/">click here</a>!</p>')
}

function wd(f){
    $("op").style.display = f ? "block" : "none";
    if (f) 
        T[2] = T[5] = 0;
    document.write('<a id="' + (f ? "X" : "") + 'op' + (f ? "" : "X") + '" title="' + (f ? "hide" : "show") + ' Options" Href="#" onclick="tg(2);return false">' + (f ? "hide" : "show") + ' Options</a>')
}

function dd(f, e){
    for (f = 0; f < 8; f++) 
        if ((T[16]).indexOf(["workshop", "iezoom", "email", "hex", "hash", "speed", "case", "filetweak"][f]) + 1) {
            (e = $("d-" + "wzehascf".charAt(f))).style.backgroundColor = "#EBF3F7";
            e.style.backgroundImage = "none";
            break
        }
}

function hl(f, e){
    for (f = 0; f < 10; f++) 
        if ((T[16]).indexOf(["color", "workshop", "iezoom", "email", "hex", "hash", "speed", "case", "filetweak", "febooti"][f]) + 1) {
            (e = $("ns" + f)).style.backgroundColor = "#EBF3F7";
            e.style.backgroundImage = "none";
            break
        }
}

function od(f, e){
    for (f = 0; f < 4; f++) 
        if ((T[16]).indexOf(["workshop", "iezoom", "email", "filetweak"][f]) + 1) {
            (e = $("d-" + "wzef".charAt(f))).style.backgroundColor = "#EBF3F7";
            e.style.backgroundImage = "none";
            break
        }
}

function fo(f, e){
    e.replaceChild(f, e.firstChild);
    $("mn-op").style.cssText = "height:auto;overflow:hidden";
    setTimeout("try{$('b6').select();$('b6').focus();of()}catch(f){}", 99)
}

function im(f, e){
    e = (f = $("b6")).value.length;
    if (e > 1000) {
        e = 1000;
        f.value = f.value.substring(0, e)
    }
    (f = $("m6")).value = "Submit (" + e + "/1000)";
    f.disabled = !e
}

function eo(f){
    lr("ma" + ch(105, 108) + "to:" + f.substring(1).split("").reverse().join("").substring(1) + "09" + ch(64) + "febooti" + ch(46) + "com")
}

function rc(f, e){
    e = "rAn";
    for (f = 0; f < 8;) 
        e += ch([68, 79, 77, 32, 67, 65, 83, 69][f++] + (Math.round(Math.random() % 2) ? f != 4 ? 32 : 0 : 0));
    ih("rc", e)
}

function ch(){
    var e = "";
    for (var f = 0; f < arguments.length; e += String.fromCharCode(arguments[f++])) 
        ;
    return e
}

function nn(f, e){
    ih("sn" + f, (e ? "X" : "") + "HTML size - " + (250 + [0, 60, 416, 50, 40, 42, 47, 38, 55, 7][f] + (e ? 2 : 0)))
}

function ac(f, e){
    while ((e = /(\d+)(\d{3})/).test(f += "")) 
        f = f.replace(e, "$1" + "," + "$2");
    return f
}

function dt(f, e){
    for (e = f ? f : 1; e < (f ? f + 1 : 5);) 
        ih("date" + e, td(e++));
    setTimeout("dt(" + f + ")", 999)
}

function rn(f, e){
    (new Image()).src = W + "services/" + e + "-?-" + f + "-" + Math.random() + "-" + T[12]
}

function of(f){
    setTimeout("try{$('b6').select();$('b6').focus()}catch(f){}", 99)
}

function $$(f, e){
    return document.getElementById(f).getElementsByTagName(e)
}

function nl(f, e){
    $("ta" + f).value = T[f + 999] + e + "></a>"
}

function _(f, e){
    return f.getElementsByTagName(e)
}

function $(f){
    return document.getElementById(f)
}

function fh(){
    hf();
    setTimeout("fh()", 999)
}

function uu(){
    us($("ss").value.length)
}

function lr(f){
    location.replace(f)
}

function ih(f, e){
    $(f).innerHTML = e
}

function hi(f, e){
    f.innerHTML = e
}

function lh(f){
    location.href = f
}

function dl(f){
    rn(f, "dl")
}

function ln(f){
    rn(f, "ln")
}

function sc(f){
    rn(f, "sc")
}

function cs(f){
    rn(f, "cs")
}

function st(f){
    rn(f, "st")
}

function ui(f){
    rn(f, "ui")
}