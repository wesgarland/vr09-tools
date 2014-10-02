#! /usr/bin/gsr

const CGI    = require("cgi").query;
const SYSTEM = require("system");	

print("Content-Type: text/html\n");
print('<html><head><link rel="stylesheet" type="text/css" href="/style.css"></head><body>');

function makeArr(alo)
{
  var i, a=[];

  for (i=0; i < alo.length; i++)
    a[i] = alo[i];

  return a;
}

function main()
{
  var uploadData   = JSON.parse(unescape(CGI.uploadData));
  var uploadResult = JSON.parse(CGI.uploadResult);
  var setNum;
  var regNum;
  var upg = uploadResult.upg;
  var sels, set, reg;

  if (!uploadResult.uploadId.match(/^[a-f0-9]+$/))
    throw new Error("Invalid uploadId: " + uploadResult.uploadId);

  //  print(uploadResult.toSource());

  print("<pre>");
  for (setNum = 0; setNum < upg.length; setNum++)
  {
    set = upg[setNum]
    print("Registration Set: " + set.name);
    sels = CGI["registrationSet_" + setNum];
    if (typeof sels === "string")
      sels = [sels];
    print(sels.length + "XXX");
    print(sels);

    for (regNum = 0; regNum < set.registrations.length; regNum++)
    {
      reg = set.registrations[regNum];
      if (sels.indexOf(reg.id) == -1)
	continue;
      print("    Registration: " + reg.name + " - #" + regNum);
    }
  }
  print("</pre>");
}

try
{
  CGI.readQuery();
  main();
}
catch(e)
{
  print(require("./callstack").renderHTML(e));
}
