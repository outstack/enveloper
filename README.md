# OutStack Enveloper

[![Build Status](https://travis-ci.org/outstack/enveloper.svg?branch=master)](https://travis-ci.org/outstack/enveloper)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/93720c538eac41c78502805bfa6c04d2)](https://www.codacy.com/app/outstack/enveloper?utm_source=github.com&utm_medium=referral&utm_content=outstack/enveloper&utm_campaign=badger)

**We've not yet reached version 1. You're still encouraged to use it, but if you find something amiss let us know. If you're not sure about something, just ask! To keep updated when we release new things, follow us on [Twitter](https://twitter.com/_outstack)**

## What is it? 

Enveloper is a small service intended to be run in your infrastucture to speed up developing and testing transactional emails in your application.

Define your templates using template files and YAML, then send messages using simple API requests. 

See [Getting Started](./docs/01-getting-started.md) and [all docs](./docs)

<img src="https://i.imgur.com/y2bhAd3.gif" alt="Enveloper GIF Demo" />

## Main features

 - Simple setup with docker.
 - Integration with most mail services like Mailgun, Mandrill, Amazon SES via SMTP
 - Attachment support
 - Simple API to send see sent messages
 - Support for Twig, MJML out of the box, [easily extensible for other languages](./docs/04-advanced-templating.md).
 - Records sent messages into [Relational DB supported by Doctrine ORM](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/platforms.html).

## Issues / Roadmap
Enveloper is currently in a workable and useful state, with minimal features. Things on the roadmap can be found at https://github.com/outstack/enveloper/issues . Those marked [High Priority](https://github.com/outstack/enveloper/issues?q=is%3Aopen+is%3Aissue+label%3A%22High+Priority%22) are likely to be worked on first. 

Create an issue if there's something you'd like to see added, and feel free to tackle any already in the list. Any help is welcomed. 
