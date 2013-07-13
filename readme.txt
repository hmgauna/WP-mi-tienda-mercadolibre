=== Mi Tienda MercadoLibre ===
Author: Hernan Matias Gauna
Tags: mercadolibre, ecommerce, products, integration, tienda, store
Requires at least: 2.8
Tested up to: 3.4.5
Stable tag: 0.1

Add your products catalog from MercadoLibre to your WordPress powered website without effort.

== Description ==

Displays a gallery with all your products currently published in MercadoLibre. 

== Installation ==

1. Upload `tienda-mercadolibre.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Insert the shortcode in the page where you want to show your products, in this way: `[tiendaml username=""]`. You must put your MercadoLibre username between the quotes to make it work, as, for example `[tiendaml username="my_mercadolibre_user"]` where "my_mercadolibre_user" should be replaced by your username, always between quotes.

== Frequently Asked Questions ==

* How to use it?

Simply use the shortcode `[tiendaml username='']` in the page you want to show your products on. For example, you may create a page, call it Products and just put in the content the shortcode.

* What is my MercadoLibre user name?

It is the same user name you use to login to MercadoLibre.

* How to show more or less products per page?

Just add the `productos_por_pagina` attribute to the shortcode in this way `[tiendaml username='' productos_por_pagina='12']`. Any number can be used, but since the plugin use a 3 column grid, it is recommended to use multiples of 3.


== Changelog ==
= 1.0 =
* Stable version.

