
<template>
<!-- Add Category Modal -->
        <div >
            <div id="mail_test_modal" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Test Mail</h5>
                                <button type="button" class="close" data-dismiss="modal" id="cancelbutton" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form  @submit.prevent="onTestMailSend">
                                    <div class="p-4 md:p-8">
                                        <div class="form-group">
                                        <label>{{$t('general.to')}}</label>
                                        <input
                                            ref="to"
                                            :class="{ 'is-invalid': $v.formData.to.$error }"
                                            v-model="formData.to"
                                            class="form-control"
                                            type="text"
                                            @input="$v.formData.to.$touch()"
                                        />
                                         <div class="invalid-feedback" v-if="$v.formData.to.$error">{{emailError}}</div>

                                        </div>
                                        <div class="form-group">
                                            <label>{{$t('general.subject')}}</label>
                                            <input
                                                :class="{'is-invalid': $v.formData.subject.$error }"
                                                v-model.trim="formData.subject"
                                                type="text"
                                                class="form-control"
                                                @input="$v.formData.subject.$touch()"
                                            />
                                             <div class="invalid-feedback" v-if="$v.formData.subject.$error">{{subjectError}}</div>
                                        </div>
                                        <div class="form-group">
                                        <label>{{$t('general.message')}}</label>
                                        <textarea
                                            :class="{ 'is-invalid': $v.formData.message.$error }"
                                            v-model.trim="formData.message"
                                            rows="4"
                                            cols="50"
                                            class="form-control"
                                            @input="$v.formData.message.$touch()"
                                        />
                                        <div class="invalid-feedback" v-if="$v.formData.message.$error">{{messageError}}</div>
                                        </div>
                                    </div>
                                    <div
                                        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
                                    >
                                        <button
                                            variant="primary-outline"
                                            type="button"
                                            class="ml-2 btn btn-primary"  
                                            @click="closeTaxModal" 
                                        >
                                            {{ $t('general.cancel') }}
                                        </button>
                                        <button
                                            variant="primary-outline" :loading="isLoading"
                                            type="submit"
                                            class="ml-2 btn btn-primary"  
                                        ><paper-airplane-icon v-if="!isLoading" class="mr-2" />
                                            {{ !isEdit ? $t('general.send') : $t('general.update') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                    </div>
                </div>
            </div>
        <button type="button" id="openmodal" class="btn btn-primary d-none" data-toggle="modal" data-target="#mail_test_modal"></button>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { PaperAirplaneIcon } from '@vue-hero-icons/outline'
const {
  required,
  minLength,
  email,
  maxLength,
} = require('vuelidate/lib/validators')
export default {
  components: {
    PaperAirplaneIcon,
  },
  data() {
    return {
      isEdit: false,
      isLoading: false,
      formData: {
        to: null,
        subject: null,
        message: null,
      },
    }
  },
  computed: {
    ...mapGetters('modal', ['modalDataID', 'modalData', 'modalActive']),
    emailError() {
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

      if (!this.$v.formData.subject.maxLength) {
        return this.$tc('validation.subject_maxlength')
      }
    },
    messageError() {
      if (!this.$v.formData.message.$error) {
        return ''
      }
      if (!this.$v.formData.message.required) {
        return this.$tc('validation.required')
      }
      if (!this.$v.formData.message.maxLength) {
        return this.$tc('validation.message_maxlength')
      }
    },
  },
  watch: {
    modalActive(val) {
      if (!this.modalActive) {
        this.resetFormData()
      }
    },
  },
  validations: {
    formData: {
      to: {
        required,
        email,
      },
      subject: {
        required,
        maxLength: maxLength(100),
      },
      message: {
        required,
        maxLength: maxLength(255),
      },
    },
  },
  async mounted() {
    this.$refs.to.focus = true
  },
  methods: {
    ...mapActions('modal', ['closeModal', 'resetModalData']),
    ...mapActions('company', ['sendTestMail']),
    resetFormData() {
      this.formData = {
        to: null,
        subject: null,
        message: null,
      }
      this.$v.formData.$reset()
    },
    async onTestMailSend() {
      this.$v.formData.$touch()

      if (this.$v.$invalid) {
        return true
      }

      this.isLoading = true
      let response = await this.sendTestMail(this.formData)
      if (response.data) {
        if (response.data.success) {
          window.toastr['success'](this.$tc('general.send_mail_successfully'))
          this.closeTaxModal()
          this.isLoading = false
          return true
        }

        window.toastr['error'](this.$tc('validation.something_went_wrong'))
        this.closeTaxModal()
        this.isLoading = false
        return true
      }
      window.toastr['error'](response.data.error)
    },
    show() {
        this.resetFormData()
        document.getElementById("openmodal").click()
    },
    
    closeTaxModal() {
      this.resetModalData()
      this.resetFormData()
      this.closeModal()
      document.getElementById("cancelbutton").click()
      
    },
  },
}
</script>
