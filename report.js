document.getElementById("dayB").onclick = function() {clicked(0)};
document.getElementById("weekB").onclick = function() {clicked(1)};
document.getElementById("monthB").onclick = function() {clicked(2)};
document.getElementById("yearB").onclick = function() {clicked(3)};

function clicked(x){
	var buttons = ["day", "week", "month", "year"];

	for(i=0; i<buttons.length; i++){
		var state = "none"
        if(i==x){
        	state = "initial";
    	}else{
    		document.getElementById(buttons[i] + "Input").value="";
    	}
        document.getElementById(buttons[i]).style.display = state;
	}
}