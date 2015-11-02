
/* $Id: sprintf.js 268 2009-12-04 23:30:59Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js

var vsprintf = function(str, args)
{
  // Check for no params
  if( !args || !args.length )
  {
    return str;
  }

  // Replace params
  var out = '';
  var m;
  var masterIndex = 0;
  var currentIndex;
  var arg;
  var instr;
  var meth;
  var sign;
  while( str.length > 0 )
  {
    // Check for no more expressions
    if( !str.match(/[%]/) )
    {
      out += str;
      break;
    }

    // Remove any preceeding non-expressions
    m = str.match(/^([^%]+?)([%].+)?$/)
    if( m )
    {
      out += m[1];
      str = typeof(m[2]) ? m[2] : '';
      if( str == '' )
      {
        break;
      }
    }

    // Check for escaped %
    if( str.substring(0, 2) == '%%' )
    {
      str = str.substring(2);
      out += '%';
      continue;
    }

    // Proc next params
    m = str.match(/^[%](?:([0-9]+)\x24)?(\x2B)?(\x30|\x27[^$])?(\x2D)?([0-9]+)?(?:\x2E([0-9]+))?([bcdeEfosuxX])/)
    if( m )
    {
      instr = m[7];
      meth = m[6] || false;
      sign = m[2] || false;
      currentIndex = ( m[1] ? m[1] - 1 : masterIndex++ );
      if( args[currentIndex] )
      {
        arg = args[currentIndex];
      }
      else
      {
        throw('Undefined argument for index ' + currentIndex);
      }

      // Make sure passed sane argument type
      switch( typeof(arg) )
      {
        case 'number':
        case 'string':
        case 'boolean':
          // Okay
          break;

        case 'undefined':
          if( arg == null )
          {
            arg = '';
            break;
          }
        default:
          throw('Unknown argument type: ' + typeof(arg));
          break;
      }

      // Now proc instr
      switch( instr )
      {
        // Binary
        case 'b':
          if( typeof(arg) != 'number' ) arg = parseInt(arg);
          arg = arg.toString(2);
          break;

        // Char
        case 'c':
          arg = String.fromCharCode(arg);
          break;

        // Integer
        case 'd':
          arg = parseInt(arg);
          break;

        // Scientific notation
        case 'E': 
        case 'e':
          if( typeof(arg) != 'number' ) arg = parseFloat(arg);
          if( meth )
          {
            arg = arg.toExponential(meth);
          }
          else
          {
            arg = arg.toExponential();
          }
          if( instr == 'E' ) arg = arg.toUpperCase();
          break;

        // Unsigned integer
        case 'u':
          arg = Math.abs(parseInt(arg));
          break;

        // Float
        case 'f':
          if( meth )
          {
            arg = parseFloat(arg).toFixed(meth)
          }
          else
          {
            arg = parseFloat(arg);
          }
          break;

        // Octal
        case 'o':
          if( typeof(arg) != 'number' ) arg = parseInt(arg);
          arg = arg.toString(8);
          break;

        // String
        case 's':
          if( typeof(arg) != 'string' ) arg = String(arg);
          if( meth )
          {
            arg = arg.substring(0, meth);
          }
          break;

        // Hex
        case 'x':
        case 'X':
          if( typeof(arg) != 'number' ) arg = parseInt(arg);
          arg = arg.toString(8);
          if( instr == 'X' ) arg = arg.toUpperCase();
          break;
      }
      
      // Add a sign if requested
      if( (instr == 'd' || instr == 'e' || instr == 'f') && sign && arg > 0 )
      {
        arg = '+' + arg;
      }

      // Do repeating if necessary
      var repeatChar, repeatCount;
      if( m[3] )
      {
        repeatChar = m[3];
      }
      else
      {
        repeatChar = ' ';
      }
      if( m[5] )
      {
        repeatCount = m[5];
      }
      else
      {
        repeatCount = 0;
      }
      repeatCount -= arg.length;

      // Do the repeating
      if( repeatCount > 0 )
      {
        var paddedness = function(str, count)
        {
          var ret = '';
          while( count > 0 )
          {
            ret += str;
            count--;
          }
          return ret;
        }(repeatChar, repeatCount);
        
        if( m[4] )
        {
          out += arg + paddedness;
        }
        else
        {
          out += paddedness + arg;
        }
      }

      // Just add the string
      else
      {
        out += arg;
      }

      // Remove from str
      str = str.substring(m[0].length);
    }
    else
    {
      throw('Malformed expression in string: ' + str);
    }
  }

  return out;
}

var sprintf = function()
{
  var args = [];
  var str = arguments[0];
  for( i = 1, l = arguments.length; i < l; i++ )
  {
    args[i-1] = arguments[i];
  }
  return vsprintf(str, args);
}