<?

//имя базы
$dbn = '';
//имя сервера
$dbh = 'localhost';
//порт
$dbr = '';
$dbr = '';
//имя пользователя
$dbu = '';
//пароль
$dbp = '';

  ob_start();
  session_name('sid');
  @session_start();
  define ('DBHOST', "$dbh");
  define ('DBPORT', "$dbr");
  define ('DBNAME', "$dbn");
  define ('DBUSER', "$dbu");
  define ('DBPASS', "$dbp");
if (!class_exists('PDO'))
                 die('Fatal Error: Для работы нужна поддержка PDO');
class PDOp 
{
	protected $PDO;
	public $numExecutes;
	public $numStatements;
	
	public function __construct($dsn, $username, $password, $driver_options=NULL)
	{
		$this->PDO = new PDO($dsn, $username, $password, $driver_options);
		$this->numExecutes = 0;
		$this->numStatements = 0;
	}

	public function __call($func, $args)
	{
		return call_user_func_array(array(&$this->PDO, $func), $args);
	}
	
	public function prepare() 
	{
		$this->numStatements++;

		$args = func_get_args();
		$PDOS = call_user_func_array(array(&$this->PDO, 'prepare'), $args);

		return new PDOpStatement($this, $PDOS);
	}

	public function query() 
	{
		$this->numExecutes++;
		$this->numStatements++;

		$args = func_get_args();
		$PDOS = call_user_func_array(array(&$this->PDO, 'query'), $args);

		return new PDOpStatement($this, $PDOS);
	}

	public function exec() 
	{
		$this->numExecutes++;

		$args = func_get_args();
		return call_user_func_array(array(&$this->PDO, 'exec'), $args);
	}
}


class PDOpStatement implements IteratorAggregate 
{
	protected $PDOS;
	protected $PDOp;

	public function __construct($PDOp, $PDOS) 
	{
		$this->PDOp = $PDOp;
		$this->PDOS = $PDOS;
	}

	public function __call($func, $args) 
	{
		return call_user_func_array(array(&$this->PDOS, $func), $args);
	}

	public function bindColumn($column, &$param, $type=NULL) 
	{
		if ($type === NULL)
		$this->PDOS->bindColumn($column, $param);
		else
		$this->PDOS->bindColumn($column, $param, $type);
	}

	public function bindParam($column, &$param, $type=NULL) 
	{
		if ($type === NULL)
		$this->PDOS->bindParam($column, $param);
		else
		$this->PDOS->bindParam($column, $param, $type);
	}

	public function execute()
	{
		$this->PDOp->numExecutes++;
		$args = func_get_args();
		return call_user_func_array(array($this->PDOS, 'execute'), $args);
	}

	public function __get($property) 
	{
		return $this->PDOS->$property;
	}

	public function getIterator() 
	{
		return $this->PDOS;
	}
}


class PDOStatement_ extends PDOStatement
{
	
	function execute($params = array())
	{
		if (func_num_args() == 1) 
		{
			$params = func_get_arg(0);
		}
		else
		{
			$params = func_get_args();
		}
		
		if(!is_array($params))
		{
			$params = array($params);
		}
		
		parent::execute($params, $fetchModeArgs);
		return $this;
	}

	function fetchSingle()
	{
		return $this -> fetchColumn(0);
	}

	function fetchAssoc()
	{
		$this -> setFetchMode(PDO :: FETCH_NUM);
		$data = array();
		while ($row = $this -> fetch())
		{
			$data[$row[0]] = $row[1];
		}
		return $data;
	}
}


class DB
{
	static $the;
	public function __construct()
	{
		try 
		{
			self :: $the = new PDOp('mysql:host=' . DBHOST . ';port=' . DBPORT . ';dbname=' . DBNAME, DBUSER, DBPASS);
			self :: $the->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
			self :: $the -> exec('SET CHARACTER SET utf8');
			self :: $the -> exec('SET NAMES utf8');
		}
		
		catch (PDOException $e)
		{
			die('Ошибка подключения: ' . $e -> getMessage());
		}
	}
}


$array = explode(" ",microtime());
$gen = $array[1] + $array[0];
$db = new DB();

DB::$the->query("SET NAMES utf8");


?>