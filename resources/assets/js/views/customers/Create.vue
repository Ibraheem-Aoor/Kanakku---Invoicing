<template>
  <base-page class="customer-create">
    <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="page-title">{{$tc('customers.customer', 2)}}</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><router-link to="/admin/dashboard">{{$tc('customers.title')}}</router-link></li>
									<li class="breadcrumb-item"><router-link to="/admin/customers">{{$tc('customers.customer', 2)}}</router-link></li>
									<li class="breadcrumb-item active"  v-if="$route.name === 'customers.edit'"><router-link to="/admin/customers">{{$t('customers.edit_customer')}}</router-link></li>
                  <li class="breadcrumb-item active"  v-else>{{$tc('customers.new_customer')}}</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-body">
									<h4 class="card-title">{{ $t('customers.basic_info') }}</h4>
									<form v-if="!initLoad" @submit.prevent="submitCustomerData">
                    <div class="row">
                      <label for="name" class="col-sm-3 col-form-label input-label">{{ $tc('settings.account_settings.profile_picture') }}</label>
                      <div class="col-sm-9">
                       
                        <div>
                        <input type="file" id="fileinput"  v-if="!previewAvatar"  @change="onFileChange" @click="clearValue(1)" />
                        </div>
                        <div>
                            <img :src="previewAvatar" id="previewImg" style="width:100px"  v-if="previewAvatar" class="avatar-img" @click="changeFileImage"  @error="altImg"  />
                        </div>
                        <input type="file" id="fileimageinput" class="d-none" @change="onFileChange" @click="clearValue(2)" />
                      </div>
                    </div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group" >
													<label>{{$t('customers.display_name')}} <span class="text-danger">*</span></label>
													<input type="text" :class="{ 'is-invalid': $v.formData.name.$error }" @input="$v.formData.name.$touch()" v-model="formData.name" class="form-control">
                          <div class="invalid-feedback" v-if="$v.formData.name.$error">{{displayNameError}}</div>
                        </div>
												<div class="form-group">
													<label>{{$t('customers.email')}}</label>
													<input :class="{ 'is-invalid': $v.formData.email.$error }" v-model="formData.email" @input="$v.formData.email.$touch()" type="email" class="form-control">
                          <div class="invalid-feedback" v-if="$v.formData.email.$error">{{emailError}}</div>
                        </div>
												<div class="form-group">
													<label>{{$t('customers.primary_currency')}}</label>
                          <select class="select form-control" v-model="currency" :class="{ 'is-invalid': $v.currency.$error }">
                            <option>{{$t('customers.select_currency')}}</option>
														<option v-for="item in currencies" :key="item.id" v-bind:value="item.id">{{item.name}}</option>
													</select>
                          <div class="invalid-feedback" v-if="$v.currency.$error">Currency required</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>{{$t('customers.primary_contact_name')}}</label>
													<input type="text" v-model.trim="formData.contact_name"  class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.phone')}}</label>
													<input type="text"  :v="$v.formData.phone" v-model.trim="formData.phone" @input="$v.formData.phone.$touch()" :class="{ 'is-invalid': $v.formData.phone.$error }" class="form-control">
                          <div class="invalid-feedback" v-if="$v.formData.phone.$error">{{phoneError}}</div>
												</div>
												<div class="form-group">
													<label>{{$t('customers.website')}}</label>
													<input type="text"  v-model="formData.website" @input="$v.formData.website.$touch()" :class="{ 'is-invalid': $v.formData.website.$error }" class="form-control">
                          <div class="invalid-feedback" v-if="$v.formData.website.$error">{{urlError}}</div>
                        </div>
											</div>
										</div>
									
									<h4 class="card-title mt-4">{{ $t('customers.billing_address') }}</h4>
									
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>{{$t('customers.name')}}</label>
													<input type="text" v-model.trim="billing.name" class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.state')}}</label>
													<input type="text" v-model="billing.state" class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.address')}}</label>
													<textarea rows="3" v-model.trim="billing.address_street_1" :class="{ 'is-invalid': $v.billing.address_street_1.$error }" @input="$v.billing.address_street_1.$touch()" cols="5" class="form-control" :placeholder="$t('general.street_1')"></textarea>
                           <div class="invalid-feedback" v-if="$v.billing.address_street_1.$error">{{billAddress1Error}}</div>
                        </div>
                        	<div class="form-group">
													<textarea rows="3" v-model.trim="billing.address_street_2" :class="{ 'is-invalid': $v.billing.address_street_2.$error }"  @input="$v.billing.address_street_2.$touch()" cols="5" class="form-control" :placeholder="$t('general.street_2')"></textarea>
                          <div class="invalid-feedback" v-if="$v.billing.address_street_2.$error">{{billAddress2Error}}</div>
                      	</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>{{$t('customers.country')}}</label>
                          <select class="select form-control" v-model="billing_country" id="billing_country">
														<option>{{$t('general.select_country')}}</option>
														<option v-for="item in countries" :key="item.id" :value="item.id">{{item.name}}</option>
							
													</select>
												</div>
												<div class="form-group">
													<label>{{$t('customers.city')}}</label>
													<input type="text" v-model="billing.city"  class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.phone')}}</label>
													<input type="text" v-model.trim="billing.phone" class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.zip_code')}}</label>
													<input type="text" v-model.trim="billing.zip" class="form-control">
												</div>
											</div>
										</div>
									
									<h4 class="card-title mt-4">{{ $t('customers.shipping_address') }}</h4>
									
										<div class="text-right">
											<a @click="copyAddress(true)" class="btn btn-outline-primary btn-sm">{{ $t('customers.copy_billing_address') }}</a>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>{{$t('customers.name')}}</label>
													<input type="text" v-model.trim="shipping.name" class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.state')}}</label>
													<input type="text" v-model.trim="shipping.state" class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.address')}}</label>
													<textarea rows="3" cols="5"  v-model.trim="shipping.address_street_1" @input="$v.shipping.address_street_1.$touch()" class="form-control" :placeholder="$t('general.street_1')"></textarea>
                           <div v-if="$v.shipping.address_street_1.$error">
                                <span
                                  v-if="!$v.shipping.address_street_1.maxLength"
                                  class="text-sm text-danger"
                                  >{{ $t('validation.address_maxlength') }}</span
                                >
                            </div>
                        </div>
                        <div class="form-group">
													<textarea rows="3" cols="5"  v-model.trim="shipping.address_street_2" @input="$v.shipping.address_street_2.$touch()" class="form-control" :placeholder="$t('general.street_2')"></textarea>
                          <div v-if="$v.shipping.address_street_2.$error">
                                <span
                                  v-if="!$v.shipping.address_street_2.maxLength"
                                  class="text-sm text-danger"
                                  >{{ $t('validation.address_maxlength') }}</span
                                >
                            </div>
                        </div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>{{$t('customers.country')}}</label>
                            <select class="select form-control" v-model="shipping_country">
														<option>{{$t('general.select_country')}}</option>
														<option v-for="item in countries" :key="item.id" :value="item.id">{{item.name}}</option>
													</select>
                        </div>
												<div class="form-group">
													<label>{{$t('customers.city')}}</label>
													<input type="text" v-model.trim="shipping.city" class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.phone')}}</label>
													<input type="text" v-model.trim="shipping.phone" class="form-control">
												</div>
												<div class="form-group">
													<label>{{$t('customers.zip_code')}}</label>
													<input type="text" v-model.trim="shipping.zip" class="form-control">
												</div>
											</div>
										</div>
										<div class="text-right mt-4">
											<button type="submit" :loading="isLoading" :disabled="isLoading" class="btn btn-primary"><i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>{{$t('customers.save_customer')}}</button>
                    </div>
									</form>
                   <base-loader v-else />
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
                        <button  type="button" class="btn btn-secondary submit-btn" @click="cancelpopup"> 
                            {{$t('general.cancel')}}</button>
                        <button type="button" :disabled="isLoading" @click="cropImage" class="btn btn-primary submit-btn"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                            {{$t('general.save')}}</button>
                             <button type="button" data-toggle="modal" id="cancelpopup" data-dismiss="modal" class="d-none">
                            {{$t('general.save')}}</button>
                            <button data-toggle="modal" id="showpopup" data-target="#crop_image_form" class="d-none">
                            {{$t('general.save')}}</button>
                      </div>
                </div>
              </div>
            </div>
          </div>
							</div>
						</div>
					</div>
				</div>
       
  </base-page>
</template>

<script>
import AddressStub from '../../stub/address'
import { mapActions, mapGetters } from 'vuex'
import _ from 'lodash'
import { DocumentDuplicateIcon } from '@vue-hero-icons/solid'
import CustomFieldsMixin from '../../mixins/customFields'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'
const htmlRegex =/<(.|\n)*?>/g;
const {
  required,
  minLength,
  email,
  url,
  maxLength,
  alpha,
  numeric, 
} = require('vuelidate/lib/validators')

export default {
  components: {
    DocumentDuplicateIcon,
    Cropper,
  },
  mixins: [CustomFieldsMixin],
  data() {
    return {
      isCopyFromBilling: false,
      isLoading: false,
      initLoad: false,
      previewImage: null,
      fileObject: null,
      uploadImage: false,
      formData: {
        name: null,
        contact_name: null,
        email: null,
        phone: null,
        currency_id: null,
        website: null,
        addresses: [],
      },
      currency: null,
      billing: {
        name: null,
        country_id: null,
        state: null,
        city: null,
        phone: null,
        zip: null,
        address_street_1: null,
        address_street_2: null,
        type: 'billing',
      },
      shipping: {
        name: null,
        country_id: null,
        state: null,
        city: null,
        phone: null,
        zip: null,
        address_street_1: null,
        address_street_2: null,
        type: 'shipping',
      },
      currencyList: [],
      previewAvatar: null,
      billing_country: null,
      shipping_country: null,

      countries: [],
    }
  },
  validations: {
    formData: {
      name: {
        required,
        minLength: minLength(3),
      },
      phone: {
        numeric,
      },
      email: {
        email,
      },
      website: {
        url,
      }
    },
    billing: {
      address_street_1: {
        maxLength: maxLength(255),
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
    shipping: {
      address_street_1: {
        maxLength: maxLength(255),
      },
      address_street_2: {
        maxLength: maxLength(255),
      },
    },
    currency: {
        required
    }
  },
  computed: {
    ...mapGetters(['currencies']),
    ...mapGetters('company', ['defaultCurrency']),
    isEdit() {
      if (this.$route.name === 'customers.edit') {
        return true
      }
      return false
    },
    pageTitle() {
      if (this.$route.name === 'customers.edit') {
        return this.$t('customers.edit_customer')
      }
      return this.$t('customers.new_customer')
    },
    hasBillingAdd() {
      let billing = this.billing
      if (
        billing.name ||
        billing.country_id ||
        billing.state ||
        billing.city ||
        billing.phone ||
        billing.zip ||
        billing.address_street_1 ||
        billing.address_street_2
      ) {
        return true
      }
      return false
    },
    hasShippingAdd() {
      let shipping = this.shipping
      if (
        shipping.name ||
        shipping.country_id ||
        shipping.state ||
        shipping.city ||
        shipping.phone ||
        shipping.zip ||
        shipping.address_street_1 ||
        shipping.address_street_2
      ) {
        return true
      }
      return false
    },
    displayNameError() {
      if (!this.$v.formData.name.$error) {
        return ''
      }
      if (!this.$v.formData.name.required) {
        return this.$tc('validation.required')
      } else {
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
    },
   
    phoneError() {
        if (!this.$v.formData.phone.$error) {
          return ''
        }

        if (!this.$v.formData.phone.numeric) {
          return 'Phone number should contain numbers only'
        }
    },
    urlError() {
      if (!this.$v.formData.website.$error) {
        return ''
      }

      if (!this.$v.formData.website.url) {
        return this.$tc('validation.invalid_url')
      }
    },
    billAddress1Error() {
      if (!this.$v.billing.address_street_1.$error) {
        return ''
      }

      if (!this.$v.billing.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    billAddress2Error() {
      if (!this.$v.billing.address_street_2.$error) {
        return ''
      }

      if (!this.$v.billing.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    shipAddress1Error() {
      if (!this.$v.shipping.address_street_1.$error) {
        return ''
      }

      if (!this.$v.shipping.address_street_1.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
    shipAddress2Error() {
      if (!this.$v.shipping.address_street_2.$error) {
        return ''
      }

      if (!this.$v.shipping.address_street_2.maxLength) {
        return this.$t('validation.address_maxlength')
      }
    },
  },
  watch: {
    billing_country(newCountry) {
      if (newCountry) {
        this.billing.country_id = newCountry
        this.isDisabledBillingState = false
      } else {
        this.billing.country_id = null
      }
    },
    shipping_country(newCountry) {
      if (newCountry) {
        this.shipping.country_id = newCountry
        return true
      } else {
        this.shipping.country_id = null
      }
    },
  },
  created() {
    this.fetchInitData()
  },
  mounted() {
    
    if (this.isEdit) {
      this.loadCustomer()
      return true
    }
    this.currency = this.defaultCurrency && this.defaultCurrency.id
    this.setInitialCustomFields('Customer')
  },
  methods: {
    ...mapActions('customer', [
      'addCustomer',
      'fetchCustomer',
      'updateCustomer',
      'fetchViewCustomer',
    ]),

    ...mapActions('customFields', ['fetchCustomFields']),

    currencyNameWithCode({ name, code }) {
      return `${code} - ${name}`
    },

    changeFileImage() {
       document.getElementById("fileimageinput").click()
    },
    changeHtml(rawStr) {
        rawStr.replace(/[\u00A0-\u9999<>\&]/g, function(i) {
              return '&#'+i.charCodeAt(0)+';';
        });
    },
    cancelpopup() {
        this.uploadImage = false
        if(this.chooseFile === 1) {
          document.getElementById("fileinput").value= null
        }
        this.previewImage = null
        this.fileObject = null
        document.getElementById('cancelbutton').click()
    },
    
    onUploadHandler(cropper) {
      this.previewAvatar = cropper
        .getCroppedCanvas()
        .toDataURL(this.cropperOutputMime)
    },
    altImg(e) {
      e.target.src = '/assets/img/default-avatar.jpg'
    },
  
    clearValue(value) {
      this.chooseFile = value
      this.uploadImage = false
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
    async onFileChange(evt) {
        this.$refs.cropper.refresh()
        let input = evt.target;
        let regex = /(\.jpg|\.jpeg|\.png|\.gif|\.ico)$/i;
          if (input.files && regex.test(input.files[0].name)) {
              if (input.files && input.files[0]) {
                let reader = new FileReader();
                this.fileObject = input.files && input.files[0].name
                this.uploadImage = true
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
          } else {
              window.toastr['error']('Please upload valid file format')
          }
   
      
    },

    async loadCustomer() {
      let params = {
        id: this.$route.params.id,
      }
      let response = await this.fetchCustomer(params)

      this.formData = { ...this.formData, ...response.data.customer }

      if (response.data.customer.billing_address) {
        this.billing = response.data.customer.billing_address

        if (response.data.customer.billing_address.country_id) {
          this.billing_country = response.data.customer.billing_address.country.id
        }
      }
      
      if(response.data.customer.avatar) {
        this.previewAvatar = response.data.customer.avatar
      } else {
        this.previewAvatar = '/assets/img/default-avatar.jpg'
      }
      if (response.data.customer.shipping_address) {
        this.shipping = response.data.customer.shipping_address

        if (response.data.customer.shipping_address.country_id) {
          this.shipping_country =
            response.data.customer.shipping_address.country.id
        }
      }

      this.formData.currency_id = response.data.customer.currency_id
      this.currency = response.data.customer.currency.id

      let res = await this.fetchCustomFields({ type: 'Customer', limit: 'all' })
      let customFields = res.data.customFields.data
      this.formData.fields = response.data.customer.fields

      this.setEditCustomFields(response.data.customer.fields, customFields)
    },
    async fetchInitData() {
      this.initLoad = true
      let res = await window.axios.get('/api/v1/countries')
      if (res) {
        this.countries = res.data.countries
      }
      this.initLoad = false
    },
    copyAddress(val) {
      if (val === true) {
        this.isCopyFromBilling = true
        this.shipping = { ...this.billing, type: 'shipping' }
        this.shipping_country = this.billing_country
        this.shipping_state = this.billing_state
        this.shipping_city = this.billing_city
      } else {
        this.shipping = { ...AddressStub, type: 'shipping' }
        this.shipping_country = null
        this.shipping_state = null
        this.shipping_city = null
      }
    },
    async submitCustomerData() {
      this.$v.formData.$touch()
      let validate = await this.touchCustomField()

      if (this.$v.$invalid || validate.error) {
        return true
      }

      if (this.hasBillingAdd && this.hasShippingAdd) {
        this.formData.addresses = [{ ...this.billing }, { ...this.shipping }]
      } else if (this.hasBillingAdd) {
        this.formData.addresses = [{ ...this.billing }]
      } else if (this.hasShippingAdd) {
        this.formData.addresses = [{ ...this.shipping }]
      }

      try {
        let response = null
        this.isLoading = true
        if (this.currency) {
          this.formData.currency_id = this.currency
        }
         if(this.fileObject && this.previewAvatar) {
              this.formData.customer_avatar = JSON.stringify({name: this.fileObject, data: this.previewAvatar})
          }
        if (this.isEdit) {
          response = await this.updateCustomer(this.formData)
          if (response.data.success) {
            this.$router.push(
              `/admin/customers/${response.data.customer.id}/view`
            )
            window.toastr['success'](this.$t('customers.updated_message'))
          }
          if (response.data.error) {
            window.toastr['error'](this.$t('validation.email_already_taken'))
          }
        } else {
          if(this.fileObject && this.previewAvatar) {
              this.formData.customer_avatar = JSON.stringify({name: this.fileObject, data: this.previewAvatar})
          }
          
          response = await this.addCustomer(this.formData)
          if (response.data.success) {
            this.$router.push(
              `/admin/customers/${response.data.customer.id}/view`
            )
            
            window.toastr['success'](this.$t('customers.created_message'))
          }
          
        }

        this.isLoading = false
        return true
      } catch (error) {
        this.isLoading = false
        if (error.response.data.errors.email) {
          window.toastr['error'](this.$t('validation.email_already_taken'))
        }
      }
    },
  },
}
</script>
