<?php
	header("content-Type: text/html; charset=utf-8");
	require("config.php");


	//上传文件
	function upload_data(){
		$msg="";
		$filename=$_FILES["file"]["name"];//文件名
		//echo get_extension($filename);exit;
		if (get_extension($filename) == "csv"){
		  	if ($_FILES["file"]["error"] > 0){
		    	$msg = "上传错误: " . $_FILES["file"]["error"] ;
		    } else {
			    if (file_exists("data/" . $_FILES["file"]["name"])){
			      $msg = $_FILES["file"]["name"] . " 已经存在，请查看data文件下是否存在您的数据文件. ";
			    }else{
			      move_uploaded_file($_FILES["file"]["tmp_name"], "data/" . $_FILES["file"]["name"]);
			      if (file_exists("data/" . $_FILES["file"]["name"])) {
			      	$msg = "上传成功！文件已经保存在了 " . "data/" . $_FILES["file"]["name"]."接下来您可以导入本次打卡记录了。";
			      }else{
			      	$msg = "上传失败，因为目录权限被拒绝，请手动上传。";
			      }
			      
		        }
		    }
		}else{
	  		$msg = "文件格式错误";
	  	}
	  	return $msg;
	}

	//文件扩展名
	function get_extension($filename){
		return substr(strrchr($filename, '.'), 1);
	} 
	
	//*******************************************************
	
	if(isset($_REQUEST['act'])){
		$act=$_REQUEST['act'];
		if($act=='upload'){
			//导入数据
			$res=upload_data($db);
			//$js= "<script>document.getElementById('res_upload').innerHTML='".$res."';</script>";
			$js= "<script>alert('".$res."');window.history.go(-1);</script>";
			echo $js;
			
		}
	}
	//--------------------------------
	@mysql_close($db);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title>数据-duty</title>
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
		<div>
			
			<?php 
				$nav=5;
				require("header.php"); 
            ?>  
		    
		    <div class="container">
		    	<div class="row">
		    		<div class="span8">
						<section>
							<div class="well">
								<p>仅限*.csv的文件，数据格式如下</p>
								<p>
									<table class="table-bordered" style="width:400px;">
										<tr>
											<th>部门</th>
											<th>姓名</th>
											<th>工号</th>
											<th>打卡时间</th>
											<th>sli</th>
										</tr>
									</table>
								</p>
								<p id="res_upload"></p>
							</div>
						</section>
						
						<section id="data_all">
							<div class="well">
		    			    	
		    			    	<form class="form-horizontal" action="?act=upload" method="post" enctype="multipart/form-data">
								  <div class="control-group">
								    <label class="control-label" for="filename">数据文件</label>
								    <div class="controls">
								      <input type="file" name="file" id="filename" placeholder="仅限*.csv的文件"/>
								    </div>
								  </div>
								 
								  <div class="control-group">
								    <div class="controls">
								      
								      <button type="submit" id="btn_import" class="btn btn-success">上传</button>&nbsp;
								      
								    </div>
								  </div>
								</form>
								
						    </div>
						</section>
					</div>
					<div class="span4">
						
					</div>
				</div>
			</div>
			
			<footer style="text-align: center;">
				<p>
					&copy; Copyright  by monica
				</p>
			</footer>
		</div>
		
	</body>
</html>
		