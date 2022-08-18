<template>
  <form id="loginForm" @submit.prevent="validateBeforeSubmit">
   <div class="form-group">
      <label class="form-control-label">{{$t('login.email')}}</label>
      <input type="email" v-model="loginData.email"  :class="{ 'is-invalid': $v.loginData.email.$error }" @input="$v.loginData.email.$touch()" focus class="form-control">
      <div class="invalid-feedback" v-if="$v.loginData.email.$error">{{emailError}}</div>
   </div>
   <div class="form-group">
      <label class="form-control-label">{{$t('login.password')}}</label>
      <div class="pass-group">
         <input :type="getInputType" v-model="loginData.password" id="password" :class="{ 'is-invalid': $v.loginData.password.$error }" @input="$v.loginData.password.$touch()" class="form-control pass-input">
         <span class="fas fa-eye toggle-password" v-if="isShowPassword" @click="isShowPassword = !isShowPassword"></span>
         <span class="fas fa-eye-slash toggle-password" v-else @click="isShowPassword = !isShowPassword"></span>
         <div class="invalid-feedback" v-if="$v.loginData.password.$error">{{passwordError}}</div>
      </div>
   </div>
   <div class="form-group">
      <div class="row">
         <div class="col-6">
            <div class="custom-control custom-checkbox">
               <input type="checkbox" value="lsRememberMe"  v-on:click="lsRememberMe()" id="rememberMe" style="margin-left: -24px;">
               <label  for="cb1" >Remember me</label>
            </div>
         </div>
         <div class="col-6 text-right">
            <router-link class="forgot-link" to="/forgot-password">Forgot Password ?</router-link>
         </div>
      </div>
   </div>
   <button class="btn btn-lg btn-block btn-primary" :disabled="isLoading" type="submit"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i> {{ $t('login.login') }} </button>
</form>
</template>

<script type="text/babel">
import { mapActions } from 'vuex'
import { EyeIcon, EyeOffIcon } from '@vue-hero-icons/outline'
const { required, email, minLength } = require('vuelidate/lib/validators')
const CryptoJS = require('crypto-js')
export default {
  components: {
    EyeIcon,
    EyeOffIcon,
  },
  data() {
    return {
      secret: "123#$%",
      loginData: {
        email: '',
        password: '',
        remember: '',
      },
      submitted: false,
      isLoading: false,
      isShowPassword: false,
    }
  },
  validations: {
    loginData: {
      email: {
        required,
        email,
      },
      password: {
        required,
        minLength: minLength(8),
      },
    },
  },
  computed: {
    emailError() {
      if (!this.$v.loginData.email.$error) {
        return ''
      }
      if (!this.$v.loginData.email.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.loginData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    passwordError() {
      if (!this.$v.loginData.password.$error) {
        return ''
      }
      if (!this.$v.loginData.password.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.loginData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.loginData.password.$params.minLength.min,
          { count: this.$v.loginData.password.$params.minLength.min }
        )
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
        const rmCheck = document.getElementById("rememberMe")
        if (localStorage.getItem("rememberMe") !== "" && localStorage.getItem("rememberMe") !== null && localStorage.getItem("rememberMe") == "true") {
          rmCheck.setAttribute("checked", "checked");
          this.loginData.email = CryptoJS.AES.decrypt(this.getEncryptedEmailData(), this.secret).toString(CryptoJS.enc.Utf8)
          let decrypted = CryptoJS.AES.decrypt(this.getEncryptedPassData(), this.secret).toString(CryptoJS.enc.Utf8)
          this.loginData.password = decrypted
        } else {
          rmCheck.removeAttribute("checked");
          this.loginData.email = "";
          this.loginData.password = "";
        }
    },
  methods: {
    ...mapActions('auth', ['login', 'getLoggedDetails']),

    ...mapActions('user', ['fetchCurrentUser']),
   
    async validateBeforeSubmit() {
      axios.defaults.withCredentials = true

      this.$v.loginData.$touch()
      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      
      try {
        await this.login(this.loginData)
        await this.fetchCurrentUser()
        this.lsRememberMe()
        this.$router.push('/admin/dashboard')
        this.isLoading = false
      } catch (error) {
        this.isLoading = false
      }
    },
      encryptPassData(password) {
        if (password.length > 0) {
          // hash the name with any algorithm
          const data = CryptoJS.AES.encrypt(password, this.secret).toString();

          // store into localStorage
          localStorage.setItem("enable_pass", data);
          // display the encrypted data
          this.getEncryptedPassData();
        }
      },
      encryptEmailData(email) {
        if (email.length > 0) {
          // hash the name with any algorithm
          const data = CryptoJS.AES.encrypt(email, this.secret).toString();

          // store into localStorage
          localStorage.setItem("enable_email", data);
          // display the encrypted data
          this.getEncryptedEmailData();
        }
      },
      getEncryptedPassData() {
          return localStorage.getItem("enable_pass")
      },
      getEncryptedEmailData() {
          return localStorage.getItem("enable_email")
      },
      lsRememberMe() {
        const rmCheck = document.getElementById("rememberMe");
        if (rmCheck.checked && this.loginData.email !== "" && this.loginData.email !== null) {
          localStorage.setItem("rememberMe", rmCheck.checked)
          this.encryptEmailData(this.loginData.email)
          this.encryptPassData(this.loginData.password)
        } else {
          localStorage.setItem("rememberMe", false)
          localStorage.setItem("enable_email", "")
          localStorage.setItem("enable_pass", "")
        }
    } ,
   
 
  },
}
</script>
