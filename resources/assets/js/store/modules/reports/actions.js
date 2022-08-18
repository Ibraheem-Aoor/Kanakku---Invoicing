import * as types from './mutation-types'

export const getSalesReports = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/reports/sales/customers/list/${params.unique_hash}`, { params })
        .then((response) => {
          commit(types.SET_SALES_REPORTS_DATA, response.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }

  export const getSalesItemsReports = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/reports/sales/items/list/${params.unique_hash}`, { params })
        .then((response) => {
          commit(types.SET_SALES_REPORTS_DATA, response.data)
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }

  export const getProfitLossReport = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/reports/profit-loss/list/${params.unique_hash}`, { params })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }

  export const getExpensesReport = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/reports/expenses/list/${params.unique_hash}`, { params })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }

  export const getTaxReport = ({ commit, dispatch, state }, params) => {
    return new Promise((resolve, reject) => {
      window.axios
        .get(`/reports/tax-summary/list/${params.unique_hash}`, { params })
        .then((response) => {
          resolve(response)
        })
        .catch((err) => {
          reject(err)
        })
    })
  }