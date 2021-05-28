<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta name="robots" content="noindex,nofollow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title>verify Code</title>

    <link rel="stylesheet" type="text/css" href="./css/verify.css">
    <style>
        html,body {width: 100%;height: 100%;margin: 0;padding: 0;background:#eee;}
        body {display: flex;align-items: center;justify-content: center;min-height:400px;}
        .verifyBody{border:1px solid #ccc;width:320px;padding:20px;margin:auto;overflow:hidden;background:#fff;}
        .verifyBox{}
    </style>
</head>
<?php
	if(!isset($_REQUEST['token']) || !$_REQUEST['token']){
		exit('Error：Access terminated');
	}
	$token = $_REQUEST['token'];
	$httpType = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
	$goToUrl = isset($_REQUEST['url']) ? $_REQUEST['url'] : $httpType.$_SERVER['HTTP_HOST'];
	
	function randFun(){
		return rand(1,5);
	}
	$rand = randFun();
	$rand = $rand == 3 ? 5 : $rand;
?>
<body>
<div class="verifyBody">
	
	
	<?php if($rand == 1){ ?>
	<!-- 字母 -->
		<div class="verifyBox">
			<div id="verifyBox1" ></div>
			<button type="button" id="check-btn" class="verify-btn">确定</button>
		</div>
	<?php } ?>
	
	<?php if($rand == 2){ ?>
	<!-- 计算 -->
		<div class="verifyBox">
			<div id="verifyBox2"></div>
			<button type="button" id="check-btn2" class="verify-btn">确定</button>
		</div>
	<?php } ?>
	
	<?php if($rand == 3){ ?>
	<!-- 滑块 -->
		<div class="verifyBox">
			<div id="verifyBox3" ></div>
		</div>
	<?php } ?>
	
	<?php if($rand == 4){ ?>
	<!-- 图片滑块 -->
		<div class="verifyBox">
			<div id="verifyBox4"></div>
		</div>
	<?php } ?>
	
	
	<?php if($rand == 5){ ?>
	<!-- 点选 -->
		<div class="verifyBox">
			<div id="verifyBox5"></div>
		</div>
	<?php } ?>
	
	
</div>

<script type="text/javascript" src="./js/jquery.min.js"></script>
<script type="text/javascript" src="./js/verify.js" ></script>
<script type="text/javascript">
	var token = '<?php echo $token;?>';
	var goToUrl = '<?php echo $goToUrl;?>';
	
	// 数据提交
	function submitData(){
		$.post('safeban.php',{flag:'verifyCode',token:token},function(result){
			console.log(result);
			if(result == 'Y'){
				window.location.href = goToUrl;
			}else{
				alert('验证错误');
				window.location.reload();
			}
		},'json').error(function(){
			alert('网络异常');
			window.location.reload();
		})
	}
	
	// 验证成功
	function successTips(){
		submitData();
	}
	
	// 验证失败
	function errorTips(flag){
		if(flag == 1){
			alert('验证码不匹配！');
		}else{
			alert('验证失败！');
		}
		window.location.reload();
	}
	
	
	
	<?php if($rand == 1){ ?>
	// 字母
	$('#verifyBox1').codeVerify({
		type : 1,
		width : '318px',
		height : '50px',
		fontSize : '30px',
		codeLength : 6,
		btnId : 'check-btn',
		ready : function() {
		},
		success : function() {
			successTips()
		},
		error : function() {
			errorTips(1);
		}
	});
	<?php } ?>
	
	<?php if($rand == 2){ ?>
	// 计算
	$('#verifyBox2').codeVerify({
		type : 2,
		figure : 10,	//位数，仅在type=2时生效
		arith : 0,	//算法，支持加减乘，不填为随机，仅在type=2时生效
		width : '318px',
		height : '50px',
		fontSize : '30px',
		btnId : 'check-btn2',
		ready : function() {
		},
		success : function() {
			successTips()
		},
		error : function() {
			errorTips(1);
		}
	});
	<?php } ?>
	
	<?php if($rand == 3){ ?>
	//滑块
	$('#verifyBox3').slideVerify({
		type : 1,		//类型
		vOffset : 5,	//误差量，根据需求自行调整
		barSize : {
			width : '316px',
			height : '40px',
		},
		ready : function() {
		},
		success : function() {
			successTips()
		},
		error : function() {
			errorTips(2);
		}
	});
	<?php } ?>
	
	<?php if($rand == 4){ ?>
	// 图片滑块
	$('#verifyBox4').slideVerify({
		type : 2,		//类型
		vOffset : 5,	//误差量，根据需求自行调整
		vSpace : 5,	//间隔
		imgName : ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg','6.jpg','7.jpg','8.jpg'],
		imgSize : {
			width: '320px',
			height: '200px',
		},
		blockSize : {
			width: '40px',
			height: '40px',
		},
		barSize : {
			width : '320px',
			height : '40px',
		},
		ready : function() {
		},
		success : function() {
			successTips()
		},
		error : function() {
			errorTips(2);
		}
		
	});
	<?php } ?>
	
	<?php if($rand == 5){ ?>
	// 点选
	$('#verifyBox5').pointsVerify({
		defaultNum : 4,	//默认的文字数量
		checkNum : 2,	//校对的文字数量
		vSpace : 5,	//间隔
		imgName : ['1.jpg','2.jpg','3.jpg','4.jpg','5.jpg','6.jpg','7.jpg','8.jpg'],
		imgSize : {
			width: '320px',
			height: '200px',
		},
		barSize : {
			width : '320px',
			height : '40px',
		},
		ready : function() {
		},
		success : function() {
			successTips()
		},
		error : function() {
			errorTips(2);
		}
		
	});
	<?php } ?>
	
</script>
</body>

</html>