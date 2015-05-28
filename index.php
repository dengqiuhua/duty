<?php
	
	header("content-Type: text/html; charset=utf-8");
	require("config.php");
	

	//总记录数
	$count_total=0;
	$_SESSION['totalcounts']=0;
	
	//导入数据
	function import($db){
		$filename=$_REQUEST['filename'];
		$month=isset($_POST['month'])?intval($_POST['month']):date('m',time());
		//文件路径
		$filepath="data/$filename";
        //echo($filepath);exit;
		//读取文件数据
		$data=init_duty($filepath);

        //echo(count($data));exit;
		if($data){			
			//总数量
			$count_total=count($data);            
            $_SESSION['totalcounts']=$count_total;
			//把数据插入到库
			$res=add_duty($data,$month);
			return $res;
		}
		return 0;
	}//end fun

	//读取文件数据
	function init_duty($filepath){
		$data=array();
		if (file_exists($filepath)) {
			$file = fopen($filepath,"r");
			if($file){					
                //读取*.csv的数据
				while(! feof($file))
				  {
				  	$data[]=fgetcsv($file);			
				  }	
                try {  
                    //数组中文转码
                    //$data = eval('return '.iconv('gbk','utf-8',var_export($data,true)).';');  
                    $data = arrayiconv::Conversion($data,"gbk","utf-8");   
                 
                } catch (Exception $e) {   
                    echo $e->getMessage();   
                    exit();   
                } 
				
                //echo(count($data));exit;
				fclose($file);
			}
		}
        //var_dump($data);exit;
		return $data;
	}

	//插入
	function add_duty($data,$month){
		require("config.php");
		
		if($data){			
			//总数量
			$count_total=count($data);			
			set_time_limit(0);//数据库延时处理
			$rows=$row_success=$row_fail=0;//结果统计
			$sql="INSERT INTO duty (department,name,number,deadline,month) VALUES ";
			$sql_val="";
			$row=0;
			foreach ($data as $key => $value) {					
				if ($value) {
					$row++;
					$time=strtotime($value[3]);
				
					if ($sql_val) {
						$sql_val.=",('$value[0]','$value[1]','$value[2]','$time','$month')";	
					}else{
						$sql_val="('$value[0]','$value[1]','$value[2]','$time','$month')";	
					}
				}else{
					continue;
				}
				//每100条数据执行一次插入
				if ($row%100==0) {
					$sql_temp=$sql.$sql_val;
					$sql_val='';                   
					$res=$db->exec($sql_temp);
				}
							
			}
			$sql_temp=$sql.$sql_val;
			$res=$db->exec($sql_temp);
			return $row;
		}			
		return 0;
	}
	
	//导入用户
	function import_user($db){
		$sql="INSERT INTO duty_user(department,name,number) SELECT DISTINCT department,name,number FROM duty";
		$res= $db->exec($sql);	
		echo $res;
		exit;		
	}
	//删除用户
	function delete_user($db){	
		$sql="DELETE FROM duty_user";
		$res= $db->exec($sql);
		echo $res;
		exit;
	}
	//删除数据
	function delete_data($db){	
		$sql="DELETE FROM duty";
		$res= $db->exec($sql);
		echo $res;
		exit;
	}
	//当前用户数
	function get_user($db){		
		$sql="SELECT count(*) FROM duty_user";
		$res=$db->query($sql);
		return $res->fetchColumn();
	}
	//当前打卡记录数
	function get_data($db){		
		$sql="SELECT count(*) FROM duty";
		$res=$db->query($sql);
		return $res->fetchColumn();
	}

	//上传文件
	function upload_data(){
		$msg="";
		$filename=$_FILES["file"]["name"];//文件名
		if (get_extension($filename) == ".csv"){
		  	if ($_FILES["file"]["error"] > 0){
		    	$msg = "上传错误: " . $_FILES["file"]["error"] ;
		    } else {
			    if (file_exists("data/" . $_FILES["file"]["name"])){
			      $msg = $_FILES["file"]["name"] . " 已经存在，请查看是否是您的数据. ";
			    }else{
			      move_uploaded_file($_FILES["file"]["tmp_name"], "data/" . $_FILES["file"]["name"]);
			      $msg = "上传成功！文件已经保存在了 " . "data/" . $_FILES["file"]["name"];
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
		if($act=='import'){
			//导入数据
			$res_add=import($db);
			echo $res_add;
			exit;
		}elseif($act=='import_user'){
			//导入用户
			import_user($db);
		}elseif($act=='delete_user'){
			//删除用户
			delete_user($db);
		}elseif($act=='delete_data'){
			//删除数据			
			delete_data($db);		
		}elseif ($act=='getdutycounts') {
			//获取打卡记录数
			$counts=get_data($db);
            $count_total=$_SESSION['totalcounts'];
			$json="{'total':'$count_total','counts':'$counts'}";
			echo $json;
			exit;
		}
	}
	//-------------------------------------
	$count_user=get_user($db);//用户数量
	$count_data=get_data($db);//打卡记录数
	
	@mysql_close($db);

//数组格式转换
class arrayiconv   
{   
    static protected $in;   
    static protected $out;   
    /**  
      * 静态方法,该方法输入数组并返回数组  
      *  
      * @param unknown_type $array 输入的数组  
      * @param unknown_type $in 输入数组的编码  
      * @param unknown_type $out 返回数组的编码  
      * @return unknown 返回的数组  
      */  
    static public function Conversion($array,$in,$out)   
    {   
      self::$in=$in;   
      self::$out=$out;   
      return self::arraymyicov($array);   
    }   
    /**  
      * 内部方法,循环数组  
      *  
      * @param unknown_type $array  
      * @return unknown  
      */  
    static private function arraymyicov($array)   
    {   
      foreach ($array as $key=>$value)   
      {   
       $key=self::myiconv($key);   
       if (!is_array($value)) {   
        $value=self::myiconv($value);   
       }else {   
        $value=self::arraymyicov($value);   
       }   
       $temparray[$key]=$value;   
      }   
      return $temparray;   
    }   
    /**  
      * 替换数组编码  
      *  
      * @param unknown_type $str  
      * @return unknown  
      */  
    static private function myiconv($str)   
    {   
      return iconv(self::$in,self::$out,$str);   
    }   
}   


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
				$nav=1;
				require("header.php"); 
            ?> 
		    
		    <div class="container">
		    	<div class="row">
		    		<div class="span8">
						<section>
							<div class="well">
								<table style="width:100%;">
									<tr>
										<th>用户数量</th>
                                        <td class="td_user_count">
											<?=$count_user;?>
											<?=$count_user&&$count_user>0?"\t<a href='user.php'>查看所有用户</a>":""; ?>
										</td>
										<th>打卡记录数</th>
										<td><?=$count_data;?></td>
										<th>月份</th>
										<td><?=$month;?></td>
									</tr>
								</table>
							</div>
						</section>
						<section id="section-progress" class="hide">
							
								正在导入，请稍后。。。
							<div class="row-fluid">
								<div class="span10">
									<div id="div-progress" class="progress progress-striped active">
								    	<div class="bar" style="width: 60%;"></div>
								    </div>
								</div>
								<div id="div-progress-value" class="span2">
									<span class="text-success">0</span>/<span class="text-error">0</span>
								</div>
							</div>
						</section>
						<section id="data_all">
							<div class="well">
		    			    	
		    			    	<form class="form-horizontal">
								  <div class="control-group">
								    <label class="control-label" for="filename">数据文件名称</label>
								    <div class="controls">
								      <input type="text" name="filename" id="filename" placeholder="*.csv，如duty_9.csv">
								      <a href="upload.php">上传文件</a>
								    </div>
								  </div>
								  <div class="control-group">
								    <label class="control-label" for="month">月份</label>
								    <div class="controls">
								      <input type="text" name="month" id="month" placeholder="月份，限数字" onKeyUp="this.value=this.value.replace(/[^\.\d]/g,'');if(this.value.split('.').length>2){this.value=this.value.split('.')[0]+'.'+this.value.split('.')[1]}">
								    </div>
								  </div>
								  <div class="control-group">
								    <div class="controls">
								      
								      <button type="button" id="btn_import" class="btn">导入</button>&nbsp;
								      
								    </div>
								  </div>
								</form>
								<div class="control-group">
									<div class="controls">
										<button type="button" id="btn_import_user" class="btn btn-success">生成用户</button>&nbsp;
									</div>
								</div>
								
						    </div>
						</section>
					</div>
					<div class="span4">
						<div class="control-group">
							<div class="controls">
								<button type="button" id="btn_delete_data" class="btn btn-danger">删除打卡数据</button>&nbsp;
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<button type="button" id="btn_delete_user" class="btn btn-inverse">删除用户</button>&nbsp;
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<footer style="text-align: center;">
				<p>
					&copy; Copyright  by monica
				</p>
			</footer>
		</div>
		<script type="text/javascript">
			//表单验证
			var validate=function(){
				if(document.getElementById("filename").value==""){
					alert("文件名不能为空");
					return false;
				}				
				return true;
			}
			
			$(function(){
				//导入用户
				function import_user(){
					$.get('?act=import_user',function(msg){
						if(msg!=null&&msg>0){
							alert('导入用户成功。');
							setTimeout('window.location.reload();',1000);
						}else{
							alert('导入用户失败,请删除后，重新导入。');
						}
					})
				}


				//获取打卡记录数
				var lission="";
				function get_dutycounts(){
					$.get('?act=getdutycounts',{},function(msg){
						if (msg!=null&&msg!="") {
							msg=eval("("+msg+")");
							var total=msg.total;
							var counts=msg.counts;
							var percent=0;
							if (total!=0) {
								percent=counts/total*100;
                                $("#div-progress").css("width",percent+"%");
                                var html_value="<span class=\"text-success\">"+counts+"</span>/<span class=\"text-error\">"+total+"</span>";
                                $("#div-progress-value").html(html_value);
                                //显示进度
                                $("#section-progress").removeClass("hide");
                                //侦听进度
                                if (total!=counts) {
                                    lission=setTimeout(function(){get_dutycounts();},3000);//侦听进度
                                };
                                
							};								
							
						};
					});
				}

				//导入打卡记录
				function import_duty(){
					var filename=$("input[name=filename]").val();
					var month=$("input[name=month]").val();

					$.post("?act=import",{filename:filename,month:month},function(msg){
						if (msg!=null&&msg!=""&&msg>0) {
							alert("导入打卡记录成功！接下来您可以管理您的用户了。");
							$("#data_all input").val("");
							setTimeout('window.location.reload();',1700);

						}else{
							alert("导入打卡记录已结束，但未成功，请检查数据文件但路径是否正确或者文件但数据格式是否正确！");
							$("#section-progress").addClass("hide");
							return false;
						};
						clearTimeout(lission);
					});
				}
				
				//删除数据
				function delete_data(){
					$.get('?act=delete_data',function(msg){
						if(msg>0){
							alert('删除成功。');
							window.location.reload();
						}else{
							alert('删除失败。');
						}
					})
				}
				
				//删除用户
				function delete_user(){
					$.get('?act=delete_user',function(msg){
						if(msg>0){
							alert('删除成功。');
							window.location.reload();
						}else{
							alert('删除失败。');
						}
					})
				}

				//导入用户
				$("#btn_import_user").click(function(){
					var count=$("td.td_user_count").text();					
					var flag=true;
					if (count>0&&confirm('目前您已经存在用户，您是否需要先清除当前的用户？')){
						flag=false;
					}
					if(flag){
						import_user();//import
					}
				});
				
				//删除用户
				$("#btn_delete_user").click(function(){					
					if(confirm('您确定要清除所有的用户吗？')){
						delete_user();//delete
					}
				});
				
				//删除记录
				$("#btn_delete_data").click(function(){					
					if(confirm('您确定要清除所有的打卡数据吗？')){
						delete_data();//delete 
					}
				});

				//导入打卡记录
				$("#btn_import").click(function(){
					
					if(validate()){
						import_duty();//导入数据
						setTimeout(function(){get_dutycounts();},2000);//侦听进度
						
					}
				});
				
			})
		</script>
	</body>
</html>
		