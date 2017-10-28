# Configuring templates

So you've got the hello world working, and you want to try a real template.

Each template has its own folder in the template directory, inside that directory Enveloper will look for a meta file ending in `.meta.yml` with the same name as the folder.

Imagine you're writing the welcome email for your onboarding process, you create a template named `new-user-welcome`.

```yml

---
# new-user-welcome/new-user-welcome.meta.yml


subject: "Welcome, {{ user.handle }}"
from: "noreply@example.com"
recipients:
  to: "{{ user.emailAddress }}"
  cc:
    template: "{{ item.name }} <{{ item.emailAddress }}>"
    with: "administrators"
content:
  html: "new-user-welcome.html.twig"
  text: "new-user-welcome.text.twig"


```

This defines a template named `new-user-welcome`, with a few templated properties. The simplest of these is `subject` as it's just a string. Some are more complex. 

Each of these templated strings uses placeholders between `{{` and `}}`, allowing them to be dynamic based on what you send. 

The templating languages that powers this is [Twig](https://twig.sensiolabs.org/), similar to Jinja in Python. 

