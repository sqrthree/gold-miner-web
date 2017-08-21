<template>
  <div class="container">
    <div class="user-info">
      <div class="user-info__avatar"><img :src="user.avatar" alt=""></div>
      <h3>{{ user.name }}</h3>
      <p class="user-info__bio">{{ user.bio }}</p>
    </div>
    <router-view></router-view>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'UserBase',
  computed: {
    ...mapState(['users']),
    user() {
      const { id } = this.$route.params
      return this.users.data[id] || {}
    },
  },
  methods: {
    ...mapActions(['fetchUserInfo']),
  },
  created() {
    const { id } = this.$route.params

    this.fetchUserInfo(id).catch(err => this.$message.error(err.message))
  },
}
</script>

<style lang="scss" scoped>
.user-info {
  margin-top: 80px;
  margin-bottom: 100px;
  text-align: center;

  &__avatar {

    img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
    }
  }
}
</style>
