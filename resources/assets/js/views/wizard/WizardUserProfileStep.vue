<template>
<div>
  <div>
    <h3>{{$t('wizard.account_info')}}</h3>
    <div>{{$t('wizard.account_info_desc')}}</div>
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <form  @submit.prevent="next()" novalidate>
      <div class="row">
        <div class="form-group col-md-12"
          :label="$tc('settings.account_settings.profile_picture')"
        >
        <label>{{$tc('general.choose_file')}}</label>
          <div>
          <input type="file" id="fileinput"  v-if="!previewAvatar"  @change="onFileChange" @click="clearValue(1)" />
          </div>
          <div>
              <img :src="previewAvatar" id="previewImg" style="width:100px" v-if="previewAvatar" class="avatar-img" @click="changeFileImage"  @error="altImg"  />
          </div>
          <input type="file" id="fileimageinput" class="d-none" @change="onFileChange" @click="clearValue(2)" />
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label>{{$t('wizard.name')}}</label>
          <input
             :class="{ 'is-invalid': $v.profileData.name.$error }"
            v-model.trim="profileData.name"
            type="text"
            class="form-control"
            name="name"
            @input="$v.profileData.name.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.profileData.name.$error">{{nameError}}</div>
        </div>

        <div class="form-group col-md-6">
          <label>{{$t('wizard.email')}}</label>
          <input
            :class="{ 'is-invalid': $v.profileData.email.$error }"
            v-model.trim="profileData.email"
            type="text"
            class="form-control"
            name="email"
            @input="$v.profileData.email.$touch()"
          />
           <div class="invalid-feedback" v-if="$v.profileData.email.$error">{{emailError}}</div>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
        <label>{{$t('wizard.password')}}</label>
          <input
            :class="{ 'is-invalid': $v.profileData.password.$error }"
            v-model.trim="profileData.password"
            type="password"
            name="password"
            class="form-control"
            @input="$v.profileData.password.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.profileData.password.$error">{{passwordError}}</div>
        </div>

        <div class="form-group col-md-6">
        <label>{{$t('wizard.confirm_password')}}</label>
          <input
            :class="{ 'is-invalid': $v.profileData.confirm_password.$error }"
            v-model.trim="profileData.confirm_password"
            type="password"
            name="confirm_password"
            class="form-control"
            @input="$v.profileData.confirm_password.$touch()"
          />
          <div class="invalid-feedback" v-if="$v.profileData.confirm_password.$error">{{confirmPasswordError}}</div>
        </div>
      </div>
         
    
      <button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="btn btn-primary mt-4"
      >
        {{ $t('wizard.save_cont') }}
      </button>
    </form>
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
                        <button type="button" class="btn btn-secondary submit-btn" @click="cancelpopup"> 
                            {{$t('general.cancel')}}</button>
                        <button  type="button" :disabled="isLoading" @click="cropImage" class="btn btn-primary submit-btn"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                            {{$t('general.save')}}</button>
                             <button type="button" data-toggle="modal" id="cancelpopup" data-dismiss="modal" class="d-none">
                            {{$t('general.save')}}</button>
                            <button type="button" data-toggle="modal" id="showpopup" data-target="#crop_image_form" class="d-none">
                            </button>
                      </div>
                </div>
              </div>
            </div>
          </div>
</div>
</template>

<script>
import { CloudUploadIcon } from '@vue-hero-icons/solid'
import { mapActions } from 'vuex'
import {TabContent} from 'vue-step-wizard'
import 'vue-step-wizard/dist/vue-step-wizard.css'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const {
  required,
  requiredIf,
  sameAs,
  minLength,
  email,
} = require('vuelidate/lib/validators')

export default {
  components: {
    CloudUploadIcon,
    Cropper,
    'tab-content': TabContent,
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
      profileData: {
        name: null,
        email: null,
        password: null,
        confirm_password: null,
      },
      isLoading: false,
      previewAvatar: '/assets/img/default-avatar.jpg',
      canvasDataurl: null,
      cropperOutputMime: '',
      fileObject: null,
      language: null,
      isRequestOnGoing: true,
      chooseFile: 1,
      previewImage: "",
    }
  },
  validations: {
    profileData: {
      name: {
        required,
        minLength: minLength(3),
      },
      email: {
        email,
        required,
      },
      password: {
        required,
        minLength: minLength(8),
      },
      confirm_password: {
        required: requiredIf('isRequired'),
        sameAsPassword: sameAs('password'),
      },
    },
  },
  mounted() {
      this.isRequestOnGoing = true
      setTimeout(() => {
        this.isRequestOnGoing = false
      }, 500)
  },
  computed: {
    emailError() {
      if (!this.$v.profileData.email.$error) {
        return ''
      }
      if (!this.$v.profileData.email.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.profileData.email.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    nameError() {
      if (!this.$v.profileData.name.$error) {
        return ''
      }
      if (!this.$v.profileData.name.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.profileData.name.minLength) {
        return this.$tc(
          'validation.name_min_length',
          this.$v.profileData.name.$params.minLength.min,
          { count: this.$v.profileData.name.$params.minLength.min }
        )
      }
    },
    passwordError() {
      if (!this.$v.profileData.password.$error) {
        return ''
      }
      if (!this.$v.profileData.password.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.profileData.password.minLength) {
        return this.$tc(
          'validation.password_min_length',
          this.$v.profileData.password.$params.minLength.min,
          { count: this.$v.profileData.password.$params.minLength.min }
        )
      }
    },
    confirmPasswordError() {
      if (!this.$v.profileData.confirm_password.$error) {
        return ''
      }
      if (!this.$v.profileData.confirm_password.sameAsPassword) {
        return this.$tc('validation.password_incorrect')
      }
    },
    isRequired() {
      if (
        this.profileData.password === null ||
        this.profileData.password === undefined ||
        this.profileData.password === ''
      ) {
        return false
      }
      return true
    },
  },
  methods: {
    ...mapActions('user', ['uploadAvatar']),
 
     altImg(e) {
      e.target.src = '/assets/img/default-avatar.jpg'
    },
   
    onChange(file) {
      this.cropperOutputMime = file.type
      this.fileObject = file
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
      this.canvasDataurl = canvas.toDataURL()
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
        let regex = /(\.jpg|\.jpeg|\.png|\.gif|\.ico)$/i;
       if (input.files && regex.test(input.files[0].name)) {
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
        } else {
          window.toastr['error']('Please upload valid file format')
        }
        document.getElementById("showpopup").click()
    },

    async next() {
      this.$v.profileData.$touch()
      if (this.$v.profileData.$invalid) {
        return true
      }
      this.isLoading = true
      let response = null
      try {
           response = await window.axios.put('/api/v1/me', this.profileData)
      } catch(err) {
        this.isLoading = false
      }
      

      if (response && response.data) {
        if (this.fileObject && this.previewAvatar) {
          let avatarData = new FormData()
          avatarData.append(
            'admin_avatar',
            JSON.stringify({
                name: this.fileObject,
                data: this.previewAvatar,
                id: response.data.user.id,
            })
          )

          this.uploadAvatar(avatarData)
        }
        this.$emit('next', 5)
        this.isLoading = false
      }
      return true
    },
  },
}
</script>

<style lang="scss">
.avatar-upload {
  @keyframes fadeIn {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
}
</style>
