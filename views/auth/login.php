<div class="contenedor login">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesión</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" 
                        id="mail" 
                        placeholder="Tu email" 
                        name="email">
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input type="password" 
                        id="password" 
                        placeholder="Tu password" 
                        name="password">
            </div>

            <input type="submit" class="boton" value="Iniciar Sesion">
        </form>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes cuenta? Obtener una</a>
            <a href="/olvide">Olvidaste tu contraseña</a>
        </div>
    </div> <!--.contenedor-sm -->
</div>