<?php
	
	header("content-Type: text/html; charset=utf-8");
	session_start();	
    if($_POST['username']&&$_POST['password']){
    	$username=trim($_POST['username']);
        $password=trim($_POST['password']);
        $url=$_GET['url'];
        if(login($username,$password)){
            $url_return=rawurldecode($url);
            //echo "<script>alert('');</script>";
            $_SESSION['user']='dengqiuhua';
            header("location:$url_return");
        }else{
            $url_error='/login.php?status=fail&u='.$username.'&url='.$url;
        	header("location:$url_error");
        }
        
    }

	//登入
    function login($username,$password){
        if($username=='dengqiuhua' && md5($password)=='e10adc3949ba59abbe56e057f20f883e'){
        	return true;
        }
        return false;
    }

	//退出
	if(isset($_GET['act'])&&$_GET['act']=='loginout'){
        unset($_SESSION['user']);
        header("location:login.php");
    }
?>

<!DOCTYPE html>
<html>
<head>
    
    <meta charset="utf-8">
    
    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
Remove this if you use the .htaccess -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <title>登录-duty</title>
    <meta name="description" content="">
    <meta name="author" content="邓秋华">
    
    <meta name="viewport" content="width=device-width; initial-scale=1.0">
    
    <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
    <link rel="shortcut icon" href="style/images/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="stylesheet" href="style/css/bootstrap.css" />
    <script type="text/javascript" src="style/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="style/js/bootstrap.js"></script>

</head>
<body>
<div id="container" class="container-fluid">
 	<?php 
        $nav=6;
        require("header.php"); 
      ?>       
    
    <div class="row-fluid" style="min-height:500px; margin-top: 5%; margin-left: 30%;margin-right: 30%;">
        <div class="span5 well">
            <div id="index_form">
                <div class="page-header">
                    <h3>登入</h3>
                </div>
                <form class="form-horizontal" action="" method="post" onsubmit="return validateLoginForm();">
                    <div class="control-group">
                        <label class="control-label" for="inputEmail">用户名</label>
                        <div class="controls">
                            <input type="text" name="username" id="inputEmail" style="height:29px;"
                            value="<?=$_GET&&isset($_GET['u'])?$_GET['u']:'';?>" placeholder="用户名">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputPassword">密码</label>
                        <div class="controls">
                            <input type="password" name="password" id="inputPassword" style="height:29px;" placeholder="密码" value="">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="btn btn-info">登录</button>
                        </div>
                    </div>
                    <?php
                		if(isset($_GET['status']) && $_GET['status']=='fail'){
                    ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>用户名或密码错误!</strong>
                        </div>
                    <?php  
                        }    
                    ?>

                </form>
                <script type="text/javascript">
                    //登入验证
                    var validateLoginForm = function () {
                        if ($.trim($("input[name=username]").val()) == "") {
                            $("input[name=username]").focus().css("border", "1px solid red");
                            return false;
                        }
                        if ($.trim($("input[name=password]").val()) == "") {
                            $("input[name=password]").focus().css("border", "1px solid red");
                            return false;
                        }
                        return true;
                    };

                    $(function () {
                        $("input[name=username],input[name=password]").on("keyup", function () {
                            $(this).css("border", "1px solid #ccc");
                        });
                    });

                </script>
            </div>
        </div>
    </div>
    <!--/row-->

</div>

</body>
</html>