<template>
  <form @submit.prevent="saveEmailConfig">
    <div class="grid gap-6 sm:grid-col-1 md:grid-cols-2 mt-4">
      <div class="row form-group" >
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.driver')}}</label>
          <div class="col-sm-9" >
            <select class="select form-control" :class="{ 'is-invalid': $v.mailConfigData.mail_driver.$error }" v-model="mailConfigData.mail_driver">
                <option v-for="item in mailDrivers" :key="item" :value="item">{{item}}</option>
            </select>
            <div class="invalid-feedback" v-if="$v.mailConfigData.mail_driver.$error">{{driverError}}</div>
          </div>
      </div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.mailgun_domain')}}</label>
          <div class="col-sm-9">
            <input
              :class="{ 'is-invalid': $v.mailConfigData.mail_mailgun_domain.$error }"
              v-model.trim="mailConfigData.mail_mailgun_domain"
              type="text"
              name="mailgun_domain"
              class="mt-2 form-control"
              @input="$v.mailConfigData.mail_mailgun_domain.$touch()"
            />
            <div class="invalid-feedback" v-if="$v.mailConfigData.mail_mailgun_domain.$error">{{domainError}}</div>
          </div>
      </div>

      <div class="row form-group">
      <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.mailgun_secret')}}</label>
        <div class="col-sm-9" >
          <input
            :class="{ 'is-invalid': $v.mailConfigData.mail_mailgun_secret.$error }" 
            v-model.trim="mailConfigData.mail_mailgun_secret"
            :type="getInputType"
            name="mailgun_secret"
            class="mt-2 form-control"
            id="mail_gun_secret"
            @input="$v.mailConfigData.mail_mailgun_secret.$touch();mailgun(1)">
          <span class="fas fa-eye toggle-password" v-if="isShowPassword" @click="isShowPassword = !isShowPassword"></span>
          <span class="fas fa-eye-slash toggle-password" v-else @click="isShowPassword = !isShowPassword"></span>
          <div class="invalid-feedback" v-if="$v.mailConfigData.mail_mailgun_secret.$error">{{secretError}}</div>
        </div>
      </div>

      <div class="row form-group">
      <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.mailgun_endpoint')}}</label>
        <div class="col-sm-9" >
          <input
            :class="{ 'is-invalid': $v.mailConfigData.mail_mailgun_endpoint.$error }" 
            v-model.trim="mailConfigData.mail_mailgun_endpoint"
            type="text"
            name="mailgun_endpoint"
            class="mt-2 form-control"
            @input="$v.mailConfigData.mail_mailgun_endpoint.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.mailConfigData.mail_mailgun_endpoint.$error">{{endpointError}}</div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.from_mail')}}</label>
          <div class="col-sm-9" >
            <input
              :class="{ 'is-invalid': $v.mailConfigData.from_mail.$error }" 
              v-model.trim="mailConfigData.from_mail"
              type="text"
              name="from_mail"
              class="mt-2 form-control"
              @input="$v.mailConfigData.from_mail.$touch()"
            />
            <div class="invalid-feedback" v-if="$v.mailConfigData.from_mail.$error">{{fromEmailError}}</div>
          </div>
        </div>
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
    <div class="flex my-10">
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
const { required, email, numeric, alpha } = require('vuelidate/lib/validators')
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
const regex = /^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/;
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
  components: {
    EyeIcon,
    EyeOffIcon,
  },
  data() {
    return {
      isShowPassword: false,
      mailConfigData: {
        mail_driver: '',
        mail_mailgun_domain: '',
        mail_mailgun_secret: '',
        mail_mailgun_endpoint: '',
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
      mail_mailgun_domain: {
        required,
        domainValidation: function(value) {
          return regex.test(value)
        }
      },
      mail_mailgun_endpoint: {
        required,
        endPointValidation: function(value) {
          return regex.test(value)
        }
      },
      mail_mailgun_secret: {
        required,
      },
      from_mail: {
        required,
        email,
      },
      from_name: {
        required,
        alpha,
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
    domainError() {
      if (!this.$v.mailConfigData.mail_mailgun_domain.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.mail_mailgun_domain.domainValidation) {
        return 'Invalid Domain'
      }

      if (!this.$v.mailConfigData.mail_mailgun_domain.required) {
        return this.$tc('validation.required')
      }
    },
    secretError() {
      if (!this.$v.mailConfigData.mail_mailgun_secret.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.mail_mailgun_secret.required) {
        return this.$tc('validation.required')
      }
    },
    endpointError() {
      if (!this.$v.mailConfigData.mail_mailgun_endpoint.$error) {
        return ''
      }

       if (!this.$v.mailConfigData.mail_mailgun_endpoint.endPointValidation) {
        return 'Invalid Endpoint'
      }

      if (!this.$v.mailConfigData.mail_mailgun_endpoint.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.mailConfigData.mail_mailgun_endpoint.numeric) {
        return this.$tc('validation.numbers_only')
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
    getInputType() {
      if (this.isShowPassword) {
        return 'text'
      }
      return 'password'
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
    mailgun(type) {
        let value=$("#mail_gun_secret").val()
        value = value.replace(/(<([^>]+)>)/g, "")
        this.mailConfigData.mail_mailgun_secret= value.replace(" ", "")
    },
    onChangeDriver() {
      this.$v.mailConfigData.mail_driver.$touch()
      this.$emit('on-change-driver', this.mailConfigData.mail_driver)
    },
  },
}
</script>
