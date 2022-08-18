<template>
  <div class="row" style="margin-top:10% !important">
    <base-loader v-if="isRequestOngoing" :show-bg-overlay="true" />
    <div class="col-xl-3">
        <div class="card filter-card">
            <div class="card-body">
              <div>
                <div class="form-group">
                  <label> Select Date range</label>
                  <select class="form-control"
                    v-model="selectedRange"
                    @change="onChangeDateRange"
                  >
                  <option v-for="(item, index) in dateRange" :key="index" :value="item" >{{item}}</option>
                  </select>
                </div>
                <div class="form-group datemx datetype"
                >
                <label>From Date</label>
                  <datepicker
                    v-model.trim="formData.from_date"
                    :editable="false"
                    :clearable="false"
                    value-type="format" format="YYYY-MM-DD"
                    @change="$v.formData.from_date.$touch()"
                  />
                </div>

                <div class="form-group datemx datetype">
                  <label>To Date</label>
                  <datepicker 
                    :editable="false"
                    :clearable="false"
                    v-model="formData.to_date"
                    value-type="format" format="YYYY-MM-DD"
                    @change="$v.formData.to_date.$touch()"
                  />
                </div>
              </div>
              <button
                variant="primary-outline"
                class="btn btn-primary mt-3 w-100"
                type="submit"
                @click.prevent="getReports()"
              >
                {{ $t('reports.update_report') }}
              </button>
            </div>
        </div>
    </div>
    <div class="col-xl-9">
       <div class="card">
          <div class="card-body">
            <div class="invoice-item">
              <div class="row">
                <div class="col-md-6">
                  <div class="invoice-logo">
                    <img src="assets/img/logo.png" alt="logo">
                  </div>
                </div>
                <div class="col-md-6">
                  <p class="invoice-details">
                    <strong>{{reportData.fromDate}} - {{reportData.toDate}}</strong>
                  </p>
                </div>
              </div>
            </div>
            <!-- Invoice Item -->
            <div class="invoice-item invoice-table-wrap">
              <h3 class="report-title">Tax Report</h3>
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table report-table">
                      <tbody >
                          <tr>
                          <th colspan="2">Tax Types</th>
                        </tr>
                        <tr v-for="item in reportData.taxData" :key="item.id">
                          <td>{{item.taxName}}</td>
                          <td class="text-right">{{item.taxAmount}}</td>
                        </tr>
                        <tr>
                          <td class="text-right report-total" colspan="2"><strong>{{reportData.taxTotalAmount}}</strong></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table total-report-table">
                      <tbody>
                      <tr>
                        <th>Total Tax:</th>
                        <td><span class="float-right">{{reportData.taxTotalAmount}}</span></td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /Invoice Item -->
            
          </div>
        </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

import { DocumentTextIcon } from '@vue-hero-icons/solid'
import moment from 'moment'
const { required } = require('vuelidate/lib/validators')

export default {
  components: {
    DocumentTextIcon,
  },
  data() {
    return {
      dateRange: [
        'Today',
        'This Week',
        'This Month',
        'This Quarter',
        'This Year',
        'Previous Week',
        'Previous Month',
        'Previous Quarter',
        'Previous Year',
        'Custom',
      ],
      selectedRange: 'This Month',
      range: new Date(),
      formData: {
        from_date: moment().startOf('month').format('YYYY-MM-DD'),
        to_date: moment().endOf('month').format('YYYY-MM-DD'),
        unique_hash: ''
      },
      url: null,
      siteURL: null,
      reportData: [],
      isRequestOngoing: false,
    }
  },
  validations: {
    range: {
      required,
    },
    formData: {
      from_date: {
        required,
      },
      to_date: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('company', ['getSelectedCompany']),
    getReportUrl() {
      return this.url
    },
    dateRangeError() {
      if (!this.$v.range.$error) {
        return ''
      }

      if (!this.$v.range.required) {
        return this.$t('validation.required')
      }
    },
    fromDateError() {
      if (!this.$v.formData.from_date.$error) {
        return ''
      }

      if (!this.$v.formData.from_date.required) {
        return this.$t('validation.required')
      }
    },
    toDateError() {
      if (!this.$v.formData.to_date.$error) {
        return ''
      }

      if (!this.$v.formData.to_date.required) {
        return this.$t('validation.required')
      }
    },

    dateRangeUrl() {
      return `${this.siteURL}?from_date=${moment(
        this.formData.from_date
      ).format('YYYY-MM-DD')}&to_date=${moment(this.formData.to_date).format(
        'YYYY-MM-DD'
      )}`
    },
  },

  watch: {
    range(newRange) {
      this.formData.from_date = moment(newRange).startOf('year').toString()
      this.formData.to_date = moment(newRange).endOf('year').toString()
    },
  },

  mounted() {
    this.siteURL = `/reports/tax-summary/${this.getSelectedCompany.unique_hash}`
    this.url = this.dateRangeUrl
    this.getReportsData()
  },

  methods: {
    ...mapActions('reports', ['getTaxReport']),

    getThisDate(type, time) {
      return moment()[type](time).toString()
    },
    getPreDate(type, time) {
      return moment().subtract(1, time)[type](time).toString()
    },
    async getReportsData() {
      this.isRequestOngoing = true
      this.formData.unique_hash = this.getSelectedCompany.unique_hash
      let response =  await this.getTaxReport(this.formData)
      this.reportData = response.data
      this.isRequestOngoing = false
    },
    onChangeDateRange() {
      switch (this.selectedRange) {
        case 'Today':
          this.formData.from_date = moment().format('YYYY-MM-DD')
          this.formData.to_date = moment().format('YYYY-MM-DD')
          break

        case 'This Week':
          this.formData.from_date = moment().startOf('isoWeek').format('YYYY-MM-DD')
          this.formData.to_date = moment().endOf('isoWeek').format('YYYY-MM-DD')
          break

        case 'This Month':
          this.formData.from_date = moment().startOf('month').format('YYYY-MM-DD')
          this.formData.to_date = moment().endOf('month').format('YYYY-MM-DD')
          break

        case 'This Quarter':
          this.formData.from_date = moment().startOf('quarter').format('YYYY-MM-DD')
          this.formData.to_date = moment().endOf('quarter').format('YYYY-MM-DD')
          break

        case 'This Year':
          this.formData.from_date = moment().startOf('year').format('YYYY-MM-DD')
          this.formData.to_date = moment().endOf('year').format('YYYY-MM-DD')
          break

        case 'Previous Week':
          this.formData.from_date = moment().subtract(1, 'weeks').startOf('isoWeek').format('YYYY-MM-DD')

          this.formData.to_date = moment().subtract(1, 'weeks').endOf('isoWeek').format('YYYY-MM-DD')
          break

        case 'Previous Month':
          this.formData.from_date = moment().subtract(1, 'months').startOf('month').format('YYYY-MM-DD')
          this.formData.to_date = moment().subtract(1, 'months').endOf('month').format('YYYY-MM-DD')
          break

        case 'Previous Quarter':
          this.formData.from_date = moment().subtract(1, 'Q').startOf('quarter').format('YYYY-MM-DD')
          this.formData.to_date = moment().subtract(1, 'Q').endOf('quarter').format('YYYY-MM-DD')
          break

        case 'Previous Year':
          this.formData.from_date = moment().subtract(1, 'years').startOf('year').format('YYYY-MM-DD')
          this.formData.to_date = moment().subtract(1, 'years').endOf('year').format('YYYY-MM-DD')
          break

        default:
          break
      }
    },
    setRangeToCustom() {
      this.selectedRange = 'Custom'
    },
    async viewReportsPDF() {
      let data = await this.getReports()
      window.open(this.getReportUrl, '_blank')
      return data
    },
    async getReports(isDownload = false) {
      this.$v.range.$touch()
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return false
      }
      this.getReportsData()
      this.url = this.dateRangeUrl
      return true
    },
    downloadReport() {
      if (!this.getReports()) {
        return false
      }

      window.open(this.url + '&download=true')

      setTimeout(() => {
        this.url = this.dateRangeUrl
      }, 200)
    },
  },
}
</script>
