import Vue from 'vue'

import BaseModal from './modal/BaseModal.vue'
import BaseLoader from './BaseLoader.vue'

import BaseCustomInput from './BaseCustomInput.vue'

import TaxSelectPopup from './popup/TaxSelectPopup.vue'

import BasePage from './BasePage.vue'

import GlobalSearch from '../GlobalSearch.vue'



Vue.component('base-modal', BaseModal)
Vue.component('global-search', GlobalSearch)

Vue.component('base-page', BasePage)


Vue.component('base-loader', BaseLoader)

Vue.component('base-custom-input', BaseCustomInput)

Vue.component('tax-select-popup', TaxSelectPopup)


