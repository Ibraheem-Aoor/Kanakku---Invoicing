<template>
  <div
    class="flex flex-col items-center justify-between w-full h-32 pt-10 step-indicator"
  >
  <div class="installer-logo">
    <img
      id="logo-kanakku"
      src="/assets/img/logo.png"
      alt="kanakku Logo"
      class="img-fluid logo-dark mt-4"
    />
    </div>
    <form-wizard class="wizardnew">
       <template slot="step" slot-scope="props">
            <wizard-step :tab="props.tab"
                :transition="props.transition"
                :key="props.tab.title"
                :index="props.index">
            </wizard-step>
        </template>
      <tab-content v-for="tab in tabs"  :key="tab.title" :title="tab.title" :icon="tab.icon">
        <component :is="tabData" @next="setTab"></component>
      </tab-content>
    </form-wizard>

  </div>
</template>

<script>
import SystemRequirement from './WizardSystemRequirementStep'
import Permission from './WizardPermissionStep'
import Database from './WizardDatabaseStep'
import EmailConfiguration from './WizardEmailConfigStep'
import UserProfile from './WizardUserProfileStep'
import CompanyInfo from './WizardCompanyInfoStep'
import Settings from './WizardSettingsStep'
import {FormWizard, TabContent} from 'vue-step-wizard'
import 'vue-step-wizard/dist/vue-step-wizard.css'

export default {
  components: {
    step1: SystemRequirement,
    step2: Permission,
    step3: Database,
    step4: EmailConfiguration,
    step5: UserProfile,
    step6: CompanyInfo,
    step7: Settings,
    'form-wizard': FormWizard,
    'tab-content': TabContent,
    tabActive: false
  },
  data() {
    return {
      profile_complete: null,
      loading: false,
      tabData: 'step1',
      step: 1,
      selectedValue: "",
      tabs: [{
          id:1,
          title: 'System requirements',
          icon: 'ti-user',
          component: 'step1'
        },
        {
          id:2,
          title: 'Permission',
          icon: 'ti-settings',
          component: 'step2'
        },
        {
          id:3,
          title: 'Database',
          icon: 'ti-location-pin',
          component: 'step3'
        },
        {
          id:4,
          title: 'Email config',
          icon: 'ti-check',
          component: 'step4'
        },
        {
          id:5,
          title: 'User Information',
          icon: 'ti-check',
          component: 'step5'
        },
        {
          id:6,
          title: 'Company Information',
          icon: 'ti-check',
          component: 'step6'
        },
        {
          id:7,
          title: 'Setting',
          icon: 'ti-check',
          component: 'step7'
        },
      ],
    }
  },
  created() {
    this.getProfileComplete()
  },
  methods: {
    async getProfileComplete() {
      let response = await axios.get('/api/v1/onboarding/wizard-step')

      if (response.data.profile_complete === 'COMPLETED') {
        this.$router.push('/admin/dashboard')

        return
      }

      let dbStep = parseInt(response.data.profile_complete)

      if (dbStep) {
        this.step = dbStep + 1
        this.tabData = `step${dbStep + 1}`
        this.getSteps(this.step)
      }
    },
    getSteps(step) {
      if(step===2) {
        $(".step-pills .step-item").removeClass('active')
        $(".step-pills .step-item:nth-child(2)").addClass('active')
      } else if(step===3) {
        $(".step-pills .step-item").removeClass('active')
        $(".step-pills .step-item:nth-child(3)").addClass('active')
      } else if(step===4) {
        $(".step-pills .step-item").removeClass('active')
        $(".step-pills .step-item:nth-child(4)").addClass('active')
      } else if(step===5) {
        $(".step-pills .step-item").removeClass('active')
        $(".step-pills .step-item:nth-child(5)").addClass('active')
      } else if(step===6) {
        $(".step-pills .step-item").removeClass('active')
        $(".step-pills .step-item:nth-child(6)").addClass('active')
      } else if(step===7) {
        $(".step-pills .step-item").removeClass('active')
        $(".step-pills .step-item:nth-child(7)").addClass('active')
      }
    },
    async setProfileComplete(data) {
      let status = {
        profile_complete: data,
      }

      let response = await axios.post('/api/v1/onboarding/wizard-step', status)
    },

    getActive(tab) {
        return tab == this.tabData
    },
  
    async setTab(data) {

      if (data) {
        this.setProfileComplete(data)
      }
      
      this.step++;

      this.getSteps(this.step)

      if (this.step <= 7) {
        this.tabData = 'step' + this.step
        this.tabActive= true
      } else {
        // window.location.reload()
      }
    },
  },
}
</script>
