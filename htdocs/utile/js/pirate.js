// script permettant de faire voguer le bateau pirate !!
// En effet, il se déplace sur l'eau si on penche sont ordinateur.
// Fonctionen avec le capteur de position de firefox

// va chercher l'info d'orientation
window.addEventListener("MozOrientation", onMozOrientation, true);

function onMozOrientation(event) {
    var x = event.x;
	var y = event.y;
	var bateau1 = document.querySelector("#bateauPirate");
	
	angle =  Math.floor(x * 90);
	if (x < -0.05 || x > 0.05){
		bateau1.style.left = (angle*10)+'px';
	}
 }