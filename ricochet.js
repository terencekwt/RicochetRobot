var t;
var t2 = 0;
var locked = false;

var forward = false;
var updating = false;

function setup(iden,name) {
    
	var legend = [ "n", "e", "w", "s", "nw", "ne", "sw", "se", "ns", "we" ]; 
	var map = [
	"4005480000548005",
	"2    52    14  1",
	"216          161",
	"6 0    16     01",
	"4     720      7",
	"2     0  723   5",
	"2 72   330 52  1",
	"2 0   1452     1",
	"23   31672     1",
	"252 14 00      7",
	"6       72   3 5",
	"4       0   14 1",
	"2     72       1",
	"2     0 16    31",
	"216      0    59",
	"6383376333376337",
	];
	var grid = [[],[],[],[],
				[],[],[],[],
				[],[],[],[],
				[],[],[],[],];
	var html = "";
	for(var i in map) {
		var cols = map[i].split('');
		html += '<div class="row">';
		
		for(var j in cols) {
			var tile = parseInt(cols[j]),
				id = i + "-" + j;
			if(isNaN(tile))
				html += '<div id="' + id + '" class="cell"></div>';
			else
				html += '<div id="' + id + '" class="cell b-' + legend[tile] + '"></div>';
			grid[i][j] = tile;
		}
		
		html += '</div>';
	}
	$('#grid').append(html);
	
	var targets = [
		[1, 5, "blue triangle"],
		[1, 12, "green rectangle"],
		[2, 2, "red rectangle"],
		[2, 14, "red bowtie"],
		[3, 8, "white circle"],
		[4, 6, "yellow circle"],
		[5, 9, "blue circle"],
		[6, 2, "green bowtie"],
		[6, 11, "yellow triangle"],
		[9, 1, "yellow bowtie"],
		[9, 5, "blue rectangle"],
		[10, 8, "yellow rectangle"],
		[11, 13, "blue bowtie"],
		[12, 6, "red circle"],
		[13, 9, "green circle"],
		[14, 2, "green triangle"],
		[14, 14, "red triangle"]
	];
	
	for(var i in targets) {
		var target = targets[i];
		$('#' + target[0] + "-" + target[1]).html(
			'<div class="' + target[2] + '"></div>');
	}
	
        var robots = ["red", "blue", "green", "yellow"];
        var state = [];
        /*
        var resetGame = function() {
                
                for(var r in robots) {
                        var id = '#' + state[r].join('-');
                        $(id).prepend($('#grid').find('.robot.' + robots[r]));
                        $('#grid').find('.selected').removeClass('selected');
                        $('#steps').text('0');
                }
        };*/
        
	var newGame = function() {
                //changing = true;
                if(t2 != 0){
                    clearTimeout(t2);
                }
                $.ajax({
                             type: "POST",
                             url: "changeGrid.php",
                             data: {'function': 'newGame'},
                             dataType: "json",
                             success: function(json) {
                                //changing = true;
                                state = json;
                                
                                $('#grid').find('.robot').remove();
                                $('#grid').find('.selected').removeClass('selected');
                
                                for(var r in robots) {
                                var robot = robots[r];
                                var i = state[r][0];
                                var j = state[r][1];
                                $('#' + i + "-" + j).prepend(
                                    '<div class="' + robot + ' robot"></div>');
                                }
                
                                var target = targets[state[4]][2];
                                $('#7-7').html('<div class="' + target + '"></div>');

                                start = new Date().getTime()
                                $('#secs').text('--');
                                $('#mins').text('--');
        
                                $('#steps').text('0');
                                
                                $('.user-box').css("color","black");
                                
                                //resetGame();
                                //changing = false;
                             }
                        });
        updatecounter();
	};
	
        //newGame();
	
	$('#newBtn').click(newGame);
	//$('#resetBtn').click(resetGame);
	
	$('#7-8').html('<div id="steps" class="counter">0</div>');
	$('#8-7').html('<div id="mins" class="counter">00</div>');
	$('#8-8').html('<div id="secs" class="counter">00</div>');
	
	//var start = new Date().getTime(),
	var	mins = $('#mins'),
		secs = $('#secs');
		
	function formatTime(flt) {
		var time = Math.floor(flt);
		if(time < 10)
			return "0" + time;
		else
			return time;
	}
	function updatecounter() {
                if(state[8] == 0){
                    t2 = setTimeout(updatecounter, 1000);
                    return;
                } 
		var now = new Date().getTime();
                var start = state[8];
                //alert(start);
	        elapsed = 10 - (now - start)/1000;
		
                if(elapsed < 0){
                   mins.text("U");
                   secs.text("P");
                   $.ajax({
                             type: "POST",
                             url: "changeGrid.php",
                             data: {'function': 'getPriority'},
                             success: function(name) {
                                 $('#'+ name).css("color","red");
                                 //$('#button_area').append('<button id="resetBtn" alt="Click to reset board" >Reset</button>');
                            }
                    });
		   //t2 = setTimeout(updatecounter, 1000); 
                }
                else{
		secs.text(formatTime(elapsed%60));
		mins.text(formatTime(elapsed/60));
		t2 = setTimeout(updatecounter, 500);
                }
	}
        
        (function updateStat() {

		$.ajax({
                             type: "POST",
                             url: "updateStat.php",
                             dataType: "json",
                             success: function(json) {
                                 //alert(json['Foo']['score']);
                                 for(var r in json){
                                    //alert(r);
                                    //$('#'+r).html('5');
                                    $('#'+r).html(r + ' ' + json[r]['call']);
                                 }
                            }
                });
                setTimeout(updateStat,1000);
                
        })();
        
        
        function updateGrid(){
            
            $.ajax({
                             type: "POST",
                             url: "changeGrid.php",
                             data: {'function': 'getState'},
                             dataType: "json",
                             success: function(json) {
                                 state = json;
                                 state[4]=json[4];
                                 var target = targets[state[4]][2];
                                 $('#7-7').html('<div class="' + target + '"></div>');                                 
                                 $('#display').html(state[5]);

                                 if(state[6] == iden){
                                     locked = false;
                                     return;    //don't update the board if you are the guy moving
                                 }
                                 else{
                                     locked = true;
                                 }
                                 
                                 //reset the clock
                                 /*
                                 if(state[8] == 0){
                                     $('#secs').text('--');
                                     $('#mins').text('--');
                                 }
                                 */

                                 for(var r in robots){
                                     var robot = robots[r];
                                     var i = state[r][0];
                                     var j = state[r][1];

                                         var newPos = $('#' + i + '-' + j);

                                         $('#grid').find('.'+robot+'.robot').remove();
                                         newPos.prepend(
                                            '<div class="' + robot + ' robot"></div>');
                                 }
                                 
                                 
                                 var selected = $('#grid').find('.selected');
                                 
                                 selected.removeClass('selected');
                                 $('#' + state[7][0] + '-' + state[7][1]).addClass('selected');
                                 
                             }
                        });    
            t = setTimeout(updateGrid,500);

        }
        updateGrid();
       
	$('#grid').delegate('.cell', 'click', function(ev) {
                
                if(locked){
                    return;
                }
		var selected = $('#grid').find('.selected'),
			clicked = $(this);
		
		//if there is a robot on the grid that you clicked
		if(clicked.has('.robot').length !== 0) {
                        
                        //forward = true;
                        clearTimeout(t);
			selected.removeClass('selected');
			clicked.addClass('selected');
                        
                        var sel = clicked.attr('id').split('-');
                        
                        $.ajax({
                             type: "POST",
                             url: "changeGrid.php",
                             data: {'function': 'updateSelected',
                                    'i': sel[0],
                                    'j': sel[1]
                             },
                             //dataType: "json",
                             success: function(data) {
                                 updateGrid();
                             }
                        });
		} else if(selected.length !== 0) {
                        forward = true;
                        clearTimeout(t);
			var pos = selected.attr('id').split('-'),
				dir = clicked.attr('id').split('-');
			dir[0] = parseInt(dir[0]);
			dir[1] = parseInt(dir[1]);
			pos[0] = parseInt(pos[0]);
			pos[1] = parseInt(pos[1]);
			
			var colDiff = dir[1]-pos[1],
				rowDiff = dir[0]-pos[0];
				
			var until, unit;
			if(rowDiff === 0) {
				if(colDiff < 0) {
					until = ['b-w', 'b-nw', 'b-sw', 'b-we'];
					unit = [0, -1];
				} else if(colDiff > 0) {
					until = ['b-e', 'b-ne', 'b-se', 'b-we'];
					unit = [0, 1];
				}
			} else if(colDiff === 0) {
				if(rowDiff < 0) {
					until = ['b-n', 'b-nw', 'b-ne', 'b-ns'];
					unit = [-1, 0];
				} else if(rowDiff > 0) {
					until = ['b-s', 'b-sw', 'b-se', 'b-ns'];
					unit = [1, 0];
				}
			}
			
			if(!until || !unit)
				return;
			
			var blocked = false;
			do {
				var tile = $('#' + pos.join('-'));
				
				if(tile.length === 0)
					return;
				
				blocked = false;
				if(tile.attr('id') !== selected.attr('id') && 
						tile.has('.robot').length !== 0) {
					pos[0] -= unit[0];
					pos[1] -= unit[1];
					break;
				} else
					for(var i in until)
						if(tile.hasClass(until[i]))
							blocked = true;
				
				if(!blocked) {
					pos[0] += unit[0];
					pos[1] += unit[1];
				}
			} while(!blocked && tile !== undefined);
			
                        //while (updating){}
                        
			var newPos = $('#' + pos.join('-'));
			newPos.prepend(selected.find('.robot'));
			selected.removeClass('selected');
			newPos.addClass('selected');
			
			var steps = $('#steps');
			steps.text(parseInt(steps.text())+1);
                        
                        
                        $.ajax({
                             type: "POST",
                             url: "changeGrid.php",
                             data: {'function': 'updateState',
                                    'r': newPos.find('.robot').attr('class'),
                                    'i': pos[0],
                                    'j': pos[1]
                             },
                             //dataType: "json",
                             success: function(data) {
                                 updateGrid();
                             }
                        });
		}
                   
	});
	
	var calcHelpLayer = function() {
		var width = $(document).width();
		var targetPos = $('#7-7').offset();
		$('#targetHelp').css('left', 'auto').css('right', width-targetPos.left+8).css('top', targetPos.top+2);
		
		var stepsPos = $('#7-8').offset();
		$('#stepsHelp').css('left', stepsPos.left+35).css('top', stepsPos.top+2);
		
		var timerPos = $('#8-7').offset();
		$('#timerHelp').css('left', timerPos.left-13).css('top', timerPos.top+33);
		
		var gridPos = $('#grid').offset();
		$('#instructions').css('left', 'auto').css('right', width-gridPos.left+20)
			.css('top', gridPos.top+50);
		$('#controls').css('left', gridPos.left+$('#grid').width()+20)
			.css('top', gridPos.top+100);
	};
	$(window).resize(calcHelpLayer);
	calcHelpLayer();
	
	$('#helpBtn').toggle(
	function() {
		$(this).addClass('helpBtnActive');
	},
	function() {
		$(this).removeClass('helpBtnActive');
	});
	$('#helpBtn').hover(
	function() {
		$('#helpLayer').show();
	},
	function() {
		if(!$(this).hasClass('helpBtnActive'))
		$('#helpLayer').hide();
	});
}
