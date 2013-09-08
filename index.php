
<? require_once('db_connect.php'); ?>

<html>

<head>
<title>Ricochet Robot - Terence Tam</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<meta http-equiv="X-UA-Compatible" content="chrome=1">-->
<!--<script type="text/javascript" src="http://bit.ly/jqsource"></script>-->

<!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.min.js"></script>-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    
<script type="text/javascript" src="ricochet.js"></script>
<link rel="stylesheet" type="text/css" href="ricochet.css" />
<link rel="stylesheet" href="style.css" type="text/css" />

<script type="text/javascript" src="apprise-1.5.min.js"></script>
<link rel="stylesheet" href="apprise.css" type="text/css" />

<script type="text/javascript" src="chat.js"></script>
<?

function login($message){
    echo'<h1>Ricochet Robot Terence Tam</h1>';
    echo'<div id="login">';
    echo'<form method="POST"><table>';
    echo'<tr><td>Name</td><td><input type="text" id="name" name="name"/></td></tr>';
    echo'<tr><td>Secret</td><td><input type="password" id="secret" name="secret" /></td></tr>';
    echo'</table><input style="margin: 10px" type="submit" /></form>';
    
    if($message != ''){
        echo '<font color="red"><em>'.$message.'</em></font>';
    }
    echo'</div>';
    
    echo'<p style="font-size:14pt; color: black"><a href="../RicochetRobotSinglePlayer">Click Here for a peek at this board game for single player</a>';
    exit(); 
}
if(!$_POST['name']){
    login('');
}

$name = trim($_POST['name']);

$results = mysql_query("SELECT * FROM users WHERE name = '".$name."'");
$data = mysql_fetch_array($results);
if(!$data){
    login('No Username Exist');
}
$secret = $data['secret'];
$id = $data['id'];

if($_POST['secret'] != $secret){
    login('Wrong Password');
}
?>

<script type="text/javascript">
    
        var name = '<? echo $name ?>';
        var id = '<? echo $id ?>';
        
    	// strip tags
    	name = name.replace(/(<([^>]+)>)/ig,"");

    	// kick off chat
        var chat =  new Chat();
    	$(function() {
    	
            chat.getState(); 
    		 
    		 // watch textarea for key presses
             $("#sendie").keydown(function(event) {  
             
                 var key = event.which;  
           
                 //all keys including return.  
                 if (key >= 33) {
                   
                     var maxLength = $(this).attr("maxlength");  
                     var length = this.value.length;  
                     
                     // don't allow new content if length is maxed out
                     if (length >= maxLength) {  
                         event.preventDefault();  
                     }  
                  }  
    		 																																																});
    		 // watch textarea for release of key press
    		 $('#sendie').keyup(function(e) {	
    		 	//clicking enter	 
    			  if (e.keyCode == 13) { 
    			  
                    var text = $(this).val();
    				var maxLength = $(this).attr("maxlength");  
                    var length = text.length; 
                     
                    // send 
                    if (length <= maxLength + 1) { 
                     
    			        chat.send(text, name);	
    			        $(this).val("");
    			        
                    } else {
                    
    					$(this).val(text.substring(0, maxLength));
    					
    				}	
    				
    				
    			  }
             });
            
    	});
        
        
    		 
        
$(document).ready(function(){
        
        setup(id,name);
        
    	$("#name-area").html("Welcome, " + name);
        
        $('#callBox').keyup(function(e) {	
    		 	//clicking enter
                        //alert('hello');
                        var time = new Date().getTime();
                        //alert(time);
    			  if (e.keyCode == 13) {   
                          var text = $(this).val();	
    			        $(this).val("");
                                //alert(text);
                                $.ajax({
                             type: "POST",
                             url: "changeGrid.php",
                             data: {'function': 'updateCall',
                                    'call': text,
                                    'id': id,
                                    'name': name,
                                    'time': time
                             },
                             //dataType: "json",
                             success: function(json) {
                                 //alert(json);
                             }
                             
                        });
                     }
        });
        
         $('#callBtn').click(function(e) {	
    		 	//clicking enter
                        //alert('hello'); 
                        var time = new Date().getTime();
                          var text = $('#callBox').val();	
    			        $('#callBox').val("");
                                //alert(text);
                                $.ajax({
                             type: "POST",
                             url: "changeGrid.php",
                             data: {'function': 'updateCall',
                                    'call': text,
                                    'id': id,
                                    'name': name,
                                    'time': time
                             },
                             //dataType: "json",
                             success: function(json) {
                                 //alert(json);
                             }
                             
                        });
        });
        /*
         $('.user-box').click(function(e) {	
    		 	//clicking enter
                        //alert('hello'); 
                          var text = $(this).attr('name');	
                             $.ajax({
                             type: "POST",
                             url: "changeGrid.php",
                             data: {'function': 'updatePerson',
                                    'name': text
                             }
                        });
        });
        */
       
});

</script>

</head>
<body onload="setInterval('chat.update()', 500);">

<h1>Ricochet Robot Terence Tam</h1>

<p>Based on Nick's single-player prototype</p>

<div id="grid_wrap">

<div id="message_box">
    <span id="name-area">Hello</span>
    <div id="chat-wrap">
        <div id="user-area">
            <?
            $results = mysql_query("SELECT * FROM users");
            while($data = mysql_fetch_array($results)){
                echo '<div class="user-box" id="'.$data['name'].'" >'.$data['name'].' ('.$data['call'].')</div>';
            }
            ?>
        </div>
        <div id="chat-area"></div>
        <div id="action-area">
            <input id="callBox" type="text" size="1" maxlength = '4'/><br/>
            <button id="callBtn">Call!</button><br/>
            
            <div id="display"></div>
        </div>
    </div>

    <form id="send-message-area">
        <textarea id="sendie" maxlength = '100'></textarea>
    </form>
</div>

<div id="grid"></div>

<div id ="button_area">
<p>

<button id="newBtn" alt="Click to choose new target" accesskey="n">New Round</button>

<button id="resetBtn" alt="Click to reset board" accesskey="r">Reset</button>

<button id="helpBtn" alt="Click to show instructions" accesskey="h">?</button>

</p>

</div>

</div>





<div id="helpLayer">

<span id="targetHelp" class="help">Current Target &rarr;</span>

<div id="stepsHelp" class="help">&larr; Number of steps</div>

<div id="timerHelp" class="help">Time elapsed</div>

<div id="instructions" class="help">

<h3>Instructions</h3>

<ol>

	<li>A new target is chosen each round</li>

	<li>Bring the target-colored robot to the target</li>

	<li>Robots can ricochet off walls and other robots</li>

	<li>Find the minimal solution as fast as possible!</li>

	<li>Robots: 

		<div class="row">

			<div class="red robot"></div>

			<div class="blue robot"></div>

			<div class="green robot"></div>

			<div class="yellow robot"></div>

		</div>

	</li>

	<li>Targets: 

		<div class="row">

			<div class="red rectangle"></div>

			<div class="red circle"></div>

			<div class="red triangle"></div>

			<div class="red bowtie"></div>

		</div>

		<div class="row">

			<div class="blue rectangle"></div>

			<div class="blue circle"></div>

			<div class="blue triangle"></div>

			<div class="blue bowtie"></div>

		</div>

		<div class="row">

			<div class="green rectangle"></div>

			<div class="green circle"></div>

			<div class="green triangle"></div>

			<div class="green bowtie"></div>

		</div>

		<div class="row">

			<div class="yellow rectangle"></div>

			<div class="yellow circle"></div>

			<div class="yellow triangle"></div>

			<div class="yellow bowtie"></div>

		</div>

		<div class="row">

			<div class="white circle"></div>

			<span id="integratedText">(works with robots of any color)</span>

		</div>

	</li>

</ol>

</div>

<!--
<div id="controls" class="help">

<h3>Controls</h3>

<ul>

	<li>Click robot to move</li>

	<li>Click row/column in the direction of movement</li>

	<li>

		<ul id="robotHelp">

			<li><div class="red robot"></div> Red Robot</li>

			<li><div class="blue robot"></div> Blue Robot</li>

			<li><div class="green robot"></div> Green Robot</li>

			<li><div class="yellow robot"></div> Yellow Robot</li>

		</ul>

	</li>

</ul>
</div>
-->
</div>



</body>
</html>



