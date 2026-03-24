<?php
namespace Codetrappers\CodetrappersAiSchemaBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CodetrappersAiSchemaBuilderPlugin {
	const OPTION_KEY = 'codetrappers-ai-schema-builder_settings';

	public function boot() {
		add_action( 'init', array( $this, 'register_post_meta' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'render_admin_notice' ) );
        add_action( 'admin_menu', array( $this, 'register_ai_page' ) );
        add_action( 'admin_post_codetrappers_ai_schema_builder_generate', array( $this, 'handle_generation' ) );
	}

	public function register_post_meta() {
		register_post_meta(
			'',
			'_codetrappers-ai-schema-builder_status',
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
			'codetrappers-ai-schema-builder_settings',
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

		if ( ! $screen || 'settings_page_codetrappers-ai-schema-builder' === $screen->id ) {
			return;
		}

		$settings = get_option( self::OPTION_KEY, array() );

		if ( empty( $settings['enabled'] ) ) {
			return;
		}

		printf(
			'<div class="notice notice-info"><p>%s</p></div>',
			esc_html__( 'Codetrappers AI Schema Builder starter is active. Extend the bootstrap logic in includes/class-codetrappers-ai-schema-builder.php.', 'codetrappers-ai-schema-builder' )
		);
	}

    public function register_ai_page() {
        add_submenu_page(
            'options-general.php',
            __( 'Codetrappers AI Schema Builder', 'codetrappers-ai-schema-builder' ),
            __( 'Codetrappers AI Schema Builder', 'codetrappers-ai-schema-builder' ),
            'manage_options',
            'codetrappers-ai-schema-builder',
            array( $this, 'render_ai_page' )
        );
    }

    public function render_ai_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $output = get_transient( 'codetrappers-ai-schema-builder_last_output' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'Codetrappers AI Schema Builder', 'codetrappers-ai-schema-builder' ); ?></h1>
            <p><?php echo esc_html__( 'This starter page wires WordPress admin UI to a replaceable AI provider.', 'codetrappers-ai-schema-builder' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'codetrappers-ai-schema-builder_generate' ); ?>
                <input type="hidden" name="action" value="codetrappers_ai_schema_builder_generate" />
                <textarea name="prompt" class="large-text code" rows="8" placeholder="<?php echo esc_attr__( 'Enter a prompt or source content.', 'codetrappers-ai-schema-builder' ); ?>"></textarea>
                <p><button type="submit" class="button button-primary"><?php echo esc_html__( 'Generate', 'codetrappers-ai-schema-builder' ); ?></button></p>
            </form>
            <?php if ( ! empty( $output ) ) : ?>
                <h2><?php echo esc_html__( 'Latest Output', 'codetrappers-ai-schema-builder' ); ?></h2>
                <pre style="white-space: pre-wrap;"><?php echo esc_html( $output ); ?></pre>
            <?php endif; ?>
        </div>
        <?php
    }

    public function handle_generation() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to perform this action.', 'codetrappers-ai-schema-builder' ) );
        }

        check_admin_referer( 'codetrappers-ai-schema-builder_generate' );

        $prompt = isset( $_POST['prompt'] ) ? sanitize_textarea_field( wp_unslash( $_POST['prompt'] ) ) : '';
        $result = $this->generate_placeholder_response( $prompt );

        set_transient( 'codetrappers-ai-schema-builder_last_output', $result, HOUR_IN_SECONDS );

        wp_safe_redirect( admin_url( 'options-general.php?page=codetrappers-ai-schema-builder' ) );
        exit;
    }

    private function generate_placeholder_response( $prompt ) {
        $trimmed_prompt = trim( (string) $prompt );

        if ( '' === $trimmed_prompt ) {
            return __( 'No prompt was provided. Replace this placeholder with an actual AI provider call.', 'codetrappers-ai-schema-builder' );
        }

        return sprintf(
            __( 'Placeholder response for: %s', 'codetrappers-ai-schema-builder' ),
            $trimmed_prompt
        );
    }

}
