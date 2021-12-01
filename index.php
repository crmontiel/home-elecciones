<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Elecciones HN</title>
  <link rel="stylesheet" href="./style.css">

</head>
<body>

<div class="bg-white txtcenter" id="diferenciaVotos">
	<a class="anim-shadow" href="#0" id="idUltimaHora"></a>
	<div class="load-10">
	<p>En Vivo</p>
        <div class="bar"></div>
      </div>
</div>

<div class="card bg-white">
	<a class="anim-position anim-position-0 bgx" href="#0">
		<span>XIOMARA CASTRO</span>
	</a>
	<a class=" anim-position anim-position-0 bgp" href="#0">
		<span>NASRY ASFURA</span>
	</a>
	
</div>



<div class="card bg-white" id="votosTotal">
	<a class="anim-shadow anim-shadow-0" href="#0" id="idXiomara"></a>
	<a class="anim-shadow anim-shadow-1" href="#0" id="idNasry"></a>
</div>
<div class="card bg-white txtcenter" id="diferenciaVotos">
	<a class="anim-shadow" href="#0" id="idDiferencia"></a>
</div>

<div class=" bg-white datos" id="logs">
	<div class="item">
	<div class="dsa">
	<span class="it brb txtb">Ultima Actualización</span> <span class="it brb txtb">Nuevos Votos</span>  <span class="it brb txtb">Xiomara:</span> <span class="it txtb">Nasry</span>
	</div>
	</div >

	<div id="items"></div>
	
	

</div>
 
</body>

<script>
var datosLeidos=[]
var datosWeb=[]

var Xiomara=0;
var Nasry=0;

async function readTextFile(file, callback) {
	datosLeidos= await obtener();
	datosLeidos=JSON.parse(datosLeidos)
    var rawFile = new XMLHttpRequest();
    rawFile.overrideMimeType("application/json");
    rawFile.open("GET", file, true);
    rawFile.onreadystatechange = function() {
        if (rawFile.readyState === 4 && rawFile.status == "200") {
            callback(rawFile.responseText);
        }
    }
    rawFile.send(null);
}
internationalNumberFormat = new Intl.NumberFormat('en-US')

readTextFile("https://resultadosgenerales2021.cne.hn/resultados/HN/PRE.json", function(text){
    var data = JSON.parse(text);
	MostrarResultados(data)
});

setInterval(()=>{
	readTextFile("https://resultadosgenerales2021.cne.hn/resultados/HN/PRE.json", function(text){
    var data = JSON.parse(text);
	MostrarResultados(data)
});
},60000)



function MostrarResultados(data){
	Xiomara= data.resultados.filter(i=> i.id_candidatura==7);
	Xiomara=Xiomara[0];
	Nasry=data.resultados.filter(i=> i.id_candidatura==2);
	Nasry=Nasry[0];
	Diferencia=0;
	datosWeb=data
	

	var divTota= document.getElementById("votosTotal");
	var divDiferencia= document.getElementById("diferenciaVotos");

	var divXiomara= document.getElementById("idXiomara")
	var divNasry= document.getElementById("idNasry")
	var divUltima= document.getElementById("idUltimaHora")
	
		
		if(Xiomara.cant_votos > Nasry.cant_votos){
			divXiomara.classList.add("ganando")
			divNasry.classList.add("perdiendo")
		}else{
			divNasry.classList.add("ganando")
			divXiomara.classList.add("perdiendo")
		}

		divXiomara.text= `Total: ${internationalNumberFormat.format(Xiomara.cant_votos)}   ${Xiomara.cant_votos > Nasry.cant_votos ? '✔' : '✖'}`;
		divNasry.text=`Total: ${internationalNumberFormat.format(Nasry.cant_votos)}   ${Nasry.cant_votos > Xiomara.cant_votos ? '✔' : '✖'}` ;
		
		var d=new Date(data.hora_proceso)

		var datestring = ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" +
    	d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);

		divUltima.text=`Ultima actualización: ${data.hora_proceso}` ;
	

		if( Xiomara.cant_votos > Nasry.cant_votos ){
			Diferencia =  Xiomara.cant_votos - Nasry.cant_votos
		}else{
			Diferencia = Nasry.cant_votos -Xiomara.cant_votos
		}

	var dataDiferencia= document.getElementById("idDiferencia")
	
	dataDiferencia.text= `Diferencia de votos: ${internationalNumberFormat.format(Diferencia)}`;
	
	
	  calcula()
}

async function calcula(){

	var datosViejos= datosLeidos[datosLeidos.length-1]

	var nuevosD={
	"hora": datosWeb.hora_proceso,
	"total":Xiomara.cant_votos +  Nasry.cant_votos,
	"Xiomara":Xiomara.cant_votos,
	"nuevosXiomara": Xiomara.cant_votos - datosViejos.Xiomara,
	"Nasry":Nasry.cant_votos,
	"nuevosNasry": Nasry.cant_votos - datosViejos.Nasry
}


if(datosViejos.total < (Xiomara.cant_votos +  Nasry.cant_votos) ){
	datosLeidos.push(nuevosD);

	var myHeaders = new Headers();

	var requestOptions = {
	method: 'GET',
	headers: myHeaders,
	redirect: 'follow'
};

fetch(`https://weeza-app.herokuapp.com/api/map/ele/${JSON.stringify(nuevosD)}`, requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
	
	
}
var ins= document.getElementById("items")
	ins.remove()
var logs=  document.getElementById("logs")
var items= document.createElement("div")
	items.id="items"

for (const item of datosLeidos.reverse()) {
	
    var sp = document.createElement("div")
	
    var sp1 = document.createElement("span")
    var sp2 = document.createElement("span")
    var sp3 = document.createElement("span")
    var sp4 = document.createElement("span")
	sp.classList.add("dsa")
	sp1.classList.add("it")
	sp1.classList.add("brb")
	sp2.classList.add("it")
	sp2.classList.add("brb")
	sp3.classList.add("it")
	sp3.classList.add("brb")
	sp4.classList.add("it")
	sp1.innerText=item.hora
	
	sp2.innerText=item.nuevosXiomara + item.nuevosNasry
	sp3.innerText=item.nuevosXiomara
	sp4.innerText=item.nuevosNasry

	sp.appendChild(sp1)
	sp.appendChild(sp2)
	sp.appendChild(sp3)
	sp.appendChild(sp4)

	items.appendChild(sp)
	
}
logs.appendChild(items)

}

async function obtener(){
	var myHeaders = new Headers();
	
	var requestOptions = {
	method: 'GET',
	headers: myHeaders,
	redirect: 'follow'
	};

var dat=await  fetch(`https://weeza-app.herokuapp.com/api/map/result`, requestOptions)
  .then(response => response.text())
  .then(result => {
	  ///console.log(result)
	  datosLeidos= result
	  return result;
  }
  )
  .catch(error => console.log('error', error));

  return dat
}





</script>


</html>
