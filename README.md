# Proyecto de Búsqueda de Vuelos

Este proyecto es una aplicación de búsqueda de vuelos desarrollada con Laravel para el backend y React para el frontend. A continuación, se presentan las instrucciones paso a paso para ejecutar el proyecto.

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalados los siguientes programas en tu sistema:

- [PHP](https://www.php.net/downloads) (versión 8.0 o superior)
- [Composer](https://getcomposer.org/download/)
- [Laravel](https://laravel.com/docs/11.x/installation#installation-via-composer)
- [Node.js](https://nodejs.org/en/download/)
- [npm](https://www.npmjs.com/get-npm)
-[xampp](https://www.apachefriends.org/es/index.html)
<!-- Para mejor entendimiento de la instalación de cada programa, 
recomiendo ver un video en YouTube -->

## Instalación de la base de datos 

# 1. Descarga e instala MySQL:
Ve a la página de descargas de MySQL y selecciona tu sistema operativo.
[MySQL](https://dev.mysql.com/downloads/workbench/)
Sigue las instrucciones de instalación proporcionadas en el sitio.

# 2. Crea una nueva base de datos:

Accede a MySQL a través de la línea de comandos o utilizando un cliente como MySQL Workbench.
Ejecuta el siguiente comando para crear una nueva base de datos:

<!-- CREATE DATABASE nombre_de_tu_base_de_datos; -->

# 3. Configura el usuario de MySQL:

Si es necesario, crea un nuevo usuario y otórgale permisos a la base de datos:

<!-- CREATE USER 'tu_usuario'@'localhost' IDENTIFIED BY 'tu_contraseña';
GRANT ALL PRIVILEGES ON nombre_de_tu_base_de_datos.* TO 'tu_usuario'@'localhost';
FLUSH PRIVILEGES;
 -->

## Instalación del Backend (Laravel)

# 1. Clona el repositorio del backend:
git clone https://github.com/JosephRios123/PruebaTecnica.git
cd PruebaTecnica

# 2. Instala las dependencias de Laravel:
composer install

# 3. Copia el archivo de configuración de ejemplo:

cp .env.example .env

# 4. Configura el archivo .env: Abre el archivo .env y establece los valores adecuados para la conexión a la base de datos y otras configuraciones necesarias.

# 5. Genera la clave de la aplicación:

php artisan key:generate

# 6. Ejecuta las migraciones para crear las tablas de la base de datos:

php artisan migrate

# 7. Inicia el servidor de desarrollo:

php artisan serve


## Uso de la Aplicación

Una vez que tanto el backend como el frontend estén en ejecución, puedes abrir tu navegador y navegar a `http://localhost:5173` para acceder a la aplicación. Desde allí, podrás buscar vuelos utilizando el formulario de búsqueda.
