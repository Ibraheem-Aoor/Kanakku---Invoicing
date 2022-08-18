import VueRouter from 'vue-router'
import Vuex from 'vuex'
import Ls from './services/ls'
import store from './store/index.js'
import Vue from 'vue'
import Vuelidate from 'vuelidate'
import money from 'v-money'
import VTooltip from 'v-tooltip'
import Transitions from 'vue2-transitions'

import VueApexCharts from 'vue-apexcharts'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import VueToastr2 from 'vue-toastr-2'
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
window.toastr = require('toastr')
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import Vuetable from 'vuetable-2'
import VuetablePagination from "vuetable-2/src/components/VuetablePagination";
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin'
import Select2 from 'v-select2-component';
import VueEditor  from "vue2-editor";
import Popper from 'vue-popperjs';
import 'vue-popperjs/dist/vue-popper.css';
import VueStepWizard from 'vue-step-wizard'
import 'vue-step-wizard/dist/vue-step-wizard.css'

/**
 * Theme
 */

/**
 * Global css plugins
 */


Vue.use(Vuelidate)

Vue.use(Transitions)

Vue.use(VueToastr2, {
  position: 'top-right',
  duration: 3000
})

Vue.use(DatePicker)

Vue.use(Vuetable)

Vue.use(VueApexCharts)

Vue.use(VueEditor);

Vue.use(Select2)

Vue.use(Popper)

Vue.use(VueStepWizard);

Vue.component('apexchart', VueApexCharts)

Vue.component('v-select', vSelect)

Vue.component('select2', Select2)

Vue.component('datepicker', DatePicker)

Vue.component('vuetable', Vuetable)

Vue.component('vuetable-pagination',VuetablePagination)

Vue.component('vuetable-field-handle',VuetableFieldMixin)

Vue.component('vue-editor',VueEditor )

window._ = require('lodash')

/**
 * Custom Directives
 */
require('./helpers/directives')

/**
 * Base Components
 */
require('./components/base')

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

window.axios = require('axios')
window.axios.defaults.withCredentials = true
window.Ls = Ls

window.axios.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest',
  'Accept': 'application/json; charset=utf-8'
}

/**
 * Interceptors
 */

window.axios.interceptors.request.use(
  function (config) {
    if (store.getters['auth/isLoggedOut']) {
      let source = window.axios.CancelToken.source()
      config.cancelToken = source.token
      setTimeout(() => {
        store.dispatch('auth/setLogoutFalse')
      }, 200)

      return config
    }
    // Do something before request is sent
    const companyId = Ls.get('selectedCompany')

    if (companyId) {
      config.headers.common['company'] = companyId
    }

    return config
  },
  function (error) {
    // Do something with request error
    return Promise.reject(error)
  }
)

/**
 * Global Axios Response Interceptor
 */
global.axios.interceptors.response.use(undefined, function (err) {
  // Do something with request error
  if (store.getters['auth/isLoggedOut']) {
    return true
  }
  if (!err.response) {
    window.toastr['error'](
      'Please check your internet connection or wait until servers are back online',
      'Network Error'
    )
  } else {
    if (
      err.response.data &&
      (err.response.statusText === 'Unauthorized' ||
        err.response.data === ' Unauthorized.')
    ) {
      // Unauthorized and log out
      window.toastr['error'](
        err.response.data.message ? err.response.data.message : 'Unauthorized'
      )
      store.dispatch('auth/logout', true)
    } else if (err.response.data.errors) {
      // Show a notification per error
      const errors = JSON.parse(JSON.stringify(err.response.data.errors))
      for (const i in errors) {
        window.toastr['error'](errors[i])
      }
    } else {
      
      // Unknown error
      window.toastr['error'](
        err.response.data.message
          ? err.response.data.message
          : err.response.data || 'Unknown error occurred',
        'Error'
      )
    }
  }
  return Promise.reject(err)
})

/**
 * Global plugins
 */
window.toastr = require('toastr')

Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(VTooltip)

// register directive v-money and component <money>
Vue.use(money, { precision: 2 })
