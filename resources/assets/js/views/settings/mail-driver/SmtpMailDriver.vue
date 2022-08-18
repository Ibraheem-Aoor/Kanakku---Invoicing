<template>
  <form @submit.prevent="saveEmailConfig">
      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.driver')}}</label>
        <div class="col-sm-9">
          <select class="select form-control"  :class="{'is-invalid': $v.mailConfigData.mail_driver.$error }"  v-model="mailConfigData.mail_driver" required>
              <option v-for="item in mailDrivers" :key="item" :value="item">{{item}}</option>
          </select>
          <div class="invalid-feedback" v-if="$v.mailConfigData.mail_driver.$error">{{driverError}}</div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.host')}}</label>
          <div class="col-sm-9">
            <input
              :class="{'is-invalid': $v.mailConfigData.mail_host.$error }" 
              v-model.trim="mailConfigData.mail_host"
              type="text"
              name="mail_host"
              class="mt-2 form-control"
              @input="$v.mailConfigData.mail_host.$touch()" required
            />
          </div>
        </div>
      <div class="invalid-feedback" v-if="$v.mailConfigData.mail_host.$error">{{hostError}}</div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.username')}}</label>
        <div class="col-sm-9">
            <input type="text" v-model.trim="mailConfigData.mail_username" :class="{ 'is-invalid': $v.mailConfigData.mail_username.$error }" 
              name="db_name" class="mt-2 form-control" @input="$v.mailConfigData.mail_username.$touch()" required>
            <div class="invalid-feedback" v-if="$v.mailConfigData.mail_username.$error">{{usernameError}}</div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.password')}}</label>
        <div class="col-sm-9">
            <input :type="getInputType" v-model.trim="mailConfigData.mail_password" id="mail_password" :class="{ 'is-invalid': $v.mailConfigData.mail_password.$error }" 
              name="db_name" class="mt-2 form-control" @input="$v.mailConfigData.mail_password.$touch();mailPassword(1)">
            <span class="fas fa-eye toggle-password" v-if="isShowPassword" @click="isShowPassword = !isShowPassword"></span>
            <span class="fas fa-eye-slash toggle-password" v-else @click="isShowPassword = !isShowPassword"></span>
            <div class="invalid-feedback" v-if="$v.mailConfigData.mail_password.$error">{{passwordError}}</div>
        </div>
      </div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.port')}}</label>  
          <div class="col-sm-9">
              <input :class="{ 'is-invalid': $v.mailConfigData.mail_port.$error }"
                v-model.trim="mailConfigData.mail_port"
                type="text"
                name="mail_port"
                class="mt-2 form-control"
                @input="$v.mailConfigData.mail_port.$touch()" required
              />
              <div class="invalid-feedback" v-if="$v.mailConfigData.mail_port.$error">{{portError}}</div>
          </div>
      </div>

      <div class="row form-group">
        <label class="col-sm-3 col-form-label input-label">{{$t('settings.mail.encryption')}}</label>  
        <div class="col-sm-9">
          <select class="select form-control mt-2 form-control" :class="{ 'is-invalid': $v.mailConfigData.mail_encryption.$error }" v-model="mailConfigData.mail_encryption" required>
              <option>Select</option>
              <option v-for="item in encryptions" :key="item" :value="item">{{item}}</option>
          </select>
          <div class="invalid-feedback" v-if="$v.mailConfigData.mail_encryption.$error">{{encryptionError}}</div>
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
              @input="$v.mailConfigData.from_mail.$touch()" required
            />
            <div class="invalid-feedback" v-if="$v.mailConfigData.from_mail.$error">{{fromEmailError}}</div>
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
              @input="$v.mailConfigData.from_name.$touch()" required
            />
            <div class="invalid-feedback" v-if="$v.mailConfigData.from_name.$error">{{fromNameError}}</div>
          </div>
      </div>
      <button 
        :disabled="loading"
        :loading="loading"
        type="submit"
        class="btn btn-primary"
        variant="primary">
        {{ $t('general.save') }}
      </button>
      <slot />
  </form>
</template>

<script>
const { required, email, numeric, alpha } = require('vuelidate/lib/validators')
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
const hostRegex = /^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/;
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
      mailConfigData: {
        mail_driver: '',
        mail_host: '',
        mail_port: null,
        mail_username: '',
        mail_password: '',
        mail_encryption: 'tls',
        from_mail: '',
        from_name: '',
      },
      isShowPassword: false,
      encryptions: ['tls', 'ssl', 'starttls'],
    }
  },
  validations: {
    mailConfigData: {
      mail_driver: {
        required,
      },
      mail_host: {
        required,
        hostValidation: function(value) {
          return hostRegex.test(value)
        }
      },
      mail_port: {
        required,
        numeric,
      },
      mail_username: {
        email,
        required,
      },
      mail_password: {
        required,
      },
      mail_encryption: {
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
    hostError() {
      if (!this.$v.mailConfigData.mail_host.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.mail_host.hostValidation) {
        return 'Invalid Host'
      }

      if (!this.$v.mailConfigData.mail_host.required) {
        return this.$tc('validation.required')
      }
    },
    usernameError() {
      if (!this.$v.mailConfigData.mail_username.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.mail_username.required) {
        return this.$tc('validation.required')
      }
    },
    passwordError() {
      if (!this.$v.mailConfigData.mail_password.$error) {
        return ''
      }

      if (!this.$v.mailConfigData.mail_password.required) {
        return this.$tc('validation.required')
      }
    },
    portError() {
      if (!this.$v.mailConfigData.mail_port.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.mail_port.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.mailConfigData.mail_port.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    encryptionError() {
      if (!this.$v.mailConfigData.mail_encryption.$error) {
        return ''
      }
      if (!this.$v.mailConfigData.mail_encryption.required) {
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
    encodeHtml(str) {
      let buf = [];
			
			for (var i=str.length-1;i>=0;i--) {
				buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
			}
			
			return buf.join('');
    },
    mailPassword(type) {
          let value=$("#mail_password").val()
          value = value.replace(/(<([^>]+)>)/g, "")
          this.mailConfigData.mail_password= value.replace(" ", "")
    },
    onChangeDriver() {
      this.$v.mailConfigData.mail_driver.$touch()
      this.$emit('on-change-driver', this.mailConfigData.mail_driver)
    },
  },
}
</script>
