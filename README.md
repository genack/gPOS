gPOS - gnu Point Of Sale
========================

Gestión Puntos de Venta

gPos es un fork de [9gestion Moda](http://sourceforge.net/projects/es9gestion/), basado en tecnologia XUL, javascript, PHP5.4 y MySQL.

gPOS se distribuye con licencia LGPL v2.1

Instalación
----------

1. Modifique los permisos de las siguientes carpetas

    chmod 777 config/

    chmod 777 install/

    chmod 777 productos_img/

    chmod 777 xulremote/

2. En su navegador firefox ingrese a `http://tudominio/gpos/`.

3. Luego el navegador firefox valida el `tudominio` remoto(*), permita ejecutar el instalador del xulremoto. Esto es solo la primera vez que ingresas al Software.

4. Borre la carpeta install por seguridad.

5. Modifique las contraseñas por defecto del local `Local:almacen, Contraseña:almacen`, del usuario `Usuario : admin, Contraseña : admin`, de mantenimiento `Usuario:soporte, Contraseña: soporte`. Otros locales registrados `local:localuno, contraseña:localuno, local:localdos, contraseña:localdos`.

(*) El instalador xulremoto no funciona con XAMPP y SO Windows. Use el plugin [Remote XUL Manager](https://addons.mozilla.org/es/firefox/addon/remote-xul-manager/) para registrar `tudominio` remoto.

Documentación
-------------

* [Manual de usuario](http://genack.net/genack/services/gpos/user_manual/inicio)


Contribución
------------

* [ekiss.biz](http://ekiss.biz)  diseño de iconos, documentación.
* [genack.net](http://genack.net)  diseño y desarrollo.
