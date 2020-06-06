<?php

global $em_logger;
global $em_twig;

if ( !$em_logger ) {
    $em_logger = new \Monolog\Logger('EM');
    $em_logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(EM_PATH . 'logs/enginemailer.log', 10, \Monolog\Logger::DEBUG));
}

function log_message($level, $log, $data = array()) {
    global $em_logger;

    if ($level == "info") {
        $em_logger->info($log, $data);

    } else if ($level == "debug") {
        $em_logger->debug($log, $data);

    } else if ($level == "warning") {
        $em_logger->warning($log, $data);

    } else if ($level == "error") {
        $em_logger->error($log, $data);

    }

}

function get_twig() {
    global $em_twig;

    if (!$em_twig) {
        $params['paths'] = EM_PATH . 'templates';
        //$params['cache'] = EM_PATH . 'cache';
        $params['cache'] = false;

        $em_twig = new Twig($params);
        $em_twig->addGlobal('em_path', EM_PATH);
        $em_twig->addGlobal('em_url', EM_URL);
        $em_twig->addGlobal('em_text_domain', EM_TEXT_DOMAIN);        
        
    }

    return $em_twig;
}
