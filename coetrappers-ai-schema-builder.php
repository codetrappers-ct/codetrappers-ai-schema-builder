<?php
/**
 * Plugin Name: Coetrappers AI Schema Builder
 * Description: Starter AI plugin for structured-data suggestions from post content.
 * Version: 0.1.0
 * Author: Coetrappers
 * License: GPL-2.0-or-later
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Text Domain: coetrappers-ai-schema-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'COETRAPPERS_AI_SCHEMA_BUILDER_VERSION', '0.1.0' );
define( 'COETRAPPERS_AI_SCHEMA_BUILDER_FILE', __FILE__ );
define( 'COETRAPPERS_AI_SCHEMA_BUILDER_PATH', plugin_dir_path( __FILE__ ) );
define( 'COETRAPPERS_AI_SCHEMA_BUILDER_URL', plugin_dir_url( __FILE__ ) );

require_once COETRAPPERS_AI_SCHEMA_BUILDER_PATH . 'includes/class-coetrappers-ai-schema-builder.php';

function coetrappers_ai_schema_builder_bootstrap() {
	$plugin = new \Coetrappers\CoetrappersAiSchemaBuilder\CoetrappersAiSchemaBuilderPlugin();
	$plugin->boot();
}

coetrappers_ai_schema_builder_bootstrap();
