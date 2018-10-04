/**
 * @file
 * Javascript behaviors for the Book module.
 */

 import './sass/admin.scss';

(function($, Drupal) {
  /**
   * Adds summaries to the book outline form.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches summary behavior to book outline forms.
   */
  Drupal.behaviors.gutenbergAdmin = {
    attach(context) {
      $('.view-reusable-blocks .views-row').click((e) => {
        $(e.currentTarget).find('input[type="checkbox"]').click();
      });
    },
  };
})(jQuery, Drupal);
