
function ZeileEinfuegen () {
var myTD1  = document.createElement("td");
var myText1 = document.createElement("input");
myText1.size = '3';
myText1.name = 'data[qty][]';
myTD1.appendChild(myText1);

var myTD2  = document.createElement("td");
var myText2 = document.createElement("input");
myText2.size ='10';
myText2.name = 'data[partnumber][]';
myTD2.appendChild(myText2);

var myTD3  = document.createElement("td");
var myText3 = document.createElement("input");
myText3.size = '70';
myText3.name = 'data[description][]';
myTD3.appendChild(myText3);

var myTR   = document.createElement("tr");
myTR.appendChild(myTD1);
myTR.appendChild(myTD2);
myTR.appendChild(myTD3);

var myTable = document.getElementById("tabelle");
myTable.appendChild(myTR);
}

