// datos test
let datosTest=[
    {
        "id_talla_asociacion":"1",
        "id_attribute":"1",
        codigo_size_group:"1ME2000E0A", 
        "codigo_pais":"fr",
        "talla_zalando":"22.5",
    },
    {
        "id_talla_asociacion":"2",
        "id_attribute":"2",
        codigo_size_group:"1ME2000E0A",
        "codigo_pais":"fr",
        "talla_zalando":"24.5",
    },
    {
        "id_talla_asociacion":"3",
        "id_attribute":"3",
        codigo_size_group:"1ME2000E0A",
        "codigo_pais":"fr",
        "talla_zalando":"25.5",
    },
    {
        "id_talla_asociacion":"4",
        "id_attribute":"4",
        codigo_size_group:"1ME2000E0A",
        "codigo_pais":"fr",
        "talla_zalando":"26.5",
    }
]

let paises=[];
let categoriaTallas=[];
let tallas=[];

// botones
let botonRegistrar=document.getElementById("botonRegistrar");
// let botonConsultarTodos=document.getElementById("botonConsultarTodos");
// let botonConsultar=document.getElementById("botonConsultar");
// let botonActualizar=document.getElementById("botonActualizar");
// let botonEliminar=document.getElementById("botonEliminar");
// let botonConsultarTallas=document.getElementById("botonConsultarTallas");

function registrar(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'postguardarasociacion',
            asociacion:datosTest
        },
        success: (respuesta) => {
            console.log(respuesta);
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}
function consultarTodos(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'getconsultartodo'
        },
        success: (respuesta) => {
            console.log(respuesta);
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}


function consultarTodosAtributos(){
    const linkDeControladorAtributoTalla=document.getElementById("linkDeControladorAtributoTalla").value;
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: linkDeControladorAtributoTalla, 
        data: {
            ajax: true,
            action: 'getconsultartodo'
        },
        success: (respuesta) => {
            console.log("datos attributo talla =>>> ",respuesta);
            consultarConsultarTallaPorAtributoTalla()
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}

function consultarConsultarTallaPorAtributoTalla(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'getconsultartallasprestaporatributoTalla',
            id_attribute:"1"

        },
        success: (respuesta) => {
            console.log("datos atributos =>>>> ",respuesta);
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}

function consultar(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'getconsultar',
            id_talla_asociacion:1

        },
        success: (respuesta) => {
            console.log(respuesta);
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}

function consultarEsquemasYCategorias(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'getconsultaresquemasycategorias',

        },
        success: (respuesta) => {
            console.log(respuesta);
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}

function actualizar(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'postactualizar',
            asociacion: datosTest

        },
        success: (respuesta) => {
            console.log(respuesta);
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}

function eliminar(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'geteliminar',
            id_talla_asociacion:2

        },
        success: (respuesta) => {
            console.log(respuesta);
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}

function actualizar(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'postactualizar',
            asociacion: datosTest

        },
        success: (respuesta) => {
            console.log(respuesta);
            // let datos=JSON.parse(JSON.stringify(respuesta.datos))
            // console.log("productos filtrados =>>> ",datos)
        },
        error: () => {
        }
    });
}

async function consultarPaises(){
    const linkControlador=document.getElementById("linkControlador").value;
    let paises=[];
    await $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'getconsultarpaiseszalando'
        },
        success: (respuesta) => {
            console.log(respuesta);
            let datos=JSON.parse(JSON.stringify(respuesta))
            paises=datos["respuestaServidor"]["items"]
            // consultarCategoriasTalla();
        },
        error: () => {
        }
    });
    return paises
}

function cargarPaisesZalando(paises){
    let campoPais=document.getElementById("campoPais")
    let option="<option value='null' >Seleccione</option>";
    for(let pais of paises){
        option+="<option value='"+pais.country_code+"' >"+pais.country_name+"</option>";
    }
    campoPais.innerHTML=option
}

function consultarCategoriasTalla(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'getconsultarcateogriasquetienentallazalando'
        },
        success: async (respuesta) => {
            let datos=JSON.parse(JSON.stringify(respuesta));
            let categoriaTallas=datos.respuestaServidor;
            let paises=await consultarPaises();
            cargarPaisesZalando(paises)
            console.log("categorias de tallas filtrados =>>> ",categoriaTallas);
            // consultarTodosAtributos();
        },
        error: () => {
        }
    });
}

function traerTallas(){
    const linkControlador=document.getElementById("linkControlador").value;
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: linkControlador, 
        data: {
            ajax: true,
            action: 'getconsultartallaszalando',
            codigo_pais:paises[2].country_code,
            codigo_size_group:categoriaTallas[4].codigo_size_group, 
        },
        success: (respuesta) => {
            // console.log(respuesta);
            let datos=JSON.parse(JSON.stringify(respuesta))
            categoriaTallas=datos.respuestaServidor
            console.log("tallas =>>> ",datos)
        },
        error: () => {
        }
    });
}

consultarCategoriasTalla();
botonRegistrar.addEventListener("click",registrar)
// botonConsultarTodos.addEventListener("click",consultarTodos)
// botonConsultar.addEventListener("click",consultar)
// botonActualizar.addEventListener("click",actualizar)
// botonEliminar.addEventListener("click",eliminar)
// botonConsultarTallas.addEventListener("click",traerTallas)
