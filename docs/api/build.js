const process       = require('process');
const fs            = require('fs');
const glob          = require('glob');
const path          = require('path');
const toOpenApi     = require('json-schema-to-openapi-schema');
const $RefParser    = require('json-schema-ref-parser');

const sourceSchemaDir = path.resolve(__dirname, './../../schemata/');
const buildSchemaDir = path.resolve(__dirname, './build');
const jsonSchemaSuffix = '.schema.json';
const openApiSchemaSuffix = '.schema.openapi';

glob(sourceSchemaDir + '/*' + jsonSchemaSuffix, function(err, files) {

  files.forEach(function(file) {
    $RefParser.dereference(file, function(err, schema) {
        if (err) {
            console.error(err);
            process.exit(1);
        }
        else {
            // `schema` is just a normal JavaScript object that contains your entire JSON Schema,
            // including referenced files, combined into a single object

            // console.log(JSON.stringify(toOpenApi(schema)));
            var outputFile = file
              .replace(sourceSchemaDir, buildSchemaDir)
              .replace(jsonSchemaSuffix, openApiSchemaSuffix)
              ;

            console.log(outputFile);

            fs.writeFile(outputFile, JSON.stringify(toOpenApi(schema)), function(err) {
              if (err) {
                console.error(err);
                process.exit(2);
              }
            });
        }
    });

  });
});
