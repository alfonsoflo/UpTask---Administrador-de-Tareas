<div class="contenedor olvide">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recuperar tu Cuenta</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/olvide" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" 
                        id="mail" 
                        placeholder="Tu email" 
                        name="email">
            </div>


            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes cuenta? Obtener una</a>
            <a href="/">Volver a Iniciar Sesion</a>
        </div>
    </div> <!--.contenedor-sm -->
</div>