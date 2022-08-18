<template>
  <base-page v-if="isSuperAdmin" class="item-create">
    <div class="page-header">
            <div class="row align-items-center">
              <div class="col">
                <h3 class="page-title">{{$t('users.new_user')}}</h3>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$t('general.home')}}</router-link></li>
                  <li class="breadcrumb-item"><router-link to="/admin/users">{{$tc('users.user', 2)}}</router-link></li>
                  <li class="breadcrumb-item" v-if="$route.name === 'users.edit'">{{$t('users.edit_user')}}</li>
                  <li class="breadcrumb-item" v-else>{{$t('users.new_user')}}</li>
                </ul>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <!-- <h4 class="card-title">Basic Info</h4> -->
                  <form @submit.prevent="submitUser">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>{{$t('users.name')}} <span class="text-danger">*</span></label>
                          <input type="text"  v-model.trim="formData.name" autocomplete="off" :class="{ 'is-invalid': $v.formData.name.$error }" @input="$v.formData.name.$touch()" class="form-control">
                          <div class="invalid-feedback" v-if="$v.formData.name.$error">{{nameError}}</div>
                        </div>
                        <div class="form-group">
                          <label>{{$t('users.email')}} <span class="text-danger">*</span></label>
                          <input type="email" v-model.trim="formData.email" autocomplete="off" :class="{ 'is-invalid': $v.formData.email.$error }" @input="$v.formData.email.$touch()" class="form-control">
                          <div class="invalid-feedback" v-if="$v.formData.email.$error">{{emailError}}</div>
                        </div>
                         <div class="form-group">
                          <label>{{$t('users.password')}} <span class="text-danger">*</span></label>
                          <input type="password" v-model.trim="formData.password" autocomplete="off" :class="{ 'is-invalid': $v.formData.password.$error }" @input="$v.formData.password.$touch()" class="form-control">
                          <div class="invalid-feedback" v-if="$v.formData.password.$error">{{passwordError}}</div>
                        </div>
                        <div class="form-group">
                          <label>{{$t('users.phone')}}</label>
                          <input type="text" v-model.trim="formData.phone" :class="{ 'is-invalid': $v.formData.phone.$error }" @input="$v.formData.phone.$touch()" autocomplete="off" class="form-control">
                           <div class="invalid-feedback" v-if="$v.formData.phone.$error">{{phoneError}}</div>
                        </div>
                        <button class="btn btn-primary" :loading="isLoading" :disabled="isLoading" type="submit"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>Save User</button>
                      </div>
                    
                    </div>
                  </form>
                 
                </div>
              </div>
            </div>
          </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const {
  required,
  minLength,
  email,
  numeric,
  minValue,
  maxLength,
  requiredIf,
  alpha,
} = require('vuelidate/lib/validators')

export default {
  data() {
    return {
      isLoading: false,
      title: 'Add User',

      formData: {
        name: '',
        email: null,
        password: null,
        phone: null,
      },
    }
  },

  computed: {
    ...mapGetters('user', ['currentUser']),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    pageTitle() {
      if (this.$route.name === 'users.edit') {
        return this.$t('users.edit_user')
      }
      return this.$t('users.new_user')
    },

    isEdit() {
      if (this.$route.name === 'users.edit') {
        return true
      }
      return false
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$t('validation.required')
      }
    
      if (!this.$v.formData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.formData.name.$params.minLength.min,
          { count: this.$v.formData.name.$params.minLength.min }
        )
      }
    },

    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }

      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }

      if (!this.$v.formData.email.required) {
        return this.$tc('validation.required')
      }
    },

    phoneError() {
      if (!this.$v.formData.phone.$error) {
        return ''
      }

      if (!this.$v.formData.phone.numeric) {
        return "Phone number should contain numbers only"
      }
    },

    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.required) {
        return this.$t('validation.required')
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },
  },

  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
    if (this.isEdit) {
      this.loadEditData()
    }
  },

  mounted() {
    this.$v.formData.$reset()
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      email: {
        email,
        required,
      },
      phone: {
        numeric
      },
      password: {
        required: requiredIf(function () {
          return !this.isEdit
        }),
        minLength: minLength(8),
      },
    },
  },

  methods: {
    ...mapActions('users', ['addUser', 'fetchUser', 'updateUser']),

    async loadEditData() {
      let response = await this.fetchUser(this.$route.params.id)

      if (response.data) {
        this.formData = { ...this.formData, ...response.data.user }
      }
    },

    async submitUser() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      try {
        let response
        this.isLoading = true
        if (this.isEdit) {
          response = await this.updateUser(this.formData)
          if (response.data.success) {
            window.toastr['success'](this.$tc('users.updated_message'))
            this.$router.push('/admin/users')
            this.isLoading = false
          }
          if (response.data.error) {
            window.toastr['error'](this.$t('validation.email_already_taken'))
          }
        } else {
          response = await this.addUser(this.formData)
         
          if (response.data.success) {
            this.isLoading = false
            if (!this.isEdit) {
              window.toastr['success'](this.$tc('users.created_message'))
              this.$router.push('/admin/users')
              return true
            }
          }
        }
      } catch (err) {
        if (err.response.data.errors.email) {
          this.isLoading = false
        }
      }
    },
  },
}
</script>
