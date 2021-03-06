<?php

//$container['db'] = function ($c) {
//	$settings = $c->get('settings')['database'];
//	$pdo = new PDO("mysql:host=" . $settings['host'] . ";dbname=" . $settings['database_name'], $settings['user'], $settings['pass']);
//	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//	$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
//	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//	return $pdo;
//};

$container['view'] = function ($container) {
//	$settings = $container->get('settings')['views'];
//	$view = new Slim\Views\PhpRenderer($settings);
//	return $view;

	$view = new \Projek\Slim\Plates([
		'directory' => 'app/views',
		'assetPath' => 'public/assets',
		'fileExtension' => 'phtml',
		// Template extension (default: false) see: http://platesphp.com/extensions/asset/
		'timestampInFilename' => false,
	]);

	$view->setResponse($container->get('response'));

	$view->loadExtension(new Projek\Slim\PlatesExtension(
		$container->get('router'),
		$container->get('request')->getUri()
	));
//	$view->loadExtension(new \ryan\template_helper($container));
	$view->loadExtension(new League\Plates\Extension\URI($container->get('request')->getUri()->getPath()));

	return $view;
};

$container['db'] = function ($container) {
	$settings = $container['settings']['database'];
	$db = new PDO("mysql:host=" . $settings['host'] . ";dbname=" . $settings['database_name'], $settings['user'], $settings['pass']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db;
};

$container['pdo'] = function ($container) {
    $settings = $container['settings']['database'];
    $pdo = new \Slim\PDO\Database("mysql:host=" . $settings['host'] . ";dbname=" . $settings['database_name'], $settings['user'], $settings['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['session'] = function ($c) {
	return new \SlimSession\Helper;
};

$container['flash'] = function () {
	return new \Slim\Flash\Messages();
};

$container['notFoundHandler'] = function ($container) {
	return function ($request, $response) use ($container) {
		return $container['response']
			->withStatus(404)
			->withHeader('Content-Type', 'text/html')
			->write($container->view->getPlates()->render("404"));
	};
};

$container['logger'] = function($c) {
    return new Silalahi\Slim\Logger(
        [
            'path' => '.',
            'name' => 'sms_log_',
            'name_format' => 'Y-m-d',
            'extension' => '.txt'
        ]
    );
};

