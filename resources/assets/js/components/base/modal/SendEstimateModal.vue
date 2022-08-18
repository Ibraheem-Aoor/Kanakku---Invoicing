<template>
  <!-- Add Estimate Modal -->
					 <div >
           <div id="add_estimate" class="modal custom-modal fade" role="dialog">
						<div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">{{ $t('estimates.send_estimate') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" id="cancelbutton" aria-label="Close" @click="closeSendEstimateModal">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form @submit.prevent="sendEstimateData">
                      <div class="form-group">
                        <label>{{$t('general.from')}}<span class="text-danger">*</span></label>
                        <input class="form-control" v-model="formData.from" :class="{'is-invalid': $v.formData.from.$error}"  @input="$v.formData.from.$touch()" type="text">
                        <div class="invalid-feedback" v-if="$v.formData.from.$error">{{fromError}}</div>
                      </div>
                      <div class="form-group">
                        <label>{{$t('general.to')}}<span class="text-danger">*</span></label>
                        <input class="form-control" v-model="formData.to" :class="{'is-invalid': $v.formData.to.$error}" @input="$v.formData.to.$touch()" type="text">
                        <div class="invalid-feedback" v-if="$v.formData.to.$error">{{toError}}</div>
                      </div>
                      <div class="form-group">
                        <label>{{$t('general.subject')}}</label>
                        <textarea class="form-control" v-model="formData.subject" :class="{'is-invalid': $v.formData.subject.$error}" @input="$v.formData.subject.$touch()" type="text" rows="3" columns="5"> </textarea>
                        <div class="invalid-feedback" v-if="$v.formData.subject.$error">{{subjectError}}</div>
                      </div>
                      <div class="form-group">
                        <label>{{$t('general.body')}}</label>
                          <vue-editor v-model="formData.body" style="height:450px:min-width:1000px" :class="{'is-invalid': $v.formData.body.$error}" @input="$v.formData.body.$touch()"  id="editor1"  />
                      </div>
                      <div class="text-right">
                           <base-custom-input
                              v-model="formData.body"
                              :fields="estimateMailFields"
                              class="mb-4"
                            />
                      </div>
                      <div class="submit-section" >
                        <button  class="btn btn-primary" @click="closeSendEstimateModal"> <i class="fas fa-window-close" />
                          {{$t('general.cancel')}}</button>
                           <button :disabled="isLoading" class="btn btn-primary"> <i  v-if="isLoading" class="fas fa-spinner fa-spin"></i>
                         {{$t('general.send') }}
                         </button>
                      </div>
                     
									</form>
								</div>
							</div>
						</div>
					</div>
        <button type="button" id="openmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#add_estimate"></button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PaperAirplaneIcon } from '@vue-hero-icons/solid'
const { required, email } = require('vuelidate/lib/validators')
const _ = require('lodash')
import { VueEditor } from "vue2-editor";

export default {
  components: {
    PaperAirplaneIcon, VueEditor, 
  },
  data() {
    return {
      isLoading: false,
      estimateMailFields: [
        'customer',
        'customerCustom',
        'estimate',
        'estimateCustom',
        'company',
      ],
      formData: {
        from: null,
        to: null,
        subject: 'New Estimate',
        body: null,
        id: null
      },
    }
  },
  validations: {
    formData: {
      from: {
        required,
        email,
      },
      to: {
        required,
        email,
      },
      subject: {
        required,
      },
      body: {
        required,
      },
    },
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),
    ...mapGetters('user', ['currentUser']),
    getEmailUrl() {
      return this.url
    },
    fromError() {
      if (!this.$v.formData.from.$error) {
        return ''
      }

      if (!this.$v.formData.from.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.from.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    toError() {
      if (!this.$v.formData.to.$error) {
        return ''
      }

      if (!this.$v.formData.to.required) {
        return this.$tc('validation.required')
      }

      if (!this.$v.formData.to.email) {
        return this.$tc('validation.email_incorrect')
      }
    },
    subjectError() {
      if (!this.$v.formData.subject.$error) {
        return ''
      }

      if (!this.$v.formData.subject.required) {
        return this.$tc('validation.required')
      }
    },
    bodyError() {
      if (!this.$v.formData.body.$error) {
        return ''
      }

      if (!this.$v.formData.body.required) {
        return this.$tc('validation.required')
      }
    },
  },
  mounted() {
    //this.setInitialData()
  },
  methods: {
    ...mapActions('modal', ['closeModal']),

    ...mapActions('estimate', ['sendEmail']),

    ...mapActions('company', ['fetchCompanySettings', 'fetchMailConfig']),

    async setInitialData(id, data) {
      let admin = await this.fetchMailConfig()
      if (data) {
        this.formData.id = id
        this.formData.from = admin.data.from_mail
        this.formData.to = data.user.email
        this.formData.subject = 'New estimate'
      }

      let res = await this.fetchCompanySettings(['estimate_mail_body'])

      this.formData.body = res.data.estimate_mail_body
    },
    resetFormData() {
      this.formData = {
        from: null,
        to: null,
        subject: null,
        body: null,
        id: null
      }
    },
    async sendEstimateData() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_send_estimate'),
        
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        try {
          if (value) {
            let data = {
              ...this.formData,
              id: this.formData.id,
              status: 'SENT',
            }

            this.isLoading = true
            let res = await this.sendEmail(data)
            this.closeSendEstimateModal()
            if (res.data.success) {
              this.isLoading = false
              window.toastr['success'](
                this.$tc('estimates.send_estimate_successfully')
              )
              return true
            }
            if (res.data.error === 'estimates.user_email_does_not_exist') {
              window.toastr['error'](
                this.$tc('estimates.user_email_does_not_exist')
              )
              return false
            }
          }
        } catch (error) {
          this.isLoading = false
          window.toastr['error'](this.$tc('estimates.something_went_wrong'))
        }
      })
    },
    show(id, data){
        this.setInitialData(id, data)
        document.getElementById("openmodal").click()
    },
    closeSendEstimateModal() {
      document.getElementById("cancelbutton").click()
      this.resetFormData()
      
    },
  },
}
</script>
