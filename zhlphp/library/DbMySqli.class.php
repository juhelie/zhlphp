<?php 
// +----------------------------------------------------------------------
// | Class  Mysql操作基类
// +----------------------------------------------------------------------
// | Copyright (c) 2020
// +----------------------------------------------------------------------

defined('SYS_PATH') or define('SYS_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');    // 项目根目录(绝对路径)
defined('SYS_DEBUG') or define('SYS_DEBUG', true);                      // 调试开关
defined('SYS_DEBUG_LOG') or define('SYS_DEBUG_LOG', true);              // 系统日志开关
defined('SYS_ERR_PATH') or define('SYS_ERR_PATH', 'runtime/error/');   // 日志打印文件路径

class DbMySqli {

    private $_dbCon;
    private $_result;
    private $_tableFix;
    private $_sql;

    //定义静态变量保存当前类的实例
    private static $instance;

    //防止在外部实例化
    private function __construct($config){
        $this->_tableFix = $config['db_fix'];
        $this->connect($config['db_host'], $config['db_user'], $config['db_pwd'], $config['db_name'], $config['db_port'], $config['db_char']);
    }

    //防止在外部克隆
    private function __clone(){

    }

    //通过静态公有的方法获取这个类的实例
    public static function getInstance($config){
        //当前对象不属于当前例就实例化
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

	/**
	 * @fun   连接数据库
	 * @desc  
	 */
    function connect($address, $account, $pwd, $dbname, $port, $char){
        try {
            $this->_dbCon = mysqli_connect($address, $account, $pwd, $dbname, $port);
        } catch (mysqli_sql_exception $e) {
            /*$errorStr = '[' . date('Y-m-d H:i:s') . '][SYS] ' . dirname(__FILE__) . (__CLASS__) . ($e->getMessage()) . PHP_EOL;
            if(SYS_DEBUG_LOG){
                error_log($errorStr, 3, SYS_PATH . SYS_ERR_PATH . 'SYS'. date('Y_m_d') . '.log', 'extra');
            }
            if(SYS_DEBUG){
                echo "$errorStr<br>";
            }
            exit;*/
        }

        if(!$this->_dbCon){
			$errTxt = iconv("GB2312","UTF-8//IGNORE",mysqli_connect_error()); 
			exit('Error('.mysqli_connect_errno().'):'.$errTxt);
        }else{
            mysqli_query($this->_dbCon,'set names '.strtolower($char));
            return 1;
        }
    }
	
	/**
	 * @fun   where 条件处理
	 * @desc  
	 */
	private function whereTo($params){
		$whereSql = '';
		if(!isset($params['where']) || empty($params['where'])){
			return $whereSql;
		}
		if(is_array($params['where'])){
            foreach($params['where'] as $k=>$v){
                if(!preg_match('/^[A-Za-z]+/',$k)){
                    exit('Error : 查询语句没有指定字段名');
                }

                if(is_array($v)){
                    $symbol = isset($v[1]) && $v[1] ? $v[1] : '';
                    $fieldVal = isset($v[0]) && $v[0] ? $v[0] : '';
                }else if(is_string($v) || is_numeric($v)){
                    $symbol = '=';
                    $fieldVal = strval($v);
                }

                $fieldVal = $this->sqlStr($fieldVal);
                $whereSql .= ' and '.$k.' '.$symbol.' '.$fieldVal;
            }
        }else{
            $whereSql = ' '.$params['where'];
        }
		return $whereSql;
	}

    /**
     * @fun   where 函数条件处理
     * @desc
     */
    private function whereFun($params){
        $whereSql = '';
        if(isset($params['locate']) && is_array($params['locate']) && !empty($params['locate'])){
            $whereSql .= ' and (';
            foreach($params['locate'] as $k=>$v){
                $k = $this->sqlVal($k);
                $v = $this->sqlStr($v);
                $whereSql .= " locate ($v,$k) > 0 or";
            }
            $whereSql = substr($whereSql,0,-2);
            $whereSql .= ' ) ';
        }
        if(isset($params['gt']) && $params['gt']){
            foreach($params['gt'] as $k=>$v){
                $whereSql .= " and $k > '$v' ";
            }
        }
        if(isset($params['lt']) && $params['lt']){
            foreach($params['lt'] as $k=>$v){
                $whereSql .= " and $k < '$v' ";
            }
        }
        if(isset($params['gts']) && $params['gts']){
            foreach($params['gts'] as $k=>$v){
                $whereSql .= " and $k >= '$v' ";
            }
        }
        if(isset($params['lts']) && $params['lts']){
            foreach($params['lts'] as $k=>$v){
                $whereSql .= " and $k <= '$v' ";
            }
        }
        return $whereSql;
    }

	/**
	 * @fun   group by 分组处理
	 * @desc  
	 */
	private function groupTo($params){
		$groupSql = '';
		if(isset($params['group']) && is_string($params['group'])){
			$groupSql = ' group by '.$this->sqlVal($params['group']).' ';
		}
		return $groupSql;
	}
	
	/**
	 * @fun   order by 排序处理
	 * @desc  
	 */
	private function orderTo($params){
		$orderSql = '';
		if(isset($params['order']) && is_string($params['order'])){
			$orderSql = ' order by '.$this->sqlVal($params['order']).' ';
		}
		return $orderSql;
	}
	
	/**
	 * @fun   limit 分页处理
	 * @desc  
	 */
	private function limitTo($params){
		$limitSql = '';
		if(isset($params['limit']) && !empty($params['limit'])){
		    if(is_array($params['limit'])){
                $start = intval(reset($params['limit']));
                $endArr = array_slice($params['limit'],1,1);
                $end = intval(reset($endArr));
                $start = $start >= 0 ? $start : 0;
                if($end > 0){
                    $limitSql = ' limit '.$start.','.$end.' ';
                }
            }else{
		        $limit = intval($params['limit']);
		        if($limit > 0){
                    $limitSql = ' limit '.$limit.' ';
                }
            }

		}
		return $limitSql;
	}
	
	/**
	 * @fun   start-end 分页处理
	 * @desc  
	 */
	private function startEnd($params){
		$limitArr = array();
		if(isset($params['limit']) && !empty($params['limit'])){
			$limits = explode(',',$params['limit']);
			$limitArr['start'] = '0';
			if(isset($limits[1]) && $limits[1]){
				$limitArr['start'] = intval($limits[0]);
				$limitArr['end'] = intval($limits[1]);
			}else{
				$limitArr['end'] = intval($limits[0]);
			}
		}
		return $limitArr;
	}
	
	/**
	 * @fun   sql 表 处理
	 * @desc  
	 */
	private function setTable($params){
		//$table = $this->_table;
		if(isset($params['table']) && $params['table']){
			$table = $this->sqlVal(strtolower($this->_tableFix.$params['table']));
		}
		return $table;
	}
	
	/**
	 * @fun   sql params 处理
	 * @desc  
	 */
	private function sqlSet($params){
		$sqlArr['field'] = '*';
		if(isset($params['field']) && $params['field']){
			$sqlArr['field'] = $this->sqlVal(strtolower($params['field']));
		}
		$sqlArr['table'] = $this->setTable($params);
		$sqlArr['as'] = '';
		if(isset($params['as']) && $params['as']){
			$sqlArr['as'] = $this->sqlVal(strtolower($params['as']));
		}
		$sqlArr['leftJoin'] = '';
		if(isset($params['leftjoin']) && $params['leftjoin']){
			foreach($params['leftjoin'] as $k=>$v){
				$sqlArr['leftJoin'] .= ' left join '.$this->_tableFix.$k.' '.$this->sqlVal(strtolower($v));
			}
		}
		$sqlArr['rightJoin'] = '';
		if(isset($params['rightjoin']) && $params['rightjoin']){
			foreach($params['rightjoin'] as $k=>$v){
				$sqlArr['rightJoin'] .= ' right join '.$this->_tableFix.$k.' '.$this->sqlVal(strtolower($v));
			}
		}
		$sqlArr['innerJoin'] = '';
		if(isset($params['innerjoin']) && $params['innerjoin']){
			foreach($params['innerjoin'] as $k=>$v){
				$sqlArr['innerJoin'] .= ' inner join '.$this->_tableFix.$k.' '.$this->sqlVal(strtolower($v));
			}
		}
		return $sqlArr;
	}
	
	/**
	 * @fun   sql data 处理
	 * @desc  
	 */
	private function setData($params){
		if(isset($params['data']) && $params['data']){
			return $params['data'];
		}
		return array();
	}

    /**
     * Notes: debug
     * User: ZhuHaili
     * Date: 2020/10/10
     */
	private function deBugLog($sql){
	    $this->_sql = $sql;
    }

	/**
	 * @fun   查询单条
	 * @desc  
	 */
    function find($params){
		$sqlArr = $this->sqlSet($params);
		$w = $this->whereTo($params);
        $f = $this->whereFun($params);
		$o = $this->orderTo($params);
		$sql = 'select '.$sqlArr['field'].' from '.$sqlArr['table'].' '.$sqlArr['as'].' ';
		$sql .= $sqlArr['leftJoin'].' '.$sqlArr['rightJoin'].' '.$sqlArr['innerJoin'].' ';
		$sql .= ' where 1=1 '.$w.' '.$f.' '.$o;
        $this->deBugLog($sql);
		return $this->query($sql,1,true);
    }
	
	/**
	 * @fun   查询-默认查询
	 * @desc  支持limit分页
	 */
    function select($params = array()){
		$sqlArr = $this->sqlSet($params);
		$w = $this->whereTo($params);
		$f = $this->whereFun($params);
		$g = $this->groupTo($params);
		$o = $this->orderTo($params);
		$l = $this->limitTo($params);
		$sql = 'select '.$sqlArr['field'].' from '.$sqlArr['table'].' '.$sqlArr['as'].' ';
		$sql .= $sqlArr['leftJoin'].' '.$sqlArr['rightJoin'].' '.$sqlArr['innerJoin'].' ';
		$sql .= ' where 1=1 '.$w.' '.$f.' '.$g.' '.$o.' '.$l;
        $this->deBugLog($sql);
		return $this->query($sql,1);
    }
	
	/**
	 * @fun   查询-起止分页
	 * @desc  支持起始分页
	 */
    function findAll($params){
		$sqlArr = $this->sqlSet($params);
		$w = $this->whereTo($params);
        $f = $this->whereFun($params);
		$g = $this->groupTo($params);
		$o = $this->orderTo($params);
		$l = $this->startEnd($params);
		$sqlStart = '';
		$sqlEnd = '';
		if(!empty($l)){
			$start = $l['start'];
			$end = $l['end'];
			$sqlStart = "select view_t1.* from ( select @rowno:=@rowno+1 as R, ymd_t.* from ( ";
			$sqlEnd = ") ymd_t ) view_t1 where view_t1.r>$start and view_t1.r<=$end";
		}
		$sqlContent = 'select @rowno:=0,'.$sqlArr['field'].' from '.$sqlArr['table'].' '.$sqlArr['as'].' ';
		$sqlContent .= $sqlArr['leftJoin'].' '.$sqlArr['rightJoin'].' '.$sqlArr['innerJoin'].' ';
		$sqlContent .= ' where 1=1 '.$w.' '.$f.' '.$g.' '.$o;
		$sql = $sqlStart.$sqlContent.$sqlEnd;
        $this->deBugLog($sql);
		return $this->query($sql,1);
    }
	
	/**
	 * @fun   新增
	 * @desc  条件必须
	 */
	function add($params){
		$table = $this->setTable($params);
		$data  = $this->setData($params);
		$field = '';
		$value = '';
		if(!empty($data)){
			foreach($data as $k=>$v){
                $k = $this->sqlVal($k);
				$v = $this->sqlStr($v);
				$field .= $k.',';
				$value .= $v.",";
			}
			$field = rtrim($field, ',');
			$value = rtrim($value, ',');
			if($field && $value){
				$sql = "insert into $table ($field) values ($value)";
                $this->deBugLog($sql);
				return $this->query($sql,2);
			}
		}
		return false;
	}
	
	/**
	 * @fun   修改
	 * @desc  
	 */
	function update($params){
		$table = $this->setTable($params);
		$data  = $this->setData($params);
		$where = $this->whereTo($params);
		$set = '';
		if(!empty($data)){
			foreach($data as $k=>$v){
                $k = $this->sqlVal($k);
				$v = $this->sqlStr($v);
				$set .= "".$k."=".$v.",";
			}
			//$set = rtrim($set, ',');
			$set = substr($set, 0, -1);
			
			if($where){
				$sql = "update $table set $set where 1=1 $where";
                $this->deBugLog($sql);
				return $this->query($sql,2);
			}
		}
		return false;
	}
	
	/**
	 * @fun   删除
	 * @desc  条件必须
	 */
    function del($params){
		$table = $this->setTable($params);
		$w = $this->whereTo($params);
		if($w){
            $sql = "delete from $table where 1=1 $w";
            $this->deBugLog($sql);
			return $this->query($sql,2);
		}
		return false;
    }
	
	/**
	 * @fun   返回作用域主键
	 * @desc  
	 */
	function returnId(){
		$sql = 'SELECT @@IDENTITY id;';
		$ids = $this->query($sql,1, true);
		return isset($ids['id']) ? intval($ids['id']) : 0;
	}
	
	/**
	 * @fun   自定义SQL执行
	 * @desc  
	 */
    function query($sql, $type=2, $single = false){
        //if(preg_match("/select/i",$sql)){
        if($type == 1){
			$this->_result = mysqli_query($this->_dbCon, $sql);
			$result = array();
			$table = array();
			$field = array();
			$tempResults = array();
			if($this->_result){
				$numOfFields = mysqli_field_count($this->_dbCon);
				for($i = 0; $i < $numOfFields; ++$i) { array_push($table,mysqli_fetch_field_direct($this->_result, $i));
					array_push($field,mysqli_fetch_field_direct($this->_result, $i));
				}
				while(@$row = mysqli_fetch_row($this->_result)){
					for($i = 0;$i < $numOfFields; ++$i){
						
						$table[$i]->table = ucfirst($table[$i]->table); 
						//$tempResults[$table[$i]->table][$field[$i]->name] = $row[$i]; 
						$tempResults[$field[$i]->name] = $row[$i]; 
					} 
					if($single){
						@mysqli_free_result($this->_result);
						return $tempResults;
					}
					array_push($result,$tempResults);
				}
				// 从结果集中取得行，然后释放结果内存
				@mysqli_free_result($this->_result);
				//$this->dbclose();
				return($result);
			}else{
				exit('Error: '.$this->getError());
			}
        }else{
			$this->_result = $this->_dbCon->multi_query($sql);
			if($this->_result){
				return true;
			}else{
				exit('Error: '.$this->getError());
			}
		}
    }
	
	/**
	 * @fun   sql执行后受影响的行数
	 * @desc
	 */
	function rowCount(){
		return $this->_dbCon->affected_rows;
	}
	
	/**
	 * @fun   转义字符串
	 * @desc  不带引号
	 */
	function sqlVal($v){
		$str = @mysqli_real_escape_string($this->_dbCon,$v);
        return $str;
	}
 
	/**
	 * @fun   转义字符串
	 * @desc  带默认加引号
	 */
	function sqlStr($v){
		//$str = @mysqli_real_escape_string($this->_dbCon,$v);
        //return "'".$str."'";
        $str = htmlspecialchars($v);
        $str = htmlspecialchars_decode($str);
        return "'".$str."'";
	}
	
	/**
	 * @fun   获取错误信息
	 * @desc  sql语句执行失败
	 */
    function getError(){
        $errTxt = mysqli_connect_errno($this->_dbCon).' : '.mysqli_error($this->_dbCon);
		$this->dbclose();
		return $errTxt;
    }
	
	/**
	 * @fun   从数据库断开
	 * @desc  
	 */
    function dbclose(){
        @mysqli_close($this->_dbCon);
    }
}