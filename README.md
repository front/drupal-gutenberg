# Drupal Gutenberg
Drupal Gutenberg brings the powerful content editing experience of Gutenberg to Drupal.

[Drupal](https://www.drupal.org/)) + [Gutenberg](https://wordpress.org/gutenberg/) is a powerful combo. Drupal 8 is a rock solid CMS framework packed with powerful admin features. Our only complaint? Drupal 8 is missing a modern UI for rich content creation. Let’s change this! 

We did a [presentation](https://docs.google.com/presentation/d/1OOTDSx4hPQaEweIrwAk8fs9UmN5nEcLeAt5VFec96ek/edit#slide=id.g19049ed2db_0_5) on DrupalCamp a while back, and have explored and even built a few different JS-powered options. But, surely, there must be some open source tools out there we could benefit from? 

Early 2017 we were introduced to a work-in-progress WordPress initiative for improved editing experience. More functionality wrapped in a smooth UI? Hundreds of hours with user testing? Decoupled? React.js? Clean output? Open source? We were hooked!

## How does it work?
Everything is a block!

True to the Drupal paradigm, all elements on a Gutenberg enabled page are (Gutenberg) blocks. Gutenberg comes with 20+ core blocks, and so does Drupal core. This means all existing Drupal blocks available in the Gutenberg UI can be inserted into a page wherever you want, alongside Gutenberg core blocks. And of course, you can extend them or build your own.

In the first release we ship blocks like:

- Drupal core blocks
- Your custom Drupal blocks (like Views)
- Heading
- Latest posts
- Paragraph with size and color options
- Auto embedded social posts
- Image gallery
- Layout blocks
- Buttons
- Etc.

![Drupal Gutenberg](https://www.frontkom.no/hubfs/drupal-page-builder.gif?t=1533202049704f "Drupal Gutenberg")

## Architecture
While Gutenberg is mainly a React app, it is not built to be CMS-agnostic (...yet; see our [Github issue from 2017](https://github.com/WordPress/gutenberg/issues/2780)). First we created a fork which used some build magic to strip away the WP specifics. After hundreds of commits, we killed this repo, and moved some of the code into a new structure for easier maintenance. 

This is the current structure:

- [Gutenberg JS](https://www.npmjs.com/package/@frontkom/gutenberg-js) - The Gutenberg stand-alone editor using the official Gutenberg plugin as a dependency
- A [Drupal module](https://www.drupal.org/sandbox/marcofernandes/2981601) using the Gutenberg JS as a dependency

Each time WordPress Gutenberg is updated, the Gutenberg JS is also updated and the Drupal module gets a new release.

Some processing is needed to allow for this. Gutenberg runs on several JS modules which are served client-side bundled on the WordPress global variable. Most of these modules were implemented with Gutenberg, but some of them are originally from Wordpress. To make Gutenberg truly CMS agnostic we had to re-implement those last ones.

After importing the @frontkom/gutenberg-js NPM package, there are 2 modules which had to be rewritten for Drupal specifically. They are api-request which originally was made to talk to WP API, but now talks to Drupal, and url which define the editor routes (new page route, edit page route, preview page route, etc). The details are well documented in the NPM package [README](https://www.npmjs.com/package/@frontkom/gutenberg-js).

The wrapping Drupal module works as a text editor that can be enabled per content type. It only requires a long text field to work it’s magic. Once enabled, it completely replaces the node edit UI for the content type. It does however play nicely with the node edit sidebar. 

We aim to reuse Drupal styling for UI elements wherever we can. We want it to feel Drupal native – without missing out on any of the Gutenberg goodness.

For the non-technical user, it’s not hard to get going. Simply install the Drupal module, and it’s all working.

## Important links
- [Gutenberg JS on NPM](https://www.npmjs.com/package/@frontkom/gutenberg-js)
- [Gutenberg on Drupal.org](https://www.drupal.org/sandbox/marcofernandes/2981601)
- [Gutenberg JS on GitHub: JavaScript only version of the WordPress Gutenberg editor](https://github.com/front/gutenberg-js)
- [Norwegian introduction post](https://www.frontkom.no/blogg/drupal-gutenberg) (English post coming later)

## Installation
To test the module, simply download it from the [Drupal Gutenberg project page](https://www.drupal.org/project/gutenberg) and enable it.

Then go to any content type edit page and enable *Gutenberg Experience*.

## Development
`npm install`

`npm run watch`
