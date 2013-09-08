<?php
require_once('db_connect.php');

$function = $_POST['function'];
//$id = $_POST['id'];
$state = array();

$results = mysql_query("SELECT json,priority FROM game WHERE id = 2");
$data = mysql_fetch_array($results);
$json = $data['json'];
//$priority = $data['priority'];

    switch($function) {
        
        case('getState'):
            //$json = file_get_contents('grid.js');
            $state = json_decode($json);
            echo json_encode($state);
        break;
    
        case('getPriority'):
            $state = json_decode($json);  
            $results2 = mysql_query("SELECT MIN(`call`),`name`,`id` FROM users GROUP BY `call` LIMIT 0,6");
            $data2 = mysql_fetch_array($results2);
            
            $player_name = $data2['name'];
            $player_id = $data2['id'];
            
            echo $player_name;
            
            $state[6] = (int) $player_id;
            mysql_query("UPDATE game SET json ='".json_encode($state)."' WHERE id = 2");
            
        break;
    
        case('newGame'):
            for ($r = 0; $r <= 3; $r++){
               $i = round(rand(0,15));
               $j = round(rand(0,15));     
            
               while(($i>6 and $i<9 and $j>6 and $j<9) or in_array(array($i,$j),$state)) {
				$i = round(rand(0,15));
				$j = round(rand(0,15));
			}
               $state[$r] = array($i, $j);
            }   
            
            $state[4] = round(rand(0,16)); //random init target
            $state[5] = 100;        //init call
            //$state[7] = selected
            $state[6] = 0;         //no one can move
            $state[7] = array(-1,-1);
            $state[8] = 0;         //time
            echo json_encode($state);
            //fwrite(fopen('grid.js', 'w'), json_encode($state));
            mysql_query("UPDATE game SET json ='".json_encode($state)."' WHERE id = 2");
            mysql_query("UPDATE users SET `call` = 100");
            
        break;
    
        case('updateState'):
            
            //$json = file_get_contents('grid.js');
            $state = json_decode($json);
            
            $robot = explode(" ",$_POST["r"]);
            $robot = $robot[0];
            
            $robots = array("red", "blue", "green", "yellow");
            $r = array_search($robot, $robots);
            $i = $_POST["i"];
            $j = $_POST["j"];
            
            $state[$r] = array((int)$i, (int)$j);
            $state[7] = array((int)$i, (int)$j);
            echo json_encode($state);
            //fwrite(fopen('grid.js', 'w'), json_encode($state));
            mysql_query("UPDATE game SET json ='".json_encode($state)."' WHERE id = 2");
            
        break;
    
        case('updateCall'):
            //$json = file_get_contents('grid.js');
            $state = json_decode($json);
            $priority = json_decode($priority);
            
            $time = $_POST['time'];
            $call = $_POST['call'];
            $state[5] = (int)$call;
            $id = $_POST['id'];
            $name = $_POST['name'];
            
            echo $state[5];
            
            //$state[6] = (int) $id;
            
            if ($state[8] == 0){
                $state[8] = $time;
            }
            
            array_push($priority,$name);
            //fwrite(fopen('grid.js', 'w'), json_encode($state));
            mysql_query("UPDATE game SET json ='".json_encode($state)."', priority ='".json_encode($priority)."' WHERE id = 2");
            mysql_query("UPDATE users SET `call` = ".$call." WHERE `id` = ".$id);
        break;
    
        case('updateSelected'):
            //$json = file_get_contents('grid.js');
            $state = json_decode($json);
            $i = $_POST["i"];
            $j = $_POST["j"];
            $state[7] = array((int)$i, (int)$j);
            echo $state[7];
            //fwrite(fopen('grid.js', 'w'), json_encode($state));
            mysql_query("UPDATE game SET json ='".json_encode($state)."' WHERE id = 2");

        break;
        
    }

?>