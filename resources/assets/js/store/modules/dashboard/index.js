import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  contacts: 0,
  invoices: 0,
  estimates: 0,
  expenses: 0,
  totalDueAmount: [],
  totalDuePercentage: 0,
  isDashboardDataLoaded: false,

  weeklyInvoices: {
    days: [],
    counter: [],
  },
  invoiceAnalytics: {
      labels: [],
      value: []
  },
  invoiceData: {
      PaidCount: 0,
      unPaidCount: 0,
      partiallyPaidCount: 0
  },
  estimatesData: {
    draftCount: 0,
    sentCount: 0,
    acceptedCount: 0
  },
  invoiceChartAmount: {
    invoicedamount: 0,
    invoicePendingAmount: 0,
    invoiceReceivedAmount: 0
  },  
  chartData: {
    months: [],
    invoiceTotals: [],
    expenseTotals: [],
    netProfits: [],
    receiptTotals: [],
  },

  salesTotal: null,
  totalReceipts: null,
  totalExpenses: null,
  netProfit: null,

  dueInvoices: [],
  recentEstimates: [],
  newContacts: [],
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}
