<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid gap-6 grid-col-1 md:grid-cols-2">
      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.driver')}}</label>
          <div class="col-sm-9">
            <select class="select form-control" :class="{ 'is-invalid': $v.mailConfigData.mail_driver.$error }" v-model="mailConfigData.mail_driver" >
                <option v-for="item in mailDrivers" :key="item" :value="item">{{item}}</option>
            </select>
            <div class="invalid-feedback" v-if="driverError">{{driverError}}</div>
          </div>
      </div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.from_mail')}}</label> 
          <div class="col-sm-9">
            <input
              :class="{ 'is-invalid': $v.mailConfigData.from_mail.$error }"
              v-model.trim="mailConfigData.from_mail"
              type="text"
              name="from_mail"
              class="mt-2 form-control"
              @input="$v.mailConfigData.from_mail.$touch()"
            />
          </div>
        <div class="invalid-feedback" v-if="fromEmailError">{{fromEmailError}}</div>
      </div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.from_name')}}</label> 
          <div class="col-sm-9">
            <input
              :class="{ 'is-invalid': $v.mailConfigData.from_name.$error }"
              v-model.trim="mailConfigData.from_name"
              type="text"
              name="from_name"
              class="mt-2 form-control"
              @input="$v.mailConfigData.from_name.$touch()"
            />
            <div class="invalid-feedback" v-if="$v.mailConfigData.from_name.$error">{{fromNameError}}</div>
          </div>
      </div>
    </div>
    <div class="flex mt-8">
      <button
        :disabled="loading"
        :loading="loading"
        class="btn btn-primary"
        type="submit"
      >
        {{ $t('general.save') }}
      </button>
      <slot />
    </div>
  </form>
</template>

<script>
const { required, email, alpha } = require('vuelidate/lib/validators')

export default {
  props: {
    configData: {
      type: Object,
      require: true,
      default: Object,
    },
    loading: {
      type: Boolean,
      require: true,
      default: false,
    },
    mailDrivers: {
      type: Array,
      require: true,
      default: Array,
    },
  },
  data() {
    return {
      mailConfigData: {
        mail_driver: '',
        mail_host: '',
        from_mail: '',
        from_name: '',
      },
    }
  },
  validations: {
    mailConfigData: {
      mail_driver: {
        required,
      },
      from_mail: {
        required,
        email,
      },
      from_name: {
        alpha,
        required,
      },
    },
  },
  watch: {
      "mailConfigData.mail_driver": function(val) {
          this.onChangeDriver()
      }
  },
  computed: {
    driverError() {
      if (!this.$v.mailConfigData.mail_driver.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.mail_driver.required) {
        return this.$tc('validation.required')
      }
    },
    fromEmailError() {
      if (!this.$v.mailConfigData.from_mail.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.from_mail.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.mailConfigData.from_mail.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    fromNameError() {
      if (!this.$v.mailConfigData.from_name.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.from_name.alpha) {
        return 'Name should contain characters only'
      }

      if (!this.$v.mailConfigData.from_name.required) {
        return this.$tc('validation.required')
      }
    },
  },
  mounted() {
    for (const key in this.mailConfigData) {
      if (this.configData.hasOwnProperty(key)) {
        this.mailConfigData[key] = this.configData[key]
      }
    }
  },
  methods: {
    async saveEmailConfig() {
      this.$v.mailConfigData.$touch()
      if (!this.$v.mailConfigData.$invalid) {
        this.$emit('submit-data', this.mailConfigData)
      }
      return false
    },
    onChangeDriver() {
      this.$v.mailConfigData.mail_driver.$touch()
      this.$emit('on-change-driver', this.mailConfigData.mail_driver)
    },
  },
}
</script>
