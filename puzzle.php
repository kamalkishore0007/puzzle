<?php 
//error_reporting(0);
include 'connection.php';
define('BASE_URL','http://www.elections.in/puzzle/');
//get ip address
function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
	    
        if (array_key_exists($key, $_SERVER)){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe
               
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
					
                }
				return $ip;
				}
			}
		}
	}
	
$mode=$_POST['mode'];
$ip=get_ip_address();
$error=array();
$response=array();
if(!empty($mode)){
switch($mode){
    case 'new_user_check':
		{
		  check_new_user(); 	
		
		 break;
		}
	case 'register':
		{
		  register();
		break;
		}
	case 'fetch_current_puzzle':
		{
		 fetch_current_puzzle();
		break;
		}
	case 'insert_score':
		{
		 insert_score();
		break;
		}	
	case 'fetch_top_scorer':
		{
		fetch_top_scorer();
		 break;
		}
	case 'recent_played':
		{
		recent_played($_POST['user_id'],$_POST['puzzle_id']);
		break;
		}
	case 'onlineuser':
		{
			check_online_user();
			break;
		}
	default:
      {
        header("Location:".BASE_URL."puzzle.html");
        break;
      }	 					
}
}

//check for the new user 
function check_new_user(){
global $ip;
$query=mysql_query("select * from puzzle_user where ip='$ip'");
if(mysql_num_rows($query)>0){
   $data=mysql_fetch_row($query);
  $response=array('status'=>201,'is_register'=>'1','name'=>$data[1],'id'=>$data[0]);
}
else{
$response=array('status'=>201,'is_register'=>'0','ip'=>$ip);
}

print_r(json_encode($response));
}

//register new user
function register(){
 extract($_POST);
global $ip;
 $insert=mysql_query("insert into puzzle_user (name,age,gender,email,state,ip) values('$name','$age','$gender','$email','$state','$ip')");
  if($insert){
  $id=mysql_insert_id();
   $response=array('status'=>201,'success'=>1,'ip'=>$ip,'id'=>$id,'name'=>$name);
  }
  else{
	$response=array('status'=>201,'success'=>0);
  }
  print_r(json_encode($response));
}
//fetch current running puzzle
function fetch_current_puzzle(){
   $query=mysql_query("select * from puzzle_list where active=1 order by created_on desc limit 1");
   while($data=mysql_fetch_array($query)){
	 $response=array('status'=>201,'puzzle_id'=>$data['puzzle_id'],'title'=>$data['title'],'img'=>$data['img'],'ans'=>ucfirst($data['answer']),'timer'=>$data['timer'],'success'=>1);
   
   }
   print_r(json_encode($response));
   
}
//inser user score in db
function insert_score(){
global $ip;
extract($_POST);
$query=mysql_query("select score from puzzle_score where user_id=$user_id and puzzle_id=$puzzle_id");
if(mysql_num_rows($query)>0){
	$data=mysql_fetch_array($query);
	if($data['score']>$score){
		 $response=array('status'=>201,'message'=>'<p class="h_scre">Well Played ,Score : '.$score.'</p><div class="re_st"><input type="button" class="restart" id="restart" value="Restart" onclick=init();></div>');
	}
	else{
	 $update=mysql_query("update puzzle_score set score=$score where user_id=$user_id and puzzle_id=$puzzle_id");
		if($update){
		 $response=array('status'=>201,'message'=>'<img src="assets/win.png"/><br /><p class="h_scre">You Got New High Score : '.$score.'</p><div class="s_icon"><div class="share"><input type="button" class="shre_btn" id="restart" value="Facebook Share" onclick="fshare();"></div><div class="share"><input type="button" class="shre_btn" id="restart" value="Twitter Share" onclick="tshare();"></div></div><div class="re_st"><input type="button" class="restart" id="restart" value="Restart" onclick=init();></div>');
		}
	
	}

}
else
{
$insert=mysql_query("insert into puzzle_score (puzzle_id,user_id,score,ip) values($puzzle_id,$user_id,$score,'$ip')");
if($insert){
 $response=array('status'=>201,'message'=>'<img src="assets/win.png"/><br /><p class="h_scre">You Got New High Score : '.$score.'</p><div class="s_icon"><div class="share"><input type="button" class="shre_btn" id="restart" value="Facebook Share" onclick="fshare();"></div><div class="share"><input type="button" class="shre_btn" id="restart" value="Twitter Share" onclick="tshare();"></div></div><div class="re_st"><input type="button" class="restart" id="restart" value="Restart" onclick=init();></div>');
}
}
  print_r(json_encode($response));
}

//fetch top scorer
function fetch_top_scorer(){
extract($_POST);
$tp_score=mysql_query("select pu.name,pu.id,pu.state,ps.score from puzzle_user pu join puzzle_score ps on pu.id=ps.user_id where ps.puzzle_id=$puzzle_id order by score desc limit 10");
if(mysql_num_rows($tp_score)>0){
$rank=1;
$list="<table class='tscore'><tr style='background: rgb(223, 220, 183);'><th>Rank</th><th>Username</th><th>State</th><th>Score</th></tr>";
 while($player=mysql_fetch_array($tp_score)){
	$bg=($player['id']==$uid)?'style="background: rgb(136, 202, 228)"':'';  
	$list.="<tr $bg><td>#$rank</td><td>".ucfirst($player['name'])."</td><td>".ucfirst($player['state'])."</td><td>".$player['score']."</td></tr>";
	$rank++;
	}
$list.="</table>";	
$response=array('status'=>201,'list'=>$list);	
}
else{
$response=array('status'=>201,'list'=>'<table class="tscore"><tr><th>Rank</th><th>Username</th><th>State</th><th>Score</th><tr><td colspan="4">No Record Found</td></tr></tr>');	
}
 print_r(json_encode($response));
}
//fetch recent played game;
function recent_played($user_id,$puzzle_id){
$recent_played_game=mysql_query("SELECT ep.img ,ps.puzzle_id,ps.score from puzzle_list ep join  puzzle_score ps on ep.puzzle_id=ps.puzzle_id where ps.user_id=$user_id");
$list="<div class='rply'><table class='tscore'><tr style='background: rgb(223, 220, 183);'><th>S.No</th><th>Puzzle</th><th>Your Score</th><th>Your Rank</th></tr>";
	if(mysql_num_rows($recent_played_game)>0){
	   $sno=1;
		while($data=mysql_fetch_array($recent_played_game)){
		 $rank=_get_rank($user_id,$data['puzzle_id']);
		 $list.="<tr><td>$sno</td><td><img src='".FILE_SERVER.'/img/'.$data['img']." ' width='50' height='50'/></td><td>".$data['score']."</td><td>#".$rank."</td></tr>";
		  $sno++;
		}
	}else{
	 $list.="<tr><td colspan='4'>No Record Found</td></tr>";
	
	}
$list.="</table></div>";	
$response=array('status'=>201,'list'=>$list);
print_r(json_encode($response));
}
function _get_rank($user_id,$game_id){
$rank_query=mysql_query("SELECT FIND_IN_SET( score, (    
SELECT GROUP_CONCAT( score
ORDER BY score DESC ) 
FROM puzzle_score where puzzle_id=$game_id)
) AS rank
FROM puzzle_score 
WHERE user_id=  '".$user_id."' and puzzle_id=".$game_id);

$data=mysql_fetch_array($rank_query);
return $data['rank'];

}

function check_online_user(){
global $ip;
	$time=time();
$time_check=$time-180; //SET timestamp 10 Minute
$tbl_name="puzzle_user_online"; // Table name
$sql="SELECT * FROM $tbl_name WHERE ip='$ip'";
$result=mysql_query($sql);

$count=mysql_num_rows($result);
if($count==0){

$sql1="INSERT INTO $tbl_name(ip, timestamp)VALUES('$ip', '$time')";
$result1=mysql_query($sql1) or die(mysql_error());
}

else {
$sql2="UPDATE $tbl_name SET timestamp='$time' WHERE ip='$ip'";
$result2=mysql_query($sql2);
}
// if over 10 minute, delete ip 
$sql4="DELETE FROM $tbl_name WHERE timestamp<$time_check";
$result4=mysql_query($sql4);

  $query=mysql_query("SELECT DISTINCT pu.name,pu.state,puo.ip FROM puzzle_user_online  puo join puzzle_user pu on pu.ip=puo.ip");
  $count_user = mysql_num_rows($query);
  if($count_user>0){
    $ulist="<table><tr><th>Users(online)</th></tr>";
  	while($user=mysql_fetch_array($query)){
	  
  		$ulist.='<tr><td>'.$user['name'].'('.$user['state'].')</td></tr>';
  		
  	
  	}
  	$ulist.="</table>";
  	$response=array('status'=>201,'users'=>$ulist,'count'=>$count_user);
  	print_r(json_encode($response));
  }


}