function blzSearch(wo) {
	if (wo=="F") {
		var blz = document.neueintrag.bank_code.value;
		var bank = document.neueintrag.bank.value;
		var ort = document.neueintrag.city.value;
	} else {
		return false;
	}
	if (blz=="" && bank=="" && ort=="") {
		alert("Bitte Ort,Bank oder BLZ (teilweise) eingeben");
		return false;
	}
	var f = open("search_blz.php?blz="+blz+"&ort="+ort+"&bank="+bank+"&wo="+wo,"win","width=450,height=200,left=100,top=100");
}
document.getElementById("blzsearch").innerHTML="<input type='button' value='suche Bank' onClick='blzSearch(\"F\")'>";

