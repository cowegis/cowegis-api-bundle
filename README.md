# cowegis - Content Web GIS API Bundle

This is the base symfony bundle providing the API entrypoints of the cowegis project

## Requirements

- PHP `^8.2`

## Install

`composer require cowegis/cowegis-api-bundle`

## Schema endpoint

The `GET {prefix}/docs/schema.json` document is generated dynamically from every
installed Cowegis bundle. Responses are envelopes: `GET .../map/{mapId}` returns
`{ "map": <MapSchema>, "assets": [<Asset>] }` and the layer-data endpoints return
`{ "data": <…>, "assets": [<Asset>] }`. `info.version` defaults to the installed
`cowegis/cowegis-api-bundle` version; pin it with `cowegis.api.version`.
