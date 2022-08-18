<template>
      <!-- Form -->
        <form @submit.prevent="validateBeforeSubmit">
          <div class="form-group">
              <label class="form-control-label">Email Address</label>
              <input class="form-control" :class="{'is-invalid': $v.formData.email.$error}"  :disabled="isSent" name="email" v-model="formData.email"  @blur="$v.formData.email.$touch()" type="text">
              <div v-if="$v.formData.email.$error">
                <span v-if="!$v.formData.email.required" class="text-sm text-danger">
                {{ $t('validation.required') }}
                </span>
                <span v-if="!$v.formData.email.email" class="text-sm text-danger">
                {{ $t('validation.email_incorrect') }}
                </span>
              </div>
          </div>
          <div class="form-group mb-0">
              <button class="btn btn-lg btn-block btn-primary" type="submit">
                <div v-if="!isSent">
                    {{ $t('validation.send_reset_link') }}
                </div>
                <div v-else>
                    {{ $t('validation.not_yet') }}
                </div>
              </button>
          </div>
        </form>   
      <!-- /Form -->
</template>

<script type="text/babel">
// import { async } from 'q'
const { required, email } = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      formData: {
        email: '',
      },
      isSent: false,
      isLoading: false,
    }
  },
  validations: {
    formData: {
      email: {
        email,
        required,
      },
    },
  },
  methods: {
    async validateBeforeSubmit(e) {
      this.$v.formData.$touch()
      if (!this.$v.formData.$invalid) {
        try {
          this.isLoading = true
          let res = await axios.post(
            '/api/v1/auth/password/email',
            this.formData
          )

          if (res.data) {
            toastr['success']('Mail sent successfuly!', 'Success')
          }

          this.isSent = true
          this.isLoading = false
        } catch (err) {
          this.isLoading = false
        }
      }
    },
  },
}
</script>
