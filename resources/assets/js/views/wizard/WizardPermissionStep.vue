<template>
  <div>
    <h3>{{$t('wizard.permissions.permissions')}}</h3>
    <div>{{$t('wizard.permissions.permission_desc')}}</div>
    <base-loader v-if="isFetching" :show-bg-overlay="true" />
    <div class="relative mt-3">
      <div
        v-for="(permission, index) in permissions"
        :key="index"
        class="border-gray-200"
      >
        <div class="form-group permission">
          <div class="col-span-2 p-3">
            {{ permission.folder }}
          </div>
          <div class="p-3 text-right">
            <span
              v-if="permission.isSet"
            />
            <span
              v-else
            />
            <span>{{ permission.permission }}</span>
          </div>
        </div>
      </div>

      <button
        v-show="!isFetching"
        :loading="isLoading"
        :disabled="isLoading"
        class="btn btn-primary mt-10"
        variant="primary"
        @click="next"
      >
        {{ $t('wizard.continue') }}
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
      isFetching: false,
      isLoading: false,
      permissions: [],
      errors: false,
    }
  },
  created() {
    this.getPermissions()
  },
  methods: {
    async getPermissions() {
      this.isLoading = this.isFetching = true

      let response = await window.axios.get(
        '/api/v1/onboarding/permissions',
        this.profileData
      )

      if (response.data) {
        this.permissions = response.data.permissions.permissions
        this.errors = response.data.permissions.errors
        let self = this

        if (this.errors) {
          swal({
            title: this.$t('wizard.permissions.permission_confirm_title'),
            text: this.$t('wizard.permissions.permission_confirm_desc'),
            icon: 'warning',
            buttons: true,
            dangerMode: true,
          }).then(async (willConfirm) => {
            if (willConfirm) {
              self.isLoading = this.isFetching = false
            }
          })
        } else {
          this.isLoading = this.isFetching = false
        }

        this.isLoading = this.isFetching = false
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
