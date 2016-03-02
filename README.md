# GIFcord

It's a GIF, it's Discord!

![React](https://raw.github.com/reactphp/gifsocket/master/doc/react.png)

This package creates GIFs from messages in Discord channels!

## Installation

Through [composer](http://getcomposer.org).

## Usage

First, start the example server:

    $ php run.php

Next, open up a browser and point it to `localhost:8080`. Now you can start
typing stuff into your Discord channel. Each line captured by DiscordPHP will be
converted to a GIF frame and streamed to the browser in Real Time (tm), also
known as Netscape Time (tm).

## Browser support

Good:

* IE6
* Safari
* Firefox

Bad:

* Opera

Non-existent:

* Chrome

## License

MIT, see LICENSE.

## Credits

* David Cole, for GIFcord.
* Alvaro Videla, for [gifsockets](https://github.com/videlalvaro/gifsockets)
* László Zsidi, for the [GifEncoder](http://www.phpclasses.org/package/3163-PHP-Generate-GIF-animations-from-a-set-of-GIF-images.html)
