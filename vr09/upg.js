/**
 *  Copyright (c) 2014 Wes Garland <wes@page.ca>
 *
 *  This file is part of vr09-tools.
 *  vr09-tools is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  vr09-tools is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with vr09-tools.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @file           upg.js          Code for working with .UPG files, which hold
 *                                  Roland VR-09 Registration Sets. 
 *
 *  @author         Wes Garland, wes@page.ca
 *  @date           Aug 2014
 */ 

const FS = require("fs-base");
const BINARY = require("binary");
const COMMON = require("./common");

exports.Registration = function UPG_Registration(set, reg)
{
  var offset;

  if (arguments.length == 2)
  {
    offset = 256 + (reg * 392);
    this.data = set.storage.slice(offset, offset + 392);
  }
}

exports.Registration.prototype.duplicate = function UPG_Registration_duplicate()
{
  var newReg = new Registration();

  newReg.data = new BINARY.ByteArray(392);
  this.data.copy(newReg.data);

  return newReg;
}

exports.Registration.prototype.save = function UPG_Registration_save(filenameFormat, set, i)
{
  var filename = COMMON.formatString(filenameFormat, set, i);
  var file = FS.openRaw(filename, { write: true, create: true } );
  
  file.write(this.data);
  file.close();
}

/** Setter and Getter for Registration.prototype.name */
Object.defineProperty(exports.Registration.prototype, "name", 
{ 
  get: function UPG_Registration_name_getter() 
  { 
    return COMMON.stringField(this.data, 67, 12);
  },

  set: function UPG_Registration_name_setter(val)
  {
    var baName = new BINARY.ByteArray((val + "                ").slice(0,12));
    baName.copy(this.data, 0, undefined, 67);
  }
});

exports.Registration.prototype.toString = function UPG_Registration_toString()
{
  return "[Registration Object (" + this.name + ")]"; 
}

exports.RegistrationSet = function UPG_RegistrationSet(filename)
{
  var i;

  this.filename = filename;
  this.storage = COMMON.mmap(this.filename);
  this.format  = this.storage.slice(0,14).decodeToString("ascii");
  if (this.format !== "AT-2012 Regist")
    throw new Error("Unrecognized file format '" + escape(ffi.Memory(this.storage.slice(0,14)).asString()).replace(/%20/g, " ") + "'");
  
  this.length = 100;
  for (i=0; i < this.length; i++)
    this[i] = new Registration(this, i);

  Object.defineProperty(this, "name", 
  { 
    get: function UPG_RegistrationSet_getter()
    { 
      return COMMON.stringField(this.storage, 16, 16);
    },

    set: function UPG_RegistrationSet_setter(val)
    {
      var baName = new BINARY.ByteArray((val + "                ").slice(0,16));
      baName.copy(this.storage, 0, undefined, 16);
    }
  });
}

exports.RegistrationSet.prototype.toString = function UPG_RegistrationSet_toString()
{
  return "[RegistrationSet Object (" + this.filename + ")]"; 
}

exports.RegistrationSet.prototype.save = function UPG_RegistrationSet_save(filename)
{
  var file = FS.openRaw(filename, { write: true, create: true });
  var i;

  file.write(this.storage.slice(0, 256));
  for (i=0; i < 100; i++)
    file.write(this[i].data);
  file.close();
}

/**
 *  Copy a ByteArray into an Array, ByteArray, or Array-like object.
 *
 *  Copies the Number values from this ByteArray between start and stop to a target ByteArray or Array at the
 *  targetStart offset. start, stop, and targetStart must be Numbers, undefined, or omitted. If omitted, start
 *  is presumed to be 0. If omitted, stop is presumed to be the length of this ByteArray. If omitted, 
 *  targetStart is presumed to be 0. 
 */
if (!BINARY.ByteArray.prototype.copy)
  BINARY.ByteArray.prototype.copy = function _monkeypatch_ByteArray_copy(target, start, stop, targetStart)
{
  var i;

  if (!start)
    start = 0;
  if (!stop)
    stop = this.length;
  if (!targetStart)
    targetStart = 0;

  for (i=start; i < stop; i++)
    target[i - start + targetStart] = this[i];
}

exports.RegistrationSet.prototype.rename = function UPG_RegistrationSet_rename(name)
{
  this.name = name;
}

exports.Registration.prototype.rename = function UPG_Registration_rename(name)
{
  this.name = name;
}
