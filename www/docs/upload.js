#! /usr/bin/gsr -C

try {
/** This code runs in tandem with upload.php, to take the uploads that the PHP
 *  page received, and put it someplace where we can find it in CommonJS-land.
 */
const FS = require("fs-base");
const COMMON = require("vr09/common");
const UPG = require("vr09/upg");
const md5 = require("vr09/md5").md5;
const Registration = UPG.Registration;
const RegistrationSet = UPG.RegistrationSet;

var uploadData = JSON.parse(unescape(arguments[1]));
var resultDir;
var result = {upg:[]};
var i, j, set, reg;
var sets = [];

result.uploadId = Number(Math.random()).toString(16).slice(2) + Number(Math.random()).toString(16).slice(2);
resultDir = "/tmp/vr09_upload_" + result.uploadId;
FS.makeDirectory(resultDir);

for (i=0; i < uploadData.length; i++)
{
  if (uploadData[i].name.match(/\.upg$/i))
  {
    if (!uploadData[i].tmp_name.match(/[a-zA-Z0-9_][a-zA-Z0-9 _\-!@#$%^&*()+={}:;''"",.><\[\]]*/i))
      throw new Error("Invalid filename " + uploadData[i].name);
    set = new RegistrationSet(uploadData[i].tmp_name);
    sets.push(set);
    set.save(resultDir + "/" + uploadData[i].name);
  }
}

for (i=0; i < sets.length; i++)
{
  set = {name: sets[i].name, registrations:[]};
  result.upg.push(set);
  for (j=0; j < sets[i].length; j++)
  {
    reg = {name: sets[i][j].name};
    reg.id = md5(sets[i][j].data);
    set.registrations.push(reg);
  }
}

print("var result = JSON.parse(unescape('"+escape(JSON.stringify(result))+"'));");
} catch(e)
{
  print(e.message + " at " + e.fileName + ":" + e.lineNumber);
  print(e.stack);
}
