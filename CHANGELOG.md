# Changelog

## 2.1.0

 - Wire core `ErrorSchemaDescriber` into the schema describer chain so the generated OpenAPI document exposes `components.schemas.Error`
 - Resolve the `cowegis.api.version` `latest` default to the installed `cowegis/cowegis-api-bundle` version instead of emitting the literal `latest`
 - Require `cowegis/cowegis-core` `^1.1.0` and add the `composer-runtime-api` `^2.0` requirement
 - Add phpspec coverage for `SchemaAction` and `Configuration`

## 2.0.0

 - Switched configuration to YAML
