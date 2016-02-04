<?php include_once 'defines.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
	<link href='style.css' rel='stylesheet' type='text/css'>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
	<script src='http://connect.facebook.net/en_US/all.js'></script>

    <script>
       var base_url='<?php echo base_url;?>/';
        const PUZZLE_DIFFICULTY = 4;
        const PUZZLE_HOVER_TINT = '#009900';

        var _stage;
        var _canvas;

        var _img;
        var _pieces;
        var _puzzleWidth;
        var _puzzleHeight;
        var _pieceWidth;
        var _pieceHeight;
        var _currentPiece;
        var _currentDropPiece;  
		var _counter;
		var time;
		var imagesrc;
		var _countdown;
		var user_info={};
		var puzzle_id;
		var user_id;
		var _name;
        var message_popup;
        var _mouse;
		var score;
		var ques;
		var answer;
          
		function startup_form(){
		         
			//check for new user	
			 $('.inner_game').append('<div id="loader"><img src="img/ajax-loader.gif"></div>');
		     setTimeout(function(){
			 $.post(base_url+'puzzle.php',{mode:'new_user_check'},function(response){
			     $('#loader').remove();
		         var response=$.parseJSON(response);
				 if(response.is_register==1){
				   $('#user_id').val(response.id);
				   user_id=response.id
				   $('#user_name').val(response.name);
				   _name=response.name;
				   $('#canvas').before(' <div class="u_info">Welcome: '+response.name+'</div>');
				   
				   game_start();
				 }
				 else{
				    
					create_user_form();
				 }
		 
		 
				});
			},5000);	
		
		}
		function create_user_form(){
		   var register_form='<form class="register" id="register" method="post"><div class="rgst-box"><div class="rest-lebel"><label class="error"></label><span class="rg_text">Name</span><span class="rg_input"><input type="text" value="" name="name"/></span></div><div class="rest-lebel"><span class="rg_text">Age</span><span class="rg_input"><input type="text" value="" name="age"/></span></div><div class="rest-lebel"><span class="rg_text">Gender</span><span class="rg_input"><input type="radio" value="male" name="gender"/><span class="rg_g">Male</span></span><span class="rg_input"><input type="radio" value="female" name="gender"/><span class="rg_g">Female</span></span></div><div class="rest-lebel"><span class="rg_text">Email ID</span><span class="rg_input"><input type="text" value="" name="email"/></span></div><div class="rest-lebel"><span class="rg_text">State</span><span class="rg_input"><input type="text" value="" name="state"/></span></div><div class="rest-lebel"><span class="br_box"><input type="Submit" value="Next" name="register" class="register"/></span></div></div></form>';
		   $('body').append(register_form)
		}
		
		function game_start(){
		  $.post(base_url+'puzzle.php',{mode:'fetch_current_puzzle'},function(response){
		      var response=$.parseJSON(response);
			  if(response.success==1){
				$('#puzzle_id').val(response.puzzle_id);
				puzzle_id=response.puzzle_id;
				imagesrc='img/'+response.img;
				_counter=response.timer;
				time=response.timer;
				ques=response.title;
				answer=response.ans;
				$('.u_info').after('<div class="c_timer">Timer : <span id="timer"></span></div>');
				$('#canvas').before('<div class="tp_scre">Top Scorer</div>');
				$('#canvas').before('<div class="u_info recent_played" onclick=recent_played();>Recent Played</div>');
				 $('.inner_game').append('<div class="dialog-box"><div class="re_st un" >'+ques+'?<div class="re_st lply" ><input type="button" value="Next" class="register" onclick="get_user_online();init()"/></div></div>'); 
				 
			  }
		  
		  });
		  
		
		
		}
        function init(){
		 $("#canvas").removeClass('canvas');
		 $('.dialog-box').remove();
			 $('.inner_game').append('<div class="dialog-box"><div class="re_st un" >Hi , '+_name+'</div><div class="re_st" ><img src="assets/play_btn.png" onclick=shufflePuzzle();></div><div class="re_st lply" >Lets Play</div></div>'); 
		     $('#restart').remove();
            _img = new Image();
            _img.addEventListener('load',onImage,false);
            _img.src = imagesrc;
			document.getElementById('timer').innerHTML=_counter;
        }
        function onImage(e){
            _pieceWidth = Math.floor(_img.width / PUZZLE_DIFFICULTY)
            _pieceHeight = Math.floor(_img.height / PUZZLE_DIFFICULTY)
            _puzzleWidth = _pieceWidth * PUZZLE_DIFFICULTY;
            _puzzleHeight = _pieceHeight * PUZZLE_DIFFICULTY;
            setCanvas();
            initPuzzle();
        }
        function setCanvas(){
            _canvas = document.getElementById('canvas');
            _stage = _canvas.getContext('2d');
            _canvas.width = _puzzleWidth;
            _canvas.height = _puzzleHeight;
            _canvas.style.border = "20px solid #2d3238";
        }
		function start_timer(){
		  _counter--;
		  document.getElementById('timer').innerHTML=_counter;
		 if(_counter==0){
		  clearInterval(_countdown);
		  _counter=time;
		  gameOver();
		 }
		}
		
        function initPuzzle(){
		
            _pieces = [];
            _mouse = {x:0,y:0};
            _currentPiece = null;
            _currentDropPiece = null;
            _stage.drawImage(_img, 0, 0, _puzzleWidth, _puzzleHeight, 0, 0, _puzzleWidth, _puzzleHeight);
            //createTitle("Click to Start Puzzle");
            buildPieces();
        }
        function createTitle(msg){
            _stage.fillStyle = "#000000";
            _stage.globalAlpha = .4;
            _stage.fillRect(100,_puzzleHeight - 40,_puzzleWidth - 200,40);
            _stage.fillStyle = "#FFFFFF";
            _stage.globalAlpha = 1;
            _stage.textAlign = "center";
            _stage.textBaseline = "middle";
            _stage.font = "20px Arial";
            _stage.fillText(msg,_puzzleWidth / 2,_puzzleHeight - 20);
        }
        function buildPieces(){
		 
            var i;
            var piece;
            var xPos = 0;
            var yPos = 0;
            for(i = 0;i < PUZZLE_DIFFICULTY * PUZZLE_DIFFICULTY;i++){
                piece = {};
                piece.sx = xPos;
                piece.sy = yPos;
                _pieces.push(piece);
                xPos += _pieceWidth;
                if(xPos >= _puzzleWidth){
                    xPos = 0;
                    yPos += _pieceHeight;
                }
            }
		
        // document.onmousedown = shufflePuzzle;
        }
        function shufflePuzzle(){
		$('.dialog-box').remove();
		
		_countdown=setInterval(function(){start_timer()},1000);
            _pieces = shuffleArray(_pieces);
            _stage.clearRect(0,0,_puzzleWidth,_puzzleHeight);
            var i;
            var piece;
            var xPos = 0;
            var yPos = 0;
            for(i = 0;i < _pieces.length;i++){
                piece = _pieces[i];
                piece.xPos = xPos;
                piece.yPos = yPos;
                _stage.drawImage(_img, piece.sx, piece.sy, _pieceWidth, _pieceHeight, xPos, yPos, _pieceWidth, _pieceHeight);
                _stage.strokeRect(xPos, yPos, _pieceWidth,_pieceHeight);
                xPos += _pieceWidth;
                if(xPos >= _puzzleWidth){
                    xPos = 0;
                    yPos += _pieceHeight;
                }
            }
			
            document.onmousedown = onPuzzleClick;
        }
        function onPuzzleClick(e){
            if(e.layerX || e.layerX == 0){
                _mouse.x = e.layerX - _canvas.offsetLeft;
                _mouse.y = e.layerY - _canvas.offsetTop;
            }
            else if(e.offsetX || e.offsetX == 0){
                _mouse.x = e.offsetX - _canvas.offsetLeft;
                _mouse.y = e.offsetY - _canvas.offsetTop;
            }
            _currentPiece = checkPieceClicked();
            if(_currentPiece != null){
                _stage.clearRect(_currentPiece.xPos,_currentPiece.yPos,_pieceWidth,_pieceHeight);
                _stage.save();
                _stage.globalAlpha = .9;
                _stage.drawImage(_img, _currentPiece.sx, _currentPiece.sy, _pieceWidth, _pieceHeight, _mouse.x - (_pieceWidth / 2), _mouse.y - (_pieceHeight / 2), _pieceWidth, _pieceHeight);
                _stage.restore();
                document.onmousemove = updatePuzzle;
                document.onmouseup = pieceDropped;
            }
        }
        function checkPieceClicked(){
            var i;
            var piece;
            for(i = 0;i < _pieces.length;i++){
                piece = _pieces[i];
                if(_mouse.x < piece.xPos || _mouse.x > (piece.xPos + _pieceWidth) || _mouse.y < piece.yPos || _mouse.y > (piece.yPos + _pieceHeight)){
                    //PIECE NOT HIT
                }
                else{
                    return piece;
                }
            }
            return null;
        }
        function updatePuzzle(e){
            _currentDropPiece = null;
            if(e.layerX || e.layerX == 0){
                _mouse.x = e.layerX - _canvas.offsetLeft;
                _mouse.y = e.layerY - _canvas.offsetTop;
            }
            else if(e.offsetX || e.offsetX == 0){
                _mouse.x = e.offsetX - _canvas.offsetLeft;
                _mouse.y = e.offsetY - _canvas.offsetTop;
            }
            _stage.clearRect(0,0,_puzzleWidth,_puzzleHeight);
            var i;
            var piece;
            for(i = 0;i < _pieces.length;i++){
                piece = _pieces[i];
                if(piece == _currentPiece){
                    continue;
                }
                _stage.drawImage(_img, piece.sx, piece.sy, _pieceWidth, _pieceHeight, piece.xPos, piece.yPos, _pieceWidth, _pieceHeight);
                _stage.strokeRect(piece.xPos, piece.yPos, _pieceWidth,_pieceHeight);
                if(_currentDropPiece == null){
                    if(_mouse.x < piece.xPos || _mouse.x > (piece.xPos + _pieceWidth) || _mouse.y < piece.yPos || _mouse.y > (piece.yPos + _pieceHeight)){
                        //NOT OVER
                    }
                    else{
                        _currentDropPiece = piece;
                        _stage.save();
                        _stage.globalAlpha = .4;
                        _stage.fillStyle = PUZZLE_HOVER_TINT;
                        _stage.fillRect(_currentDropPiece.xPos,_currentDropPiece.yPos,_pieceWidth, _pieceHeight);
                        _stage.restore();
                    }
                }
            }
            _stage.save();
            _stage.globalAlpha = .6;
            _stage.drawImage(_img, _currentPiece.sx, _currentPiece.sy, _pieceWidth, _pieceHeight, _mouse.x - (_pieceWidth / 2), _mouse.y - (_pieceHeight / 2), _pieceWidth, _pieceHeight);
            _stage.restore();
            _stage.strokeRect( _mouse.x - (_pieceWidth / 2), _mouse.y - (_pieceHeight / 2), _pieceWidth,_pieceHeight);
        }
        function pieceDropped(e){
            document.onmousemove = null;
            document.onmouseup = null;
            if(_currentDropPiece != null){
                var tmp = {xPos:_currentPiece.xPos,yPos:_currentPiece.yPos};
                _currentPiece.xPos = _currentDropPiece.xPos;
                _currentPiece.yPos = _currentDropPiece.yPos;
                _currentDropPiece.xPos = tmp.xPos;
                _currentDropPiece.yPos = tmp.yPos;
            }
            resetPuzzleAndCheckWin();
        }
        function resetPuzzleAndCheckWin(){
            _stage.clearRect(0,0,_puzzleWidth,_puzzleHeight);
            var gameWin = true;
            var i;
            var piece;
            for(i = 0;i < _pieces.length;i++){
                piece = _pieces[i];
                _stage.drawImage(_img, piece.sx, piece.sy, _pieceWidth, _pieceHeight, piece.xPos, piece.yPos, _pieceWidth, _pieceHeight);
                _stage.strokeRect(piece.xPos, piece.yPos, _pieceWidth,_pieceHeight);
                if(piece.xPos != piece.sx || piece.yPos != piece.sy){
                    gameWin = false;
                }
            }
            if(gameWin){
			    display_score(_name);
			   clearInterval(_countdown);
			   _counter=time;
			   document.onmousedown = null;
            document.onmousemove = null;
            document.onmouseup = null;
            }
			
        }
        function gameOver(){
            document.onmousedown = null;
            document.onmousemove = null;
            document.onmouseup = null;
			restart();
        }
        function shuffleArray(o){
            for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
            return o;
        }
		function display_score(_name){
		 user_id=document.getElementById('user_id').value;
		 puzzle_id=document.getElementById('puzzle_id').value;
		 score=_counter*10
		 $.post(base_url+'puzzle.php',{mode:'insert_score',score:score,puzzle_id:puzzle_id,user_id:user_id},function(response){
		 var response=$.parseJSON(response);
		   message_popup='<div class="dialog-box " ><div>Ansser: '+answer+'</div>'+response.message+'<div class="re_st"><input type="button" class="restart"  value="Exit" onclick=home();></div>';
		    $('.inner_game').append(message_popup);
		 });
		
		}
		function restart(){
		  var html='<div class="dialog-box"><p class="h_scre">Game Over</p><div class="re_st"><input type="button" class="restart" id="restart" value="Restart" onclick=init();></div><div class="re_st"><input type="button" class="restart" value="Exit" onclick="home();"></div></div>'
			$('.inner_game').append(html);
		}
		//resent played game
		function recent_played(){
		 $.post(base_url+'puzzle.php',{user_id:user_id,puzzle_id:puzzle_id,mode:'recent_played'},function(response){
			 var response=$.parseJSON(response);
			 $('.rp_cl').remove();
			$('.inner_game').append('<div class="dialog-box rplayed rp_cl"><span class="close">X</span>'+response.list+'</div>');
		 });
		
		
		}
		setInterval(function(){get_user_online()},180000);
		function get_user_online(){
			 $.post(base_url+'puzzle.php',{mode:'onlineuser'},function(response){
			 var response=$.parseJSON(response);
			 $('.u_online').remove();
		      $('.inner_game').after('<div class="u_online"></div>');
			  $('.u_online').append(response.users);
			  
		  });
		
		}
		//home 
		function home(){
		 window.location.href=base_url+'/puzzle.html';
		}
		
//registration process
$(document).on('click','.register',function(){
      $('form#register').validate({
                rules: {
                    name: "required",
                    age: "required",
                    email: {
                        required: true,
                        email: true
                    },
					state:"required",
					gender:"required"
                },
                messages: {
                    name: "",
                    age: "",
                    email: "",
                    state: "",
					gender: ""
					
                },
				showErrors: function(errorMap, errorList) {
					$(".error").html("All fields are required.");
				},
                submitHandler: function(form) {
				    
                   $.ajax({
				    type:"POST",
					url:"puzzle.php",
					data:$(form).serialize()+"&mode=register",
					success:function(data){
						 var response=$.parseJSON(data);	
                         if(response.success==1){
						    $(form).remove();
							$('#user_id').val(response.id);
							$('#user_name').val(response.name);
							  user_id=response.id
							_name=response.name;
							 $('#canvas').before(' <div class="u_info">Welcome: '+response.name+'</div>')
							game_start();
						 }	
						 else{
							alert('whoops! something went wrong');
						 }
					}	
				   });
				   return false;
                }
            });
  
});
 //form validation rules
//fetch top Scorer

$(document).on('click','.tp_scre',function(){
p_id=$('#puzzle_id').val();
$.post(base_url+'puzzle.php',{puzzle_id:p_id,uid:user_id,mode:'fetch_top_scorer'},function(response){
  var response=$.parseJSON(response);	
  $('.rp_cl').remove();
 $('.inner_game').append('<div class="dialog-box rp_cl"><span class="close">X</span><div class="rply">'+response.list+'</div></div>');
});
}); 

$(document).on('click','.close',function(){
$(this).parent().remove();
});
         
//facebook n twitter share
function fshare(){
 //var title=$(get).attr('rel');
	FB.init({appId: "693720480690107", status: true, cookie: true});
	// calling the API ...
	var obj = {
		  method: 'feed',
		  link: 'http://www.elections.in/political-quote-of-the-day.html',
		  picture:'http://images.elections.in/img/'+imagesrc,
		  name:'Election Puzzle',
		  description:'Hey I got '+score+' in election puzzle'
	};

	function callback(response) {
	   
		 
		  if(!response || response.error){
		   $('.dialog-box').remove();
			 $('.inner_game').append('<div class="dialog-box rp_cl"><span class="close">X</span><p class="h_scre">Some Error Occured </p><div class="re_st"><input type="button" class="restart" id="restart" value="Restart" onclick=init();></div><div class="re_st"><input type="button" class="restart" value="Exit" onclick="home();"></div></div>');
		  }
		  else{
		  $('.inner_game').append('<div class="dialog-box rp_cl"><span class="close">X</span><p class="h_scre">Successfull</p><div class="re_st"><input type="button" class="restart" id="restart" value="Restart" onclick=init();></div><div class="re_st"><input type="button" class="restart" value="Exit" onclick="home();"></div></div>');
		  }
	}
	FB.ui(obj, callback);
}
function tshare()
{
var url="https://twitter.com/intent/tweet?original_referer=http://www.elections.in/election-puzzle.html&source=tweetbutton&text=I got score:"+score+" in election puzzle check out yours  via @elections.in";
window.open(url,"_blank","top=250, left=500, width=600, height=600");
}		 
    </script>
</head>

<body onload="startup_form();">
<div class='game_container'>   
	<input type="hidden" name='puzzle_id' id='puzzle_id'>
	<input type="hidden" name='user_id' id='user_id'>
	<input type="hidden" name='user_name' id='user_name'>

	<div class='g_main'>
	   <div class='inner_game'>
		<canvas id="canvas" class='canvas' >
			Something went wrong
		</canvas>
		<div class="Rank"></div>
	   </div>
	   
   
	</div>
	
</div>	
	
</body>
</html>