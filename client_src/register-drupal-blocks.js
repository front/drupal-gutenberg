import { data } from '@frontkom/gutenberg';
import DrupalBlock from './components/drupal-block';
import DrupalIcon from './components/drupal-icon';

const providerIcons = {
  'system': DrupalIcon, // 'admin-home',
  'user': 'admin-users',
  'views': 'media-document',
  'core' : DrupalIcon
};

export default function registerDrupalBlocks(blocks, editor) {
  return new Promise(resolve => {
    const {
      BlockAlignmentToolbar,
      BlockControls,
    }  = editor;

    const Fragment = wp.element.Fragment;

    jQuery.ajax(drupalSettings.path.baseUrl + 'editor/blocks/load')
    .done(definitions => {
      const category = {
        slug: 'drupal',
        title: Drupal.t( 'Drupal Blocks' ),
      };
    
      const categories = [
        category,
        ...data.select('core/blocks').getCategories(),
      ];

      data.dispatch( 'core/blocks' ).setCategories( categories );
      
      for (const id in definitions) {
        const definition = definitions[id];
        let block_id = `drupalblock/${id}`; // ${definition.id}
        block_id = block_id.replace(/_/g,'-');
        block_id = block_id.replace(/:/g,'-');

        blocks.registerBlockType( block_id, {
          title: `${definition.admin_label} [${definition.category}]`,
          icon: providerIcons[definition.provider] || DrupalIcon,
          category: 'drupal',
          supports: {
            html: false,
          },
          attributes: {
            blockId: {
              type: 'string'
            },
            align: {
              type: 'string'
            }
          },
          edit({attributes, className, setAttributes}) {
            const { align } = attributes;
            setAttributes( { blockId: id } );

            return (
              <Fragment>
                <BlockControls>
                  <BlockAlignmentToolbar
                    value={align}
                    onChange={nextAlign => {
                      setAttributes( { align: nextAlign } );
                    }}
                    controls={[ 'left', 'right', 'center', 'wide', 'full' ]}
                  />
                </BlockControls>
                <DrupalBlock className={className} id={id} url={`${drupalSettings.path.baseUrl}editor/blocks/load/${id}`} />
              </Fragment>
            );
          },
          save() {
            return null;
          },
        });
      }
      resolve();
    });
  });
}