# ZHLPHP 
## 简介 
> 自开发私有PHP MVC 框架：ZHLPHP  
> 小、精、简，低耦合、高内聚，高扩展，几乎零配置。门槛低，无需学习沉重的框架语法，只需要了解框架目录结构以及了解php语法会写if else和数组foreach遍历即可使用该框架。  

### 运行条件 
> PHP+MYSQL/ORACLE  
> * php5.3以上版本
> * mysql5以上版本
> * php运行环境（如Apache）

### 升级日志 
#### v2.3.1 - 2022-04-04 
> * 优化多数据库支持功能  
> * URL参数解析优化  
> * 辅助文件loger函数优化  
> * view文件映射功能优化，新增模版内引入其他模版 
#### v2.3.0 - 2022-03-17  
> * index入口文件优化  
> * Base基础文件优化  
> * Core核心类优化  
> * 支持核心架构组件扩展入口  
> * 添加核心架构配置 
> * 优化系统函数重构辅助函数和数据库类设计模式  
> * 添加多数据库支持  
> * 优化基类Controller,Model,View  
> * 移除loger.php文件整合loger方法
#### v2.2 - 2021-05-28 
> * 整体代码修改构建以及性能优化
> * 支持核心组件扩展   
### 版权信息  
> zhl个人版权所有,只能用于学习与研究不准进行商业性活动。 
---  
## 使用文档
### 一、目录结构  
```
app  ------------------------------------ 应用目录(框架核心配置文件可以配置和修改当前文件夹名)
    a  ---------------------------------- [自定义]功能模块目录（比如当前为后台）(必须包含三个文件夹:controllers、models、views)
    w  ---------------------------------- [自定义]功能模块目录（比如当前为前台）
        controllers  -------------------- 控制器目录
            W_IndexController.php  ------ [自定义]控制器文件（主要接收数据控制流程）
        models  ------------------------- 模型目录
            W_CommModel.php  ------------ [自定义]模型文件（主要数据交互）
        serves  ------------------------- 服务层目录
            W_CommServe.php  ------------ [自定义]服务文件（主要业务逻辑）
        views  -------------------------- 视图目录
            w_index  -------------------- [自定义]视图目录（一般根据控制器命名，可自定义）
                common.phtml  ----------- w_index目录下公共父级模版视图,会覆盖上级目录common.phtml（控制器displs方法才起作用）
                index.phtml  ------------ [自定义]视图文件
            common.phtml  --------------- 所有控制器父级模版视图（控制器displs方法才起作用）
config  --------------------------------- 配置文件目录
    config.php  ------------------------- 全局变量参数配置文件
    database.php  ----------------------- 数据库配置文件
extend  --------------------------------- 第三方扩展类库（第三方类库存放目录）
    function.php  ----------------------- 用户自定义方法文件（可有可无非必须）
public  --------------------------------- 静态资源存放目录（比如css,js,image等）
runtime  -------------------------------- 系统缓存日志等目录
    error  ------------------------------ 系统bug日志存放目录
    logs  ------------------------------- 程序用户打印日志存放目录, 调用系统辅助函数loger($s)打印的日志
zhlphp  --------------------------------- 框架核心文件目录
.htaccess  ------------------------------ 分布式配置文件
404.html  ------------------------------- 404页面
index.php  ------------------------------ 应用程序框架入口文件
README.md  ------------------------------ 说明文档
```
### 二、安装  
>下载并解压到根目录下即可。
### 三、运行     
#### 1、框架运行流程      
>* index.php -> /zhlphp/Base.php -> /zhlphp/core/Core.php -> {控制器C} -> {服务层S} -> {模型M} -> {视图V}
#### 2、URL结构组成和解析        
>* {网站根域名}/web_about_index/cid/1/id/2.html   
>  web:为功能模块(框架配置默认模块时可以为空)，  
>  about:为控制器(index时可以为空)，  
>  index:为方法名(index时可以为空)，  
>  cid/id:为参数，1和2:分别为cid/id对应的值。   
>  
#### 3、配置文件 /config/
> (1)、config.php：可以配置系统全局变量  
> (3)、database.php：配置数据库链接参数
参数名和值必须全部小写：
>```
>'mysql1'=>array(    ------------------------- 数据库昵称(自定义)
>     'db_type' => 'mysql',    --------------- 数据库类型(mysql/oracle)
>     'db_link' => 'mysqli',    -------------- 链接方式(mysqli/pdo)
>     'db_host' => '127.0.0.1',    ----------- 链接地址
>     'db_port' => 3306,    ------------------ 链接端口号
>     'db_name' => 'zhl_cms2',    ------------ 数据库名
>     'db_user' => 'root',    ---------------- 链接帐号
>     'db_pwd' => '123456',    --------------- 链接密码
>     'db_fix' => 'zhl_',    ----------------- 表前缀
>     'db_char' => 'utf8',    ---------------- 链接编码
> ),
>``` 
  
#### 4、功能模块    
> app目录下创建个功能模块目录（自定义）类似上文w、a文件夹，比如起个名字为web，在创建的功能模块下新建4个文件夹分别为：controllers、 serves、 models、views，功能模块创建成功。
#### 5、控制器  Controller 
> (1)、新建控制器：  
>* 在新建的功能模块下“controllers”目录下新建个控制器，文件命名规则：｛必须首字母大写功能模块文件名｝+｛下划线｝+｛首字母大写自定义控制器名｝+｛Controller.php｝，比如“Web_AboutController.php”。  

> (2)、参数接收：  
>* $id = $this->fun->input($name, $val, $type, $leng, $start);  
>* $name：参数key；
>* $val：没有接收到值定义的默认值；
>* $type：参数类型，默认s(d:数字,b:布尔型,f:小数,h:html标签,s:字符串)；
>* $leng：字符串截取长度,$type为小数时代表小数点后几位；
>* $start：截取字符串时开始位置；
>* $this->fun->input(); // 不知名参数名接收所有参数整合为数组  

>(3)、实例化调取服务层、数据层业务逻辑和数据：
>* 示例1(实例化服务层)：  
>```
>// 实例化功能模块“w”下服务器层“W_CommServe”类  
>$commSer = new W_CommServe();  
>// 调去方法  
>$list = $commSer->getClassLists($id);
>```
>* 示例2(实例化模型)： 
>```
>// 实例化功能模块“w”下数据层“W_CommModel”类  
>$commM = new W_CommModel();  
>// 调去方法  
>$list = $commM->getClassLists($id);
>```

>(4)、映射模版：
>* $this->display();  
>  默认映射模版：{当前功能模块}/views/{当前控制器名}/{当前方法}.phtml；比如当前功能模块为“web”，控制为“Web_AboutController” ，方法为 "index()", 映射的模版位置为：web/views/web_about/index.phtml   
>* $this->display('w_index/index');  
>  指定映射模版：{当前功能模块}/views/w_index/index.phtml；比如当前功能模块为“web”，映射的模版位置为：web//views/w_index/index.phtml；不允许跨功能模块；只允许夸控制器和方法。    
>* $this->displays();  
>  默认映射模版,但是会以“/views/common.phtml”为父级模版(父级模版内容也会展示),比如：web/views/web_about/common.phtml 会重置覆盖：web/views/common.phtml 的模版内容。  
>* $this->displays('w_index/index');  
>  同样可以指定模版，但是同时也会加载父级公共模版：common.phtml。

>(5)、控制器变量参数映射到模版：
>* $this->set('titleName', $title);  
>  把变量“$title”以“titleName”命名映射到模版，模版里接收为：$titleName，支持数组等。  

>(6)、控制器不映射模版，直接输出类似接口json数据：  
>* $this->fun->json($list, true);  // $list转换成json并直接输出json；  
>* $this->fun->json($list,);  // $list转换成json但不输出；  

#### 6、服务层 Serve（业务逻辑层，主要在这里写业务逻辑）  
> (1)、新建服务层：  
>* 在新建的功能模块下“serves”目录下新建个业务服务，文件命名规则：｛必须首字母大写功能模块文件名｝+｛下划线｝+｛首字母大写自定义服务业务名｝+｛Serve.php｝，比如“Web_AboutServe.php”。  

> (2)、业务逻辑：实例化其他服务层类和数据层Model类。  

#### 7、模型 Model（主要在这里操作数据库）  
> (1)、新建模型：  
>* 在新建的功能模块下“models”目录下新建个模型，文件命名规则：｛必须首字母大写功能模块文件名｝+｛下划线｝+｛首字母大写自定义模型名｝+｛Model.php｝，比如“Web_AboutModel.php”。  

> (2)、数据库配置(多数据库支持)：
>* 数据库链接参数以配置文件在 /config/database.php ，详细见篇章【8、配置文件 /config/】。  

> (3)、mysqli链接方式：  
>* 封装了操作数据库类一系列方法，详细参考：DbMySqli 类（嫌繁琐可以跳过此处，使用PDO方式）。  
> mysqli链接方式示例：
>```
>// 不带表前缀的表名
>$table = 'member'; 
>// 数组key对应数据库字段，value对应字段的值
>$data = array(
>   'uname'=>'小明',
>   'qq'=>'123456789'
>);
>
>// 插入
>function addData($table, $data){
>   $sql['data'] = $data;
>   $sql['table'] = $table;
>   $this->add($sql); // sql执行是否成功
>   $this->rowCount(); // 受影响的行数
>   $this->returnId(); // 新插入数据的主键ID值
>}
>
>// 修改
>function updateClass($data, $id){
>   $sql['table'] = 'classify';
>   $sql['data'] = $data;
>   $sql['where'][id] = $id;
>   $this->update($sql); // sql执行是否成功
>   return $this->rowCount(); // 返回受影响的行数
>}
>
>// 删除
>function delClass($id){
>   $sql['table'] = 'classify';
>   $sql['where']['id'] = $id;
>   $this->del($sql);
>   return $this->rowCount(); // 返回受影响的行数
>}
>
>// 查询单条
>function getClassInfo($id){
>   $params['table'] = 'classify';
>   $params['field'] = '*';  // 可以指定字段如:“id,classname”等
>   $params['where']['id'] = $id;
>   return $this->find($params);
>}
>
>// 查询多条
>function getClassList($status){
>   $params['table'] = 'classify';
>   $params['field'] = '*';  // 可以指定字段如:“id,classname”等
>   $params['where']['status'] = $status;
>   return $this->select($params);
>}
>
>// 查询多条方法二
>function getClassList($status){
>   $params['table'] = 'classify';
>   $params['field'] = '*';  // 可以指定字段如:“id,classname”等
>   $params['where']['status'] = $status;
>   return $this->findAll($params);
>}
>
>// 表连接查询(leftjoin、rightjoin、innerjoin 等)
>function getClassList($navtype){
>   $sql['field'] = 'c.id,c.mouldcode,m.mouldname,c.classname';
>   $sql['table'] = 'classify';
>   $sql['as'] = 'c';
>   $sql['leftjoin']['moulds'] = 'm on c.mouldcode=m.mouldcode';
>   $sql['where']['navtype'] = $navtype;
>   $result = $this->select($sql);
>   return $result;
>}
>
>// 执行手写sql语句方式(不支持封装的where/order/limit/group)
>// 1、单条
>return $this->query($sql, 1, 1);
>// 2、多条
>return $this->query($sql, 1);
>// 3、添加/修改/删除
>return $this->query($sql, 2);
>
>// where条件
>// 1、指定字段
>$sql['where']['id'] = $id;
>$sql['where']['status'] = $statusCode;
>// 或者：
>$where = array('id'=>$id,'status'=>$statusCode);
>$sql['where'] = $where;
>// 2、where字符串(不推荐，必须使用时接收参数建议进行转译，比如：$id=$db->sqlStr($id);)
>$where = " id='$id' and status='$statusCode'";
>$sql['where'] = $where;
>// 3、区间查询
>// (1)大于
>$sql['gt']['id'] = $id;
>$sql['gt']['statusCode'] = $statusCode;
>// (2)大于等于
>$sql['gts']['id'] = $id;
>$sql['gts']['statusCode'] = $statusCode;
>// (3)小于
>$sql['lt']['id'] = $id;
>$sql['lt']['statusCode'] = $statusCode;
>// (4)小于等于
>$sql['lts']['id'] = $id;
>$sql['lts']['statusCode'] = $statusCode;
>// 4、模糊查询
>$sql['locate']['classname'] = $className;
>$sql['locate']['title'] = $title;
>
>// orderby排序
>$sql['order'] = 'a.viewseq desc,a.id desc';
>
>// limit指定条数
>$limit = array(2,10); // 第二条开始往后取10条
>$sql['limit'] = $limit;
>或者字符串：
>$sql['limit'] = '10'; // 取10条
>
>// groupby分组
>$sql['group'] = 'cid';
>```

> (4)、pdo链接方式：  
>* 详细参考PHP:PDO官方文档，以下列举常用到的：  
>```
>// 单条：
>$db = $this->conn('mysql2'); // config/database.php 配置的自定义数据库昵称
>$sql = 'select id,fid,classname,status from '.$db->db_fix.'_classify where id=:id';
>$stmt = $db->prepare($sql);
>$stmt->bindValue('id', $id);
>$stmt->execute();
>return $stmt->fetch(PDO::FETCH_ASSOC);
>// 多条fetch改成fetchAll：
>>return $stmt->fetchAll(PDO::FETCH_ASSOC);
>// 添加/修改/删除(只执行到$stmt->execute();可以跟下句响应行数)：
>>return $stmt->rowCount() > 0;
>// 获取刚插入的行数据主键ID
>$stmt->lastInsertId();
>```

#### 8、视图 View（页面模版文件）  
> (1)、新建视图：  
>* 在新建的功能模块下“views”目录下新建个视图，文件命名规则：｛小写功能模块文件名｝+｛下划线｝+｛自定义控制器名｝+｛.phtml｝，比如“web_about.phtml”。  

> (2)、获取控制器映射的值：  
>* 比如控制器中：“$this->set('titleName', $title);” 映射过来的值，在模版中输出：
>```
><?php
> echo $titleName;
>//如果是数组根据业务逻辑进行foreach遍历
>foreach($list as $v){
>   echo $v.'<br>';
>}
>?>
>```

> (3)、模版中加载其他模版：  
>```
><?php
>include SYS_VIEWS.'test.phtml';
>include SYS_VIEWS.'/w_index/test.phtml';
>?>
>```

### 四、扩展   
#### 1、自定义方法
> /extend/function.php 可以自定义方法。  

#### 2、扩展第三方类库
> 第三方类库直接放入 /extend/ 文件夹下，类库名和文件名必须要一致。
 
#### 3、全局常量
> (1)、固定常量：  
>* SYS_VERSION：系统版本  
>* SYS_PATH：项目根目录  
>* SYS_ROOT：核心文件目录  
>* HTTP_PATH：项目域名路径  
>* SYS_WEB_URL：当前访问的url  
>* SYS_PRO_PATH：当前功能模块  
>* SYS_START_MEMORY：系统初始内存  
>* SYS_START_TIME：系统运行初始时间  
>* SYS_VIEWS：模版绝对路径常量  
>* SYS_PRO_PATH：当前功能模块  

> (2)、可配置常量,核心目录下/helper/config.php (常量名字固定不可修改)：  
>* SYS_APP_PATH：应用路径   
>* SYS_APP_DEFAULT：程序指向默认模块  
>* SYS_DEBUG：调试开关(系统错误是否输出到页面)  
>* SYS_DEBUG_LOG：系统日志开关(系统错误是否打印日志)  
>* SYS_FLAG_LOG：程序日志开关(程序自定义日志开关)  
>* SYS_PAGE404：404页面开关  
>* SYS_URL_BOGUS：伪静态开关  
>* SYS_APP_URL_FIX：伪静态后缀初始值  
>* SYS_LOG_PATH：程序日志目录  
>* SYS_ERR_PATH：系统错误日志目录   

#### 4、系统辅助类 
> 核心目录下/helper/helper.php (可扩展)：  
>* cookiesCount($kname, $kcount)：客户端操作次数限制   
>* sys_encrypt($string, $key)：字符串-加密  
>* sys_decrypt($string, $key)：字符串-解密   
>* setSessions($name, $val)：设置sessions  
>* getSessions($name)：获取sessions  
>* delSessions($name)：删除sessions  
>* clearSessions()：清空所有session  
>* setCookies($name, $val, $expire=604800)：设置cookie  
>* getCookies($name)：获取cookie  
>* delCookies($name)：删除cookie  
>* runCosts()：运行开销统计  
>* redirect($url)：重定向
>* jump($txt='提示', $url='')：js提示跳转  
>* sysUrlFix($url)：由拼接(如：sysUrlFix('web_about_item/id/2')) 
>* sysloger($str,$errorFile='',$errorLine='0')：系统错误日志(runtime/error/)  
>* loger($str)：程序打印日志文件(runtime/logs/)  
>* loger_r($str)：程序日志输出到页面(美化print_r)  
>* loger_d($str)：程序日志输出到页面(美化var_dump)  

--- END ---
---
