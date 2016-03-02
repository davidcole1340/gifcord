<?php

include 'vendor/autoload.php';

echo "GIFcord\r\n";

if ($argc < 3) {
	echo "Usage: php {$argv[0]} <email> <password> [port]\r\n";
	die(1);
}

@list(, $email, $password, $port) = $argv;

if (empty($port)) {
	$port = 8080;
}

function createGifFrame(array $messages)
{
    $im = imagecreatetruecolor(1000, 800);

    imagefilledrectangle($im, 0, 0, 1000, 800, 0x000000);
    foreach ($messages as $i => $message) {
        imagestring($im, 3, 40, 20 + $i*20, $message, 0xFFFFFF);
    }

    ob_start();

    imagegif($im);
    imagedestroy($im);

    return ob_get_clean();
}

function sendEmptyFrameAfter($gifServer)
{
    return function ($request, $response) use ($gifServer) {
        $gifServer($request, $response);
        $gifServer->addFrame(createGifFrame(['']));
    };
}

$loop = \React\EventLoop\Factory::create();
$discord = new \Discord\Discord($email, $password);
$ws = new \Discord\WebSockets\WebSocket($discord, $loop);

$socket = new \React\Socket\Server($loop);
$http = new \React\Http\Server($socket);

$gifServer = new \React\Gifsocket\Server($loop);

$messages = [];
$addMessage = function ($message) use ($gifServer, &$messages) {
    $messages[] = $message;
    if (count($messages) > 36) {
        $messages = array_slice($messages, count($messages) - 36);
    }

    $frame = createGifFrame($messages);
    $gifServer->addFrame($frame);
};

$ws->on('ready', function ($d) use ($ws, $addMessage) {
	echo "Discord WebSocket is ready.\r\n";
	echo "Username: {$d->username}\r\n";
	echo "Email: {$d->email}\r\n";

	$ws->on(\Discord\WebSockets\Event::MESSAGE_CREATE, function ($message) use ($addMessage) {
		$parts = explode("\n", $message->content);
		$addMessage("{$message->full_channel->guild->name} > #{$message->full_channel->name} > {$message->author->username} > {$parts[0]}");
		unset($parts[0]);

		foreach ($parts as $part) {
			$addMessage($part);
		}
	});
});

$router = new \React\Gifsocket\Router([
	'/' => sendEmptyFrameAfter($gifServer)
]);

$http->on('request', $router);

echo "GIFcord listening on port {$port}\r\n";

$socket->listen($port);
$loop->run();