<template>
   <div class="main-wrapper">
			<div class="error-box">
				<h1>404</h1>
				<h3 class="h2 mb-3"><i class="fas fa-exclamation-circle"></i> Oops! Page not found!</h3>
				<p class="h4 font-weight-normal">The page you requested was not found.</p>
				<router-link to="/admin/dashboard" class="btn btn-primary">Back to Home</router-link>
			</div>
        </div>
</template>

<script>
import { ArrowLeftIcon } from '@vue-hero-icons/solid'
export default {
  components: {
    ArrowLeftIcon,
  },
  mounted() {
    this.setLayoutClasses()
  },
  destroyed() {
    let body = document.getElementsByTagName('body')
    body[0].classList -= ' bg-black'
  },
  methods: {
    setLayoutClasses() {
      let body = document.getElementsByTagName('body')
      body[0].classList += ' bg-black'
    },
  },
}
</script>
