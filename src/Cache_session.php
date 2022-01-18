<?php
namespace booosta\cache_session;

use \booosta\Framework as b;
b::init_module('cache_session');

class Cache_session extends \booosta\cache\Cache
{
  use moduletrait_cache_session;

  protected $var;

  public function __construct($var = 'cache')
  {
    parent::__construct();
    $this->var = $var;
    session_start();
  }

  public function after_instanciation()
  {
    parent::after_instanciation();
    $this->store = $this->makeInstance("\\booosta\\cache_session\\Cachestore_Session", $this->var);
  }
}


class Cachestore_Session extends \booosta\cache\Cachestore
{
  protected $var;

  public function __construct($var = 'cache')
  {
    parent::__construct();
    $this->var = $var;
  }

  public function getobj($key)
  {
    $key = md5($key);
    if(isset($_SESSION[$this->var][$key])) return $_SESSION[$this->var][$key]['value'];
    return false;
  }

  public function storeobj($key, $data)
  {
    $key = md5($key);
    $_SESSION[$this->var][$key] = [];
    $_SESSION[$this->var][$key]['value'] = $data;
    $_SESSION[$this->var][$key]['timestamp'] = time();
  }

  public function get_timestamp($key)
  {
    $key = md5($key);
    if(isset($_SESSION[$this->var][$key])) return $_SESSION[$this->var][$key]['timestamp'];
    return 0;
  }

  public function invalidate($key)
  {
    $key = md5($key);
    unset($_SESSION[$this->var][$key]);
  }

  public function clear()
  {
    $_SESSION[$this->var] = [];
  }

  public function cleanup()
  {
    foreach($_SESSION[$this->var] as $key=>$val)
      if($this->is_invalid($key)) unset($_SESSION[$this->var][$key]);
  }
}
