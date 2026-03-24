<?php
namespace Coetrappers\CoetrappersAiSchemaBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CoetrappersAiSchemaBuilderPlugin {
	const OPTION_KEY = 'coetrappers-ai-schema-builder_settings';

	public function boot() {
		add_action( 'init', array( $this, 'register_post_meta' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'render_admin_notice' ) );
        add_action( 'admin_menu', array( $this, 'register_ai_page' ) );
        add_action( 'admin_post_coetrappers_ai_schema_builder_generate', array( $this, 'handle_generation' ) );
	}

	public function register_post_meta() {
		register_post_meta(
			'',
			'_coetrappers-ai-schema-builder_status',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	public function register_settings() {
		register_setting(
			'general',
			'coetrappers-ai-schema-builder_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(
					'provider' => 'mock',
					'model'    => 'starter-model',
				),
			)
		);
		register_setting(
			'general',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(
					'enabled' => true,
					'notes'   => 'ai, schema, seo',
				),
			)
		);
	}

	public function sanitize_settings( $settings ) {
		$settings = is_array( $settings ) ? $settings : array();

		return array(
			'enabled' => ! empty( $settings['enabled'] ),
			'notes'   => isset( $settings['notes'] ) ? sanitize_text_field( $settings['notes'] ) : '',
			'provider' => isset( $settings['provider'] ) ? sanitize_key( $settings['provider'] ) : 'mock',
			'model'    => isset( $settings['model'] ) ? sanitize_text_field( $settings['model'] ) : 'starter-model',
		);
	}

	public function render_admin_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || 'settings_page_coetrappers-ai-schema-builder' === $screen->id ) {
			return;
		}

		$settings = get_option( self::OPTION_KEY, array() );

		if ( empty( $settings['enabled'] ) ) {
			return;
		}

		printf(
			'<div class="notice notice-info"><p>%s</p></div>',
			esc_html__( 'Coetrappers AI Schema Builder starter is active. Extend the bootstrap logic in includes/class-coetrappers-ai-schema-builder.php.', 'coetrappers-ai-schema-builder' )
		);
	}

    public function register_ai_page() {
        add_submenu_page(
            'options-general.php',
            __( 'Coetrappers AI Schema Builder', 'coetrappers-ai-schema-builder' ),
            __( 'Coetrappers AI Schema Builder', 'coetrappers-ai-schema-builder' ),
            'manage_options',
            'coetrappers-ai-schema-builder',
            array( $this, 'render_ai_page' )
        );
    }

    public function render_ai_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $output = get_transient( 'coetrappers-ai-schema-builder_last_output' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'Coetrappers AI Schema Builder', 'coetrappers-ai-schema-builder' ); ?></h1>
            <p><?php echo esc_html__( 'This starter page wires WordPress admin UI to a replaceable AI provider.', 'coetrappers-ai-schema-builder' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'coetrappers-ai-schema-builder_generate' ); ?>
                <input type="hidden" name="action" value="coetrappers_ai_schema_builder_generate" />
                <textarea name="prompt" class="large-text code" rows="8" placeholder="<?php echo esc_attr__( 'Enter a prompt or source content.', 'coetrappers-ai-schema-builder' ); ?>"></textarea>
                <p><button type="submit" class="button button-primary"><?php echo esc_html__( 'Generate', 'coetrappers-ai-schema-builder' ); ?></button></p>
            </form>
            <?php if ( ! empty( $output ) ) : ?>
                <h2><?php echo esc_html__( 'Latest Output', 'coetrappers-ai-schema-builder' ); ?></h2>
                <pre style="white-space: pre-wrap;"><?php echo esc_html( $output ); ?></pre>
            <?php endif; ?>
        </div>
        <?php
    }

    public function handle_generation() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to perform this action.', 'coetrappers-ai-schema-builder' ) );
        }

        check_admin_referer( 'coetrappers-ai-schema-builder_generate' );

        $prompt = isset( $_POST['prompt'] ) ? sanitize_textarea_field( wp_unslash( $_POST['prompt'] ) ) : '';
        $result = $this->generate_placeholder_response( $prompt );

        set_transient( 'coetrappers-ai-schema-builder_last_output', $result, HOUR_IN_SECONDS );

        wp_safe_redirect( admin_url( 'options-general.php?page=coetrappers-ai-schema-builder' ) );
        exit;
    }

    private function generate_placeholder_response( $prompt ) {
        $trimmed_prompt = trim( (string) $prompt );

        if ( '' === $trimmed_prompt ) {
            return __( 'No prompt was provided. Replace this placeholder with an actual AI provider call.', 'coetrappers-ai-schema-builder' );
        }

        return sprintf(
            __( 'Placeholder response for: %s', 'coetrappers-ai-schema-builder' ),
            $trimmed_prompt
        );
    }

}
