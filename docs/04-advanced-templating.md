# Advanced templating

> **While Pipeprint integration is a new experimental feature, and for now Twig is still supported natively, it's likely 
  that this will become the recommended and only option.**

Developing robust emails that work well and have good support in a large amount of email clients is hard. 

A modern tool should provide enough tools to speed up this process, while also adapting to any existing workflows and
in-house templates you may already have. 

When you want to move past simple Twig templates, you should consider using our integration with [Pipeprint](https://github.com/outstack/pipeprint). 

## Configuration

To setup a simple Twig and MJML workflow, we need to add a few docker containers to our setup. Using docker composer add these
services:

    pipeprint:
        image: outstack/pipeprint
        environment:
          - 'PIPEPRINT_ENGINE_CONFIG={"twig": "http://twig", "mjml": "http://mjml"}'
        links:
            - twig
            - mjml
    twig:
        image: outstack/pipeprint-engine-twig
        restart: on-failure
    mjml:
        image: outstack/pipeprint-engine-mjml
        restart: on-failure

and add the environment variable `ENVELOPER_PIPEPRINT_URL=http://pipeprint` to enable the integration. 

Setup your template - note the filenames, these are important.

```
# mjml-example.mjml.twig
<mjml>
    <mj-body>
        <mj-container>
            <mj-text>Hello, {{ name }}</mj-text>
        </mj-container>
    </mj-body>
</mjml>
```

```
# mjml-example.meta.yml
content:
  html: "mjml-example.mjml.twig"
  text: "mjml-example.text.twig"
```

```
#mjml-example.text.twig
Hello, {{ name }}
```

## Using your favourite languages

Enveloper resolves your templates based on their filename extensions. In `mjml-example.mjml.twig`, it passes your template
through two pipeline stages. Twig first, then mjml. 

These correspond to engines as defined in the `PIPEPRINT_ENGINE_CONFIG` environment variable. 

In our config above, we have only these two engines available. Follow along with [Pipeprint](https://github.com/outstack/pipeprint)
progress to know when new languages are available or to learn how to create your own. 

For backwards compatibility and readability, `.txt` and `.html` are ignored in your filenames. 