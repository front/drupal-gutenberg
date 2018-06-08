const requestPaths = {
  'save-post': {
    method: 'PUT',
    regex: /\/wp\/v2\/(\w*)\/(\d*)/g,
    process: (matches, data) => {
      // console.log(data);
      // document.forms[0].submit();
      // document.getElementById('node-article-edit-form').submit();
      return new Promise((resolve, reject) => {
        resolve({
          pathType: 'save-post',
          id: matches[2],
          type: matches[1],
          title: {
            raw: document.title
          },
          content: {
            raw: data
          }
        });
      });
    }
  },
  'load-media': {
    method: 'GET',
    regex: /\/wp\/v2\/media\/(\d*)/g,
    process: (matches, data) => {
      return new Promise((resolve, reject) => {
        jQuery.ajax({
          method: 'GET',
          url: drupalSettings.path.baseUrl + 'editor/image/load/' + matches[1],
          accepts: {
            json: 'application/json, text/javascript, */*; q=0.01'
          },
        }).done(result => {
          resolve(result);
        }).fail(() => {
          reject({message: 'Error'});
        });
      });
    }
  },
  'save-media': {
    method: 'POST',
    regex: /\/wp\/v2\/media/g,
    process: (matches, data) => {
      console.log('save media', drupalSettings);
      return new Promise((resolve, reject) => {

        let file;
        for (let pair of data.entries()) {
          console.log(pair);
          if (pair[0] === 'file') {
            file = pair[1];
          }
        }
        const formData = new FormData();
        formData.append('files[fid]', file);
        formData.append('fid[fids]', '');
        formData.append('attributes[alt]', 'Test');
        formData.append('_drupal_ajax', '1');
        formData.append('form_id', jQuery('[name="form_id"]').val());
        formData.append('form_build_id', jQuery('[name="form_build_id"]').val());
        formData.append('form_token', jQuery('[name="form_token"]').val());

        jQuery.ajax({
          method: 'POST',
          url: drupalSettings.path.baseUrl + 'editor/image/upload/gutenberg',
          // url: drupalSettings.path.baseUrl + 'editor/dialog/image/gutenberg?ajax_form=0&element_parents=fid',
          data : formData,
          processData: false,
          contentType: false,
          accepts: {
            json: 'application/json, text/javascript, */*; q=0.01'
          },
        })
        .done((result) => {
          resolve(result);
        })
        .fail(() => {
          reject('Error');
        });
      });
    }
  },
  'categories': {
    method: 'GET',
    regex: /\/wp\/v2\/categories\?(.*)/g,
    process: (matches, data) => {
      return new Promise((resolve, reject) => {
        resolve('ok');
      });
    }
  },
  'users': {
    method: 'GET',
    regex: /\/wp\/v2\/users\/\?(.*)/g,
    process: (matches, data) => {
      return new Promise((resolve, reject) => {
        resolve('ok');
      });
    }
  },
  'taxonomies': {
    method: 'GET',
    regex: /\/wp\/v2\/taxonomies\?(.*)/g,
    process: (matches, data) => {
      return new Promise((resolve, reject) => {
        resolve('ok');
      });
    }
  },
  'root': {
    method: 'GET',
    regex: /^\/$/g,
    process: (matches, data) => {
      return new Promise((resolve, reject) => {
        resolve('ok');
      });
    }
  },
  'default': {
    method: 'GET',
    regex: /\/wp\/v2\/types\/node/g,
    process: () => {
      return new Promise((resolve, reject) => {
        return resolve({
          labels: {
            Document: Drupal.t('Node'),
            document: Drupal.t('Node'),
            posts: Drupal.t('Nodes'),
            extras: Drupal.t('Fields') // extra tab label in sidebar
          },
          id: 1,
          name: 'Node', rest_base: 'nodes', slug: 'node',
          supports: {
            author: false,
            comments: false, // hide discussion-panel
            'custom-fields': true,
            document: true, // * hide document tab
            editor: true,
            'media-library': false, // * hide media library
            'page-attributes': false, // hide page-attributes panel
            posts: false, // * hide posts-panel
            revisions: false,
            'template-settings': false, // * hide template-settings panel
            thumbnail: false, // featured-image panel
            title: false, // show title on editor
            extras: true,
          },
          viewable: false,
          saveable: false,
          publishable: false,
          autosaveable: false
        });
      });       
    }
  }
};

function processPath(options) {
  for (const key in requestPaths) {
    if (requestPaths.hasOwnProperty(key)) {
      const requestPath = requestPaths[key];
      requestPath.regex.lastIndex = 0;
      let matches = requestPath.regex.exec(options.path + '');

      if (matches && matches.length > 0 && (options.method || 'GET' === requestPath.method)) {
        return requestPath.process(matches, options.data);
      }
    }
  }

  // None found, return type settings.
  return new Promise((resolve, reject) => {
    return reject({
      code:	'api_handler_not_found',
      message:	'API handler not found.',
      data: {
        path: options.path,
        status: 404
      }
    });
  });
}

export default function apiRequest (options) {
  return processPath(options);
}