import { data } from '@frontkom/gutenberg-js';

const { withSelect } = data;

class DrupalBlock extends wp.element.Component {
  constructor(props) {
    super(props);
  }

  render() {
    if (this.props.blockContent) {
      return (
        <div className={this.props.className} dangerouslySetInnerHTML={{__html: this.props.blockContent.html}} />
      );
    }

    return(
      <div className="loading-drupal-block">{Drupal.t('Loading')}...</div>
    );
  }
}

export default withSelect((select, props) => {
  const { getBlock } = select('drupal');
  const { id } = props;

  return {
    blockContent: getBlock(id) // `/editor/blocks/load/${blockId}`
  };
})( DrupalBlock );