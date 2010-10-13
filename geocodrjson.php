<xn:head>
<script src="../JSON/jsonStringify.js"></script>
<script language="javascript">

function showxml(){
alert(xhr.responseText);
}


function thing() {
if (document.getElementById("compact").value != "false") {
sss = true;
}else{
sss =false;
}


dojo.byId("status").innerHTML = "Connecting...";
var the_mimetype;
if (document.getElementById("format").value == "json"){
the_mimetype = "text/json";
} else {
the_mimetype = "text/xml";
}

							dojo.io.bind( {
								url: "http://geothings.ning.com/Flickr/flickrgeocodr_cluster.php",
								
								mimetype: the_mimetype,
								method: "get",
								content: {"place": document.getElementById("place").value,
								 "showclusters": document.getElementById("showclusters").value,
								  "showpoints":document.getElementById("showpoints").value,
								  "format":document.getElementById("format").value,
								 "numclusters":document.getElementById("numclust").value,
								 "xn_auth":"no"},
								useCache: false,
								load: function(type, data, obj){
									//dojo.byId("results").innerHTML = data;
									JSONstring.compactOutput= sss;     
									JSONstring.includeProtos=true;     
									JSONstring.includeFunctions=true;     
									JSONstring.detectCirculars=true;          
									JSONstring.restoreCirculars=true;
									 
									// Now let us stringify this
									if (document.getElementById("format").value == "json") {
									var s=JSONstring.make(data);
									dojo.byId("results").innerHTML = s;
									} else {
									nodes = data.documentElement.childNodes;
									dojo.byId("results").innerHTML= nodes.item(0).text ;


									
								
									
									}
									
									if (window.console) console.log(data);
									
									dojo.byId("status").innerHTML = "Displaying!";
								}
							} );
						//	document.getElementById("sign").value
}
							</script>
							</xn:head>
							<p>http://geothings.ning.com/Flickr/flickrgeocodr_cluster.php<br />
							geocode place:<input type="text" id="place" value="london"/><br />
							num clusters:<input type="text" id="numclust" value="3"/><br />
							show clusters:<input type="text" id="showclusters" value="true"/><br />
							show points:<input type="text" id="showpoints" value="false"/><br />
							<input type="hidden" id="format" value="json"/><br />
							compactJSON:<input type="text" id="compact" value="false"/><br />
							<a href="javascript:thing();">Give me the JSON!</a></p>
							
							<div id="status"></div><br /><div id="results"></div>
							<p><br /> <br />JSON also outputed to console.log, fellow firebug users!. Uses Stringify.js from http://www.thomasfrank.se/json_stringify_revisited.html</p>