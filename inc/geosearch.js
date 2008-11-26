function geoSearch(wo) {
	if (wo=="R") {
		var plz = document.neueintrag.zipcode.value;
		var ort = document.neueintrag.city.value;
		var country = document.neueintrag.country.value;
	} else if (wo=="P") {
		var plz = document.formular.cp_zipcode.value;
		var ort = document.formular.cp_city.value;
		var country = document.formular.cp_country.value;
	} else {
		var plz = document.neueintrag.shiptozipcode.value;
		var ort = document.neueintrag.shiptocity.value;
		var country = document.neueintrag.shiptocountry.value;
	}
	var f = open("search_geo.php?plz="+plz+"&ort="+ort+"&country="+country+"&wo="+wo,"win","width=350,height=200,left=100,top=100");
}
