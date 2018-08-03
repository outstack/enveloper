# Validating parameters with JSON schema

As templates become more and more complex, the required parameters needed to render an email can become large and complex too.
It's easy to omit a parameter, or forget what parameters are needed to be submitted for any given template. A mistake here, can result in
your template language throwing an error, and a 500 response.

In order to minimise this, you can optionally define a JSON schema which runtime parameters will be validated against.


Simply define your JSON schema next to your template's meta file, e.g. `hello-world/hello-world.schema.json`:

```json
{
  "$schema": "http://json-schema.org/draft-06/schema#",
  "properties": {
    "email": {
      "type": "string",
      "format": "email"
    },
    "name": {
      "type": "string"
    }
  },
  "required": ["email"]
}
```
