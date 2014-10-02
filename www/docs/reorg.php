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
?><!doctype html>
<html>
<head>
  <title>VR-09 | Organize Registration</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>  
  <link rel="stylesheet" type="text/css" href="style.css">
  <style type="text/css">
SELECT.regPicker
{
  font-family: courier, fixed;
}

TD#navButtons BUTTON
{
  width: 2.5em;
  text-align: center;
  opacity:    0.3;
}

TD#navButtons.active BUTTON
{
  opacity:    1.0;
}

#copyButton
{
  opacity:	0.3;
}

#copyButton.active
{
  opacity:	1.0;
}
  </style>
  <script>
var upgFiles = JSON.parse('<?php print(json_encode($_FILES['upg'])) ?>');
var allFiles = JSON.parse('<?php print(json_encode($_FILES['upg'])) ?>');
<?php print(shell_exec("./upload.js '" . rawurlencode(json_encode($_FILES['upg'])) . "'")) ?>

/**
 * Function to draw a registration set's select multiple. It works by
 * cloning a template node and tweaking it for the given set.
 */
function drawSet(setNumber)
{
  var i;
  var template = document.getElementById("regPicker_template");
  var regs = template.cloneNode();
  var upg = result.upg[setNumber].registrations;
  var opt;

  regs.id = regs.name = "registrationSet_" + setNumber;

  for (i=0; i < upg.length; i++)
  {
    opt = new Option((Math.floor(i / 4) + 1) + " - " + ((i % 4) + 1) + ": " + upg[i].name);
    opt.value = upg[i].id;
    opt.name = upg[i].name;
    opt.loc = setNumber + "p" + i;
    regs.options[regs.options.length++] = opt;	
  }

  regs.style.position = "absolute";
  regs.style.visibility="hidden";
  template.parentNode.insertBefore(regs,template);
}

/**
 * Function to change which registration set select multiple 
 * is currently visible. They all exist at all times, and
 * have the same screen location, but are switched in and
 * would with the visibility attribute.
 */
function showSet(setNumber)
{
  for (i=0; i < result.upg.length; i++)
  {
    document.getElementById("registrationSet_" + i).style.visibility = (i == setNumber ? "" : "hidden");
  };
}

/**
 * Callback triggered when the registration set select
 * element is changed.
 */
function changeSet(el)
{
  showSet(el.value);
}

/**
 * Callback triggered when the registration select multiple is
 * changed.  We use it to update the number of registrations
 * selected onscreen.
 */  
function selectRegistrations(sel)
{
  var count = 0;

  for (i=0; i < sel.options.length; i++)
  {
    if (sel.options[i].selected)
      count += 1;
  }

  document.getElementById("selectedCount").innerHTML = count;
  document.getElementById("copyButton").className = (count ? "active" : "");
  document.getElementById("allButton").textContent = (count == 100 ? "None" : "All");
}

function findActiveSetNumber(sels)
{
  var i;
  if (!sels)
    sels = document.getElementById("picker").getElementsByTagName("SELECT");

  for (i=0; i < sels.length; i++)
  {
     if (sels[i].name.match(/^registrationSet_/) && sels[i].style.visibility != "hidden")
       return i;
  }

  return undefined;
}

function findActiveSet()
{
  var sels = document.getElementById("picker").getElementsByTagName("SELECT");
  var i = findActiveSetNumber(sels);

  return sels[i];
}

function selectAllRegistrations(el)
{
  var i, j, sel;

  sel = findActiveSet();

  /* fake-click everything */  
  for (j=0; j < 100; j++)
    sel.options[j].selected = (el.textContent == "All" ? true : false);
  selectRegistrations(sel);
}

/** Set the bank and registration number of an option, based on the
 *  name property and its position within the select list.
 */
function setBankRegNumber(opt, pos)
{
  opt.text = (Math.floor(pos / 4) + 1) + " - " + ((pos  % 4) + 1) + ": " + opt.name;
}

/** Set the bank and registration number of many options via
 *  setBankRegNumber().  If start is unspecified, do the entire
 *  set; otherwise do from start to the end of the set.
 */
function setBankRegNumbers(set, start)
{
  var i;

  if (!start)
    start = 0;

  for (i=start; i < set.options.length; i++)
    setBankRegNumber(set.options[i], i);
}

var X;

/**  Copy registrations from one of the source sets to the target set */
function copyRegs(source)
{
  var target = document.getElementById("newSet");
  var i, opt, t;
  var insIdx = target.selectedIndex;
  var targetSelIdx = target.selectedIndex;

  if (!source) 
    source = findActiveSet();
  if (insIdx === -1)
    insIdx = target.options.length;
  for (i=0; i < target.options.length; i++)
    target.options[i].selected = false;

  for (i=0; i < source.options.length; i++)
  {
    if (target.options.length == 100)
    {
      alert("Target registration set is full!");
      return;
    }

    if (source.options[i].selected)
    {
      opt = new Option((Math.floor(insIdx / 4) + 1) + " - " + ((insIdx  % 4) + 1) + ": " + source.options[i].name);
      opt.value = source.options[i].value;
      opt.name = source.options[i].name;
      opt.loc = source.options[i].loc;   
      target.add(opt, insIdx);
      insIdx++;
    }
  }

  setBankRegNumbers(target, insIdx);
  if (targetSelIdx >= 0)
    target.options[insIdx].selected = true;

  t = target.options[target.options.length - 1];  
  t.parentElement.scrollTop = t.scrollHeight * (insIdx-1);

  document.getElementById("navButtons").className = "active";
}

/** Exchange two registrations in a select box set by exchanging their option properties */
function switchRegs(set, source, target)
{
  var sOpt = set.options[source];
  var tOpt = set.options[target];
  var prop;

  function switchProps(a,b,p)
  {
    var tmp = b[p];
    b[p] = a[p];
    a[p] = tmp;
  }

  for (prop in {loc: true, name: true, text: true, value: true, selected: true})
  {
    switchProps(sOpt, tOpt, prop);
    setBankRegNumber(sOpt, source);
    setBankRegNumber(tOpt, target);
  }
}

/** Move registration(s) one step up in the target set. Triggered by up arrow button */
function navUp()
{
  var set = document.getElementById("newSet");
  var source, target;

  if (set.options[0].selected)
    return;

  for (source = 1; source < set.options.length; source++)
  {
    if (!set.options[source].selected)
      continue;
    
    target = source - 1;
    switchRegs(set, source, target);
  }
}

/** Move registration(s) one step down in the target set. Triggered by down arrow button */
function navDown()
{
  var set = document.getElementById("newSet");
  var source, target;

  if (set.options[set.options.length - 1].selected)
    return;

  for (source = set.options.length-1; source >= 0; source--)
  {
    if (!set.options[source].selected)
      continue;
    console.log(source + " is selected: " + set.options[source].selected);
    target = source + 1;
    switchRegs(set, source, target);
  }
}

/** Delete registration(s) from the target set. Triggered by the x (times) button */
function navDel()
{
  var set = document.getElementById("newSet");
  var opt;
  var optSel = set.options.selectedIndex;  /* selection that gets re-highlighted */

  if (optSel === -1)
    return;

  for (opt = 0; opt < set.options.length; opt++)
  {
    while (opt < set.options.length && set.options[opt].selected)
      set.remove(opt);
  }

  if (!set.options.length)
  {
    document.getElementById("navButtons").className = "";
    return;
  }

  if (optSel >= set.options.length)
    optSel = set.options.length - 1;

  set.options[optSel].selected = true;
  setBankRegNumbers(set, optSel);
}

/** Add an empty registration by faking a copy operation from a non-existant set */
function addBlank()
{
  copyRegs({options: [{selected: true, value: "blank", name: "Regis me", loc: "blank"}]});
}

/** MouseDown handler for the newSet selection widget which allows 
 *  toggling of a selection with a mouse-click, rather than the 
 *  default behaviour which only selects and never de-selects.
 */
function newSetClick(ev)
{
  if (ev.altKey || ev.shiftKey || ev.ctrlKey || ev.metaKey)
    return;

  if (ev.target.selected)
    window.setTimeout(function(){ev.target.selected = false; ev.target.parentElement.selectedIndex=-1}, 0);
}

/**
 * Callback triggered by body.onload. It draws the select box
 * which picks which registration set to view, calls drawSet()
 * on each set to render it based on the template, hides the
 * template (but makes sure it takes up the correct screen area),
 * and displays the first set uploaded.
 */
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
    document.getElementById("setPicker").style.display = "none";
  document.getElementById("picker").elements.uploadResult.value = JSON.stringify(result);
  showSet(0);

  document.getElementById("newSet").onmousedown = newSetClick;

  window.onkeydown = function(ev) 
  { 
    if (ev.altKey || ev.shiftKey || ev.ctrlKey || ev.metaKey)
      return;
    switch(ev.keyCode)
    {
      case 38: 
        navUp();
        break;
      case 40: 
        navDown();
        break;
      case 46: 
        navDel();
        break;
    }
  };
}

  </script>
</head>
<body onload="init();"><div id="wrapper">
  <h1>Organize Registrations</h1>
  <p>
    Please select the registrations you would like to use in your new set, and copy them to the new
    set with the "Copy" button.  Use control-click (Windows) or cmd-click (Mac) to select
    more than one registration.  Use shift-click to a select range of registrations.
    <div>
      <form id="picker" onsubmit="return false;">
        <input 
	  type="hidden" 
	  name="uploadData" 
	  value="<?php print(rawurlencode(json_encode(array_merge($_FILES['dat'], $_FILES['upg'])))) ?>"
	/>
        <input type="hidden" name="uploadResult">
        <div id="setPicker">
          Show Set: 
            <select name="set" onchange="changeSet(this)"></select>
        </div>
        <table><tr>
          <td>
            <div>Select Registrations to copy:</div>
            <select style="width:270px;" multiple size=16 class="regPicker" id="regPicker_template" onchange="selectRegistrations(this)"></select>
            <div style="text-align: right;"><button id="allButton" onclick="selectAllRegistrations(this);">All</button></div>
            <div style="margin-top: -1.2em;"><span id="selectedCount">0</span> registrations selected</div>
          </td>
          <td valign="middle">
	    &nbsp;<button id="copyButton" onclick="copyRegs();">Copy -></button>&nbsp;
	  </td>
          <td valign=top>
            <div>New Set:</div>
            <select style="width:270px;" multiple size=16 class="regPicker" id="newSet"></select>
            <div style="text-align: right;"><button onclick="addBlank();">Add Blank</button></div>
            <div style="margin-top: -1.2em;"><span id="newSetSize">0</span> registrations</div>
          </td>
          <td id="navButtons">
            <button onclick="navUp();">&#8593;</button><br>
            <button onclick="navDel();">&times;</button><br>
            <button onclick="navDown();">&#8595;</button><br>
          </td>
	</tr>
        <tr><td colspan=3 align=right><br><button>Download</button></td></tr>
        </tr></table>
      </form>
    </div>
  </p>
</div></body>
</html>
