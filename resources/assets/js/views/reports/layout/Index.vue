<template>
  <base-page class="profit-loss-reports reports">
    <div class="col-sm-12">
            <h3 class="page-title">{{$tc('reports.report', 2)}}</h3>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link></li>
              <li class="breadcrumb-item"><router-link to="/admin/reports">{{$tc('reports.report', 2)}}</router-link></li>
            </ul>
        </div>
       
        <button class="btn btn-primary floatright m-3" @click="onDownload()">
          <download-icon  />
          {{ $t('reports.download_pdf') }}
        </button>
         <button class="btn btn-primary floatright m-3" @click="viewPdf()">
          <document-text-icon />
          {{ $t('reports.view_pdf') }}
        </button>
    <transition name="fade" mode="out-in">
        <router-view ref="report" />
    </transition>
  </base-page>
</template>

<script>
import { DownloadIcon } from '@vue-hero-icons/solid'
import { DocumentTextIcon } from '@vue-hero-icons/solid'
export default {
  components: {
    DownloadIcon,
    DocumentTextIcon
  },
  data() {
    return {
      activeTab: 'SALES',
    }
  },
  watch: {
    '$route.path'(newValue) {
      if (newValue === '/admin/reports') {
        this.$router.push('/admin/reports/sales')
      }
    },
  },
  created() {
    if (this.$route.path === '/admin/reports') {
      this.$router.push('/admin/reports/sales')
    }
  },
  methods: {
    onDownload() {
      this.$refs.report.downloadReport()
    },
    setActiveTab(val) {
      this.activeTab = val
    },
     viewPdf() {
       this.$refs.report.viewReportsPDF()
    },
  },
}
</script>

<style scoped>
.tab {
  padding: 0 !important;
}

.tab-link {
  padding: 10px 30px;
  display: block;
}
</style>
