exports.renderHTML = function(e)
{
  var html = "<table class='errorMessage'>";
  var type = e.type || "Error";

  html += "<tr class='errorMessage_details'  ><td>" + type + ": </td><td>" + e.message + "</td></tr>";
  html += "<tr class='errorMessage_location' ><td>Location:     </td><td>" + e.fileName + ":" + e.lineNumber + "</td></tr>";
  html += "<tr class='errorMessage_callStack'><td>Stack:        </td><td><div class='scrollContainer'>" + e.stack
      .replace(/(^|\n)([^(]*)/g,"\n<span class='line'><span class='functionName'>$2</span>")
      .replace(/(\n)([^\n][^\n]*)/g,"$2</span>")
      + "</div></td></tr>";
	       
  html += "</table></body></html>";
  return html;
}
