/* eslint-disable import/first */
import './wp-init';
// import { initializeEditor, select, dispatch } from '@frontkom/gutenberg';
import { editPost, plugins, components, data } from '@frontkom/gutenberg';

import './sass/index.scss';

let el, PanelBody, PluginSidebar;

(($, Drupal, wp) => {
  Drupal.editors.gutenberg = {
    attach(element) {
      function MoreFieldsPluginSidebar() {
        return el(
          PluginSidebar,
          {
            name: 'more-fields',
            title: 'More fields',
            icon: 'forms',
            isPinnable: true,
          },
          el(
            PanelBody,
            {},
            'My sidebar content'
          )
        );
      }
      
      initGutenberg(element).then(() => {
        // On page load always select sidebar's document tab.
        data.dispatch('core/edit-post').openGeneralSidebar('edit-post/document');

        el = wp.element.createElement;
        PanelBody = components.PanelBody;
        PluginSidebar = editPost.PluginSidebar;

        plugins.registerPlugin('drupal', {icon: 'smiley', render: MoreFieldsPluginSidebar});

        setTimeout(() => {
          $('.edit-post-header__settings').append($('.gutenberg-header-settings'));
        }, 0);

        $('.gutenberg-full-editor').addClass('ready');
        $('#gutenberg-loading').addClass('hide');

        // Gutenberg is full of buttons which cause the form
        // to submit (no default prevent).
        $(document.forms[0]).submit(e => {
          const selectEditor = data.select('core/editor');
          const dispatchEditor = data.dispatch('core/editor');

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
    detach(element, format, trigger) {
      const $textArea = $(element);
      const id = 'editor-' + $textArea.data('drupal-selector');

      // Update fields content with editor content.
      const content = data.select('core/editor').getEditedPostContent();
      $textArea.val(content);

      // Isn't serializing (node save)? Remove the editor.
      if (trigger !== 'serialize') {
        $('#' + id).remove();
      }
    },

    onChange() { // element, callback
    }
  };

  /**
   * Initializes Gutenberg editor.
   * 
   * @param {DOMElement} element Target DOM element, probably a textarea.
   * @returns {Promise}
   */
  function initGutenberg(element) {
    const $textArea = $(element);
    const target = 'editor-' + $textArea.data('drupal-selector');
    const $editor = $('<div id="' + target + '" class="gutenberg__editor"></div>');
    $editor.insertAfter($textArea);
    $textArea.hide();

    wp.node = {
      content: { raw: $(element).val() },
      templates: '',
      title: { raw: document.title },
      type: 'page',
      status: 'auto-draft',
      id: 12345, // Doesn't really matters because we don't do "AJAX" saves.
    };

    const editorSettings = { 
      alignWide: true,
      availableTemplates: [],
      allowedBlockTypes: true, 
      disableCustomColors: false, 
      disablePostFormats: false,
      titlePlaceholder: Drupal.t('Add title'),
      bodyPlaceholder: Drupal.t('Write your story'),
      isRTL: false,
      autosaveInterval: 100,
      // alignWide: false,
      // availableTemplates: [],
      // disableCustomColors: false,
      // titlePlaceholder: 'Add a title here...',
    };

    window.customGutenberg = {
      events: {
        'OPEN_GENERAL_SIDEBAR': action => {
          console.log('OPEN_GENERAL_SIDEBAR', action);
          let tab = action.name.replace(/edit-post\//g, '');
          tab = tab.replace(/drupal\//g, '');

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
        'CLOSE_GENERAL_SIDEBAR': () => {
          $(document.body).removeClass('gutenberg-sidedar-open');
          // Move tab before sidebar is "destroyed".
          $('.gutenberg-sidebar').append($('.edit-post-sidebar .components-panel .tab'));
        },
        'REMOVE_BLOCKS': (action, store) => {
          console.log('REMOVE_BLOCKS', action, store);
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
            return item => item.category !== 'embed' && item.category !== 'shared' && item.category !== 'rows';
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
            return item => item.category === 'rows';
          },
        },
      ],
      panels: [ 'post-status', 'articles-panel', 'settings-panel', 'last-revision' ],
      editor: {
        hideTitle: true,
        noMediaLibrary: false,
      },
    };

    return new Promise(resolve => {
      // Wait a tick for CKEditor(?) to finish its things.
      setTimeout(() => {
        editPost.initializeEditor( target, 'page', 12345, editorSettings, {} );
        resolve();
      }, 0);
    });
  }
})(jQuery, Drupal, wp, drupalSettings, _);
