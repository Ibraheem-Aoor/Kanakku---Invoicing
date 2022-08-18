<template>
  <div class="row">
    <div class="col-xl-7 d-flex">
      <div class="card flex-fill">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">{{ $t('dashboard.monthly_chart.title') }}</h5>
                <select class="form-control dashboard_chart" v-model="selectedYear">
                  <option v-for="item in years" :key="item" :value="item">{{item}}</option>
                </select>
            
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap">
            <div class="row ml-2">
              <div>
                <span>{{ $t('dashboard.chart_info.total_sales') }}</span>
                <p class="h3 text-primary mr-5"><span v-html="$utils.formatMoney(getTotalSales, defaultCurrency)"></span></p>
              </div>
              <div>
                <span>{{ $t('dashboard.chart_info.total_receipts') }}</span>
                <p class="h3 text-success mr-5" ><span v-html="$utils.formatMoney(getTotalReceipts, defaultCurrency)"></span></p>
              </div>
              <div>
                <span>{{ $t('dashboard.chart_info.total_expense') }}</span>
                <p class="h3 text-danger mr-5"><span v-html="$utils.formatMoney(getTotalExpenses, defaultCurrency)"></span></p>
              </div>
              <div>
                <span>{{ $t('dashboard.chart_info.net_income') }}</span>
                <p class="h3 text-dark mr-5"><span v-html="$utils.formatMoney(getNetProfit, defaultCurrency)" ></span></p>
              </div>
            </div>
          </div>
          
          <div><apexchart width="300" type="bar" :options="options" :series="series"></apexchart></div>
        </div>
      </div>
    </div>
    <div class="col-xl-5 d-flex">
      <div class="card flex-fill">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Invoice Analytics</h5> 
              <select class="form-control dashboard_chart" v-model="selectedValue">
                  <option v-for="item in analyticsType" :key="item" :value="item">{{item}}</option>
                </select> 
          </div>
        </div>
        <div class="card-body">
          <div id="invoice_chart"><div class="text-center paymentsempty" v-if="noData">
                <span class="text-danger"> No Data </span>
              </div><apexchart type="donut" :options="invoiceoptions" :series="invoiceseries"></apexchart></div>
          <div class="text-center text-muted">
            <div class="row">
              <div class="col-4">
                <div class="mt-4">
                  <p class="mb-2 text-truncate"><i class="fas fa-circle text-primary mr-1"></i> Invoiced</p>
                  <h5><span v-html="$utils.formatMoney(chartAmount.invoicedAmount, defaultCurrency)"></span></h5>
                </div>
              </div>
              <div class="col-4">
                <div class="mt-4">
                  <p class="mb-2 text-truncate"><i class="fas fa-circle text-success mr-1"></i> Received</p>
                  <h5><span v-html="$utils.formatMoney(chartAmount.receivedAmount, defaultCurrency)"></span></h5>
                </div>
              </div>
              <div class="col-4">
                <div class="mt-4">
                  <p class="mb-2 text-truncate"><i class="fas fa-circle text-danger mr-1"></i> Pending</p>
                  <h5><span v-html="$utils.formatMoney(chartAmount.pendingAmount, defaultCurrency)"></span></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { SweetModal, SweetModalTab } from 'sweet-modal-vue'
import LineChart from '../../components/chartjs/LineChart'
import { ChartSquareBarIcon } from "@vue-hero-icons/outline"

export default {
  components: {
    LineChart,
    ChartSquareBarIcon
  },
  data() {
    return {
      ...this.$store.state.dashboard,
      isLoaded: false,
      years: ['This year', 'Previous year'],
      analyticsType: ['Weekly', 'Monthly', 'Yearly'],
      selectedYear: 'This year',
      selectedValue: 'Monthly',
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
   invoiceseries: [0,0,0],
   noData: false,
    invoiceoptions: {
				colors: ['#7638ff', '#ff737b', '#fda600'],
				chart: {
					fontFamily: 'Poppins, sans-serif',
					height: 350,
					type: 'donut',
				},
				labels: ['Paid', 'Unpaid', 'Partially Paid'],
				legend: {show: false},
			
			},
   chartAmount: {
     invoicedAmount: 0,
     pendingAmount: 0,
     receivedAmount: 0
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
  mounted() {
    //this.renderchart();  
	},
  computed: {
    ...mapGetters('user', {
      user: 'currentUser',
    }),
    ...mapGetters('dashboard', [
      'getChartMonths',
      'getChartInvoices',
      'getChartExpenses',
      'getNetProfits',
      'getReceiptTotals',
      'getTotalSales',
      'getTotalReceipts',
      'getTotalExpenses',
      'getNetProfit',
    ]),
    ...mapGetters('company', ['defaultCurrency']),
  },
  watch: {
    selectedYear(val) {
      
      if (val === 'Previous year') {
        let params = { previous_year: true}
        if (this.selectedValue === 'Monthly' || this.selectedValue === 'Weekly' || this.selectedValue === 'Yearly') {
          params = { previous_year: true, invoice_type: this.selectedValue}
        }
        this.loadData(params)
      } if (val === 'This year') {
        let params = { }
        if (this.selectedValue === 'Monthly' || this.selectedValue === 'Weekly' || this.selectedValue === 'Yearly') {
          params = { invoice_type: this.selectedValue}
        }
        this.loadData(params)
      } else {
        this.loadData()
      }
    },
     selectedValue(val) {
      if (val === 'Monthly' || val === 'Weekly' || val === 'Yearly') {
        let params = { invoice_type: val}
        if(this.selectedYear === 'Previous Year') {
          params = {previous_year: true, invoice_type: val}
        }
        this.loadData(params)
      } else {
        this.loadData()
      }
    },
  },
  mounted() {
    this.loadData()
  },
  methods: {
    ...mapActions('dashboard', ['loadData']),
    renderChart() {
      const {dashboard} = this.$store.state
      const toNumbers = arr => arr.map(Number);
      this.chartAmount.invoicedAmount = dashboard.invoiceChartAmount.invoicedAmount
      this.chartAmount.pendingAmount = dashboard.invoiceChartAmount.invoicePendingAmount
      this.chartAmount.receivedAmount = dashboard.invoiceChartAmount.invoiceReceivedAmount
     
      this.invoiceseries = toNumbers(dashboard.invoiceAnalytics.value)
      if(this.invoiceseries.every(item => parseFloat(item) === 0)) {
        this.noData = true
      } else {
        this.noData = false
      }
      
      this.series = [
          {
            name: "Sales",
            type: "column",
            data:  dashboard.chartData.invoiceTotals.map((invoice) => invoice / 100)
          },
          {
            name: "Receipts",
            type: "column",
            data: dashboard.chartData.receiptTotals.map((invoice) => invoice / 100)
          },
          {
            name: "Expenses",
            type: "column",
            data: dashboard.chartData.expenseTotals.map((invoice) => invoice / 100)
          },
          {
            name: "Net Income",
            type: "column",
            data: dashboard.chartData.netProfits.map((invoice) => invoice / 100)
          }
      ];
    
       this.options = {
      colors: ['#7638ff', '#22cc62', '#ef3737', '#343a40'],
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
      categories: dashboard.chartData.months
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

    async loadData(params) {
      let response = await this.$store.dispatch('dashboard/loadData', params)
      this.isLoaded = true
      if(response) {
        this.renderChart()
      } 
    },
  },
}
</script>
