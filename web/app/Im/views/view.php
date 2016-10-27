<!DOCTYPE html>
<html>
<head>
    <title>A test page</title>

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
        }

        .tip {
            color: pink;
        }
    </style>
    <script src="//cdn.bootcss.com/jquery/1.11.2/jquery.js"></script>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title">
            A test page
        </div>
    </div>
</div>
<div class="tip">
    <?php
    $url1 = $_app->pagePathing('view');
    $url2 = $_app->pagePathing('view',$_GET);
    $url3 = $_app->pagePathing('view',['A'=>1,'b'=>2,time()]);
    ?>
    <p><a href="<?php echo $url1?>" target="_BLANK">平台登录测试</a></p>
    <p><a href="<?php echo $url2?>" target="_BLANK">平台登录测试</a></p>
    <p><a href="<?php echo $url3?>" target="_BLANK">平台登录测试</a></p>

</div>
<?php echo \Ws\Debug\AsDebug::instance()->jqAjaxBind();?>
</body>
</html>
