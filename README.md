# PersonalProjects
Este repositorio almacena scripts y archivos creados por mi, con el fin de tener un registro de los proyectos en los cuales trabajo. 


Los Requisitos para ejecutar este script son los siguientes:

Windows:
-Api-key y Token (Luego de su obtención, ingresar los datos en el "archivo_de_config.ini").
-Apache y Mysql (Recomendablemente Xampp, luego de instalarlos ingrese los datos de la conexión en  el "archivo_de_config.ini").
-PHP 7.2 (Viene incluido en Xampp).
-Importar Base de Datos.
-PhpMyAdmin (Se utiliza para manejar la base de datos).
-Crear una carpeta en el directorio de ScriptV llamada "Logs", en esta carpeta se almacenará el registro de cada dato que se almacena en la Base De Datos por cada ejecución.

Linux:
-Api-Key y Token (Luego de su obtención, ingresar los datos en el "archivo_de_config.ini").
-Mysql .
-PHP 7.2.
-Importar Base de Datos.
-Crear una carpeta en el directorio de ScriptV llamada "Logs", en esta carpeta se almacenará el registro de cada dato que se almacena en la Base De Datos por cada ejecución.


Intrucciones:
Windows: 
  1.- En phpmyadmin crear una base de datos con el nombre que usted desee, sin embargo luego de crear su base de datos, usted debe ingresar al archivo "estructura_bd" y asignarle el nombre de la base de datos que acaba de crear a la variable "Base de Datos:".
  2.- En phpMyAdmin posicionese en la base de datos que acaba de crear e importe el archivo "bd_estructura.sql".
  3.- Rellene los datos que utiliza el "archivo_de_confi.ini".
  4.- Cree una carpeta llamada "Logs". Esta carpeta almacena el registro de cada dato que se cargo en la base de datos, si ocurre un error, tambien será registrado.
  5.- Ejecute el Script.
  6.- Revise los Datos y el Logs.
  
  Linux: 
  1.- Crear una base de datos y asignarle el nombre de la base de datos que acaba de crear a la variable "Base de Datos:".
  2.- Importar la base de datos, especificamente el archivo "bd_estructura.sql".
  3.- Rellene los datos que utiliza el "archivo_de_confi.ini".
  4.- Cree una carpeta llamada "logs". Esta carpeta almacena el registro de cada dato que se cargo en la base de datos, si ocurre un error, tambien será registrado.
  5.- Ejecute el Script.
  6.- Revise los Datos y el Logs.
