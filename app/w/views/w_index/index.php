<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>111</title>
    <meta name="keywords" content="222">
    <meta name="description" content="333">
    <!-- 设置缩放 -->
    <meta name="viewport" content="minimal-ui,width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <!-- IOS中禁用将数字识别为电话号码/忽略Android平台中对邮箱地址的识别 -->
    <meta name="format-detection" content="telephone=no">
    <!-- windows phone 点击无高光 -->
    <meta name="msapplication-tap-highlight" content="no">
    <script>

    </script>
    <style>
        body{margin:0;padding:0;background:#ccc;width:100%;height:100%;text-align:center;}
        .content{width:200px;height:230px;border:1px solid #999;background:#fff;position:fixed;top:50%;left:50%;margin:-115px 0 0 -100px;}
        .title{border-bottom:1px solid #ccc;line-height: 30px;}
        .qrcode{width:180px;height:180px;margin:10px;}
    </style>
</head>
<body>
<div class="content">
    <div class="title"><?php echo $title;?></div>
    <div class="qrcode"></div>
</div>
<?php
$r = runCosts();
print_r($r);
?>
<script type="text/javascript">

</script>
</body>
</html>