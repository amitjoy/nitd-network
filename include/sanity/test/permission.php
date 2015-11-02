<?php

class SESanityTestPermission extends SESanityTest
{
  // The minimum permissions required. Will bitwise and it with current settings
  var $path;
  
  // The minimum permissions required. Will bitwise and it with current settings
  var $perms;
  
  // Apply recursively?
  var $recurse;
  
  
  // Info
  var $path_real;
  
  var $perms_actual;
  
  
  
  function SESanityTestPermission($options)
  {
    parent::SESanityTest($options);
    
    $cleaned_filename = $this->path;
    if( substr($cleaned_filename, 0, 2)=="./" ) $cleaned_filename = substr($cleaned_filename, 2, strlen($cleaned_filename)-2);
    $cleaned_filename_array = preg_split("/[^a-zA-Z0-9_\.-]+/", $cleaned_filename);
    $cleaned_filename = join('_', $cleaned_filename_array);
    $cleaned_filename = preg_replace('/\.[^.]$/', '', $cleaned_filename);
    $this->name = 'permission_'.$cleaned_filename;
  }
  
  
  
  function setOptions($options)
  {
    if( isset($options['path']) ) $this->path = $options['path'];
    if( isset($options['perms']) ) $this->perms = $options['perms'];
    if( isset($options['recurse']) ) $this->recurse = $options['recurse'];
    
    parent::setOptions($options);
  }
  
  
  
  function execute()
  {
    if( $this->preset() )
      return;
    
    clearstatcache();
    
    if( substr($this->path, 0, 2)=="./" || substr($this->path, 0, 2)==".\\" )
    {
      $sanity =& SESanity::getInstance();
      $this->path = $sanity->root.DIRECTORY_SEPARATOR.substr($this->path, 2, strlen($this->path)-2);
    }
    
    $this->path_real = realpath($this->path);
    
    if( !file_exists($this->path) )
    {
      $this->perms_actual = 0;
      $this->result = FALSE;
      return;
    }
    
    $this->perms_actual = 0;
    
    // Linux
    if( strtoupper(substr(php_uname('s'), 0, 3)) !== 'WIN' )
    {
      if( is_executable($this->path) ) $this->perms_actual = $this->perms_actual | 0x0001;
      if( is_writable($this->path)   ) $this->perms_actual = $this->perms_actual | 0x0002;
      if( is_readable($this->path)   ) $this->perms_actual = $this->perms_actual | 0x0004;
    }
    
    else
    {
      $stat_info = $this->_alt_stat($this->path);
      $this->perms_actual = ( $stat_info['perms']['mode1'] & 0x0007 );
    }
    
    
    switch( TRUE )
    {
      case ( ($this->perms & 0x0004) & ~$this->perms_actual ):
      case ( ($this->perms & 0x0002) & ~$this->perms_actual ):
      case ( ($this->perms & 0x0001) & ~$this->perms_actual ):
        $this->result = FALSE;
        break;
      default:
        $this->result = TRUE;
        break;
    }
  }
  
  
  
  function merge($new_object)
  {
    $this->perms = ( $this->perms | $new_object->perms );
  }
  
  
  
  function getValue()
  {
    switch( TRUE )
    {
      default:
        return "Okay";
        break;
      case ( !$this->perms ):
        return "File does not exist";
        break;
      case ( ($this->perms & 0x0004) && !(0x0004 & $this->perms_actual) ):
        return "File not executable";
        break;
      case ( ($this->perms & 0x0002) && !(0x0002 & $this->perms_actual) ):
        return "File not writable";
        break;
      case ( ($this->perms & 0x0001) && !(0x0001 & $this->perms_actual) ):
        return "File not readable";
        break;
    }
  }
  
  
  
  function _alt_stat($file)
  {
    clearstatcache();
    $ss=@stat($file);
    if(!$ss) return false; //Couldnt stat file

    $ts=array(
    0140000=>'ssocket',
    0120000=>'llink',
    0100000=>'-file',
    0060000=>'bblock',
    0040000=>'ddir',
    0020000=>'cchar',
    0010000=>'pfifo'
    );

    $p=$ss['mode'];
    $t=decoct($ss['mode'] & 0170000); // File Encoding Bit

    $str =(array_key_exists(octdec($t),$ts))?$ts[octdec($t)]{0}:'u';
    $str.=(($p&0x0100)?'r':'-').(($p&0x0080)?'w':'-');
    $str.=(($p&0x0040)?(($p&0x0800)?'s':'x'):(($p&0x0800)?'S':'-'));
    $str.=(($p&0x0020)?'r':'-').(($p&0x0010)?'w':'-');
    $str.=(($p&0x0008)?(($p&0x0400)?'s':'x'):(($p&0x0400)?'S':'-'));
    $str.=(($p&0x0004)?'r':'-').(($p&0x0002)?'w':'-');
    $str.=(($p&0x0001)?(($p&0x0200)?'t':'x'):(($p&0x0200)?'T':'-'));

    $s=array(
    'perms'=>array(
    'umask'=>sprintf("%04o",@umask()),
    'human'=>$str,
    'octal1'=>sprintf("%o", ($ss['mode'] & 000777)),
    'octal2'=>sprintf("0%o", 0777 & $p),
    'decimal'=>sprintf("%04o", $p),
    'fileperms'=>@fileperms($file),
    'mode1'=>$p,
    'mode2'=>$ss['mode']),

    'owner'=>array(
    'fileowner'=>$ss['uid'],
    'filegroup'=>$ss['gid'],
    'owner'=>
    (function_exists('posix_getpwuid'))?
    @posix_getpwuid($ss['uid']):'',
    'group'=>
    (function_exists('posix_getgrgid'))?
    @posix_getgrgid($ss['gid']):''
    ),

    'file'=>array(
    'filename'=>$file,
    'realpath'=>(@realpath($file) != $file) ? @realpath($file) : '',
    'dirname'=>@dirname($file),
    'basename'=>@basename($file)
    ),

    'filetype'=>array(
    'type'=>substr($ts[octdec($t)],1),
    'type_octal'=>sprintf("%07o", octdec($t)),
    'is_file'=>@is_file($file),
    'is_dir'=>@is_dir($file),
    'is_link'=>@is_link($file),
    'is_readable'=> @is_readable($file),
    'is_writable'=> @is_writable($file)
    ),

    'device'=>array(
    'device'=>$ss['dev'], //Device
    'device_number'=>$ss['rdev'], //Device number, if device.
    'inode'=>$ss['ino'], //File serial number
    'link_count'=>$ss['nlink'], //link count
    'link_to'=>($s['type']=='link') ? @readlink($file) : ''
    ),

    'size'=>array(
    'size'=>$ss['size'], //Size of file, in bytes.
    'blocks'=>$ss['blocks'], //Number 512-byte blocks allocated
    'block_size'=> $ss['blksize'] //Optimal block size for I/O.
    ),

    'time'=>array(
    'mtime'=>$ss['mtime'], //Time of last modification
    'atime'=>$ss['atime'], //Time of last access.
    'ctime'=>$ss['ctime'], //Time of last status change
    'accessed'=>@date('Y M D H:i:s',$ss['atime']),
    'modified'=>@date('Y M D H:i:s',$ss['mtime']),
    'created'=>@date('Y M D H:i:s',$ss['ctime'])
    ),
    );

    clearstatcache();
    return $s;
  }
}

?>