<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \App\models\Login AS LoginDao;

class Login{
    private $_contenedor;

    function __construct(){

    }

    public function index() {
        $extraHeader =<<<html
        <link rel="stylesheet" href="/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" href="/css/bootstrap/datatables.bootstrap.css">
        <link rel="stylesheet" href="/css/contenido/custom.min.css">
        <link rel="stylesheet" href="/css/validate/screen.css">
        <link rel="stylesheet" type="text/css" href="/librerias/vintage_flip_clock/jquery.flipcountdown.css" />

html;
        $extraFooter =<<<html
        <script src="/js/jquery.min.js"></script>
        <script src="/js/validate/jquery.validate.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        <script type="text/javascript" src="/librerias/vintage_flip_clock/jquery.flipcountdown.js"></script>

       
        <script>
        
        document.getElementById("usuario").focus();
        
        function enviar_formulario(){
           $("#btnEntrar").click();
        }
        
        function mayus(e) {
            e.value = e.value.toUpperCase();
        }
        
            $(document).ready(function(){
                $.validator.addMethod("checkUserName",function(value, element) {
                  var response = false;
                    $.ajax({
                        type:"POST",
                        async: false,
                        url: "/Login/isUserValidate",
                        data: {usuario: $("#usuario").val()},
                        success: function(data) {
                            if(data=="true"){
                                $('#availability').html('<span class="text-success glyphicon glyphicon-ok"></span>');
                                $('#btnEntrar').attr("disabled", false);
                                response = true;
                            }else{
                                $('#availability').html('<span class="text-danger glyphicon glyphicon-remove"></span>');
                                $('#btnEntrar').attr("disabled", true);
                            }
                        }
                    });

                    return response;
                },"El usuario no es correcto, o no tiene acceso al sistema, verifique. ");

                $("#login").validate({
                    rules:{
                        usuario:{
                            required: true,
                            checkUserName: true
                        },
                        password:{
                            required: true,
                        }
                    },
                    messages:{
                        usuario:{
                            required: "Este campo es requerido",
                        },
                        password:{
                            required: "Este campo es requerido",
                        }
                    }
                });

                $("#btnEntrar").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "/Login/verificarUsuario",
                        data: $("#login").serialize(),
                        success: function(response){
                            if(response!=""){
                                var usuario = jQuery.parseJSON(response);
                                if(usuario.nombre!=""){
                                    $("#login").append('<input type="hidden" name="autentication" id="autentication" value="OK"/>');
                                    $("#login").append('<input type="hidden" name="nombre" id="nombre" value="'+usuario.nombre+'"/>');
                                    $("#login").submit();
                            }else{
                                      swal("Error de autenticación ", "El usuario o contraseña son incorrectos", "error");
                               
                            }
                            }else{
                                 swal("Error de autenticación ", "El usuario o contraseña son incorrectos", "error");
                            }
                        }
                    });
                });


                /***************************************************************************************/
                $(function(){
                  var i = 1;
                  $('#retroclockbox1').flipcountdown({
                    tick:function(){
                      return i++;
                    }
                  });
                })
                /***************************************************************************************/

            });
        </script>
html;
        View::set('header',$extraHeader);
        View::set('footer',$extraFooter);
        View::render("login");
    }

    public function isUserValidate(){
        echo (count(LoginDao::getUser($_POST['usuario']))>=1)? 'true' : 'false';
    }

    public function verificarUsuario(){
        $usuario = new \stdClass();
        $usuario->_usuario = MasterDom::getData("usuario");
        $usuario->_password = MasterDom::getData("password");
        $user = LoginDao::getById($usuario);

        if (count($user)>=1) {
            $user['NOMBRE'] = utf8_encode($user['NOMBRE']);
            echo json_encode($user);
        }
    }

    public function crearSession(){
        $usuario = new \stdClass();
        $usuario->_usuario = MasterDom::getData("usuario");
        $usuario->_password = MasterDom::getData("password");
        $user = LoginDao::getById($usuario);
        session_start();
        $_SESSION['usuario'] = $user['CODIGO'];
        $_SESSION['nombre'] = $user['NOMBRE'];
        $_SESSION['puesto'] = $user['PUESTO'];
        $_SESSION['cdgco'] = $user['CDGCO'];
        $_SESSION['perfil'] = $user['PERFIL'];

        header("location: /Principal/");
    }

    public function cerrarSession(){
        unset($_SESSION);
        session_unset();
        session_destroy();
        header("Location: /Login/");
    }

}
