import { blocks, createElement } from '@frontkom/gutenberg';

const { registerBlockType } = blocks;
console.log('register block');

registerBlockType('drupal/block', {
  title: 'Drupal Block',
  icon: 'welcome-widgets-menus',
  category: 'common',
  // attributes: {
  //   content: {
  //     type: 'array',
  //     source: 'children',
  //     selector: 'p',
  //   },
  //   alignment: {
  //     type: 'string',
  //   },
  // },
  edit({ className }) {
    return createElement(
      'p',
      {
        className,
        value: 'Drupal Block - from editor.',
      }
    );
  },
  save({ className }) {
    return createElement(
      'p',
      {
        className: className,
        value: 'Drupal Block - from frontend.'
      }
    );
  },
});
