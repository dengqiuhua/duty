<?php
	header("content-Type: text/html; charset=utf-8");
	require("config.php");
	
	function data($db,$date,$number=''){
        if($date){        
            $time_s=strtotime("$date 00:00:00");
            $time_e=strtotime("$date 23:59:59");
            
            $where=" and deadline>$time_s AND deadline<$time_e AND number='$number'" ;
        }else{
            //个人全部
        	$where=" AND number='$number'" ;
        }
		$sql="SELECT * FROM duty WHERE 1=1 $where order by deadline asc";	
		$query = $db->query($sql);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	//当前用户
	function get_username($db,$number){		
		$sql="SELECT name FROM duty_user WHERE number='$number' LIMIT 1;";
		$res=$db->query($sql);
		return $res->fetchColumn();
	}
	//--------------------------------------------------
	//工号
	$num=$_REQUEST['number'];
	//姓名：
	$username=get_username($db,$num);

	//week
	$strweek = array('日' , '一', '二', '三', '四', '五', '六' );
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?=$username;?>-duty</title>
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
		    		<div class="well">
		    			<div>
							<div class="pagination">
								<ul>
								<?php
								//当前日期
								$page=$_REQUEST && isset($_REQUEST['p'])?$_REQUEST['p']:0;

								$strPage="<li class='".($page==0?'active':'')."'><a href='?p=0&number=$num'>全部</a></li>";
								//分页
								$strPage.="<li><a href='?p=".($page-1)."&number=$num'><<</a></li>";								
								for ($i=1; $i < 31; $i++) {
									 $class=$i==$page?'active':'';
									$strPage.="<li class='$class'><a href='?p=$i&number=$num'>$i</a></li>";
								}
								$strPage.="<li><a href='?p=".($page+1)."&number=$num'>>></a></li>";
								echo $strPage;
								?>
								</ul>
							</div>
						</div>
		    		</div>
		    	</div>
		    	<div class="row">
		    		<div class="span8">
						<div class="well">
						<section id="data_all">
							
							<table class="table table-striped table-hover" style="width:100%;" cellspacing="0">
								<tr>
									<th>部门</th>
									<th>姓名</th>
									<th>编号</th>
									<th>打卡时间</th>
									<th>星期</th>
								</tr>
								<?php
								//当天
                                if($page&&$page!=0){
                                    $current_day="$year-$month-$page";
                                    $current_week=date('w',strtotime($current_day));//当周
                                    //当天打卡记录
                                    $data=data($db,$current_day,$num);
                                }else{
                                    //当天打卡记录
                                	$data=data($db,"",$num);  
                                	$current_day = "本月";  
                                	$current_week= "";                                
                                }								
								
								@mysql_close($db);
								if ($data) {
									foreach($data as $key=>$value){
                                        $strDate=date('Y-m-d H:i:s',$value['deadline']);
                                        $current_week=date('w',strtotime($strDate));//当周
								?>
								<tr>
									<td><?=$value['department'];?></td>
									<td><?=$value['name'];?></a></td>
									<td><?=$value['number'];?></td>
									<td><?=$strDate;?></td>					
									<td><?=$strweek[$current_week];?></td>
								</tr>
								<?php
									}
								}else{
									echo "<tr><td colspan='5'>无记录</td></tr>";
									}
								?>
							</table>
						</section>
						</div>
					</div>
					<div class="span4">
						<div class="well">
							<p>
							姓名：<?=$username;?>
							</p>
							<p>
							日期：<?=$current_day;?>
							</p>
							<p>
							星期：<?=$current_week==0?7:$current_week;;?>
							</p>
							<p>
								<a href="user.php">查看其员工</a>
							</p>
						</div>
						<div>
							<form class="form-horizontal" action="?act=chk" method="get" onsubmit="return validate();">					  
								<label class="" for="number">编号</label>
								<input type="text" name="number" id="number" value="<?=$_REQUEST && $_REQUEST['number']?$_REQUEST['number']:'';?>" placeholder="number">
							  
							  <div class="control-group">
								<div class="controls">
								  <input type="hidden" name="p" value="<?=$page; ?>"/>
								  <button type="submit" class="btn">查询</button>&nbsp;
								  
								</div>
							  </div>
							</form>
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
		
	</body>
</html>
