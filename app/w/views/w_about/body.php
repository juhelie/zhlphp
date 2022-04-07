<h2>about_body这是测试页面H2</h2>
<?php
echo $title;
foreach($list as $v){
$title = $v['title'];
    echo <<<html
    <div>
$title
</div>
html;
}
?>