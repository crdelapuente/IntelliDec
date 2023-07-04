# IntelliDec: Plataforma de Toma de Decisiones

<p align="center">
  <img src="./public/imagenes/LogoDoradoSinFondo.png" alt="Logo 1" width="200" style="vertical-align: middle;"/>
  <img src="./public/imagenes/NombreDoradoSinFondo.png" alt="Logo 2" width="700" style="vertical-align: middle;"/>
</p>

IntelliDec es una plataforma avanzada de toma de decisiones en grupo para entornos con un número alto de alternativas construida con Laravel. Permite a los usuarios crear y administrar procesos de toma de decisiones de manera eficiente y eficaz, utilizando una variedad de criterios y obteniendo resultados basados en las preferencias de los usuarios.

_Esta plataforma ha sido desarrollada por Carlos Romero de la Puente como Trabajo de Fin de Grado de la Escuela Técnica Superior de Ingeniería Informática y Telecomunicaciones de la Universidad de Granada bajo la tutela de Juan Antonio Morente Molinera._

## Características

- Creación y administración de procesos de toma de decisiones.
- Asignación de criterios y preferencias.
- Generación de resultados basados en las preferencias.
- Foro de discusión para facilitar la colaboración y el intercambio de ideas.

## Ejecución

Para ejecutar IntelliDec en tu sistema local, sigue los siguientes pasos:

1. Clona el repositorio:
    ```
    git clone https://github.com/crdelapuente/IntelliDec.git
    ```

2. Navega a la carpeta del proyecto:
    ```
    cd IntelliDec
    ```

3. Instala las dependencias del proyecto con Composer:
    ```
    composer install
    ```

4. Copia el archivo `.env.example` a `.env`:
    ```
    cp .env.example .env
    ```

5. Genera una clave de aplicación:
    ```
    php artisan key:generate
    ```

6. Configura tu base de datos en el archivo `.env`. Asegúrate de tener instalado MySQL (o cualquier sistema de gestión de base de datos que prefieras) y de crear una base de datos para el proyecto. Asegúrate de incluir el nombre de la base de datos, el nombre de usuario y la contraseña en el archivo `.env`.

7. Ejecuta las migraciones para crear las tablas en la base de datos:
    ```
    php artisan migrate
    ```

8. Ejecuta el servidor de desarrollo local:
    ```
    php artisan serve
    ```

Ahora, deberías poder ver el proyecto ejecutándose en tu navegador en `http://localhost:8000` (o la URL que te proporciona la consola).

> Nota: Aunque este proceso de ejecución se ha realizado utilizando un sistema MacOS, el proyecto puede ser ejecutado en otros sistemas operativos, siempre que se disponga de un entorno que soporte Laravel. Para usuarios de Windows, puedes utilizar [Laravel Homestead](https://laravel.com/docs/8.x/homestead). Para usuarios de Linux y macOS, además de Homestead, también puedes configurar un stack LAMP/LEMP (Linux, Apache/Nginx, MySQL, PHP) tradicional. Todos los pasos a seguir que recomiendo se encuentran en este [videotutorial de Aprendible](https://www.youtube.com/watch?v=rQZmhqah0PQ&t=811s).

© 2023 Carlos Romero de la Puente
