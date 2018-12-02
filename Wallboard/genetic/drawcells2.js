var canvas;
var context;
var cellSize = 11;
var circleSize = cellSize - 1;//was -2
var gridSize = 64;
var edgeX = circleSize/2+1+1;
var edgeY = circleSize/2+1+1;
var liveX=[];
var liveY=[];
var maxGeneration=900;
var running = true;
var generation = 0;
var delayTime=250;
var cellMap = Array(gridSize);

var jobQueue = new Object;

function pausecomp(millis)
{
var date = new Date();
var curDate = null;

do { curDate = new Date(); }
while(curDate-date < millis);
} 
function drawCell(cellX,cellY){

    pixX = cellX * cellSize+edgeX;
    pixY = cellY * cellSize+edgeY;

    context.moveTo(pixX,pixY);
    context.lineTo(pixX,pixY);
}
function drawGrid(){
    context.lineWidth = 1;
    context.strokeStyle = "rgb(120,120,240)";
    context.lineCap = "butt";
    for(p=0;p<=gridSize+1;p=p+1){
        context.moveTo(p*cellSize+1,1);
        context.lineTo(p*cellSize+1,cellSize*(gridSize+1)+1);

        context.moveTo(1,p*cellSize);
        context.lineTo(cellSize*(gridSize+1)+1,p*cellSize+1);
    }
    context.stroke();
}
/************************************************/
function initGenZero(){
//	$jscript = "<script>\n $rulestring \n $startstring \n var startwidth=$startwidth;\n var startheight=$startwidth;\n var startcountwidth = $startcountwidth;\nvar startcountheight = $startcountheight;\n</script>\n";
	var x;
	var y;
	var i;
	var startx = (64-startwidth*startcountwidth)/2;
	var starty = (64-startheight*startcountheight)/2;
	var endx = startx+startwidth*startcountwidth;
	var endy = starty+startheight*startcountheight;
	for(x=startx;x<endx;x+=startwidth){
		for(y=starty;y<endy;y+=startheight){
			for(i=0;i<sourcex.length;i++){
				liveX[liveX.length]=sourcex[i]+x;
				liveY[liveY.length]=sourcey[i]+y;
			}
		}
	}
}
/************************************************/
function drawPopulation(){
    context.beginPath();
    context.lineWidth = circleSize;
    context.strokeStyle = "rgb(40,40,120)";
    context.lineCap = "square";

    var i;
        for (i = 0;i<=liveY.length;i=i+1){
                 drawCell(liveX[i],liveY[i]);
        }
    context.stroke();
}

function initCellMap(){
    var i;
    var j;
    for (i=0;i<cellMap.length;i++){
        cellMap[i] = Array(gridSize);
        for (j=0;j<gridSize;j++){
            cellMap[i][j]=false;
        }
    }
}
function clearCellMap(){
    var i;
    var j;
    for (i=0;i<cellMap.length;i++){
        for (j=0;j<gridSize;j++){
            cellMap[i][j]=false;
        }
    }
}
function addToQueue(x,y){
    var name = x+' '+y;
    jobQueue[name]={x:x,y:y};
}
var adjacentX=[-1,-1,-1, 0,  0, 1, 1, 1];
var adjacentY=[-1, 0, 1,-1,  1,-1, 0, 1];
function addAdjacentCellsToQueue(x,y){
    var i;
    var newX;
    var newY;
    for(i=0;i<adjacentX.length;i++){
        newX=x+adjacentX[i];
        newY=y+adjacentY[i];
        if(newX<gridSize && newY<gridSize && newX>=0 && newY>=0){
            addToQueue(newX,newY);
        }
    }
}
function countAdjacents(x,y){
    var i;
    var newX;
    var newY;
    var count =0;
    for(i=0;i<adjacentX.length;i++){
        newX=x+adjacentX[i];
        newY=y+adjacentY[i];
        if(newX<gridSize && newY<gridSize && newX>=0 && newY>=0)
            if(cellMap[newX][newY]) 
                count++;
    }
    return count;
}
function checkLive(x,y){
    var addCount = countAdjacents(x,y);
    
    if(cellMap[x][y]) 
        addCount+=10;// if it's alive we add 10 to the rule
    
    var result= rules[addCount];
    return result;
}
/*************************************/
function doGeneration(){
    
	var i;
	var x;
	var y;
	var name;
	//draw last generation
	context.clearRect(0, 0, canvas.width, canvas.height);

	//drawGrid(); // draw the grid
	drawPopulation();

	// calculate generation

	//1) copy all the live cells to the map
	clearCellMap();
	for(i=0;i<liveY.length;i++){
		cellMap[liveX[i]][liveY[i]] = true;
	}

	//2) add all the live cells and all the cells adjacent to them to the queue; use pop to remove them from the list
	i=0;
	while(liveY.length>0){
		x=liveX.pop();
		y=liveY.pop();
		name = x+' '+y;
		jobQueue[name]={x:x,y:y};
		addAdjacentCellsToQueue(x,y);
	}
	//3) run down the queue and check every point in it to see if it lives 
	for(job in jobQueue){
		x=jobQueue[job].x;
		y=jobQueue[job].y;
		delete jobQueue[job];
		if (checkLive(x,y)){
			// 3.1) if so, add them to the live list 
			liveX.push(x);
			liveY.push(y);
		}
	}

	// increment generation and decide if we stop

	generation++;
	if(generation>=maxGeneration)running=false;
	if(liveY.length==0){
		running=false;
	//            context.clearRect(0, 0, canvas.width, canvas.height);
	}

	if (running) t=setTimeout("doGeneration()",delayTime);
	i=0;
	if(!running){
		context.clearRect(0, 0, canvas.width, canvas.height);
		drawGrid(); // draw the grid
	}
}
/*************************************/
window.onload = function() {
    // Get the canvas and the drawing context.
    canvas = document.getElementById("drawingCanvas");
    context = canvas.getContext("2d");
    
    initCellMap();
    clearCellMap();
    initGenZero();//create the first generation population 
   doGeneration()  
};
/*************************************/
/*************************************/
