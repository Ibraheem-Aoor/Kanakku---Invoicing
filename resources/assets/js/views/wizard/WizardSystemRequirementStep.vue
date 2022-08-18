<template>
  <div>
    <h3>{{$t('wizard.req.system_req')}}</h3>
    <div>
      {{$t('wizard.req.system_req_desc')}}
    </div>
    <div>
      <div >
          <div
          v-if="phpSupportInfo"
          class="p-3 border mb-2 mt-2"
        >
          <div class="text-sm">
            {{
              $t('wizard.req.php_req_version', {
                version: phpSupportInfo.minimum,
              })
            }}
          </div>
          <div>
            {{ phpSupportInfo.current }}
            <span
              v-if="phpSupportInfo.supported"
              class="fas fa-circle text-success"
            />
            <span
              v-else
              class="fas fa-circle text-danger"
            />
          </div>
        </div>
        <div v-if="requirements">
          <div class="mb-1"
            v-for="(requirement, index) in requirements"
            :key="index"
          >
            <div class="col-span-2 text-sm">
              {{ index }}
            </div>
            <div class="text-right">
              <span
                v-if="requirement"
                
              />
              <span
                v-else
              />
            </div>
          </div>
        </div>
      </div>
      <button
        v-if="hasNext"
        type="button"
        class="btn btn-primary mt-4"
        variant="primary"
        @click="next"
      >
        {{ $t('wizard.continue') }}
        <arrow-right-icon class="h-5 ml-2 -mr-1" />
      </button>
      <button
        v-if="!requirements"
        :loading="isLoading"
        :disabled="isLoading"
        class="btn btn-primary mt-4"
        variant="primary"
        @click="getRequirements"
      >
        {{ $t('wizard.req.check_req') }}
      </button>
    </div>
  </div>
</template>

<script>
import { ArrowRightIcon } from '@vue-hero-icons/solid'
import {TabContent} from 'vue-step-wizard'
import 'vue-step-wizard/dist/vue-step-wizard.css'

export default {
  components: {
    ArrowRightIcon,
    'tab-content': TabContent
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
      requirements: null,
      phpSupportInfo: null,
      isLoading: false,
      isShow: true,
    }
  },
  computed: {
    hasNext() {
      if (this.requirements) {
        let isRequired = true
        for (const key in this.requirements) {
          if (!this.requirements[key]) {
            isRequired = false
          }
        }
        return this.requirements && this.phpSupportInfo.supported && isRequired
      }
      return false
    },
  },
  methods: {
    listToggle() {
      this.isShow = !this.isShow
    },
    async getRequirements() {
      this.isLoading = true

      let response = await window.axios.get(
        '/api/v1/onboarding/requirements',
        this.profileData
      )

      if (response.data) {
        this.requirements = response.data.requirements.requirements.php
        this.phpSupportInfo = response.data.phpSupportInfo
        this.isLoading = false
      }
    },
    async next() {
      this.isLoading = true
      await this.$emit('next')
      this.isLoading = false
    },
  },
}
</script>
