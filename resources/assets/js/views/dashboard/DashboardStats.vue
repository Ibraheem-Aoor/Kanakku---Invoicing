<template>
	<div class="row">
		<div class="col-xl-3 col-sm-6 col-12">
			<div class="card">
				<div class="card-body">
					<div class="dash-widget-header">
					<span class="dash-widget-icon bg-1">
					<i class="fas fa-dollar-sign"></i>
					</span>
					<div class="dash-count">
						<div class="dash-title">{{ $t('dashboard.cards.due_amount') }}</div>
						<div class="dash-counts">
							<span v-html="$utils.formatMoney(getTotalDueAmount, defaultCurrency)"/> 
						</div>
					</div>
					</div>
					<div class="progress progress-sm mt-3">
					<div class="progress-bar bg-5" role="progressbar" v-bind:style="{ width: getTotalDuePercentage+'%' }" :aria-valuenow="getTotalDuePercentage" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-sm-6 col-12">
			<div class="card">
				<div class="card-body">
					<div class="dash-widget-header">
					<span class="dash-widget-icon bg-2">
					<i class="fas fa-users"></i>
					</span>
					<div class="dash-count">
						<div class="dash-title">{{ $t('dashboard.cards.customers') }}</div>
						<div class="dash-counts">
							<p>{{ getContacts }}</p>
						</div>
					</div>
					</div>
					<div class="progress progress-sm mt-3">
					<div class="progress-bar bg-6" role="progressbar" v-bind:style="{ width: getContacts+'%' }" :aria-valuenow="getContacts" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-sm-6 col-12">
			<div class="card">
				<div class="card-body">
					<div class="dash-widget-header">
					<span class="dash-widget-icon bg-3">
					<i class="fas fa-file-alt"></i>
					</span>
					<div class="dash-count">
						<div class="dash-title">{{ $t('dashboard.cards.invoices') }}</div>
						<div class="dash-counts">
							<p>{{getInvoices}}</p>
						</div>
					</div>
					</div>
					<div class="progress progress-sm mt-3">
					<div class="progress-bar bg-7" role="progressbar" v-bind:style="{ width: getInvoices+'%' }" :aria-valuenow="getInvoices" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<!-- <p class="text-muted mt-3 mb-0"><span class="text-success mr-1"><i class="fas fa-arrow-up mr-1"></i>3.77%</span> since last week</p> -->
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-sm-6 col-12">
			<div class="card">
				<div class="card-body">
					<div class="dash-widget-header">
					<span class="dash-widget-icon bg-4">
					<i class="far fa-file"></i>
					</span> 
					<div class="dash-count">
						<div class="dash-title">{{ $t('dashboard.cards.estimates') }}</div>
						<div class="dash-counts">
							<p>{{ getEstimates }}</p>
						</div>
					</div>
					</div>
					<div class="progress progress-sm mt-3">
					<div class="progress-bar bg-8" role="progressbar" v-bind:style="{ width: getEstimates+'%' }" :aria-valuenow="getEstimates" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data() {
    return {
      ...this.$store.state.dashboard,
    }
  },
  computed: {
    ...mapGetters('user', {
      user: 'currentUser',
    }),
    ...mapGetters('dashboard', [
      'getContacts',
      'getInvoices',
      'getEstimates',
      'getTotalDueAmount',
	  'getTotalDuePercentage',
      'getDashboardDataLoaded',
    ]),
    ...mapGetters('company', ['defaultCurrency']),
  },
}
</script>
