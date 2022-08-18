<template>
  <div>
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="card-title">{{$t('dashboard.monthly_chart.title')}}</h5>
          <select class="form-control dashboard_chart" v-model="selectedYear" @change="onChangeYear">
              <option v-for="item in years" :key="item" :value="item">{{item}}</option>
          </select>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
          <div class="col-lg-10">
              <div>
                <apexchart width="600" type="bar" :options="options" :series="series"></apexchart>
              </div>
          </div>
          <div class="col-lg-2" style="text-align:center;">
              <div class="mb-4">
                <span> {{ $t('dashboard.chart_info.total_sales') }}</span>
                <p class="h3 text-primary mr-5 sales_value"> <span v-html="getFormattedSalesTotal" /></p>
              </div>
              <div class="mb-4">
                <span> {{ $t('dashboard.chart_info.total_receipts') }}</span>
                <p class="h3 text-success mr-5 sales_value"><span v-html="getFormattedTotalReceipts" /></p>
              </div>
              <div class="mb-4">
                <span>{{ $t('dashboard.chart_info.total_expense') }}</span>
                <p class="h3 text-danger mr-5 sales_value">  <span v-html="getFormattedTotalExpenses" /></p>
              </div>
              <div class="mb-4">
                <span>{{ $t('dashboard.chart_info.net_income') }}</span>
                <p class="h3 text-dark mr-5 sales_value"> <span v-html="getFormattedTotalNetProfit" /></p>
              </div>
          </div>
        </div>
    </div>
  </div>
</template>

<script>
import CustomerInfo from './CustomerInfo'
import LineChart from '../../../components/chartjs/LineChart'
import { mapActions, mapGetters } from 'vuex'
import { ChartSquareBarIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    LineChart,
    CustomerInfo,
    ChartSquareBarIcon,
  },
  data() {
    return {
      id: null,
      customers: [],
      isLoaded: false,
      chartData: null,
      years: ['This year', 'Previous year'],
      selectedYear: 'This year',
    options : {
      colors: ['#7638ff', '#22cc62', '#ef3737', '#343a40'],
      responsive: [
      {
        breakpoint: 700,
        width:300,
        options: {
          plotOptions: {
            bar: {
              horizontal: false
            },
             xaxis: {
              lines: {
                show: false,
              }    
            },

          },
          legend: {
            position: "bottom"
          }
        }
      }
    ],
    chart: {
      type: 'bar',
      fontFamily: 'Poppins, sans-serif',
      height: 350,
      width: "100%",
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '60%',
        endingShape: 'rounded'
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    xaxis: {
       lines: {
        show: false,
      },
      categories: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]
    },
    yaxis: {
      lines: {
        show: false,
      },
      title: {
        text: '$ (thousands)'
      }
    },
    fill: {
      opacity: 1
    },
    tooltip: {
       y: {
        formatter: undefined,
          title: {
              formatter: (seriesName) => seriesName,
          },
      }
    }
  },
      series: [ {
                  name: "Sales",
                  type: "column",
                  data:  [0,0,0,0,0,0,0,0,0,0,0,0]
                },
                {
                  name: "Receipts",
                  type: "column",
                  data: [0,0,0,0,0,0,0,0,0,0,0,0]
                },
                {
                  name: "Expenses",
                  type: "column",
                  data: [0,0,0,0,0,0,0,0,0,0,0,0]
                },
                {
                  name: "Net Income",
                  type: "column",
                  data: [0,0,0,0,0,0,0,0,0,0,0,0]
                }]
    }
  },
  computed: {
    ...mapGetters('company', ['defaultCurrency']),
    getChartInvoices() {
      if (this.chartData && this.chartData.invoiceTotals) {
        return this.chartData.invoiceTotals
      }
      return []
    },
    getChartExpenses() {
      if (this.chartData && this.chartData.expenseTotals) {
        return this.chartData.expenseTotals
      }
      return []
    },
    getReceiptTotals() {
      if (this.chartData && this.chartData.receiptTotals) {
        return this.chartData.receiptTotals
      }
      return []
    },
    getNetProfits() {
      if (this.chartData && this.chartData.netProfits) {
        return this.chartData.netProfits
      }
      return []
    },
    getChartMonths() {
      if (this.chartData && this.chartData.months) {
        return this.chartData.months
      }
      return []
    },
    getFormattedSalesTotal() {
      if (this.chartData && this.chartData.salesTotal) {
        return this.$utils.formatMoney(
          this.chartData.salesTotal,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalReceipts() {
      if (this.chartData && this.chartData.totalReceipts) {
        return this.$utils.formatMoney(
          this.chartData.totalReceipts,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalExpenses() {
      if (this.chartData && this.chartData.totalExpenses) {
        return this.$utils.formatMoney(
          this.chartData.totalExpenses,
          this.defaultCurrency
        )
      }
      return 0
    },
    getFormattedTotalNetProfit() {
      if (this.chartData && this.chartData.netProfit) {
        return this.$utils.formatMoney(
          this.chartData.netProfit,
          this.defaultCurrency
        )
      }
      return 0
    },
  },
  watch: {
    $route(to, from) {
      this.loadCustomer()
      this.selectedYear = 'This year'
    },
  },
  created() {
    this.loadCustomer()
  },
  
  methods: {
    ...mapActions('customer', ['fetchViewCustomer']),

    async loadCustomer() {
      this.isLoaded = false
      let response = await this.fetchViewCustomer({ id: this.$route.params.id })
      if (response.data) {
        this.chartData = response.data.chartData
        this.renderChart();
      }
      this.isLoaded = false
    },
    async onChangeYear() {
      if (this.selectedYear == 'Previous year') {
        let response = await this.fetchViewCustomer({
          id: this.$route.params.id,
          previous_year: true,
        })
        if (response.data) {
          this.chartData = response.data.chartData
           if(this.chartData) {
             this.renderChart();
           }
        }
        return true
      }
      let response = await this.fetchViewCustomer({ id: this.$route.params.id })
      if (response.data) {
        this.chartData = response.data.chartData
        if(this.chartData) {
            this.renderChart();
        }
      }
      return true
    },
    renderChart() {
      this.series = [
          {
            name: "Sales",
            type: "column",
            data:  this.getChartInvoices.map((invoice) => invoice / 100)
          },
          {
            name: "Receipts",
            type: "column",
            data: this.getReceiptTotals.map((invoice) => invoice / 100)
          },
          {
            name: "Expenses",
            type: "column",
            data: this.getChartExpenses.map((invoice) => invoice / 100)
          },
          {
            name: "Net Income",
            type: "column",
            data: this.getNetProfits.map((invoice) => invoice / 100)
          }
      ];
       this.options = {
      colors: ['#7638ff', '#22cc62', '#ef3737', '#343a40'],
      responsive: [
      {
        breakpoint: 700,
        width:300,
        options: {
          plotOptions: {
            bar: {
              horizontal: false
            },
             xaxis: {
              lines: {
                show: false,
              }    
            },
          },
          legend: {
            position: "bottom"
          }
        }
      }
    ],
    chart: {
      type: 'bar',
      fontFamily: 'Poppins, sans-serif',
      height: 350,
      width: "100%",
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '60%',
        endingShape: 'rounded'
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    xaxis: {
       lines: {
        show: false,
      },
      categories: this.getChartMonths
    },
    yaxis: {
      lines: {
        show: false,
      },
      title: {
        text: `${this.defaultCurrency.symbol}(thousands)`
      }
    },
    fill: {
      opacity: 1
    },
    tooltip: {
      y: {
          formatter: 'P',
          title: {
              formatter: (seriesName) => seriesName+"  "+this.defaultCurrency.symbol,
          },
      }
    }
  }
      
     
  },
  },
}
</script>
