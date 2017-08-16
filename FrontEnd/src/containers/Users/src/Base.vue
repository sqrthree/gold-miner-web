<template>
  <div class="container">
    <div class="user-info">
      <div class="user-info__avatar"><img :src="currentUser.avatar" alt=""></div>
      <h3>{{ currentUser.name }}</h3>
      <p class="user-info__bio">{{ currentUser.bio }}</p>
    </div>
    <router-view></router-view>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'UserBase',
  computed: {
    ...mapGetters(['currentUser']),
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
