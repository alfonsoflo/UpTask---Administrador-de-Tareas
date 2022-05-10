<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = new Usuario($_POST);
            
            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                // Verifica que el user exista
                $usuario = Usuario::where('email',$usuario->email);

                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error','El usuario no existe o no está confirmado');
                } else {
                    // El usuario existe
                    if( password_verify($_POST['password'],$usuario->password) ){
                        // debuguear('Correcto');
                        // Inicar sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;


                        // Redireccionar
                        header('Location: /dashboard');

                        debuguear($_SESSION);
                    }else {
                        Usuario::setAlerta('error','Password incorrecto');
                    }
                }               
                
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        // echo "Desde logout";
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function crear(Router $router)
    {
        $alertas = [];
        $usuario = new Usuario;



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();


            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                // debuguear($existeUsuario);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else{

                    // Hashear password

                    $usuario->hashPassword();

                    // Eliminar password2

                    unset($usuario->password2);

                    // Generar token

                    $usuario->crearToken();


                    // debuguear($usuario);
                    //Crear Nuevo Usuario
                    $resultado = $usuario->guardar();

                    // Enviar Email
                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token);
                    // debuguear($email);
                    $email->enviarConfirmacion();


                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }
        }

        // Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu Cuenta en Uptask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router)
    {
        $alertas = [];


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                // Buscar el user
                $usuario = Usuario::where('email',$usuario->email);

                if($usuario && $usuario->confirmado){
                    // Generar token
                    $usuario->crearToken();
                    unset($usuario->password2);


                    // Actualizar user
                    $usuario->guardar();

                    // Enviar mail
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    

                    // Imprimir alerta
                    Usuario::setAlerta('exito','Revisa tu Bandeja de Entrada');

                }else{
                    Usuario::setAlerta('error','El usuario no existe o no está confirmado');
                }
                // debuguear($usuario);

            }
        }

        $alertas = Usuario::getAlertas();        

        // Render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Recupera tu Cuenta en Uptask',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router)
    {
        $token = s($_GET['token']);
        $mostrar = true;

        // debuguear($token);
        if(!$token){
            header('Location: /');
        }

        // Identifica user con ese token
        $usuario = Usuario::where('token',$token);

        if(empty($usuario)){
            Usuario::setAlerta('error','Token No Válido');
            $mostrar = false;
        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Añadir nuevo password
            $usuario->sincronizar($_POST);

            // Validar password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                // Hashearemos nuevo password
                $usuario->hashPassword();
                unset($usuario->password2);


                // Eliminar token
                $usuario->token = "";

                // Guardar usuario en BD
                $resultado = $usuario->guardar();

                // Redireccionar

                if($resultado){
                    header('Location: /');
                }

                debuguear($usuario);
            }

            // debuguear($usuario);


            
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer tu Cuenta en Uptask',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router)
    {
        // echo "desde mensaje";

        // Render a la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada exitosamente'
        ]);
    }

    public static function confirmar(Router $router)
    {
        $token = s($_GET['token']);
        // debuguear($token);

        if(!$token){
            header('Location: /');
        }

        // Encontrar al usuario con este token
        $usuario = Usuario::where('token',$token);

        if(empty($usuario)){
            // No se encontró un usuario con ese token
            Usuario::setAlerta('error','Token No Válido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            unset($usuario->password2);
            $usuario->token = "";
            

            // Guardar en la BBDD
            $usuario->guardar();

            Usuario::setAlerta('exito','Cuenta comprobada correctamente');


        }

        $alertas = Usuario::getAlertas();

        // debuguear($usuario);


        // echo "desde confirmar";

        // Render a la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu Cuenta Uptask',
            'alertas' => $alertas
        ]);
    }
}
