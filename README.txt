=== Plugin Name ===
Contributors: ingenieriamodo
Tags: woocommerce, modo, paga, pagar, pay, payments, ecommerce
Requires at least: 5.4
Tested up to: 6.6.1
Stable tag: 1.1.0
Requires PHP: 8.3.10
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integración entre MODO y WooCommerce.

== Description ==

Instalá el botón de pago de MODO en tu checkout y **aceptá todas las tarjetas de crédito y débito** bancarias.

Tus clientes pueden pagar en un click sin tener que ingresar los datos de su tarjeta, en cuotas y con todas las **promos de sus bancos**.

Además, **pagás menos con las comisiones** más competitivas del mercado.

Integrar el botón de MODO es muy fácil y rápido, hacelo vos mismo siguiendo el [paso a paso aquí](https://merchants.modo.com.ar/docs/).

Para obtener tus credenciales de MODO déjanos tus datos en el siguiente [link](https://docs.google.com/forms/d/e/1FAIpQLSeSkFiayijCrNT_AdWKo_whWbQUwwAAvvS36Q8jv6WziyCPBg/viewform).

INSTALANDO EL PLUGIN DE MODO VAS PODER:

* Ofrecer cuotas y trasladar tus promos bancarias.
* Empezá a recibir pagos más seguros de usuarios con titularidad previamente validada, reduciendo posibles fraudes o contracargos.
* Tus Clientes podrán hacer sus compras mucho más rápido teniendo sus tarjetas ya asociadas en la billetera (app MODO o app Bancaria) sin necesidad de cargarlas manualmente evitando cualquier error.

== Installation ==

Requisitos técnicos mínimos:

Para instalar este plugin deberás contar con:

* PHP 8.3 o superior.
* WordPress 6.6.1 o superior.
* WooCommerce 9.2.3 o superior.
* Poseer cuenta en MODO.

Para instalar el plugin en WooCommerce desde el repositorio, sigue estos pasos:

* Ve al menú de administración de tu tienda y allí selecciona Plugins > Agregar nuevo.
* En el buscador, escribe Paga con MODO para WooCommerce y selecciona el botón Buscar.
* Selecciona el botón Instalar que corresponda al plugin de MODO.

Para instalar el módulo desde el archivo .ZIP, sigue estos pasos:

* Ve al menú de administración y allí selecciona Plugins > Agregar nuevo.
* En la parte superior de la pantalla selecciona el botón Subir plugin.
* Selecciona el archivo .zip guardado previamente en tu computadora y selecciona el botón Instalar ahora.

== Frequently Asked Questions ==

= ¿Por qué no se puede ver MODO como método de pago en mi tienda? =

Esto puede ocurrir por varias razones relacionadas a la configuración de tu tienda, estas pueden ser:

Moneda
Debes asegurarte que la moneda configurada en tu tienda sea pesos argentinos, en caso contrario podría no mostrarse MODO como método de pago.

Credenciales
Si las credenciales configuradas en los ajustes del plugin no son las correctas, no se va a mostrar MODO como método de pago en tu tienda, para ello puedes hacer un repaso de Configuración (opens new window)y asegurarte de que los datos son correctos. Si los datos son correctos y el problema persiste, contacta con soporte.

= ¿Por qué en el panel de WooCommerce se ven canceladas algunas órdenes? =
Puede suceder que en tu panel de WooCommerce veas ordenes en estado Cancelado que no cancelaste tu, si es el caso no te preocupes, te explicamos por qué sucede.

Cuando un cliente añade un producto al carrito y realiza el pedido, se crea la intención de pago, en ese momento se crea la orden en WooCommerce en estado Pendiente de pago y se descuenta el/los producto/s de tu inventario.

En muchos casos, los clientes no finalizan el pago. Dependiendo de la configuración de tu tienda, estas órdenes cambian a estado Cancelado en un determinado tiempo.

En la sección WooCommerce > Ajustes > Productos , podrás encontrar una opción para definir el tiempo exacto en el que los pedidos en estado Pendiente de pago, cambien su estado a Cancelado.

== Screenshots ==

1. Checkout.

== Changelog ==
= 1.0.14 =
* Primera versión

= 1.0.15 =
* Cambios menores

= 1.0.16 =
* Opción para seleccionar nuevo medio de pago ante compras rechazadas

= 1.0.17 =
* Nuevo formulario de alta para comercios

= 1.0.18 =
* Cambios menores

= 1.0.19 =
* Cambios menores

= 1.0.2 =
* Mejoras en la tasa de aceptación de pagos
* Cambios menores

= 1.0.21 =
* Soportar versión de php 8.3
* Soportar versión de Wordpress 6.6.1
* Cambios menores

= 1.1.0 =
* Mejoras en el manejo de sesión