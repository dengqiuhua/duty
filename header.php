<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            
            <a href="/" class="brand">duty analysis</a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li class="<?=$nav&&$nav==1?'active':'';?>">
                        <a href="index.php">数据</a>
                    </li>
                    <li class="<?=$nav&&$nav==2?'active':'';?>">
                        <a href="check.php">统计</a>
                    </li>
                    <?php
						if(isset($_SESSION['user'])&&$_SESSION['user']){
					?>
                    <li>
                        <a href="login.php?act=loginout">退出</a>
                    </li>
                    <?php
                        }
					?>
                </ul>
            </div>
        </div>
    </div>
 </div>  
<div class="clear" style=" margin-top: 50px;">&nbsp;</div>