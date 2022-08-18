<template>
  <form action="" @submit.prevent="next()">
    <div>
      <div class="row mt-4">
        <div class="form-group col-md-6">
        <label>{{$t('wizard.database.app_url')}}</label><span class="text-danger">*</span>
          <input
            :class="{ 'is-invalid': $v.databaseData.app_url.$error }"
            v-model.trim="databaseData.app_url"
            type="text"
            class="form-control"
            name="name"
            @input="$v.databaseData.app_url.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.databaseData.app_url.$error">{{urlError}}</div>
        </div>

        <div class="form-group col-md-6">
        <label>{{$t('wizard.database.app_domain')}} </label><span class="text-danger">*</span>
          <input
            :class="{ 'is-invalid': $v.databaseData.app_domain.$error }"
            v-model.trim="databaseData.app_domain"
            type="text"
            name="name"
            class="form-control"
            placeholder="kanakku.com"
            @input="$v.databaseData.app_domain.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.databaseData.app_domain.$error">{{domainError}}</div>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
        <label>{{$t('wizard.database.connection')}}</label><span class="text-danger">*</span>
          <select
          :class="{ 'is-invalid': $v.databaseData.database_connection.$error }"
            v-model="databaseData.database_connection"
            class="form-control"
            @change="onChangeConnection">
            <option v-for="item in connections" :key="item">
                {{item}}
            </option>
          </select>
          <div class="invalid-feedback" v-if="$v.databaseData.database_connection.$error">{{connectionError}}</div>
        </div>

        <div class="form-group col-md-6">
        <label>{{$t('wizard.database.port')}}</label><span class="text-danger">*</span>
          <input
            :class="{ 'is-invalid': $v.databaseData.database_port.$error }"
            v-model.trim="databaseData.database_port"
            type="text"
            class="form-control"
            name="database_port"
            @input="$v.databaseData.database_port.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.databaseData.database_port.$error">{{urlError}}</div>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
        <label>{{$t('wizard.database.db_name')}}</label><span class="text-danger">*</span>
          <input
            :class="{ 'is-invalid': $v.databaseData.database_name.$error }"
            v-model.trim="databaseData.database_name"
            type="text"
            class="form-control"
            name="database_name"
            @input="$v.databaseData.database_name.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.databaseData.database_name.$error">{{nameError}}</div>
        </div>

        <div class="form-group col-md-6">
        <label>{{$t('wizard.database.username')}}</label><span class="text-danger">*</span>
          <input
            :invalid="$v.databaseData.database_username.$error"
            v-model.trim="databaseData.database_username"
            type="text"
            class="form-control"
            name="database_username"
            @input="$v.databaseData.database_username.$touch()"
          />
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          {{$t('wizard.database.password')}}
          <input
            v-model.trim="databaseData.database_password"
            type="password"
            name="name"
            class="form-control"
          />
        </div>

        <div class="form-group col-md-6">
        <label>{{$t('wizard.database.host')}}</label><span class="text-danger">*</span>
          <input
            :class="{ 'is-invalid': $v.databaseData.database_hostname.$error }"
            v-model.trim="databaseData.database_hostname"
            type="text"
            class="form-control"
            name="database_hostname"
            @input="$v.databaseData.database_hostname.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.databaseData.database_hostname.$error">{{hostnameError}}</div>
        </div>
      </div>
      <button
        v-show="!isFetching"
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        class="btn btn-primary mt-4"
        type="submit"
      >
        <save-icon v-if="!isLoading" class="h-5 mr-2" />
        {{ $t('wizard.save_cont') }}
      </button>
    </div>
  </form>
</template>

<script>
import { SaveIcon } from '@vue-hero-icons/outline'
import { validationMixin } from 'vuelidate'
const { required, numeric, url } = require('vuelidate/lib/validators')

export default {
  components: {
    SaveIcon,
  },
   props: {
    configData: {
      type: Object,
      require: true,
      default: Object,
    },
    isLoading: {
      type: Boolean,
      require: true,
      default: false,
    },
    isFetching: {
      type: Boolean,
      require: true,
      default: false,
    },
  },
  data() {
    return {
      databaseData: {
        database_connection: 'mysql',
        database_hostname: '127.0.0.1',
        database_port: '3306',
        database_name: null,
        database_username: null,
        database_password: null,
        app_url: window.location.origin,
        app_domain: window.location.origin.replace(/(^\w+:|^)\/\//, ''),
      },
      connections: ['sqlite', 'mysql', 'pgsql', 'sqlsrv'],
    }
  },
  validations: {
    databaseData: {
      database_connection: {
        required,
      },
      database_hostname: {
        required,
      },
      database_port: {
        required,
        numeric,
      },
      database_name: {
        required,
      },
      database_username: {
        required,
      },
      app_url: {
        required,
        isUrl(val) {
          return this.$utils.checkValidUrl(val)
        },
      },
      app_domain: {
        required,
        isUrl(val) {
          return this.$utils.checkValidDomainUrl(val)
        },
      },
    },
  },
  computed: {
    urlError() {
      if (!this.$v.databaseData.app_url.$error) {
        return ''
      }

      if (!this.$v.databaseData.app_url.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.databaseData.app_url.isUrl) {
        return this.$tc('validation.invalid_url')
      }
    },
    domainError() {
      if (!this.$v.databaseData.app_domain.$error) {
        return ''
      }

      if (!this.$v.databaseData.app_domain.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.databaseData.app_domain.isUrl) {
        return this.$tc('validation.invalid_domain_url')
      }
    },
    connectionError() {
      if (!this.$v.databaseData.database_connection.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_connection.required) {
        return this.$tc('validation.required')
      }
    },
    portError() {
      if (!this.$v.databaseData.database_port.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_port.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.databaseData.database_port.numeric) {
        return this.$tc('validation.numbers_only')
      }
    },
    nameError() {
      if (!this.$v.databaseData.database_name.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_name.required) {
        return this.$tc('validation.required')
      }
    },
    usernameError() {
      if (!this.$v.databaseData.database_username.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_username.required) {
        return this.$tc('validation.required')
      }
    },
    hostnameError() {
      if (!this.$v.databaseData.database_hostname.$error) {
        return ''
      }

      if (!this.$v.databaseData.database_hostname.required) {
        return this.$tc('validation.required')
      }
    },
  },
   mounted() {
    for (const key in this.databaseData) {
      if (this.configData.hasOwnProperty(key)) {
        this.databaseData[key] = this.configData[key]
      }
    }
  },
  methods: {
    async next() {
      this.$v.databaseData.$touch()
      if (!this.$v.databaseData.$invalid) {
        this.$emit('submit-data', this.databaseData)
      }
      return false
    },
    onChangeConnection() {
      this.$v.databaseData.database_connection.$touch()
      this.$emit('on-change-driver', this.databaseData.database_connection)
    },
  },
}
</script>
