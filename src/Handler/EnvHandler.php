<?php

namespace LTSC\Handler;


use Dotenv\Dotenv;
use Dotenv\Environment\Adapter\ApacheAdapter;
use Dotenv\Environment\Adapter\ArrayAdapter;
use Dotenv\Environment\Adapter\EnvConstAdapter;
use Dotenv\Environment\Adapter\PutenvAdapter;
use Dotenv\Environment\Adapter\ServerConstAdapter;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Loader;
use LTSC\Core;
use LTSC\Helper\AbstractEnv;

/**
 * @method bool isInteger(string $var)
 * @method bool isBoolean(string $var)
 * @method bool notEmpty(string $var)
 * @method bool allowedValues(string $var, array $allows)
 */
class EnvHandler extends AbstractEnv
{
    protected const STATE_EMPTY = -1;
    protected const STATE_BUILD = 0;
    protected const STATE_LOADED = 1;
    protected const STATE_LOADDIRECT = 2;
    protected const SUPPORT_ADAPTER = ['env' => EnvConstAdapter::class,
                                        'putenv' => PutenvAdapter::class,
                                        'server' => ServerConstAdapter::class,
                                        'apache' => ApacheAdapter::class,
                                        'array' => ArrayAdapter::class
                                        ];

    protected $_state = self::STATE_EMPTY;

    protected $supports = [];
    protected $_supportArray = false;
    protected $_supportOnlyArray = false;

    /**
     * @var null|Dotenv|Loader
     */
    protected $dotenv = null;
    protected $variables = null;

    public function register(Core $core) {
        $core->Container()->set('env', $this);
    }

    public function support(array $adapters) {
        if($this->_state == self::STATE_EMPTY) {
            foreach($adapters as $adapter) {
                $adapter = strtolower($adapter);
                if(key_exists($adapter, self::SUPPORT_ADAPTER)) {
                    if(!in_array($adapter, $this->supports))
                        $this->supports[] = $adapter;
                } else {
                    return $adapter;
                }
            }
            return true;
        }
        return false;
    }

    public function build(string $path, string $filename = null) {
        if($this->_state == self::STATE_EMPTY) {
            if(count($this->supports) == 0) {
                $this->dotenv = Dotenv::create($path, $filename);
            } else {
                $factories = [];
                foreach($this->supports as $support) {
                    $classname = self::SUPPORT_ADAPTER[$support];
                    $factories[] = new $classname();
                }
                $factory = new DotenvFactory($factories);
                $this->dotenv = Dotenv::create($path, $filename, $factory);
            }
            $this->_state = self::STATE_BUILD;
            return true;
        }
        return false;
    }

    public function load(bool $overload = false) {
        $method = 'load';
        if($overload)
            $method = 'overload';
        if($this->_state == self::STATE_BUILD) {
            if(in_array('array', $this->supports)) {
                $this->_supportArray = true;
                if(count($this->supports) == 1)
                    $this->_supportOnlyArray = true;
                $this->variables = $this->dotenv->$method();
            } else {
                $this->dotenv->$method();
            }
            $this->_state = self::STATE_LOADED;
            return true;
        }
        return false;
    }

    public function __call($name, $arguments) {
        $need1 = ['isInteger', 'isBoolean', 'notEmpty'];
        $need2 = 'allowedValues';
        if($this->_state == self::STATE_LOADED) {
            if($this->_supportOnlyArray)
                return false;
            if(in_array($name, $need1)) {
                if(count($arguments) == 1) {
                    if(!is_string($arguments[0]) && !is_array($arguments[0]))
                        return false;
                } else {
                    return false;
                }
                $this->dotenv->required($arguments[0])->$name();
                return true;
            } elseif ($name == $need2) {
                if(count($arguments) == 2) {
                    if(!is_string($arguments[0]) && !is_array($arguments[0]) && !is_array($arguments[1]))
                        return false;
                } else {
                    return false;
                }
                $this->dotenv->required($arguments[0])->allowedValues($arguments[1]);
                return true;
            } else
                return false;
        }
        return false;
    }

    public function loadDirect(string $content) {
        if($this->_state == self::STATE_EMPTY) {
            $this->supports = ['array'];
            $this->_supportArray = true;
            $this->_supportOnlyArray = true;
            $this->dotenv = new Loader([], new DotenvFactory([new ArrayAdapter()]));
            $this->variables = $this->dotenv->loadDirect($content);
            $this->_state = self::STATE_LOADDIRECT;
        }
    }

    public function env(string $key = null) {
        if($this->_state == self::STATE_LOADED || $this->_state == self::STATE_LOADDIRECT) {
            if($this->_supportArray) {
                if(is_null($key)) {
                    return $this->variables;
                } else {
                    if(key_exists($key, $this->variables))
                        return $this->variables[$key];
                    else
                        return null;
                }
            }
        }
        return null;
    }

    public function set($name, $value) {
        if($this->_state == self::STATE_LOADED || $this->_state == self::STATE_LOADDIRECT && $this->_supportArray)
            $this->variables[$name] = $value;
    }
}