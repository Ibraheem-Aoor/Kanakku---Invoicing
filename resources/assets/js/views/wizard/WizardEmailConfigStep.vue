<template>
  <div>
    <h3>{{$t('wizard.mail.mail_config')}}</h3>
  <div>{{$t('wizard.mail.mail_config_desc')}}</div>
    <base-loader v-if="isFetching" :show-bg-overlay="true" />
    <form action="" @submit.prevent="next()">
      <component
        :is="mail_driver"
        :config-data="mailConfigData"
        :loading="isLoading"
        :mail-drivers="mail_drivers"
        @on-change-driver="
          (val) => (mail_driver = mailConfigData.mail_driver = val)
        "
        @submit-data="next"
      />
    </form>
  </div>
</template>

<script>
import Smtp from './mail-driver/SmtpMailDriver'
import Mailgun from './mail-driver/MailgunMailDriver'
import Ses from './mail-driver/SesMailDriver'
import Basic from './mail-driver/BasicMailDriver'
import {TabContent} from 'vue-step-wizard'
import 'vue-step-wizard/dist/vue-step-wizard.css'

export default {
  components: {
    'smtp': Smtp,
    'mail': Basic,
    'sendmail': Basic,
    'mailgun': Mailgun,
    'ses': Ses,
    'tab-content': TabContent
  },
  props: {
    selectedTab: {
      type: Number,
      require: true,
      default: Number,
    },
  },
  data() {
    return {
      mailConfigData: {
        mail_driver: 'mail',
      },
      mail_driver: 'mail',
      isLoading: false,
      isFetching: false,
      mail_drivers: [],
    }
  },
  created() {
    this.getMailDrivers()
  },
  methods: {
    async getMailDrivers() {
      this.isLoading = this.isFetching = true

      let response = await window.axios.get('/api/v1/mail/drivers')

      if (response.data) {
        this.mail_drivers = response.data
        this.isLoading = this.isFetching = false
      }
    },
    async next(mailConfigData) {
      this.isLoading = this.isFetching = true
      try {
        let response = await window.axios.post(
          '/api/v1/mail/config',
          mailConfigData
        )
        if (response.data.success) {
          this.$emit('next', 4)
          window.toastr['success'](
            this.$t('wizard.success.' + response.data.success)
          )
        } else {
          window.toastr['error'](
            this.$t('wizard.errors.' + response.data.error)
          )
        }
        this.isLoading = this.isFetching = false
        return true
      } catch (e) {
        this.isLoading = this.isFetching = false
        window.toastr['error']('Something went wrong')
      }
    },
  },
}
</script>
