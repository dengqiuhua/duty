<?php
	header("content-Type: text/html; charset=utf-8");
	require("config.php");
	
	function data($db){
        $where="";
        if (isset($_REQUEST['number'])) {
        	$number=$_REQUEST['number'];
        	$where.=" and number='$number' ";
        }
        if (isset($_REQUEST['name'])) {
        	$name=$_REQUEST['name'];
        	$where.=" and name like '%$name%' ";
        }
		$sql="SELECT * FROM duty_user WHERE 1=1 $where order by department asc";	
		$query = $db->query($sql);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	//当前用户数
	function get_user($db){		
		$sql="SELECT count(*) FROM duty_user";
		$res=$db->query($sql);
		return $res->fetchColumn();
	}
	
	//所有用户数
	$count_user=get_user($db);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title>用户-duty</title>
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
		    			<div class="row">
				    		<div class="well">
				    			<table style="width:100%;">
									<tr>
										<th>用户数量</th>
										<td class="td_user_count"><?=$count_user;?></td>
										
										<th>月份</th>
										<td><?=$month;?></td>
									</tr>
								</table>
				    		</div>
				    	</div>
						<div class="well">
						<section id="data_all">
							
							<table class="table table-striped table-hover" style="width:100%;" cellspacing="0">
								<tr>
									<th>序号</th>
									<th>部门</th>
									<th>姓名</th>
									<th>编号</th>
									
								</tr>
								<?php
								
                                 //所有用户
                                $data=data($db);                                    
                               
								@mysql_close($db);
								if ($data) {
									$row=0;
									foreach($data as $key=>$value){
                                        $row++;
								?>
								<tr>
									<td><?=$row;?></td>	
									<td><?=$value['department'];?></td>
									<td><a title="<?=$value['name'];?>" href="detail.php?number=<?=$value['number'];?>" target="_blank">
										<?=$value['name'];?></a></td>
									<td><?=$value['number'];?></td>
									
								</tr>
								<?php
									}
								}else{
									if ($count_user&&$count_user>0) {
										echo "<tr><td colspan='4'>无用户，请筛选查询条件或者<a href='user.php'>显示全部用户</a></td></tr>";
									}else{
										echo "<tr><td colspan='4'>无用户，请<a href='data.php'>导入数据</a></td></tr>";
									}
									
									}
								?>
							</table>
						</section>
						</div>
					</div>
					<div class="span4">
						
						<div>
							<form class="form-horizontal" action="?act=chk" method="get" onsubmit="return validate();">		
							<div>
									  
								<label class="" for="number">编号</label>
								<input type="text" name="number" id="number" value="<?=$_REQUEST && $_REQUEST['number']?$_REQUEST['number']:'';?>" placeholder="编号">
								
								<label class="" for="name">姓名</label>
								<input type="text" name="name" id="name" value="<?=$_REQUEST && $_REQUEST['name']?$_REQUEST['name']:'';?>" placeholder="姓名">
								
							</div>
							  
							  <div class="control-group">
								<div class="controls">
								  
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
