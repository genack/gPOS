var po_servidorocupado    = "gPOS:\n\n El servidor esta ocupado,"+
                            " por favor intente mas tarde."

function iniComboLocales(cadena){
    var combolocales     = id("combolocales");
    var filas = cadena.split(";");
    for(var i = 0; i<filas.length; i++){
        var celdas = filas[i].split(",");
        var elemento = document.createElement('menuitem');
        elemento.setAttribute('label',celdas[1]);
        elemento.setAttribute('value',celdas[0]);
        //elemento.setAttribute('oncommand','BuscarCompra()');
	combolocales.appendChild(elemento);
    }
    //<menuitem label="Todos" selected="true"  />
}

function iniComboLocalSel(cadena){
    var combolocales = id("combolocalselect");
    var filas = cadena.split(";");
    for(var i = 0; i<filas.length; i++){
        var celdas = filas[i].split(",");
        var elemento = document.createElement('menuitem');
        elemento.setAttribute('label',celdas[1]);
        elemento.setAttribute('value',celdas[0]);
        //elemento.setAttribute('oncommand','BuscarMovimiento()');
        combolocales.appendChild(elemento);
    }
    //<menuitem label="Todos" selected="true"  />
}

function esValidaFecha(day,month,year){
    var dteDate;
    month=month-1;
    dteDate=new Date(year,month,day);
    return ((day==dteDate.getDate()) && 
	    (month==dteDate.getMonth()) && 
	    (year==dteDate.getFullYear()));
}

function validaFecha(fecha){
    var patron = new RegExp("^(19|20)+([0-9]{2})([-])([0-9]{1,2})([-])([0-9]{1,2})$");
    var values = fecha.split("-");

    if(fecha.search(patron)==0)

        if( esValidaFecha(values[2],values[1],values[0]) )
            return true;

    return false;
}

function trim(cadena) { 
    cadena = new String(cadena);
    for(i=0; i<cadena.length; ) { 
        if(cadena.charAt(i)==" " || cadena.charAt(i)=="\t" ) 
            cadena=cadena.substring(i+1, cadena.length); 
        else 
            break; 
    } 
    for(i=cadena.length-1; i>=0; i=cadena.length-1) { 
        if(cadena.charAt(i)==" " || cadena.charAt(i)=="\t" ) 
            cadena=cadena.substring(0,i); 
        else 
            break; 
    } 
    return cadena; 
}
 

function convertirNumLetras(number,idmoneda)
{
    var cad, millions_final_string, thousands_final_string, centenas_final_string, descriptor; 
  //number = number_format (number, 2);
    var number1=number.toString();
   //settype (number, "integer");
    var cent = number1.split(".");   
    var centavos = cent[1];
   //Mind Mod
    var number=cent[0];
   if (centavos == 0 || centavos == undefined)
   {
	centavos = "00";
   }
   if (number == 0 || number == "") 
   { // if amount = 0, then forget all about conversions, 
      centenas_final_string=" cero "; // amount is zero (cero). handle it externally, to 
      // function breakdown 
  } 
   else 
   { 
       var millions  = ObtenerParteEntDiv(number, 1000000); // first, send the millions to the string 
       number = mod(number, 1000000);           // conversion function 
      
     if (millions != 0)
      {                      
      // This condition handles the plural case 
         if (millions == 1) 
         {              // if only 1, use 'millon' (million). if 
            descriptor= " millon ";  // > than 1, use 'millones' (millions) as 
            } 
         else 
         {                           // a descriptor for this triad. 
              descriptor = " millones "; 
            } 
      } 
      else 
      {    
          descriptor = " ";                 // if 0 million then use no descriptor. 
      } 
      var millions_final_string = string_literal_conversion(millions)+descriptor; 
      thousands = ObtenerParteEntDiv(number, 1000);  // now, send the thousands to the string 
        number = mod(number, 1000);            // conversion function. 
      //print "Th:".thousands;
     if (thousands != 1) 
      {                   // This condition eliminates the descriptor 
         thousands_final_string =string_literal_conversion(thousands) + " mil "; 
       //  descriptor = " mil ";          // if there are no thousands on the amount 
      } 
      if (thousands == 1)
      {
         thousands_final_string = " mil "; 
     }
      if (thousands < 1) 
      { 
         thousands_final_string = " "; 
      } 
      // this will handle numbers between 1 and 999 which 
      // need no descriptor whatsoever. 
     centenas  = number;                     
      centenas_final_string = string_literal_conversion(centenas) ; 
   } //end if (number ==0) 

   /*if (ereg("un",centenas_final_string))
   {
     centenas_final_string = ereg_replace("","o",centenas_final_string); 
   }*/
   //finally, print the output. 

   /* Concatena los millones, miles y cientos*/
   cad = millions_final_string+thousands_final_string+centenas_final_string; 
   /* Convierte la cadena a MayÃºsculas*/
   cad = cad.toUpperCase();       
   if (centavos.length>2)
   {  
      if(centavos.substring(2,3)>= 5){
         centavos = centavos.substring(0,1)+(parseInt(centavos.substring(1,2))+1).toString();
      }   else{
	  
        centavos = centavos.substring(0,1);
      }
   }

   /* Concatena a los centavos la cadena "/100" */
   if (centavos.length==1)
   {
      centavos = centavos+"0";
   }
   centavos = centavos+ "/100"; 

   /* Asigna el tipo de moneda, para 1 = PESO, para distinto de 1 = PESOS*/
   if (number == 1)
       moneda = (idmoneda == 1)? cMoneda[1]['T']:cMoneda[2]['T'];

   else
       moneda = (idmoneda == 1)? cMoneda[1]['TP']:cMoneda[2]['TP'];

   /* Regresa el nÃºmero en cadena entre parÃ©ntesis y con tipo de moneda y la fase M.N.*/
   //Mind Mod, si se deja MIL pesos y se utiliza esta funciÃ³n para imprimir documentos
   //de caracter legal, dejar solo MIL es incorrecto, para evitar fraudes se debe de poner UM MIL pesos
   if(cad == '  MIL ')
   {
	cad=' UN MIL ';
   }
  // alert( "FINAL="+cad+moneda+centavos+" M.N.");
  return cad+" CON "+centavos+" "+moneda;
}






// Función modulo, regresa el residuo de una división 
function mod(dividendo , divisor) 
{ 
  resDiv = dividendo / divisor ;  
  parteEnt = Math.floor(resDiv);            // Obtiene la parte Entera de resDiv 
  parteFrac = resDiv - parteEnt ;      // Obtiene la parte Fraccionaria de la división
  //modulo = parteFrac * divisor;  // Regresa la parte fraccionaria * la división (modulo) 
  modulo = Math.round(parteFrac * divisor)
  return modulo; 
} 
// Fin de función mod

// Función ObtenerParteEntDiv, regresa la parte entera de una división
function ObtenerParteEntDiv(dividendo , divisor) 
{ 
  resDiv = dividendo / divisor ;  
  parteEntDiv = Math.floor(resDiv);
  return parteEntDiv; 
} 
// Fin de función ObtenerParteEntDiv

// function fraction_part, regresa la parte Fraccionaria de una cantidad
function fraction_part(dividendo , divisor) 
{ 
  resDiv = dividendo / divisor ;  
  f_part = Math.floor(resDiv); 
  return f_part; 
} 
// Fin de función fraction_part

// function string_literal conversion is the core of this program 
// converts numbers to spanish strings, handling the general special 
// cases in spanish language. 
function string_literal_conversion(number) 
{   
   // first, divide your number in hundreds, tens and units, cascadig 
   // trough subsequent divisions, using the modulus of each division 
   // for the next. 

   centenas = ObtenerParteEntDiv(number, 100); 
   number = mod(number, 100); 
   decenas = ObtenerParteEntDiv(number, 10); 
   number = mod(number, 10); 

   unidades = ObtenerParteEntDiv(number, 1); 
   number = mod(number, 1);  
   string_hundreds="";
   string_tens="";
   string_units="";
   
   // cascade trough hundreds. This will convert the hundreds part to 
   // their corresponding string in spanish.
   if(centenas == 1){
      string_hundreds = "ciento ";
   } 
   if(centenas == 2){
      string_hundreds = "doscientos ";
   }
   if(centenas == 3){
      string_hundreds = "trescientos ";
   } 
   if(centenas == 4){
      string_hundreds = "cuatrocientos ";
   } 
   if(centenas == 5){
      string_hundreds = "quinientos ";
   } 
   if(centenas == 6){
      string_hundreds = "seiscientos ";
   } 
   if(centenas == 7){
      string_hundreds = "setecientos ";
   } 
   if(centenas == 8){
      string_hundreds = "ochocientos ";
   } 
   if(centenas == 9){
      string_hundreds = "novecientos ";
   } 
 // end switch hundreds 

   // casgade trough tens. This will convert the tens part to corresponding 
   // strings in spanish. Note, however that the strings between 11 and 19 
   // are all special cases. Also 21-29 is a special case in spanish. 
   if(decenas == 1){
	   
      //Special case, depends on units for each conversion
      if(unidades == 1){
         string_tens = "once";
      }
      if(unidades == 2){
         string_tens = "doce";
      }
      if(unidades == 3){
         string_tens = "trece";
      }
      if(unidades == 4){
         string_tens = "catorce";
      }
      if(unidades == 5){
         string_tens = "quince";
      }
      if(unidades == 6){
         string_tens = "dieciseis";
      }
      if(unidades == 7){
         string_tens = "diecisiete";
      }
      if(unidades == 8){
         string_tens = "dieciocho";
      }
      if(unidades == 9){
         string_tens = "diecinueve";
      }
   } 
   //alert("STRING_TENS ="+string_tens);
   
   if(decenas == 2){
      string_tens = "veinti";
   }
   if(decenas == 3){
      string_tens = "treinta";
   }
   if(decenas == 4){
      string_tens = "cuarenta";
   }
   if(decenas == 5){
      string_tens = "cincuenta";
   }
   if(decenas == 6){
      string_tens = "sesenta";
   }
   if(decenas == 7){
      string_tens = "setenta";
   }
   if(decenas == 8){
      string_tens = "ochenta";
   }
   if(decenas == 9){
      string_tens = "noventa";
   }
    // Fin of swicth decenas

   // cascades trough units, This will convert the units part to corresponding 
   // strings in spanish. Note however that a check is being made to see wether 
   // the special cases 11-19 were used. In that case, the whole conversion of 
   // individual units is ignored since it was already made in the tens cascade. 
   if (decenas == 1) 
   { 
      string_units="";  
	  // empties the units check, since it has alredy been handled on the tens switch 
   } 
   else 
   { 
      if(unidades == 1){
         string_units = "un";
      }
      if(unidades == 2){
         string_units = "dos";
      }
      if(unidades == 3){
         string_units = "tres";
      }
      if(unidades == 4){
         string_units = "cuatro";
      }
      if(unidades == 5){
         string_units = "cinco";
      }
      if(unidades == 6){
         string_units = "seis";
      }
      if(unidades == 7){
         string_units = "siete";
      }
      if(unidades == 8){
         string_units = "ocho";
      }
      if(unidades == 9){
         string_units = "nueve";
      }
       // end switch units 
   } // end if-then-else 
//final special cases. This conditions will handle the special cases which 
//are not as general as the ones in the cascades. Basically four: 

// when you've got 100, you dont' say 'ciento' you say 'cien' 
// 'ciento' is used only for [101 >= number > 199] 
if (centenas == 1 && decenas == 0 && unidades == 0) 
{ 
   string_hundreds = "cien " ; 
}  
// when you've got 10, you don't say any of the 11-19 special 
// cases.. just say 'diez' 
if (decenas == 1 && unidades ==0) 
{ 
   string_tens = "diez " ; 
} 
// when you've got 20, you don't say 'veinti', which is used 
// only for [21 >= number > 29] 
if (decenas == 2 && unidades ==0) 
{ 
  string_tens = "veinte " ; 
} 
// for numbers >= 30, you don't use a single word such as veintiuno 
// (twenty one), you must add 'y' (and), and use two words. v.gr 31 
// 'treinta y uno' (thirty and one) 
if (decenas >=3 && unidades >=1) 
{ 
   string_tens = string_tens+" y "; 
} 
// this line gathers all the hundreds, tens and units into the final string 
// and returns it as the function value.
final_string = string_hundreds+string_tens+string_units;
return final_string ; 
} 
//end of function string_literal_conversion 
// handle some external special cases. Specially the millions, thousands 
// and hundreds descriptors. Since the same rules apply to all number triads 
// descriptions are handled outside the string conversion function, so it can 
// be re used for each triad. 

function formatDinero(numero) {
    
    var num = new Number(numero);
    num = num.toString();
    
    if(isNaN(num)) num = "0";
    
    num = Math.round(num*100)/100;
    //num = Math.round(num*10)/10;
    //more  alert(num);
    var sign = (num == (num = Math.abs(num)));
    num = num.toFixed(2);
    	var num = new Number(numero);
        num = num.toString().replace(/\$|\,/g,'');
	
        if(isNaN(num)) num = "0";
	
        var sign = (num == (num = Math.abs(num)));
        num = Math.floor(num*100+0.50000000001);
        var cents = num%100;
        num = Math.floor(num/100).toString();
	
        if(cents<10) cents = "0" + cents;
	
        for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
            num = num.substring(0,num.length-(4*i+3))+','+ num.substring(num.length-(4*i+3));
	    
            return (((sign)?'':'-') + num + '.' + cents);
    return (((sign)?'':'-') + num );   
}

function verCarritoCompras(){ 
	var subweb   = parent.document.getElementById("web");
	var url      = "vercarrito.php?modo=check";
        var mainweb  = parent.document.getElementById("WebNormal");
        var mainlist = parent.document.getElementById("WebLista");
	var deck     = parent.document.getElementById("DeckArea");  

        mainweb.setAttribute("collapsed","false");
        mainlist.setAttribute("collapsed","true");
	subweb.setAttribute("src",url);
        deck.setAttribute("selectedIndex",2);
	deck.setAttribute("collapsed","false");	 	   
}

function soloNumeros(evt,num){
    keynum = (window.event)?evt.keyCode:evt.which;
    if(keynum == 46) 
    {
        var sChar=String.fromCharCode(keynum);
        if(isNaN(num+sChar)) return false;
    }
    return (keynum <= 13 || (keynum >= 48 && keynum <= 57) || keynum == 46);
}

function soloNumerosEnteros(evt,num){
    // Backspace = 8, Enter = 13, ’0′ = 48, ’9′ = 57, ‘.’ = 46
    keynum = (window.event)?evt.keyCode:evt.which;
    if(keynum == 46) 
    {
        var sChar=String.fromCharCode(keynum);
        if(isNaN(num+sChar)) return false;
    }
    return (keynum <= 13 || (keynum >= 48 && keynum <= 57));
}

function changeProvPost(o,label){
    value = o.value;
    var ndoc = getMe('NDoc');
    var	url = "services?modo=checkndocCompra&ndoc="+ndoc.value+"&idprov="+value;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    //alert(xrequest.responseText);//0:existe 1:noexite
 }

function setProvPost(o,label){
    ProveedorPost   = label;
    IdProveedorPost = o.value;
}

function setSubsidPost(o,label){
    SubsidiarioPost   = label;
    IdSubsidiarioPost = o.value;
}

function setClientPost(o,label){
    ClientePost   = label;
    IdClientePost = o.value;
}

function setLocalPost(o,label){
    LocalPost   = label;
    IdLocalPost = o.value;
}

function selProveedorAux() { 
    var ven = "dialogWidth:" + "350" + "px;dialogHeight:" + "350" + "px";
    popupx('selproveedor.php?modo=proveedorpost',ven,'proveedorhab'); 
 }

function selClienteAux() { 
    var ven = "dialogWidth:" + "350" + "px;dialogHeight:" + "350" + "px";
    popupx('selcliente.php?modo=clientepost',ven,'clientehab'); 
 }

function selLocalAux() { 
    var ven = "dialogWidth:" + "350" + "px;dialogHeight:" + "350" + "px";
    popupx('sellocal.php?modo=localpost',ven,'localhab'); 
 }


function popupx(url,extra,tipo) {
    // window.open(url,tipo,extra);
    window.showModalDialog(url,tipo,extra);
}

function soloAlfaNumerico(e){
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz0123456789:,%-";
    especiales = [8, 13, 9, 39, 46, 35, 36];
    tecla_especial = false
    for(var i in especiales){
        if(key == especiales[i]){
            tecla_especial = true;
            break;
        }
    }
    
    if(letras.indexOf(tecla)==-1 && !tecla_especial){
        return false;
    }
}

function soloNumericoCodigoSerie(e){
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "-0123456789";
    especiales = [8, 13, 9];
    tecla_especial = false
    for(var i in especiales){
        if(key == especiales[i]){
            tecla_especial = true;
            break;
        }
    }
    
    if(letras.indexOf(tecla)==-1 && !tecla_especial){
        return false;
    }
}


function soloAlfaNumericoCodigo(e){
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "abcdefghijklmnopqrstuvwxyz0123456789";
    especiales = [8, 13, 9];
    tecla_especial = false
    for(var i in especiales){
        if(key == especiales[i]){
            tecla_especial = true;
            break;
        }
    }
    
    if(letras.indexOf(tecla)==-1 && !tecla_especial){
        return false;
    }
}

function unique(arr) {
    var i,len=arr.length,out=[],obj={};
    for (i=0;i<len;i++) {
	obj[arr[i]]=0;
    }
    for (i in obj) {
	out.push(i);
    }
    return out;
}

function comparaFechas(Fecha1, Fecha2) {
    // Fecha 1 = yyyy,mm,dd hh:mm:ss
    var f1 =  new Date(Fecha1);
    var f2 =  new Date(Fecha2);
    return f1.getTime() - f2.getTime();
}

function convertToUpperCase(xthis){
    xthis.value = xthis.value.toUpperCase();
}

function compararIgualdadFechas(Fecha1, Fecha2) {
    // Fecha 1 = yyyy,mm,dd hh:mm:ss
    var f1 =  new Date(Fecha1);
    var f2 =  new Date(Fecha2);
    var resto =  f1.getTime() - f2.getTime();
    
    if(resto == 0) return false;
    else return true;
}

