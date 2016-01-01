gPOS - Gestión Puntos de Venta
==============================

gPOS es un fork de [9gestion Moda](http://sourceforge.net/projects/es9gestion/), basado en tecnologia XUL, javascript, PHP5.5 y MySQL o Mariadb.

gPOS se distribuye con licencia LGPL v2.1

Instalación
----------

1. Modifique los permisos de las siguientes carpetas

    chown apache:apache  gpos/ -Rf

    chmod 740 gpos/ -Rf

2. Mozilla a restringido la validación de los complementos no firmados desde Firefox v43. Nos vemos forzados a:

  * Escribe about:config dentro de la barra de direcciones de Firefox
  * En el campo de búsqueda escribe xpinstall.signatures.required
  * Has doble clic sobre la preferencia, o clic derecho y escoge Modificar, para cambiarla a false.

3. En su navegador Firefox dentro de la barra de direcciones escribe `http://tudominio/gpos/`.

  * Luego el navegador Firefox valida el `tudominio` remoto(*), permita ejecutar el instalador del xulremoto. Esto es solo la      primera vez que ingresas al Software.
  * Despues la preferencia xpinstall.signatures.required tenemos que cambiarla a true.

4. Borre la carpeta install por seguridad.

5. Modifique las contraseñas por defecto del usuario `Usuario : admin, Contraseña : admin`, de mantenimiento `Usuario:soporte, Contraseña: soporte`.

(*) El instalador xulremoto no funciona con XAMPP y SO Windows. Use el plugin [Remote XUL Manager](https://addons.mozilla.org/es/firefox/addon/remote-xul-manager/) para registrar `tudominio` remoto.

Migración
---------

Migrar de la versión v2.0 a v3.0.*

1. Saque copia de seguridad de su base de datos

2. Sincroniza tu instalación 

    Si usas github: `(git pull)`

    En caso contrario descargue y reempláze sus archivos.

3. Carque el fichero `esquema/update_db.sql`

    `mysql -uuser -ppass -e 'use dbname; source update_db.sql;'`


Documentación
-------------

* [Manual de usuario](http://genack.net/servicios/formacion/gpos/inicio)


Contribución
------------

* [ekiss.biz](http://ekiss.biz)  diseño de iconos, documentación.
* [genack.net](http://genack.net)  diseño y desarrollo.
