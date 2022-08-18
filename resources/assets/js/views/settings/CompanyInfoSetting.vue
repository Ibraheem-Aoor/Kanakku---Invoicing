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
                  <h5 class="card-title">{{ $t('settings.company_info.company_info') }}</h5>
                  <!-- <p>{{ $t('settings.company_info.section_description') }}</p> -->
                </div>
                <div class="card-body">
                
                  <!-- Form -->
                  <form @submit.prevent="updateCompanyData">
                       <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
                    <div class="row form-group">
                      <label for="name" class="col-sm-3 col-form-label input-label">{{$tc('settings.company_info.company_logo')}}</label>
                      <div class="col-sm-9">
                       
                        <div>
                        <input type="file" id="fileinput" v-if="!previewLogo"  @change="onFileChange" @click="clearValue(1)" />
                        </div>
                        <div>
                          <img :src="previewLogo" style="width:100px" id="previewImg" class= "avatar-img" v-if="previewLogo"  @click="changeFileImage" @error="altImg"  />
                        </div>
                         <input type="file" class="d-none" id="fileimageinput"  @change="onFileChange"  @click="clearValue(2)"/>
        
                      </div>
                    </div>
                    <div class="row form-group">
                      <label for="name" class="col-sm-3 col-form-label input-label">{{$tc('settings.company_info.company_name')}} <span class="text-danger">*</span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" v-model="formData.name" id="name" :placeholder="$t('settings.company_info.company_name')">
                      </div>
                    </div>
                    <div class="row form-group">
                      <label for="email" class="col-sm-3 col-form-label input-label">{{$tc('settings.company_info.phone')}}</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" v-model="formData.phone" id="phone" placeholder="+918943594895">
                      </div>
                    </div>
                    <div class="form-group row">
                          <label class="col-lg-3 col-form-label">{{$tc('settings.company_info.country')}} <span class="text-danger">*</span></label>
                          <div class="col-lg-9">
                            <select class="select form-control" v-model="country">
                              <option>Select</option>
                              <option v-for="item in countries" :key="item.id" v-bind:value="item.id">{{item.name}}</option>
                            </select>
                          </div>
                        </div>
                        
                      <div class="row form-group">
                      <label for="email" class="col-sm-3 col-form-label input-label">{{$tc('settings.company_info.state')}}</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control"  v-model="formData.state" id="state" placeholder="State">
                      </div>
                    </div>
                      <div class="row form-group">
                      <label for="email" class="col-sm-3 col-form-label input-label">{{$tc('settings.company_info.city')}}</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" v-model="formData.city" id="city" placeholder="Islamabad">
                      </div>
                    </div>
                     <div class="row form-group">
                      <label for="email" class="col-sm-3 col-form-label input-label">{{$tc('settings.company_info.zip')}}</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" v-model="formData.zip" id="Zip" placeholder="Zip">
                      </div>
                    </div>
                     <div class="row form-group">
                      <label for="email" class="col-sm-3 col-form-label input-label">{{$tc('settings.company_info.address')}}</label>
                      <div class="col-sm-9">
                        <textarea rows="3" columns="5" class="form-control" v-model="formData.address_street_1" id="Address" :placeholder="$tc('general.street_1')"> </textarea>
                      </div>
                    </div>
                    <div class="row form-group">
                      <label for="email" class="col-sm-3 col-form-label input-label"></label>
                      <div class="col-sm-9">
                        <textarea rows="3" columns="5" class="form-control" v-model="formData.address_street_2" id="Address2" :placeholder="$tc('general.street_2')"> </textarea>
                      </div>
                    </div>
                    
                    <div class="text-right">
                      <button type="submit" :loading="isLoading" :disabled="isLoading" class="btn btn-primary"><i v-if="!isLoading" className="fa fa-refresh fa-spin" /> {{ $tc('settings.company_info.save') }}</button>
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
                       <cropper
                          class="cropper"
                          v-bind:src="previewImage"
                          ref="cropper"
                            @change="changeImage"
                          ></cropper>
                      <div class="submit-section" >
                        <button class="btn btn-secondary submit-btn" @click="cancelPopup"> 
                            {{$t('general.cancel')}}</button>
                        <button @click="cropImage" class="btn btn-primary submit-btn"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
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
    </div>
    </base-page>
</template>

<script>
import { EditIcon } from 'vue-feather-icons'
import { mapActions, mapGetters } from 'vuex'
import { CloudUploadIcon } from '@vue-hero-icons/solid'
const { required, email, maxLength } = require('vuelidate/lib/validators')
import { Cropper } from 'vue-advanced-cropper'

export default {
  components: {
    CloudUploadIcon,
    EditIcon,
    Cropper,
  },
  data() {
    return {
      isFetchingData: false,
      formData: {
        name: null,
        email: '',
        phone: '',
        zip: '',
        address_street_1: '',
        address_street_2: '',
        website: '',
        country_id: null,
        state: '',
        city: '',
      },
      isLoading: false,
      country: null,
      passData: [],
      fileSendUrl: '/api/v1/settings/company',
      previewLogo: null,
      fileObject: null,
      cropperOutputMime: '',
      previewImage: null,
      isRequestOnGoing: false,
      canvasDataurl: null,
      chooseFile: 1
    }
  },
  watch: {
    country(newCountry) {
      this.formData.country_id = newCountry
      if (this.isFetchingData) {
        return true
      }
    },
  },
  validations: {
    formData: {
      name: {
        required,
      },
      address_street_1: {
        maxLength: maxLength(255),
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
  },
  computed: {
    ...mapGetters(['countries']),
    nameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      }
    },
    countryError() {
      if (!this.$v.formData.country_id.$error) {
        return ''
      }
      if (!this.$v.formData.country_id.required) {
        return this.$tc('validation.required')
      }
    },
    address1Error() {
      if (!this.$v.formData.address_street_1.$error) {
        return ''
      }

      if (!this.$v.formData.address_street_1.maxLength) {
        return this.$tc('validation.address_maxlength')
      }
    },
    address2Error() {
      if (!this.$v.formData.address_street_2.$error) {
        return ''
      }

      if (!this.$v.formData.address_street_2.maxLength) {
        return this.$tc('validation.address_maxlength')
      }
    },
  },
  mounted() {
    this.setInitialData()
  },
  methods: {
    ...mapActions('company', ['updateCompany', 'updateCompanyLogo']),
    ...mapActions('user', ['fetchCurrentUser']),
    onUploadHandler(cropper) {
      this.previewLogo = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
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
      this.canvasDataurl = canvas.toDataURL()
    },
    cropImage() {
        this.previewLogo =  this.canvasDataurl ? this.canvasDataurl : this.previewImage
        document.getElementById('cancelbutton').click()
    },
    changeFileImage() {
         document.getElementById("fileimageinput").click()
    },
    altImg(e) {
      e.target.src = '/assets/img/logo.png'
    },
    cancelPopup() {
        if(this.chooseFile === 1) {
          document.getElementById("fileinput").value= null
        }
        this.previewImage = null
        this.fileObject = null
        document.getElementById('cancelbutton').click()
    },
    async onFileChange(evt) {
      this.previewImage = null

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
      if(input.files[0]) {
           $("#crop_image_form").modal("show")
      }
     
    },
    async setInitialData() {
      this.isRequestOnGoing = true
      let response = await this.fetchCurrentUser()
      this.isFetchingData = true
      if (response.data.user) {
        this.formData.name = response.data.user.company.name
        this.formData.address_street_1 =
          response.data.user.company.address.address_street_1
        this.formData.address_street_2 =
          response.data.user.company.address.address_street_2
        this.formData.zip = response.data.user.company.address.zip
        this.formData.phone = response.data.user.company.address.phone
        this.formData.state = response.data.user.company.address.state
        this.formData.city = response.data.user.company.address.city
        this.country = response.data.user.company.address.country && response.data.user.company.address.country.id;
        this.previewLogo = response.data.user.company.logo
      }
      this.isRequestOnGoing = false
    },
    async updateCompanyData() {
      this.$v.formData.$touch()
      if (this.$v.$invalid) {
        return true
      }
      this.isLoading = true

      let response = await this.updateCompany(this.formData)
      if (response.data.success) {
        this.isLoading = false
        if (this.fileObject && this.previewLogo) {
          let logoData = new FormData()
          logoData.append(
            'company_logo',
            JSON.stringify({
              name: this.fileObject,
              data: this.previewLogo,
            })
          )
          await this.updateCompanyLogo(logoData)
        }
        this.isLoading = false

        window.toastr['success'](
          this.$t('settings.company_info.updated_message')
        )
        return true
      }
      this.isLoading = false
      window.toastr['error'](response.data.error)
      return true
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