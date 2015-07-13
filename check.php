<?php
	header("content-Type: text/html; charset=utf-8");
	require("config.php");
	
	//当天缺勤数据
	function data2($db,$date){
		$time_s=strtotime("$date 00:00:00");
		$time_e=strtotime("$date 23:59:59");

        //$where=" and deadline>$time_s AND deadline<$time_e" ;
        //$sql="SELECT * FROM `duty_user` where number not in(select distinct number from duty where 1=1 $where)";
        $where=" and deadline>$time_s AND deadline<$time_e" ;
        //$sql="SELECT * FROM `duty_user` LEFT JOIN duty ON duty_user.number=duty.number WHERE duty.number IS NULL $where;";
        $sql = "SELECT duty_user.* FROM `duty_user` left join (select distinct number from duty where 1=1 $where) as tb on tb.number =duty_user.number where tb.number is null;";
        //echo $sql;
		$query = $db->query($sql);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	//所有用户
	function data($db,$req){
        $where="";        
		$sql="SELECT * FROM duty_user WHERE 1=1 $where";	
		$query = $db->query($sql);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	function isWorkDay($req,$current_week,$day){
		//排除日期
		$trimdate=isset($req['trimdate'])?str_replace ('，',',',$req['trimdate']):'';
		//添加日期
		$adddate=isset($req['adddate'])?str_replace ('，',',',$req['adddate']):'';
		$trimdate=$trimdate?explode(',',$trimdate):null;	
		$adddate=$adddate?explode(',',$adddate):null;
		//排除指定天
		if($trimdate){									
			$go=true;//是否统计
			foreach($trimdate as $key=>$value){										
				if($value==$day)return false;
			}				
		}
		//排除周末,追加某日
		if ($current_week==0 || $current_week==6) {
			$go=false;//是否统计
			if($adddate){									
				foreach($adddate as $key=>$value){
					if($value==$day)return true;
				}
			}
			return false;	
		}
		return true;
	}

	//上班和下班打卡时间
	function getCheckTime($db,$user_number,$date){
		$time_s=strtotime("$date 00:00:00");
		$time_e=strtotime("$date 23:59:59");
		$where=" number='$user_number' and deadline>$time_s AND deadline<$time_e" ;
		$sql = "SELECT deadline FROM duty WHERE $where ORDER BY deadline ASC LIMIT 1";
		//echo "$sql";exit();
		$res=$db->query($sql);
		$on = $res->fetchColumn();
		$sql = "SELECT deadline FROM duty WHERE $where ORDER BY deadline DESC LIMIT 1";
		$res=$db->query($sql);
		$off = $res->fetchColumn();		
		return [$on,$off];
	}

	
	//week星期格式
	$strweek = array('日' , '一', '二', '三', '四', '五', '六' );
	
?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title>考勤计算-duty</title>
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
				$nav=2;
				require("header.php"); 
            ?> 
		    
		    <div class="container" style="width:1060px;">
		    	<div class="row-fluid">
		    		<div class="span9">
						<section>
							<div class="well">
								<p>
								默认排除周末，若要包含某天或删除某天，请到右边重新查询
								</p>
							</div>
						</section>
						<section id="data_all">
							<table class="table table-bordered table-hover">
								<tr>
									<th>自定义编号</th>
									<th>姓名</th>									
									<th>日期</th>
									<th>星期</th>
									<th>签到时间</th>
									<th>签退时间</th>
									<th>部门</th>
									<th>备注</th>									
								</tr>
								<?php

								//所有用户
								$data = data($db,$_REQUEST);
								if ($data) {
									
									
									//遍历所有用户
									foreach ($data as $key=>$value) {	
										$startDate = '2015-05-26';
										$endDate = '2015-06-25';															 
										while (strtotime($startDate) <= strtotime($endDate)) {
											//echo "$startDate\n";										
											//当天										
											$date = $startDate;
											//打卡标准时间
											$sign_early = strtotime($date." 08:30");
											$sign_moning = strtotime($date." 09:10");
											$sign_noon = strtotime($date." 12:00");
											$sign_noon2 = strtotime($date." 13:40");
											$sign_afternoon = strtotime($date." 17:40");
											$sign_overtime = strtotime($date." 18:50");//加班
											//当周
											$current_week=date('w',strtotime($date));
											//当天
											$current_day=date('d',strtotime($date));
											//排除特定日期
											if (isWorkDay($_REQUEST,$current_week,$current_day)) {
												//上班和下班打卡时间
												$arrSign = getCheckTime($db,$value['number'],$date);
												$on = $arrSign[0];
												$off = $arrSign[1];
												//备注
												$remark = "";
												//缺勤
												if ($on == null || $off == null) {
													$remark = "缺勤";
												}else{
													//有打卡记录
													if ($on != $off) {
														//迟到
														if ($on>$sign_moning&&$on<$sign_noon) {
															$remark = "迟到 ";
														}elseif ($on>$sign_noon) {
															//上午没打卡
															$remark = "上午缺勤 ";
															$on = "";
														}elseif ($on<=$sign_early) {
															$remark = "早 ";
														}

														if ($off<$sign_noon2) {
															//下午没来
															$remark .= "下午缺勤";
															$off = "";
														}elseif ($off<$sign_afternoon) {
															//下午没打卡
															$remark .= "下午未打卡";
														}elseif ($off > $sign_overtime) {
															//
															$remark .= "加班";
														}

													}else{
														//打了一次卡
														$remark = "只打了一次卡";
														if ($on<$sign_noon) {
															$off = "";
														}else{
															$on = "";
														}
													}
												}
												if ($on) {
													$on = date('H:i',$on);
												}
												if ($off) {
													$off = date('H:i',$off);
												}
												
												$html = "<tr>";
												$html .= "<td>".$value['number']."</td>";
												$html .= "<td>".$value['name']."</td>";
												$html .= "<td>$date</td>";
												$html .= "<td>".$strweek[$current_week]."</td>";
												$html .= "<td>$on</td>";
												$html .= "<td>$off</td>";
												$html .= "<td>".$value['department']."</td>";
												$html .= "<td>$remark</td>";												
												$html .= "</tr>";
												echo $html;

											}//end if										

											//下一个日期
											$startDate = date ("Y-m-d", strtotime("+1 day", strtotime($startDate)));
										} //end while

										echo "<tr><td colspan='8'></td></tr>";
										
									}//end foreach	
							
								}else{
									echo "<tr><td colspan='8'>打卡数据没有，请导入数据</td></tr>";
								}

								?>
							</table>
						</section>
					</div>
					<div class="span3">
						<div class="well">
							<form action="?" method="post">
								<div class="control-group">
									<div class="controls">
										排除日期：<input type="text" name="trimdate" value="<?=isset($_POST['trimdate'])?$_POST['trimdate']:'';?>"  placeholder="输入某日，用“,”分隔">
									</div>
								</div>
								<div class="control-group">
									<div class="controls">
										包含日期：<input type="text" name="adddate" value="<?=isset($_POST['adddate'])?$_POST['adddate']:'';?>"  placeholder="输入某日，用“,”分隔">
									</div>
								</div>
								<div class="control-group">
									<div class="controls">
										<button type="submit" class="btn">提交</button>&nbsp;
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

