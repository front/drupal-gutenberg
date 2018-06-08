import './wp-init';
import { initializeEditor, select, dispatch } from '@frontkom/gutenberg';

import './sass/index.scss';

(($, Drupal, drupalSettings, _) => {
  let editor;
  let fields = [];

  Drupal.editors.gutenberg = {
    attach: function attach(element, format) {
      initGutenberg(element).then(() => {
        // On page load always select sidebar's document tab.
        dispatch('core/edit-post').openGeneralSidebar('edit-post/document');

        $('.edit-post-header__settings').append($('.gutenberg-header-settings'));

        $('.gutenberg-full-editor').addClass('ready');
        $('#gutenberg-loading').addClass('hide');

        // Gutenberg is full of buttons which cause the form
        // to submit (no default prevent).
        $(document.forms[0]).submit((e) => {
          const selectEditor = select('core/editor');
          const dispatchEditor = dispatch('core/editor');

          dispatchEditor.savePost();
          $(element).val(selectEditor.getEditedPostContent());

          // Get the original button clicked.
          const $source = $(e.originalEvent.explicitOriginalTarget);

          // Only these buttons are allowed to submit.
          if ($source.attr('id') === 'edit-submit' ||
              $source.attr('id') === 'edit-preview' || 
              $source.attr('id') === 'edit-delete') {

            return true;
          }

          // Just stop everything.
          e.preventDefault();
          e.stopPropagation();
          return false;
        });
      });
    },

    // Editor detaching happens when changing editors and
    // when saving the node.
    detach: function detach(element, format, trigger) {
      const $textArea = $(element);
      const id = 'editor-' + $textArea.data('drupal-selector');

      // Update fields content with editor content.
      const content = select('core/editor').getEditedPostContent();
      $textArea.val(content);

      // Isn't serializing (node save)? Remove the editor.
      if (trigger !== 'serialize') {
        $('#' + id).remove();
      }
    },

    onChange: function onChange(element, callback) {
    }
  };

  /**
   * Initializes Gutenberg editor.
   * 
   * @param {DOMElement} element Target DOM element, probably a textarea.
   */
  function initGutenberg(element) {
    const $textArea = $(element);
    const id = 'editor-' + $textArea.data('drupal-selector');
    const $editor = $('<div id="' + id + '" class="gutenberg__editor"></div>');
    $editor.insertAfter($textArea);
    $textArea.hide();

    const post = { 
      content: { raw: $(element).val() },
      templates: '',
      title: { raw: document.title },
      type: 'node',
      status: 'auto-draft',
      // id: 0, // Doesn't really matters because we don't do AJAX saves.
    }

    const editorSettings = { 
      alignWide: false,
      availableTemplates: [],
      disableCustomColors: false,
      titlePlaceholder: 'Add a title here...',
    };

    window.customGutenberg = {
      events: {
        'OPEN_GENERAL_SIDEBAR': function( action, store ) {
          let tab = action.name.replace(/edit-post\//g, '');

          // Make sure node's "tabs" are in the original placeholder.
          let $tabG = $('.edit-post-sidebar .components-panel .tab');
          $('.gutenberg-sidebar').append($tabG);

          // Should move tab only when sidebar is fully generated.
          setTimeout(() => {  
            let $tabD = $('.gutenberg-sidebar .tab.' + tab);
            $('.edit-post-sidebar .components-panel').append($tabD);
          }, 0);

          $(document.body).addClass('gutenberg-sidedar-open');
        },
        'CLOSE_GENERAL_SIDEBAR': function( action, store ) {
          $(document.body).removeClass('gutenberg-sidedar-open');
          // Move tab before sidebar is "destroyed".
          $('.gutenberg-sidebar').append($('.edit-post-sidebar .components-panel .tab'));
        },
      },
      categories: [
        { slug: 'rows', title: 'Rows Blocks' },
        { slug: 'common', title: 'Common Blocks' },
        { slug: 'formatting', title: 'Formatting' },
        { slug: 'layout', title: 'Layout Elements' },
        // { slug: 'widgets', title: 'Widgets' },
        { slug: 'embed', title: 'Embeds' },
        { slug: 'shared', title: 'Shared Blocks' },
      ],
      rows: [
        { cols: [ 6, 6 ], title: 'col6 x 2', description: '2 eq columns layout' },
        { cols: [ 4, 4, 4 ], title: 'col4 x 3', description: '3 eq columns layout' },
        { cols: [ 7, 5 ], title: 'col7-col5', description: 'A col7 and a col5' },
        { cols: [ 5, 7 ], title: 'col5-col7', description: 'A col5 and a col7' },
        { cols: [ 1, 10, 1 ], title: 'col1-col10-col1', description: 'A col1, a col10 and a col1' },
        { cols: [ 2, 8, 2 ], title: 'col2-col8-col2', description: 'A col2, a col8 and a col2' },
      ],
      tabs: [
        {
          options: {
            name: 'blocks',
            title: 'Blocks',
            className: 'editor-inserter__tab',
          },
          tabScrollTop: 0,
          getItemsForTab() {
            return ( item ) => item.category !== 'embed' && item.category !== 'shared' && item.category !== 'rows';
          },
        },
        {
          options: {
            name: 'rows',
            title: 'Rows',
            className: 'editor-inserter__tab',
          },
          tabScrollTop: 0,
          getItemsForTab() {
            return ( item ) => item.category === 'rows';
          },
        },
      ],
      panels: [ 'post-status', 'articles-panel', 'settings-panel', 'last-revision' ],
      editor: {
        hideTitle: true,
        noMediaLibrary: false,
      },
    };

    return new Promise((resolve, reject) => {
      // Wait a tick for CKEditor(?) to finish its things.
      setTimeout(() => {
        initializeEditor( id, post, editorSettings );
        resolve();
      }, 0);
    });
  }
})(jQuery, Drupal, drupalSettings, _);
