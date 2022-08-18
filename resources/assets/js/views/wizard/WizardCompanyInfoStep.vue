<template>
  <div>
    <h3>{{$t('wizard.company_info')}}</h3>
    <div>{{$t('wizard.company_info_desc')}}</div>
    <base-loader v-if="isFetching" :show-bg-overlay="true" />
    <form action="" @submit.prevent="next()">
      <div>
        <div class="row mt-4">
          <div class="form-group col-md-6">
            <label>{{$tc('settings.company_info.company_logo')}}</label>
            <div id="logo-box">
              <div>
                  <input type="file" id="fileinput" v-if="!previewLogo"  @change="onFileChange" @click="clearValue(1)" />
                  </div>
                  <div>
                    <img :src="previewLogo" style="width:100px" id="previewImg" class= "avatar-img" v-if="previewLogo"  @click="changeFileImage" @error="altImg"  />
                  </div>
                  <input type="file" class="d-none" id="fileimageinput"  @change="onFileChange"  @click="clearValue(2)"/>
              </div>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-6">
            <label>{{$t('wizard.company_name')}}</label>
            <input
             :class="{ 'is-invalid': $v.companyData.name.$error }"
              v-model.trim="companyData.name"
              type="text"
              name="name"
              class="form-control"
              @input="$v.companyData.name.$touch()"
            />
            <div class="invalid-feedback" v-if="$v.companyData.name.$error">{{companyNameError}}</div>
          </div>
          <div class="form-group col-md-6">
            <label>{{$t('wizard.country')}}</label>
            <select
             :class="{ 'is-invalid': $v.companyData.country_id.$error }"
              v-model="country"
              class="form-control"
              :placeholder="$t('general.select_country')"
              track-by="id"
              label="name"
            > 
            <option v-for="country in countries" :key="country.id" :value="country.id">{{country.name}}</option>
            </select>
            <div class="invalid-feedback" v-if="$v.companyData.country_id.$error">{{countryError}}</div>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-6">
            <label>{{$t('wizard.state')}}</label>
            <input  v-model="companyData.state" class="form-control" name="state" type="text" />
          </div>

          <div class="form-group col-md-6">
            <label>{{$t('wizard.city')}}</label>
            <input v-model="companyData.city" class="form-control" name="city" type="text" />
          </div>
        </div>

        <div >
          <div class="row">
            <div class="form-group col-md-6">
              <label>{{$t('wizard.address')}}</label>
              <textarea
                v-model.trim="companyData.address_street_1"
                :placeholder="$t('general.street_1')"
                name="billing_street1"
                class="form-control"
                rows="2"
                @input="$v.companyData.address_street_1.$touch()"
              />
            </div>

            <div class="form-group col-md-6">
              <label></label>
              <textarea
                :invalid="$v.companyData.address_street_2.$error"
                v-model="companyData.address_street_2"
                :placeholder="$t('general.street_2')"
                name="billing_street2"
                class="form-control"
                rows="2"
                @input="$v.companyData.address_street_2.$touch()"
              />
            </div>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label>{{$t('wizard.zip_code')}}</label>
              <input v-model.trim="companyData.zip" class="form-control" type="text" name="zip" />
            </div>

            <div class="form-group col-md-6">
              <label>{{$t('wizard.phone')}}</label>
              <input
                v-model.trim="companyData.phone"
                type="text"
                name="phone"
                class="form-control"
              />
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
                        <button type="button" class="btn btn-secondary submit-btn" @click="cancelPopup"> 
                            {{$t('general.cancel')}}</button>
                        <button type="button" @click="cropImage" class="btn btn-primary submit-btn"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                            {{$t('general.save')}}</button>
                             <button type="button" data-toggle="modal" id="cancelpopup" data-dismiss="modal" class="d-none">
                            {{$t('general.save')}}</button>
                            <button type="button" data-toggle="modal" id="showpopup" data-target="#crop_image_form" class="d-none">
                            {{$t('general.save')}}</button>
                      </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button
          :loading="isLoading"
          :disabled="isLoading"
          variant="primary"
          class="btn btn-primary mt-4"
          type="submit"
        >
          {{ $t('wizard.save_cont') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { CloudUploadIcon } from '@vue-hero-icons/solid'
import { mapActions } from 'vuex'
const { required, maxLength } = require('vuelidate/lib/validators')
import {TabContent} from 'vue-step-wizard'
import 'vue-step-wizard/dist/vue-step-wizard.css'
import { Cropper } from 'vue-advanced-cropper'

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
      companyData: {
        logo: '',
        name: null,
        address_street_1: '',
        address_street_2: '',
        city: '',
        state: '',
        country_id: '',
        zip: '',
        phone: '',
      },
      isLoading: false,
      isFetching: false,
      step: 1,
      countries: [],
      country: null,
      previewLogo: null,
      fileObject: null,
      cropperOutputMime: '',
      previewImage: null,
      isRequestOnGoing: false,
      canvasDataurl: null,
      chooseFile: 1
    }
  },
  validations: {
    companyData: {
      name: {
        required,
      },
      country_id: {
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
  watch: {
    country: function(newValue, oldValue) {
      this.companyData.country_id = newValue
      return true
    },
  },
  computed: {
    companyNameError() {
      if (!this.$v.companyData.name.$error) {
        return ''
      }

      if (!this.$v.companyData.name.required) {
        return this.$tc('validation.required')
      }
    },
    countryError() {
      if (!this.$v.companyData.country_id.$error) {
        return ''
      }

      if (!this.$v.companyData.country_id.required) {
        return this.$tc('validation.required')
      }
    },
    address1Error() {
      if (!this.$v.companyData.address_street_1.$error) {
        return ''
      }

      if (!this.$v.companyData.address_street_1.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
    address2Error() {
      if (!this.$v.companyData.address_street_2.$error) {
        return ''
      }

      if (!this.$v.companyData.address_street_2.maxLength) {
        return this.$t('validation.description_maxlength')
      }
    },
  },
  mounted() {
    this.fetchCountries()
  },
  methods: {
    ...mapActions('company', ['setSelectedCompany']),

    onHandleUploadError() {
      window.toastr['error']('Oops! Something went wrong...')
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
      this.$v.companyData.$touch()

      if (this.$v.companyData.$invalid) {
        return true
      }

      this.isLoading = true
      let response = null
      try {
           response = await window.axios.put('/api/v1/company', this.companyData)
      } catch (err) {
          this.isLoading = false
      }
      

      if (response && response.data) {
        this.setSelectedCompany(response.data.company)

        if (this.fileObject && this.previewLogo) {
          let logoData = new FormData()
          logoData.append(
            'company_logo',
            JSON.stringify({
              name: this.fileObject,
              data: this.previewLogo,
            })
          )

          await axios.post('/api/v1/company/upload-logo', logoData, {
            headers: {
              'Content-Type': 'multipart/form-data',
              company: response.data.company.id,
            },
          })
        }

        this.$emit('next', 6)
        this.isLoading = false
      }
    },

    async fetchCountries() {
      this.isFetching = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.isFetching = false
    },
  },
}
</script>
