import apiRequest from './api-request';
import { addQueryArgs } from './url'; 

// set the locale
window._wpDateSettings = { 
  l10n: { 
      locale: 'pt_PT',
  },
};

window.wp = {
  apiRequest,
  url: { addQueryArgs },
}
