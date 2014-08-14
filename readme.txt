Welcome to VR09-tools.

This library and toolkit are provided under the terms of the GNU General Public License,
version 3.0. You can read the full text of the license in the file COPYING, which is
distributed in the same directory as this readme.

VR09-tools is written in the CommonJS platform GPSEE. GPSEE runs on UNIX, Linux, and Mac OS X.
You can find GPSEE at http://code.google.com/p/gpsee.

Status
======
This version is a very, very, very early pre-release. Just about everything is subject to
change without notice. If you are using the libraries to develop your own programs, please
let me know which interfaces you find useful as-is.

Installation
============
bash# ./configure
bash# sudo make install

Note: You must have previously installed GPSEE!

Tips
====
You can run programs in this directory without installing them, which is useful for
debugging. This is how:

bash# gsr -c 'require.paths.push(".")' -F vr09-regedit -- --help

Author
======
Wes Garland, wes@page.ca
