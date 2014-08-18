<?php
session_start();

/**
* Fixes the odd indexing of multiple file uploads from the format:
*
* $_FILES['field']['key']['index']
*
* To the more standard and appropriate:
*
* $_FILES['field']['index']['key']
*
* @param array $files
* @author Corey Ballou
* @link http://www.jqueryin.com
*/
function fixFilesArray(&$files)
{
    $names = array( 'name' => 1, 'type' => 1, 'tmp_name' => 1, 'error' => 1, 'size' => 1);

    foreach ($files as $key => $part) {
        // only deal with valid keys and multiple files
        $key = (string) $key;
        if (isset($names[$key]) && is_array($part)) {
            foreach ($part as $position => $value) {
                $files[$position][$key] = $value;
            }
            // remove old key reference
            unset($files[$key]);
        }
    }
}
fixFilesArray($_FILES['upg']);
fixFilesArray($_FILES['dat']);
?><!doctype html>
<html>
<head>
  <title>VR-09 | Select Registrations</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>  
  <link rel="stylesheet" type="text/css" href="style.css">
  <style type="text/css">
SELECT.regPicker
{
  font-family: courier, fixed;
}
  </style>
  <script>
var upgFiles = JSON.parse('<?php print(json_encode($_FILES['upg'])) ?>');
var datFiles = JSON.parse('<?php print(json_encode($_FILES['dat'])) ?>');
var allFiles = JSON.parse('<?php print(json_encode(array_merge($_FILES['dat'], $_FILES['upg']))) ?>');
<?php print(shell_exec("./upload.js '" . rawurlencode(json_encode(array_merge($_FILES['dat'], $_FILES['upg']))) . "'")) ?>

function drawSet(setNumber)
{
  var i;
  var template = document.getElementById("regPicker_template");
  var regs = template.cloneNode();
  var upg = result.upg[setNumber].registrations;
  var opt;

  regs.id = regs.name = "registrationSet_" + setNumber;
  regs.options[0] = new Option(template.options[0].text, template.options[0].value);

  for (i=0; i < upg.length; i++)
  {
    opt = new Option((Math.floor(i / 4) + 1) + " - " + ((i % 4) + 1) + ": " + upg[i].name);
    opt.value = upg[i].id;
    regs.options[regs.options.length++] = opt;	
  }

  regs.style.position = "absolute";
  regs.style.visibility="hidden";
  template.parentNode.insertBefore(regs,template);
}

function showSet(setNumber)
{
  for (i=0; i < result.upg.length; i++)
  {
    document.getElementById("registrationSet_" + i).style.visibility = (i == setNumber ? "" : "hidden");
  };
}

function changeSet(el)
{
  showSet(el.value);
}

function selectRegistrations(el)
{
  var count = 0;
  var sel;

  for (i=0; i < result.upg.length; i++)
  {
    sel = el.form.elements["registrationSet_" + i];
    if (sel.options[0].selected)
      count += 100;
    else
      count += sel.selectedOptions.length;
  }

  document.getElementById("selectedCount").innerHTML = count;
}

function init()
{
  var i, opt;
  var setPicker = document.getElementById("picker").elements.set;
  var regs;

  for (i=0; i < result.upg.length; i++)
  {
    opt = new Option(result.upg[i].name);
    opt.value = i;
    setPicker.options[setPicker.options.length++]=opt;
    drawSet(i);
  }

  document.getElementById("regPicker_template").style.visibility="hidden";
  if (result.upg.length === 1)
    document.getElementById("setPicker").display = "none";
  showSet(0);
}

  </script>
</head>
<body onload="init();"><div id="wrapper">
  <h1>Select Registrations</h1>
  <p>
    Please select the registrations you would like to publish. Use control-click (Windows) or cmd-click (Mac) to select
    more than one registration.  Use shift-click to a select range of registrations. If you select "Entire Set", the 
    registration set will also be published.
    <div>
      <form id="picker">
        <div id="setPicker">
          Show Set: 
            <select name="set" onchange="changeSet(this)"></select>
        </div>
        <div>Select Registrations to Publish:</div>
        <select multiple size=16 class="regPicker" id="regPicker_template" onchange="selectRegistrations(this)">
          <option value="all">------ Entire Set -------</option>
        </select>
      </form>
      <div><span id="selectedCount">0</span> registrations selected</div>
      <br><br><input type="submit" value="Publish ->">
    </div>
  </p>
</div></body>
</html>
