<template>
 <base-page>
    
    <div class="page-header">
            <div class="row">
              <div class="col-sm-6">
                <h3 class="page-title">{{ $t('navigation.settings') }}</h3>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><router-link to="/admin/dashboard">{{ $t('general.home') }}</router-link>
                  </li>
                  <li class="breadcrumb-item active">{{ $t('navigation.settings') }}</li>
                </ul>
              </div>
            </div>
          </div>
        
          <div class="row">
            <div class="col-xl-3 col-md-4">
            
              <!-- Settings Menu -->
              <div class="widget settings-menu">
                <ul>
                  <li class="nav-item">
                    <router-link to="/admin/settings/user-profile" class="nav-link">
                      <i class="far fa-user"></i> <span>{{ $t('settings.account_settings.account_settings') }}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/company-info" class="nav-link">
                      <i class="far fa-list-alt"></i> <span>{{$t('settings.menu_title.company_information')}}</span>
                    </router-link>
                  </li>
                   <li class="nav-item">
                    <router-link to="/admin/settings/customization" class="nav-link">
                      <i class="fas fa-edit"></i> <span>{{$t('settings.menu_title.customization')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/preferences" class="nav-link">
                      <i class="fas fa-cog"></i> <span>{{$t('settings.menu_title.preferences')}}</span>
                    </router-link>
                  </li>
                  
                   <li class="nav-item">
                    <router-link to="/admin/settings/notifications" class="nav-link">
                      <i class="far fa-bell"></i> <span>{{$t('settings.menu_title.notifications')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/tax-types" class="nav-link">
                      <i class="far fa-check-square"></i> <span>{{$t('settings.menu_title.tax_types')}}</span>
                    </router-link>
                  </li>
                   <li class="nav-item">
                    <router-link to="/admin/settings/payment-mode" class="nav-link">
                      <i class="far fa-credit-card"></i> <span>{{$t('settings.menu_title.payment_modes')}}</span>
                    </router-link>
                  </li>
                   <li class="nav-item">
                    <router-link to="/admin/settings/custom-fields" class="nav-link">
                      <i class="far fa-bell"></i> <span>{{$t('settings.menu_title.custom_fields')}}</span>
                    </router-link>
                  </li>
                  
                  <li class="nav-item">
                    <router-link to="/admin/settings/expense-category" class="nav-link">
                      <i class="far fa-list-alt"></i> <span>{{$t('settings.menu_title.expense_category')}}</span>
                    </router-link>
                  </li>
                  <li class="nav-item">
                    <router-link to="/admin/settings/mail-configuration" class="nav-link">
                      <i class="far fa-check-square"></i> <span>{{$t('settings.mail.mail_config')}}</span>
                    </router-link>
                  </li>
                  
                </ul>
              </div>
              <!-- /Settings Menu -->
              
            </div>
            
            <div class="col-xl-9 col-md-8 mt-3">
            
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title">{{ $t('settings.account_settings.account_settings') }}</h5>
                  <p> {{ $t('settings.account_settings.section_description') }}</p>
                </div>
                <div class="card-body">
                
                  <!-- Form -->
                  <form  @submit.prevent="updateUserData">
                  <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
                    <div class="row form-group">
                      <label for="name" class="col-sm-3 col-form-label input-label">{{ $tc('settings.account_settings.profile_picture') }}</label>
                      <div class="col-sm-9">
                       
                        <div>
                        <input type="file" id="fileinput"  v-if="!previewAvatar"  @change="onFileChange" @click="clearValue(1)" />
                        </div>
                        <div>
                            <img :src="previewAvatar" id="previewImg" style="width:100px" v-if="previewAvatar" class="avatar-img" @click="changeFileImage"  @error="altImg"  />
                        </div>
                        <input type="file" id="fileimageinput" class="d-none" @change="onFileChange" @click="clearValue(2)" />
                      </div>
                    </div>
                    <div class="row form-group">
                      <label for="name" class="col-sm-3 col-form-label input-label">{{$tc('settings.account_settings.name')}}</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" v-model="formData.name" :class="{ 'is-invalid': $v.formData.name.$error }" @input="$v.formData.name.$touch()"  id="name"  placeholder="Your Name">
                        <div class="invalid-feedback" v-if="$v.formData.name.$error">{{nameError}}</div>
                      </div>
                    </div>
                    <div class="row form-group">
                      <label for="email" class="col-sm-3 col-form-label input-label">{{$tc('settings.account_settings.email')}}</label>
                      <div class="col-sm-9">
                        
                        <input type="email" class="form-control"  :class="{ 'is-invalid': $v.formData.email.$error }" @input="$v.formData.email.$touch()"  id="email" v-model="formData.email" :placeholder="$t('settings.user_profile.email')">
                        <div class="invalid-feedback" v-if="$v.formData.email.$error">{{emailError}}</div>
                      </div>
                    </div>
                    
                    <div class="row form-group">
                      <label for="phone" class="col-sm-3 col-form-label input-label">{{$tc('settings.account_settings.password')}}</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" :class="{ 'is-invalid': $v.formData.password.$error }" v-model="formData.password" @input="$v.formData.password.$touch()" id="password" placeholder="**********">
                        <div class="invalid-feedback" v-if="$v.formData.password.$error">{{passwordError}}</div>
                      </div>
                    </div>
                     <div class="row form-group">
                      <label for="phone" class="col-sm-3 col-form-label input-label">{{$tc('settings.account_settings.confirm_password')}}</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" v-model="formData.confirm_password" id="confirm_password" :class="{ 'is-invalid': $v.formData.confirm_password.$error}" @input="$v.formData.confirm_password.$touch()" placeholder="***********">
                         <div class="invalid-feedback" v-if="$v.formData.confirm_password.$error">{{confirmPasswordError}}</div>
                      </div>
                    </div>
                    <div class="form-group row">
                          <label class="col-lg-3 col-form-label">{{$tc('settings.language')}}</label>
                          <div class="col-lg-9">
                            <select class="select form-control" v-model="language">
                              <option>{{$tc('settings.preferences.select_language')}}</option>
                              <option v-for="item in languages" v-bind:key="item.code" :value="item.code">{{item.name}}</option>
                            </select>
                          </div>
                        </div>

                    <div class="text-right">
                      <button type="submit" v-if="!isLoading"  class="btn btn-primary">{{$tc('settings.account_settings.save')}}</button>
                    </div>
                    
                  </form>
                  <!-- /Form -->
                  
                </div>
              </div>
            </div>
            <div id="crop_image_form" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Crop Image</h5>
                    <button type="button" class="close" data-dismiss="modal" id="cancelbutton" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                       <Cropper
                          class="cropper"
                          v-bind:src="previewImage"
                          @change="changeImage"
                          ref="cropper"
                          :stencil-props="{
			                    	aspectRatio: 1,
                          }"
                          :canvas="{
                            height: 400,
                            width: 400
                          }"
                          />
                      <div class="submit-section" >
                        <button class="btn btn-secondary submit-btn" @click="cancelpopup"> 
                            {{$t('general.cancel')}}</button>
                        <button :disabled="isLoading" @click="cropImage" class="btn btn-primary submit-btn"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                            {{$t('general.save')}}</button>
                             <button data-toggle="modal" id="cancelpopup" data-dismiss="modal" class="d-none">
                            {{$t('general.save')}}</button>
                            <button data-toggle="modal" id="showpopup" data-target="#crop_image_form" class="d-none">
                            {{$t('general.save')}}</button>
                      </div>
                </div>
              </div>
            </div>
          </div>
    
</button>
          </div>
   
    </base-page>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import { CloudUploadIcon } from '@vue-hero-icons/solid'
import BaseLoader from '../../components/base/BaseLoader.vue'
import { EditIcon } from 'vue-feather-icons'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const {
  required,
  requiredIf,
  sameAs,
  email,
  minLength,
} = require('vuelidate/lib/validators')

export default {
  components: {
    CloudUploadIcon,
    BaseLoader,
    EditIcon,
    Cropper,
  },

  data() {
    return {
      formData: {
        name: null,
        email: null,
        password: null,
        confirm_password: null,
      },
      isLoading: false,
      previewAvatar: null,
      previewImage: '/assets/img/default-avatar.jpg',
      canvasDataurl: null,
      cropperOutputMime: '',
      fileObject: null,
      language: null,
      isRequestOnGoing: false,
      chooseFile: 1,
    }
  },

  validations: {
    formData: {
      name: {
        required,
      },
      email: {
        required,
        email,
      },
      password: {
        minLength: minLength(8),
      },
      confirm_password: {
        sameAsPassword: sameAs('password'),
      },
    },
    language: {
      required,
    },
  },

  computed: {
    ...mapGetters(['languages']),

    emailError() {
      if (!this.$v.formData.email.$error) {
        return ''
      }
      if (!this.$v.formData.email.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },

    passwordError() {
      if (!this.$v.formData.password.$error) {
        return ''
      }
      if (!this.$v.formData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.formData.password.$params.minLength.min,
          { count: this.$v.formData.password.$params.minLength.min }
        )
      }
    },

    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },

    confirmPasswordError() {
      if (!this.$v.formData.confirm_password.$error) {
        return ''
      }

      if (!this.$v.formData.confirm_password.sameAsPassword) {
        return this.$tc('validation.password_incorrect')
      }
    },

    languageError() {
      if (!this.$v.language.$error) {
        return ''
      }
      if (!this.$v.language.required) {
        return this.$tc('validation.required')
      }
    },
  },

  watch: {
    'formData.password'(val) {
      if (!val) {
        this.formData.confirm_password = ''
      }
    },
  },

  mounted() {
    this.setInitialData()
    this.fetchLanguages()
  },

  methods: {
    ...mapActions('user', [
      'fetchCurrentUser',
      'updateCurrentUser',
      'fetchUserSettings',
      'updateUserSettings',
      'uploadAvatar',
    ]),
  
    ...mapActions(['fetchLanguages']),

    onUploadHandler(cropper) {
      this.previewAvatar = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
    },
    altImg(e) {
      e.target.src = '/assets/img/default-avatar.jpg'
    },
    onHandleUploadError() {
      window.toastr['error']('Oops! Something went wrong...')
    },
    clearValue(value) {
      this.chooseFile = value
     if(value === 1) {
        document.getElementById("fileinput").value= null
     } else {
        document.getElementById("fileimageinput").value= null
     }
     
    },
    changeImage({coordinates, canvas}) {
      this.canvasDataurl = canvas && canvas.toDataURL()
    },
    cropImage() {
        this.previewAvatar =  this.canvasDataurl ? this.canvasDataurl : this.previewImage
        document.getElementById('cancelbutton').click()
    },
    changeFileImage() {
       document.getElementById("fileimageinput").click()
    },
    cancelpopup() {
        if(this.chooseFile === 1) {
          document.getElementById("fileinput").value= null
        }
        this.previewImage = null
        this.fileObject = null
        document.getElementById('cancelbutton').click()
    },
    async onFileChange(evt) {
        this.$refs.cropper.refresh()
        let input = evt.target;
              if (input.files && input.files[0]) {
                let reader = new FileReader(); 
                this.fileObject = input.files && input.files[0].name
                  reader.onload = (e) => {
                      this.previewImage = null
                      let i = 0
                      setInterval(()=> {
                        if(i===1) {
                          this.previewImage = e.target.result;
                        }
                        i++
                      },200)
                  };
                  reader.readAsDataURL(input.files[0]);
              }
              document.getElementById("showpopup").click()
    },

    async setInitialData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCurrentUser()

      this.formData.name = response.data.user.name
      this.formData.email = response.data.user.email

      if (response.data.user.avatar) {
        this.previewAvatar = response.data.user.avatar
      } else {
        this.previewAvatar = '/assets/img/default-avatar.jpg'
      }

      let res = await this.fetchUserSettings(['language'])
      this.language =  res.data.language && this.languages.length && this.languages.find(
        (language) => language.code == res.data.language
      ).code;
      this.isRequestOnGoing = false
    },

    async updateUserData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true

      let data = {
        name: this.formData.name,
        email: this.formData.email,
      }
      try {
        if (
          this.formData.password != null &&
          this.formData.password !== undefined &&
          this.formData.password !== ''
        ) {
          data = { ...data, password: this.formData.password }
        }

        let response = await this.updateCurrentUser(data)

        let languageData = {
          settings: {
            language: this.language,
          },
        }

        let languageRes = await this.updateUserSettings(languageData)

        if (response.data.success) {
          this.isLoading = false

          if (this.fileObject && this.previewAvatar) {
            let avatarData = new FormData()

            avatarData.append(
              'admin_avatar',
              JSON.stringify({
                name: this.fileObject,
                data: this.previewAvatar,
              })
            )

            this.uploadAvatar(avatarData)
          }
          await this.fetchCurrentUser()
          window.toastr['success'](
            this.$t('settings.account_settings.updated_message')
          )

          this.formData.password = ''
          this.formData.confirm_password = ''
        }
      } catch (error) {
        this.isLoading = false
        return true
      }
    },
  },
}
</script>

<style scoped>
.cropper {
  height: 600px;
  width: 500px;
  background: #DDD;
}
</style>
