import apiFetch from './api-fetch';
import { addQueryArgs } from './url'; 

// set the locale
window._wpDateSettings = { 
  l10n: { 
    locale: 'pt_PT',
  },
};

window.wp = {
  apiFetch,
  url: { addQueryArgs },
};