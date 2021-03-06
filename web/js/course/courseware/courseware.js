var hostname = 'http://'+location.host;
window.onload = function (){
	fileID = getQueryContent()
	filename = "/courseware/"+fileID+".pdf"
	var pdf = new PDFObject({ url: filename }).embed("pdf")
}
/**
 * 得到url中指定参数的值
 * @param  {String} name [指定的参数]
 * @return {[String or null]}      [参数的值]
 */
function getQueryContent(name = "fileID")
{
	var url = location.href;
	var paras = url.substr(url.indexOf("?") + 1).split("&")
	for(var i=0;i < paras.length;i++){ 
		num=paras[i].indexOf("="); 
		if(num > 0 && paras[i].substring(0,num) == name)
			return paras[i].substr(num+1)
	} 
	return null
}
window.onbeforeunload = function(){
	$.ajax({
	    type: "POST",
	    url:  hostname+'/course/courseware/exit-courseware',
	    success: function (data) {
	        //alert("goodbye")
	    },
	    error: function(XMLHttpRequest, textStatus, errorThrown) {
	        alert(XMLHttpRequest.statusText);
	    }
	});
};
