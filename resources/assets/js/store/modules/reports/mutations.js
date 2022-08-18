import * as types from './mutation-types'

export default {
  [types.SET_SALES_REPORTS_DATA](state, reportsList) {
    state.salesReport = reportsList
  },
}
