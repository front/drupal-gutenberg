export default function registerDrupalStore(data) {
  const { registerStore, dispatch } = data;

  const DEFAULT_STATE = {
    blocks: {},
  };
  
  return registerStore( 'drupal', {
    reducer( state = DEFAULT_STATE, action ) {
      switch ( action.type ) {
        case 'SET_BLOCK':
          return {
            ...state,
            blocks: {
              ...state.blocks,
              [ action.item ]: action.block,
            },
          };
      }
  
      return state;
    },
  
    actions: {
      setBlock( item, block ) {
        return {
          type: 'SET_BLOCK',
          item,
          block,
        };
      },
    },
  
    selectors: {
      getBlock( state, item ) {
        const { blocks } = state;
        const block = blocks[ item ];
  
        return block;
      },
    },

    resolvers: {
      async getBlock( state, item ) {
        const response = await fetch(`${drupalSettings.path.baseUrl}editor/blocks/load/${item}`);
        const block = await response.json();
        dispatch( 'drupal' ).setBlock( item, block );
        return block;
      },
    },
    
  });
}