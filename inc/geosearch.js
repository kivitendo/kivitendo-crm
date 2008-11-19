//document.getElementById("geosearchR").innerHTML="<br /><br /><input type='button' value='suche Ort' onClick='geoSearch(\"R\")'>";
//document.getElementById("geosearchL").innerHTML="<br /><br /><input type='button' value='suche Ort' onClick='geoSearch(\"L\")'>";
function geoSearch(wo) {
	if (wo=="R") {
		var plz = document.neueintrag.zipcode.value;
		var ort = document.neueintrag.city.value;
		var country = document.neueintrag.country.value;
	} else {
		var plz = document.neueintrag.shiptozipcode.value;
		var ort = document.neueintrag.shiptocity.value;
		var country = document.neueintrag.shiptocountry.value;
	}
	var f = open("search_geo.php?plz="+plz+"&ort="+ort+"&country="+country+"&wo="+wo,"win","width=350,height=200,left=100,top=100");
}
