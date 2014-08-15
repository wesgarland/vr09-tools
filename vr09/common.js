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
 *  @file           common.js       Common helper code for working with
 *                                  Roland VR-09 data.
 *
 *  @author         Wes Garland, wes@page.ca
 *  @date           Aug 2014
 */ 

const FS = require("fs-base");
const BINARY = require("binary");
const ffi = require("gffi");

const _mmap 	= new ffi.CFunction(ffi.pointer, 	"mmap", 	ffi.pointer, ffi.size_t, ffi.int, ffi.int, ffi.int, ffi.off_t);
const _munmap 	= new ffi.CFunction(ffi.int, 		"munmap", 	ffi.pointer, ffi.size_t);
const _open 	= new ffi.CFunction(ffi.int, 		"open", 	ffi.pointer, ffi.int, ffi.int);
const _close 	= new ffi.CFunction(ffi.int,		"close", 	ffi.int);
const _strerror = new ffi.CFunction(ffi.pointer,	"strerror", 	ffi.int);
const _fstat 	= new ffi.CFunction(ffi.int, 		"fstat", 	ffi.int, ffi.pointer);

exports.mmap = function mmap(filename)
{
  function perror(msg)
  {
    if (ffi.errno)
      return msg + ": " + _strerror(ffi.errno).asString();
    return msg;
  }

  var fd = _open(filename, ffi.std.O_RDONLY, parseInt('0666'));
  if (fd == -1)
    throw(new Error(perror("Cannot open file " + filename)));

  try        
  {
    var sb = new ffi.MutableStruct("struct stat");
    if (_fstat(fd, sb) != 0)
      throw(new Error(perror("Cannot stat file " + filename)));

    var mem = _mmap.call(null, sb.st_size, ffi.std.PROT_READ, ffi.std.MAP_PRIVATE, fd, 0);
    if (mem == ffi.Memory(-1))
      throw(new Error(perror("Cannot read file " + filename)));

    mem.finalizeWith(_munmap, mem, sb.st_size);
    return BINARY.ByteArray(mem, sb.st_size);
  }
  finally
  {
    _close(fd);
  }
}

/**
 *  Format a string, similar to printf().
 *  %i - patch index
 *  %n - patch name
 *  %N - patch number (B-N)
 *  %s - set name
 *  %% - percent symbol
 *  
 *  @param   set     The working registration set
 *  @param   i       The working registration index
 *  @param   format  The format string
 */
exports.formatString = function formatString(format, set, i)
{
  return format
    .replace(/%s/g, set.name)
    .replace(/%i/g, i)
    .replace(/%n/g, set[i].name)
    .replace(/%N/g, (Math.floor(i/4) + 1) + "-" + ((i % 4) + 1))
    .replace(/%%/g, "%");
}

exports.stringField = function stringField(data, start, length)
{
  var ba = BINARY.ByteArray(data.slice(start, start + length));
  var i;

  for (i=0; i < ba.length; i++)
  {
    if (ba[i] == 0)
    {
      ba.length = i;
      break;
    }
  }

  return ffi.Memory(ba).asString();
}

/** Shim for early GPSEE which is not quite ES5 */
if (!Object.hasOwnProperty("defineProperty"))
  Object.defineProperty = function(obj, propName, descriptor)
{
  if (descriptor.get)
    obj.__defineGetter__(propName, descriptor.get);
  if (descriptor.set)
    obj.__defineSetter__(propName, descriptor.set);
}

