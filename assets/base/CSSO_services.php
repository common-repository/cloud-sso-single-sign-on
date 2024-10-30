<?php
global $enqueue;
global $environment_service;
global $page_builder;
global $services;
global $providers_manager;
global $action_handler;
global $supported_providers;
global $error_handler;

$enqueue             = new \CloudSingleSignOn\base\CSSO_Enqueue();
$environment_service = new \CloudSingleSignOn\base\CSSO_EnvironmentService();
$page_builder        = new \CloudSingleSignOn\base\CSSO_PageBuilder();
$providers_manager   = new \CloudSingleSignOn\base\CSSO_ProvidersManager();
$action_handler      = new \CloudSingleSignOn\base\CSSO_ActionHandler();
$supported_providers = new \CloudSingleSignOn\base\CSSO_SupportedProviders();
$error_handler       = new \CloudSingleSignOn\base\CSSO_ErrorHandler();

$services = [
	$enqueue,
	$supported_providers,
	$environment_service,
	$providers_manager,
	$action_handler,
	$page_builder,
	$error_handler
];

