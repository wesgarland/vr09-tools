Welcome to VR09-Tools.

VR09-Tools is a toolkit (application and libraries) for manipulating 
registration ("User Program Group") files, e.g. VR09_001.UPG, used by the 
Roland VR-09 Combo Organ.

This library and toolkit are provided under the terms of the GNU General 
Public License, version 3.0 (GPLv3). You can read the full text of the 
license in the file COPYING, which is distributed in the same directory
as this readme.

VR09-Tools is written in the CommonJS platform GPSEE. GPSEE runs on UNIX, 
Linux, and Mac OS X. You can find GPSEE at http://code.google.com/p/gpsee.

Status
======
This version is a very, very, very early pre-release. Just about everything
is subject to change without notice. If you are using the libraries to 
develop your own programs, please let me know which interfaces you find useful.

WARNING
=======
I have read no documentation nor received any input from Roland in developing
this software package. It is possible that there are bugs or features in this
software which can damage your VR-09. 

*** USE THIS SOFTWARE AT YOUR OWN RISK!!! ***

Installation
============
bash# ./configure
bash# sudo make install

Note: You must have previously installed GPSEE!

Examples 
======== 
Here's how to copy bank 17, patch 1, into all registrations and store as 
the file hello.UPG:

bash# vr09-regedit -- test3 patch 17-1 store 1-1 end patch 1-2 print "%N:%n" \
copy 1-1 next until 1-1 rename "Empty Set" save hello.UPG show all

You can visualize the program structure thus:

patch 17-1
  store 1-1 
  end 
patch 1-2 
  print "%N:%n" 
  copy 1-1 
  next 
  until 1-1 
rename "Empty Set" 
save "hello.UPG"
show all

The "until 1-1" command takes advantage of the wrap-around behaviour of next.

Tips
====
You can run programs in this directory without installing them, which is 
useful for debugging VR09-Tools. This is how:

bash# gsr -c 'require.paths.push(".")' -F vr09-regedit -- --help

Author
======
Copyright (c) 2014 Wes Garland, wes@page.ca
