# Coetrappers AI Schema Builder

`coetrappers-ai-schema-builder` is a ai-enabled wordpress plugin for the Coetrappers project set.
Starter AI plugin for structured-data suggestions from post content.
The codebase is scaffolded to be a clean starting point, not a complete production feature.

## Project Summary

- Slug: `coetrappers-ai-schema-builder`
- Type: AI-enabled WordPress plugin
- Focus: ai, schema, seo

## What This Repository Includes

- Plugin bootstrap file with WordPress headers
- Starter admin UI for prompt submission in wp-admin
- Replaceable placeholder AI response flow
- Settings structure for provider and model configuration

## Recommended Project Structure

- `<slug>.php`: plugin bootstrap and constants
- `includes/class-<slug>.php`: admin UI and AI placeholder flow
- `composer.json`: package metadata
- `README.md`: project documentation

## Setup

- Copy the folder into `wp-content/plugins` and activate it.
- Open the generated admin page and test the placeholder generation flow.
- Replace the mock response method with a real provider integration.

## How To Extend It

- Activate `coetrappers-ai-schema-builder` from the Plugins screen.
- Use `coetrappers-ai-schema-builder.php` as the primary bootstrap file for extension work.
- Keep feature logic inside dedicated classes rather than expanding the root file.

## Development Notes

- The generated code favors readability and a low-friction starting structure.
- Credentials, provider integrations, persistence rules, and deployment concerns still need to be implemented for real use.
- Review capability checks, sanitization, and data storage choices before using any starter in production.

## Roadmap

- Integrate a real LLM provider and secure API credential storage.
- Add request logging, moderation, and rate limiting.
- Define prompt templates and structured output handling.

## WordPress Usage

- Copy the folder into `wp-content/plugins`.
- Activate the plugin from wp-admin.
- Extend the main class under `includes/` or split logic into additional classes as the plugin grows.

## Production Hardening Checklist

- Add nonce handling and permission checks for every form or action.
- Add automated tests and linting before release.
- Validate plugin behavior against the target WordPress and PHP versions.

## AI Integration Notes

- Replace the placeholder generation method with a real provider client.
- Store provider credentials securely and avoid hardcoding API keys.
- Add prompt logging, rate limits, and moderation rules before exposing AI features to content teams.
