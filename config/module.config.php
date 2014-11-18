<?php
return array(
    //@see \NpPage\ProvidesConfigId
    'np-page' => array(
        'blocks' => array(),//よく使う汎用的なものについてはここに記述しておくとよい。
        'config_resolver' => array(
            'path_stack' => array(
                // この行はサンプルモジュールへ移動する。
                //より詳しくは個々のアプリケーションで設定するべきである。
                //'sample' => __DIR__ . '/pages',
            ),
            'map' => array(
                //'NpPage\Pages\Sample'       => __DIR__ . '/pages/sample.php',
                //'error/404'               => __DIR__ . '/../view/error/404.phtml',
                //'error/index'             => __DIR__ . '/../view/error/index.phtml',
            ),
        ),
        'config_loader' => 'NpPage\Config\Loader\File',
        'service_name' => 'NpPage_Service',
        'config_loader_name' => 'NpPage_ConfigLoader',
        'config_resolver_name' => 'NpPage_ConfigResolver',
        'repository_service_name' => 'NpPage_BlockRepository',
        'blocks_service_name' => 'NpPage_BlockPluginManager',
    ),
    'service_manager' => array(
        'factories' => array(
            'NpPage_Service' => 'NpPage\Service\ServiceFactory',
            'NpPage_BlockRepository' => 'NpPage\Service\BlockRepositoryFactory',
            'NpPage_ConfigLoader' => 'NpPage\Service\ConfigLoaderFileFactory',
            'NpPage_ConfigResolver' => 'NpPage\Service\ConfigFileResolverFactory',
            'NpPage_BlockPluginManager' => 'NpPage\Service\BlockPluginManagerFactory',
        ),
    ),
);
