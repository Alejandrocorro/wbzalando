<?php
include("curlController.php");
include("logger.php");
use Clases\CurlController;

class CategoriaController extends ModuleAdminController{

    private $id_idioma;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->id_idioma = $this->context->language->id;
    }

    public function init()
    {
        parent::init();
    }

    public function initContent(){
        parent::initContent();
        $esquemasDB=$this->chequearEsquemasDeHoyDB();
        if(count($esquemasDB)===0){
            $datosEsquemas=$this->consultarEsquemasDeProducto();
            if(count($datosEsquemas["esquemas_name_label"])>0){
                $respuestaResgistro=$this->registrarEsquemasDB($datosEsquemas);
            }
        }
        $linkDeControlador=$this->context->link->getAdminLink("Categoria",true);
        $variablesSmarty=[
            "linkControlador" => $linkDeControlador
        ];
        $this->context->smarty->assign($variablesSmarty);
        $this->setTemplate('/asociacion_categoria/vista.tpl');
    }

    public function chequearEsquemasDeHoyDB(){
        $fechaHoy=date("Y-m-d");
        $SQL="SELECT * FROM ps_wbzalando_esquemas WHERE fecha_registro='$fechaHoy'";
        return $this->validarRespuestaBD(Db::getInstance()->executeS($SQL));
    }

    public function consultarEsquemasDeProducto(){
        $respuestaZalando=["respuestaServidor" => [],"estatuRespuestaApi" => 0,"esquemas_name_label"=>[],"esquemas_full" => []];
        $idComerciante=Configuration::get("WB_ZALANDO_ID_COMERCIANTE");
        $endPoint=Configuration::get("WB_ZALANDO_END_POINT");
        $token=Configuration::get("WB_ZALANDO_TOKEN_ACCESO");
        $url=$endPoint."/merchants/".$idComerciante."/outlines";
        $curlController=new CurlController($url);
        $header = array(
            'Authorization: '.'Bearer '. $token
        );
        $curlController->setdatosCabezera($header);
        $respuesta=$curlController->ejecutarPeticion("get",false);
        $outline =[];
        error_log("respuesta al consultar los esquema de producto zalando =>>>>  " . var_export($respuesta["response"], true));
        if(is_object($respuesta["response"])){
            if(property_exists($respuesta["response"],"items")){
                foreach($respuesta["response"]->items as $esquema){
                    $outline[]=$esquema->name->en."-".$esquema->label;
                }
            }
            if(count($outline)>0){
                $respuestaZalando["esquemas_name_label"] = $outline;
                $respuestaZalando["esquemas_full"] = $respuesta["response"]->items;
            }
        }
        else{
            $respuestaZalando["respuestaServidor"]= $respuesta["response"];
            $respuestaZalando["estatuRespuestaApi"]= $respuesta["estado"];
        }
        return $respuestaZalando;
    }

    public function registrarEsquemasDB($datos){
        $fechaHoy=date("Y-m-d");
        $jsonEsquemasNameLabel=json_encode($datos["esquemas_name_label"]);
        $jsonEsquemasFull=json_encode($datos["esquemas_full"]);
        $SQL="INSERT INTO ps_wbzalando_esquemas(
            fecha_registro,
            esquemas_name_label,
            esquemas_full
            )
            VALUES(
                '".$fechaHoy."',
                '".$jsonEsquemasNameLabel."',
                '".$jsonEsquemasFull."'
            );";
        return Db::getInstance()->execute($SQL);
    }

    public function validarRespuestaBD($respuesta){
        if(is_array($respuesta)){
            return $respuesta;
        }
        return [];
    }

    // -------------------------------

    public function ajaxProcessPostGuardarAsociacion(){
        $respuesta_servidor=["respuestaServidor" => []];
        $datosEsquema=$this->consultarEsquema($_POST["asociacion"]["outline"]);
        $modeloProducto=$this->creacionDeModelo($datosEsquema,$_POST["asociacion"]["outline"]);
        $jsonFake=["msj" => "hola mundo"];
        // $repuestaDB=$this->registrar($_POST["asociacion"]["id_category"],$_POST["asociacion"]["outline"],$_POST["asociacion"]["outline_name"],$jsonFake);
        if(true){
            // $respuesta_servidor["respuestaServidor"]=[
            //     "mensaje" => "registro completado",
            //     "modeloProducto" => $modeloProducto,
            //     "estado" => 200
            // ];
            $respuesta_servidor["respuestaServidor"]=$modeloProducto;
        }
        else{
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "error al registrar",
                "estado" => 500
            ];
        }
        print(json_encode($respuesta_servidor));

    }

    public function registrar($idCategoria,$outline,$outline_name,$modeloJson){
        $SQL="INSERT INTO ps_wbzalando_asociacion_categoria(
            id_category,
            outline,
            outline_name,
            modelo
        )
        VALUES(
            ".$idCategoria.",
            '".$outline."',
            '".$outline_name."',
            '".json_encode($modeloJson)."'
        )";
        return Db::getInstance()->execute($SQL);
    }


    public function consultarEsquema($outline){
        $capturaEsquema=null;
        $resultEsquemas=$this->chequearEsquemasDeHoyDB();
        if(count($resultEsquemas)>0){
            $resultEsquemas[0]["esquemas_full"]=json_decode($resultEsquemas[0]["esquemas_full"]);
            foreach($resultEsquemas[0]["esquemas_full"] as $esquema){
                $esquema=(object)$esquema;
                if($outline===$esquema->label){
                    $capturaEsquema=[
                        "label" => $esquema->label,
                        "model"=> [
                            "mandatory_types" =>$esquema->tiers->model->mandatory_types,
                            "optional_types" =>$esquema->tiers->model->optional_types
                        ],
                        "config"=> [
                            "mandatory_types" =>$esquema->tiers->config->mandatory_types,
                            "optional_types" =>$esquema->tiers->config->optional_types
                        ],
                        "simple"=> [
                            "mandatory_types" =>$esquema->tiers->simple->mandatory_types,
                            "optional_types" =>$esquema->tiers->simple->optional_types
                        ]
                    ];
                    break;

                }
            }
        }
        return $capturaEsquema;
    }

    public function creacionDeModelo($esquema,$label_outline){
        $modeloBase=[
            "outline" => $label_outline,
            "product_model" =>[
                "merchant_product_model_id" => "null",
                "product_model_attributes" => [],
                "product_configs" => [
                    [
                        "merchant_product_config_id" => "null",
                        "product_config_attributes" => [
                        ],
                        "product_simples" => [
                            [
                                "merchant_product_simple_id" => "null",
                                "product_simple_attributes" => [
                                ]
                            ]
                        ]

                    ]
                ],
            ],
        ];
        // modelo
        foreach($esquema["model"]["mandatory_types"] as $propiedades_obligatorias_modelo){
            $modeloBase["product_model"]["product_model_attributes"][$propiedades_obligatorias_modelo]="";
        }
        foreach($esquema["model"]["optional_types"] as $propiedades_opcionales_modelo){
            $modeloBase["product_model"]["product_model_attributes"][$propiedades_opcionales_modelo]="";
        }
        // config
        foreach($esquema["config"]["mandatory_types"] as $propiedades_obligatorias_config){
            $modeloBase["product_model"]["product_configs"][0]["product_config_attributes"][$propiedades_obligatorias_config]="";
        }
        foreach($esquema["config"]["optional_types"] as $propiedades_opcionales_config){
            $modeloBase["product_model"]["product_configs"][0]["product_config_attributes"][$propiedades_opcionales_config]="";
        }
        // simple
        foreach($esquema["simple"]["mandatory_types"] as $propiedades_obligatorias_simple){
            $modeloBase["product_model"]["product_configs"][0]["product_simples"][0]["product_simple_attributes"][$propiedades_obligatorias_simple]="";
        }
        foreach($esquema["simple"]["optional_types"] as $propiedades_opcionales_simple){
            $modeloBase["product_model"]["product_configs"][0]["product_simples"][0]["product_simple_attributes"][$propiedades_opcionales_simple]="";
        }
        return $modeloBase;

    }

    public function ajaxProcessGetConsultarTodo(){
        $respuesta_servidor=["respuestaServidor" => []];
        $repuestaDB=$this->consultarTodo();
        if(count($repuestaDB)>0){
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "consulta completada",
                "datos" => $repuestaDB,
                "estado" => 200
            ];
        }
        else{
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "no hay asociaciones registradas",
                "estado" => 404
            ];
        }
        print(json_encode($respuesta_servidor));
    }

    public function consultarTodo(){
        // $SQL="SELECT * FROM ps_wbzalando_asociacion_categoria;";
        $SQL="
        SELECT 
        ps_wbzalando_asociacion_categoria.outline_name,
        ps_wbzalando_asociacion_categoria.id_category,
        ps_wbzalando_asociacion_categoria.outline,
        ps_category_lang.name
        FROM 
        ps_wbzalando_asociacion_categoria,
        ps_category,
        ps_category_lang,
        ps_lang
        WHERE 
        ps_category_lang.id_category=ps_wbzalando_asociacion_categoria.id_category AND 
        ps_category_lang.id_lang=".$this->id_idioma." AND 
        ps_category_lang.id_category=ps_category.id_category AND 
        ps_category_lang.id_lang=ps_lang.id_lang
        ";
        return $this->validarRespuestaBD(Db::getInstance()->executeS($SQL));
    }
    
    public function ajaxProcessGetConsultar(){
        $respuesta_servidor=["respuestaServidor" => []];
        $repuestaDB=$this->consultar($_GET["id_categoria_asociacion"]);
        if(count($repuestaDB)>0){
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "consulta completada",
                "datos" => $repuestaDB,
                "estado" => 200
            ];
        }
        else{
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "error no se ha encontrado el recuros solicitado",
                "estado" => 404
            ];
        }
        print(json_encode($respuesta_servidor));
    }

    public function consultar($id){
        // $SQL="SELECT * FROM ps_wbzalando_asociacion_categoria WHERE id_categoria_asociacion=".$id.";";
        $SQL="
        SELECT 
        ps_wbzalando_asociacion_categoria.outline_name,
        ps_wbzalando_asociacion_categoria.id_category,
        ps_wbzalando_asociacion_categoria.outline,
        ps_category_lang.name
        FROM 
        ps_wbzalando_asociacion_categoria,
        ps_category,
        ps_category_lang,
        ps_lang
        WHERE 
        ps_wbzalando_asociacion_categoria.id_categoria_asociacion=".$id." AND 
        ps_category_lang.id_category=ps_wbzalando_asociacion_categoria.id_category AND 
        ps_category_lang.id_lang=".$this->id_idioma." AND 
        ps_category_lang.id_category=ps_category.id_category AND 
        ps_category_lang.id_lang=ps_lang.id_lang ;";
        return $this->validarRespuestaBD(Db::getInstance()->executeS($SQL));
    }
    
    public function ajaxProcessGetEliminar(){
        $respuesta_servidor=["respuestaServidor" => []];
        $repuestaDB=$this->eliminar($_GET["id_categoria_asociacion"]);
        if($repuestaDB){
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "eliminación existosa",
                "estado" => 200
            ];
        }
        else{
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "error no se ha encontrado el recuros",
                "estado" => 404
            ];
        }
        print(json_encode($respuesta_servidor));
    }

    public function eliminar($id){
        $SQL="DELETE FROM ps_wbzalando_asociacion_categoria WHERE id_categoria_asociacion=".$id.";";
        return Db::getInstance()->execute($SQL);
    }

    public function ajaxProcessPostActualizarAsociacion(){
        $respuesta_servidor=["respuestaServidor" => []];
        $jsonFake=["msj" => "hola mundo"];
        $repuestaDB=$this->actualizar($_POST["asociacion"]["id_categoria_asociacion"],$_POST["asociacion"]["id_category"],$_POST["asociacion"]["outline"],$_POST["asociacion"]["outline_name"],$jsonFake);
        if($repuestaDB){
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "actualización completada",
                "estado" => 200
            ];
        }
        else{
            $respuesta_servidor["respuestaServidor"]=[
                "mensaje" => "error al actualizar",
                "estado" => 500
            ];
        }
        print(json_encode($respuesta_servidor));

    }

    public function actualizar($idAsocicaionCategoria,$idCategoria,$outline,$outline_name,$modeloJson){
        $SQL="UPDATE ps_wbzalando_asociacion_categoria SET
            id_category=".$idCategoria.",
            outline='".$outline."',
            outline_name='".$outline_name."',
            modelo='".json_encode($modeloJson)."'
            WHERE id_categoria_asociacion=".$idAsocicaionCategoria.";";
        return Db::getInstance()->execute($SQL);
    }

}