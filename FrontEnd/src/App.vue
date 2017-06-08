<template>
  <div class="container-fluid">
    <navbar :user="user"></navbar>
    <router-view></router-view>
    <page-footer></page-footer>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

const store = require('store')

export default {
  name: 'app',
  data() {
    return {
      activeIndex: '1',
    }
  },
  computed: {
    ...mapState(['users']),
    user() {
      return this.$store.getters.currentUser
    },
  },
  methods: {
    ...mapMutations(['login']),
  },
  created() {
    let user = null
    let isNewUser = false

    // eslint-disable-next-line
    if (window.__USER__) {
      try {
        // eslint-disable-next-line
        user = JSON.parse(decodeURIComponent(window.__USER__))
        isNewUser = true
      } catch (err) {
        // eslint-disable-next-line
        console.error(err)
      }
    } else {
      user = store.get('user')
    }

    if (!user) return

    this.login(user)

    if (isNewUser) {
      store.set('user', user)

      if (user.istranslator) {
        this.$router.replace('/')
      } else {
        this.$router.replace('/applications/apply')
      }
    }
  },
}
</script>

<style lang="scss" scoped>
@import '~nprogress/nprogress.css';
@import "~styles/exports";

.navbar {
  background: #eef1f6;
}
</style>
